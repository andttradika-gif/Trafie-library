@php
    $pal = ['#a78bfa', '#f48fb1', '#7cc6f5', '#7fd1ae', '#f6c36b', '#c58ae8'];
    $stars = []; $byCat = [];
    foreach ($completions as $c) {
        $x = 70 + (crc32('s' . $c->book_id) % 660);
        $y = 60 + (crc32('t' . $c->book_id) % 360);
        $col = $pal[($c->book->category_id ?? 0) % 6];
        $stars[] = ['x' => $x, 'y' => $y, 'col' => $col, 'r' => 4 + (int) $c->rating, 'fav' => in_array($c->book_id, $favIds), 'title' => $c->book->title ?? 'Buku', 'cat' => $c->book->category->name ?? 'Umum', 'date' => $c->completed_at->format('d M Y'), 'rating' => (int) $c->rating, 'url' => route('student.books.show', $c->book), 'cid' => $c->book->category_id ?? 0];
        $byCat[$c->book->category_id ?? 0][] = [$x, $y, $col];
    }
    $neb = min(6, max(1, $lv['level']));
@endphp
<x-student-layout title="Reading Galaxy">
    <div class="tl-row" style="justify-content:space-between;margin-bottom:.8rem">
        <p class="pg-sub" style="margin:0">Level {{ $lv['level'] }} - {{ $lv['name'] }}. Galaksimu berisi <b>{{ count($stars) }} bintang</b>: satu bintang untuk setiap buku yang selesai kamu baca (terverifikasi).</p>
    </div>
    <div class="gx-wrap">
        <svg class="gx" viewBox="0 0 800 480" preserveAspectRatio="xMidYMid slice">
            <defs>
                <radialGradient id="gxBg" cx="50%" cy="45%" r="75%"><stop offset="0" stop-color="#34307a"/><stop offset=".55" stop-color="#1d2a5e"/><stop offset="1" stop-color="#0f1a40"/></radialGradient>
                <radialGradient id="gxN1"><stop offset="0" stop-color="#f48fb1" stop-opacity=".55"/><stop offset="1" stop-color="#f48fb1" stop-opacity="0"/></radialGradient>
                <radialGradient id="gxN2"><stop offset="0" stop-color="#7cc6f5" stop-opacity=".5"/><stop offset="1" stop-color="#7cc6f5" stop-opacity="0"/></radialGradient>
                <radialGradient id="gxN3"><stop offset="0" stop-color="#a78bfa" stop-opacity=".55"/><stop offset="1" stop-color="#a78bfa" stop-opacity="0"/></radialGradient>
            </defs>
            <rect width="800" height="480" fill="url(#gxBg)"/>
            <circle cx="620" cy="130" r="{{ 70 + $neb * 22 }}" fill="url(#gxN1)"/>
            <circle cx="180" cy="340" r="{{ 60 + $neb * 24 }}" fill="url(#gxN2)"/>
            @if($neb >= 3)<circle cx="420" cy="230" r="{{ 40 + $neb * 22 }}" fill="url(#gxN3)"/>@endif
            @for($i = 0; $i < 70; $i++)
                <circle cx="{{ crc32('bx' . $i) % 800 }}" cy="{{ crc32('by' . $i) % 480 }}" r="{{ 0.5 + (crc32('br' . $i) % 10) / 10 }}" fill="#fff" opacity="{{ 0.25 + (crc32('bo' . $i) % 50) / 100 }}"/>
            @endfor
            @foreach($byCat as $cid => $pts)
                @if(count($pts) > 1)
                    @for($i = 1; $i < count($pts); $i++)
                        <line x1="{{ $pts[$i-1][0] }}" y1="{{ $pts[$i-1][1] }}" x2="{{ $pts[$i][0] }}" y2="{{ $pts[$i][1] }}" stroke="{{ $pts[$i][2] }}" stroke-opacity=".55" stroke-width="1.2" stroke-dasharray="4 4"/>
                    @endfor
                @endif
            @endforeach
            @foreach($stars as $s)
                <g class="gx-star" data-title="{{ $s['title'] }}" data-cat="{{ $s['cat'] }}" data-date="{{ $s['date'] }}" data-rating="{{ $s['rating'] }}" data-url="{{ $s['url'] }}" style="color: {{ $s['col'] }}">
                    @if($s['fav'])<circle cx="{{ $s['x'] }}" cy="{{ $s['y'] }}" r="{{ $s['r'] + 9 }}" fill="{{ $s['col'] }}" opacity=".3" class="gx-fav"/>@endif
                    <circle cx="{{ $s['x'] }}" cy="{{ $s['y'] }}" r="{{ $s['r'] + 14 }}" fill="transparent"/>
                    <path d="M{{ $s['x'] }} {{ $s['y'] - $s['r'] - 5 }} L{{ $s['x'] + $s['r'] * .35 }} {{ $s['y'] - $s['r'] * .35 }} L{{ $s['x'] + $s['r'] + 5 }} {{ $s['y'] }} L{{ $s['x'] + $s['r'] * .35 }} {{ $s['y'] + $s['r'] * .35 }} L{{ $s['x'] }} {{ $s['y'] + $s['r'] + 5 }} L{{ $s['x'] - $s['r'] * .35 }} {{ $s['y'] + $s['r'] * .35 }} L{{ $s['x'] - $s['r'] - 5 }} {{ $s['y'] }} L{{ $s['x'] - $s['r'] * .35 }} {{ $s['y'] - $s['r'] * .35 }}Z" fill="#fff" stroke="{{ $s['col'] }}" stroke-width="1.5"/>
                </g>
            @endforeach
        </svg>
        @if(empty($stars))<div class="gx-empty">Galaksimu masih kosong. Selesaikan buku pertamamu (lewat Reading Progress) untuk menyalakan bintang pertama.</div>@endif
        <div class="gx-info" id="gxInfo" style="display:none"></div>
    </div>
    <div class="tl-row" style="margin-top:.8rem;font-size:.7rem;color:#6f6b8c">
        <span>Warna bintang = genre.</span><span>Bintang berpendar = buku favoritmu.</span><span>Garis putus-putus = konstelasi satu genre.</span>
    </div>
    <script>
    (function () {
        var box = document.getElementById('gxInfo');
        document.querySelectorAll('.gx-star').forEach(function (g) {
            g.addEventListener('click', function () {
                var r = parseInt(g.dataset.rating, 10) || 0;
                var st = ''; for (var i = 0; i < 5; i++) { st += i < r ? '\u2605' : '\u2606'; }
                box.innerHTML = '<b></b><small></small><span class="pg-stars"></span><a class="pg-btn" style="text-decoration:none;display:inline-block;margin-top:.5rem">Lihat buku</a>';
                box.querySelector('b').textContent = g.dataset.title;
                box.querySelector('small').textContent = g.dataset.cat + ' - selesai ' + g.dataset.date;
                box.querySelector('.pg-stars').textContent = st;
                box.querySelector('a').href = g.dataset.url;
                box.style.display = 'block';
            });
        });
    })();
    </script>
</x-student-layout>