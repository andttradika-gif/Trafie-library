@php $grad = ['linear-gradient(135deg,#cfe8f7,#b9a8ee)','linear-gradient(135deg,#f7c8dc,#d9ccf5)','linear-gradient(135deg,#cfe5d6,#cfe8f7)','linear-gradient(135deg,#d9ccf5,#f7c8dc)']; @endphp
<x-student-layout title="My Favorites">
    <div class="tl-card" style="margin-bottom:1.1rem">
        <h3 class="pg-h">Buku Favoritku</h3>
        @if($favorites->isEmpty())
            <div class="tl-empty">Belum ada favorit. Pilih buku dari daftar di bawah dengan menekan tombol hati.</div>
        @else
        <div class="st-grid">
            @foreach($favorites as $b)
            <div class="st-bk">
                <form method="POST" action="{{ route('student.favorites.toggle', $b) }}" class="heart">@csrf<button class="st-heart on" type="submit" title="Hapus dari favorit">&#9829;</button></form>
                <a href="{{ route('student.books.show', $b) }}" class="cv" style="background: {{ $grad[$b->id % 4] }}">@if($b->cover_url)<img src="{{ $b->cover_url }}" alt="">@else<span>{{ $b->title }}</span>@endif</a>
                <h4>{{ $b->title }}</h4><p>{{ $b->author }}</p>
                <small>{{ $b->category->name ?? '' }}</small>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="tl-card">
        <h3 class="pg-h">Temukan Buku</h3>
        <form method="GET" action="{{ route('student.favorites.index') }}" class="tl-row" style="margin:.4rem 0 1rem">
            <input class="tl-input" style="max-width:300px" name="q" value="{{ $q }}" placeholder="Cari judul atau penulis">
            <button class="tl-btn" type="submit">Cari</button>
        </form>
        @if($discover->isEmpty())
            <div class="tl-empty">Tidak ada buku lain untuk ditampilkan.</div>
        @else
        <div class="st-grid">
            @foreach($discover as $b)
            <div class="st-bk">
                <form method="POST" action="{{ route('student.favorites.toggle', $b) }}" class="heart">@csrf<button class="st-heart" type="submit" title="Tambah ke favorit">&#9829;</button></form>
                <a href="{{ route('student.books.show', $b) }}" class="cv" style="background: {{ $grad[$b->id % 4] }}">@if($b->cover_url)<img src="{{ $b->cover_url }}" alt="">@else<span>{{ $b->title }}</span>@endif</a>
                <h4>{{ $b->title }}</h4><p>{{ $b->author }}</p>
                <small>{{ $b->category->name ?? '' }}</small>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</x-student-layout>