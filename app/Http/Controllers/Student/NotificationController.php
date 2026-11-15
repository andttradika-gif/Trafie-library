<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $items = $user->notifications()->paginate(15);
        $unread = $user->unreadNotifications()->count();
        return view('student.notifications.index', compact('items', 'unread'));
    }

    public function open(Request $request, string $id)
    {
        $n = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $n->markAsRead();
        $url = $n->data['url'] ?? null;
        return ($url && str_starts_with($url, '/')) ? redirect($url) : redirect()->route('student.notifications.index');
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}