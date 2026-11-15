@php $grad = ['linear-gradient(135deg,#cfe8f7,#b9a8ee)','linear-gradient(135deg,#f7c8dc,#d9ccf5)','linear-gradient(135deg,#cfe5d6,#cfe8f7)','linear-gradient(135deg,#d9ccf5,#f7c8dc)']; @endphp
<x-student-layout title="Katalog Buku">
    <div class="tl-card" style="margin-bottom:1.25rem">
        <form method="GET" action="{{ route('student.books.index') }}" class="tl-row">
            <input class="tl-input" style="max-width:320px" type="text" name="q" value="{{ $q }}" placeholder="Cari judul, penulis, penerbit">
            <select class="tl-input" style="max-width:200px" name="kategori">
                <option value="">Semua kategori</option>
                @foreach($categories as $c)<option value="{{ $c->slug }}" @selected($cat === $c->slug)>{{ $c->name }} ({{ $c->books_count }})</option>@endforeach
            </select>
            <select class="tl-input" style="max-width:180px" name="urut">
                <option value="terbaru" @selected($sort === 'terbaru')>Terbaru</option>
                <option value="judul" @selected($sort === 'judul')>Judul A-Z</option>
                <option value="tersedia" @selected($sort === 'tersedia')>Paling tersedia</option>
            </select>
            <button class="tl-btn" type="submit">Terapkan</button>
        </form>
    </div>

    @if($books->isEmpty())
        <div class="tl-card tl-empty">Tidak ada buku yang cocok.</div>
    @else
        <div class="tl-books">
            @foreach($books as $b)
            <a href="{{ route('student.books.show', $b) }}" class="tl-book">
                <div class="tl-cover" style="background: {{ $grad[$b->id % 4] }}">
                    @if($b->cover_url)<img src="{{ $b->cover_url }}" alt="{{ $b->title }}">@else<span>{{ $b->title }}</span>@endif
                    <em class="tl-tag">{{ $b->category->name }}</em>
                </div>
                <h3>{{ $b->title }}</h3>
                <p>{{ $b->author }}</p>
                <span class="tl-avail {{ $b->available_count > 0 ? 'yes' : 'no' }}">
                    {{ $b->available_count > 0 ? 'Tersedia ' . $b->available_count . '/' . $b->copies_count : 'Sedang dipinjam' }}
                </span>
            </a>
            @endforeach
        </div>
        <div style="margin-top:1.25rem">{{ $books->links() }}</div>
    @endif
</x-student-layout>