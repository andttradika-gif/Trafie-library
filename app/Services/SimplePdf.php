<?php

namespace App\Services;

/**
 * Penulis PDF mini tanpa library. Koordinat memakai "top" (dari atas halaman), satuan point.
 */
class SimplePdf
{
    public float $w;
    public float $h;
    private array $pages = [];
    private string $cur = '';
    private bool $started = false;

    private const REG = [278,278,355,556,556,889,667,191,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,278,278,584,584,584,556,1015,667,667,722,722,667,611,778,722,278,500,667,556,833,722,778,667,778,722,667,611,722,667,944,667,667,611,278,278,278,469,556,333,556,556,500,556,556,278,556,556,222,222,500,222,833,556,556,556,556,333,500,278,556,500,722,500,500,500,334,260,334,584];
    private const BOLD = [278,333,474,556,556,889,722,238,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,333,333,584,584,584,611,975,722,722,722,722,667,611,778,722,278,556,722,611,833,722,778,667,778,722,667,611,722,667,944,667,667,611,333,278,333,584,556,333,556,611,556,611,556,333,611,611,278,278,556,278,889,611,611,611,611,389,556,333,611,556,778,556,556,500,389,280,389,584];

    public function __construct(float $w = 841.89, float $h = 595.28)
    {
        $this->w = $w;
        $this->h = $h;
    }

    public function addPage(): void
    {
        if ($this->started) {
            $this->pages[] = $this->cur;
        }
        $this->cur = '';
        $this->started = true;
    }

