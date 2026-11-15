<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private function withStock()
    {
        return Book::with('category')
            ->withCount(['copies', 'copies as available_count' => fn ($c) => $c->where('status', 'Available')]);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $cat = $request->query('kategori');
        $sort = $request->query('urut', 'terbaru');

        $query = $this->withStock()
            ->when($q !== '', fn ($x) => $x->where(function ($w) use ($q) {
                $w->where('title', 'like', "%$q%")->orWhere('author', 'like', "%$q%")->orWhere('publisher', 'like', "%$q%");
            }))
            ->when($cat, fn ($x) => $x->whereHas('category', fn ($c) => $c->where('slug', $cat)));

        $query = match ($sort) {
            'judul' => $query->orderBy('title'),
            'tersedia' => $query->orderByDesc('available_count'),
            default => $query->latest(),
        };

        return view('student.books.index', [
            'books' => $query->paginate(12)->withQueryString(),
            'categories' => Category::withCount('books')->orderBy('name')->get(),
            'q' => $q, 'cat' => $cat, 'sort' => $sort,
        ]);
    }

    public function show(Book $book)
    {
        $book = $this->withStock()->findOrFail($book->id);
        $related = $this->withStock()->where('category_id', $book->category_id)->where('id', '!=', $book->id)->latest()->take(4)->get();
        return view('student.books.show', compact('book', 'related'));
    }
}