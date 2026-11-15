@php
    $unpaid = Auth::user()->loans()->get()->sum(function ($l) {
        if ($l->status === 'Borrowed') { return $l->current_fine; }
        if ($l->status === 'Returned' && !$l->fine_paid_at) { return $l->fine_amount; }
        return 0;
    });
@endphp
<x-student-layout title="Riwayat Pinjam">
    <div class="tl-card" style="margin-bottom:1.25rem">
        <div><small style="color:#7b7796">Peminjaman aktif (menunggu + dipinjam):</small>
            <b style="color:#2d2a4a"> {{ $active }} dari {{ $max }}</b></div>
        <div style="margin-top:.4rem"><small style="color:#7b7796">Denda belum dibayar:</small>
            <b style="color:{{ $unpaid > 0 ? '#c2415d' : '#2d2a4a' }}"> {{ \App\Models\Loan::rupiah($unpaid) }}</b>
            <small style="color:#8b87a8"> (denda {{ \App\Models\Loan::rupiah(config('library.fine_per_day')) }} per hari terlambat, dibayar ke admin)</small></div>
        <div style="margin-top:.4rem"><small style="color:#8b87a8">Setiap pinjaman boleh diperpanjang 1 kali selama belum lewat jatuh tempo.</small></div>
    </div>

    <div class="tl-card" style="overflow-x:auto">
        <table class="tl-table">
            <thead><tr><th>Buku</th><th>Durasi</th><th>Diajukan</th><th>Jatuh Tempo</th><th>Dikembalikan</th><th>Status</th><th>Denda</th><th style="width:170px">Aksi</th></tr></thead>
            <tbody>
            @forelse($loans as $l)
                @php $fine = $l->current_fine; $rs = $l->renewalState(); @endphp
                <tr>
                    <td>
                        <a href="{{ route('student.books.show', $l->book) }}" class="tl-link-plain">{{ $l->book->title }}</a><br>
                        <small>{{ $l->book->author }}</small>
                    </td>
                    <td>{{ $l->duration_days }} hari</td>
                    <td>{{ $l->requested_at ? $l->requested_at->format('d M Y') : '-' }}</td>
                    <td>
                        {{ $l->due_date ? $l->due_date->format('d M Y') : '-' }}
                        @if($l->isOverdue())<br><small class="tl-fine">Terlambat {{ $l->late_days }} hari, segera kembalikan</small>@endif
                    </td>
                    <td>
                        {{ $l->returned_at ? $l->returned_at->format('d M Y') : '-' }}
                        @if($l->status === 'Returned' && $l->late_days > 0)<br><small class="tl-fine">Telat {{ $l->late_days }} hari</small>@endif
                    </td>
                    <td><span class="tl-st st-{{ strtolower($l->status) }}">{{ $l->label }}</span></td>
                    <td>
                        @if($fine > 0)
                            <span class="tl-fine">{{ \App\Models\Loan::rupiah($fine) }}</span><br>
                            <small>{{ $l->status === 'Borrowed' ? 'berjalan' : ($l->fine_paid_at ? 'lunas' : 'belum dibayar') }}</small>
                        @else
                            <small>-</small>
                        @endif
                    </td>
                    <td>
                        @if($l->status === 'Pending')
                            <form method="POST" action="{{ route('student.loans.cancel', $l) }}" onsubmit="return confirm('Batalkan permintaan ini?')">@csrf @method('PATCH')<button class="tl-btn-danger" type="submit">Batal</button></form>
                        @elseif($rs === 'can')
                            <form method="POST" action="{{ route('student.loans.renew', $l) }}" class="tl-row" style="gap:.3rem;flex-wrap:nowrap">
                                @csrf
                                <select class="tl-input" style="padding:.3rem .5rem;font-size:.7rem;width:auto" name="extra_days">
                                    <option value="3">+3 hari</option>
                                    <option value="7">+7 hari</option>
                                </select>
                                <button class="tl-btn-ok" type="submit">Perpanjang</button>
                            </form>
                            @if($l->renewals->where('status', 'Rejected')->isNotEmpty())<small class="tl-fine">Perpanjangan sebelumnya ditolak</small>@endif
                        @elseif($rs === 'pending')
                            <small>Perpanjangan menunggu admin</small>
                        @elseif($rs === 'done')
                            <small>Sudah diperpanjang</small>
                        @else
                            <small>-</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="tl-empty">Kamu belum pernah meminjam. Buka Katalog Buku untuk mulai.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $loans->links() }}</div>
</x-student-layout>