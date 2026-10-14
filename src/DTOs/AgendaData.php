<?php

namespace Bale\Umpak\DTOs;

use Carbon\Carbon;

/**
 * Representasi data Agenda yang dikembalikan ke landing page package.
 */
readonly class AgendaData
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $description,
        public ?string $location,
        public Carbon $startDate,
        public ?Carbon $endDate,

        /**
         * Kategori agenda bebas, contoh: 'rapat', 'kegiatan', 'libur'.
         */
        public ?string $category,

        public bool $isVisible,
        public bool $allDay,
        public ?string $timezone,
        public bool $isRecurring,

        /**
         * RRULE (RFC 5545) untuk agenda berulang.
         */
        public ?string $rrule,

        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {}

    /**
     * Format tanggal mulai.
     * Contoh: "12 Mei 2025"
     */
    public function formattedDate(string $format = 'd F Y'): string
    {
        return $this->startDate->translatedFormat($format);
    }

    /**
     * Jam mulai. Kosong bila all_day.
     * Contoh: "09:00"
     */
    public function formattedStartTime(string $format = 'H:i'): string
    {
        return $this->allDay ? '' : $this->startDate->translatedFormat($format);
    }

    /**
     * Jam selesai. Kosong bila all_day atau tanpa end_date.
     * Contoh: "15:30"
     */
    public function formattedEndTime(string $format = 'H:i'): string
    {
        if ($this->allDay || $this->endDate === null) {
            return '';
        }

        return $this->endDate->translatedFormat($format);
    }

    /**
     * Rentang tanggal siap tampil untuk agenda multi-hari.
     * Contoh: "12 Mei 2025" atau "12 – 14 Mei 2025".
     */
    public function formattedDateRange(string $format = 'd F Y'): string
    {
        if ($this->endDate === null || $this->endDate->isSameDay($this->startDate)) {
            return $this->formattedDate($format);
        }

        if ($this->startDate->isSameMonth($this->endDate)) {
            return $this->startDate->translatedFormat('d').' – '.$this->endDate->translatedFormat($format);
        }

        return $this->formattedDate($format).' – '.$this->endDate->translatedFormat($format);
    }

    /**
     * Apakah agenda berlangsung lebih dari satu hari.
     */
    public function isMultiDay(): bool
    {
        return $this->endDate !== null && ! $this->endDate->isSameDay($this->startDate);
    }

    /**
     * Apakah agenda sedang berlangsung (berdasarkan start_date/end_date).
     */
    public function isOngoing(): bool
    {
        $now = Carbon::now();

        if ($now->lt($this->startDate)) {
            return false;
        }

        if ($this->endDate === null) {
            return $now->isSameDay($this->startDate);
        }

        return $now->lte($this->endDate);
    }

    /**
     * Apakah agenda sudah berakhir.
     */
    public function hasEnded(): bool
    {
        $end = $this->endDate ?? $this->startDate;

        return Carbon::now()->gt($end);
    }
}
