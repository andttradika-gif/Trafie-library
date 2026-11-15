@php
    use App\Models\Announcement;
    use App\Models\Loan;
    use App\Models\Reservation;
    use Carbon\Carbon;

    $raw = (string) request('m');
    try { $cur = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $raw) ? Carbon::createFromFormat('Y-m-d', $raw . '-01')->startOfDay() : now()->startOfMonth(); }
    catch (\Throwable $e) { $cur = now()->startOfMonth(); }
    $start = $cur->copy()->startOfMonth();
    $end = $cur->copy()->endOfMonth();

    $ev = [];
    $add = function ($d, $type, $text) use (&$ev) { $ev[Carbon::parse($d)->format('Y-m-d')][] = ['t' => $type, 'x' => $text]; };
    foreach (Loan::with(['user', 'book'])->where('status', 'Borrowed')->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])->get() as $l) {
        $add($l->due_date, $l->due_date->lt(today()) ? 'late' : 'due', ($l->book->title ?? 'Buku') . ' - ' . ($l->user->name ?? '-'));
    }
    foreach (Loan::with(['user', 'book'])->where('status', 'Returned')->whereBetween('returned_at', [$start, $end->copy()->endOfDay()])->get() as $l) {
        $add($l->returned_at, 'ret', ($l->book->title ?? 'Buku') . ' - ' . ($l->user->name ?? '-'));
    }
    foreach (Reservation::with(['user', 'book'])->where('status', 'Ready')->whereBetween('expires_at', [$start, $end->copy()->endOfDay()])->get() as $r) {
        $add($r->expires_at, 'res', ($r->book->title ?? 'Buku') . ' - ' . ($r->user->name ?? '-'));
    }
    foreach (Announcement::where('type', 'event')->where('is_active', true)->whereBetween('ends_on', [$start->toDateString(), $end->toDateString()])->get() as $a) {
        $add($a->ends_on, 'event', $a->title);
    }
    ksort($ev);

    $labels = ['due' => 'Jatuh tempo', 'late' => 'Terlambat', 'ret' => 'Dikembalikan', 'res' => 'Reservasi kedaluwarsa', 'event' => 'Acara'];
    $tot = array_fill_keys(array_keys($labels), 0);
    foreach ($ev as $list) { foreach ($list as $e) { $tot[$e['t']]++; } }
    $offset = $start->dayOfWeekIso - 1;
    $days = $start->daysInMonth;
    $weeks = (int) ceil(($offset + $days) / 7);
    $bln = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$start->month - 1] . ' ' . $start->year;
    $prev = route('admin.extra.calendar', ['m' => $start->copy()->subMonth()->format('Y-m')]);
    $next = route('admin.extra.calendar', ['m' => $start->copy()->addMonth()->format('Y-m')]);
    $now = route('admin.extra.calendar');
@endphp
<x-admin-layout title="Kalender">
<div class="clx-head"><x-admin-head title="Kalender" sub="Jatuh tempo, keterlambatan, pengembalian, reservasi, dan acara perpustakaan." icon="cal">
    <a class="ah-btn soft" href="{{ $prev }}">&larr;</a>
    <span style="font:600 .9rem 'Playfair Display',serif;color:#2d2a4a;min-width:130px;text-align:center">{{ $bln }}</span>
    <a class="ah-btn soft" href="{{ $next }}">&rarr;</a>
    <a class="ah-btn" href="{{ $now }}">Hari ini</a>
