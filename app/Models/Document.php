<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'file_path',
        'mime_type',
        'size',
        'type',
    ];
    protected $casts = [
        'embedding' => 'array', // This handles the array-to-string conversion for Postgres
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
