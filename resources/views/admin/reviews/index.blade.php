<x-admin-layout title="Ulasan Buku">
    <p style="margin:0 0 1rem;font-size:.78rem;color:#7b7796">Ulasan baru tidak tampil di landing page sebelum kamu setujui. Menunggu persetujuan: <b>{{ $pending }}</b>.</p>

    <div class="tl-card" style="overflow-x:auto">
        <table class="tl-table">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Rating</th><th>Ulasan</th><th>Status</th><th style="width:150px">Aksi</th></tr></thead>
            <tbody>
            @forelse($reviews as $r)
                <tr>
                    <td><b>{{ $r->user->name }}</b><br><small>NISN {{ $r->user->nisn ?: '-' }}</small></td>
                    <td>{{ $r->book->title }}</td>
                    <td><span style="color:#f5b83d;letter-spacing:1px">{!! str_repeat('&#9733;', $r->rating) !!}</span><small style="color:#c9c5dd">{!! str_repeat('&#9733;', 5 - $r->rating) !!}</small></td>
                    <td style="max-width:320px">{{ $r->body }}</td>
                    <td>
                        @if($r->approved_at)<span class="tl-st st-returned">Tampil</span>@else<span class="tl-st st-pending">Menunggu</span>@endif
                    </td>
                    <td>
                        <div class="tl-row" style="gap:.4rem">
                            @unless($r->approved_at)
                                <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">@csrf @method('PATCH')<button class="tl-btn-ok" type="submit">Setujui</button></form>
                            @endunless
                            <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Hapus ulasan ini?')">@csrf @method('DELETE')<button class="tl-btn-danger" type="submit">Hapus</button></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="tl-empty">Belum ada ulasan dari siswa.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $reviews->links() }}</div>
</x-admin-layout>