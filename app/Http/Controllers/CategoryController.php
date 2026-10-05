<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Panel Kategori
 */
class CategoryController extends Controller
{
    /**
     * Tampilkan halaman daftar kategori.
     */
    public function index(Request $request): View
    {
        // TODO: Ambil daftar kategori dari database (kategori sistem + kategori user)
        return view('categories.index');
    }

    /**
     * Tampilkan formulir tambah kategori baru.
     */
    public function create(): View
    {
        // TODO: Tampilkan view formulir tambah kategori
        return view('categories.create');
    }

    /**
     * Simpan kategori baru ke database.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        // TODO: Simpan data kategori baru ke database via CategoryRepository / Model
        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit kategori.
     */
    public function edit(Category $category): View
    {
        // TODO: Tampilkan view edit kategori dengan data $category
        return view('categories.edit', compact('category'));
    }

    /**
     * Perbarui data kategori di database.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        // TODO: Perbarui data kategori di database
        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori dari database.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // TODO: Hapus kategori kustom milik pengguna
        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
