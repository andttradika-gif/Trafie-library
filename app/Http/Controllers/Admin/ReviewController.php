<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookReview;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = BookReview::with(['user', 'book'])
            ->orderByRaw('approved_at is null desc')
            ->latest('id')
            ->paginate(15);

        $pending = BookReview::whereNull('approved_at')->count();

        return view('admin.reviews.index', compact('reviews', 'pending'));
    }

    public function approve(BookReview $review)
    {
        if ($review->approved_at) {
            return back()->with('error', 'Ulasan ini sudah disetujui.');
        }
        $review->update(['approved_at' => now()]);
        return back()->with('success', 'Ulasan disetujui dan tampil di publik.');
    }

    public function destroy(BookReview $review)
    {
        $review->delete();
        return back()->with('success', 'Ulasan dihapus.');
    }
}