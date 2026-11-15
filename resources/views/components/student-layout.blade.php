@props(['title' => 'Dashboard'])
@php
    use Illuminate\Support\Facades\Route;
    $mk = fn ($route, $match, $label, $icon) => Route::has($route)
        ? ['route' => $route, 'match' => $match, 'label' => $label, 'icon' => $icon]
        : ['label' => $label, 'icon' => $icon, 'soon' => true];
    $menu = [
        $mk('student.dashboard', 'student.dashboard', 'Dashboard', 'home'),
        $mk('student.books.index', 'student.books.*', 'Book Catalog', 'book'),
        $mk('student.categories.index', 'student.categories.*', 'Categories', 'grid'),
        $mk('student.loans.index', 'student.loans.*', 'My Borrowings', 'bookmark'),
        $mk('student.reservations.index', 'student.reservations.*', 'Reservations', 'clock'),
        $mk('student.fines.index', 'student.fines.*', 'Pembayaran Denda', 'coin'),
        $mk('student.reading.index', 'student.reading.*', 'Reading Progress', 'trend'),
        $mk('student.favorites.index', 'student.favorites.*', 'My Favorites', 'heart'),
        $mk('student.galaxy.index', 'student.galaxy.*', 'Reading Galaxy', 'compass'),
        $mk('student.wrapped.index', 'student.wrapped.*', 'Reading Wrapped', 'gift'),
        $mk('student.challenges.index', 'student.challenges.*', 'Reading Challenges', 'award'),
        $mk('student.achievements.index', 'student.achievements.*', 'Achievements', 'star'),
        $mk('student.notifications.index', 'student.notifications.*', 'Notifications', 'bell'),
        $mk('student.announcements.index', 'student.announcements.*', 'Pengumuman', 'mega'),
        $mk('student.profile.show', 'student.profile.*', 'My Profile', 'user'),
        $mk('student.settings.edit', 'student.settings.*', 'Settings', 'cog'),
    ];
@endphp
<x-shell role="Siswa" :title="$title" :items="$menu">
    {{ $slot }}
</x-shell>