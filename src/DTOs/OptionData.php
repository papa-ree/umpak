<?php

namespace Bale\Umpak\DTOs;

/**
 * Snapshot semua options organisasi dalam satu object.
 *
 * Dipakai oleh ViewComposer untuk inject $umpakOrg ke semua view.
 * Key yang tidak ada di database akan bernilai null.
 */
readonly class OptionData
{
    public function __construct(
        public ?string $organizationSlug,
        public ?string $organizationName,
        public ?string $organizationAddress,
        public ?string $organizationPhone,
        public ?string $organizationEmail,
        public ?string $organizationLogo,
        public ?string $url,
        public ?string $socialFacebook,
        public ?string $socialInstagram,
        public ?string $socialTwitter,
        public ?string $socialYoutube,

        /**
         * Semua options mentah — untuk akses key yang belum ada di DTO.
         * Penggunaan: $umpakOrg->raw('key_custom')
         *
         * @var array<string, string|null>
         */
        public array $raw,
    ) {}

    /**
     * Akses option dengan key arbitrary — future-proof tanpa ubah DTO.
     *
     * Penggunaan:
     *   $umpakOrg->raw('key_baru')
     *   $umpakOrg->raw('key_baru', 'fallback')
     */
    public function get(string $key, ?string $default = null): ?string
    {
        return $this->raw[$key] ?? $default;
    }
}
