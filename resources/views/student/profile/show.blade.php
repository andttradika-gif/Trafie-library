@php use App\Models\Loan; @endphp
<x-student-layout title="My Profile">
    <div class="tl-card st-prof">
        <div class="pg-avatar">@if($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="">@else<span>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>@endif</div>
        <div>
            <h2 class="pg-h" style="font-size:1.5rem">{{ $user->name }}</h2>
            <p class="pg-sub" style="margin:.2rem 0">NISN {{ $user->nisn }} &middot; {{ $user->email }}</p>
            <p class="pg-sub" style="margin:0">Anggota sejak {{ $user->created_at->format('d M Y') }}</p>
            <a class="pg-btn" href="{{ route('account.edit') }}" style="text-decoration:none;display:inline-block;margin-top:.8rem">Ubah foto dan password</a>
        </div>
    </div>

    <div class="pg-stats" style="margin-top:1.1rem">
        <div class="pg-stat"><small>Total Peminjaman</small><b>{{ $stats['total'] }}</b></div>
        <div class="pg-stat"><small>Sedang Dipinjam</small><b>{{ $stats['active'] }}</b></div>
        <div class="pg-stat"><small>Sudah Dikembalikan</small><b>{{ $stats['returned'] }}</b></div>
        <div class="pg-stat"><small>Buku Favorit</small><b>{{ $stats['favorites'] }}</b></div>
        <div class="pg-stat"><small>Ulasan Ditulis</small><b>{{ $stats['reviews'] }}</b></div>
        <div class="pg-stat"><small>Denda Belum Dibayar</small><b style="font-size:1.1rem">{{ Loan::rupiah($stats['unpaid']) }}</b></div>
    </div>

    <div class="tl-card" style="margin-top:1.1rem">
        <h3 class="pg-h">Level Membaca</h3>
        @php $pr = \App\Services\Reading::summary(Auth::user()); @endphp<p class="pg-sub" style="margin:0"><b>Level {{ $pr['lv']['level'] }} - {{ $pr['lv']['name'] }}</b> &middot; {{ $pr['xp'] }} XP &middot; {{ $pr['books'] }} buku selesai &middot; streak {{ $pr['streak'] }} hari</p><div class="rp-bar" style="margin:.7rem 0"><i style="width:{{ $pr['lv']['pct'] }}%"></i></div><a class="pg-btn" href="{{ route('student.achievements.index') }}" style="text-decoration:none;display:inline-block">Lihat achievement</a>
    </div>
</x-student-layout>