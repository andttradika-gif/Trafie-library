@php use App\Models\XpTransaction; use App\Services\Reading; @endphp
<x-student-layout title="Achievements">
    <div class="tl-card rp-lv" style="margin-bottom:1.1rem">
        <div class="rp-lv-n"><small>Level</small><b>{{ $lv['level'] }}</b></div>
        <div style="flex:1;min-width:220px">
            <h3 class="pg-h" style="margin:0">{{ $lv['name'] }}</h3>
            <p class="pg-sub" style="margin:.2rem 0 .6rem">{{ $xp }} XP{{ $lv['next'] ? ' - ' . $lv['to_next'] . ' XP lagi menuju ' . $lv['next'][1] : ' - level tertinggi' }}</p>
            <div class="rp-bar"><i style="width:{{ $lv['pct'] }}%"></i></div>
        </div>
    </div>

    <div class="rp-ach">
        @foreach(Reading::ACH as $code => $a)
            @php $val = min($metrics[$a[2]] ?? 0, $a[3]); $on = $unlocked->has($code); @endphp
            <div class="rp-ac {{ $on ? 'on' : '' }}">
                <span class="i"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! Reading::icon($a[4]) !!}</svg></span>
                <b>{{ $a[0] }}</b>
                <small>{{ $a[1] }}</small>
                <div class="rp-bar"><i style="width:{{ round($val / $a[3] * 100) }}%"></i></div>
                <em>{{ $on ? 'Terbuka ' . $unlocked[$code]->unlocked_at->format('d M Y') : $val . ' / ' . $a[3] }}</em>
            </div>
        @endforeach
    </div>

    @if($badges->isNotEmpty())
    <div class="tl-card" style="margin-top:1.1rem">
        <h3 class="pg-h">Badge Tantangan</h3>
        <div class="pg-actions">@foreach($badges as $b)<span class="pg-b pg-b-claimed">{{ $b->badge_name }} - {{ $b->title }}</span>@endforeach</div>
    </div>
    @endif

    <div class="tl-card" style="margin-top:1.1rem">
        <h3 class="pg-h">Riwayat XP</h3>
        @forelse($history as $h)
            <div class="pg-li"><div><b>{{ XpTransaction::REASONS[$h->reason] ?? $h->reason }}</b><small>{{ $h->note }} &middot; {{ $h->created_at->diffForHumans() }}</small></div><span style="font-weight:700;color:{{ $h->amount >= 0 ? '#2f6b4f' : '#c2415d' }}">{{ $h->amount > 0 ? '+' : '' }}{{ $h->amount }} XP</span></div>
        @empty
            <div class="tl-empty">Belum ada XP. Selesaikan buku pertamamu untuk memulai.</div>
        @endforelse
    </div>
</x-student-layout>