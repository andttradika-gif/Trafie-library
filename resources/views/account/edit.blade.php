@if(Auth::user()->role === 'admin')
<x-admin-layout title="Akun Saya">
    @include('account.partials.form')
</x-admin-layout>
@else
<x-student-layout title="Akun Saya">
    @include('account.partials.form')
</x-student-layout>
@endif