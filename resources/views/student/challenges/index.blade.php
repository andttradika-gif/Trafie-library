<x-student-layout title="Reading Challenges">
    <p class="pg-sub">Bergabunglah di tantangan dari admin. Progres dihitung dari buku yang kamu selesaikan dan terverifikasi sejak bergabung.</p>
    <div class="rp-chs">
        @forelse($rows as $c)
            @php
                $me = $mine[$c->id] ?? null;
                $done = $me && $me->completed_at;
                $cnt = $progress[$c->id] ?? 0;
                $open = $c->status === 'Active' && !$c->ends_on->copy()->endOfDay()->isPast();
            @endphp
            <div class="tl-card rp-ch">
                <div class="tl-row" style="justify-content:space-between">
                    <span class="pg-b {{ $done ? 'pg-b-verified' : ($open ? 'pg-b-active' : 'pg-b-closed') }}">{{ $done ? 'Selesai' : ($open ? 'Aktif' : 'Ditutup') }}</span>
                    <small style="color:#8b87a8">{{ $c->starts_on->format('d M') }} - {{ $c->ends_on->format('d M Y') }}</small>
                </div>
                <h3 class="pg-h" style="margin:.6rem 0 .2rem">{{ $c->title }}</h3>
                <p class="pg-sub">{{ $c->description }}</p>
                <p class="pg-sub" style="margin:0 0 .6rem">Target <b>{{ $c->target_books }} buku</b> &middot; {{ $c->category->name ?? 'semua kategori' }} &middot; Reward <b>{{ $c->reward_xp }} XP</b>@if($c->badge_name) + badge {{ $c->badge_name }}@endif</p>
                @if($me)
                    <div class="rp-bar"><i style="width:{{ min(100, round($cnt / max(1, $c->target_books) * 100)) }}%"></i></div>
                    <small>{{ min($cnt, $c->target_books) }} / {{ $c->target_books }} buku</small>
                @elseif($open)
                    <form method="POST" action="{{ route('student.challenges.join', $c) }}">@csrf<button class="tl-btn" type="submit">Ikut Tantangan</button></form>
                @endif
            </div>
        @empty
            <div class="tl-card tl-empty">Belum ada tantangan dari admin.</div>
        @endforelse
    </div>
</x-student-layout>