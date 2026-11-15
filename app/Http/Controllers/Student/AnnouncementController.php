<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $items = Announcement::live()->orderByDesc('is_pinned')->latest('id')->get();
        return view('student.announcements.index', compact('items'));
    }
}