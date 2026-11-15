@php
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $tabs = ['all' => 'Semua', 'unpaid' => 'Belum Dibayar', 'paid' => 'Sudah Dibayar'];
    $pct = ((int) $total) > 0 ? (int) round(((int) $paid) / ((int) $total) * 100) : 0;
@endphp
<x-admin-layout title="Denda">
<style>
.fd{--fd-card:rgba(255,255,255,.74);--fd-bd:rgba(124,92,255,.15);--fd-tx:#1f1a3d;--fd-mu:#7a7599;--fd-hover:rgba(124,92,255,.07);--fd-a:#7c5cff;--fd-b:#ff6fb5;--fd-ok:#16a37a;--fd-no:#e5484d;color:var(--fd-tx)}
.dark .fd,html.dark .fd,[data-theme="dark"] .fd{--fd-card:rgba(30,27,52,.72);--fd-bd:rgba(255,255,255,.1);--fd-tx:#f1eeff;--fd-mu:#a9a3c9;--fd-hover:rgba(255,255,255,.06)}
.fd *{box-sizing:border-box}
.fd-hd h2{font-size:1.9rem;font-weight:800;margin:0;letter-spacing:-.02em}
.fd-hd p{margin:.25rem 0 0;color:var(--fd-mu);font-size:.92rem}
.fd-sum{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin:1.3rem 0}
.fd-s{position:relative;overflow:hidden;background:var(--fd-card);border:1px solid var(--fd-bd);border-radius:22px;padding:1.1rem 1.2rem;backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);transition:transform .25s,box-shadow .25s}
.fd-s:hover{transform:translateY(-3px);box-shadow:0 14px 34px -14px rgba(124,92,255,.45)}
.fd-s::after{content:"";position:absolute;right:-36px;top:-36px;width:120px;height:120px;border-radius:50%;opacity:.16;background:var(--g)}
.fd-s.a{--g:linear-gradient(135deg,#7c5cff,#b18cff)}
.fd-s.b{--g:linear-gradient(135deg,#16a37a,#5ee0b5)}
.fd-s.d{--g:linear-gradient(135deg,#ff6fb5,#ff9a76)}
.fd-ic{width:42px;height:42px;border-radius:14px;display:grid;place-items:center;background:var(--g);color:#fff;box-shadow:0 8px 18px -8px rgba(0,0,0,.35)}
.fd-ic svg{width:21px;height:21px}
.fd-s small{display:block;margin-top:.8rem;color:var(--fd-mu);font-weight:600;font-size:.8rem}
.fd-s b{display:block;font-size:1.65rem;font-weight:800;letter-spacing:-.02em;margin-top:.1rem}
.fd-bar{height:7px;border-radius:99px;background:var(--fd-hover);margin-top:.7rem;overflow:hidden}
.fd-bar i{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,#16a37a,#5ee0b5);transition:width .8s cubic-bezier(.2,.8,.2,1)}
.fd-card{background:var(--fd-card);border:1px solid var(--fd-bd);border-radius:26px;padding:1.1rem;backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px)}
.fd-top{display:flex;flex-wrap:wrap;gap:.8rem;align-items:center;justify-content:space-between;margin-bottom:1rem}
.fd-tabs{position:relative;display:inline-flex;padding:5px;border-radius:99px;background:var(--fd-hover);border:1px solid var(--fd-bd);max-width:100%;overflow-x:auto}
.fd-pill{position:absolute;top:5px;left:0;height:calc(100% - 10px);width:0;border-radius:99px;background:linear-gradient(135deg,var(--fd-a),var(--fd-b));box-shadow:0 8px 20px -8px rgba(124,92,255,.7);transition:transform .35s cubic-bezier(.3,.9,.3,1),width .35s cubic-bezier(.3,.9,.3,1)}
.fd-tabs a{position:relative;z-index:1;padding:.5rem 1.05rem;border-radius:99px;font-weight:700;font-size:.88rem;color:var(--fd-mu);text-decoration:none;white-space:nowrap;cursor:pointer;transition:color .25s}
.fd-tabs a.on{color:#fff}
.fd-search{display:flex;align-items:center;gap:.5rem;padding:.5rem .9rem;border-radius:99px;background:var(--fd-hover);border:1px solid var(--fd-bd);min-width:220px}
.fd-search svg{width:17px;height:17px;color:var(--fd-mu);flex:none}
.fd-search input{border:0;outline:0;background:transparent;color:var(--fd-tx);font-size:.88rem;width:100%}
.fd-search:focus-within{border-color:var(--fd-a);box-shadow:0 0 0 3px rgba(124,92,255,.15)}
.fd-body{overflow-x:hidden}
.fx-panel{will-change:transform,opacity}
.fx-l{animation:fxl .3s cubic-bezier(.2,.8,.2,1)}
.fx-r{animation:fxr .3s cubic-bezier(.2,.8,.2,1)}
@keyframes fxl{from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:none}}
@keyframes fxr{from{opacity:0;transform:translateX(-30px)}to{opacity:1;transform:none}}
.fd-cols,.fd-row{display:grid;grid-template-columns:minmax(0,2.4fr) minmax(0,1fr) minmax(0,1fr) minmax(0,1.1fr) auto;gap:1rem;align-items:center}
.fd-cols{padding:.3rem 1rem .6rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--fd-mu)}
.fd-row{padding:.8rem 1rem;border-radius:18px;border:1px solid transparent;transition:background .2s,border-color .2s,transform .2s}
.fd-row+.fd-row{margin-top:.25rem}
.fd-row:hover{background:var(--fd-hover);border-color:var(--fd-bd);transform:translateX(3px)}
.fd-row[hidden]{display:none}
.fd-who{display:flex;align-items:center;gap:.75rem;min-width:0}
.fd-av{flex:none;width:42px;height:42px;border-radius:14px;display:grid;place-items:center;color:#fff;font-weight:800;background:linear-gradient(135deg,var(--fd-a),var(--fd-b))}
.fd-who b{display:block;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.fd-who span{display:block;font-size:.8rem;color:var(--fd-mu);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.fd-due{font-size:.88rem}
.fd-due em{display:block;font-style:normal;font-size:.75rem;color:var(--fd-no);font-weight:600}
.fd-amt{font-weight:800;font-size:1.02rem}
.fd-chip{display:inline-flex;align-items:center;gap:.4rem;padding:.3rem .75rem;border-radius:99px;font-size:.78rem;font-weight:700}
.fd-chip::before{content:"";width:7px;height:7px;border-radius:50%;background:currentColor}
.fd-chip.ok{color:var(--fd-ok);background:rgba(22,163,122,.12)}
.fd-chip.no{color:var(--fd-no);background:rgba(229,72,77,.12)}
.fd-act{text-align:right;min-width:128px}
.fd-btn{display:inline-flex;align-items:center;gap:.4rem;border:0;cursor:pointer;color:#fff;font-weight:700;font-size:.82rem;padding:.55rem .95rem;border-radius:99px;background:linear-gradient(135deg,var(--fd-a),var(--fd-b));box-shadow:0 8px 18px -8px rgba(124,92,255,.75);transition:transform .2s,box-shadow .2s}
.fd-btn:hover{transform:translateY(-2px);box-shadow:0 12px 22px -8px rgba(124,92,255,.85)}
.fd-btn:active{transform:scale(.96)}
.fd-btn svg{width:15px;height:15px}
.fd-pg{display:flex;gap:.6rem;justify-content:center;margin-top:1rem}
.fd-pg a{padding:.45rem 1rem;border-radius:99px;font-weight:700;font-size:.85rem;text-decoration:none;color:var(--fd-a);background:var(--fd-hover);border:1px solid var(--fd-bd)}
.fd-pg a:hover{background:var(--fd-a);color:#fff}
.fd-empty,.fd-nomatch{text-align:center;padding:3rem 1rem;color:var(--fd-mu)}
.fd-empty .fd-eic{width:74px;height:74px;margin:0 auto 1rem;border-radius:24px;display:grid;place-items:center;background:var(--fd-hover);border:1px dashed var(--fd-bd);color:var(--fd-a);animation:fdfloat 3s ease-in-out infinite}
.fd-eic svg{width:34px;height:34px}
.fd-empty p,.fd-nomatch{font-weight:600;margin:0}
@keyframes fdfloat{50%{transform:translateY(-6px)}}
@media(max-width:820px){
 .fd-sum{grid-template-columns:1fr}
 .fd-cols{display:none}
 .fd-row{grid-template-columns:1fr auto;gap:.5rem .8rem;border:1px solid var(--fd-bd);padding:.9rem}
 .fd-row+.fd-row{margin-top:.6rem}
 .fd-who{grid-column:1/-1}
 .fd-due{grid-column:1}
 .fd-amt{grid-column:2;grid-row:2;text-align:right}
 .fd-st{grid-column:1}
 .fd-act{grid-column:2;text-align:right}
 .fd-search{width:100%}
}
/* fd-glam: ngapung, kaca glasier, glow, sparkle */
.fd{position:relative;isolation:isolate}
.fd::before,.fd::after{content:"";position:absolute;inset:-30px;pointer-events:none;z-index:-1;filter:drop-shadow(0 0 5px #fff)}
.fd::before{background:
 radial-gradient(circle at 8% 14%,#fff 0 2px,transparent 3px),
 radial-gradient(circle at 30% 6%,#ffd6f0 0 1.5px,transparent 3px),
 radial-gradient(circle at 62% 10%,#fff 0 2px,transparent 3.5px),
 radial-gradient(circle at 91% 18%,#d9ccff 0 2px,transparent 3px),
 radial-gradient(circle at 96% 52%,#fff 0 1.5px,transparent 3px),
 radial-gradient(circle at 4% 62%,#ffd6f0 0 2px,transparent 3.5px),
 radial-gradient(circle at 18% 92%,#fff 0 2px,transparent 3px);animation:fdtw 3.4s ease-in-out infinite}
.fd::after{background:
 radial-gradient(circle at 20% 30%,#fff 0 1.5px,transparent 3px),
 radial-gradient(circle at 48% 3%,#d9ccff 0 2px,transparent 3.5px),
 radial-gradient(circle at 78% 4%,#ffd6f0 0 1.5px,transparent 3px),
 radial-gradient(circle at 99% 80%,#fff 0 2px,transparent 3px),
 radial-gradient(circle at 52% 98%,#d9ccff 0 2px,transparent 3.5px),
 radial-gradient(circle at 84% 94%,#fff 0 1.5px,transparent 3px),
 radial-gradient(circle at 2% 38%,#fff 0 2px,transparent 3px);animation:fdtw 4.6s ease-in-out -1.8s infinite}
@keyframes fdtw{0%,100%{opacity:.25;transform:scale(.98)}50%{opacity:1;transform:scale(1.02)}}
@keyframes fdbob{0%,100%{translate:0 0}50%{translate:0 -8px}}
@keyframes fdbob2{0%,100%{translate:0 0}50%{translate:0 -4px}}
@keyframes fdshine{0%{left:-70%}55%,100%{left:170%}}
@keyframes fdglow{0%,100%{box-shadow:0 8px 18px -8px rgba(124,92,255,.7),0 0 0 0 rgba(255,111,181,.0)}50%{box-shadow:0 8px 22px -6px rgba(124,92,255,.9),0 0 22px 2px rgba(255,111,181,.45)}}
.fd-s,.fd-card{
 background:linear-gradient(145deg,rgba(255,255,255,.82),rgba(255,255,255,.42));
 backdrop-filter:blur(24px) saturate(170%);-webkit-backdrop-filter:blur(24px) saturate(170%);
 border:1px solid rgba(255,255,255,.7);
 box-shadow:inset 0 1px 0 rgba(255,255,255,.95),inset 0 -1px 0 rgba(124,92,255,.12),0 18px 50px -18px rgba(124,92,255,.45),0 0 40px -12px rgba(255,111,181,.35)}
.dark .fd-s,.dark .fd-card,html.dark .fd-s,html.dark .fd-card,[data-theme="dark"] .fd-s,[data-theme="dark"] .fd-card{
 background:linear-gradient(145deg,rgba(60,52,104,.6),rgba(30,27,52,.4));border-color:rgba(255,255,255,.14);
 box-shadow:inset 0 1px 0 rgba(255,255,255,.18),0 18px 50px -18px rgba(0,0,0,.6),0 0 40px -12px rgba(124,92,255,.5)}
.fd-s{animation:fdbob 5.5s ease-in-out infinite}
.fd-s.b{animation-delay:-1.8s}.fd-s.d{animation-delay:-3.6s}
.fd-card{animation:fdbob2 8s ease-in-out infinite}
.fd-s:hover,.fd-card:hover{animation-play-state:paused}
.fd-s::before{content:"";position:absolute;top:0;left:-70%;width:38%;height:100%;pointer-events:none;
 background:linear-gradient(100deg,transparent,rgba(255,255,255,.6),transparent);transform:skewX(-20deg);animation:fdshine 7s ease-in-out infinite}
.fd-s.b::before{animation-delay:-2.3s}.fd-s.d::before{animation-delay:-4.6s}
.fd-ic{box-shadow:0 8px 20px -6px rgba(124,92,255,.7),0 0 22px rgba(255,111,181,.5),inset 0 1px 0 rgba(255,255,255,.5)}
.fd-pill{animation:fdglow 3s ease-in-out infinite}
.fd-btn{animation:fdglow 3.4s ease-in-out infinite}
.fd-row:hover{box-shadow:0 10px 30px -14px rgba(124,92,255,.55),inset 0 1px 0 rgba(255,255,255,.7)}
.fd-av{box-shadow:0 6px 16px -6px rgba(124,92,255,.7),0 0 14px rgba(255,111,181,.35)}
.fd-hd h2{background:linear-gradient(90deg,#7c5cff,#ff6fb5);-webkit-background-clip:text;background-clip:text;color:transparent}
@media(prefers-reduced-motion:reduce){.fd *,.fd::before,.fd::after{animation:none!important}}
</style>
<script>
window.finTabs = function (start) {
  return {
    cur: start,
    q: '',
    order: ['all', 'unpaid', 'paid'],
    cache: {},
    init() {
      this.cache[start] = this.$refs.panel.innerHTML;
      this.order.filter(k => k !== start).forEach(k => this.load(k));
      this.$nextTick(() => this.movePill());
      window.addEventListener('resize', () => this.movePill());
      this.$watch('q', () => this.filter());
      let x0 = null;
      const b = this.$refs.body;
      b.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
      b.addEventListener('touchend', e => {
        if (x0 === null) return;
        const dx = e.changedTouches[0].clientX - x0;
        x0 = null;
        if (Math.abs(dx) < 60) return;
        const n = this.order[this.order.indexOf(this.cur) + (dx < 0 ? 1 : -1)];
        if (n) this.go(n);
      }, { passive: true });
    },
    movePill() {
      const el = this.$refs.tabs.querySelector('[data-k="' + this.cur + '"]');
      const pill = this.$refs.pill;
      if (!el || !pill) return;
      pill.style.width = el.offsetWidth + 'px';
      pill.style.transform = 'translateX(' + el.offsetLeft + 'px)';
    },
    filter() {
      const q = this.q.trim().toLowerCase();
      const p = this.$refs.panel;
      let shown = 0, total = 0;
      p.querySelectorAll('.fd-row').forEach(r => {
        total++;
        const hide = q && !(r.dataset.q || '').includes(q);
        r.hidden = !!hide;
        if (!hide) shown++;
      });
      const nm = p.querySelector('.fd-nomatch');
      if (nm) nm.hidden = !(total > 0 && shown === 0);
    },
    async load(k) {
      if (this.cache[k] !== undefined) return;
      try {
        const r = await fetch(location.pathname + '?s=' + k, { credentials: 'same-origin' });
        const d = new DOMParser().parseFromString(await r.text(), 'text/html');
        const p = d.querySelector('.fx-panel');
        if (p) this.cache[k] = p.innerHTML;
      } catch (e) {}
    },
    async go(k) {
      if (k === this.cur) return;
      const dir = this.order.indexOf(k) > this.order.indexOf(this.cur) ? 'fx-l' : 'fx-r';
      if (this.cache[k] === undefined) {
        await this.load(k);
        if (this.cache[k] === undefined) { location.href = location.pathname + '?s=' + k; return; }
      }
      this.cur = k;
      this.movePill();
      const p = this.$refs.panel;
      p.innerHTML = this.cache[k];
      p.classList.remove('fx-l', 'fx-r');
      void p.offsetWidth;
      p.classList.add(dir);
      this.filter();
      history.replaceState(null, '', '?s=' + k);
    }
  };
};
</script>
<div class="fd">
    <div class="fd-hd"><h2>Denda</h2><p>Daftar denda keterlambatan pengembalian buku.</p></div>

    <div class="fd-sum">
        <div class="fd-s a">
            <div class="fd-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M14.5 9.2c-.6-.8-1.5-1.2-2.5-1.2-1.4 0-2.5.8-2.5 2s1 1.7 2.5 2 2.5.8 2.5 2-1.1 2-2.5 2c-1 0-1.9-.4-2.5-1.2M12 6.5V8m0 8v1.5"/></svg></div>
            <small>Total Denda</small><b>{{ $rp($total) }}</b>
            <div class="fd-bar"><i style="width:{{ $pct }}%"></i></div>
            <small style="margin-top:.4rem">{{ $pct }}% sudah lunas</small>
        </div>
        <div class="fd-s b">
            <div class="fd-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></div>
            <small>Sudah Dibayar</small><b>{{ $rp($paid) }}</b>
        </div>
        <div class="fd-s d">
            <div class="fd-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
            <small>Belum Dibayar</small><b>{{ $rp($unpaid) }}</b>
        </div>
    </div>

    <div class="fd-card" x-data="finTabs('{{ $s }}')">
        <div class="fd-top">
            <div class="fd-tabs" x-ref="tabs">
                <span class="fd-pill" x-ref="pill"></span>
                @foreach($tabs as $k => $v)<a href="{{ url()->current() }}?s={{ $k }}" data-k="{{ $k }}" :class="{ 'on': cur === '{{ $k }}' }" @click.prevent="go('{{ $k }}')">{{ $v }}</a>@endforeach
            </div>
            <label class="fd-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" x-model="q" placeholder="Cari siswa atau buku...">
            </label>
        </div>

        <div x-ref="body" class="fd-body">
        <div x-ref="panel" class="fx-panel">
        @if($rows->count())
            <div class="fd-cols"><span>Siswa &amp; Buku</span><span>Jatuh Tempo</span><span>Denda</span><span>Status</span><span></span></div>
            @foreach($rows as $l)
                @php
                    $nm = $l->user->name ?? '-';
                    $bk = $l->book->title ?? '-';
                    $late = (int) ($l->late_days ?? 0);
                @endphp
                <div class="fd-row" data-q="{{ mb_strtolower($nm . ' ' . $bk) }}">
                    <div class="fd-who">
                        <div class="fd-av">{{ mb_strtoupper(mb_substr($nm, 0, 1)) }}</div>
                        <div style="min-width:0"><b>{{ $nm }}</b><span>{{ $bk }}</span></div>
                    </div>
                    <div class="fd-due">{{ $l->due_date ? \Carbon\Carbon::parse($l->due_date)->format('d M Y') : '-' }}@if($late > 0)<em>Terlambat {{ $late }} hari</em>@endif</div>
                    <div class="fd-amt">{{ $rp($l->fine_amount) }}</div>
                    <div class="fd-st">@if($l->fine_paid_at)<span class="fd-chip ok">Lunas</span>@else<span class="fd-chip no">Belum dibayar</span>@endif</div>
                    <div class="fd-act">
                        @if(!$l->fine_paid_at)
                        <form method="POST" action="{{ url()->current() }}/{{ $l->id }}/lunas" onsubmit="return confirm('Tandai denda ini sudah lunas?')">@csrf<button type="submit" class="fd-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Tandai Lunas</button></form>
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="fd-nomatch" hidden>Tidak ada hasil yang cocok dengan pencarianmu.</div>
            <div class="fd-pg">@if($rows->previousPageUrl())<a href="{{ $rows->previousPageUrl() }}">&larr; Sebelumnya</a>@endif @if($rows->nextPageUrl())<a href="{{ $rows->nextPageUrl() }}">Berikutnya &rarr;</a>@endif</div>
        @else
            <div class="fd-empty">
                <div class="fd-eic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12v8H4v-8"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 1 1 0-5C11 2 12 7 12 7Z"/><path d="M12 7h4.5a2.5 2.5 0 1 0 0-5C13 2 12 7 12 7Z"/></svg></div>
                <p>Tidak ada denda pada kategori ini.</p>
            </div>
        @endif
        </div>
        </div>
    </div>
</div>
</x-admin-layout>