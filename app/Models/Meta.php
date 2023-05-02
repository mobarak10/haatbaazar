<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;
    protected $fillable = [
        'meta_key',
        'meta_value',
        'meta_description'
    ];

    /**
     * get the parent metaable model
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function metaable()
    {
        return $this->morphTo();
    }
}
