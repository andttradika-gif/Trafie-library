<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $v = [
            'fine_per_day'      => (int) config('library.fine_per_day', 1000),
            'max_loans'         => (int) config('library.max_loans', 3),
            'reserve_hold_days' => (int) config('library.reserve_hold_days', 2),
        ];
        return view('admin.settings.edit', compact('v'));
    }

    public function update(Request $request)
    {
        $d = $request->validate([
            'fine_per_day'      => 'required|integer|min:0|max:100000',
            'max_loans'         => 'required|integer|min:1|max:20',
            'reserve_hold_days' => 'required|integer|min:1|max:14',
        ]);
        foreach ($d as $k => $val) { Setting::put('lib.' . $k, (string) $val); }
        return back()->with('success', 'Pengaturan disimpan dan langsung berlaku.');
    }
}