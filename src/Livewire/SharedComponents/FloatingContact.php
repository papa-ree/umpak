<?php

namespace Bale\Umpak\Livewire\SharedComponents;

use Bale\Umpak\Livewire\UmpakComponent;

class FloatingContact extends UmpakComponent
{
    public array $contact = [];

    public string $title = 'Hubungi kami';

    public array $socialLinks = [];

    public array $contactInfos = [];

    public bool $hasContact = false;

    public ?string $borderColor = null;

    public function mount(): void
    {
        $section = $this->section('floating-contact');

        if ($section) {
            $this->contact = [
                'meta' => $section->meta,
                'items' => $section->items,
            ];
            $this->parseData();
        }
    }

    protected function parseData(): void
    {
        $socialPlatforms = config('umpak.social-media', []);
        $items = $this->contact['items'] ?? [];
        $contact = !empty($items) ? $items[0] : [];
        $meta = $this->contact['contact'] ?? $this->contact['meta'] ?? [];
        $order = $meta['order'] ?? ['email', 'phone', 'address'];

        $this->title = $meta['title'] ?? 'Hubungi kami';

        // Helper: ambil nilai pertama dari array atau string
        $val = fn($v) => is_array($v) ? ($v[0] ?? null) : $v;

        // Kumpulkan sosmed yang tersedia
        foreach ($socialPlatforms as $key => $platform) {
            $contactKey = "sm_{$key}";
            $url = $val($contact[$contactKey] ?? null);
            if ($url) {
                $this->socialLinks[$key] = array_merge($platform, ['url' => $url]);
            }
        }

        // Kumpulkan info kontak berdasarkan meta.order
        foreach ($order as $field) {
            // Jangan masukkan key sosial media (berawalan sm_) ke info kontak
            if (str_starts_with($field, 'sm_')) {
                continue;
            }
            $raw = $val($contact[$field] ?? null);
            if ($raw) {
                $this->contactInfos[$field] = $raw;
            }
        }

        $this->hasContact = !empty($this->socialLinks) || !empty($this->contactInfos);
    }

    public function render()
    {
        return view('umpak::livewire.shared-components.floating-contact');
    }
}
