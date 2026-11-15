<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookFavorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $uid = $request->user()->id;
        $ids = BookFavorite::where('user_id', $uid)->latest('id')->pluck('book_id');
        $favorites = Book::with('category')->whereIn('id', $ids)->get()->sortBy(fn ($b) => $ids->search($b->id))->values();

        $q = trim((string) $request->query('q', ''));
        $discover = Book::with('category')
            ->whereNotIn('id', $ids)
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('title', 'like', "%$q%")->orWhere('author', 'like', "%$q%")))
            ->latest('id')->take(12)->get();

        return view('student.favorites.index', compact('favorites', 'discover', 'q'));
    }

    public function toggle(Request $request, Book $book)
    {
        $uid = $request->user()->id;
        $fav = BookFavorite::where('user_id', $uid)->where('book_id', $book->id)->first();
        if ($fav) {
            $fav->delete();
            return back()->with('success', '"' . $book->title . '" dihapus dari favorit.');
        }
        BookFavorite::create(['user_id' => $uid, 'book_id' => $book->id]);
        return back()->with('success', '"' . $book->title . '" ditambahkan ke favorit.');
    }
}