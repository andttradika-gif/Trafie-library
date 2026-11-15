<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XpTransaction extends Model
{
    protected $fillable = ['user_id', 'amount', 'reason', 'ref', 'note'];

    public const REASONS = ['completion' => 'Buku selesai', 'review' => 'Ulasan', 'rating' => 'Rating', 'new_genre' => 'Genre baru', 'challenge' => 'Tantangan', 'revoke' => 'Dibatalkan admin'];
}