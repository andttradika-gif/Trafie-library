@php use App\Models\Loan; @endphp
<x-admin-layout title="Pembayaran Denda">
<x-admin-head title="Pembayaran Denda" sub="Atur metode bayar, verifikasi bukti, dan catat denda tunai." icon="coin"/>
    <div class="pg-stats">
        <div class="pg-stat"><small>Total Denda</small><b>{{ Loan::rupiah($stats['total']) }}</b></div>
        <div class="pg-stat"><small>Sudah Dibayar</small><b>{{ Loan::rupiah($stats['paid']) }}</b></div>
        <div class="pg-stat"><small>Belum Dibayar</small><b>{{ Loan::rupiah($stats['unpaid']) }}</b></div>
        <div class="pg-stat"><small>Menunggu Verifikasi</small><b>{{ $stats['pending'] }}</b></div>
    </div>

    <div class="tl-card" style="margin-bottom:1.1rem">
        <h3 class="pg-h">Metode Pembayaran untuk Siswa</h3>
        <p class="pg-sub">Siswa melihat tiga pilihan ini di halaman denda mereka: Tunai, E-Wallet, dan QRIS. Denda baru lunas setelah kamu memverifikasi.</p>
        <form method="POST" action="{{ route('admin.fines.settings') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="pg-methods">
                <div class="pg-method">
                    <h4>Tunai</h4>
                    <label class="tl-label">Catatan / lokasi pembayaran</label>
                    <input class="tl-input" name="cash_note" value="{{ old('cash_note', $set['cash_note']) }}" maxlength="255">
                </div>
                <div class="pg-method">
                    <h4>E-Wallet</h4>
                    <label class="tl-label">Provider</label>
                    <select class="tl-input" name="ewallet_provider">
                        <option value="">Belum diatur</option>
                        @foreach(['DANA','OVO','GoPay','ShopeePay','LinkAja'] as $pv)<option value="{{ $pv }}" @selected(old('ewallet_provider', $set['ewallet_provider']) === $pv)>{{ $pv }}</option>@endforeach
                    </select>
                    <label class="tl-label">Nomor e-wallet</label>
                    <input class="tl-input" name="ewallet_number" value="{{ old('ewallet_number', $set['ewallet_number']) }}" inputmode="numeric" placeholder="Contoh: 081234567890">
                    <label class="tl-label">Atas nama</label>
                    <input class="tl-input" name="ewallet_holder" value="{{ old('ewallet_holder', $set['ewallet_holder']) }}">
                </div>
                <div class="pg-method">
                    <h4>QRIS</h4>
                    @if($set['qris_image'])<img class="pg-qris" src="{{ asset('storage/' . $set['qris_image']) }}" alt="QRIS">@endif
                    <label class="tl-label">Unggah gambar QRIS (jpg, png, webp, maks 3 MB)</label>
                    <input class="tl-input" type="file" name="qris" accept="image/*">
                    <label class="tl-label" style="margin-top:.55rem">Catatan</label>
                    <input class="tl-input" name="qris_note" value="{{ old('qris_note', $set['qris_note']) }}" maxlength="255" placeholder="Contoh: a.n. Perpustakaan SMK">
                </div>
            </div>
            <button class="tl-btn" type="submit" style="margin-top:1rem">Simpan Metode Pembayaran</button>
        </form>
    </div>

    <div class="tl-card" style="margin-bottom:1.1rem; overflow-x:auto">
        <h3 class="pg-h">Menunggu Verifikasi</h3>
        <table class="tl-table">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Nominal</th><th>Metode</th><th>Bukti</th><th style="width:300px">Aksi</th></tr></thead>
            <tbody>
            @forelse($pending as $p)
                <tr>
                    <td><b>{{ $p->user->name ?? '-' }}</b><br><small>{{ $p->created_at->diffForHumans() }}</small></td>
                    <td>{{ $p->loan->book->title ?? '-' }}</td>
                    <td><b>{{ Loan::rupiah($p->amount) }}</b></td>
                    <td><span class="tl-badge">{{ $p->method_label }}</span>@if($p->reference)<br><small>Ref: {{ $p->reference }}</small>@endif</td>
                    <td>@if($p->proof_url)<a href="{{ $p->proof_url }}" target="_blank"><img class="pg-proof" src="{{ $p->proof_url }}" alt="Bukti"></a>@else<small>-</small>@endif</td>
                    <td>
                        <div class="pg-actions">
                            <form method="POST" action="{{ route('admin.fines.verify', $p) }}">@csrf @method('PATCH')<button class="pg-btn" type="submit">Verifikasi</button></form>
                            <form method="POST" action="{{ route('admin.fines.reject', $p) }}" class="pg-actions">@csrf @method('PATCH')
                                <input class="tl-input" style="width:130px" name="reason" placeholder="Alasan tolak" required maxlength="255">
                                <button class="tl-btn-danger" type="submit">Tolak</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="tl-empty">Tidak ada bukti pembayaran yang menunggu.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="tl-card" style="margin-bottom:1.1rem; overflow-x:auto">
        <h3 class="pg-h">Denda Belum Dibayar</h3>
        <table class="tl-table">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Terlambat</th><th>Denda</th><th style="width:200px">Aksi</th></tr></thead>
            <tbody>
            @forelse($unpaid as $l)
                <tr>
                    <td><b>{{ $l->user->name ?? '-' }}</b><br><small>{{ $l->user->nisn ?? '' }}</small></td>
                    <td>{{ $l->book->title ?? '-' }}</td>
                    <td>{{ $l->late_days }} hari</td>
                    <td><b>{{ Loan::rupiah($l->fine_amount) }}</b>@if(in_array($l->id, $pendingLoanIds))<br><span class="pg-b pg-b-pending">Ada bukti masuk</span>@endif</td>
                    <td>
                        <form method="POST" action="{{ route('admin.fines.cash', $l) }}" onsubmit="return confirm('Catat pembayaran tunai {{ Loan::rupiah($l->fine_amount) }}?')">@csrf<button class="pg-btn" type="submit">Catat bayar tunai</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="tl-empty">Tidak ada denda yang belum dibayar.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($running->isNotEmpty())
    <div class="tl-card" style="margin-bottom:1.1rem; overflow-x:auto">
        <h3 class="pg-h">Denda Berjalan</h3>
        <p class="pg-sub">Buku belum dikembalikan dan sudah lewat jatuh tempo. Denda dikunci setelah buku dikembalikan.</p>
        <table class="tl-table">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Jatuh Tempo</th><th>Terlambat</th><th>Denda Sementara</th></tr></thead>
            <tbody>
            @foreach($running as $l)
                <tr><td>{{ $l->user->name ?? '-' }}</td><td>{{ $l->book->title ?? '-' }}</td><td>{{ $l->due_date->format('d M Y') }}</td><td>{{ $l->late_days }} hari</td><td>{{ Loan::rupiah($l->current_fine) }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="tl-card" style="overflow-x:auto">
        <h3 class="pg-h">Riwayat Pembayaran</h3>
        <table class="tl-table">
            <thead><tr><th>Siswa</th><th>Buku</th><th>Nominal</th><th>Metode</th><th>Status</th><th>Diproses</th></tr></thead>
            <tbody>
            @forelse($history as $p)
                <tr>
                    <td>{{ $p->user->name ?? '-' }}</td><td>{{ $p->loan->book->title ?? '-' }}</td><td>{{ Loan::rupiah($p->amount) }}</td>
                    <td>{{ $p->method_label }}</td>
                    <td><span class="pg-b pg-b-{{ strtolower($p->status) }}">{{ $p->label }}</span>@if($p->reject_reason)<br><small>{{ $p->reject_reason }}</small>@endif</td>
                    <td>{{ $p->verified_at ? $p->verified_at->format('d M Y H:i') : '-' }}<br><small>{{ $p->verifier->name ?? '' }}</small></td>
                </tr>
            @empty
                <tr><td colspan="6" class="tl-empty">Belum ada riwayat pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>