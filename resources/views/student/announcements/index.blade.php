<x-student-layout title="Pengumuman">
    @forelse($items as $a)
        <div class="tl-card" style="margin-bottom:1rem">
            <div class="tl-row" style="margin-bottom:.4rem">
                @if($a->is_pinned)<span class="pg-b pg-b-claimed">Disematkan</span>@endif
                <span class="tl-badge">{{ \App\Models\Announcement::TYPES[$a->type] ?? $a->type }}</span>
                <small style="color:#8b87a8">{{ $a->published_at ? $a->published_at->format('d M Y') : '' }}</small>
            </div>
            <h3 class="pg-h">{{ $a->title }}</h3>
            <p style="margin:0;font-size:.82rem;line-height:1.7;color:#4b4868;white-space:pre-line">{{ $a->body }}</p>
        </div>
    @empty
        <div class="tl-card tl-empty">Belum ada pengumuman.</div>
    @endforelse
</x-student-layout>