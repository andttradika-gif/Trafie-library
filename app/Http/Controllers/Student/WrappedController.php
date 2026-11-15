<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BookFavorite;
use App\Models\ReadingCompletion;
use App\Models\ReadingLog;
use App\Models\UserAchievement;
use App\Models\XpTransaction;
use App\Services\Reading;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WrappedController extends Controller
{
    public function index(Request $request)
    {
        $uid = $request->user()->id;
        $type = $request->query('type') === 'year' ? 'year' : 'month';
        $year = max(2020, min(2100, (int) $request->query('year', now()->year)));
        $month = max(1, min(12, (int) $request->query('month', now()->month)));
        $from = $type === 'year' ? Carbon::create($year, 1, 1)->startOfDay() : Carbon::create($year, $month, 1)->startOfDay();
        $to = $type === 'year' ? $from->copy()->endOfYear() : $from->copy()->endOfMonth();

        $comp = ReadingCompletion::with('book.category')->where('user_id', $uid)->where('status', 'Verified')->whereBetween('completed_at', [$from, $to])->get();
        $genres = $comp->groupBy(fn ($c) => $c->book->category->name ?? 'Umum')->map->count()->sortDesc();
        $best = $comp->sortByDesc('rating')->first();
        $favBook = null;
        if ($comp->isNotEmpty()) {
            $fid = BookFavorite::where('user_id', $uid)->whereIn('book_id', $comp->pluck('book_id'))->value('book_id');
            $favBook = $fid ? optional($comp->firstWhere('book_id', $fid))->book : null;
        }
        $xpPeriod = (int) XpTransaction::where('user_id', $uid)->whereBetween('created_at', [$from, $to])->sum('amount');
        $ach = UserAchievement::where('user_id', $uid)->whereBetween('unlocked_at', [$from, $to])->get();

        $names = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $byDay = ReadingLog::where('user_id', $uid)->whereBetween('log_date', [$from->toDateString(), $to->toDateString()])->get()
            ->groupBy(fn ($l) => $l->log_date->dayOfWeekIso)->map(fn ($g) => $g->sum('pages_read'))->sortDesc();
        $activeDay = $byDay->isNotEmpty() ? $names[$byDay->keys()->first()] : null;
        $pages = (int) $byDay->sum();

        [$cur, $bestStreak] = Reading::streaks($uid);
        $lv = Reading::levelFor(Reading::xp($uid));
        $label = $type === 'year' ? 'Tahun ' . $year : ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$month] . ' ' . $year;

        return view('student.wrapped.index', compact('type', 'year', 'month', 'comp', 'genres', 'best', 'favBook', 'xpPeriod', 'ach', 'activeDay', 'pages', 'cur', 'bestStreak', 'lv', 'label'));
    }
}