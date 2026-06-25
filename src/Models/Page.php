<?php

namespace Bale\Umpak\Models;

use Bale\Umpak\Concerns\InteractsWithSeo;
use Bale\Umpak\DTOs\PageData;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Page.
 *
 * Tabel dikelola oleh admin panel (project terpisah).
 *
 * Skema tabel:
 * - id         uuid PK
 * - title      varchar nullable
 * - slug       varchar unique
 * - type       varchar  — default 'paragraph', menentukan renderer yang dipakai
 * - content    json nullable — EditorJS JSON output
 * - created_at timestamp
 * - updated_at timestamp
 *
 * @property string                      $id
 * @property string|null                 $title
 * @property string                      $slug
 * @property string                      $type
 * @property array<string, mixed>|null   $content
 * @property \Carbon\Carbon              $created_at
 * @property \Carbon\Carbon              $updated_at
 */
class Page extends Model
{
    use InteractsWithSeo;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['title', 'slug', 'type', 'content'];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Konversi model ke DTO.
     */
    public function toData(): PageData
    {
        return new PageData(
            id: $this->id,
            slug: $this->slug,
            title: $this->title ?? '',
            type: $this->type,
            content: $this->content,
            updatedAt: $this->updated_at,
            seo: $this->seoMeta,
        );
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
                    $text .= $block['data']['text'] . ' ';
                }
            }
        } elseif (is_string($this->content)) {
            $text = $this->content;
        }

        return \Illuminate\Support\Str::limit(strip_tags($text), $limit);
    }
}
