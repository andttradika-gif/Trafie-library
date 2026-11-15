<x-admin-layout title="Pengembalian">
<div class="xp">
    <div class="xp-hd"><div><h2>Pengembalian</h2><p>Buku yang terlambat dan riwayat pengembalian.</p></div></div>
    <div class="xp-sum">
        <div class="xp-s b"><small>Sudah Dikembalikan</small><b>{{ number_format($totalReturned) }}</b></div>
        <div class="xp-s d"><small>Terlambat Dikembalikan</small><b>{{ number_format($totalLate) }}</b></div>
    </div>
    @if($late->count())
    <div class="xp-card">
        <div class="xp-ch"><h3>Perlu Ditagih</h3><span class="bd no">{{ $totalLate }} terlambat</span></div>
        <table class="xp-t">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Jatuh Tempo</th><th class="r">Terlambat</th></tr></thead>
            <tbody>
            @foreach($late as $l)
                <tr><td><b>{{ $l->user->name ?? '-' }}</b></td><td>{{ $l->book->title ?? '-' }}</td><td>{{ \Carbon\Carbon::parse($l->due_date)->format('d/m/Y') }}</td><td class="r"><span class="bd no">{{ abs((int) \Carbon\Carbon::parse($l->due_date)->diffInDays(today())) }} hari</span></td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
    <div class="xp-card">
        <div class="xp-ch"><h3>Riwayat Pengembalian</h3></div>
        @if($rows->count())
        <table class="xp-t">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Jatuh Tempo</th><th>Diperbarui</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($rows as $l)
                <tr><td><b>{{ $l->user->name ?? '-' }}</b></td><td>{{ $l->book->title ?? '-' }}</td><td>{{ $l->due_date ? \Carbon\Carbon::parse($l->due_date)->format('d/m/Y') : '-' }}</td><td>{{ $l->updated_at ? $l->updated_at->format('d/m/Y H:i') : '-' }}</td><td><span class="bd ok">Dikembalikan</span></td></tr>
            @endforeach
            </tbody>
        </table>
        <div class="xp-pg">@if($rows->previousPageUrl())<a href="{{ $rows->previousPageUrl() }}">&larr; Sebelumnya</a>@endif @if($rows->nextPageUrl())<a href="{{ $rows->nextPageUrl() }}">Berikutnya &rarr;</a>@endif</div>
        @else
        <div class="xp-empty"><i></i><p>Belum ada buku yang dikembalikan.</p></div>
        @endif
    </div>
</div>
</x-admin-layout>