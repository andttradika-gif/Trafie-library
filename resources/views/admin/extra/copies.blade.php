<x-admin-layout title="Eksemplar Buku">
<div class="xp">
    <div class="xp-hd">
        <div><h2>Eksemplar Buku</h2><p>Jumlah salinan fisik untuk setiap judul buku.</p></div>
        <form method="GET" class="xp-search"><input type="text" name="q" value="{{ $q }}" placeholder="Cari judul atau penulis..."><button type="submit">Cari</button></form>
    </div>
    <div class="xp-sum">
        <div class="xp-s a"><small>Total Judul</small><b>{{ number_format($totalJudul) }}</b></div>
        <div class="xp-s b"><small>Total Eksemplar</small><b>{{ number_format($totalCopies) }}</b></div>
        <div class="xp-s c"><small>Tersedia</small><b>{{ number_format($available) }}</b></div>
    </div>
    <div class="xp-card">
        @if($rows->count())
        <table class="xp-t">
            <thead><tr><th>Judul Buku</th><th>Penulis</th><th class="r">Eksemplar</th><th class="r">Tersedia</th><th class="r">Dipinjam</th></tr></thead>
            <tbody>
            @foreach($rows as $b)
                <tr><td><b>{{ $b->title }}</b></td><td>{{ $b->author }}</td><td class="r">{{ $b->copies_count }}</td><td class="r"><span class="bd ok">{{ $b->available_count }}</span></td><td class="r">{{ max(0, $b->copies_count - $b->available_count) }}</td></tr>
            @endforeach
            </tbody>
        </table>
        <div class="xp-pg">@if($rows->previousPageUrl())<a href="{{ $rows->previousPageUrl() }}">&larr; Sebelumnya</a>@endif @if($rows->nextPageUrl())<a href="{{ $rows->nextPageUrl() }}">Berikutnya &rarr;</a>@endif</div>
        @else
        <div class="xp-empty"><i></i><p>Belum ada buku yang cocok.</p></div>
        @endif
    </div>
</div>
</x-admin-layout>