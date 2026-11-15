<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BookFavorite;
use App\Models\ReadingCompletion;
use App\Services\Reading;
use Illuminate\Http\Request;

class GalaxyController extends Controller
{
    public function index(Request $request)
    {
        $uid = $request->user()->id;
        $completions = ReadingCompletion::with('book.category')->where('user_id', $uid)->where('status', 'Verified')->orderBy('completed_at')->get();
        $favIds = BookFavorite::where('user_id', $uid)->pluck('book_id')->all();
        $lv = Reading::levelFor(Reading::xp($uid));
        return view('student.galaxy.index', compact('completions', 'favIds', 'lv'));
    }
}