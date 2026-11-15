<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookReview;
use App\Models\Loan;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $data = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'body'   => 'required|string|min:10|max:500',
        ]);
        $user = $request->user();

        $pernahKembalikan = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'Returned')
            ->exists();

        if (!$pernahKembalikan) {
            return back()->with('error', 'Kamu bisa menulis ulasan setelah mengembalikan buku ini.');
        }

        // Kirim ulang = ubah ulasan, dan persetujuan admin diulang
        BookReview::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['rating' => (int) $data['rating'], 'body' => $data['body'], 'approved_at' => null]
        );

        return back()->with('success', 'Ulasan terkirim. Ulasan tampil di publik setelah disetujui admin.');
    }
}