<x-admin-layout title="Pengumuman">
<x-admin-head title="Pengumuman" sub="Informasi dan acara untuk seluruh siswa." icon="mega"/>
    <div class="tl-card" style="margin-bottom:1.1rem">
        <h3 class="pg-h">Buat Pengumuman</h3>
        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @csrf
            <div class="tl-form-grid">
                <div class="full"><label class="tl-label">Judul *</label><input class="tl-input" name="title" value="{{ old('title') }}" required maxlength="150"></div>
                <div class="full"><label class="tl-label">Isi *</label><textarea class="tl-input" name="body" rows="3" required maxlength="2000">{{ old('body') }}</textarea></div>
                <div>
                    <label class="tl-label">Jenis *</label>
                    <select class="tl-input" name="type">@foreach(\App\Models\Announcement::TYPES as $k => $v)<option value="{{ $k }}" @selected(old('type') === $k)>{{ $v }}</option>@endforeach</select>
                </div>
                <div><label class="tl-label">Tayang sampai (opsional)</label><input class="tl-input" type="date" name="ends_on" value="{{ old('ends_on') }}"></div>
                <div class="full"><label style="font-size:.78rem"><input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned'))> Sematkan di bagian atas</label></div>
            </div>
            <button class="tl-btn" type="submit" style="margin-top:1rem">Terbitkan</button>
        </form>
    </div>

    <div class="tl-card" style="overflow-x:auto">
        <table class="tl-table">
            <thead><tr><th>Pengumuman</th><th>Jenis</th><th>Tayang</th><th>Status</th><th style="width:190px">Aksi</th></tr></thead>
            <tbody>
            @forelse($items as $a)
                <tr>
                    <td>@if($a->is_pinned)<span class="pg-b pg-b-claimed">Disematkan</span> @endif<b>{{ $a->title }}</b><br><small>{{ \Illuminate\Support\Str::limit($a->body, 90) }}</small></td>
                    <td><span class="tl-badge">{{ \App\Models\Announcement::TYPES[$a->type] ?? $a->type }}</span></td>
                    <td>{{ $a->published_at ? $a->published_at->format('d M Y') : '-' }}<br><small>{{ $a->ends_on ? 'sampai ' . $a->ends_on->format('d M Y') : 'tanpa batas' }}</small></td>
                    <td><span class="pg-b {{ $a->is_active ? 'pg-b-verified' : 'pg-b-closed' }}">{{ $a->is_active ? 'Tampil' : 'Disembunyikan' }}</span></td>
                    <td>
                        <div class="pg-actions">
                            <form method="POST" action="{{ route('admin.announcements.toggle', $a) }}">@csrf @method('PATCH')<button class="pg-btn" type="submit">{{ $a->is_active ? 'Sembunyikan' : 'Tampilkan' }}</button></form>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}" onsubmit="return confirm('Hapus pengumuman ini?')">@csrf @method('DELETE')<button class="tl-btn-danger" type="submit">Hapus</button></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="tl-empty">Belum ada pengumuman.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>