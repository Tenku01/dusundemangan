<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataChangeLog extends Model
{
    public $timestamps = false;

    protected $table = 'data_change_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_peran',
        'aksi',
        'model_type',
        'model_label',
        'model_id',
        'subject_label',
        'old_values',
        'new_values',
        'ip_address',
        'http_method',
        'url',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function labelAksi(): string
    {
        return match ($this->aksi) {
            'created' => 'Tambah Data',
            'updated' => 'Ubah Data',
            'deleted' => 'Hapus Data',
            default => ucfirst((string) $this->aksi),
        };
    }

    public function badgeClassesAksi(): string
    {
        return match ($this->aksi) {
            'created' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
            'updated' => 'bg-emas-100 text-emas-900 border border-emas-300',
            'deleted' => 'bg-rose-50 text-rose-800 border border-rose-200',
            default => 'bg-slate-100 text-slate-800 border border-slate-200',
        };
    }
}
