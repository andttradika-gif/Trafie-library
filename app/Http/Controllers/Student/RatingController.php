<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookReview;
use App\Models\Loan;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $uid = $request->user()->id;

        $data = $request->validate(
            ['rating' => 'required|integer|between:1,5', 'comment' => 'nullable|string|max:1000'],
            ['rating.required' => 'Pilih jumlah bintang dulu.', 'rating.between' => 'Rating harus 1 sampai 5 bintang.']
        );

        $pernahPinjam = Loan::where('user_id', $uid)->where('book_id', $book->id)->whereIn('status', ['Borrowed', 'Returned'])->exists();
        if (!$pernahPinjam) {
            return back()->with('error', 'Kamu hanya bisa memberi rating buku yang pernah kamu pinjam.');
        }

        $body = trim((string) ($data['comment'] ?? ''));
        $existing = BookReview::where('user_id', $uid)->where('book_id', $book->id)->first();

        // Rating dan ulasan langsung tampil, tanpa menunggu admin.
        $approved = ($existing && $existing->approved_at) ? $existing->approved_at : now();

        BookReview::updateOrCreate(
            ['user_id' => $uid, 'book_id' => $book->id],
            ['rating' => (int) $data['rating'], 'body' => $body, 'approved_at' => $approved]
        );

        $msg = $approved
            ? 'Rating ' . (int) $data['rating'] . ' bintang tersimpan. Terima kasih!'
            : 'Ulasanmu tersimpan dan langsung tampil. Terima kasih!';
        return back()->with('success', $msg);
    }

    public function destroy(Request $request, Book $book)
    {
        $deleted = BookReview::where('user_id', $request->user()->id)->where('book_id', $book->id)->delete();
        return back()->with($deleted ? 'success' : 'error', $deleted ? 'Ratingmu dihapus.' : 'Kamu belum memberi rating buku ini.');
    }
}