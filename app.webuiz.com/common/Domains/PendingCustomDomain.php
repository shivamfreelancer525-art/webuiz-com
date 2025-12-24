<?php

namespace Common\Domains;

use App\Models\User;
use Common\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingCustomDomain extends BaseModel
{
    public const MODEL_TYPE = 'pendingCustomDomain';

    protected $table = 'custom_domain_queue';

    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'validation_attempts' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isValidating(): bool
    {
        return $this->status === 'validating';
    }

    public function isValid(): bool
    {
        return $this->status === 'valid';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isExpired(): bool
    {
        // Consider expired if older than 1 hour
        return $this->created_at->diffInMinutes(now()) > 60;
    }

    public function canRetry(): bool
    {
        return $this->isPending() && !$this->isExpired() && $this->validation_attempts < 6;
    }

    public static function filterableFields(): array
    {
        return [
            'id',
            'host',
            'user_id',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    public function toNormalizedArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->host,
            'model_type' => self::MODEL_TYPE,
        ];
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'host' => $this->host,
            'status' => $this->status,
            'user' => $this->user ? $this->user->getSearchableValues() : null,
            'created_at' => $this->created_at?->timestamp ?? '_null',
            'updated_at' => $this->updated_at?->timestamp ?? '_null',
        ];
    }

    public static function getModelTypeAttribute(): string
    {
        return self::MODEL_TYPE;
    }
}

