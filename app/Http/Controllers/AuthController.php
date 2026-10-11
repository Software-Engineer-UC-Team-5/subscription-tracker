<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * MODUL: Autentikasi Pengguna (Login, Register, Logout)
 */
class AuthController extends Controller
{
    /**
     * Siapkan service untuk pendaftaran akun dan pencatatan aktivitas autentikasi.
     */
    public function __construct(protected AuthService $authService)
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
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Catat log aktivitas login
            $this->authService->recordLogin(Auth::id());

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
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = $this->authService->register($request->validated());

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
            $this->authService->recordLogout($userId);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
