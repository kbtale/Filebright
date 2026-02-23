<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DocumentChunk extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'document_chunks';

    protected $fillable = [
        'document_id',
        'content',
        'embedding',
        'metadata',
    ];

    /**
     * Relationship to the document metadata in PostgreSQL.
     */
    public function document()
    {
        return $this->belongsTo(DocumentMetadata::class, 'document_id');
    }
}
