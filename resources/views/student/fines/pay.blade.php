@php use App\Models\Loan; @endphp
<x-student-layout title="Bayar Denda">
    <a href="{{ route('student.fines.index') }}" class="tl-link-plain">&larr; Kembali ke Pembayaran Denda</a>

    <div class="tl-card" style="margin-top:1rem">
        <h3 class="pg-h">{{ $loan->book->title ?? 'Buku' }}</h3>
        <p class="pg-sub">Terlambat {{ $loan->late_days }} hari</p>
        <div class="st-total">{{ Loan::rupiah($loan->fine_amount) }}</div>
    </div>

    @if($pending)
        <div class="tl-card" style="margin-top:1rem">
            <span class="pg-b pg-b-pending">Menunggu verifikasi admin</span>
            <p class="pg-sub" style="margin-top:.7rem">Bukti pembayaran {{ $pending->method_label }} sudah kamu kirim {{ $pending->created_at->diffForHumans() }}. Denda lunas setelah admin memverifikasi.</p>
            @if($pending->proof_url)<img src="{{ $pending->proof_url }}" alt="Bukti" style="max-width:220px;border-radius:14px">@endif
        </div>
    @else
        @if($last && $last->reject_reason)
            <div class="tl-alert err" style="margin-top:1rem">Pembayaran sebelumnya ditolak: {{ $last->reject_reason }}. Silakan kirim ulang.</div>
        @endif
        <form method="POST" action="{{ route('student.fines.store', $loan) }}" enctype="multipart/form-data" class="tl-card" style="margin-top:1rem">
            @csrf
            <h3 class="pg-h">Pilih Metode Pembayaran</h3>
            <div class="st-choices">
                <label class="st-choice"><input type="radio" name="method" value="cash" @checked(old('method') === 'cash')><span><b>Tunai</b><small>Bayar ke petugas</small></span></label>
                <label class="st-choice"><input type="radio" name="method" value="ewallet" @checked(old('method', 'ewallet') === 'ewallet')><span><b>E-Wallet</b><small>DANA, OVO, GoPay, dll.</small></span></label>
                <label class="st-choice"><input type="radio" name="method" value="qris" @checked(old('method') === 'qris')><span><b>QRIS</b><small>Scan kode QR</small></span></label>
            </div>

            <div class="st-panel" data-m="cash">
                <p class="pg-sub">{{ $set['cash_note'] }}</p>
                <p class="pg-sub">Setelah kamu membayar, petugas mencatat pembayaranmu di sistem dan denda otomatis lunas. Tidak perlu mengunggah bukti.</p>
            </div>

            <div class="st-panel" data-m="ewallet">
                @if($set['ewallet_number'])
                    <div class="st-dest">
                        <small>Transfer ke {{ $set['ewallet_provider'] }}</small>
                        <b id="ewNum">{{ $set['ewallet_number'] }}</b>
                        <small>a.n. {{ $set['ewallet_holder'] ?: '-' }}</small>
                        <button type="button" class="pg-btn" onclick="navigator.clipboard.writeText(document.getElementById('ewNum').textContent.trim());this.textContent='Tersalin'">Salin nomor</button>
                    </div>
                    <p class="pg-sub">Transfer tepat <b>{{ Loan::rupiah($loan->fine_amount) }}</b>, lalu unggah screenshot bukti transfernya.</p>
                @else
                    <p class="pg-sub">E-Wallet belum diatur admin. Pilih Tunai atau QRIS.</p>
                @endif
            </div>

            <div class="st-panel" data-m="qris">
                @if($set['qris_image'])
                    <img class="st-qr" src="{{ asset('storage/' . $set['qris_image']) }}" alt="QRIS">
                    @if($set['qris_note'])<p class="pg-sub">{{ $set['qris_note'] }}</p>@endif
                    <p class="pg-sub">Scan, bayar tepat <b>{{ Loan::rupiah($loan->fine_amount) }}</b>, lalu unggah screenshot bukti pembayaran.</p>
                @else
                    <p class="pg-sub">QRIS belum diunggah admin. Pilih Tunai atau E-Wallet.</p>
                @endif
            </div>

            <div class="st-proof" id="proofBox">
                <label class="tl-label">Bukti pembayaran (jpg, png, webp, maks 3 MB)</label>
                <input class="tl-input" type="file" name="proof" accept="image/*">
                <label class="tl-label" style="margin-top:.6rem">Nomor referensi (opsional)</label>
                <input class="tl-input" name="reference" value="{{ old('reference') }}" maxlength="100">
            </div>
            <button class="tl-btn" id="sendBtn" type="submit" style="margin-top:1rem">Kirim Bukti Pembayaran</button>
        </form>
        <script>
        (function () {
            function sync() {
                var c = document.querySelector('input[name=method]:checked');
                var v = c ? c.value : 'ewallet';
                document.querySelectorAll('.st-panel').forEach(function (p) { p.style.display = p.dataset.m === v ? 'block' : 'none'; });
                document.getElementById('proofBox').style.display = v === 'cash' ? 'none' : 'block';
                document.getElementById('sendBtn').textContent = v === 'cash' ? 'Saya Mengerti' : 'Kirim Bukti Pembayaran';
            }
            document.querySelectorAll('input[name=method]').forEach(function (r) { r.addEventListener('change', sync); });
            sync();
        })();
        </script>
    @endif
</x-student-layout>