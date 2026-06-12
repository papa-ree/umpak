<?php

namespace Bale\Umpak\Repositories;

use Bale\Umpak\Contracts\OptionRepositoryInterface;
use Bale\Umpak\DTOs\OptionData;
use Bale\Umpak\Models\Option;

class OptionRepository implements OptionRepositoryInterface
{
    public function all(): OptionData
    {
        $raw = Option::allAsArray();

        return new OptionData(
            organizationSlug: $raw['organization_slug'] ?? null,
            organizationName: $raw['organization_name'] ?? null,
            organizationAddress: $raw['organization_address'] ?? null,
            organizationPhone: $raw['organization_phone'] ?? null,
            organizationEmail: $raw['organization_email'] ?? null,
            organizationLogo: $raw['organization_logo'] ?? null,
            url: $raw['url'] ?? null,
            socialFacebook: $raw['social_facebook'] ?? null,
            socialInstagram: $raw['social_instagram'] ?? null,
            socialTwitter: $raw['social_twitter'] ?? null,
            socialYoutube: $raw['social_youtube'] ?? null,
            raw: $raw,
        );
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return Option::getValue($key, $default);
    }
}
