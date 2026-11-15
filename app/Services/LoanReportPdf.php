<?php

namespace App\Services;

class LoanReportPdf
{
    private const ROW_H = 21;
    private const HEAD_H = 24;
    private const TOTAL_H = 24;
    private const SIG_H = 112;
    private const GAP = 20;
    private const LIMIT = 553;

    /**
     * $rows: array of ['name','nisn','title','code','requested','due','returned','status','label','fine','fine_note']
     * $meta: ['app','filters','periode','statusLabel','generated','dateLong','docno','petugas','total','borrowed','returned','fine','unpaid']
     */
    public static function render(array $rows, array $meta): string
    {
        $pdf = new SimplePdf();
        $n = count($rows);

        $cols = [
            ['No', 28, 'c'], ['Nama Siswa', 112, 'l'], ['NISN', 66, 'l'], ['Judul Buku', 160, 'l'], ['Kode', 66, 'l'],
            ['Diajukan', 62, 'c'], ['Jatuh Tempo', 62, 'c'], ['Dikembalikan', 62, 'c'], ['Status', 62, 'c'], ['Denda', 106, 'r'],
        ];
        $x0 = 28;
        $tw = 0;
        foreach ($cols as $c) {
            $tw += $c[1];
        }

        // rencana halaman: [mulai, jumlah baris, apakah memuat total+tanda tangan]
        $plan = [];
        $i = 0;
        $p = 0;
        while (true) {
            $start = $p === 0 ? 196 : 58;
            $capFull = (int) floor((self::LIMIT - $start - self::HEAD_H) / self::ROW_H);
            $capLast = (int) floor((self::LIMIT - $start - self::HEAD_H - self::TOTAL_H - self::GAP - self::SIG_H) / self::ROW_H);
            $remain = $n - $i;
            if ($remain <= $capLast) {
                $plan[] = [$start, $remain, true, true];
                break;
            }
            if ($remain <= $capFull) {
                $plan[] = [$start, $remain, false, true];
                $plan[] = [58, 0, true, false];
                break;
            }
            $plan[] = [$start, $capFull, false, true];
            $i += $capFull;
            $p++;
        }
        $pages = count($plan);

        $i = 0;
        foreach ($plan as $pi => [$start, $cnt, $closing, $hasTable]) {
            $pdf->addPage();
            self::background($pdf, $pi);

            if ($pi === 0) {
                self::headerBig($pdf, $meta, $x0, $tw);
            } else {
                self::headerSmall($pdf, $meta, $x0, $tw);
            }

            $y = $start;
            if ($hasTable) {
                self::tableHead($pdf, $cols, $x0, $y, $tw);
                $y += self::HEAD_H;
                if ($n === 0) {
                    $pdf->rect($x0, $y, $tw, 40, 'ffffff');
                    $pdf->text($x0 + $tw / 2, $y + 24, 'Tidak ada data untuk filter ini.', 9, '8b87a8', false, 'c');
                    self::grid($pdf, $cols, $x0, $y, 40);
                    $y += 40;
                }
                for ($k = 0; $k < $cnt; $k++, $i++) {
                    self::row($pdf, $rows[$i], $i + 1, $cols, $x0, $y, $tw, $i % 2 === 1);
                    $y += self::ROW_H;
                }
            }

            if ($closing) {
                if ($hasTable) {
                    self::totalRow($pdf, $meta, $n, $cols, $x0, $y, $tw);
                    $y += self::TOTAL_H + self::GAP;
                } else {
                    $y += 4;
                    $pdf->text($x0, $y + 8, 'Rekapitulasi dan Pengesahan', 11, '3b2f86', true);
                    $pdf->text($x0, $y + 22, 'Jumlah data: ' . $n . '  |  Total denda: ' . $meta['fine'] . '  |  Belum dibayar: ' . $meta['unpaid'], 8.5, '6b6788');
                    $y += 40;
                }
                self::signature($pdf, $meta, $x0, $tw, $y);
            }

            // kaki halaman
            $pdf->line($x0, $pdf->h - 34, $x0 + $tw, $pdf->h - 34, 'd9cff3', 0.8);
            $pdf->text($x0, $pdf->h - 20, $meta['app'] . '  |  Laporan Peminjaman Buku  |  ' . $meta['docno'], 7.5, '8b87a8');
            $pdf->text($x0 + $tw, $pdf->h - 20, 'Halaman ' . ($pi + 1) . ' dari ' . $pages, 7.5, '8b87a8', false, 'r');
        }

        return $pdf->output('Laporan Peminjaman Buku - ' . $meta['app']);
    }

