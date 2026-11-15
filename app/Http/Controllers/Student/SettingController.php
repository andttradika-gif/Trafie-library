<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit(Request $request)
    {
        $s = UserSetting::forUser($request->user()->id);
        return view('student.settings.edit', compact('s'));
    }

    public function update(Request $request)
    {
        $data = $request->validate(['default_duration' => 'required|in:3,7,10']);
        $s = UserSetting::forUser($request->user()->id);
        $s->fill([
            'default_duration' => (int) $data['default_duration'],
            'n_due'           => $request->boolean('n_due'),
            'n_reservation'   => $request->boolean('n_reservation'),
            'n_fines'         => $request->boolean('n_fines'),
            'n_announcements' => $request->boolean('n_announcements'),
        ])->save();
        return back()->with('success', 'Pengaturan disimpan.');
    }
}