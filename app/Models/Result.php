<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Domain;


class Result extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'keyword',
        'url',
        'target_account',
        'capture',
        'domain_id',
        'validator_id',
        'team_id',
        'validated_at',
        'published_at',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'domain_id' => 'integer',
            'validator_id' => 'integer',
            'team_id' => 'integer',
            'validated_at' => 'timestamp',
            'published_at' => 'datetime',
        ];
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_id');
    }
    public function team(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_id');
    }

    function tracer()
    {
        return $this->belongsTo(Tracer::class);
    }
}
