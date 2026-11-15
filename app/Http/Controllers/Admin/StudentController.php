<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $students = User::where('role', 'student')
            ->withCount([
                'loans',
                'loans as active_loans_count' => fn ($c) => $c->whereIn('status', ['Pending', 'Borrowed']),
            ])
            ->when($q !== '', fn ($x) => $x->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")->orWhere('nisn', 'like', "%$q%")->orWhere('email', 'like', "%$q%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.index', compact('students', 'q'));
    }

    public function show(User $student)
    {
        abort_if($student->role !== 'student', 404);

        $loans = $student->loans()->with('book')->latest('id')->paginate(10);
        return view('admin.students.show', compact('student', 'loans'));
    }

    public function resetPassword(User $student)
    {
        abort_if($student->role !== 'student', 404);

        $new = Str::random(10);
        $student->update(['password' => $new]);

        return back()->with('success', 'Password baru untuk ' . $student->name . ': ' . $new . ' (catat sekarang, tidak akan ditampilkan lagi).');
    }
}