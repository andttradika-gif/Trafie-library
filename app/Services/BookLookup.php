<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class BookLookup
{
    // ---------- ISBN ----------
    public static function cleanIsbn(string $raw): string
    {
        return strtoupper(preg_replace('/[^0-9Xx]/', '', $raw));
    }

    /** Mengembalikan ISBN-13 yang valid, atau null kalau salah (panjang atau check digit). */
    public static function toIsbn13(string $raw): ?string
    {
        $s = self::cleanIsbn($raw);

        if (strlen($s) === 13 && ctype_digit($s)) {
            $sum = 0;
            for ($i = 0; $i < 13; $i++) {
                $sum += (int) $s[$i] * ($i % 2 ? 3 : 1);
            }
            return $sum % 10 === 0 ? $s : null;
        }

        if (strlen($s) === 10 && preg_match('/^\d{9}[\dX]$/', $s)) {
            $sum = 0;
            for ($i = 0; $i < 10; $i++) {
                $v = $s[$i] === 'X' ? 10 : (int) $s[$i];
                $sum += $v * (10 - $i);
            }
            if ($sum % 11 !== 0) {
                return null;
            }
            $core = '978' . substr($s, 0, 9);
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int) $core[$i] * ($i % 2 ? 3 : 1);
            }
            return $core . ((10 - ($sum % 10)) % 10);
        }

        return null;
    }

    public static function toIsbn10(string $isbn13): ?string
    {
        if (strlen($isbn13) !== 13 || !str_starts_with($isbn13, '978')) {
            return null;
        }
        $core = substr($isbn13, 3, 9);
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $core[$i] * (10 - $i);
        }
        $c = (11 - ($sum % 11)) % 11;
        return $core . ($c === 10 ? 'X' : (string) $c);
    }

    // ---------- HTTP ----------
    private static function caBundle(): ?string
    {
        $candidates = [
            config('books.ca_bundle'),
            ini_get('curl.cainfo'),
            ini_get('openssl.cafile'),
            storage_path('app/cacert.pem'),
            'C:/xampp/php/extras/ssl/cacert.pem',
            'C:/xampp/apache/bin/curl-ca-bundle.crt',
        ];
        foreach ($candidates as $p) {
            if ($p && is_file($p)) {
                return $p;
            }
        }
        return null;
    }

    private static function base(): PendingRequest
    {
        $opts = ['allow_redirects' => ['max' => 4, 'protocols' => ['https']]];
        if (defined('CURLOPT_IPRESOLVE')) {
            $opts['curl'] = [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4];
        }
        if ($ca = self::caBundle()) {
            $opts['verify'] = $ca;
        }
        return Http::withOptions($opts)
            ->timeout((int) config('books.timeout', 20))
            ->connectTimeout(10)
            ->withHeaders(['User-Agent' => 'Trafie-library/1.0']);
    }

    private static function http(): PendingRequest
    {
        return self::base()->acceptJson();
    }

    private static function netError(Throwable $e, string $service): string
    {
        $m = $e->getMessage();
        if (str_contains($m, 'cURL error 60') || str_contains($m, 'SSL certificate') || str_contains($m, 'cURL error 77')) {
            return 'Sertifikat SSL server PHP belum lengkap sehingga ' . $service . ' tidak bisa dihubungi. Jalankan ulang skrip V2-15 (otomatis memperbaiki), atau isi BOOKS_CA_BUNDLE di .env.';
        }
        if (str_contains($m, 'cURL error 6') || str_contains($m, 'Could not resolve host')) {
            return 'Tidak ada koneksi internet dari server, jadi ' . $service . ' tidak bisa dihubungi.';
        }
        if (str_contains($m, 'cURL error 28') || str_contains($m, 'timed out')) {
            return 'Koneksi ke ' . $service . ' terlalu lama (timeout). Coba lagi sebentar lagi.';
        }
        return 'Gagal menghubungi ' . $service . ': ' . Str::limit($m, 140);
    }

    // ---------- PENCARIAN ----------
    public static function lookup(string $isbn13): array
    {
        $warnings = [];
        @set_time_limit(120);
        $google = self::google($isbn13, $warnings);

        $ol = null;
        if (!$google || self::incomplete($google)) {
            $ol = self::openLibrary($isbn13, $warnings);
        }

        if (!$google && !$ol) {
            return ['found' => false, 'warnings' => $warnings];
        }

        $data = $google ?: [];
        $filled = false;
        if ($ol) {
            foreach (['title', 'author', 'publisher', 'year', 'pages', 'description', 'cover_url', 'cover_preview'] as $k) {
                if (empty($data[$k]) && !empty($ol[$k])) {
                    $data[$k] = $ol[$k];
                    $filled = true;
                }
            }
            if (empty($data['categories']) && !empty($ol['categories'])) {
                $data['categories'] = $ol['categories'];
            }
        }

        if (empty($data['description'])) {
            $extra = \App\Services\OpenLibraryExtra::description($isbn13);
            if ($extra) {
                $data['description'] = $extra;
                $filled = true;
            }
        }
        $source = $google ? ($filled ? 'Google Books + Open Library' : 'Google Books') : 'Open Library';

        return [
            'found' => true,
            'source' => $source,
            'data' => $data + ['categories' => [], 'language' => null],
            'warnings' => $warnings,
        ];
    }

    private static function incomplete(array $d): bool
    {
        foreach (['publisher', 'year', 'pages', 'description', 'cover_url'] as $k) {
            if (empty($d[$k])) {
                return true;
            }
        }
        return false;
    }

    private static function google(string $isbn13, array &$warnings): ?array
    {
        $key = config('books.google_key');

        foreach ([$isbn13, self::toIsbn10($isbn13)] as $q) {
            if (!$q) {
                continue;
            }
            try {
                $res = self::http()->retry(2, 700, fn ($e) => $e instanceof \Illuminate\Http\Client\ConnectionException, false)->get('https://www.googleapis.com/books/v1/volumes', array_filter([
                    'q' => 'isbn:' . $q,
                    'maxResults' => 5,
                    'printType' => 'books',
                    'key' => $key,
                ]));
            } catch (Throwable $e) {
                $warnings[] = self::netError($e, 'Google Books');
                return null;
            }

            if (in_array($res->status(), [403, 429], true)) {
                $warnings[] = 'Google Books menolak permintaan (kuota habis). Coba lagi nanti, atau isi GOOGLE_BOOKS_API_KEY di .env.';
                return null;
            }
            if (!$res->ok()) {
                $warnings[] = 'Google Books membalas error HTTP ' . $res->status() . '.';
                return null;
            }

            $items = $res->json('items') ?? [];
            if (!$items) {
                continue;
            }

            $pick = null;
            foreach ($items as $it) {
                foreach ($it['volumeInfo']['industryIdentifiers'] ?? [] as $id) {
                    if (self::toIsbn13($id['identifier'] ?? '') === $isbn13) {
                        $pick = $it;
                        break 2;
                    }
                }
            }
            return self::fromGoogle($pick ?: $items[0]);
        }

        return null;
    }

    private static function fromGoogle(array $item): array
    {
        $v = $item['volumeInfo'] ?? [];

        $title = trim(($v['title'] ?? '') . (!empty($v['subtitle']) ? ': ' . $v['subtitle'] : ''));

        $cover = null;
        foreach (['extraLarge', 'large', 'medium', 'small', 'thumbnail', 'smallThumbnail'] as $k) {
            if (!empty($v['imageLinks'][$k])) {
                $cover = $v['imageLinks'][$k];
                break;
            }
        }
        if ($cover) {
            $cover = preg_replace('#^http://#i', 'https://', $cover);
            $cover = str_replace('&edge=curl', '', $cover);
        }

        $cats = [];
        foreach ($v['categories'] ?? [] as $c) {
            foreach (explode('/', $c) as $p) {
                $p = trim($p);
                if ($p !== '') {
                    $cats[] = $p;
                }
            }
        }

        return [
            'title' => $title,
            'author' => implode(', ', $v['authors'] ?? []),
            'publisher' => trim((string) ($v['publisher'] ?? '')),
            'year' => preg_match('/^(\d{4})/', (string) ($v['publishedDate'] ?? ''), $m) ? (int) $m[1] : null,
            'pages' => !empty($v['pageCount']) ? (int) $v['pageCount'] : null,
            'description' => self::cleanText((string) ($v['description'] ?? '')),
            'categories' => array_values(array_unique($cats)),
            'language' => self::languageName($v['language'] ?? null),
            'cover_url' => $cover,
            'cover_preview' => $cover,
        ];
    }

    private static function openLibrary(string $isbn, array &$warnings): ?array
    {
        try {
            $res = self::http()->get('https://openlibrary.org/api/books', [
                'bibkeys' => 'ISBN:' . $isbn,
                'format' => 'json',
                'jscmd' => 'data',
            ]);
        } catch (Throwable $e) {
            $warnings[] = self::netError($e, 'Open Library');
            return null;
        }
        if (!$res->ok()) {
            return null;
        }

        $d = $res->json()['ISBN:' . $isbn] ?? null;
        if (!$d) {
            return null;
        }

        $cover = $d['cover']['large'] ?? ($d['cover']['medium'] ?? ($d['cover']['small'] ?? null));
        $cats = [];
        foreach (array_slice($d['subjects'] ?? [], 0, 6) as $s) {
            if (!empty($s['name'])) {
                $cats[] = $s['name'];
            }
        }

        return [
            'title' => trim(($d['title'] ?? '') . (!empty($d['subtitle']) ? ': ' . $d['subtitle'] : '')),
            'author' => implode(', ', array_column($d['authors'] ?? [], 'name')),
            'publisher' => $d['publishers'][0]['name'] ?? '',
            'year' => preg_match('/(\d{4})/', (string) ($d['publish_date'] ?? ''), $m) ? (int) $m[1] : null,
            'pages' => !empty($d['number_of_pages']) ? (int) $d['number_of_pages'] : null,
            'description' => '',
            'categories' => $cats,
            'cover_url' => $cover,
            'cover_preview' => $cover,
        ];
    }

    private static function cleanText(string $t): string
    {
        $t = preg_replace('#<\s*br\s*/?>#i', "\n", $t);
        $t = preg_replace('#</\s*(p|div|li)\s*>#i', "\n", $t);
        $t = html_entity_decode(strip_tags($t), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $t = preg_replace("/[ \t]+/", ' ', $t);
        $t = preg_replace("/\n{3,}/", "\n\n", $t);
        return trim($t);
    }

    private static function languageName(?string $code): ?string
    {
        if (!$code) {
            return null;
        }
        $map = [
            'id' => 'Indonesia', 'en' => 'Inggris', 'ms' => 'Melayu', 'ar' => 'Arab', 'ja' => 'Jepang',
            'zh' => 'Mandarin', 'ko' => 'Korea', 'de' => 'Jerman', 'fr' => 'Prancis', 'es' => 'Spanyol',
            'nl' => 'Belanda', 'jv' => 'Jawa', 'su' => 'Sunda', 'pt' => 'Portugis', 'ru' => 'Rusia',
        ];
        $c = strtolower(substr($code, 0, 2));
        return $map[$c] ?? strtoupper($code);
    }

    // ---------- KATEGORI ----------
    /** Mencocokkan kategori sumber (Inggris) ke kategori yang sudah ada di aplikasi. */
    public static function suggestCategory(array $raw): array
    {
        $cats = Category::orderBy('name')->get(['id', 'name']);
        $byName = $cats->mapWithKeys(fn ($c) => [mb_strtolower($c->name) => $c->id]);

        $syn = [
            'fiction' => ['fiksi', 'novel'], 'novel' => ['novel', 'fiksi'], 'literary' => ['fiksi', 'novel'],
            'poetry' => ['fiksi'], 'comics' => ['fiksi'], 'drama' => ['fiksi'], 'fantasy' => ['fiksi', 'novel'],
            'science' => ['sains'], 'nature' => ['sains'], 'technology' => ['sains'], 'computers' => ['sains'],
            'mathematics' => ['sains'], 'medical' => ['sains'], 'physics' => ['sains'], 'engineering' => ['sains'],
            'history' => ['sejarah'], 'biography' => ['sejarah', 'non-fiksi'], 'political' => ['sejarah', 'non-fiksi'],
            'education' => ['pendidikan'], 'study aids' => ['pendidikan'], 'language arts' => ['pendidikan'],
            'foreign language' => ['pendidikan'], 'reference' => ['pendidikan', 'non-fiksi'],
            'business' => ['non-fiksi'], 'self-help' => ['non-fiksi'], 'religion' => ['non-fiksi'],
            'psychology' => ['non-fiksi'], 'health' => ['non-fiksi'], 'cooking' => ['non-fiksi'],
            'art' => ['non-fiksi'], 'philosophy' => ['non-fiksi'], 'social science' => ['non-fiksi'],
            'family' => ['non-fiksi'], 'true crime' => ['non-fiksi'],
        ];

        foreach ($raw as $segment) {
            $s = mb_strtolower($segment);

            // nama persis sama atau terkandung
            foreach ($byName as $name => $id) {
                if ($s === $name || (mb_strlen($name) >= 4 && str_contains($s, $name))) {
                    return ['category_id' => $id, 'new_category' => null];
                }
            }

            if (str_contains($s, 'nonfiction') || str_contains($s, 'non-fiction')) {
                $targets = ['non-fiksi'];
            } else {
                $targets = [];
                foreach ($syn as $kw => $names) {
                    if (str_contains($s, $kw)) {
                        $targets = $names;
                        break;
                    }
                }
            }
            foreach ($targets as $t) {
                if (isset($byName[$t])) {
                    return ['category_id' => $byName[$t], 'new_category' => null];
                }
            }
        }

        return ['category_id' => null, 'new_category' => $raw ? Str::title($raw[0]) : null];
    }

    // ---------- COVER ----------
    private static function hostAllowed(string $host): bool
    {
        $host = strtolower($host);
        if (in_array($host, (array) config('books.cover_hosts'), true)) {
            return true;
        }
        return str_ends_with($host, '.googleusercontent.com');
    }

    /** Mengunduh cover ke storage publik. Mengembalikan path relatif atau null. */
    public static function downloadCover(string $url): ?string
    {
        $parts = parse_url($url);
        if (($parts['scheme'] ?? '') !== 'https' || !self::hostAllowed($parts['host'] ?? '')) {
            return null;
        }

        $candidates = [];
        if (strtolower($parts['host']) === 'books.google.com' && preg_match('/[?&]zoom=\d/', $url)) {
            foreach ([3, 2, 1] as $z) {
                $candidates[] = preg_replace('/([?&]zoom=)\d/', '${1}' . $z, $url);
            }
        }
        $candidates[] = $url;

        foreach (array_unique($candidates) as $u) {
            try {
                $res = self::base()->withHeaders(['Accept' => 'image/*'])->get($u);
            } catch (Throwable $e) {
                continue;
            }
            if (!$res->ok()) {
                continue;
            }
            $body = $res->body();
            if (strlen($body) < 3000 || strlen($body) > 3 * 1024 * 1024) {
                continue;
            }
            $info = @getimagesizefromstring($body);
            if (!$info || $info[0] < 80) {
                continue;
            }
            $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'][$info[2]] ?? null;
            if (!$ext) {
                continue;
            }
            $name = 'covers/' . Str::random(32) . '.' . $ext;
            Storage::disk('public')->put($name, $body);
            return $name;
        }

        return null;
    }
}