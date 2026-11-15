@props(['title' => 'Dashboard'])
@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Schema;

    $notes = [];
    if (($n = \App\Models\Loan::where('status', 'Pending')->count()) > 0) {
        $notes[] = ['url' => route('admin.loans.index', ['status' => 'Pending']), 'text' => $n . ' permintaan pinjam'];
    }
    if (($n = \App\Models\LoanRenewal::where('status', 'Pending')->count()) > 0) {
        $notes[] = ['url' => route('admin.renewals.index'), 'text' => $n . ' permintaan perpanjangan'];
    }
    if (($n = \App\Models\BookReview::whereNull('approved_at')->count()) > 0) {
        $notes[] = ['url' => route('admin.reviewboard.index', ['status' => 'pending']), 'text' => $n . ' ulasan baru'];
    }
    if (Schema::hasTable('fine_payments') && ($n = \App\Models\FinePayment::where('status', 'Pending')->count()) > 0) {
        $notes[] = ['url' => route('admin.fines.index'), 'text' => $n . ' bukti pembayaran denda'];
    }

    $mk = fn ($route, $match, $label, $icon) => Route::has($route)
        ? ['route' => $route, 'match' => $match, 'label' => $label, 'icon' => $icon]
        : ['label' => $label, 'icon' => $icon, 'soon' => true];

    $menu = [
        $mk('admin.dashboard', 'admin.dashboard', 'Dashboard', 'home'),
        $mk('admin.books.index', 'admin.books.*', 'Manajemen Buku', 'book'),
        $mk('admin.categories.index', 'admin.categories.*', 'Kategori Buku', 'grid'),
        $mk('admin.extra.copies', 'admin.extra.copies', 'Eksemplar Buku', 'layers'),
        $mk('admin.students.index', 'admin.students.*', 'Manajemen Siswa', 'users'),
        $mk('admin.loans.index', 'admin.loans.*', 'Peminjaman', 'bookmark'),
        $mk('admin.reservations.index', 'admin.reservations.*', 'Reservasi & Waiting List', 'clock'),
        $mk('admin.renewals.index', 'admin.renewals.*', 'Perpanjangan', 'refresh'),
        $mk('admin.extra.returns', 'admin.extra.returns', 'Pengembalian', 'ret'),
        $mk('admin.fines.index', 'admin.fines.*', 'Denda & Pembayaran', 'coin'),
        $mk('admin.reviewboard.index', 'admin.reviewboard.*', 'Ulasan & Rating', 'star'),
        $mk('admin.challenges.index', 'admin.challenges.*', 'Tantangan Membaca', 'award'),
        $mk('admin.completions.index', 'admin.completions.*', 'Verifikasi Penyelesaian', 'log'),
        $mk('admin.announcements.index', 'admin.announcements.*', 'Pengumuman', 'mega'),
        $mk('admin.reports.index', 'admin.reports.*', 'Laporan & Statistik', 'chart'),
        $mk('admin.extra.calendar', 'admin.extra.calendar', 'Kalender', 'calendar'),
        $mk('admin.extra.activity', 'admin.extra.activity', 'Log Aktivitas', 'log'),
        $mk('admin.settings.edit', 'admin.settings.*', 'Pengaturan', 'cog'),
        $mk('account.edit', 'account.*', 'Akun Saya', 'user'),
    ];
@endphp
<x-shell role="Administrator" :title="$title" :items="$menu">
    @if(count($notes) > 0)
        <div class="tl-alert warn">
            Menunggu keputusan kamu:
            @foreach($notes as $nt)@if(!$loop->first), @endif<a href="{{ $nt['url'] }}">{{ $nt['text'] }}</a>@endforeach
        </div>
    @endif
    {{ $slot }}
</x-shell>