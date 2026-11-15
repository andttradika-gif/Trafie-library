@php
    use App\Models\ReadingProgress;
    $grad = ['linear-gradient(135deg,#cfe8f7,#b9a8ee)','linear-gradient(135deg,#f7c8dc,#d9ccf5)','linear-gradient(135deg,#cfe5d6,#cfe8f7)','linear-gradient(135deg,#d9ccf5,#f7c8dc)'];
    $order = ['Reading', 'Paused', 'Want', 'Completed'];
    $grouped = $items->groupBy('status');
    $badge = ['Reading' => 'pg-b-active', 'Paused' => 'pg-b-closed', 'Want' => 'pg-b-waiting', 'Completed' => 'pg-b-verified'];
@endphp
<x-student-layout title="Reading Progress">
<style>
.rp-body .rp-page{display:flex;gap:.6rem;align-items:stretch;margin-top:.6rem}
.rp-body .rp-page .tl-input{flex:0 0 96px;width:96px;text-align:center}
.rp-body .rp-page .pg-btn{flex:1;min-width:0;justify-content:center;white-space:nowrap}
.rp-body .rp-acts{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:.6rem;margin-top:.6rem}
.rp-body .rp-acts form{margin:0;display:block}
.rp-body .rp-acts .pg-btn,.rp-body .rp-acts .tl-btn-danger{width:100%;display:inline-flex;align-items:center;justify-content:center;white-space:nowrap}
@media(max-width:420px){.rp-body .rp-page{flex-direction:column}.rp-body .rp-page .tl-input{flex:none;width:100%}.rp-body .rp-acts{grid-template-columns:1fr}}
/* rp-fix2 */
.rp-body .rp-acts{display:grid !important;grid-auto-flow:column !important;grid-auto-columns:minmax(0,1fr) !important;grid-template-columns:none !important;gap:.5rem !important;width:100% !important}
.rp-body .rp-acts form{display:block !important;width:100% !important;min-width:0 !important;margin:0 !important}
.rp-body .rp-acts .pg-btn,.rp-body .rp-acts .tl-btn-danger{display:flex !important;width:100% !important;align-items:center;justify-content:center;gap:.35rem;padding-left:.5rem !important;padding-right:.5rem !important;font-size:.82rem !important;white-space:nowrap}
.rp-body .rp-page .pg-btn{width:auto !important}
@media(max-width:420px){.rp-body .rp-acts{grid-auto-flow:row !important;grid-auto-columns:auto !important;grid-template-columns:1fr !important}}
</style>
    <div class="tl-card" style="margin-bottom:1.1rem">
        <h3 class="pg-h">Catat Buku</h3>
        <p class="pg-sub">Alur XP: pinjam buku, catat progres, ajukan selesai, kembalikan buku, lalu isi rating dan refleksi. Setelah terverifikasi kamu mendapat XP (100 buku selesai, 20 ulasan, 10 rating, 25 genre baru).</p>
        <form method="POST" action="{{ route('student.reading.store') }}" class="tl-row">
            @csrf
            <select class="tl-input" name="book_id" style="max-width:340px" required>
                <option value="">Pilih buku</option>
                @if($loans->isNotEmpty())<optgroup label="Buku yang kamu pinjam">@foreach($loans as $l)<option value="{{ $l->book_id }}">{{ $l->book->title ?? '-' }}</option>@endforeach</optgroup>@endif
                @if($others->isNotEmpty())<optgroup label="Buku lain (Want to Read)">@foreach($others as $b)<option value="{{ $b->id }}">{{ $b->title }}</option>@endforeach</optgroup>@endif
            </select>
            <select class="tl-input" name="status" style="max-width:190px"><option value="Reading">Currently Reading</option><option value="Want">Want to Read</option></select>
            <button class="tl-btn" type="submit">Tambah</button>
        </form>
    </div>

    @forelse($order as $st)
        @if($grouped->has($st))
        <h3 class="pg-h" style="margin:1.1rem 0 .6rem">{{ ReadingProgress::LABELS[$st] }}</h3>
        <div class="rp-list">
            @foreach($grouped[$st] as $p)
            <div class="rp-item">
                <div class="rp-cv" style="background: {{ $grad[$p->book_id % 4] }}">@if($p->book->cover_url)<img src="{{ $p->book->cover_url }}" alt="">@else<span>{{ $p->book->title }}</span>@endif</div>
                <div class="rp-body">
                    <div class="rp-top"><b>{{ $p->book->title }}</b><span class="pg-b {{ $badge[$p->status] ?? '' }}">{{ ReadingProgress::LABELS[$p->status] }}</span></div>
                    <small>{{ $p->book->author }}</small>
                    <div class="rp-bar"><i style="width:{{ $p->percent }}%"></i></div>
                    <small>Hal. {{ $p->current_page }}@if($p->total_pages) / {{ $p->total_pages }}@endif &middot; {{ $p->percent }}%</small>

                    @if($p->status === 'Reading')
                        <form method="POST" action="{{ route('student.reading.update', $p) }}" class="rp-page">@csrf @method('PATCH')
                            <input type="hidden" name="action" value="page">
                            <input class="tl-input" type="number" name="current_page" min="0" @if($p->total_pages) max="{{ $p->total_pages }}" @endif value="{{ $p->current_page }}">
                            <button class="pg-btn" type="submit">Simpan halaman</button>
                        </form>
                        @if($p->completion_requested_at && in_array($p->book_id, $returnedIds))
                            <form method="POST" action="{{ route('student.reading.complete', $p) }}" class="rp-form">@csrf
                                <label class="tl-label">Rating buku ini</label>
                                <select class="tl-input" name="rating" required style="width:140px">@foreach([5,4,3,2,1] as $s)<option value="{{ $s }}">{{ $s }} bintang</option>@endforeach</select>
                                <label class="tl-label" style="margin-top:.5rem">Refleksi (min. 20 karakter)</label>
                                <textarea class="tl-input" name="reflection" rows="3" required minlength="20" maxlength="1000" placeholder="Apa yang kamu pelajari atau rasakan dari buku ini?"></textarea>
                                <button class="tl-btn" type="submit" style="margin-top:.6rem">Verifikasi Penyelesaian</button>
                            </form>
                        @elseif($p->completion_requested_at)
                            <p class="pg-sub" style="margin:.5rem 0 0">Penyelesaian diajukan. Kembalikan bukunya ke perpustakaan, lalu form rating dan refleksi muncul di sini.</p>
                        @endif
                        <div class="rp-acts">
                            @if(!$p->completion_requested_at)
                            <form method="POST" action="{{ route('student.reading.update', $p) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="request"><button class="pg-btn" type="submit">Ajukan selesai</button></form>
                            @endif
                            <form method="POST" action="{{ route('student.reading.update', $p) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="pause"><button class="pg-btn" type="submit">Jeda</button></form>
                            <form method="POST" action="{{ route('student.reading.update', $p) }}" onsubmit="return confirm('Hapus dari daftar?')">@csrf @method('PATCH')<input type="hidden" name="action" value="remove"><button class="tl-btn-danger" type="submit">Hapus</button></form>
                        </div>
                    @elseif(in_array($p->status, ['Paused', 'Want']))
                        <div class="rp-acts">
                            <form method="POST" action="{{ route('student.reading.update', $p) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="start"><button class="pg-btn" type="submit">{{ $p->status === 'Want' ? 'Mulai membaca' : 'Lanjutkan' }}</button></form>
                            <form method="POST" action="{{ route('student.reading.update', $p) }}" onsubmit="return confirm('Hapus dari daftar?')">@csrf @method('PATCH')<input type="hidden" name="action" value="remove"><button class="tl-btn-danger" type="submit">Hapus</button></form>
                        </div>
                    @else
                        @php $cm = $compMap[$p->book_id] ?? null; @endphp
                        @if($cm)<small>Selesai {{ $cm->completed_at->format('d M Y') }} &middot; <span class="pg-stars">{!! str_repeat('&#9733;', (int) $cm->rating) !!}</span></small>@endif
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    @empty
    @endforelse
    @if($items->isEmpty())<div class="tl-card tl-empty">Belum ada buku di daftarmu. Tambahkan buku pertamamu di atas.</div>@endif
</x-student-layout>