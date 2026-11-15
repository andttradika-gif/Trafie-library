<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\LoanReportPdf;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    private function rules(): array
    {
        return ['dari' => 'nullable|date', 'sampai' => 'nullable|date', 'status' => 'nullable|string|max:20'];
    }

    private function filtered(Request $r, $query)
    {
        $status = (string) $r->query('status', '');
        return $query
            ->when($r->filled('dari'), fn ($q) => $q->whereDate('requested_at', '>=', $r->query('dari')))
            ->when($r->filled('sampai'), fn ($q) => $q->whereDate('requested_at', '<=', $r->query('sampai')))
            ->when(array_key_exists($status, Loan::LABELS), fn ($q) => $q->where('status', $status));
    }

    private function totals(Request $request): array
    {
        $counts = $this->filtered($request, Loan::query())
            ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $totalFine = (int) $this->filtered($request, Loan::query())->where('status', 'Returned')->sum('fine_amount');
        $unpaidFine = (int) $this->filtered($request, Loan::query())
            ->where('status', 'Returned')->whereNull('fine_paid_at')->sum('fine_amount');

        return [$counts, $totalFine, $unpaidFine];
    }

    private const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    /** Statistik per bulan (maks. 24 bulan terakhir dalam periode) dan buku terpopuler. */
    private function series(Request $request): array
    {
        $loans = $this->filtered($request, Loan::query())->whereNotNull('requested_at')->get();

        $dari = (string) $request->query('dari', '');
        $sampai = (string) $request->query('sampai', '');
        $end = $sampai !== '' ? Carbon::parse($sampai)->startOfMonth() : now()->startOfMonth();
        $first = $loans->sortBy('requested_at')->first();
        if ($dari !== '') {
            $start = Carbon::parse($dari)->startOfMonth();
        } elseif ($first) {
            $start = Carbon::parse($first->requested_at)->startOfMonth();
        } else {
            $start = $end->copy();
        }
        if ($start->gt($end)) {
            $start = $end->copy();
        }
        $span = ($end->year - $start->year) * 12 + ($end->month - $start->month);
        if ($span > 23) {
            $start = $end->copy()->subMonths(23);
        }

        $months = [];
        for ($m = $start->copy(); $m->lte($end); $m->addMonth()) {
            $months[$m->format('Y-m')] = [
                'label' => self::BULAN[$m->month - 1], 'year' => $m->year,
                'total' => 0, 'borrowed' => 0, 'returned' => 0, 'other' => 0, 'fine' => 0, 'unpaid' => 0,
            ];
        }

        foreach ($loans as $l) {
            $k = Carbon::parse($l->requested_at)->format('Y-m');
            if (!isset($months[$k])) {
                continue;
            }
            $months[$k]['total']++;
            if ($l->status === 'Borrowed') {
                $months[$k]['borrowed']++;
            } elseif ($l->status === 'Returned') {
                $months[$k]['returned']++;
                $months[$k]['fine'] += (int) $l->fine_amount;
                if (!$l->fine_paid_at) {
                    $months[$k]['unpaid'] += (int) $l->fine_amount;
                }
            } else {
                $months[$k]['other']++;
            }
        }

        $counts = $loans->groupBy('book_id')->map->count()->sortDesc()->take(5);
        $grand = max(1, $loans->count());
        $books = Book::whereIn('id', $counts->keys())->get()->keyBy('id');
        $top = [];
        foreach ($counts as $bookId => $n) {
            $b = $books[$bookId] ?? null;
            $top[] = [
                'title' => (string) ($b->title ?? 'Buku'),
                'author' => (string) ($b->author ?? ''),
                'cover' => ($b && $b->cover) ? (string) $b->cover : null,
                'n' => (int) $n,
                'pct' => (int) round($n / $grand * 100),
            ];
        }

        return [array_values($months), $top, $start, $end];
    }

    public function index(Request $request)
    {
        $request->validate($this->rules());

        // Tanpa pilihan periode: tampilkan 12 bulan terakhir. "Semua waktu" = dari kosong.
        if (!$request->query->has('dari') && !$request->query->has('sampai')) {
            $request->query->set('dari', now()->subMonths(11)->startOfMonth()->toDateString());
        }

        $loans = $this->filtered($request, Loan::with(['user', 'book', 'copy']))
            ->latest('id')->paginate(20)->withQueryString();

        [$counts, $totalFine, $unpaidFine] = $this->totals($request);
        [$months, $top, $start, $end] = $this->series($request);

        $dari = (string) $request->query('dari', '');
        $sampai = (string) $request->query('sampai', '');
        $status = (string) $request->query('status', '');

        $presets = [];
        foreach ([3 => '3 bulan terakhir', 6 => '6 bulan terakhir', 12 => '12 bulan terakhir'] as $n => $label) {
            $d = now()->subMonths($n - 1)->startOfMonth()->toDateString();
            $presets[] = ['label' => $label, 'on' => $dari === $d && $sampai === '',
                'q' => ['dari' => $d, 'sampai' => '', 'status' => $status]];
        }
        $presets[] = ['label' => 'Semua waktu', 'on' => $dari === '' && $sampai === '',
            'q' => ['dari' => '', 'sampai' => '', 'status' => $status]];

        return view('admin.reports.index', [
            'loans' => $loans,
            'counts' => $counts,
            'totalFine' => $totalFine,
            'unpaidFine' => $unpaidFine,
            'months' => $months,
            'top' => $top,
            'presets' => $presets,
            'periode' => self::BULAN[$start->month - 1] . ' ' . $start->year . ' - ' . self::BULAN[$end->month - 1] . ' ' . $end->year,
            'dari' => $dari,
            'sampai' => $sampai,
            'status' => $status,
        ]);
    }

    /** Unduh laporan dalam bentuk PDF (maksimal 3000 baris terbaru sesuai filter). */
    private function tgl($v, bool $panjang = false): string
    {
        $c = $v instanceof \Carbon\CarbonInterface ? $v : \Illuminate\Support\Carbon::parse($v);
        $b = $panjang
            ? ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            : ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return $c->format('d') . ' ' . $b[(int) $c->format('n')] . ' ' . $c->format('Y');
    }

    public function export(Request $request)
    {
        $request->validate($this->rules());
        @set_time_limit(120);

        [$counts, $totalFine, $unpaidFine] = $this->totals($request);

        $loans = $this->filtered($request, Loan::with(['user', 'book', 'copy']))
            ->latest('id')->limit(3000)->get();

        $d = fn ($v) => $v ? $this->tgl($v) : '-';
        $rows = [];
        foreach ($loans as $l) {
            $fine = (int) $l->current_fine;
            $rows[] = [
                'name' => (string) ($l->user->name ?? '-'),
                'nisn' => (string) ($l->user->nisn ?? ''),
                'title' => (string) ($l->book->title ?? '-'),
                'code' => (string) ($l->copy->copy_code ?? ''),
                'requested' => $d($l->requested_at),
                'due' => $d($l->due_date),
                'returned' => $d($l->returned_at),
                'status' => (string) $l->status,
                'label' => (string) $l->label,
                'fine' => $fine > 0 ? Loan::rupiah($fine) : '-',
                'fine_note' => $fine > 0 ? ($l->status === 'Borrowed' ? 'berjalan' : ($l->fine_paid_at ? 'lunas' : 'belum dibayar')) : '',
            ];
        }

        $dari = (string) $request->query('dari', '');
        $sampai = (string) $request->query('sampai', '');
        $status = (string) $request->query('status', '');
        $fmt = fn ($v) => $this->tgl($v, true);
        $periode = ($dari !== '' || $sampai !== '')
            ? ($dari !== '' ? $fmt($dari) : 'awal') . ' - ' . ($sampai !== '' ? $fmt($sampai) : 'sekarang')
            : 'Semua tanggal';

        $pdf = LoanReportPdf::render($rows, [
            'app' => (string) config('app.name', 'Trafie-library'),
            'filters' => 'Periode ' . $periode . '  |  Status: ' . (Loan::LABELS[$status] ?? 'Semua'),
            'generated' => $this->tgl(now(), true) . ' ' . now()->format('H:i'),
            'periode' => $periode,
            'statusLabel' => (string) (Loan::LABELS[$status] ?? 'Semua'),
            'dateLong' => $this->tgl(now(), true),
            'docno' => 'LPB/' . now()->format('Y/m/d-His'),
            'petugas' => (string) optional($request->user())->name,
            'total' => (int) $counts->sum(),
            'borrowed' => (int) ($counts['Borrowed'] ?? 0),
            'returned' => (int) ($counts['Returned'] ?? 0),
            'fine' => Loan::rupiah($totalFine),
            'unpaid' => Loan::rupiah($unpaidFine),
        ]);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-peminjaman-' . now()->format('Ymd-His') . '.pdf"',
            'Content-Length' => (string) strlen($pdf),
        ]);
    }
}