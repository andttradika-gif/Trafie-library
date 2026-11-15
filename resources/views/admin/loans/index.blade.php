<x-admin-layout title="Peminjaman">
    <div class="tl-tabs">
        <a href="{{ route('admin.loans.index') }}" class="{{ $status === '' ? 'on' : '' }}">Semua</a>
        @foreach(\App\Models\Loan::LABELS as $k => $v)
            <a href="{{ route('admin.loans.index', ['status' => $k]) }}" class="{{ $status === $k ? 'on' : '' }}">{{ $v }} ({{ $counts[$k] ?? 0 }})</a>
        @endforeach
    </div>
    <p style="margin:0 0 1rem;font-size:.72rem;color:#8b87a8">Denda keterlambatan: {{ \App\Models\Loan::rupiah(config('library.fine_per_day')) }} per hari. Denda dihitung final saat buku dikembalikan.</p>

    <div class="tl-card" style="overflow-x:auto">
        <table class="tl-table">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Durasi</th><th>Diajukan</th><th>Jatuh Tempo</th><th>Status</th><th>Denda</th><th style="width:170px">Aksi</th></tr></thead>
            <tbody>
            @forelse($loans as $l)
                @php $fine = $l->current_fine; @endphp
                <tr>
                    <td><b>{{ $l->user->name }}</b><br><small>NISN {{ $l->user->nisn ?: '-' }}</small></td>
                    <td><b>{{ $l->book->title }}</b><br><small>{{ $l->copy ? $l->copy->copy_code : 'eksemplar belum ditentukan' }}</small></td>
                    <td>{{ $l->duration_days }} hari</td>
                    <td>{{ $l->requested_at ? $l->requested_at->format('d M Y H:i') : '-' }}</td>
                    <td>
                        {{ $l->due_date ? $l->due_date->format('d M Y') : '-' }}
                        @if($l->isOverdue())<br><small class="tl-fine">Terlambat {{ $l->late_days }} hari</small>@endif
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
                        <div class="tl-row" style="gap:.4rem">
                        @if($l->status === 'Pending')
                            <form method="POST" action="{{ route('admin.loans.approve', $l) }}">@csrf @method('PATCH')<button class="tl-btn-ok" type="submit">Setujui</button></form>
                            <form method="POST" action="{{ route('admin.loans.reject', $l) }}" onsubmit="return confirm('Tolak permintaan ini?')">@csrf @method('PATCH')<button class="tl-btn-danger" type="submit">Tolak</button></form>
                        @elseif($l->status === 'Borrowed')
                            <form method="POST" action="{{ route('admin.loans.return', $l) }}" onsubmit="return confirm('Tandai buku sudah dikembalikan?')">@csrf @method('PATCH')<button class="tl-btn-ok" type="submit">Terima Kembali</button></form>
                        @elseif($l->status === 'Returned' && $l->fine_amount > 0 && !$l->fine_paid_at)
                            <form method="POST" action="{{ route('admin.loans.finepaid', $l) }}" onsubmit="return confirm('Tandai denda sudah dibayar?')">@csrf @method('PATCH')<button class="tl-btn-ok" type="submit">Tandai Lunas</button></form>
                        @else
                            <small>-</small>
                        @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="tl-empty">Belum ada data peminjaman.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $loans->links() }}</div>
</x-admin-layout>