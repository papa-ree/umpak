<?php

namespace Bale\Umpak\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Option — key-value store konfigurasi organisasi.
 *
 * Skema tabel:
 * - id         uuid PK
 * - name       varchar  — key, contoh: 'organization_name'
 * - value      varchar  — nilai
 * - created_at timestamp
 * - updated_at timestamp
 *
 * Key standar yang direkomendasikan:
 *   organization_slug     → slug unik instansi, contoh: 'dinas-pendidikan'
 *   organization_name     → nama lengkap instansi
 *   organization_address  → alamat kantor
 *   organization_phone    → nomor telepon
 *   organization_email    → email resmi
 *   organization_logo     → path logo (diproses via cdn_asset())
 *   url                   → URL publik landing page
 *   social_facebook       → URL Facebook
 *   social_instagram      → URL Instagram
 *   social_twitter        → URL Twitter/X
 *   social_youtube        → URL YouTube
 *
 * Menambah key baru tidak perlu mengubah kode — cukup insert row baru
 * ke tabel options dan akses via Option::getValue('key_baru').
 *
 * @property string      $id
 * @property string      $name
 * @property string|null $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Option extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['name', 'value'];

    /**
     * Ambil nilai option berdasarkan key.
     *
     * Penggunaan:
     *   Option::getValue('organization_name')
     *   Option::getValue('social_facebook', 'https://facebook.com')
     *   Option::getValue('key_baru_apapun')
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::where('name', $key)->value('value') ?? $default;
    }

    /**
     * Ambil banyak options sekaligus sebagai key-value array.
     *
     * Penggunaan:
     *   Option::getMany(['organization_name', 'organization_logo', 'url'])
     *   // → ['organization_name' => 'Dinas Pendidikan', 'organization_logo' => '...', ...]
     *
     * @param  array<int, string> $keys
     * @return array<string, string|null>
     */
    public static function getMany(array $keys): array
    {
        return static::whereIn('name', $keys)
            ->pluck('value', 'name')
            ->toArray();
    }

    /**
     * Ambil semua options sebagai key-value array.
     *
     * @return array<string, string|null>
     */
    public static function allAsArray(): array
    {
        return static::pluck('value', 'name')->toArray();
    }
}
