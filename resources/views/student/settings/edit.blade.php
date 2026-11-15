<x-student-layout title="Settings">
    <form method="POST" action="{{ route('student.settings.update') }}" class="tl-card" style="max-width:640px">
        @csrf @method('PUT')
        <h3 class="pg-h">Peminjaman</h3>
        <label class="tl-label">Durasi pinjam bawaan</label>
        <div class="st-choices" style="margin-bottom:1.2rem">
            @foreach([3, 7, 10] as $d)
                <label class="st-choice"><input type="radio" name="default_duration" value="{{ $d }}" @checked((int) old('default_duration', $s->default_duration) === $d)><span><b>{{ $d }} hari</b></span></label>
            @endforeach
        </div>

        <h3 class="pg-h">Notifikasi</h3>
        <p class="pg-sub">Notifikasi peminjaman disetujui atau ditolak selalu dikirim. Sisanya bisa kamu atur.</p>
        <label class="st-switch"><input type="checkbox" name="n_due" value="1" @checked(old('n_due', $s->n_due))><span>Pengingat jatuh tempo dan keterlambatan</span></label>
        <label class="st-switch"><input type="checkbox" name="n_reservation" value="1" @checked(old('n_reservation', $s->n_reservation))><span>Reservasi siap atau kedaluwarsa</span></label>
        <label class="st-switch"><input type="checkbox" name="n_fines" value="1" @checked(old('n_fines', $s->n_fines))><span>Denda dan status pembayaran</span></label>
        <label class="st-switch"><input type="checkbox" name="n_announcements" value="1" @checked(old('n_announcements', $s->n_announcements))><span>Pengumuman baru</span></label>
        <button class="tl-btn" type="submit" style="margin-top:1.2rem">Simpan Pengaturan</button>
    </form>
</x-student-layout>