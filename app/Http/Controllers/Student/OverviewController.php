<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BookFavorite;
use App\Models\BookReview;
use App\Models\Loan;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $stats = [
            'total'    => Loan::where('user_id', $user->id)->whereIn('status', ['Borrowed', 'Returned'])->count(),
            'active'   => Loan::where('user_id', $user->id)->where('status', 'Borrowed')->count(),
            'returned' => Loan::where('user_id', $user->id)->where('status', 'Returned')->count(),
            'favorites' => BookFavorite::where('user_id', $user->id)->count(),
            'reviews'  => BookReview::where('user_id', $user->id)->count(),
            'unpaid'   => (int) Loan::where('user_id', $user->id)->where('status', 'Returned')->where('fine_amount', '>', 0)->whereNull('fine_paid_at')->sum('fine_amount'),
        ];
        return view('student.profile.show', compact('user', 'stats'));
    }
}