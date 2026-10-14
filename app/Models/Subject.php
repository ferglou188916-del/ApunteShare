<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = ['academic_area_id', 'name', 'code', 'is_active'];

    public function academicArea(): BelongsTo
    {
        return $this->belongsTo(AcademicArea::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

}
