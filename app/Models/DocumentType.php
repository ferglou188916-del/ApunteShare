<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $fillable = ['name', 'is_active'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

}
