<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingLog extends Model
{
    protected $fillable = ['user_id', 'book_id', 'log_date', 'pages_read'];

    protected function casts(): array
    {
        return ['log_date' => 'date'];
    }
}