</x-admin-head></div>
<style>
.clx{--c-card:linear-gradient(145deg,rgba(255,255,255,.86),rgba(247,238,255,.5));--c-bd:rgba(255,255,255,.75);--c-tx:#2d2a4a;--c-mu:#8b87a8;--c-cell:linear-gradient(160deg,rgba(255,255,255,.88),rgba(255,255,255,.5));--c-a:#7c5cff;--c-b:#ff6fb5;position:relative;isolation:isolate;font-family:'Poppins',sans-serif;color:var(--c-tx)}
.dark .clx,html.dark .clx,[data-theme="dark"] .clx{--c-card:linear-gradient(145deg,rgba(60,52,104,.6),rgba(30,27,52,.4));--c-bd:rgba(255,255,255,.14);--c-tx:#f1eeff;--c-mu:#a9a3c9;--c-cell:linear-gradient(160deg,rgba(70,62,120,.55),rgba(40,36,72,.35))}
.clx::before,.clx::after{content:"";position:absolute;inset:-30px;pointer-events:none;z-index:-1;filter:drop-shadow(0 0 5px #fff)}
.clx::before{background:
 radial-gradient(circle at 6% 10%,#fff 0 2px,transparent 3px),
 radial-gradient(circle at 28% 4%,#ffd6f0 0 1.5px,transparent 3px),
 radial-gradient(circle at 58% 8%,#fff 0 2px,transparent 3.5px),
 radial-gradient(circle at 93% 14%,#d9ccff 0 2px,transparent 3px),
 radial-gradient(circle at 97% 46%,#fff 0 1.5px,transparent 3px),
 radial-gradient(circle at 3% 55%,#ffd6f0 0 2px,transparent 3.5px),
 radial-gradient(circle at 15% 94%,#fff 0 2px,transparent 3px);animation:cltw 3.4s ease-in-out infinite}
.clx::after{background:
 radial-gradient(circle at 22% 28%,#fff 0 1.5px,transparent 3px),
 radial-gradient(circle at 46% 3%,#d9ccff 0 2px,transparent 3.5px),
 radial-gradient(circle at 80% 5%,#ffd6f0 0 1.5px,transparent 3px),
 radial-gradient(circle at 99% 78%,#fff 0 2px,transparent 3px),
 radial-gradient(circle at 50% 98%,#d9ccff 0 2px,transparent 3.5px),
 radial-gradient(circle at 85% 95%,#fff 0 1.5px,transparent 3px),
 radial-gradient(circle at 2% 36%,#fff 0 2px,transparent 3px);animation:cltw 4.6s ease-in-out -1.8s infinite}
@keyframes cltw{0%,100%{opacity:.25;transform:scale(.98)}50%{opacity:1;transform:scale(1.02)}}
@keyframes clbob{0%,100%{translate:0 0}50%{translate:0 -6px}}
@keyframes clbob2{0%,100%{translate:0 0}50%{translate:0 -3px}}
@keyframes clshine{0%{left:-70%}55%,100%{left:170%}}
@keyframes clpulse{0%,100%{box-shadow:0 10px 26px -8px rgba(124,92,255,.6),0 0 0 0 rgba(255,111,181,0)}50%{box-shadow:0 14px 32px -6px rgba(124,92,255,.8),0 0 30px 4px rgba(255,111,181,.55)}}
@keyframes clspin{to{transform:rotate(360deg)}}

.cl{display:grid;gap:1.2rem;grid-template-columns:minmax(0,1fr) 300px;align-items:start}
.cl-card{position:relative;overflow:hidden;padding:1.2rem;border-radius:28px;background:var(--c-card);border:1px solid var(--c-bd);
 backdrop-filter:blur(24px) saturate(170%);-webkit-backdrop-filter:blur(24px) saturate(170%);
 box-shadow:inset 0 1px 0 rgba(255,255,255,.9),inset 0 -1px 0 rgba(124,92,255,.12),0 20px 50px -18px rgba(124,92,255,.45),0 0 44px -12px rgba(255,111,181,.35);animation:clbob2 8s ease-in-out infinite}
.cl-card::before{content:"";position:absolute;top:0;left:-70%;width:35%;height:100%;pointer-events:none;background:linear-gradient(100deg,transparent,rgba(255,255,255,.45),transparent);transform:skewX(-20deg);animation:clshine 9s ease-in-out infinite}
.cl-side .cl-card:nth-child(2){animation-delay:-3s}
.cl-side .cl-card:nth-child(2)::before{animation-delay:-4s}
.cl-grid{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:.5rem;position:relative}
.cl-h{padding:.3rem 0 .6rem;text-align:center;font-size:.64rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--c-a);opacity:.75}
.cl-d{position:relative;min-height:104px;padding:.5rem;border-radius:18px;background:var(--c-cell);border:1px solid var(--c-bd);
 box-shadow:inset 0 1px 0 rgba(255,255,255,.85),0 8px 18px -10px rgba(124,92,255,.35);transition:transform .25s ease,box-shadow .25s ease,border-color .25s;animation:clbob 6s ease-in-out infinite}
.cl-d:nth-child(7n+9){animation-delay:-.8s}.cl-d:nth-child(7n+10){animation-delay:-1.6s}.cl-d:nth-child(7n+11){animation-delay:-2.4s}
.cl-d:nth-child(7n+12){animation-delay:-3.2s}.cl-d:nth-child(7n+13){animation-delay:-4s}.cl-d:nth-child(7n+14){animation-delay:-4.8s}.cl-d:nth-child(7n+8){animation-delay:-5.6s}
.cl-d:hover{transform:translateY(-5px) scale(1.04);z-index:3;animation-play-state:paused;border-color:rgba(124,92,255,.45);box-shadow:inset 0 1px 0 #fff,0 18px 34px -10px rgba(124,92,255,.6),0 0 26px rgba(255,111,181,.35)}
.cl-d.empty{background:transparent;border:0;box-shadow:none;animation:none;backdrop-filter:none}
.cl-d.empty:hover{transform:none;box-shadow:none}
.cl-d.has{border-color:rgba(124,92,255,.28)}
.cl-d.today{background:linear-gradient(145deg,#e0d2ff,#ffd3e8);border-color:#fff;animation:clpulse 2.6s ease-in-out infinite}
.cl-d.today::after{content:"✦";position:absolute;top:.4rem;right:.55rem;font-size:.7rem;color:#fff;text-shadow:0 0 8px #ff6fb5,0 0 14px #7c5cff;animation:cltw 1.8s ease-in-out infinite}
.cl-n{font-size:.78rem;font-weight:800;color:var(--c-tx);margin-bottom:.3rem}
.cl-d.today .cl-n{color:#fff;display:inline-grid;place-items:center;min-width:26px;height:26px;padding:0 .35rem;border-radius:99px;background:linear-gradient(135deg,var(--c-a),var(--c-b));box-shadow:0 6px 14px -4px rgba(124,92,255,.8),0 0 14px rgba(255,111,181,.6)}
.cl-e{display:block;margin-bottom:3px;padding:3px 7px;border-radius:99px;font-size:.58rem;font-weight:700;line-height:1.35;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;box-shadow:0 4px 10px -4px currentColor;transition:transform .2s}
.cl-e:hover{transform:translateX(2px)}
.cl-more{font-size:.58rem;font-weight:700;color:var(--c-a);padding-left:5px}
.t-due{background:#fbdbe8;color:#b13a72}.t-late{background:#fecdd3;color:#b4233f}.t-ret{background:#d8f0e2;color:#2f6b4f}.t-res{background:#d9e9fa;color:#2f5f99}.t-event{background:#e6defa;color:#5b34a8}
.cl-side{display:flex;flex-direction:column;gap:1.2rem}
.cl-side h3{margin:0 0 .8rem;font-family:'Playfair Display',serif;font-size:1.05rem;color:var(--c-tx)}
.cl-lg{display:grid;gap:.55rem}
.cl-lg div{display:flex;align-items:center;gap:.6rem;font-size:.76rem;color:var(--c-mu);font-weight:500}
.cl-lg i{width:13px;height:13px;border-radius:5px;box-shadow:0 0 10px currentColor}
.cl-lg em{margin-left:auto;font-style:normal;font-weight:800;color:var(--c-tx);padding:.05rem .55rem;border-radius:99px;background:rgba(124,92,255,.1)}
.cl-ag{max-height:360px;overflow-y:auto;scrollbar-width:thin}
.cl-ag b{display:block;margin:.8rem 0 .3rem;font-size:.66rem;letter-spacing:.07em;text-transform:uppercase;color:var(--c-a)}
.cl-ag b:first-child{margin-top:0}
.cl-it{display:flex;align-items:flex-start;gap:.55rem;padding:.35rem .5rem;margin:0 -.5rem;border-radius:12px;font-size:.74rem;color:var(--c-tx);line-height:1.4;transition:background .2s}
.cl-it:hover{background:rgba(124,92,255,.08)}
.cl-it i{flex:0 0 9px;width:9px;height:9px;margin-top:.35rem;border-radius:50%;box-shadow:0 0 8px currentColor}
.cl-none{font-size:.76rem;color:var(--c-mu);padding:.6rem 0}
.clx .ah-btn{box-shadow:0 8px 20px -8px rgba(124,92,255,.7),0 0 18px rgba(255,111,181,.3)}
@media(max-width:1100px){.cl{grid-template-columns:1fr}.cl-d{min-height:72px}.cl-e{font-size:0;padding:0;height:6px;margin-bottom:3px}}
@media(max-width:560px){.cl-card{padding:.7rem;border-radius:22px}.cl-grid{gap:.3rem}.cl-d{min-height:58px;padding:.35rem;border-radius:12px}.cl-n{font-size:.68rem}}
@media(prefers-reduced-motion:reduce){.clx *,.clx::before,.clx::after{animation:none!important}}
/* clx-megah */
@property --ang{syntax:'<angle>';initial-value:0deg;inherits:false}
@keyframes clang{to{--ang:360deg}}
@keyframes claur{to{transform:translate(70px,50px) scale(1.25)}}
@keyframes clrise{0%{transform:translateY(0) rotate(0) scale(.6);opacity:0}15%{opacity:1}85%{opacity:.9}100%{transform:translateY(-120vh) rotate(200deg) scale(1.1);opacity:0}}
@keyframes clchip{0%,100%{box-shadow:0 4px 10px -4px currentColor}50%{box-shadow:0 0 14px 1px currentColor}}
.clx-sky{position:absolute;inset:-80px -40px;z-index:-1;pointer-events:none;overflow:hidden;border-radius:60px}
.clx-aur{position:absolute;display:block;width:520px;height:520px;border-radius:50%;filter:blur(70px);opacity:.5;animation:claur 14s ease-in-out infinite alternate}
.clx-aur.a1{background:#b9a4ff;top:-120px;left:-80px}
.clx-aur.a2{background:#ffb3da;top:30%;right:-120px;animation-delay:-5s}
.clx-aur.a3{background:#9fe3ff;bottom:-160px;left:30%;animation-delay:-9s}
.clx-sp{position:absolute;bottom:-20px;opacity:0;line-height:1;filter:drop-shadow(0 0 5px currentColor) drop-shadow(0 0 10px #fff);animation:clrise linear infinite}
.cl-card::after,.clx-head>*::after,.cl-d.today::before{content:"";position:absolute;inset:0;border-radius:inherit;padding:2px;pointer-events:none;
 background:conic-gradient(from var(--ang),#ff6fb5,#ffd166,#7dd3fc,#a78bfa,#ff6fb5);
 -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask:linear-gradient(#000 0 0) content-box exclude,linear-gradient(#000 0 0);
 animation:clang 6s linear infinite}
.clx-head>*{position:relative;animation:clbob2 8s ease-in-out infinite;backdrop-filter:blur(24px) saturate(170%);-webkit-backdrop-filter:blur(24px) saturate(170%);
 box-shadow:inset 0 1px 0 rgba(255,255,255,.9),0 20px 50px -18px rgba(124,92,255,.5),0 0 50px -10px rgba(255,111,181,.45)}
.cl-d.today{transform:scale(1.07);z-index:2;box-shadow:0 0 0 4px rgba(255,255,255,.55),0 0 36px 6px rgba(255,111,181,.55),0 0 60px 10px rgba(124,92,255,.35)}
.cl-d.today:hover{transform:scale(1.1)}
.cl-d.today::before{padding:3px;animation-duration:3s}
.cl-d.has{background:linear-gradient(160deg,rgba(255,255,255,.95),rgba(236,224,255,.65))}
.cl-e{animation:clchip 3s ease-in-out infinite}
.cl-h{text-shadow:0 0 12px rgba(124,92,255,.5);opacity:1}
.cl-side h3{background:linear-gradient(90deg,#7c5cff,#ff6fb5);-webkit-background-clip:text;background-clip:text;color:transparent}
.cl-ag{overflow-x:auto}
.cl-it{margin:0 -.5rem}
@media(prefers-reduced-motion:reduce){.clx-sky,.clx-head>*::after,.cl-card::after{display:none}}
</style>
<div class="clx">
<div class="cl">
    <div class="cl-card">
        <div class="cl-grid">
            @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $h)<div class="cl-h">{{ $h }}</div>@endforeach
            @for($i = 0; $i < $weeks * 7; $i++)
                @php $n = $i - $offset + 1; @endphp
                @if($n < 1 || $n > $days)
                    <div class="cl-d empty"></div>
                @else
                    @php $key = $start->copy()->day($n)->format('Y-m-d'); $list = $ev[$key] ?? []; @endphp
                    <div class="cl-d {{ $key === today()->format('Y-m-d') ? 'today' : '' }} {{ count($list) ? 'has' : '' }}">
                        <div class="cl-n">{{ $n }}</div>
                        @foreach(array_slice($list, 0, 3) as $e)<span class="cl-e t-{{ $e['t'] }}" title="{{ $labels[$e['t']] }}: {{ $e['x'] }}">{{ $e['x'] }}</span>@endforeach
                        @if(count($list) > 3)<div class="cl-more">+{{ count($list) - 3 }} lagi</div>@endif
                    </div>
                @endif
            @endfor
        </div>
    </div>
    <div class="cl-side">
        <div class="cl-card">
            <h3>Keterangan</h3>
            <div class="cl-lg">
                @foreach($labels as $k => $lb)<div><i class="t-{{ $k }}"></i>{{ $lb }}<em>{{ $tot[$k] }}</em></div>@endforeach
            </div>
        </div>
        <div class="cl-card">
            <h3>Agenda {{ $bln }}</h3>
            <div class="cl-ag">
                @forelse($ev as $date => $list)
                    <b>{{ Carbon::parse($date)->locale('id')->translatedFormat('l, d M') }}</b>
                    @foreach($list as $e)<div class="cl-it"><i class="t-{{ $e['t'] }}"></i><span>{{ $e['x'] }}</span></div>@endforeach
                @empty
                    <div class="cl-none">Belum ada agenda di bulan ini.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>
<script>
(function(){var r=document.querySelector('.clx');if(!r||r.querySelector('.clx-sky'))return;
var s=document.createElement('div');s.className='clx-sky';
s.innerHTML='<i class="clx-aur a1"></i><i class="clx-aur a2"></i><i class="clx-aur a3"></i>';
var c=['#a78bfa','#ff7fbf','#7dd3fc','#fbbf24','#c084fc'];
var n=window.innerWidth<700?14:32;
for(var i=0;i<n;i++){var e=document.createElement('span');e.className='clx-sp';
e.textContent=Math.random()>.5?'\u2726':'\u2727';
e.style.left=(Math.random()*100)+'%';e.style.fontSize=(8+Math.random()*16)+'px';e.style.color=c[i%c.length];
e.style.animationDuration=(7+Math.random()*9)+'s';e.style.animationDelay=(-Math.random()*14)+'s';s.appendChild(e);}
r.prepend(s);})();
</script>
</x-admin-layout>