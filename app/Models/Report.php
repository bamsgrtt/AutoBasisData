<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'practical_id',
        'score',
        'summary',
        'generated_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    public function practical(): BelongsTo
    {
        return $this->belongsTo(Practical::class);
    }
}
