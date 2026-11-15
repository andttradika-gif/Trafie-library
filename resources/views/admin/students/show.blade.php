<x-admin-layout title="Riwayat Siswa">
    <a href="{{ route('admin.students.index') }}" class="tl-link-plain">&larr; Kembali ke daftar siswa</a>

    <div class="tl-card" style="margin:1rem 0 1.25rem">
        <h3 class="tl-serif" style="margin:0 0 .8rem;color:#2d2a4a">{{ $student->name }}</h3>
        <dl class="tl-meta">
            <div><dt>Email</dt><dd>{{ $student->email }}</dd></div>
            <div><dt>NISN</dt><dd>{{ $student->nisn ?: '-' }}</dd></div>
            <div><dt>Bergabung</dt><dd>{{ $student->created_at->format('d M Y') }}</dd></div>
        </dl>
    </div>

    <div class="tl-card" style="overflow-x:auto">
        <table class="tl-table">
            <thead><tr><th>Buku</th><th>Durasi</th><th>Diajukan</th><th>Jatuh Tempo</th><th>Dikembalikan</th><th>Status</th><th>Denda</th></tr></thead>
            <tbody>
            @forelse($loans as $l)
                @php $fine = $l->current_fine; @endphp
                <tr>
                    <td><b>{{ $l->book->title }}</b><br><small>{{ $l->book->author }}</small></td>
                    <td>{{ $l->duration_days }} hari</td>
                    <td>{{ $l->requested_at ? $l->requested_at->format('d M Y') : '-' }}</td>
                    <td>{{ $l->due_date ? $l->due_date->format('d M Y') : '-' }}</td>
                    <td>{{ $l->returned_at ? $l->returned_at->format('d M Y') : '-' }}</td>
                    <td><span class="tl-st st-{{ strtolower($l->status) }}">{{ $l->label }}</span></td>
                    <td>
                        @if($fine > 0)
                            <span class="tl-fine">{{ \App\Models\Loan::rupiah($fine) }}</span><br>
                            <small>{{ $l->status === 'Borrowed' ? 'berjalan' : ($l->fine_paid_at ? 'lunas' : 'belum dibayar') }}</small>
                        @else
                            <small>-</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="tl-empty">Siswa ini belum pernah meminjam.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $loans->links() }}</div>
</x-admin-layout>