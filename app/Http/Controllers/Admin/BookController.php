<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Services\BookLookup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $cat = (int) $request->query('kategori');
        $stok = (string) $request->query('stok');
        $urut = (string) $request->query('urut', 'terbaru');
        $available = fn ($c) => $c->where('status', 'Available');

        $query = Book::with('category')
            ->withCount(['copies', 'copies as available_count' => $available]);

        if ($q !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('title', 'like', $like)->orWhere('author', 'like', $like)
                  ->orWhere('isbn', 'like', $like)->orWhere('publisher', 'like', $like);
            });
        }
        if ($cat > 0) {
            $query->where('category_id', $cat);
        }
        if ($stok === 'tersedia') {
            $query->whereHas('copies', $available);
        } elseif ($stok === 'habis') {
            $query->whereDoesntHave('copies', $available);
        }

        if ($urut === 'judul') {
            $query->orderBy('title');
        } elseif ($urut === 'stok') {
            $query->orderBy('available_count')->orderByDesc('id');
        } else {
            $query->orderByDesc('id');
        }

        $books = $query->paginate(12)->withQueryString();

        $stats = [
            'titles' => Book::count(),
            'copies' => BookCopy::count(),
            'available' => BookCopy::where('status', 'Available')->count(),
            'borrowed' => BookCopy::where('status', 'Borrowed')->count(),
        ];

        return view('admin.books.index', [
            'books' => $books,
            'stats' => $stats,
            'categories' => Category::orderBy('name')->get(),
            'q' => $q, 'cat' => $cat, 'stok' => $stok, 'urut' => $urut,
            'filtered' => $q !== '' || $cat > 0 || $stok !== '',
        ]);
    }

    public function create()
    {
        return view('admin.books.create', ['categories' => Category::orderBy('name')->get()]);
    }

    /** Endpoint AJAX: cari data buku berdasarkan ISBN (Google Books, cadangan Open Library). */
    public function lookup(Request $request)
    {
        $request->validate(['isbn' => 'required|string|max:30']);

        $isbn = BookLookup::toIsbn13((string) $request->query('isbn'));
        if (!$isbn) {
            return response()->json([
                'ok' => false,
                'message' => 'ISBN tidak valid. Pastikan 10 atau 13 digit dan angka terakhirnya (check digit) benar.',
            ], 422);
        }

        $isbn10 = BookLookup::toIsbn10($isbn);
        $exists = Book::whereIn('isbn', array_filter([$isbn, $isbn10]))->first();
        if ($exists) {
            return response()->json([
                'ok' => true, 'found' => true, 'isbn' => $isbn,
                'exists' => [
                    'title' => $exists->title,
                    'edit_url' => route('admin.books.edit', $exists),
                ],
            ]);
        }

        $r = BookLookup::lookup($isbn);
        if (!$r['found']) {
            return response()->json([
                'ok' => true, 'found' => false, 'isbn' => $isbn,
                'message' => 'ISBN ' . $isbn . ' tidak ditemukan di Google Books maupun Open Library. Isi data secara manual.',
                'warnings' => $r['warnings'],
            ]);
        }

        $suggest = BookLookup::suggestCategory($r['data']['categories'] ?? []);

        return response()->json([
            'ok' => true, 'found' => true, 'isbn' => $isbn, 'exists' => null,
            'source' => $r['source'],
            'data' => $r['data'],
            'category_id' => $suggest['category_id'],
            'new_category' => $suggest['new_category'],
            'categories_raw' => $r['data']['categories'] ?? [],
            'warnings' => $r['warnings'],
        ]);
    }

    public function store(Request $request)
    {
        if ($request->filled('isbn')) {
            $raw = (string) $request->input('isbn');
            $request->merge(['isbn' => BookLookup::toIsbn13($raw) ?? BookLookup::cleanIsbn($raw)]);
        }

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:255',
            'publisher'        => 'required|string|max:255',
            'publication_year' => 'required|integer|between:1900,2100',
            'category_id'      => 'required',
            'new_category'     => 'nullable|string|max:100',
            'copies'           => 'required|integer|min:1|max:200',
            'isbn'             => 'nullable|string|max:20|unique:books,isbn',
            'pages'            => 'nullable|integer|min:1|max:20000',
            'language'         => 'nullable|string|max:50',
            'shelf_location'   => 'nullable|string|max:50',
            'description'      => 'nullable|string|max:20000',
            'cover'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover_url'        => 'nullable|url|max:600',
        ]);

        if ($data['category_id'] === 'new') {
            if (trim((string) ($data['new_category'] ?? '')) === '') {
                return back()->withErrors(['new_category' => 'Isi nama kategori baru.'])->withInput();
            }
        } elseif (!Category::whereKey($data['category_id'])->exists()) {
            return back()->withErrors(['category_id' => 'Pilih kategori yang valid.'])->withInput();
        }

        $base = Str::slug($data['title']) ?: 'buku';
        $slug = $base;
        $n = 2;
        while (Book::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $n++;
        }

        // cover: unggahan manual diutamakan, kalau tidak ada unduh dari URL sumber
        $coverPath = null;
        $coverFailed = false;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
        } elseif (!empty($data['cover_url'])) {
            $coverPath = BookLookup::downloadCover($data['cover_url']);
            $coverFailed = $coverPath === null;
        }

        $book = DB::transaction(function () use ($data, $slug, $coverPath) {
            if ($data['category_id'] === 'new') {
                $name = trim($data['new_category']);
                $catSlug = Str::slug($name) ?: Str::random(6);
                $category = Category::where('slug', $catSlug)->orWhere('name', $name)->first()
                    ?: Category::create(['name' => $name, 'slug' => $catSlug]);
                $categoryId = $category->id;
            } else {
                $categoryId = (int) $data['category_id'];
            }

            $book = Book::create([
                'isbn' => $data['isbn'] ?? null,
                'title' => $data['title'],
                'slug' => $slug,
                'cover' => $coverPath,
                'author' => $data['author'],
                'publisher' => $data['publisher'],
                'publication_year' => $data['publication_year'],
                'pages' => $data['pages'] ?? null,
                'description' => $data['description'] ?? null,
                'category_id' => $categoryId,
                'language' => $data['language'] ?: 'Indonesia',
                'shelf_location' => $data['shelf_location'] ?? null,
            ]);

            for ($i = 1; $i <= $data['copies']; $i++) {
                BookCopy::create([
                    'book_id' => $book->id,
                    'copy_code' => 'TL-' . str_pad($book->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'condition' => 'Bagus',
                    'procurement_date' => now()->toDateString(),
                    'status' => 'Available',
                ]);
            }
            return $book;
        });

        $redirect = $request->boolean('again') ? redirect()->route('admin.books.create') : redirect()->route('admin.books.index');
        $redirect->with('ab_ok', '"' . $book->title . '" ditambahkan dengan ' . $data['copies'] . ' eksemplar.');
        if ($coverFailed) {
            $redirect->with('ab_warn', 'Cover tidak bisa diunduh otomatis. Kamu bisa mengunggahnya lewat menu Edit.');
        }
        return $redirect;
    }

    public function edit(Book $book)
    {
        $book->loadCount(['copies', 'copies as available_count' => fn ($c) => $c->where('status', 'Available')]);
        return view('admin.books.edit', ['book' => $book, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(Request $request, Book $book)
    {
        if ($request->filled('isbn')) {
            $raw = (string) $request->input('isbn');
            $request->merge(['isbn' => BookLookup::toIsbn13($raw) ?? BookLookup::cleanIsbn($raw)]);
        }

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:255',
            'publisher'        => 'required|string|max:255',
            'publication_year' => 'required|integer|between:1900,2100',
            'category_id'      => 'required|exists:categories,id',
            'add_copies'       => 'nullable|integer|min:0|max:200',
            'isbn'             => ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($book->id)],
            'pages'            => 'nullable|integer|min:1|max:20000',
            'language'         => 'nullable|string|max:50',
            'shelf_location'   => 'nullable|string|max:50',
            'description'      => 'nullable|string|max:20000',
            'cover'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $coverPath = $book->cover;
        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $coverPath = $request->file('cover')->store('covers', 'public');
        } elseif ($request->boolean('remove_cover') && $book->cover) {
            Storage::disk('public')->delete($book->cover);
            $coverPath = null;
        }

        DB::transaction(function () use ($data, $book, $coverPath) {
            // slug tidak diubah supaya tautan lama tetap berlaku
            $book->update([
                'isbn' => $data['isbn'] ?? null,
                'title' => $data['title'],
                'cover' => $coverPath,
                'author' => $data['author'],
                'publisher' => $data['publisher'],
                'publication_year' => $data['publication_year'],
                'pages' => $data['pages'] ?? null,
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'],
                'language' => $data['language'] ?: 'Indonesia',
                'shelf_location' => $data['shelf_location'] ?? null,
            ]);

            $add = (int) ($data['add_copies'] ?? 0);
            $n = $book->copies()->count();
            for ($i = 0; $i < $add; $i++) {
                do {
                    $n++;
                    $code = 'TL-' . str_pad($book->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($n, 3, '0', STR_PAD_LEFT);
                } while (BookCopy::where('copy_code', $code)->exists());

                BookCopy::create([
                    'book_id' => $book->id,
                    'copy_code' => $code,
                    'condition' => 'Bagus',
                    'procurement_date' => now()->toDateString(),
                    'status' => 'Available',
                ]);
            }
        });

        return redirect()->route('admin.books.index')->with('ab_ok', 'Buku "' . $book->title . '" diperbarui.');
    }

    public function destroy(Book $book)
    {
        // jangan hapus buku yang masih dipinjam atau punya permintaan aktif (riwayat ikut terhapus)
        if (class_exists(\App\Models\Loan::class)
            && \App\Models\Loan::where('book_id', $book->id)->whereIn('status', ['Pending', 'Borrowed'])->exists()) {
            return redirect()->route('admin.books.index')
                ->with('ab_err', 'Buku "' . $book->title . '" masih dipinjam atau punya permintaan aktif, jadi tidak bisa dihapus.');
        }

        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }
        $title = $book->title;
        $book->delete();
        return redirect()->route('admin.books.index')->with('ab_ok', 'Buku "' . $title . '" dihapus.');
    }
}