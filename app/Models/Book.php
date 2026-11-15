<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'isbn', 'title', 'slug', 'cover', 'author', 'publisher', 'publication_year',
        'pages', 'description', 'category_id', 'language', 'shelf_location',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(BookReview::class)->whereNotNull('approved_at');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover ? asset('storage/' . $this->cover) : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}