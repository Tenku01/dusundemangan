<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MethodStatusLog extends Model
{
    public $timestamps = false;

    protected $table = 'method_status_logs';

    protected $fillable = [
        'http_method',
        'route_name',
        'controller_action',
        'path',
        'status_code',
        'duration_ms',
        'memory_kb',
        'user_id',
        'user_name',
        'ip_address',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'duration_ms' => 'integer',
            'memory_kb' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function badgeClassesStatusCode(): string
    {
        if ($this->status_code >= 500) {
            return 'bg-rose-50 text-rose-800 border border-rose-200';
        }

        if ($this->status_code >= 400) {
            return 'bg-amber-50 text-amber-900 border border-amber-200';
        }

        if ($this->status_code >= 300) {
            return 'bg-sky-50 text-sky-800 border border-sky-200';
        }

        return 'bg-emerald-50 text-emerald-800 border border-emerald-200';
    }

    public function badgeClassesHttpMethod(): string
    {
        return match (strtoupper((string) $this->http_method)) {
            'GET' => 'bg-sky-50 text-sky-800 border border-sky-200',
            'POST' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
            'PUT', 'PATCH' => 'bg-emas-100 text-emas-900 border border-emas-300',
            'DELETE' => 'bg-rose-50 text-rose-800 border border-rose-200',
            default => 'bg-slate-100 text-slate-800 border border-slate-200',
        };
    }
}
