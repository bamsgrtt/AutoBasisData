<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'practical_id',
        'title',
        'description',
        'sql_answer',
        'order',
    ];

    public function practical(): BelongsTo
    {
        return $this->belongsTo(Practical::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(Execution::class);
    }
}
