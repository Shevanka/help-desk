<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('tickets')->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category(['is_active' => true])]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return redirect()->route('admin.categories.index')->with('status', "Kategori {$category->name} berhasil dibuat.");
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')->with('status', "Kategori {$category->name} berhasil diperbarui.");
    }

    /**
     * Categories that are already used by tickets are deactivated instead of
     * deleted, so ticket history stays intact.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->tickets()->exists()) {
            $category->update(['is_active' => false]);

            return back()->with('status', "Kategori {$category->name} masih dipakai tiket, sehingga dinonaktifkan (tidak dihapus).");
        }

        $category->delete();

        return back()->with('status', "Kategori {$category->name} berhasil dihapus.");
    }
}
