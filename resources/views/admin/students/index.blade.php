@php
    use App\Models\User;
    use Illuminate\Support\Facades\Route;

    $q = trim((string) request('q'));
    $f = in_array(request('f'), ['active', 'fine'], true) ? request('f') : '';
    $s = in_array(request('s'), ['nama', 'pinjam', 'denda'], true) ? request('s') : 'baru';

    $unpaid = fn ($l) => $l->where('status', 'Returned')->where('fine_amount', '>', 0)->whereNull('fine_paid_at');
    $base = User::where('role', 'student');
    $cnt = [
        'all'    => (clone $base)->count(),
        'active' => (clone $base)->whereHas('loans', fn ($l) => $l->where('status', 'Borrowed'))->count(),
        'fine'   => (clone $base)->whereHas('loans', $unpaid)->count(),
        'new'    => (clone $base)->where('created_at', '>=', now()->subDays(30))->count(),
    ];

    $list = User::where('role', 'student')
        ->withCount([
            'loans as total_loans'  => fn ($l) => $l->whereIn('status', ['Borrowed', 'Returned']),
            'loans as active_loans' => fn ($l) => $l->where('status', 'Borrowed'),
        ])
        ->withSum(['loans as fine_unpaid' => $unpaid], 'fine_amount')
        ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('nisn', 'like', "%$q%")->orWhere('email', 'like', "%$q%")))
        ->when($f === 'active', fn ($x) => $x->having('active_loans', '>', 0))
        ->when($f === 'fine', fn ($x) => $x->having('fine_unpaid', '>', 0));
    $list = match ($s) {
        'nama'   => $list->orderBy('name'),
        'pinjam' => $list->orderByDesc('total_loans')->orderBy('name'),
        'denda'  => $list->orderByDesc('fine_unpaid')->orderBy('name'),
        default  => $list->orderByDesc('id'),
    };
    $list = $list->paginate(10)->withQueryString();

    $url = fn ($ff) => route('admin.students.index', array_filter(['q' => $q ?: null, 'f' => $ff ?: null, 's' => $s !== 'baru' ? $s : null]));
    $ic = [
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',
        'book'  => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
        'coin'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5h4a1.5 1.5 0 0 1 0 3h-3a1.5 1.5 0 0 0 0 3h4"/>',
        'star'  => '<path d="M12 2l3 6.5 7 .9-5.1 4.8 1.3 7L12 17.8 5.8 21.2l1.3-7L2 9.4l7-.9z"/>',
        'clock' => '<path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/>',
        'key'   => '<circle cx="8" cy="15" r="4"/><path d="M10.8 12.2L20 3M16 7l3 3"/>',
    ];
