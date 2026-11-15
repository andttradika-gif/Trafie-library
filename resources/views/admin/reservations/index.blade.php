@php
    $tabs = ['' => 'Semua', 'Waiting' => 'Menunggu', 'Ready' => 'Siap Diambil', 'Claimed' => 'Diajukan', 'Fulfilled' => 'Selesai', 'Expired' => 'Kedaluwarsa', 'Cancelled' => 'Dibatalkan'];
@endphp
<x-admin-layout title="Reservasi & Waiting List">
<x-admin-head title="Reservasi & Waiting List" sub="Antrean buku yang sedang dipinjam semua eksemplarnya." icon="clock"/>
    <div class="pg-tabs">
        @foreach($tabs as $k => $v)
            <a href="{{ route('admin.reservations.index', $k === '' ? [] : ['status' => $k]) }}" class="{{ $status === $k ? 'on' : '' }}">{{ $v }} <em>{{ $k === '' ? $counts->sum() : ($counts[$k] ?? 0) }}</em></a>
        @endforeach
    </div>
    <p class="pg-sub">Saat buku dikembalikan, eksemplar otomatis disiapkan untuk antrean pertama dan ditahan {{ (int) config('library.reserve_hold_days', 2) }} hari. Lewat batas itu, reservasi kedaluwarsa dan antrean berikutnya dipanggil.</p>
    <div class="tl-card" style="overflow-x:auto">
        <table class="tl-table">
            <thead><tr><th>Antrean</th><th>Siswa</th><th>Buku</th><th>Status</th><th>Batas Ambil</th><th style="width:170px">Aksi</th></tr></thead>
            <tbody>
            @forelse($items as $r)
                <tr>
                    <td>{{ $r->status === 'Waiting' ? '#' . ($posById[$r->id] ?? '-') : '-' }}</td>
                    <td><b>{{ $r->user->name ?? '-' }}</b><br><small>{{ $r->user->nisn ?? '' }}</small></td>
                    <td>{{ $r->book->title ?? '-' }}@if($r->copy)<br><small>{{ $r->copy->copy_code }}</small>@endif</td>
                    <td><span class="pg-b pg-b-{{ strtolower($r->status) }}">{{ $r->label }}</span></td>
                    <td>{{ $r->expires_at ? $r->expires_at->format('d M Y H:i') : '-' }}</td>
                    <td>
                        <div class="pg-actions">
                            @if($r->status === 'Waiting')
                                <form method="POST" action="{{ route('admin.reservations.ready', $r) }}">@csrf @method('PATCH')<button class="pg-btn" type="submit">Tandai siap</button></form>
                            @endif
                            @if(in_array($r->status, ['Waiting', 'Ready']))
                                <form method="POST" action="{{ route('admin.reservations.cancel', $r) }}" onsubmit="return confirm('Batalkan reservasi ini?')">@csrf @method('PATCH')<button class="tl-btn-danger" type="submit">Batalkan</button></form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="tl-empty">Belum ada reservasi. Siswa bisa memesan buku yang sedang dipinjam lewat menu Reservations.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $items->links() }}</div>
</x-admin-layout>