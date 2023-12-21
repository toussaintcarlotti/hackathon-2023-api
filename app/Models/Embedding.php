<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Embedding extends Model
{
    protected $fillable = ['data', 'model', 'object', 'usage'];

    protected $casts = [
        'data' => 'array',
        'usage' => 'array'
    ];

    /******************************
     *** RELATIONSHIPS
     ******************************/

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
