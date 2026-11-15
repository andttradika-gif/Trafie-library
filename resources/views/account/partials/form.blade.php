@php $user = Auth::user(); @endphp
<x-admin-head title="Akun Saya" sub="Foto profil, nama, dan password akunmu." icon="user"/>
<div class="pg-grid2">
    <div class="tl-card" style="text-align:center">
        <h3 class="pg-h">Foto Profil</h3>
        <div class="pg-avatar" style="margin:1rem auto">
            @if($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="">@else<span>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>@endif
        </div>
        <form method="POST" action="{{ route('account.avatar') }}" enctype="multipart/form-data">
            @csrf
            <input class="tl-input" type="file" name="avatar" accept="image/*" required>
            <p class="pg-sub" style="margin:.5rem 0 .8rem">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
            <button class="tl-btn" type="submit">Unggah Foto</button>
        </form>
        @if($user->avatar)
            <form method="POST" action="{{ route('account.avatar.delete') }}" style="margin-top:.7rem" onsubmit="return confirm('Hapus foto profil?')">@csrf @method('DELETE')<button class="tl-btn-danger" type="submit">Hapus Foto</button></form>
        @endif
    </div>
    <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="tl-card">
            <h3 class="pg-h">Data Akun</h3>
            <form method="POST" action="{{ route('account.name') }}">
                @csrf @method('PUT')
                <div class="tl-form-grid">
                    <div class="full"><label class="tl-label">Nama lengkap</label><input class="tl-input" name="name" value="{{ old('name', $user->name) }}" required maxlength="255"></div>
                    <div><label class="tl-label">Email</label><input class="tl-input" value="{{ $user->email }}" disabled></div>
                    <div><label class="tl-label">{{ $user->role === 'admin' ? 'Peran' : 'NISN' }}</label><input class="tl-input" value="{{ $user->role === 'admin' ? 'Administrator' : $user->nisn }}" disabled></div>
                </div>
                <button class="tl-btn" type="submit" style="margin-top:1rem">Simpan Nama</button>
            </form>
        </div>
        <div class="tl-card">
            <h3 class="pg-h">Ganti Password</h3>
            <form method="POST" action="{{ route('account.password') }}">
                @csrf @method('PUT')
                <div class="tl-form-grid">
                    <div class="full"><label class="tl-label">Password saat ini</label><input class="tl-input" type="password" name="current_password" required autocomplete="current-password"></div>
                    <div><label class="tl-label">Password baru (min. 8 karakter)</label><input class="tl-input" type="password" name="password" required autocomplete="new-password"></div>
                    <div><label class="tl-label">Ulangi password baru</label><input class="tl-input" type="password" name="password_confirmation" required autocomplete="new-password"></div>
                </div>
                <button class="tl-btn" type="submit" style="margin-top:1rem">Ganti Password</button>
            </form>
        </div>
    </div>
</div>