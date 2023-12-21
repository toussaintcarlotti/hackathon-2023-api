<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    protected $fillable = ['type', 'title', 'summary'];

    /******************************
     *** RELATIONSHIPS
     ******************************/
    public function embedding(): HasOne
    {
        return $this->hasOne(Embedding::class);
    }
}
