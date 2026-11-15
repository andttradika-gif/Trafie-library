<x-admin-layout title="Verifikasi Penyelesaian">
<x-admin-head title="Verifikasi Penyelesaian" sub="Periksa penyelesaian buku dan batalkan yang mencurigakan." icon="check"/>
    <div class="pg-stats">
        <div class="pg-stat"><small>Terverifikasi</small><b>{{ $stats['verified'] }}</b></div>
        <div class="pg-stat"><small>Dibatalkan</small><b>{{ $stats['revoked'] }}</b></div>
    </div>
    <p class="pg-sub">Sistem memverifikasi otomatis (buku pernah dipinjam, sudah dikembalikan, progres diajukan). Periksa yang bertanda mencurigakan dan batalkan bila perlu. XP-nya ikut ditarik.</p>
    <div class="tl-card" style="overflow-x:auto">
        <table class="tl-table">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Pinjam sampai kembali</th><th>Rating dan refleksi</th><th>Status</th><th style="width:120px">Aksi</th></tr></thead>
            <tbody>
            @forelse($items as $c)
                @php $l = $c->loan; $cepat = $l && $l->approved_at && $l->returned_at && $l->approved_at->diffInHours($l->returned_at) < 6; @endphp
                <tr>
                    <td><b>{{ $c->user->name ?? '-' }}</b><br><small>{{ $c->completed_at->format('d M Y H:i') }}</small></td>
                    <td>{{ $c->book->title ?? '-' }}</td>
                    <td>@if($l && $l->approved_at && $l->returned_at){{ $l->approved_at->format('d M') }} - {{ $l->returned_at->format('d M') }}@else-@endif @if($cepat)<br><span class="pg-b pg-b-pending">Mencurigakan: &lt; 6 jam</span>@endif</td>
                    <td><span class="pg-stars">{!! str_repeat('&#9733;', (int) $c->rating) !!}</span><div style="font-size:.74rem;max-width:300px">{{ \Illuminate\Support\Str::limit($c->reflection, 140) }}</div></td>
                    <td><span class="pg-b {{ $c->status === 'Verified' ? 'pg-b-verified' : 'pg-b-closed' }}">{{ $c->status === 'Verified' ? 'Terverifikasi' : 'Dibatalkan' }}</span></td>
                    <td>@if($c->status === 'Verified')<form method="POST" action="{{ route('admin.completions.revoke', $c) }}" onsubmit="return confirm('Batalkan penyelesaian ini dan tarik XP-nya?')">@csrf @method('PATCH')<button class="tl-btn-danger" type="submit">Batalkan</button></form>@endif</td>
                </tr>
            @empty
                <tr><td colspan="6" class="tl-empty">Belum ada penyelesaian buku.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $items->links() }}</div>
</x-admin-layout>