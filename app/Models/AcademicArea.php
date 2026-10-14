<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicArea extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

}