    private static function background(SimplePdf $pdf, int $pi): void
    {
        $pdf->rect(0, 0, $pdf->w, $pdf->h, 'faf7ff');
        $pdf->circle($pdf->w - 30, 20, 150, 'e9defc', 'G3');
        $pdf->circle(20, $pdf->h - 10, 120, 'ffe0ef', 'G3');
        $pdf->circle($pdf->w / 2, $pdf->h + 40, 110, 'dcecff', 'G2');
        $pdf->star($pdf->w - 54, 130, 7, 'c9b3f5', 'G4');
        $pdf->star($pdf->w - 80, 150, 4, 'f4b6d2', 'G4');
        $pdf->star(46, $pdf->h - 70, 6, 'c9b3f5', 'G3');
        $pdf->star(70, $pdf->h - 52, 3.5, 'f4b6d2', 'G4');
        $pdf->star($pdf->w - 46, $pdf->h - 90, 5, 'b9d6f5', 'G4');
    }

    private static function headerBig(SimplePdf $pdf, array $m, float $x0, float $tw): void
    {
        // kop: logo + nama lembaga
        $pdf->circle($x0 + 26, 46, 23, 'e5dcff');
        $pdf->circle($x0 + 26, 46, 19, '7c5ce0');
        $pdf->circle($x0 + 26, 46, 19, 'f06fb0', 'G2');
        $pdf->star($x0 + 26, 46, 11, 'ffffff');
        $pdf->star($x0 + 38, 34, 4, 'ffffff', 'G4');

        $pdf->text($x0 + 60, 43, strtoupper($m['app']), 19, '4b3a9f', true);
        $pdf->text($x0 + 60, 58, 'Perpustakaan Sekolah  -  Sistem Informasi Peminjaman Buku', 8.5, '7b7796');
        $pdf->text($x0 + $tw, 38, 'No. Dokumen', 7, '9a96b8', false, 'r');
        $pdf->text($x0 + $tw, 49, $m['docno'], 8.5, '4b3a9f', true, 'r');
        $pdf->text($x0 + $tw, 61, 'Dicetak ' . $m['generated'], 7.5, '7b7796', false, 'r');

        $pdf->gradientH($x0, 72, $tw, 3.6, ['7c5ce0', 'b560d8', 'f06fb0']);
        $pdf->rect($x0, 77.4, $tw, 0.7, 'd9cff3');

        // judul
        $cx = $x0 + $tw / 2;
        $pdf->text($cx, 103, 'LAPORAN PEMINJAMAN BUKU', 16, '3b2f86', true, 'c');
        $pdf->gradientH($cx - 90, 109, 180, 2.4, ['7c5ce0', 'b560d8', 'f06fb0'], 30);
        $pdf->star($cx - 140, 98, 5, 'b560d8', 'G4');
        $pdf->star($cx + 140, 98, 5, 'f06fb0', 'G4');
        $pdf->text($cx, 124, 'Periode: ' . $m['periode'] . '   |   Status: ' . $m['statusLabel'], 9, '5b5780', false, 'c');
        $pdf->text($cx, 137, 'Rekapitulasi data peminjaman buku perpustakaan berdasarkan filter di atas.', 7.5, '9a96b8', false, 'c');

        // kartu ringkasan
        $cards = [
            ['Total Pengajuan', (string) $m['total'], 'sesuai filter', 'ece6ff', '6a4fd0'],
            ['Dipinjam', (string) $m['borrowed'], 'belum kembali', 'dff0fc', '2a6fb0'],
            ['Dikembalikan', (string) $m['returned'], 'selesai', 'dcf5ea', '1d7a58'],
            ['Total Denda', $m['fine'], 'belum dibayar ' . $m['unpaid'], 'ffe3ef', 'c2415d'],
        ];
        $gap = 12;
        $cw = ($tw - $gap * 3) / 4;
        foreach ($cards as $k => $c) {
            $cx2 = $x0 + $k * ($cw + $gap);
            $cy = 148;
            $pdf->rrect($cx2 + 1.5, $cy + 3, $cw, 36, 10, 'cdbff5', 'G2');
            $pdf->rrect($cx2, $cy, $cw, 36, 10, 'ffffff');
            $pdf->rrect($cx2, $cy, 5, 36, 2.5, $c[4]);
            $pdf->rrect($cx2 + 12, $cy + 7, 22, 22, 7, $c[3]);
            $pdf->star($cx2 + 23, $cy + 18, 6.5, $c[4]);
            $pdf->text($cx2 + 42, $cy + 14, $c[0], 7.5, '7b7796');
            $pdf->text($cx2 + 42, $cy + 29, $c[1], $k === 3 ? 12 : 14, '2d2a4a', true);
            $pdf->text($cx2 + $cw - 10, $cy + 14, $c[2], 6.6, '9a96b8', false, 'r');
        }
    }

