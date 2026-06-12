<?php

namespace Bale\Umpak\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Navigation.
 *
 * Skema tabel:
 * - id         uuid PK
 * - name       varchar        — label yang ditampilkan
 * - slug       varchar unique
 * - url        varchar nullable — URL eksternal (dipakai jika url_mode = true)
 * - url_mode   boolean nullable — true = URL eksternal, false = link ke page internal
 * - order      integer        — urutan tampil
 * - actived    boolean        — apakah item ini ditampilkan
 * - parent_id  uuid nullable  — referensi ke id di tabel ini (untuk nested menu)
 * - page_slug  uuid nullable  — referensi ke pages.id (dipakai jika url_mode = false)
 * - created_at timestamp
 * - updated_at timestamp
 *
 * @property string       $id
 * @property string       $name
 * @property string       $slug
 * @property string|null  $url
 * @property bool|null    $url_mode
 * @property int          $order
 * @property bool         $actived
 * @property string|null  $parent_id
 * @property string|null  $page_slug
 */
class Navigation extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name', 'slug', 'url', 'url_mode',
        'order', 'actived', 'parent_id', 'page_slug',
    ];

    protected $casts = [
        'url_mode' => 'boolean',
        'actived'  => 'boolean',
        'order'    => 'integer',
    ];

    /**
     * Child items (untuk dropdown/nested menu).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Navigation::class, 'parent_id')
            ->where('actived', true)
            ->orderBy('order');
    }

    /**
     * Parent item.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Navigation::class, 'parent_id');
    }

    /**
     * Page yang dirujuk (jika url_mode = false).
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_slug');
    }

    /**
     * Generate URL yang benar berdasarkan url_mode.
     *
     * - url_mode = true  → gunakan kolom url langsung
     * - url_mode = false → generate dari page slug
     * - url_mode = null  → fallback ke '#'
     */
    public function resolveUrl(): string
    {
        if ($this->url_mode === true) {
            return $this->url ?? '#';
        }

        if ($this->url_mode === false && $this->page_slug) {
            return url("/{$this->page_slug}");
        }

        return $this->url ?? '#';
    }

    /**
     * Scope: hanya item aktif.
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('actived', true);
    }

    /**
     * Scope: hanya root items (tidak punya parent).
     */
    public function scopeRoot(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNull('parent_id');
    }
}
