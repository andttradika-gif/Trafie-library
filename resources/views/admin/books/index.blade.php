@php
    $g = ['linear-gradient(150deg,#2b4a7a,#4f86c0)', 'linear-gradient(150deg,#d9a86a,#a8693a)', 'linear-gradient(150deg,#8f78d8,#4f3fa0)', 'linear-gradient(150deg,#f08fb8,#9a6fe0)', 'linear-gradient(150deg,#4fbfa0,#3d7fd0)'];
@endphp
<x-admin-layout title="Manajemen Buku">
@include('admin.partials.ui')
<div class="ab">
    <div class="ab-top">
        <div>
            <span class="ab-eye"><i></i>Katalog</span>
            <h1 class="ab-h1">Koleksi <em>buku</em></h1>
            <p class="ab-sub">Semua judul di perpustakaan dalam satu rak. Arahkan kursor ke sampul untuk mengubah atau menghapus.</p>
        </div>
        <div class="ab-acts">
            <a class="ab-btn primary" href="{{ route('admin.books.create') }}"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Tambah via ISBN</a>
        </div>
    </div>

    <div class="ab-bento">
        <div class="ab-tile hero"><span>Judul buku</span><b>{{ number_format($stats['titles']) }}</b></div>
        <div class="ab-tile"><span>Eksemplar</span><b>{{ number_format($stats['copies']) }}</b></div>
        <div class="ab-tile c"><span>Tersedia</span><b>{{ number_format($stats['available']) }}</b></div>
        <div class="ab-tile d"><span>Dipinjam</span><b>{{ number_format($stats['borrowed']) }}</b></div>
    </div>

    <form class="ab-bar" method="GET" action="{{ route('admin.books.index') }}">
        <div class="ab-search">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l5 5"/></svg>
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul, penulis, penerbit, ISBN" aria-label="Cari buku">
        </div>
        <div class="ab-seg" role="radiogroup" aria-label="Stok">
            <label><input type="radio" name="stok" value="" @checked($stok === '') onchange="this.form.submit()"><span>Semua</span></label>
            <label><input type="radio" name="stok" value="tersedia" @checked($stok === 'tersedia') onchange="this.form.submit()"><span>Tersedia</span></label>
            <label><input type="radio" name="stok" value="habis" @checked($stok === 'habis') onchange="this.form.submit()"><span>Habis</span></label>
        </div>
        <select class="ab-sel" name="kategori" aria-label="Kategori" onchange="this.form.submit()">
            <option value="">Semua kategori</option>
            @foreach($categories as $c)<option value="{{ $c->id }}" @selected($cat === $c->id)>{{ $c->name }}</option>@endforeach
        </select>
        <select class="ab-sel" name="urut" aria-label="Urutkan" onchange="this.form.submit()">
            <option value="terbaru" @selected($urut === 'terbaru')>Terbaru</option>
            <option value="judul" @selected($urut === 'judul')>Judul A-Z</option>
            <option value="stok" @selected($urut === 'stok')>Stok tersedikit</option>
        </select>
        <button class="ab-btn primary" type="submit" style="height:46px">Cari</button>
    </form>

    @if($books->isEmpty())
        <div class="ab-empty">
            <svg viewBox="0 0 96 96" fill="none" aria-hidden="true">
                <defs><linearGradient id="abE" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#d9ccf5"/><stop offset="1" stop-color="#f7c8dc"/></linearGradient></defs>
                <rect x="14" y="18" width="68" height="62" rx="16" fill="url(#abE)" opacity=".6"/>
                <path d="M48 28c-6-4-15-5-24-3v40c9-2 18-1 24 3 6-4 15-5 24-3V25c-9-2-18-1-24 3z" fill="#fff" stroke="#8d69d6" stroke-width="2.4" stroke-linejoin="round"/>
                <path d="M48 28v40" stroke="#8d69d6" stroke-width="2.4"/>
            </svg>
            @if($filtered)
                <h3>Tidak ada buku yang cocok</h3>
                <p>Coba ubah kata kunci atau filter yang kamu pakai.</p>
                <a class="ab-btn ghost" href="{{ route('admin.books.index') }}">Reset filter</a>
            @else
                <h3>Rak masih kosong</h3>
                <p>Mulai dengan satu ISBN. Judul, penulis, sinopsis, dan cover terisi otomatis.</p>
                <a class="ab-btn primary" href="{{ route('admin.books.create') }}">Tambah buku pertama</a>
            @endif
        </div>
    @else
        <div class="ab-shelf">
            @foreach($books as $b)
                @php
                    $ratio = $b->copies_count > 0 ? round($b->available_count / $b->copies_count * 100) : 0;
                    $state = $b->available_count === 0 ? 'out' : ($ratio <= 34 ? 'low' : '');
                @endphp
                <article class="ab-bk" style="animation-delay: {{ min($loop->index, 12) * 40 }}ms">
                    <div class="ab-poster" style="background: {{ $g[$b->id % 5] }}">
                        <a href="{{ route('admin.books.edit', $b) }}" aria-label="Edit {{ $b->title }}">
                            @if($b->cover_url)<img src="{{ $b->cover_url }}" alt="" loading="lazy">@else<span>{{ \Illuminate\Support\Str::limit($b->title, 40) }}</span>@endif
                        </a>
                        <span class="ab-pill {{ $state }}"><i></i>{{ $b->available_count === 0 ? 'Habis' : $b->available_count . ' dari ' . $b->copies_count }}</span>
                        <div class="ab-over">
                            <a class="ab-btn ghost sm" href="{{ route('admin.books.edit', $b) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.books.destroy', $b) }}" onsubmit="return confirm('Hapus buku &quot;{{ addslashes($b->title) }}&quot; beserta semua eksemplarnya?')">
                                @csrf @method('DELETE')
                                <button class="ab-btn danger sm" type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>
                    <div class="ab-info">
                        <h3>{{ $b->title }}</h3>
                        <p>{{ $b->author }}</p>
                        <div class="ab-chips">
                            <span class="ab-chip">{{ $b->category->name }}</span>
                            @if($b->pages)<span class="ab-chip mono">{{ $b->pages }} hlm</span>@endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div>{{ $books->links() }}</div>
    @endif
</div>
</x-admin-layout>