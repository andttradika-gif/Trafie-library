@php $grad = ['linear-gradient(135deg,#cfe8f7,#b9a8ee)','linear-gradient(135deg,#f7c8dc,#d9ccf5)','linear-gradient(135deg,#cfe5d6,#cfe8f7)','linear-gradient(135deg,#d9ccf5,#f7c8dc)']; @endphp
<x-student-layout title="Reservations">
    <div class="tl-card" style="margin-bottom:1.1rem">
        <h3 class="pg-h">Reservasi Saya</h3>
        <p class="pg-sub">Kalau semua eksemplar sedang dipinjam, kamu bisa masuk antrean. Saat buku kembali, eksemplar ditahan untukmu selama {{ (int) config('library.reserve_hold_days', 2) }} hari. Ajukan pinjam sebelum batas itu, kalau tidak giliran pindah ke siswa berikutnya.</p>
        <div style="overflow-x:auto">
        <table class="tl-table">
            <thead><tr><th>Buku</th><th>Status</th><th>Antrean</th><th>Batas Ambil</th><th style="width:300px">Aksi</th></tr></thead>
            <tbody>
            @forelse($mine as $r)
                <tr>
                    <td><b>{{ $r->book->title ?? '-' }}</b><br><small>{{ $r->book->author ?? '' }}</small></td>
                    <td><span class="pg-b pg-b-{{ strtolower($r->status) }}">{{ $r->label }}</span></td>
                    <td>{{ $r->status === 'Waiting' ? '#' . ($pos[$r->id] ?? '-') : '-' }}</td>
                    <td>{{ $r->status === 'Ready' && $r->expires_at ? $r->expires_at->format('d M Y H:i') : '-' }}</td>
                    <td>
                        <div class="pg-actions">
                            @if($r->status === 'Ready')
                                <form method="POST" action="{{ route('student.reservations.claim', $r) }}" class="pg-actions">@csrf
                                    <select class="tl-input" name="duration" style="width:105px">
                                        @foreach([3, 7, 10] as $d)<option value="{{ $d }}" @selected($defaultDur == $d)>{{ $d }} hari</option>@endforeach
                                    </select>
                                    <button class="pg-btn" type="submit">Ajukan pinjam</button>
                                </form>
                            @endif
                            @if(in_array($r->status, ['Waiting', 'Ready']))
                                <form method="POST" action="{{ route('student.reservations.cancel', $r) }}" onsubmit="return confirm('Batalkan reservasi ini?')">@csrf @method('PATCH')<button class="tl-btn-danger" type="submit">Batalkan</button></form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="tl-empty">Kamu belum punya reservasi.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="tl-card">
        <h3 class="pg-h">Buku yang Bisa Dipesan</h3>
        <p class="pg-sub">Daftar buku yang semua eksemplarnya sedang dipinjam atau ditahan.</p>
        <form method="GET" action="{{ route('student.reservations.index') }}" class="tl-row" style="margin-bottom:1rem">
            <input class="tl-input" style="max-width:300px" name="q" value="{{ $q }}" placeholder="Cari judul atau penulis">
            <button class="tl-btn" type="submit">Cari</button>
        </form>
        @if($books->isEmpty())
            <div class="tl-empty">Tidak ada buku yang perlu dipesan. Semua buku yang kamu cari sedang tersedia, atau kamu sudah mengantre.</div>
        @else
        <div class="st-grid">
            @foreach($books as $b)
            <div class="st-bk">
                <div class="cv" style="background: {{ $grad[$b->id % 4] }}">@if($b->cover_url)<img src="{{ $b->cover_url }}" alt="">@else<span>{{ $b->title }}</span>@endif</div>
                <h4>{{ $b->title }}</h4><p>{{ $b->author }}</p>
                <small>Antrean: {{ $queue[$b->id] ?? 0 }} orang</small>
                <form method="POST" action="{{ route('student.reservations.store', $b) }}">@csrf<button class="pg-btn" type="submit" style="width:100%;margin-top:.5rem">Masuk antrean</button></form>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</x-student-layout>