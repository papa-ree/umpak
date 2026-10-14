<?php

namespace Bale\Umpak\Models;

use Bale\Umpak\Concerns\InteractsWithSeo;
use Bale\Umpak\DTOs\AnnouncementData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Model Announcement.
 *
 * Tabel dikelola oleh admin panel (project terpisah).
 *
 * Skema tabel:
 * - id          uuid PK
 * - title       varchar
 * - slug        varchar unique
 * - content     json nullable   — EditorJS JSON output
 * - attachment  varchar nullable — nama file di CDN (folder 'attachments/')
 * - is_pinned   boolean default false
 * - published   boolean default false
 * - start_date  timestamp nullable — awal jendela publikasi
 * - end_date    timestamp nullable — akhir jendela publikasi
 * - created_at  timestamp
 * - updated_at  timestamp
 *
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property array<string, mixed>|null $content
 * @property string|null $attachment
 * @property bool $is_pinned
 * @property bool $published
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Announcement extends UmpakModel
{
    use InteractsWithSeo;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title', 'slug', 'content', 'attachment',
        'is_pinned', 'published', 'start_date', 'end_date',
    ];

    protected $casts = [
        'content' => 'array',
        'is_pinned' => 'boolean',
        'published' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Konversi model ke DTO.
     */
    public function toData(): AnnouncementData
    {
        return new AnnouncementData(
            id: $this->id,
            slug: $this->slug,
            title: $this->title,
            excerpt: $this->getExcerpt(),
            content: $this->content,
            attachment: $this->attachment ? cdn_asset('attachments/'.$this->attachment) : null,
            isPinned: $this->is_pinned,
            published: $this->published,
            startDate: $this->start_date,
            endDate: $this->end_date,
            createdAt: $this->created_at,
            updatedAt: $this->updated_at,
            seo: $this->seoMeta,
        );
    }

    /**
     * Scope: hanya pengumuman yang dipublish dan berada dalam jendela publikasi.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true)
            ->where(function (Builder $q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Mendapatkan cuplikan konten untuk SEO.
     */
    public function getExcerpt(int $limit = 150): string
    {
        if (empty($this->content)) {
            return '';
        }

        $text = '';

        if (is_array($this->content) && isset($this->content['blocks'])) {
            foreach ($this->content['blocks'] as $block) {
                if ($block['type'] === 'paragraph' && ! empty($block['data']['text'])) {
                    $text .= $block['data']['text'].' ';
                }
            }
        } elseif (is_string($this->content)) {
            $text = $this->content;
        }

        return Str::limit(strip_tags($text), $limit);
    }
}
