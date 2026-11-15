<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\ReadingCompletion;
use App\Models\ReadingLog;
use App\Models\ReadingProgress;
use App\Services\Reading;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    private function hasLoan(int $uid, int $bookId): bool
    {
        return Loan::where('user_id', $uid)->where('book_id', $bookId)->whereIn('status', ['Borrowed', 'Returned'])->exists();
    }

    public function index(Request $request)
    {
        $uid = $request->user()->id;
        $items = ReadingProgress::with('book')->where('user_id', $uid)->latest('updated_at')->get();
        $tracked = $items->pluck('book_id')->all();
        $loans = Loan::with('book')->where('user_id', $uid)->whereIn('status', ['Borrowed', 'Returned'])->whereNotIn('book_id', $tracked)->latest('id')->get()->unique('book_id')->values();
        $others = Book::whereNotIn('id', array_merge($tracked, $loans->pluck('book_id')->all()))->orderBy('title')->take(150)->get(['id', 'title']);
        $returnedIds = Loan::where('user_id', $uid)->where('status', 'Returned')->pluck('book_id')->unique()->all();
        $compMap = ReadingCompletion::where('user_id', $uid)->get()->keyBy('book_id');
        return view('student.reading.index', compact('items', 'loans', 'others', 'returnedIds', 'compMap'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['book_id' => 'required|exists:books,id', 'status' => 'required|in:Want,Reading']);
        $uid = $request->user()->id;
        $book = Book::findOrFail($data['book_id']);
        if (ReadingProgress::where('user_id', $uid)->where('book_id', $book->id)->exists()) {
            return back()->with('error', 'Buku ini sudah ada di daftarmu.');
        }
        if ($data['status'] === 'Reading' && !$this->hasLoan($uid, $book->id)) {
            return back()->with('error', 'Pinjam buku ini dulu sebelum mulai mencatat progres. Atau pilih "Want to Read".');
        }
        ReadingProgress::create([
            'user_id' => $uid, 'book_id' => $book->id, 'status' => $data['status'], 'current_page' => 0,
            'total_pages' => $book->pages, 'started_at' => $data['status'] === 'Reading' ? now() : null, 'last_activity_at' => now(),
        ]);
        return back()->with('success', '"' . $book->title . '" ditambahkan.');
    }

    public function update(Request $request, ReadingProgress $progress)
    {
        $uid = $request->user()->id;
        abort_unless($progress->user_id === $uid, 403);
        if ($progress->status === 'Completed') { return back()->with('error', 'Buku ini sudah selesai dan terverifikasi.'); }
        $action = (string) $request->input('action');

        if ($action === 'page') {
            if ($progress->status !== 'Reading') { return back()->with('error', 'Mulai membaca dulu sebelum mencatat halaman.'); }
            $data = $request->validate(['current_page' => 'required|integer|min:0|max:100000']);
            $page = (int) $data['current_page'];
            if ($progress->total_pages && $page > $progress->total_pages) {
                return back()->with('error', 'Halaman melebihi total halaman buku (' . $progress->total_pages . ').');
            }
            $delta = max(0, $page - (int) $progress->current_page);
            $progress->update(['current_page' => $page, 'last_activity_at' => now(), 'started_at' => $progress->started_at ?: now()]);
            if ($delta > 0) {
                $log = ReadingLog::firstOrCreate(['user_id' => $uid, 'book_id' => $progress->book_id, 'log_date' => today()->toDateString()], ['pages_read' => 0]);
                $log->increment('pages_read', $delta);
                Reading::evaluateAchievements($request->user());
            }
            return back()->with('success', 'Progres tersimpan: halaman ' . $page . '.');
        }

        if ($action === 'start') {
            if (!$this->hasLoan($uid, $progress->book_id)) { return back()->with('error', 'Pinjam buku ini dulu sebelum mulai membaca.'); }
            $progress->update(['status' => 'Reading', 'started_at' => $progress->started_at ?: now(), 'last_activity_at' => now()]);
            return back()->with('success', 'Selamat membaca!');
        }

        if ($action === 'pause') {
            if ($progress->status !== 'Reading') { return back()->with('error', 'Hanya buku yang sedang dibaca yang bisa dijeda.'); }
            $progress->update(['status' => 'Paused']);
            return back()->with('success', 'Buku dijeda.');
        }

        if ($action === 'request') {
            if ($progress->status !== 'Reading') { return back()->with('error', 'Buku harus berstatus Currently Reading.'); }
            if (!$this->hasLoan($uid, $progress->book_id)) { return back()->with('error', 'Buku harus pernah kamu pinjam.'); }
            if ($progress->total_pages && $progress->current_page < (int) ceil($progress->total_pages * 0.9)) {
                return back()->with('error', 'Catat progres sampai minimal 90% halaman dulu.');
            }
            $progress->update(['completion_requested_at' => now()]);
            return back()->with('success', 'Penyelesaian diajukan. Kembalikan bukunya ke perpustakaan, lalu isi rating dan refleksi.');
        }

        if ($action === 'remove') {
            $progress->delete();
            return back()->with('success', 'Dihapus dari daftar.');
        }

        return back()->with('error', 'Aksi tidak dikenal.');
    }

    public function complete(Request $request, ReadingProgress $progress)
    {
        abort_unless($progress->user_id === $request->user()->id, 403);
        $data = $request->validate(
            ['rating' => 'required|integer|between:1,5', 'reflection' => 'required|string|min:20|max:1000'],
            ['reflection.min' => 'Refleksi minimal 20 karakter.', 'reflection.required' => 'Tulis refleksi singkat tentang buku ini.']
        );
        [$ok, $msg] = Reading::complete($request->user(), (int) $progress->book_id, (int) $data['rating'], trim($data['reflection']));
        return back()->with($ok ? 'success' : 'error', $msg);
    }
}