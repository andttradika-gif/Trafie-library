<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function edit(Request $request)
    {
        return view('account.edit', ['user' => $request->user()]);
    }

    public function updateName(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $request->user()->update(['name' => $data['name']]);
        return back()->with('success', 'Nama diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);
        $request->user()->update(['password' => $data['password']]);
        return back()->with('success', 'Password berhasil diganti.');
    }
}