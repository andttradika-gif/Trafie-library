@php use App\Models\Loan; @endphp
<x-student-layout title="Pembayaran Denda">
    <div class="pg-stats">
        <div class="pg-stat"><small>Denda Belum Dibayar</small><b>{{ Loan::rupiah($totals['unpaid']) }}</b></div>
        <div class="pg-stat"><small>Denda Berjalan (belum dikembalikan)</small><b>{{ Loan::rupiah($totals['running']) }}</b></div>
        <div class="pg-stat"><small>Sudah Dibayar</small><b>{{ Loan::rupiah($totals['paid']) }}</b></div>
    </div>

    <div class="tl-card" style="margin-bottom:1.1rem">
        <h3 class="pg-h">Cara Membayar Denda</h3>
        <p class="pg-sub">Denda dihitung Rp {{ number_format((int) config('library.fine_per_day'), 0, ',', '.') }} per hari keterlambatan dan baru lunas setelah diverifikasi admin.</p>
        <div class="pg-methods">
            <div class="pg-method"><h4>Tunai</h4><p class="pg-sub" style="margin:0">{{ $set['cash_note'] }}</p></div>
            <div class="pg-method"><h4>E-Wallet</h4>
                @if($set['ewallet_number'])<p class="pg-sub" style="margin:0"><b>{{ $set['ewallet_provider'] }}</b> {{ $set['ewallet_number'] }}<br>a.n. {{ $set['ewallet_holder'] ?: '-' }}</p>@else<p class="pg-sub" style="margin:0">Belum diatur admin.</p>@endif
            </div>
            <div class="pg-method"><h4>QRIS</h4>
                @if($set['qris_image'])<a href="{{ asset('storage/' . $set['qris_image']) }}" target="_blank" title="Klik untuk memperbesar"><img src="{{ asset('storage/' . $set['qris_image']) }}" alt="QRIS" style="width:100%;max-width:170px;aspect-ratio:1;object-fit:contain;display:block;margin:.4rem 0;padding:6px;border-radius:16px;background:#fff;border:1px solid rgba(154,128,238,.3);box-shadow:0 10px 24px -10px rgba(124,92,255,.5)"></a>@if(!empty($set['qris_note']))<p class="pg-sub" style="margin:0">{{ $set['qris_note'] }}</p>@endif<p class="pg-sub" style="margin:.2rem 0 0">Scan dengan aplikasi bank atau e-wallet.</p>@else<p class="pg-sub" style="margin:0">Belum diatur admin.</p>@endif
            </div>
        </div>
    </div>

    <div class="tl-card" style="margin-bottom:1.1rem;overflow-x:auto">
        <h3 class="pg-h">Denda yang Harus Dibayar</h3>
        <table class="tl-table">
            <thead><tr><th>Buku</th><th>Terlambat</th><th>Denda</th><th>Status</th><th style="width:140px">Aksi</th></tr></thead>
            <tbody>
            @forelse($unpaid as $l)
                <tr>
                    <td><b>{{ $l->book->title ?? '-' }}</b><br><small>Dikembalikan {{ $l->returned_at ? $l->returned_at->format('d M Y') : '-' }}</small></td>
                    <td>{{ $l->late_days }} hari</td>
                    <td><b>{{ Loan::rupiah($l->fine_amount) }}</b></td>
                    <td>@if(in_array($l->id, $pendingIds))<span class="pg-b pg-b-pending">Menunggu verifikasi</span>@else<span class="pg-b pg-b-waiting">Belum dibayar</span>@endif</td>
                    <td><a class="pg-btn" href="{{ route('student.fines.pay', $l) }}" style="text-decoration:none;display:inline-block">{{ in_array($l->id, $pendingIds) ? 'Lihat' : 'Bayar' }}</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="tl-empty">Tidak ada denda yang harus dibayar. Terima kasih sudah tepat waktu.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($running->isNotEmpty())
    <div class="tl-card" style="margin-bottom:1.1rem;overflow-x:auto">
        <h3 class="pg-h">Denda Berjalan</h3>
        <p class="pg-sub">Bukunya belum dikembalikan. Dendanya terus bertambah tiap hari sampai kamu mengembalikannya.</p>
        <table class="tl-table">
            <thead><tr><th>Buku</th><th>Jatuh Tempo</th><th>Terlambat</th><th>Denda Sementara</th></tr></thead>
            <tbody>@foreach($running as $l)<tr><td>{{ $l->book->title ?? '-' }}</td><td>{{ $l->due_date->format('d M Y') }}</td><td>{{ $l->late_days }} hari</td><td><b>{{ Loan::rupiah($l->current_fine) }}</b></td></tr>@endforeach</tbody>
        </table>
    </div>
    @endif

    <div class="tl-card" style="overflow-x:auto">
        <h3 class="pg-h">Riwayat Pembayaran</h3>
        <table class="tl-table">
            <thead><tr><th>Buku</th><th>Nominal</th><th>Metode</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
            @forelse($payments as $p)
                <tr>
                    <td>{{ $p->loan->book->title ?? '-' }}</td><td>{{ Loan::rupiah($p->amount) }}</td><td>{{ $p->method_label }}</td>
                    <td><span class="pg-b pg-b-{{ strtolower($p->status) }}">{{ $p->label }}</span>@if($p->reject_reason)<br><small>{{ $p->reject_reason }}</small>@endif</td>
                    <td>{{ $p->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="tl-empty">Belum ada riwayat pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
<style>
/* fn-megah */
@property --fa{syntax:'<angle>';initial-value:0deg;inherits:false}
@keyframes fnspin{to{--fa:360deg}}
@keyframes fnaur{to{transform:translate(60px,40px) scale(1.2)}}
@keyframes fnrise{0%{transform:translateY(0) rotate(0) scale(.6);opacity:0}15%{opacity:1}85%{opacity:.9}100%{transform:translateY(-115vh) rotate(180deg) scale(1.1);opacity:0}}
@keyframes fnbob{0%,100%{translate:0 0}50%{translate:0 -5px}}
@keyframes fnshine{0%{left:-70%}55%,100%{left:170%}}
@keyframes fnglow{0%,100%{box-shadow:0 10px 26px -10px rgba(124,92,255,.55),0 0 0 0 rgba(255,111,181,0)}50%{box-shadow:0 14px 32px -8px rgba(124,92,255,.7),0 0 30px 2px rgba(255,111,181,.4)}}
.fn-sky{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
.fn-aur{position:absolute;width:480px;height:480px;border-radius:50%;filter:blur(80px);opacity:.38;animation:fnaur 16s ease-in-out infinite alternate}
.fn-aur.a1{background:#b9a4ff;top:-100px;left:10%}
.fn-aur.a2{background:#ffb3da;top:35%;right:-100px;animation-delay:-6s}
.fn-aur.a3{background:#9fe3ff;bottom:-160px;left:35%;animation-delay:-10s}
.fn-sp{position:absolute;bottom:-20px;opacity:0;line-height:1;filter:drop-shadow(0 0 5px currentColor) drop-shadow(0 0 9px #fff);animation:fnrise linear infinite}
.tl-card,.pg-stats>*,.pg-stat{position:relative;overflow:hidden;
 background:linear-gradient(150deg,rgba(255,255,255,.9),rgba(247,238,255,.55)) !important;
 backdrop-filter:blur(22px) saturate(165%);-webkit-backdrop-filter:blur(22px) saturate(165%);
 border:1px solid rgba(255,255,255,.85) !important;
 box-shadow:inset 0 1px 0 #fff,inset 0 -1px 0 rgba(124,92,255,.1),0 20px 48px -20px rgba(124,92,255,.45),0 0 40px -14px rgba(255,111,181,.35) !important}
.tl-card::before,.pg-stats>*::before,.pg-stat::before{content:"";position:absolute;top:0;left:-70%;width:34%;height:100%;pointer-events:none;
 background:linear-gradient(100deg,transparent,rgba(255,255,255,.55),transparent);transform:skewX(-20deg);animation:fnshine 9s ease-in-out infinite}
.tl-card::after,.pg-stats>*::after,.pg-stat::after{content:"";position:absolute;inset:0;border-radius:inherit;padding:1.5px;pointer-events:none;
 background:conic-gradient(from var(--fa),#ff9fcf,#ffe29a,#a5e3ff,#c4b0ff,#ff9fcf);
 -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask:linear-gradient(#000 0 0) content-box exclude,linear-gradient(#000 0 0);
 opacity:.75;animation:fnspin 8s linear infinite}
.pg-stats>*,.pg-stat{animation:fnbob 6s ease-in-out infinite}
.pg-stats>*:nth-child(2){animation-delay:-2s}.pg-stats>*:nth-child(3){animation-delay:-4s}
.pg-stats>*:hover,.pg-stat:hover{transform:translateY(-5px)}
.pg-stats b,.pg-stat b{background:linear-gradient(90deg,#7c5cff,#ff6fb5);-webkit-background-clip:text;background-clip:text;color:transparent}
.pg-method{position:relative;display:flex;flex-direction:column;align-items:flex-start;border-radius:22px !important;
 background:linear-gradient(160deg,rgba(255,255,255,.95),rgba(240,230,255,.55)) !important;
 border:1px solid rgba(255,255,255,.9) !important;
 box-shadow:inset 0 1px 0 #fff,0 14px 30px -16px rgba(124,92,255,.5) !important;transition:transform .3s ease,box-shadow .3s ease}
.pg-method:hover{transform:translateY(-6px);box-shadow:inset 0 1px 0 #fff,0 22px 40px -14px rgba(124,92,255,.6),0 0 30px -6px rgba(255,111,181,.5) !important}
.pg-method h4{margin:0 0 .5rem;font-size:1.05rem;background:linear-gradient(90deg,#7c5cff,#ff6fb5);-webkit-background-clip:text;background-clip:text;color:transparent}
.pg-method img{align-self:center;animation:fnglow 3.4s ease-in-out infinite;border-radius:18px}
.pg-h{background:linear-gradient(90deg,#7c5cff,#ff6fb5);-webkit-background-clip:text;background-clip:text;color:transparent;width:fit-content}
.tl-card .pg-btn,.tl-card .tl-btn{animation:fnglow 3.4s ease-in-out infinite}
@media(prefers-reduced-motion:reduce){.fn-sky,.tl-card::after,.pg-stats>*::after,.pg-stat::after{display:none}.tl-card,.pg-stat,.pg-stats>*,.pg-method img{animation:none}}
</style>
<script>
(function(){if(document.querySelector('.fn-sky'))return;
var s=document.createElement('div');s.className='fn-sky';
s.innerHTML='<i class="fn-aur a1"></i><i class="fn-aur a2"></i><i class="fn-aur a3"></i>';
var c=['#a78bfa','#ff9fcf','#8fd8ff','#fbbf24','#c4b0ff'];
var n=window.innerWidth<700?12:26;
for(var i=0;i<n;i++){var e=document.createElement('span');e.className='fn-sp';
e.textContent=Math.random()>.5?'\u2726':'\u2727';
e.style.left=(Math.random()*100)+'%';e.style.fontSize=(8+Math.random()*14)+'px';e.style.color=c[i%c.length];
e.style.animationDuration=(9+Math.random()*10)+'s';e.style.animationDelay=(-Math.random()*16)+'s';s.appendChild(e);}
document.body.prepend(s);})();
</script>
</x-student-layout>