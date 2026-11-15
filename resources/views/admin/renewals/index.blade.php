<x-admin-layout title="Perpanjangan Pinjaman">
    <div class="tl-card" style="margin-bottom:1.25rem;overflow-x:auto">
        <h3 class="tl-serif" style="margin:0 0 .6rem;color:#2d2a4a">Menunggu Keputusan</h3>
        <table class="tl-table">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Jatuh Tempo Sekarang</th><th>Tambahan</th><th>Jatuh Tempo Baru</th><th>Diajukan</th><th style="width:150px">Aksi</th></tr></thead>
            <tbody>
            @forelse($pending as $r)
                @php $loan = $r->loan; @endphp
                <tr>
                    <td><b>{{ $loan->user->name }}</b><br><small>NISN {{ $loan->user->nisn ?: '-' }}</small></td>
                    <td>{{ $loan->book->title }}</td>
                    <td>
                        {{ $loan->due_date->format('d M Y') }}
                        @if($loan->isOverdue())<br><small class="tl-fine">sudah terlambat {{ $loan->late_days }} hari</small>@endif
                    </td>
                    <td>+{{ $r->extra_days }} hari</td>
                    <td>{{ $loan->due_date->copy()->addDays($r->extra_days)->format('d M Y') }}</td>
                    <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <div class="tl-row" style="gap:.4rem">
                            <form method="POST" action="{{ route('admin.renewals.approve', $r) }}">@csrf @method('PATCH')<button class="tl-btn-ok" type="submit">Setujui</button></form>
                            <form method="POST" action="{{ route('admin.renewals.reject', $r) }}" onsubmit="return confirm('Tolak perpanjangan ini?')">@csrf @method('PATCH')<button class="tl-btn-danger" type="submit">Tolak</button></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="tl-empty">Tidak ada permintaan perpanjangan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="tl-card" style="overflow-x:auto">
        <h3 class="tl-serif" style="margin:0 0 .6rem;color:#2d2a4a">Riwayat Keputusan</h3>
        <table class="tl-table">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Tambahan</th><th>Keputusan</th><th>Waktu</th></tr></thead>
            <tbody>
            @forelse($history as $r)
                <tr>
                    <td><b>{{ $r->loan->user->name }}</b></td>
                    <td>{{ $r->loan->book->title }}</td>
                    <td>+{{ $r->extra_days }} hari</td>
                    <td><span class="tl-st {{ $r->status === 'Approved' ? 'st-returned' : 'st-rejected' }}">{{ $r->label }}</span></td>
                    <td>{{ $r->decided_at ? $r->decided_at->format('d M Y H:i') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="tl-empty">Belum ada riwayat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $history->links() }}</div>
</x-admin-layout>