<x-admin-layout title="Edit Buku">
@include('admin.partials.ui')
<div class="ab">
    <div class="ab-top">
        <div>
            <span class="ab-eye"><i></i>Edit</span>
            <h1 class="ab-h1">Ubah <em>buku</em></h1>
            <p class="ab-sub">{{ $book->title }}</p>
        </div>
        <a class="ab-btn ghost" href="{{ route('admin.books.index') }}">Kembali ke katalog</a>
    </div>

    <div class="ab-split">
        <aside class="ab-side">
            <div class="ab-stage">
                <div class="ab-book">
                    <img id="pvImg" alt="" @if($book->cover_url) src="{{ $book->cover_url }}" @else hidden @endif>
                    <span id="pvTxt" @if($book->cover_url) hidden @endif>{{ $book->title }}</span>
                </div>
                <div class="ab-shadow"></div>
            </div>
            <div class="ab-pv">
                <h3>{{ $book->title }}</h3>
                <p>{{ $book->author }}</p>
                <div class="ab-chips">
                    <span class="ab-chip ok">{{ $book->available_count }} dari {{ $book->copies_count }} tersedia</span>
                    @if($book->isbn)<span class="ab-chip mono">{{ $book->isbn }}</span>@endif
                </div>
            </div>
        </aside>

        <form class="ab-form" method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="ab-msg err" style="margin-bottom:20px"><b>Belum tersimpan:</b>
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="ab-sec">
                <h2><b>1</b>Identitas</h2>
                <div class="ab-grid">
                    <div class="ab-f full"><input id="f_title" name="title" placeholder=" " value="{{ old('title', $book->title) }}" required><label for="f_title">Judul buku</label></div>
                    <div class="ab-f"><input id="f_author" name="author" placeholder=" " value="{{ old('author', $book->author) }}" required><label for="f_author">Penulis</label></div>
                    <div class="ab-f"><input id="f_publisher" name="publisher" placeholder=" " value="{{ old('publisher', $book->publisher) }}" required><label for="f_publisher">Penerbit</label></div>
                    <div class="ab-f"><input id="f_year" type="number" name="publication_year" min="1900" max="2100" placeholder=" " value="{{ old('publication_year', $book->publication_year) }}" required><label for="f_year">Tahun terbit</label></div>
                    <div class="ab-f"><input id="f_pages" type="number" name="pages" min="1" placeholder=" " value="{{ old('pages', $book->pages) }}"><label for="f_pages">Jumlah halaman</label></div>
                    <div class="ab-f"><input id="f_isbn" name="isbn" maxlength="20" placeholder=" " value="{{ old('isbn', $book->isbn) }}"><label for="f_isbn">ISBN</label></div>
                    <div class="ab-f"><input id="f_lang" name="language" placeholder=" " value="{{ old('language', $book->language) }}"><label for="f_lang">Bahasa</label></div>
                </div>
            </div>

            <div class="ab-sec">
                <h2><b>2</b>Kategori dan koleksi</h2>
                <div class="ab-grid">
                    <div class="ab-f">
                        <select id="f_cat" name="category_id" required>
                            @foreach($categories as $c)<option value="{{ $c->id }}" @selected((string) old('category_id', $book->category_id) === (string) $c->id)>{{ $c->name }}</option>@endforeach
                        </select>
                        <label for="f_cat">Kategori</label>
                    </div>
                    <div class="ab-f"><input id="f_shelf" name="shelf_location" placeholder=" " value="{{ old('shelf_location', $book->shelf_location) }}"><label for="f_shelf">Lokasi rak</label></div>
                    <div>
                        <div class="ab-f"><input id="f_add" type="number" name="add_copies" min="0" max="200" placeholder=" " value="{{ old('add_copies', 0) }}"><label for="f_add">Tambah eksemplar</label></div>
                        <div class="ab-note">Sekarang {{ $book->copies_count }} eksemplar. Kode eksemplar baru dibuat otomatis.</div>
                    </div>
                </div>
            </div>

            <div class="ab-sec">
                <h2><b>3</b>Sinopsis dan cover</h2>
                <div class="ab-f"><textarea id="f_desc" name="description" rows="6" placeholder=" ">{{ old('description', $book->description) }}</textarea><label for="f_desc">Sinopsis</label></div>
                <div>
                    <input class="ab-file" id="f_cover" type="file" name="cover" accept="image/*">
                    <div class="ab-note">Jpg, png, atau webp maks 2 MB. Cover lama diganti.</div>
                </div>
                @if($book->cover_url)
                    <label class="ab-check"><input type="checkbox" name="remove_cover" value="1"> Hapus cover saat ini</label>
                @endif
            </div>

            <div class="ab-dock">
                <span></span>
                <div class="ab-acts">
                    <a class="ab-btn ghost" href="{{ route('admin.books.index') }}">Batal</a>
                    <button class="ab-btn primary" type="submit">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    (function () {
        var f = document.getElementById('f_cover');
        if (!f) { return; }
        f.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (!file) { return; }
            var img = document.getElementById('pvImg');
            img.src = URL.createObjectURL(file); img.hidden = false; document.getElementById('pvTxt').hidden = true;
        });
    })();
</script>
</x-admin-layout>