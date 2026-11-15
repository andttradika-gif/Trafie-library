<x-admin-layout title="Kategori Buku">
    <div class="tl-card" style="margin-bottom:1.25rem">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="tl-row">
            @csrf
            <input class="tl-input" style="max-width:240px" type="text" name="name" value="{{ old('name') }}" placeholder="Nama kategori" required>
            <input class="tl-input" type="text" name="description" value="{{ old('description') }}" placeholder="Deskripsi (opsional)">
            <button class="tl-btn" type="submit">Tambah</button>
        </form>
    </div>

    <div class="tl-card">
        <table class="tl-table">
            <thead><tr><th>Nama</th><th>Slug</th><th>Jumlah Buku</th><th style="width:150px">Aksi</th></tr></thead>
            <tbody>
            @forelse($categories as $c)
                <tr>
                    <td><b>{{ $c->name }}</b><br><small>{{ $c->description }}</small></td>
                    <td>{{ $c->slug }}</td>
                    <td><span class="tl-badge">{{ $c->books_count }} buku</span></td>
                    <td>
                        <div class="tl-row" style="gap:.4rem">
                            <a href="{{ route('admin.categories.edit', $c) }}" class="tl-btn-ok">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button class="tl-btn-danger" type="submit">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="tl-empty">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>