    private function rgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        return sprintf('%.3f %.3f %.3f', hexdec(substr($hex, 0, 2)) / 255, hexdec(substr($hex, 2, 2)) / 255, hexdec(substr($hex, 4, 2)) / 255);
    }

    private function n(float $v): string
    {
        return rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.') ?: '0';
    }

    private function wrap(string $body, ?string $alpha): string
    {
        return $alpha ? "q /{$alpha} gs\n{$body}Q\n" : $body;
    }

    public function rect(float $x, float $top, float $w, float $h, string $hex, ?string $alpha = null): void
    {
        $y = $this->h - $top - $h;
        $this->cur .= $this->wrap($this->rgb($hex) . " rg\n" . $this->n($x) . ' ' . $this->n($y) . ' ' . $this->n($w) . ' ' . $this->n($h) . " re f\n", $alpha);
    }

    private function rrPath(float $x, float $y, float $w, float $h, float $r): string
    {
        $r = max(0, min($r, $w / 2, $h / 2));
        $k = $r * 0.5523;
        $n = fn ($v) => $this->n($v);
        $p = $n($x + $r) . ' ' . $n($y) . " m\n";
        $p .= $n($x + $w - $r) . ' ' . $n($y) . " l\n";
        $p .= $n($x + $w - $r + $k) . ' ' . $n($y) . ' ' . $n($x + $w) . ' ' . $n($y + $r - $k) . ' ' . $n($x + $w) . ' ' . $n($y + $r) . " c\n";
        $p .= $n($x + $w) . ' ' . $n($y + $h - $r) . " l\n";
        $p .= $n($x + $w) . ' ' . $n($y + $h - $r + $k) . ' ' . $n($x + $w - $r + $k) . ' ' . $n($y + $h) . ' ' . $n($x + $w - $r) . ' ' . $n($y + $h) . " c\n";
        $p .= $n($x + $r) . ' ' . $n($y + $h) . " l\n";
        $p .= $n($x + $r - $k) . ' ' . $n($y + $h) . ' ' . $n($x) . ' ' . $n($y + $h - $r + $k) . ' ' . $n($x) . ' ' . $n($y + $h - $r) . " c\n";
        $p .= $n($x) . ' ' . $n($y + $r) . " l\n";
        $p .= $n($x) . ' ' . $n($y + $r - $k) . ' ' . $n($x + $r - $k) . ' ' . $n($y) . ' ' . $n($x + $r) . ' ' . $n($y) . " c\nh\n";
        return $p;
    }

    public function rrect(float $x, float $top, float $w, float $h, float $r, string $hex, ?string $alpha = null): void
    {
        $y = $this->h - $top - $h;
        $this->cur .= $this->wrap($this->rgb($hex) . " rg\n" . $this->rrPath($x, $y, $w, $h, $r) . "f\n", $alpha);
    }

    public function rrectStroke(float $x, float $top, float $w, float $h, float $r, string $hex, float $lw = 0.8): void
    {
        $y = $this->h - $top - $h;
        $this->cur .= $this->rgb($hex) . " RG\n" . $this->n($lw) . " w\n" . $this->rrPath($x, $y, $w, $h, $r) . "S\n";
    }

    public function circle(float $cx, float $cyTop, float $r, string $hex, ?string $alpha = null): void
    {
        $cy = $this->h - $cyTop;
        $k = $r * 0.5523;
        $n = fn ($v) => $this->n($v);
        $p = $n($cx + $r) . ' ' . $n($cy) . " m\n";
        $p .= $n($cx + $r) . ' ' . $n($cy + $k) . ' ' . $n($cx + $k) . ' ' . $n($cy + $r) . ' ' . $n($cx) . ' ' . $n($cy + $r) . " c\n";
        $p .= $n($cx - $k) . ' ' . $n($cy + $r) . ' ' . $n($cx - $r) . ' ' . $n($cy + $k) . ' ' . $n($cx - $r) . ' ' . $n($cy) . " c\n";
        $p .= $n($cx - $r) . ' ' . $n($cy - $k) . ' ' . $n($cx - $k) . ' ' . $n($cy - $r) . ' ' . $n($cx) . ' ' . $n($cy - $r) . " c\n";
        $p .= $n($cx + $k) . ' ' . $n($cy - $r) . ' ' . $n($cx + $r) . ' ' . $n($cy - $k) . ' ' . $n($cx + $r) . ' ' . $n($cy) . " c\nf\n";
        $this->cur .= $this->wrap($this->rgb($hex) . " rg\n" . $p, $alpha);
    }

    /** bintang 4 sudut */
    public function star(float $cx, float $cyTop, float $r, string $hex, ?string $alpha = null): void
    {
        $cy = $this->h - $cyTop;
        $i = $r * 0.22;
        $pts = [[0, $r], [$i, $i], [$r, 0], [$i, -$i], [0, -$r], [-$i, -$i], [-$r, 0], [-$i, $i]];
        $p = '';
        foreach ($pts as $k => $pt) {
            $p .= $this->n($cx + $pt[0]) . ' ' . $this->n($cy + $pt[1]) . ($k === 0 ? " m\n" : " l\n");
        }
        $this->cur .= $this->wrap($this->rgb($hex) . " rg\n" . $p . "h f\n", $alpha);
    }

    /** gradasi horizontal lewat potongan tipis */
    public function gradientH(float $x, float $top, float $w, float $h, array $stops, int $steps = 56): void
    {
        $cols = array_map(fn ($s) => [hexdec(substr(ltrim($s, '#'), 0, 2)), hexdec(substr(ltrim($s, '#'), 2, 2)), hexdec(substr(ltrim($s, '#'), 4, 2))], $stops);
        $seg = count($cols) - 1;
        $sw = $w / $steps;
        for ($i = 0; $i < $steps; $i++) {
            $t = $i / max(1, $steps - 1) * $seg;
            $a = min((int) floor($t), $seg - 1);
            $f = $t - $a;
            $c = [];
            for ($j = 0; $j < 3; $j++) {
                $c[] = (int) round($cols[$a][$j] + ($cols[$a + 1][$j] - $cols[$a][$j]) * $f);
            }
            $this->rect($x + $i * $sw, $top, $sw + 0.6, $h, sprintf('%02x%02x%02x', $c[0], $c[1], $c[2]));
        }
    }

    private function enc(string $s): string
    {
        $r = function_exists('iconv') ? @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $s) : false;
        if ($r === false || $r === null) {
            $r = function_exists('mb_convert_encoding') ? @mb_convert_encoding($s, 'ISO-8859-1', 'UTF-8') : preg_replace('/[^\x20-\x7E]/', '?', $s);
        }
        return (string) $r;
    }

    public function textWidth(string $s, float $size, bool $bold = false): float
    {
        $t = $bold ? self::BOLD : self::REG;
        $sum = 0;
        $e = $this->enc($s);
        for ($i = 0, $l = strlen($e); $i < $l; $i++) {
            $o = ord($e[$i]);
            $sum += ($o >= 32 && $o <= 126) ? $t[$o - 32] : 556;
        }
        return $sum * $size / 1000;
    }

    public function fit(string $s, float $size, bool $bold, float $maxW): string
    {
        $s = trim(preg_replace('/\s+/', ' ', $s));
        if ($this->textWidth($s, $size, $bold) <= $maxW) {
            return $s;
        }
        while ($s !== '' && $this->textWidth($s . '...', $size, $bold) > $maxW) {
            $s = function_exists('mb_substr') ? mb_substr($s, 0, mb_strlen($s) - 1) : substr($s, 0, -1);
        }
        return rtrim($s) . '...';
    }

    /** $align: l, c, r. $baseTop = posisi garis dasar teks dari atas halaman */
    public function text(float $x, float $baseTop, string $s, float $size, string $hex = '2d2a4a', bool $bold = false, string $align = 'l', ?string $alpha = null): void
    {
        if ($s === '') {
            return;
        }
        $wd = $this->textWidth($s, $size, $bold);
        if ($align === 'c') {
            $x -= $wd / 2;
        } elseif ($align === 'r') {
            $x -= $wd;
        }
        $e = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $this->enc($s));
        $body = $this->rgb($hex) . " rg\nBT /" . ($bold ? 'F2' : 'F1') . ' ' . $this->n($size) . ' Tf ' . $this->n($x) . ' ' . $this->n($this->h - $baseTop) . ' Td (' . $e . ") Tj ET\n";
        $this->cur .= $this->wrap($body, $alpha);
    }

    public function line(float $x1, float $top1, float $x2, float $top2, string $hex, float $lw = 0.6): void
    {
        $this->cur .= $this->rgb($hex) . " RG\n" . $this->n($lw) . ' w ' . $this->n($x1) . ' ' . $this->n($this->h - $top1) . ' m ' . $this->n($x2) . ' ' . $this->n($this->h - $top2) . " l S\n";
    }

    public function output(string $title = 'Dokumen'): string
    {
        if ($this->started) {
            $this->pages[] = $this->cur;
            $this->started = false;
        }
        if (!$this->pages) {
            $this->pages[] = '';
        }

        $objs = [];
        $np = count($this->pages);
        $kids = [];
        for ($i = 0; $i < $np; $i++) {
            $kids[] = (6 + $i * 2) . ' 0 R';
        }
        $objs[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objs[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . $np . ' >>';
        $objs[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objs[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        $objs[5] = '<< /Title (' . str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $this->enc($title)) . ') /Producer (Trafie-library) >>';

        $res = '<< /Font << /F1 3 0 R /F2 4 0 R >> /ExtGState << /G1 << /ca 0.14 >> /G2 << /ca 0.32 >> /G3 << /ca 0.55 >> /G4 << /ca 0.85 >> >> >>';
        foreach ($this->pages as $i => $stream) {
            $pid = 6 + $i * 2;
            $cid = $pid + 1;
            $objs[$pid] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . $this->n($this->w) . ' ' . $this->n($this->h) . '] /Resources ' . $res . ' /Contents ' . $cid . ' 0 R >>';
            if (function_exists('gzcompress')) {
                $z = gzcompress($stream, 6);
                $objs[$cid] = '<< /Length ' . strlen($z) . " /Filter /FlateDecode >>\nstream\n" . $z . "\nendstream";
            } else {
                $objs[$cid] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
            }
        }

        $out = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $off = [];
        ksort($objs);
        foreach ($objs as $id => $body) {
            $off[$id] = strlen($out);
            $out .= $id . " 0 obj\n" . $body . "\nendobj\n";
        }
        $max = max(array_keys($objs));
        $xref = strlen($out);
        $out .= "xref\n0 " . ($max + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= $max; $i++) {
            $out .= sprintf("%010d 00000 n \n", $off[$i] ?? 0);
        }
        $out .= "trailer\n<< /Size " . ($max + 1) . " /Root 1 0 R /Info 5 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
        return $out;
    }
}