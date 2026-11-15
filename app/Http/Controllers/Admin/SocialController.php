<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Challenge;
use Illuminate\Http\Request;

class SocialController extends Controller {
    // ULASAN
    public function ulasanIndex() {
        $reviews = Review::with(['user', 'book'])->latest()->get();
        return view('admin.ulasan.index', compact('reviews'));
    }
    public function destroyUlasan(Review $review) {
        $review->delete();
        return back()->with('success', 'Ulasan tidak pantas berhasil dihapus.');
    }

    // TANTANGAN (CHALLENGES)
    public function tantanganIndex() {
        $challenges = Challenge::latest()->get();
        return view('admin.tantangan.index', compact('challenges'));
    }
    public function storeTantangan(Request $request) {
        Challenge::create($request->all());
        return back()->with('success', 'Tantangan baru berhadiah XP berhasil diterbitkan!');
    }
    public function destroyTantangan(Challenge $challenge) {
        $challenge->delete();
        return back()->with('success', 'Tantangan ditarik dari peredaran.');
    }
}