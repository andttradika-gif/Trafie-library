<?php

namespace App\Console\Commands;

use App\Services\Reservations;
use Illuminate\Console\Command;

class ExpireReservations extends Command
{
    protected $signature = 'reservations:expire';
    protected $description = 'Kedaluwarsakan reservasi yang lewat batas ambil dan panggil antrean berikutnya';

    public function handle(): int
    {
        $n = Reservations::expireDue();
        $this->info("Reservasi kedaluwarsa: $n");
        return self::SUCCESS;
    }
}