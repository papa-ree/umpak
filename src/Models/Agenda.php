<?php

namespace Bale\Umpak\Models;

use Bale\Umpak\DTOs\AgendaData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model Agenda.
 *
 * Tabel dikelola oleh admin panel (project terpisah).
 *
 * Skema tabel:
 * - id            uuid PK
 * - title         varchar
 * - description   text nullable
 * - location      varchar nullable
 * - start_date    timestamp
 * - end_date      timestamp nullable
 * - category      varchar nullable
 * - is_visible    boolean default true
 * - all_day       boolean default false
 * - timezone      varchar nullable
 * - is_recurring  boolean default false
 * - rrule         varchar nullable  — RRULE untuk agenda berulang
 * - created_at    timestamp
 * - updated_at    timestamp
 *
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property string|null $location
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string|null $category
 * @property bool $is_visible
 * @property bool $all_day
 * @property string|null $timezone
 * @property bool $is_recurring
 * @property string|null $rrule
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Agenda extends UmpakModel
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title', 'description', 'location', 'start_date', 'end_date',
        'category', 'is_visible', 'all_day', 'timezone',
        'is_recurring', 'rrule',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_visible' => 'boolean',
        'all_day' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    /**
     * Konversi model ke DTO.
     */
    public function toData(): AgendaData
    {
        return new AgendaData(
            id: $this->id,
            title: $this->title,
            description: $this->description,
            location: $this->location,
            startDate: $this->start_date,
            endDate: $this->end_date,
            category: $this->category,
            isVisible: $this->is_visible,
            allDay: $this->all_day,
            timezone: $this->timezone,
            isRecurring: $this->is_recurring,
            rrule: $this->rrule,
            createdAt: $this->created_at,
            updatedAt: $this->updated_at,
        );
    }

    /**
     * Scope: hanya agenda yang terlihat.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope: agenda yang akan datang (start_date >= sekarang), urut terdekat.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>=', now())
            ->orderBy('start_date');
    }
}
