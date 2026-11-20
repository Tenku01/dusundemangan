<?php

namespace App\Observers;

use App\Models\Barang;
use App\Models\DataChangeLog;
use App\Models\Keuangan;
use App\Models\Peminjaman;
use App\Models\TagihanListrik;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuditModelObserver
{
    private const SENSITIVE_FIELDS = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    private const IGNORED_FIELDS = [
        'updated_at',
        'created_at',
    ];

    private static ?bool $tableAvailable = null;

    public static function resetTableAvailabilityCache(): void
    {
        self::$tableAvailable = null;
    }

    public function created(Model $model): void
    {
        $newValues = $this->sanitizeAttributes($model->getAttributes());

        $this->record('created', $model, null, $newValues);
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        $oldValues = [];
        $newValues = [];

        foreach ($changes as $key => $newVal) {
            if (in_array($key, self::IGNORED_FIELDS, true)) {
                continue;
            }

            $oldVal = $model->getOriginal($key);

            if (in_array($key, self::SENSITIVE_FIELDS, true)) {
                $oldValues[$key] = '***';
                $newValues[$key] = '***';

                continue;
            }

            $oldValues[$key] = $oldVal;
            $newValues[$key] = $newVal;
        }

        if (empty($newValues)) {
            return;
        }

        $this->record('updated', $model, $oldValues, $newValues);
    }

    public function deleted(Model $model): void
    {
        $oldValues = $this->sanitizeAttributes($model->getAttributes());

        $this->record('deleted', $model, $oldValues, null);
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    private function record(string $aksi, Model $model, ?array $oldValues, ?array $newValues): void
    {
        if ($model instanceof DataChangeLog) {
            return;
        }

        try {
            if (self::$tableAvailable === null) {
                self::$tableAvailable = Schema::hasTable('data_change_logs');
            }

            if (! self::$tableAvailable) {
                return;
            }

            $actor = auth()->user();
            $req = app()->bound('request') ? request() : null;

            DataChangeLog::create([
                'user_id' => $actor?->id,
                'user_name' => $actor?->name ?? ($req ? 'Publik / Warga' : 'Sistem (Console)'),
                'user_email' => $actor?->email,
                'user_peran' => $actor?->peran ?? ($req ? 'guest' : 'system'),
                'aksi' => $aksi,
                'model_type' => $model::class,
                'model_label' => $this->resolveModelLabel($model),
                'model_id' => $model->getKey(),
                'subject_label' => $this->resolveSubjectLabel($model),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $req?->ip(),
                'http_method' => $req?->method() ?? 'CLI',
                'url' => $req ? mb_substr((string) $req->fullUrl(), 0, 500) : null,
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Jangan gagalkan operasi bisnis utama bila logging mengalami kendala koneksi/tabel
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function sanitizeAttributes(array $attributes): array
    {
        $clean = [];

        foreach ($attributes as $key => $value) {
            if (in_array($key, self::IGNORED_FIELDS, true)) {
                continue;
            }

            $clean[$key] = in_array($key, self::SENSITIVE_FIELDS, true) ? '***' : $value;
        }

        return $clean;
    }

    private function resolveModelLabel(Model $model): string
    {
        return match ($model::class) {
            TagihanListrik::class => 'Tagihan Listrik',
            Warga::class => 'Data Warga',
            Keuangan::class => 'Buku Kas',
            Barang::class => 'Inventaris Barang',
            Peminjaman::class => 'Peminjaman Barang',
            User::class => 'Akun Pengurus',
            default => class_basename($model),
        };
    }

    private function resolveSubjectLabel(Model $model): string
    {
        return match ($model::class) {
            TagihanListrik::class => trim("{$model->nama_warga} ({$model->periode}) #{$model->nomor_struk}"),
            Warga::class => trim("{$model->nama} (RT {$model->rt} / #{$model->nomor_meter})"),
            Keuangan::class => trim("{$model->kode_transaksi} - {$model->kategori}"),
            Barang::class => trim("{$model->kode_barang} - {$model->nama_barang}"),
            Peminjaman::class => trim("{$model->kode_pinjam} - {$model->nama_peminjam}"),
            User::class => trim("{$model->name} ({$model->email})"),
            default => (string) ($model->getKey() ?? '-'),
        };
    }
}