    private static function headerSmall(SimplePdf $pdf, array $m, float $x0, float $tw): void
    {
        $pdf->gradientH($x0, 22, $tw, 24, ['7c5ce0', 'b560d8', 'f06fb0']);
        $pdf->star($x0 + 14, 34, 6, 'ffffff', 'G4');
        $pdf->text($x0 + 28, 38.5, 'LAPORAN PEMINJAMAN BUKU', 10.5, 'ffffff', true);
        $pdf->text($x0 + $tw - 12, 38, 'Periode: ' . $m['periode'] . '  |  Status: ' . $m['statusLabel'], 7.5, 'ffffff', false, 'r', 'G4');
    }

    /** garis kotak ala Excel untuk satu baris */
    private static function grid(SimplePdf $pdf, array $cols, float $x0, float $y, float $h): void
    {
        $x = $x0;
        $pdf->line($x0, $y + $h, $x0 + array_sum(array_column($cols, 1)), $y + $h, 'cfc4ee', 0.6);
        $pdf->line($x, $y, $x, $y + $h, 'cfc4ee', 0.6);
        foreach ($cols as $c) {
            $x += $c[1];
            $pdf->line($x, $y, $x, $y + $h, 'cfc4ee', 0.6);
        }
    }

    private static function tableHead(SimplePdf $pdf, array $cols, float $x0, float $top, float $tw): void
    {
        $pdf->gradientH($x0, $top, $tw, self::HEAD_H, ['6d4fd6', 'b05ad8', 'ef6bb0']);
        $x = $x0;
        foreach ($cols as $k => $c) {
            $tx = $c[2] === 'r' ? $x + $c[1] - 8 : ($c[2] === 'c' ? $x + $c[1] / 2 : $x + 8);
            $pdf->text($tx, $top + 15.4, strtoupper($c[0]), 7, 'ffffff', true, $c[2]);
            if ($k > 0) {
                $pdf->line($x, $top, $x, $top + self::HEAD_H, 'ffffff', 0.5);
            }
            $x += $c[1];
        }
    }

    private static function row(SimplePdf $pdf, array $r, int $no, array $cols, float $x0, float $y, float $tw, bool $odd): void
    {
        $pdf->rect($x0, $y, $tw, self::ROW_H, $odd ? 'f3eeff' : 'ffffff');
        self::grid($pdf, $cols, $x0, $y, self::ROW_H);

        $cells = [(string) $no, $r['name'], $r['nisn'] ?: '-', $r['title'], $r['code'] ?: '-', $r['requested'], $r['due'], $r['returned'], $r['label'], $r['fine']];
        $x = $x0;
        foreach ($cols as $k => $c) {
            $pad = 8;
            $maxW = $c[1] - $pad * 2;
            $cx = $c[2] === 'r' ? $x + $c[1] - $pad : ($c[2] === 'c' ? $x + $c[1] / 2 : $x + $pad);
            $main = $cells[$k];

            if ($k === 8) {
                $st = [
                    'Pending' => ['fff0cc', '8a6100'], 'Borrowed' => ['dff0fc', '1f6a9a'], 'Returned' => ['d9f3e5', '2f6b4f'],
                    'Rejected' => ['fee2e2', 'a12c45'], 'Cancelled' => ['e6e4f0', '6b6788'],
                ][$r['status']] ?? ['e6e4f0', '6b6788'];
                $lab = $pdf->fit($main, 6.6, true, $c[1] - 8);
                $pw = min($c[1] - 6, $pdf->textWidth($lab, 6.6, true) + 12);
                $pdf->rrect($cx - $pw / 2, $y + 5, $pw, 11, 5.5, $st[0]);
                $pdf->text($cx, $y + 13.1, $lab, 6.6, $st[1], true, 'c');
                $x += $c[1];
                continue;
            }

            $bold = $k === 1;
            $isFine = $k === 9 && $main !== '-';
            $color = $isFine ? 'c2415d' : ($k === 0 ? '7b7796' : '2d2a4a');
            $pdf->text($cx, $y + 13.6, $pdf->fit($main, 8, $bold || $isFine, $maxW), 8, $color, $bold || $isFine, $c[2]);
            $x += $c[1];
        }
    }

