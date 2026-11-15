@php
    $label = ['Pending' => 'Mengajukan peminjaman', 'Borrowed' => 'Buku dipinjam', 'Returned' => 'Buku dikembalikan', 'Rejected' => 'Permintaan ditolak'];
    $cls = ['Pending' => 'wait', 'Borrowed' => 'ok', 'Returned' => 'ok', 'Rejected' => 'no'];
@endphp
<x-admin-layout title="Log Aktivitas">
<div class="xp">
    <div class="xp-hd"><div><h2>Log Aktivitas</h2><p>Jejak aktivitas peminjaman terbaru di perpustakaan.</p></div></div>
    <div class="xp-card">
        @if($rows->count())
        <div class="xp-tl">
            @foreach($rows as $l)
            <div class="it">
                <span class="dt"></span>
                <div class="tx"><b>{{ $label[$l->status] ?? $l->status }}</b><small>{{ $l->book->title ?? 'Buku' }} &middot; {{ $l->user->name ?? '-' }}</small></div>
                <span class="bd {{ $cls[$l->status] ?? 'wait' }}">{{ $l->status }}</span>
                <em>{{ $l->updated_at ? $l->updated_at->diffForHumans() : '' }}</em>
            </div>
            @endforeach
        </div>
        <div class="xp-pg">@if($rows->previousPageUrl())<a href="{{ $rows->previousPageUrl() }}">&larr; Sebelumnya</a>@endif @if($rows->nextPageUrl())<a href="{{ $rows->nextPageUrl() }}">Berikutnya &rarr;</a>@endif</div>
        @else
        <div class="xp-empty"><i></i><p>Belum ada aktivitas.</p></div>
        @endif
    </div>
</div>
</x-admin-layout>