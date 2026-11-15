@php $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.'); @endphp
<x-admin-layout title="Pembayaran">
<div class="xp">
    <div class="xp-hd"><div><h2>Pembayaran</h2><p>Riwayat pembayaran denda yang sudah diterima.</p></div></div>
    <div class="xp-sum">
        <div class="xp-s b"><small>Total Diterima</small><b>{{ $rp($total) }}</b></div>
        <div class="xp-s a"><small>Jumlah Transaksi</small><b>{{ number_format($count) }}</b></div>
    </div>
    <div class="xp-card">
        @if($rows->count())
        <table class="xp-t">
            <thead><tr><th>Tanggal Bayar</th><th>Siswa</th><th>Buku</th><th class="r">Jumlah</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($rows as $l)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($l->fine_paid_at)->format('d/m/Y H:i') }}</td>
                    <td><b>{{ $l->user->name ?? '-' }}</b></td>
                    <td>{{ $l->book->title ?? '-' }}</td>
                    <td class="r"><b>{{ $rp($l->fine_amount) }}</b></td>
                    <td><span class="bd ok">Lunas</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="xp-pg">@if($rows->previousPageUrl())<a href="{{ $rows->previousPageUrl() }}">&larr; Sebelumnya</a>@endif @if($rows->nextPageUrl())<a href="{{ $rows->nextPageUrl() }}">Berikutnya &rarr;</a>@endif</div>
        @else
        <div class="xp-empty"><i></i><p>Belum ada pembayaran denda.</p></div>
        @endif
    </div>
</div>
</x-admin-layout>