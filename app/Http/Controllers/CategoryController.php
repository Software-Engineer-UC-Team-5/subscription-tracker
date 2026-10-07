<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Panel Kategori
 */
class CategoryController extends Controller
{
    /**
     * Siapkan repository dan service untuk pengelolaan kategori.
     */
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected CategoryService $categoryService
    ) {
    }

    /**
     * Tampilkan halaman daftar kategori.
     */
    public function index(Request $request): View
    {
        $categories = $this->categoryRepository->getAvailableForUser($request->user()->id);

        // Pulihkan pilihan edit hanya dari kategori milik akun aktif.
        $editingCategory = $categories->where('user_id', $request->user()->id)
            ->firstWhere('id', $request->old('category_id'));

        return view('categories.index', compact('categories', 'editingCategory'));
    }

    /**
     * Tampilkan formulir tambah kategori baru.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Simpan kategori baru ke database.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->create($request->user()->id, $request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit kategori.
     */
    public function edit(Category $category): View
    {
        $this->categoryService->ensureOwnership($category, auth()->id());

        return view('categories.edit', compact('category'));
    }

    /**
     * Perbarui data kategori di database.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->update($category, $request->user()->id, $request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori dari database.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->delete($category, auth()->id());

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