@endphp
<x-admin-layout title="Daftar Siswa">
<style>
.sd { display: flex; flex-direction: column; gap: 1.1rem; padding-bottom: 2rem; font-family: 'Poppins', sans-serif; }
.sd * { box-sizing: border-box; }
@keyframes sd-up { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
.sd-head { position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; padding: 1.3rem 1.6rem; border-radius: 26px; background: linear-gradient(110deg, rgba(255,255,255,.92), rgba(240,230,253,.85) 55%, rgba(214,230,252,.85)); border: 1px solid #fff; box-shadow: 0 18px 44px rgba(139,92,246,.12); animation: sd-up .5s both; }
.sd-head::after { content: ''; position: absolute; right: -40px; top: -60px; width: 260px; height: 260px; border-radius: 50%; background: radial-gradient(circle, rgba(247,200,220,.65), transparent 70%); pointer-events: none; }
.sd-ttl { display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1; }
.sd-ico { display: grid; place-items: center; width: 54px; height: 54px; border-radius: 18px; color: #fff; background: linear-gradient(135deg, #a78bfa, #f48fb1); box-shadow: 0 12px 26px rgba(167,125,230,.4), inset 0 1px 0 rgba(255,255,255,.5); }
.sd-ico svg { width: 26px; height: 26px; fill: none; stroke: currentColor; stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round; }
.sd-ttl h1 { margin: 0; font-family: 'Playfair Display', serif; font-size: 1.7rem; color: #2d2a4a; line-height: 1.1; }
.sd-ttl p { margin: .2rem 0 0; font-size: .76rem; color: #7b7796; }
.sd-search { position: relative; z-index: 1; display: flex; align-items: center; gap: .6rem; min-width: 300px; flex: 0 1 400px; margin: 0; }
.sd-search input { flex: 1 1 auto; width: auto; min-width: 0; height: 44px; padding: 0 1rem; border-radius: 15px; border: 1px solid rgba(205,188,245,.95); background: rgba(255,255,255,.92); font: 500 .8rem 'Poppins', sans-serif; color: #3b3858; outline: 0; box-shadow: inset 0 1px 3px rgba(139,92,246,.08); }
.sd-search input:focus { border-color: #a78bfa; box-shadow: 0 0 0 4px rgba(167,139,250,.2); }
.sd-go { flex: 0 0 auto; height: 44px; padding: 0 1.3rem; border: 0; border-radius: 15px; cursor: pointer; font: 600 .78rem 'Poppins', sans-serif; color: #fff; background: linear-gradient(115deg, #9a80ee, #c58ae8 55%, #f29bc8); box-shadow: 0 10px 22px rgba(160,120,230,.38); transition: transform .2s ease; }
.sd-go:hover { transform: translateY(-2px); }

.sd-stats { display: grid; gap: .9rem; grid-template-columns: repeat(4, minmax(0, 1fr)); }
.sd-stat { display: flex; align-items: center; gap: .85rem; padding: 1rem 1.1rem; border-radius: 22px; background: rgba(255,255,255,.86); border: 1px solid #fff; box-shadow: 0 10px 26px rgba(139,92,246,.08); animation: sd-up .5s both; }
.sd-stat .i { flex: 0 0 42px; width: 42px; height: 42px; display: grid; place-items: center; border-radius: 15px; }
.sd-stat .i svg { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.sd-stat small { display: block; font-size: .66rem; color: #7b7796; }
.sd-stat b { display: block; font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #2d2a4a; line-height: 1.15; }
.sd .c1 { background: #e6defa; color: #7a54d6; } .sd .c2 { background: #d8f0e2; color: #3f9a6f; } .sd .c3 { background: #fbdbe8; color: #d9558a; } .sd .c4 { background: #d9e9fa; color: #4b8fd6; }

.sd-bar { display: flex; align-items: center; justify-content: space-between; gap: .8rem; flex-wrap: wrap; }
.sd-chips { display: flex; gap: .5rem; flex-wrap: wrap; }
.sd-chips a { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem 1rem; border-radius: 999px; font-size: .72rem; font-weight: 600; color: #6f6b8c; text-decoration: none; background: rgba(255,255,255,.85); border: 1px solid #fff; box-shadow: 0 4px 12px rgba(139,92,246,.06); transition: all .2s ease; }
.sd-chips a em { font-style: normal; padding: .05rem .45rem; border-radius: 999px; font-size: .62rem; background: #f1ecfb; color: #7a54d6; }
.sd-chips a:hover { color: #5b34a8; transform: translateY(-1px); }
.sd-chips a.on { color: #fff; background: linear-gradient(115deg, #a98cf0, #f29bc8); box-shadow: 0 8px 18px rgba(167,125,230,.35); }
.sd-chips a.on em { background: rgba(255,255,255,.28); color: #fff; }
.sd-sort { display: flex; align-items: center; gap: .5rem; font-size: .7rem; color: #7b7796; margin: 0; }
.sd-sort select { height: 38px; padding: 0 2.2rem 0 .9rem; border-radius: 12px; border: 1px solid rgba(205,188,245,.95); background-color: rgba(255,255,255,.92); font: 600 .72rem 'Poppins', sans-serif; color: #5b34a8; outline: 0; }

.sd-list { display: flex; flex-direction: column; gap: .6rem; }
.sd-cols { display: grid; grid-template-columns: minmax(220px, 2.2fr) 1.1fr 1.1fr .65fr .65fr 1fr 206px; gap: 1rem; padding: 0 1.3rem; font-size: .6rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: #9a8fd0; }
.sd-row { position: relative; display: grid; grid-template-columns: minmax(220px, 2.2fr) 1.1fr 1.1fr .65fr .65fr 1fr 206px; gap: 1rem; align-items: center; padding: .85rem 1.3rem; border-radius: 20px; background: linear-gradient(150deg, rgba(255,255,255,.92), rgba(250,243,255,.8)); border: 1px solid #fff; box-shadow: 0 8px 22px rgba(139,92,246,.07); transition: transform .22s ease, box-shadow .22s ease; animation: sd-up .5s both; }
.sd-row::before { content: ''; position: absolute; left: 0; top: 16%; bottom: 16%; width: 4px; border-radius: 0 6px 6px 0; background: linear-gradient(180deg, #a78bfa, #f48fb1); opacity: 0; transition: opacity .22s ease; }
.sd-row:hover { transform: translateY(-2px); box-shadow: 0 16px 34px rgba(139,92,246,.16); }
.sd-row:hover::before { opacity: 1; }
.sd-who { display: flex; align-items: center; gap: .85rem; min-width: 0; }
.sd-av { flex: 0 0 46px; width: 46px; height: 46px; border-radius: 50%; overflow: hidden; display: grid; place-items: center; font-weight: 600; font-size: .95rem; color: #5b34a8; background: linear-gradient(135deg, #f7c8dc, #d9ccf5); border: 2px solid #fff; box-shadow: 0 5px 14px rgba(139,92,246,.2); }
.sd-av img { width: 100%; height: 100%; object-fit: cover; }
.sd-who b { display: block; font-size: .86rem; color: #2d2a4a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sd-who small { display: block; font-size: .68rem; color: #8b87a8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sd-nisn { display: inline-block; padding: .25rem .7rem; border-radius: 10px; font-size: .74rem; font-weight: 600; letter-spacing: .04em; color: #5f5b7a; background: rgba(241,236,251,.9); }
.sd-date b { display: block; font-size: .78rem; color: #3b3858; font-weight: 500; }
.sd-date small { font-size: .62rem; color: #9a96b8; }
.sd-num { font-family: 'Playfair Display', serif; font-size: 1.15rem; color: #2d2a4a; }
.sd-act { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 28px; padding: 0 .6rem; border-radius: 999px; font-size: .76rem; font-weight: 700; background: #f1ecfb; color: #8b87a8; }
.sd-act.on { background: #d8f0e2; color: #2f6b4f; }
.sd-fine { font-size: .76rem; font-weight: 700; color: #c93b64; }
.sd-fine.none { color: #b3aed0; font-weight: 500; }
.sd-btns { display: flex; gap: .45rem; align-items: center; flex-wrap: nowrap; }
.sd-btns form { margin: 0; display: inline-flex; }
.sd-b { display: inline-flex; align-items: center; gap: .4rem; height: 36px; padding: 0 .95rem; border-radius: 12px; cursor: pointer; text-decoration: none; white-space: nowrap; font: 600 .72rem 'Poppins', sans-serif; transition: all .2s ease; }
.sd-b svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.sd-b.main { color: #fff; border: 0; background: linear-gradient(115deg, #9a80ee, #c58ae8 55%, #f29bc8); box-shadow: 0 8px 18px rgba(160,120,230,.35), inset 0 1px 0 rgba(255,255,255,.5); }
.sd-b.main:hover { transform: translateY(-2px); box-shadow: 0 14px 26px rgba(160,120,230,.45); }
.sd-b.soft { color: #a5741a; background: #fff8e8; border: 1px solid #f1dcaa; }
.sd-b.soft:hover { background: #fff0cc; transform: translateY(-1px); }

.sd-empty { text-align: center; padding: 2.6rem 1rem; border-radius: 24px; background: rgba(255,255,255,.8); border: 1.5px dashed #d9ccf5; color: #8b87a8; font-size: .82rem; }
.sd-empty b { display: block; margin: .6rem 0 .2rem; font-family: 'Playfair Display', serif; font-size: 1.15rem; color: #2d2a4a; }
.sd-pg { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; font-size: .72rem; color: #7b7796; }
.sd-pg div { display: flex; gap: .35rem; }
.sd-pg a, .sd-pg span.cur { display: grid; place-items: center; min-width: 36px; height: 36px; padding: 0 .6rem; border-radius: 12px; font-weight: 600; text-decoration: none; color: #6f6b8c; background: rgba(255,255,255,.88); border: 1px solid #fff; box-shadow: 0 4px 10px rgba(139,92,246,.06); }
.sd-pg a:hover { color: #5b34a8; transform: translateY(-1px); }
.sd-pg span.cur { color: #fff; background: linear-gradient(115deg, #a98cf0, #f29bc8); border: 0; }

@media (max-width: 820px) { .sd-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 940px) {
  .sd-cols { display: none; }
  .sd-row { grid-template-columns: 1fr 1fr; row-gap: .7rem; }
  .sd-who, .sd-btns { grid-column: 1 / -1; }
  .sd-row [data-l]::before { content: attr(data-l); display: block; font-size: .56rem; letter-spacing: .12em; text-transform: uppercase; color: #9a8fd0; margin-bottom: .15rem; }
}
@media (max-width: 640px) { .sd-stats { grid-template-columns: 1fr; } .sd-search { min-width: 0; flex: 1 1 100%; } }
@media (prefers-reduced-motion: reduce) { .sd *, .sd-row { animation: none !important; } }
</style>

<div class="sd">
    <div class="sd-head">
        <div class="sd-ttl">
            <span class="sd-ico"><svg viewBox="0 0 24 24">{!! $ic['users'] !!}</svg></span>
            <div><h1>Daftar Siswa</h1><p>Data siswa terdaftar, riwayat pinjam, dan reset password.</p></div>
        </div>
        <form class="sd-search" method="GET" action="{{ route('admin.students.index') }}">
            @if($f)<input type="hidden" name="f" value="{{ $f }}">@endif
            @if($s !== 'baru')<input type="hidden" name="s" value="{{ $s }}">@endif
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, NISN, atau email" autocomplete="off">
            <button type="button" class="sd-go" onclick="this.form.submit()">Cari</button>
        </form>
    </div>

    <div class="sd-stats">
        <div class="sd-stat"><span class="i c1"><svg viewBox="0 0 24 24">{!! $ic['users'] !!}</svg></span><div><small>Total Siswa</small><b>{{ $cnt['all'] }}</b></div></div>
        <div class="sd-stat"><span class="i c2"><svg viewBox="0 0 24 24">{!! $ic['book'] !!}</svg></span><div><small>Sedang Meminjam</small><b>{{ $cnt['active'] }}</b></div></div>
        <div class="sd-stat"><span class="i c3"><svg viewBox="0 0 24 24">{!! $ic['coin'] !!}</svg></span><div><small>Punya Denda</small><b>{{ $cnt['fine'] }}</b></div></div>
        <div class="sd-stat"><span class="i c4"><svg viewBox="0 0 24 24">{!! $ic['star'] !!}</svg></span><div><small>Baru 30 Hari</small><b>{{ $cnt['new'] }}</b></div></div>
    </div>

    <div class="sd-bar">
        <div class="sd-chips">
            <a href="{{ $url('') }}" class="{{ $f === '' ? 'on' : '' }}">Semua <em>{{ $cnt['all'] }}</em></a>
            <a href="{{ $url('active') }}" class="{{ $f === 'active' ? 'on' : '' }}">Sedang meminjam <em>{{ $cnt['active'] }}</em></a>
            <a href="{{ $url('fine') }}" class="{{ $f === 'fine' ? 'on' : '' }}">Punya denda <em>{{ $cnt['fine'] }}</em></a>
        </div>
        <form class="sd-sort" method="GET" action="{{ route('admin.students.index') }}">
            @if($q !== '')<input type="hidden" name="q" value="{{ $q }}">@endif
            @if($f)<input type="hidden" name="f" value="{{ $f }}">@endif
            <label for="sdSort">Urutkan</label>
            <select id="sdSort" name="s" onchange="this.form.submit()">
                <option value="baru" @selected($s === 'baru')>Terbaru</option>
                <option value="nama" @selected($s === 'nama')>Nama A-Z</option>
                <option value="pinjam" @selected($s === 'pinjam')>Paling sering pinjam</option>
                <option value="denda" @selected($s === 'denda')>Denda terbesar</option>
            </select>
        </form>
    </div>

    @if($list->isEmpty())
        <div class="sd-empty">
            <svg width="54" height="54" viewBox="0 0 64 64" fill="none"><polygon points="32,4 46,24 32,60 18,24" fill="#e6defa" stroke="#b9a8ee" stroke-width="1.5"/><polygon points="32,4 46,24 18,24" fill="#fff" fill-opacity=".6"/></svg>
            <b>Tidak ada siswa yang cocok</b>
            Coba kata kunci lain atau pilih filter "Semua".
        </div>
    @else
    <div class="sd-list">
        <div class="sd-cols"><span>Siswa</span><span>NISN</span><span>Bergabung</span><span>Total</span><span>Aktif</span><span>Denda</span><span>Aksi</span></div>
        @foreach($list as $st)
            @php $dd = (int) ($st->fine_unpaid ?? 0); @endphp
            <div class="sd-row" style="animation-delay: {{ min($loop->index, 8) * .04 }}s">
                <div class="sd-who">
                    <span class="sd-av">@if($st->avatar_url)<img src="{{ $st->avatar_url }}" alt="">@else{{ strtoupper(mb_substr($st->name, 0, 1)) }}@endif</span>
                    <div style="min-width:0"><b>{{ $st->name }}</b><small>{{ $st->email }}</small></div>
                </div>
                <div data-l="NISN"><span class="sd-nisn">{{ $st->nisn ?: '-' }}</span></div>
                <div class="sd-date" data-l="Bergabung"><b>{{ $st->created_at->translatedFormat('d M Y') }}</b><small>{{ $st->created_at->diffForHumans() }}</small></div>
                <div data-l="Total pinjam"><span class="sd-num">{{ $st->total_loans }}</span></div>
                <div data-l="Aktif"><span class="sd-act {{ $st->active_loans > 0 ? 'on' : '' }}">{{ $st->active_loans }}</span></div>
                <div data-l="Denda"><span class="sd-fine {{ $dd > 0 ? '' : 'none' }}">{{ $dd > 0 ? 'Rp ' . number_format($dd, 0, ',', '.') : 'Tidak ada' }}</span></div>
                <div class="sd-btns">
                    <a class="sd-b main" href="{{ route('admin.students.show', $st) }}"><svg viewBox="0 0 24 24">{!! $ic['clock'] !!}</svg>Riwayat</a>
                    @if(Route::has('admin.students.reset'))
                    <form method="POST" action="{{ route('admin.students.reset', $st) }}">
                        @csrf @method('PATCH')
                        <button type="button" class="sd-b soft" onclick="if (confirm('Reset password siswa ini?')) { this.form.submit(); }"><svg viewBox="0 0 24 24">{!! $ic['key'] !!}</svg>Reset PW</button>
                    </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="sd-pg">
        <span>Menampilkan {{ $list->firstItem() }}-{{ $list->lastItem() }} dari {{ $list->total() }} siswa</span>
        @if($list->hasPages())
        <div>
            @if(!$list->onFirstPage())<a href="{{ $list->previousPageUrl() }}">&lsaquo;</a>@endif
            @foreach($list->getUrlRange(max(1, $list->currentPage() - 2), min($list->lastPage(), $list->currentPage() + 2)) as $pg => $pu)
                @if($pg === $list->currentPage())<span class="cur">{{ $pg }}</span>@else<a href="{{ $pu }}">{{ $pg }}</a>@endif
            @endforeach
            @if($list->hasMorePages())<a href="{{ $list->nextPageUrl() }}">&rsaquo;</a>@endif
        </div>
        @endif
    </div>
    @endif
</div>
</x-admin-layout>