    private static function totalRow(SimplePdf $pdf, array $m, int $n, array $cols, float $x0, float $y, float $tw): void
    {
        $pdf->gradientH($x0, $y, $tw, self::TOTAL_H, ['e5dcff', 'f3d9ee', 'ffe0ef'], 40);
        $lastW = $cols[9][1];
        $pdf->text($x0 + 10, $y + 15.4, 'TOTAL  (' . $n . ' data)', 8, '4b3a9f', true);
        $pdf->text($x0 + $tw - $lastW - 10, $y + 15.4, 'Total Denda', 8, '4b3a9f', true, 'r');
        $pdf->text($x0 + $tw - 8, $y + 15.4, $m['fine'], 8.5, 'c2415d', true, 'r');
        $pdf->line($x0, $y, $x0 + $tw, $y, '9d8bdc', 1);
        $pdf->line($x0, $y + self::TOTAL_H, $x0 + $tw, $y + self::TOTAL_H, '9d8bdc', 1);
        $pdf->line($x0, $y, $x0, $y + self::TOTAL_H, 'cfc4ee', 0.6);
        $pdf->line($x0 + $tw, $y, $x0 + $tw, $y + self::TOTAL_H, 'cfc4ee', 0.6);
        $pdf->line($x0 + $tw - $lastW, $y, $x0 + $tw - $lastW, $y + self::TOTAL_H, 'cfc4ee', 0.6);
    }

    private static function signature(SimplePdf $pdf, array $m, float $x0, float $tw, float $top): void
    {
        $bw = 236;
        $bh = 104;
        $items = [
            [$x0 + 36, 'Mengetahui,', 'Kepala Sekolah', ''],
            [$x0 + $tw - 36 - $bw, $m['dateLong'], 'Petugas Perpustakaan', $m['petugas']],
        ];
        foreach ($items as [$bx, $l1, $l2, $name]) {
            $pdf->rrect($bx + 1.5, $top + 3, $bw, $bh, 14, 'cdbff5', 'G2');
            $pdf->rrect($bx, $top, $bw, $bh, 14, 'ffffff');
            $pdf->rrectStroke($bx, $top, $bw, $bh, 14, 'dccff7', 0.9);
            $cx = $bx + $bw / 2;
            $pdf->text($cx, $top + 18, $l1, 8.5, '5b5780', false, 'c');
            $pdf->text($cx, $top + 30, $l2, 9.5, '3b2f86', true, 'c');
            $pdf->star($bx + 18, $top + 18, 4, 'c9b3f5', 'G4');
            $pdf->star($bx + $bw - 18, $top + 18, 4, 'f4b6d2', 'G4');
            // ruang tanda tangan
            $pdf->text($cx, $top + 58, 'Tanda tangan dan cap', 6.8, 'c2bcdd', false, 'c');
            $nm = $name !== '' ? $pdf->fit($name, 9, true, $bw - 40) : '';
            if ($nm !== '') {
                $pdf->text($cx, $top + 84, $nm, 9, '2d2a4a', true, 'c');
            }
            $pdf->line($bx + 30, $top + 87, $bx + $bw - 30, $top + 87, '8b7fc8', 0.8);
            $pdf->text($cx, $top + 98, 'NIP. ............................................', 7.5, '7b7796', false, 'c');
        }
        $pdf->text($x0 + $tw / 2, $top + 56, 'Dokumen ini dibuat otomatis', 7, '9a96b8', false, 'c');
        $pdf->text($x0 + $tw / 2, $top + 66, 'oleh sistem ' . $m['app'], 7, '9a96b8', false, 'c');
        $pdf->star($x0 + $tw / 2, $top + 40, 6, 'c9b3f5', 'G4');
    }
}