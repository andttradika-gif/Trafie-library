<x-student-layout title="Reading Wrapped">
    <form method="GET" action="{{ route('student.wrapped.index') }}" class="tl-row no-print" style="margin-bottom:1rem">
        <select class="tl-input" name="type" style="max-width:130px"><option value="month" @selected($type === 'month')>Bulanan</option><option value="year" @selected($type === 'year')>Tahunan</option></select>
        <select class="tl-input" name="month" style="max-width:150px">@foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $m)<option value="{{ $i + 1 }}" @selected($month === $i + 1)>{{ $m }}</option>@endforeach</select>
        <input class="tl-input" type="number" name="year" value="{{ $year }}" min="2020" max="2100" style="max-width:100px">
        <button class="tl-btn" type="submit">Tampilkan</button>
        <button class="pg-btn" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </form>

    <div class="wr-card">
        <div class="wr-head">
            <small>Reading Wrapped</small>
            <h2>{{ $label }}</h2>
            <p>{{ Auth::user()->name }} &middot; Level {{ $lv['level'] }} {{ $lv['name'] }}</p>
        </div>
        <div class="wr-grid">
            <div class="wr-s"><small>Buku selesai</small><b>{{ $comp->count() }}</b></div>
            <div class="wr-s"><small>XP didapat</small><b>{{ $xpPeriod }}</b></div>
            <div class="wr-s"><small>Halaman dicatat</small><b>{{ $pages }}</b></div>
            <div class="wr-s"><small>Streak saat ini</small><b>{{ $cur }} hari</b><em>terbaik {{ $bestStreak }} hari</em></div>
            <div class="wr-s"><small>Genre favorit</small><b style="font-size:1.1rem">{{ $genres->keys()->first() ?? '-' }}</b></div>
            <div class="wr-s"><small>Hari paling aktif</small><b style="font-size:1.1rem">{{ $activeDay ?? '-' }}</b></div>
            <div class="wr-s"><small>Buku rating tertinggi</small><b style="font-size:1rem">{{ $best->book->title ?? '-' }}</b>@if($best)<em class="pg-stars">{!! str_repeat('&#9733;', (int) $best->rating) !!}</em>@endif</div>
            <div class="wr-s"><small>Buku favorit</small><b style="font-size:1rem">{{ $favBook->title ?? '-' }}</b></div>
        </div>
        <div class="wr-row">
            <div><h4>Achievement terbuka</h4>@forelse($ach as $a)<span class="pg-b pg-b-claimed">{{ \App\Services\Reading::ACH[$a->code][0] ?? $a->code }}</span> @empty<small>Belum ada di periode ini.</small>@endforelse</div>
            <div><h4>Perkembangan galaxy</h4><small>+{{ $comp->count() }} bintang baru di periode ini.</small></div>
        </div>
        @if($comp->isNotEmpty())
        <div class="wr-list"><h4>Buku yang selesai</h4>@foreach($comp as $c)<div class="pg-li"><div><b>{{ $c->book->title ?? '-' }}</b><small>{{ $c->completed_at->format('d M Y') }}</small></div><span class="pg-stars">{!! str_repeat('&#9733;', (int) $c->rating) !!}</span></div>@endforeach</div>
        @endif
        <p class="wr-note">Jam membaca tidak ditampilkan karena belum ada data sesi membaca yang valid.</p>
    </div>
</x-student-layout>