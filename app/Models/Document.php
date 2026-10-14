<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'academic_area_id', 'subject_id', 'document_type_id', 'title', 'description', 'university', 'career', 'academic_cycle', 'topic', 'original_filename', 'file_path', 'mime_type', 'file_size', 'status', 'views_count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicArea(): BelongsTo
    {
        return $this->belongsTo(AcademicArea::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(DocumentView::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

}
