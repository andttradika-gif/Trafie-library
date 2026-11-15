@php
    use Illuminate\Support\Facades\DB;
    $done = DB::table('challenge_participants')->whereNotNull('completed_at')->selectRaw('challenge_id, count(*) as c')->groupBy('challenge_id')->pluck('c', 'challenge_id');
    $stat = [
        'aktif'   => $challenges->where('status', 'Active')->count(),
        'tutup'   => $challenges->where('status', '!=', 'Active')->count(),
        'peserta' => (int) $challenges->sum('participants_count'),
        'selesai' => (int) $done->sum(),
    ];
@endphp
<x-admin-layout title="Tantangan Membaca">
<x-admin-head title="Tantangan Membaca" sub="Ajak siswa membaca lewat target buku, XP, dan badge." icon="award">
    <button type="button" class="ah-btn" onclick="document.getElementById('chForm').classList.toggle('open')">+ Tantangan Baru</button>
</x-admin-head>
<style>
.ch-form { display: none; margin-bottom: 1.1rem; padding: 1.4rem; border-radius: 26px; background: linear-gradient(150deg, rgba(255,255,255,.94), rgba(247,238,255,.8)); border: 1px solid #fff; box-shadow: 0 20px 48px rgba(139,92,246,.14); font-family: 'Poppins', sans-serif; }
.ch-form.open { display: block; animation: ah-up .4s both; }
.ch-form h3 { margin: 0 0 .2rem; font-family: 'Playfair Display', serif; font-size: 1.15rem; color: #2d2a4a; }
.ch-form > p { margin: 0 0 1rem; font-size: .72rem; color: #7b7796; }
.ch-fg { display: grid; gap: .9rem 1.1rem; grid-template-columns: repeat(4, minmax(0, 1fr)); }
.ch-fg .w2 { grid-column: span 2; } .ch-fg .w4 { grid-column: 1 / -1; }
.ch-fg textarea { min-height: 70px; }
.ch-grid { display: grid; gap: 1.1rem; grid-template-columns: repeat(auto-fill, minmax(330px, 1fr)); font-family: 'Poppins', sans-serif; }
.ch-c { position: relative; overflow: hidden; padding: 1.3rem; border-radius: 26px; background: linear-gradient(150deg, rgba(255,255,255,.94), rgba(247,238,255,.78)); border: 1px solid #fff; box-shadow: 0 18px 44px rgba(139,92,246,.1); transition: transform .25s ease, box-shadow .25s ease; animation: ah-up .5s both; }
.ch-c:hover { transform: translateY(-3px); box-shadow: 0 24px 54px rgba(139,92,246,.18); }
.ch-c::before { content: ''; position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; border-radius: 50%; background: radial-gradient(circle, rgba(247,200,220,.6), transparent 70%); pointer-events: none; }
.ch-top { display: flex; align-items: center; justify-content: space-between; gap: .5rem; margin-bottom: .6rem; position: relative; }
.ch-pill { padding: .2rem .7rem; border-radius: 999px; font-size: .62rem; font-weight: 700; }
.ch-pill.on { background: #d8f0e2; color: #2f6b4f; } .ch-pill.off { background: #f1ecfb; color: #7b7796; } .ch-pill.end { background: #fdf0cf; color: #a5741a; }
.ch-c h4 { margin: 0 0 .25rem; font-family: 'Playfair Display', serif; font-size: 1.15rem; color: #2d2a4a; position: relative; }
.ch-c > p { margin: 0 0 .8rem; font-size: .72rem; line-height: 1.55; color: #7b7796; min-height: 2.2em; position: relative; }
.ch-tags { display: flex; gap: .4rem; flex-wrap: wrap; margin-bottom: .9rem; position: relative; }
.ch-tags span { padding: .25rem .7rem; border-radius: 999px; font-size: .64rem; font-weight: 600; background: #f1ecfb; color: #5b34a8; }
.ch-tags span.xp { background: #fdf0cf; color: #a5741a; } .ch-tags span.bd { background: #fbdbe8; color: #b13a72; }
.ch-bar { height: 8px; border-radius: 999px; background: #eee9fa; overflow: hidden; }
.ch-bar i { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #a78bfa, #f48fb1); }
.ch-meta { display: flex; justify-content: space-between; margin: .4rem 0 .9rem; font-size: .64rem; color: #8b87a8; }
.ch-num { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; margin-bottom: .9rem; }
.ch-num div { padding: .6rem .8rem; border-radius: 14px; background: rgba(250,247,255,.9); border: 1px solid rgba(217,204,245,.6); }
.ch-num b { display: block; font-family: 'Playfair Display', serif; font-size: 1.3rem; color: #2d2a4a; line-height: 1.1; }
.ch-num small { font-size: .62rem; color: #8b87a8; }
.ch-act { display: flex; gap: .5rem; }
.ch-act form { margin: 0; display: inline-flex; }
.ch-empty { grid-column: 1 / -1; text-align: center; padding: 2.6rem 1rem; border-radius: 24px; background: rgba(255,255,255,.8); border: 1.5px dashed #d9ccf5; color: #8b87a8; font-size: .82rem; }
@media (max-width: 900px) { .ch-fg { grid-template-columns: 1fr 1fr; } .ch-fg .w2 { grid-column: 1 / -1; } }
</style>

<div class="ah-stats">
    <div class="ah-stat"><span class="i x2">{{ $stat['aktif'] }}</span><div><small>Tantangan Aktif</small><b>{{ $stat['aktif'] }}</b></div></div>
    <div class="ah-stat"><span class="i x1">{{ $stat['tutup'] }}</span><div><small>Ditutup</small><b>{{ $stat['tutup'] }}</b></div></div>
    <div class="ah-stat"><span class="i x4">{{ $stat['peserta'] }}</span><div><small>Total Peserta</small><b>{{ $stat['peserta'] }}</b></div></div>
    <div class="ah-stat"><span class="i x3">{{ $stat['selesai'] }}</span><div><small>Berhasil Diselesaikan</small><b>{{ $stat['selesai'] }}</b></div></div>
</div>

<div class="ch-form {{ $errors->any() ? 'open' : '' }}" id="chForm">
    <h3>Buat Tantangan Baru</h3>
    <p>Siswa yang bergabung dan menyelesaikan target buku (terverifikasi) mendapat XP dan badge.</p>
    <form method="POST" action="{{ route('admin.challenges.store') }}">
        @csrf
        <div class="ch-fg">
            <div class="w2"><label class="tl-label">Judul *</label><input class="tl-input" name="title" value="{{ old('title') }}" required maxlength="150" placeholder="Contoh: Summer Reading Challenge"></div>
            <div class="w2"><label class="tl-label">Target kategori (opsional)</label><select class="tl-input" name="category_id"><option value="">Semua kategori</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
            <div class="w4"><label class="tl-label">Deskripsi</label><textarea class="tl-input" name="description" maxlength="1000" placeholder="Ceritakan tantangannya secara singkat">{{ old('description') }}</textarea></div>
            <div><label class="tl-label">Mulai *</label><input class="tl-input" type="date" name="starts_on" value="{{ old('starts_on', date('Y-m-d')) }}" required></div>
            <div><label class="tl-label">Selesai *</label><input class="tl-input" type="date" name="ends_on" value="{{ old('ends_on', date('Y-m-d', strtotime('+30 days'))) }}" required></div>
            <div><label class="tl-label">Target buku *</label><input class="tl-input" type="number" name="target_books" min="1" max="100" value="{{ old('target_books', 3) }}" required></div>
            <div><label class="tl-label">Reward XP *</label><input class="tl-input" type="number" name="reward_xp" min="0" max="1000" value="{{ old('reward_xp', 50) }}" required></div>
            <div class="w2"><label class="tl-label">Nama badge</label><input class="tl-input" name="badge_name" value="{{ old('badge_name') }}" maxlength="60" placeholder="Contoh: Summer Reader"></div>
        </div>
        <div style="display:flex;gap:.6rem;margin-top:1.1rem">
            <button class="tl-btn" type="submit">Terbitkan Tantangan</button>
            <button type="button" class="ah-btn soft" onclick="document.getElementById('chForm').classList.remove('open')">Batal</button>
        </div>
    </form>
</div>

<div class="ch-grid">
    @forelse($challenges as $c)
        @php
            $ended = $c->ends_on->copy()->endOfDay()->isPast();
            $total = max(1, (int) $c->starts_on->diffInDays($c->ends_on));
            $pct = $ended ? 100 : (int) max(0, min(100, round(today()->diffInDays($c->starts_on, false) * -1 / $total * 100)));
            $left = (int) today()->diffInDays($c->ends_on, false);
        @endphp
        <div class="ch-c" style="animation-delay: {{ min($loop->index, 8) * .05 }}s">
            <div class="ch-top">
                <span class="ch-pill {{ $c->status !== 'Active' ? 'off' : ($ended ? 'end' : 'on') }}">{{ $c->status !== 'Active' ? 'Ditutup' : ($ended ? 'Berakhir' : 'Aktif') }}</span>
                <small style="color:#8b87a8;font-size:.64rem">{{ $c->starts_on->format('d M') }} - {{ $c->ends_on->format('d M Y') }}</small>
            </div>
            <h4>{{ $c->title }}</h4>
            <p>{{ $c->description ?: 'Tanpa deskripsi.' }}</p>
            <div class="ch-tags">
                <span>{{ $c->target_books }} buku</span>
                <span>{{ $c->category->name ?? 'Semua kategori' }}</span>
                <span class="xp">{{ $c->reward_xp }} XP</span>
                @if($c->badge_name)<span class="bd">{{ $c->badge_name }}</span>@endif
            </div>
            <div class="ch-bar"><i style="width: {{ $pct }}%"></i></div>
            <div class="ch-meta"><span>Waktu berjalan</span><span>{{ $ended ? 'Sudah berakhir' : ($left <= 0 ? 'Berakhir hari ini' : $left . ' hari lagi') }}</span></div>
            <div class="ch-num">
                <div><b>{{ $c->participants_count }}</b><small>Peserta</small></div>
                <div><b>{{ $done[$c->id] ?? 0 }}</b><small>Menyelesaikan</small></div>
            </div>
            <div class="ch-act">
                <form method="POST" action="{{ route('admin.challenges.toggle', $c) }}">@csrf @method('PATCH')<button class="pg-btn" type="submit">{{ $c->status === 'Active' ? 'Tutup' : 'Aktifkan' }}</button></form>
                <form method="POST" action="{{ route('admin.challenges.destroy', $c) }}" onsubmit="return confirm('Hapus tantangan ini beserta pesertanya?')">@csrf @method('DELETE')<button class="tl-btn-danger" type="submit">Hapus</button></form>
            </div>
        </div>
    @empty
        <div class="ch-empty">Belum ada tantangan. Klik <b>+ Tantangan Baru</b> untuk membuat yang pertama.</div>
    @endforelse
</div>
</x-admin-layout>