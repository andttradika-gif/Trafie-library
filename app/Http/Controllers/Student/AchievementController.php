<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserAchievement;
use App\Models\XpTransaction;
use App\Services\Reading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        Reading::evaluateAchievements($u);
        $metrics = Reading::metrics($u->id);
        $unlocked = UserAchievement::where('user_id', $u->id)->get()->keyBy('code');
        $xp = Reading::xp($u->id);
        $lv = Reading::levelFor($xp);
        $history = XpTransaction::where('user_id', $u->id)->latest('id')->take(10)->get();
        $badges = DB::table('challenge_participants as cp')->join('reading_challenges as c', 'c.id', '=', 'cp.challenge_id')
            ->where('cp.user_id', $u->id)->whereNotNull('cp.completed_at')->whereNotNull('c.badge_name')->select('c.badge_name', 'c.title', 'cp.completed_at')->get();
        return view('student.achievements.index', compact('metrics', 'unlocked', 'xp', 'lv', 'history', 'badges'));
    }
}