@php
    $ic = [
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
        'bookmark' => '<path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'coin' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5h4a1.5 1.5 0 0 1 0 3h-3a1.5 1.5 0 0 0 0 3h4"/>',
        'mega' => '<path d="M3 11v2a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1z"/><path d="M15 9a4 4 0 0 1 0 6"/>',
        'star' => '<path d="M12 2l3 6.5 7 .9-5.1 4.8 1.3 7L12 17.8 5.8 21.2l1.3-7L2 9.4l7-.9z"/>',
    ];
@endphp
<x-student-layout title="Notifications">
    <div class="tl-row" style="justify-content:space-between;margin-bottom:1rem">
        <p class="pg-sub" style="margin:0">{{ $unread > 0 ? $unread . ' notifikasi belum dibaca' : 'Semua notifikasi sudah dibaca' }}</p>
        @if($unread > 0)
            <form method="POST" action="{{ route('student.notifications.readall') }}">@csrf<button class="pg-btn" type="submit">Tandai semua dibaca</button></form>
        @endif
    </div>
    <div class="tl-card">
        @forelse($items as $n)
            @php $d = $n->data; @endphp
            <a href="{{ route('student.notifications.open', $n->id) }}" class="st-nt {{ $n->read_at ? '' : 'new' }}">
                <span class="i"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $ic[$d['icon'] ?? 'bell'] ?? $ic['bell'] !!}</svg></span>
                <div class="t"><b>{{ $d['title'] ?? 'Notifikasi' }}</b><small>{{ $d['body'] ?? '' }}</small></div>
                <em>{{ $n->created_at->diffForHumans() }}</em>
            </a>
        @empty
            <div class="tl-empty">Belum ada notifikasi.</div>
        @endforelse
    </div>
    <div style="margin-top:1rem">{{ $items->links() }}</div>
</x-student-layout>