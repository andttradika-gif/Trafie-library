<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Throwable;

class OpenLibraryExtra
{
    private static function client(): PendingRequest
    {
        $opts = ['allow_redirects' => ['max' => 4, 'protocols' => ['https']]];
        if (defined('CURLOPT_IPRESOLVE')) {
            $opts['curl'] = [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4];
        }
        foreach ([config('books.ca_bundle'), ini_get('curl.cainfo'), ini_get('openssl.cafile'), storage_path('app/cacert.pem'), 'C:/xampp/php/extras/ssl/cacert.pem'] as $p) {
            if ($p && is_file($p)) {
                $opts['verify'] = $p;
                break;
            }
        }
        return Http::withOptions($opts)->timeout(15)->connectTimeout(10)->acceptJson()
            ->withHeaders(['User-Agent' => 'Trafie-library/1.0']);
    }

    private static function text($d): string
    {
        if (is_array($d)) {
            $d = $d['value'] ?? '';
        }
        $d = (string) $d;
        $d = preg_split('/\R-{5,}/', $d)[0];
        $d = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $d);
        $d = preg_replace('/(\*\*|__)/', '', $d);
        return trim($d);
    }

    public static function description(string $isbn): ?string
    {
        try {
            $ed = self::client()->get('https://openlibrary.org/isbn/' . $isbn . '.json');
            if (!$ed->ok()) {
                return null;
            }
            $e = $ed->json();
            $desc = self::text($e['description'] ?? '');

            $key = $e['works'][0]['key'] ?? '';
            if ($desc === '' && preg_match('#^/works/OL\d+W$#', $key)) {
                $w = self::client()->get('https://openlibrary.org' . $key . '.json');
                if ($w->ok()) {
                    $desc = self::text($w->json('description'));
                }
            }
            return $desc !== '' ? $desc : null;
        } catch (Throwable $e) {
            return null;
        }
    }
}