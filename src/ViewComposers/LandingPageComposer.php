<?php

namespace Bale\Umpak\ViewComposers;

use Bale\Umpak\Contracts\NavigationRepositoryInterface;
use Bale\Umpak\Contracts\OptionRepositoryInterface;
use Illuminate\View\View;

/**
 * View Composer yang menyuntikkan data global ke semua view landing page.
 *
 * Setiap view otomatis menerima:
 *
 * $umpakOrg  : OptionData  — seluruh konfigurasi organisasi dari tabel options
 *              Akses: $umpakOrg->organizationName, $umpakOrg->organizationLogo
 *              Custom key: $umpakOrg->get('key_custom')
 *
 * $umpakNav  : Collection<NavigationData>  — root nav items dari tabel navigations,
 *              beserta children-nya (dropdown)
 *              Akses: @foreach($umpakNav as $item) $item->name, $item->resolvedUrl
 *                     $item->hasChildren(), $item->children
 */
class LandingPageComposer
{
    public function __construct(
        private readonly OptionRepositoryInterface $optionRepository,
        private readonly NavigationRepositoryInterface $navigationRepository,
    ) {}

    public function compose(View $view): void
    {
        $view->with('umpakOrg', $this->safeGetOptions());
        $view->with('umpakNav', $this->safeGetNav());
    }

    private function safeGetOptions(): mixed
    {
        try {
            return $this->optionRepository->all();
        } catch (\Throwable) {
            return null;
        }
    }

    private function safeGetNav(): mixed
    {
        try {
            return $this->navigationRepository->rootWithChildren();
        } catch (\Throwable) {
            return collect();
        }
    }
}
