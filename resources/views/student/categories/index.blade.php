@php $grad = ['linear-gradient(135deg,#cfe8f7,#b9a8ee)','linear-gradient(135deg,#f7c8dc,#d9ccf5)','linear-gradient(135deg,#cfe5d6,#cfe8f7)','linear-gradient(135deg,#d9ccf5,#f7c8dc)']; @endphp
<x-student-layout title="Categories">
    <p class="pg-sub">Pilih kategori untuk menjelajahi buku di dalamnya.</p>
    <div class="st-cats">
        @forelse($categories as $c)
        <a class="st-cat" href="{{ route('student.books.index', ['kategori' => $c->slug]) }}">
            <span class="ic" style="background: {{ $grad[$loop->index % 4] }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
            <h3>{{ $c->name }}</h3>
            <p>{{ $c->description ?: 'Jelajahi buku di kategori ini.' }}</p>
            <em>{{ $c->books_count }} buku</em>
        </a>
        @empty
        <div class="tl-card tl-empty">Belum ada kategori.</div>
        @endforelse
    </div>
</x-student-layout>