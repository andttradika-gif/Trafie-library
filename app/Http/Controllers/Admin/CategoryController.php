<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:255',
        ]);
        $data['slug'] = Str::slug($data['name']);

        if (Category::where('slug', $data['slug'])->exists()) {
            return back()->withErrors(['name' => 'Nama kategori terlalu mirip dengan yang sudah ada.'])->withInput();
        }

        Category::create($data);
        return back()->with('success', 'Kategori "' . $data['name'] . '" ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => 'nullable|string|max:255',
        ]);
        $slug = Str::slug($data['name']);

        if (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            return back()->withErrors(['name' => 'Nama kategori terlalu mirip dengan yang sudah ada.'])->withInput();
        }

        $category->update(['name' => $data['name'], 'description' => $data['description'] ?? null, 'slug' => $slug]);
        return redirect()->route('admin.categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Kategori masih memiliki buku, tidak bisa dihapus.');
        }
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }
}