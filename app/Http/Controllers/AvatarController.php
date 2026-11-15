<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function update(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'], [
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.max' => 'Ukuran foto maksimal 2 MB.',
        ]);
        $user = $request->user();
        if ($user->avatar) { Storage::disk('public')->delete($user->avatar); }
        $user->forceFill(['avatar' => $request->file('avatar')->store('avatars', 'public')])->save();
        return back()->with('success', 'Foto profil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        if ($user->avatar) { Storage::disk('public')->delete($user->avatar); }
        $user->forceFill(['avatar' => null])->save();
        return back()->with('success', 'Foto profil dihapus.');
    }
}