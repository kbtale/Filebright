<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentMetadata extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
        'mime_type',
        'path',
        'status',
        'vector_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
