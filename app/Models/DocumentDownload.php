<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentDownload extends Model
{
    protected $fillable = ['user_id', 'document_id'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
