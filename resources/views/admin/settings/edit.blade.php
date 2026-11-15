<x-admin-layout title="Pengaturan">
<x-admin-head title="Pengaturan Perpustakaan" sub="Aturan peminjaman, denda, dan reservasi yang berlaku untuk semua siswa." icon="cog"/>
<style>
.st2 { display: grid; gap: 1.1rem; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); font-family: 'Poppins', sans-serif; }
.st2-c { padding: 1.3rem; border-radius: 24px; background: linear-gradient(150deg, rgba(255,255,255,.92), rgba(247,238,255,.76)); border: 1px solid #fff; box-shadow: 0 16px 38px rgba(139,92,246,.1); }
.st2-c .i { display: grid; place-items: center; width: 44px; height: 44px; margin-bottom: .8rem; border-radius: 15px; }
.st2-c .i svg { width: 21px; height: 21px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.st2-c h3 { margin: 0 0 .2rem; font-family: 'Playfair Display', serif; font-size: 1.05rem; color: #2d2a4a; }
.st2-c p { margin: 0 0 .9rem; font-size: .72rem; line-height: 1.5; color: #7b7796; }
.st2-u { display: flex; align-items: center; gap: .6rem; }
.st2-u span { font-size: .72rem; font-weight: 600; color: #6f6b8c; white-space: nowrap; }
.st2-h { margin-top: .6rem; font-size: .68rem; color: #8b87a8; min-height: 1.2em; }
.st2-h b { color: #5b34a8; }
.st2-info { margin-top: 1.1rem; padding: 1.1rem 1.3rem; border-radius: 20px; background: rgba(250,247,255,.9); border: 1px solid rgba(217,204,245,.7); font-size: .76rem; color: #5f5b7a; line-height: 1.7; }
.st2-info a { color: #6d3fc4; font-weight: 600; }
</style>
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf @method('PUT')
    <div class="st2">
        <div class="st2-c">
            <span class="i x3" style="background:#fbdbe8;color:#d9558a"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5h4a1.5 1.5 0 0 1 0 3h-3a1.5 1.5 0 0 0 0 3h4"/></svg></span>
            <h3>Denda per Hari</h3>
            <p>Dikenakan untuk tiap hari keterlambatan. Denda dikunci saat buku dikembalikan.</p>
            <div class="st2-u"><span>Rp</span><input type="number" name="fine_per_day" id="fineIn" min="0" max="100000" step="100" value="{{ old('fine_per_day', $v['fine_per_day']) }}" required><span>/ hari</span></div>
            <div class="st2-h" id="fineHint"></div>
        </div>
        <div class="st2-c">
            <span class="i" style="background:#d8f0e2;color:#3f9a6f"><svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
            <h3>Batas Pinjaman Aktif</h3>
            <p>Jumlah buku yang boleh dipinjam seorang siswa pada waktu yang sama.</p>
            <div class="st2-u"><input type="number" name="max_loans" min="1" max="20" value="{{ old('max_loans', $v['max_loans']) }}" required><span>buku</span></div>
        </div>
        <div class="st2-c">
            <span class="i" style="background:#d9e9fa;color:#4b8fd6"><svg viewBox="0 0 24 24"><path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/></svg></span>
            <h3>Batas Ambil Reservasi</h3>
            <p>Lama eksemplar ditahan untuk siswa di antrean sebelum kedaluwarsa dan dialihkan.</p>
            <div class="st2-u"><input type="number" name="reserve_hold_days" min="1" max="14" value="{{ old('reserve_hold_days', $v['reserve_hold_days']) }}" required><span>hari</span></div>
        </div>
    </div>
    <div class="st2-info">
        Durasi pinjam yang bisa dipilih siswa tetap <b>3, 7, atau 10 hari</b>. Metode pembayaran denda (Tunai, E-Wallet, QRIS) diatur di <a href="{{ route('admin.fines.index') }}">Denda &amp; Pembayaran</a>.
    </div>
    <button class="tl-btn" type="submit" style="margin-top:1.1rem">Simpan Pengaturan</button>
</form>
<script>
(function () {
    var i = document.getElementById('fineIn'), h = document.getElementById('fineHint');
    function rp(n) { return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    function u() { var v = parseInt(i.value, 10) || 0; h.innerHTML = 'Contoh: terlambat 3 hari = <b>' + rp(v * 3) + '</b>'; }
    i.addEventListener('input', u); u();
})();
</script>
</x-admin-layout>