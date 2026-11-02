<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Screenshot extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'execution_id',
        'file_path',
        'thumbnail_path',
    ];

    public function execution(): BelongsTo
    {
        return $this->belongsTo(Execution::class);
    }
}
