<x-admin-layout title="Edit Kategori">
    <div class="tl-card">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf @method('PUT')
            <div class="tl-form-grid">
                <div><label class="tl-label">Nama *</label><input class="tl-input" name="name" value="{{ old('name', $category->name) }}" required></div>
                <div><label class="tl-label">Deskripsi</label><input class="tl-input" name="description" value="{{ old('description', $category->description) }}"></div>
            </div>
            <div class="tl-row" style="margin-top:1.25rem">
                <button class="tl-btn" type="submit">Simpan Perubahan</button>
                <a href="{{ route('admin.categories.index') }}" class="tl-link-plain">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>