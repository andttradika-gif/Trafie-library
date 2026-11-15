<x-admin-layout title="Ulasan & Rating">
<x-admin-head title="Ulasan & Rating" sub="Moderasi ulasan siswa dan lihat rating buku." icon="star"/>
    <div class="pg-stats">
        <div class="pg-stat"><small>Total Ulasan</small><b>{{ $stats['total'] }}</b></div>
        <div class="pg-stat"><small>Rating Rata-rata</small><b>{{ $stats['avg'] ?: '-' }} <span class="pg-stars">&#9733;</span></b></div>
        <div class="pg-stat"><small>Tampil di Publik</small><b>{{ $stats['approved'] }}</b></div>
        <div class="pg-stat"><small>Menunggu Persetujuan</small><b>{{ $stats['pending'] }}</b></div>
    </div>

    <div class="pg-grid2" style="margin-bottom:1.1rem">
        <div class="tl-card">
            <h3 class="pg-h">Sebaran Bintang</h3>
            @foreach([5,4,3,2,1] as $s)
                <div class="pg-dist"><span>{{ $s }} <i class="pg-stars">&#9733;</i></span><div><i style="width: {{ round(($dist[$s] ?? 0) / $distMax * 100) }}%"></i></div><em>{{ $dist[$s] ?? 0 }}</em></div>
            @endforeach
            <h3 class="pg-h" style="margin-top:1.2rem">Buku Berating Tertinggi</h3>
            @forelse($top as $t)
                <div class="pg-li"><div><b>{{ $t->title }}</b><small>{{ $t->author }}</small></div><span class="pg-stars">{{ number_format($t->avg_rating, 1) }} &#9733; <em>({{ $t->total }})</em></span></div>
            @empty<div class="tl-empty">Belum ada ulasan yang tampil.</div>@endforelse
        </div>

        <div class="tl-card" style="overflow-x:auto">
            <form method="GET" action="{{ route('admin.reviewboard.index') }}" class="tl-row" style="margin-bottom:1rem">
                <input class="tl-input" style="max-width:220px" name="q" value="{{ $q }}" placeholder="Cari buku, siswa, atau isi">
                <select class="tl-input" style="max-width:150px" name="status">
                    <option value="">Semua status</option>
                    <option value="pending" @selected($status === 'pending')>Menunggu</option>
                    <option value="approved" @selected($status === 'approved')>Tampil</option>
                </select>
                <select class="tl-input" style="max-width:130px" name="rating">
                    <option value="">Semua bintang</option>
                    @foreach([5,4,3,2,1] as $s)<option value="{{ $s }}" @selected($rating === $s)>{{ $s }} bintang</option>@endforeach
                </select>
                <button class="tl-btn" type="submit">Terapkan</button>
            </form>
            <table class="tl-table">
                <thead><tr><th>Ulasan</th><th>Rating</th><th>Status</th><th style="width:210px">Aksi</th></tr></thead>
                <tbody>
                @forelse($reviews as $r)
                    <tr>
                        <td><b>{{ $r->book->title ?? '-' }}</b><br><small>oleh {{ $r->user->name ?? '-' }} &middot; {{ $r->created_at->format('d M Y') }}</small><div style="font-size:.78rem;margin-top:.3rem">{{ $r->body }}</div></td>
                        <td class="pg-stars">{!! str_repeat('&#9733;', (int) $r->rating) !!}<span style="color:#d8d2ea">{!! str_repeat('&#9733;', 5 - (int) $r->rating) !!}</span></td>
                        <td><span class="pg-b {{ $r->approved_at ? 'pg-b-verified' : 'pg-b-pending' }}">{{ $r->approved_at ? 'Tampil' : 'Menunggu' }}</span></td>
                        <td>
                            <div class="pg-actions">
                                @if(!$r->approved_at)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">@csrf @method('PATCH')<button class="pg-btn" type="submit">Setujui</button></form>
                                @else
                                    <form method="POST" action="{{ route('admin.reviewboard.hide', $r) }}">@csrf @method('PATCH')<button class="pg-btn" type="submit">Sembunyikan</button></form>
                                @endif
                                <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Hapus ulasan ini?')">@csrf @method('DELETE')<button class="tl-btn-danger" type="submit">Hapus</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="tl-empty">Tidak ada ulasan yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div style="margin-top:1rem">{{ $reviews->links() }}</div>
        </div>
    </div>
</x-admin-layout>