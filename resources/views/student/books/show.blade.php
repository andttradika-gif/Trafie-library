@php
    $grad = ['linear-gradient(135deg,#cfe8f7,#b9a8ee)','linear-gradient(135deg,#f7c8dc,#d9ccf5)','linear-gradient(135deg,#cfe5d6,#cfe8f7)','linear-gradient(135deg,#d9ccf5,#f7c8dc)'];
    $myLoan = Auth::user()->loans()->where('book_id', $book->id)->whereIn('status', ['Pending', 'Borrowed'])->first();

    $reviews   = $book->reviews()->approved()->with('user')->latest()->take(10)->get();
    $avgRating = $book->reviews()->approved()->avg('rating');
    $canReview = Auth::user()->loans()->where('book_id', $book->id)->where('status', 'Returned')->exists();
    $mine      = $book->reviews()->where('user_id', Auth::id())->first();
@endphp
<x-student-layout title="Detail Buku">
    <a href="{{ route('student.books.index') }}" class="tl-link-plain">&larr; Kembali ke katalog</a>

    <div class="tl-card tl-detail" style="margin-top:1rem">
        <div class="tl-cover big" style="background: {{ $grad[$book->id % 4] }}">
            @if($book->cover_url)<img src="{{ $book->cover_url }}" alt="{{ $book->title }}">@else<span>{{ $book->title }}</span>@endif
        </div>
        <div>
            <span class="tl-badge">{{ $book->category->name }}</span>
            <h1 class="tl-serif" style="font-size:1.9rem;margin:.5rem 0 .2rem;color:#2d2a4a">{{ $book->title }}</h1>
            <p style="margin:0 0 .4rem;color:#7b7796">oleh {{ $book->author }}</p>
            @if($avgRating)
                <p style="margin:0 0 .8rem;font-size:.8rem;color:#5f5b7a"><span style="color:#f5b83d">&#9733;</span> <b>{{ number_format($avgRating, 1) }}</b> dari {{ $reviews->count() }} ulasan</p>
            @endif

            <span class="tl-avail {{ $book->available_count > 0 ? 'yes' : 'no' }}">
                {{ $book->available_count > 0 ? 'Tersedia ' . $book->available_count . ' dari ' . $book->copies_count . ' eksemplar' : 'Semua eksemplar sedang dipinjam' }}
            </span>

            <dl class="tl-meta">
                <div><dt>Penerbit</dt><dd>{{ $book->publisher }}</dd></div>
                <div><dt>Tahun</dt><dd>{{ $book->publication_year }}</dd></div>
                <div><dt>ISBN</dt><dd>{{ $book->isbn ?: '-' }}</dd></div>
                <div><dt>Halaman</dt><dd>{{ $book->pages ?: '-' }}</dd></div>
                <div><dt>Bahasa</dt><dd>{{ $book->language }}</dd></div>
                <div><dt>Lokasi Rak</dt><dd>{{ $book->shelf_location ?: '-' }}</dd></div>
            </dl>

            <p style="font-size:.85rem;line-height:1.7;color:#4b4868">{{ $book->description ?: 'Belum ada deskripsi.' }}</p>

            <div class="tl-borrow">
                @if($myLoan)
                    <b>Status pinjaman kamu: {{ $myLoan->label }}</b>
                    @if($myLoan->due_date)<br><small>Jatuh tempo {{ $myLoan->due_date->format('d M Y') }}</small>@endif
                    <br><a href="{{ route('student.loans.index') }}" class="tl-link-plain">Lihat riwayat pinjam</a>
                @elseif($book->available_count > 0)
                    <form method="POST" action="{{ route('student.loans.store', $book) }}" class="tl-row">
                        @csrf
                        <select class="tl-input" style="max-width:180px" name="duration_days">
                            <option value="3">Pinjam 3 hari</option>
                            <option value="7" selected>Pinjam 7 hari</option>
                            <option value="10">Pinjam 10 hari</option>
                        </select>
                        <button class="tl-btn" type="submit">Ajukan Pinjam</button>
                    </form>
                    <small style="color:#8b87a8">Permintaan akan diperiksa admin. Jatuh tempo dihitung sejak disetujui.</small>
                @else
                    <small style="color:#a12c6b">Belum bisa dipinjam karena semua eksemplar sedang dipinjam.</small>
                @endif
            </div>
        </div>
    </div>


    @if($related->isNotEmpty())
        <h3 class="tl-serif" style="margin:1.75rem 0 .75rem;color:#2d2a4a">Buku Terkait</h3>
        <div class="tl-books">
            @foreach($related as $b)
            <a href="{{ route('student.books.show', $b) }}" class="tl-book">
                <div class="tl-cover" style="background: {{ $grad[$b->id % 4] }}">
                    @if($b->cover_url)<img src="{{ $b->cover_url }}" alt="">@else<span>{{ $b->title }}</span>@endif
                </div>
                <h3>{{ $b->title }}</h3><p>{{ $b->author }}</p>
            </a>
            @endforeach
        </div>
    @endif
@include('student.partials.rating')
</x-student-layout>
