<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReadingChallenge;
use App\Services\Reading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $uid = $request->user()->id;
        $rows = ReadingChallenge::with('category')->latest('id')->get();
        $mine = DB::table('challenge_participants')->where('user_id', $uid)->get()->keyBy('challenge_id');
        $progress = [];
        foreach ($rows as $c) {
            if (isset($mine[$c->id])) { $progress[$c->id] = Reading::challengeCount($uid, $c, $mine[$c->id]->joined_at); }
        }
        return view('student.challenges.index', compact('rows', 'mine', 'progress'));
    }

    public function join(Request $request, ReadingChallenge $challenge)
    {
        $uid = $request->user()->id;
        if ($challenge->status !== 'Active' || $challenge->ends_on->copy()->endOfDay()->isPast()) {
            return back()->with('error', 'Tantangan ini sudah ditutup.');
        }
        $exists = DB::table('challenge_participants')->where('challenge_id', $challenge->id)->where('user_id', $uid)->exists();
        if ($exists) { return back()->with('error', 'Kamu sudah bergabung di tantangan ini.'); }
        DB::table('challenge_participants')->insert(['challenge_id' => $challenge->id, 'user_id' => $uid, 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        Reading::evaluateChallenges($request->user());
        return back()->with('success', 'Kamu bergabung di "' . $challenge->title . '". Buku yang kamu selesaikan mulai hari ini ikut dihitung.');
    }
}