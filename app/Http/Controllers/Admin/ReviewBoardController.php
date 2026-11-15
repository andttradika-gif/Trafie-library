<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewBoardController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->query('status', '');
        $rating = (int) $request->query('rating', 0);
        $q = trim((string) $request->query('q', ''));

        $reviews = BookReview::with(['user', 'book'])
            ->when($status === 'pending', fn ($x) => $x->whereNull('approved_at'))
            ->when($status === 'approved', fn ($x) => $x->whereNotNull('approved_at'))
            ->when($rating >= 1 && $rating <= 5, fn ($x) => $x->where('rating', $rating))
            ->when($q !== '', fn ($x) => $x->where(function ($w) use ($q) {
                $w->whereHas('book', fn ($b) => $b->where('title', 'like', "%$q%"))
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$q%"))
                  ->orWhere('body', 'like', "%$q%");
            }))
            ->orderByRaw('approved_at is null desc')->latest('id')
            ->paginate(12)->withQueryString();

        $stats = [
            'total'   => BookReview::count(),
            'avg'     => round((float) BookReview::whereNotNull('approved_at')->avg('rating'), 1),
            'pending' => BookReview::whereNull('approved_at')->count(),
            'approved' => BookReview::whereNotNull('approved_at')->count(),
        ];
        $dist = BookReview::whereNotNull('approved_at')->selectRaw('rating, count(*) as c')->groupBy('rating')->pluck('c', 'rating');
        $distMax = max(1, (int) $dist->max());

        $top = DB::table('book_reviews')->join('books', 'books.id', '=', 'book_reviews.book_id')
            ->whereNotNull('book_reviews.approved_at')
            ->select('books.title', 'books.author', DB::raw('AVG(book_reviews.rating) as avg_rating'), DB::raw('COUNT(*) as total'))
            ->groupBy('books.id', 'books.title', 'books.author')
            ->orderByDesc('avg_rating')->orderByDesc('total')->take(5)->get();

        return view('admin.reviewboard.index', compact('reviews', 'stats', 'dist', 'distMax', 'top', 'status', 'rating', 'q'));
    }

    public function hide(BookReview $review)
    {
        if (!$review->approved_at) { return back()->with('error', 'Ulasan ini memang belum tampil.'); }
        $review->update(['approved_at' => null]);
        return back()->with('success', 'Ulasan disembunyikan dari publik.');
    }
}