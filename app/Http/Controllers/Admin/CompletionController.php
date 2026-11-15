<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadingCompletion;
use App\Services\Reading;

class CompletionController extends Controller
{
    public function index()
    {
        $items = ReadingCompletion::with(['user', 'book', 'loan'])->latest('id')->paginate(15);
        $stats = [
            'verified' => ReadingCompletion::where('status', 'Verified')->count(),
            'revoked'  => ReadingCompletion::where('status', 'Revoked')->count(),
        ];
        return view('admin.completions.index', compact('items', 'stats'));
    }

    public function revoke(ReadingCompletion $completion)
    {
        if ($completion->status !== 'Verified') { return back()->with('error', 'Penyelesaian ini sudah dibatalkan.'); }
        Reading::revoke($completion);
        return back()->with('success', 'Penyelesaian dibatalkan dan XP-nya ditarik.');
    }
}