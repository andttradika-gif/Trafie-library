<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Services\Notice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemindLoans extends Command
{
    protected $signature = 'loans:remind';
    protected $description = 'Kirim pengingat jatuh tempo (3 hari, 1 hari, hari ini, terlambat)';

    public function handle(): int
    {
        $sent = 0;
        $today = today();
        foreach (Loan::with(['user', 'book'])->where('status', 'Borrowed')->whereNotNull('due_date')->get() as $l) {
            if (!$l->user) { continue; }
            $diff = (int) $today->diffInDays($l->due_date->copy()->startOfDay(), false);
            $judul = $l->book->title ?? 'buku';
            if ($diff === 3) { $key = 'due3:' . $l->id; $t = 'Jatuh tempo 3 hari lagi'; $b = '"' . $judul . '" jatuh tempo ' . $l->due_date->format('d M Y') . '.'; }
            elseif ($diff === 1) { $key = 'due1:' . $l->id; $t = 'Jatuh tempo besok'; $b = '"' . $judul . '" harus dikembalikan besok.'; }
            elseif ($diff === 0) { $key = 'due0:' . $l->id; $t = 'Jatuh tempo hari ini'; $b = '"' . $judul . '" harus dikembalikan hari ini.'; }
            elseif ($diff < 0) { $key = 'late:' . $l->id . ':' . $today->format('Ymd'); $t = 'Buku terlambat'; $b = '"' . $judul . '" terlambat ' . abs($diff) . ' hari. Denda berjalan ' . Loan::rupiah($l->current_fine) . '.'; }
            else { continue; }

            $ada = DB::table('notifications')->where('notifiable_id', $l->user_id)->where('data', 'like', '%"key":"' . $key . '"%')->exists();
            if ($ada) { continue; }
            Notice::to($l->user, 'due', $t, $b, route('student.loans.index', [], false), 'clock', $key);
            $sent++;
        }
        $this->info("Pengingat terkirim: $sent");
        return self::SUCCESS;
    }
}