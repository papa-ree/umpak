<?php

namespace Bale\Umpak\Models;

use Bale\Umpak\Concerns\InteractsWithSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Category.
 *
 * Skema tabel:
 * - id         uuid PK
 * - name       varchar
 * - slug       varchar unique
 * - created_at timestamp
 * - updated_at timestamp
 *
 * @property string $id
 * @property string $name
 * @property string $slug
 */
class Category extends Model
{
    use InteractsWithSeo;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['name', 'slug'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'category_slug', 'slug');
    }

    public function toData(): \Bale\Umpak\DTOs\CategoryData
    {
        return new \Bale\Umpak\DTOs\CategoryData(
            id: $this->id,
            name: $this->name,
            slug: $this->slug,
            seo: $this->seoMeta,
        );
    }
}
