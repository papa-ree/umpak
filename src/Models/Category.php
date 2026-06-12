<?php

namespace Bale\Umpak\Models;

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
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['name', 'slug'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'category_slug', 'slug');
    }
}
