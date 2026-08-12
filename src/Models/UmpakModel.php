<?php

namespace Bale\Umpak\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model dasar untuk seluruh model Umpak.
 *
 * Koneksi database diambil dari config('umpak.database.connection')
 * dengan default 'mysql'. Dapat diubah via env UMPAK_DB_CONNECTION
 * atau langsung di config/umpak.php.
 */
abstract class UmpakModel extends Model
{
    /**
     * Koneksi database yang dipakai oleh model ini.
     */
    public function getConnectionName(): ?string
    {
        return config('umpak.database.connection', 'mysql');
    }
}
