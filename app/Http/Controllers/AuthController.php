<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * MODUL: Autentikasi Pengguna (Login, Register, Logout)
 */
class AuthController extends Controller
{
    /**
     * Siapkan repository untuk mengisi kategori awal milik akun baru.
     */
    public function __construct(protected CategoryRepository $categoryRepository)
    {
    }

    /**
     * Tampilkan formulir login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Catat log aktivitas login
            ActivityLog::record(
                userId: Auth::id(),
                action: 'LOGIN',
                entity: 'User',
                entityId: Auth::id(),
                description: 'Pengguna berhasil masuk ke sistem'
            );

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan formulir registrasi akun baru.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun pengguna baru.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Akun, kategori awal, dan log registrasi disimpan dalam satu transaksi.
        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $this->categoryRepository->copyDefaultsForUser($user->id);

            ActivityLog::record(
                userId: $user->id,
                action: 'REGISTER',
                entity: 'User',
                entityId: $user->id,
                description: 'Pendaftaran akun baru berhasil'
            );

            return $user;
        });

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil didaftarkan. Selamat datang!');
    }

    /**
     * Proses logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        if ($userId) {
            ActivityLog::record(
                userId: $userId,
                action: 'LOGOUT',
                entity: 'User',
                entityId: $userId,
                description: 'Pengguna keluar dari sistem'
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
