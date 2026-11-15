<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ReadingChallenge;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index()
    {
        $challenges = ReadingChallenge::with('category')->withCount('participants')->latest('id')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.challenges.index', compact('challenges', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'description'  => 'nullable|string|max:1000',
            'starts_on'    => 'required|date',
            'ends_on'      => 'required|date|after_or_equal:starts_on',
            'target_books' => 'required|integer|min:1|max:100',
            'category_id'  => 'nullable|exists:categories,id',
            'reward_xp'    => 'required|integer|min:0|max:1000',
            'badge_name'   => 'nullable|string|max:60',
        ]);
        $data['status'] = 'Active';
        $data['created_by'] = $request->user()->id;
        ReadingChallenge::create($data);
        return back()->with('success', 'Tantangan "' . $data['title'] . '" diterbitkan.');
    }

    public function toggle(ReadingChallenge $challenge)
    {
        $challenge->update(['status' => $challenge->status === 'Active' ? 'Closed' : 'Active']);
        return back()->with('success', 'Status tantangan diperbarui.');
    }

    public function destroy(ReadingChallenge $challenge)
    {
        $challenge->delete();
        return back()->with('success', 'Tantangan dihapus.');
    }
}