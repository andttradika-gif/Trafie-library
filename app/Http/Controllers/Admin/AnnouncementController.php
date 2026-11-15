<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $items = Announcement::orderByDesc('is_pinned')->latest('id')->get();
        return view('admin.announcements.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:150',
            'body'    => 'required|string|max:2000',
            'type'    => 'required|in:info,penting,event',
            'ends_on' => 'nullable|date|after_or_equal:today',
        ]);
        $data['is_pinned'] = $request->boolean('is_pinned');
        $data['is_active'] = true;
        $data['published_at'] = now();
        $data['created_by'] = $request->user()->id;
        Announcement::create($data);
        return back()->with('success', 'Pengumuman diterbitkan.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        return back()->with('success', $announcement->is_active ? 'Pengumuman ditampilkan.' : 'Pengumuman disembunyikan.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Pengumuman dihapus.');
    }
}