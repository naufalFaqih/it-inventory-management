<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::withCount('assets');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $categories = $query->latest()->paginate(15)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        AuditLog::log('create_category', $category, null, [
            'name' => $category->name,
            'description' => $category->description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori aset berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $oldValues = [
            'name' => $category->name,
            'description' => $category->description,
        ];

        $category->update($request->validated());

        AuditLog::log('update_category', $category, $oldValues, [
            'name' => $category->name,
            'description' => $category->description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori aset berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->assets()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih terhubung dengan data aset.');
        }

        $oldValues = ['name' => $category->name];
        $category->delete();

        AuditLog::log('delete_category', $category, $oldValues, null);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
