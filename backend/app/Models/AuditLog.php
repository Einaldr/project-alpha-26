<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLog extends Model
{
    /** @use HasFactory<\Database\Factories\AuditLogFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * List of attributes that are mass assignable.
     * 
     * @var list<string>
     */
    protected $fillable = [
        'group_id',
        'user_id',
        'action',
        'target_id',
        'target_type',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    // The target of the action
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    // The group this log entry belongs to
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    // The user who preformed the action
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
