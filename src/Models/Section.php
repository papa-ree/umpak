<?php

namespace Bale\Umpak\Models;

use Bale\Umpak\DTOs\SectionData;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Section.
 *
 * Tabel dikelola oleh admin panel (project terpisah).
 *
 * Skema tabel:
 * - id         uuid PK
 * - name       varchar nullable  — label untuk admin
 * - slug       varchar nullable  — identifier, contoh: 'banner', 'collection'
 * - type       varchar           — 'core' atau custom type
 * - usage      varchar           — 'general' atau scope penggunaan
 * - content    json nullable     — { meta: {...}, items: [...] }
 * - actived    boolean           — apakah section ditampilkan
 * - created_at timestamp
 * - updated_at timestamp
 *
 * Struktur content.meta mengikuti config cms.sections.*.meta:
 *   - mandatory_meta: title, subtitle, buttons, background
 *   - custom per section type: organization_name, post_limit, dsb
 *
 * Struktur content.items mengikuti config cms.sections.*.items:
 *   - blocks: { title, description, icon }
 *   - metrics: { label, value, suffix }
 *   - dsb
 *
 * @property string                $id
 * @property string|null           $name
 * @property string|null           $slug
 * @property string                $type
 * @property string                $usage
 * @property array<string, mixed>|null $content
 * @property bool                  $actived
 * @property \Carbon\Carbon        $created_at
 * @property \Carbon\Carbon        $updated_at
 */
class Section extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name', 'slug', 'type', 'usage', 'content', 'actived',
    ];

    protected $casts = [
        'content' => 'array',
        'actived' => 'boolean',
    ];

    /**
     * Konversi model ke DTO.
     */
    public function toData(): SectionData
    {
        return new SectionData(
            id: $this->id,
            name: $this->name ?? '',
            slug: $this->slug ?? '',
            type: $this->type,
            usage: $this->usage,
            actived: $this->actived,
            meta: $this->content['meta'] ?? [],
            items: $this->content['items'] ?? [],
        );
    }

    /**
     * Scope: hanya section yang aktif.
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('actived', true);
    }
}
