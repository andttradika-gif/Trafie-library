<?php

return [
    'google_key' => env('GOOGLE_BOOKS_API_KEY') ?: null,
    'ca_bundle' => env('BOOKS_CA_BUNDLE') ?: null,
    'timeout' => 20,
    'cover_hosts' => ['books.google.com', 'books.googleusercontent.com', 'covers.openlibrary.org'],
];