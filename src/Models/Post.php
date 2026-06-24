<?php

namespace Bale\Umpak\Models;

use Bale\Umpak\Concerns\InteractsWithSeo;
use Bale\Umpak\DTOs\PostData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Post.
 *
 * Tabel dikelola oleh admin panel (project terpisah).
 *
 * Skema tabel:
 * - id            uuid PK
 * - author        uuid  — referensi ke users.id
 * - title         varchar
 * - slug          varchar unique
 * - content       json nullable   — EditorJS JSON output
 * - thumbnail     varchar nullable — full URL dari CDN
 * - category_slug varchar nullable — referensi ke categories.slug
 * - published     boolean
 * - published_at  timestamp nullable
 * - deleted_at    timestamp nullable (soft delete)
 * - created_at    timestamp
 * - updated_at    timestamp
 *
 * @property string                      $id
 * @property string                      $author
 * @property string                      $title
 * @property string                      $slug
 * @property array<string, mixed>|null   $content
 * @property string|null                 $thumbnail
 * @property string|null                 $category_slug
 * @property bool                        $published
 * @property \Carbon\Carbon|null         $published_at
 * @property \Carbon\Carbon|null         $deleted_at
 * @property \Carbon\Carbon              $created_at
 * @property \Carbon\Carbon              $updated_at
 */
class Post extends Model
{
    use SoftDeletes, InteractsWithSeo;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'author', 'title', 'slug', 'content',
        'thumbnail', 'category_slug', 'published', 'published_at',
    ];

    protected $casts = [
        'content'      => 'array',
        'published'    => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Relasi ke user sebagai author.
     */
    public function authorUser(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model', \App\Models\User::class),
            'author'
        );
    }

    /**
     * Relasi ke category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_slug', 'slug');
    }

    /**
     * Konversi model ke DTO.
     */
    public function toData(): PostData
    {
        return new PostData(
            id: $this->id,
            slug: $this->slug,
            title: $this->title,
            excerpt: null,
            content: $this->content,
            thumbnail: $this->thumbnail,
            categorySlug: $this->category_slug,
            publishedAt: $this->published_at ?? $this->created_at,
            updatedAt: $this->updated_at,
            seo: $this->seoMeta,
        );
    }

    /**
     * Scope: hanya post yang dipublish.
     */
    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
