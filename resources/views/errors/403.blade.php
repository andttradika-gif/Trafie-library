@include('errors.themed', [
    'code' => 403,
    'title' => 'Akses ditolak',
    'message' => (isset($exception) && $exception->getMessage()) ? $exception->getMessage() : 'Kamu tidak punya izin untuk membuka halaman ini.',
])