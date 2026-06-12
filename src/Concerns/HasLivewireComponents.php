<?php

namespace Bale\Umpak\Concerns;

use Illuminate\Support\Str;
use Livewire\Component as LivewireComponent;
use Livewire\Livewire;
use Symfony\Component\Finder\Finder;

/**
 * Trait untuk registrasi otomatis komponen Livewire class-based.
 */
trait HasLivewireComponents
{
    /**
     * Daftarkan komponen Livewire secara otomatis dari sebuah direktori.
     *
     * @param string $path Direktori file komponen (misal: __DIR__.'/Livewire')
     * @param string $namespace Namespace dasar (misal: 'Bale\\Umpak\\Livewire')
     * @param string $aliasPrefix Prefix untuk alias komponen (misal: 'umpak')
     */
    protected function discoverLivewireComponents(string $path, string $namespace, string $aliasPrefix): void
    {
        if (! is_dir($path)) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in($path)->name(['*.php', '*.blade.php']);

        foreach ($finder as $file) {
            $relativePathname = $file->getRelativePathname();
            $pathName         = $file->getPathname();
            
            // Buat alias berdasarkan struktur folder (kebab-case)
            $withoutExt = Str::replaceLast($file->getExtension() === 'php' && Str::endsWith($relativePathname, '.blade.php') ? '.blade.php' : '.php', '', $relativePathname);
            $segments   = preg_split('#[\/\\\\]#', $withoutExt);
            $kebab      = array_map(fn ($s) => Str::kebab($s), $segments);
            $alias      = $aliasPrefix.'.'.implode('.', $kebab);

            if (Str::endsWith($relativePathname, '.blade.php')) {
                // Handling Livewire 4 Single File Components (SFC)
                // Kita hanya mendaftarkan jika file dimulai dengan <?php (indikasi SFC)
                $content = file_get_contents($pathName);
                if (Str::startsWith(trim($content), '<?php')) {
                    Livewire::addComponent($alias, $pathName);
                }
                continue;
            }

            // Handling Class-based Components
            $nsPath = str_replace(['/', '\\'], '\\', $relativePathname);
            $class  = $namespace.'\\'.Str::beforeLast($nsPath, '.php');

            if (class_exists($class) && is_subclass_of($class, LivewireComponent::class)) {
                Livewire::component($alias, $class);
            }
        }
    }
}
