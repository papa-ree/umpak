<?php

namespace Bale\Umpak\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class Icon extends Component
{
    public string $name;
    public string $fallback;

    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $fallback = 'box')
    {
        $this->name = $name;
        $this->fallback = $fallback;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $icon = "lucide-{$this->name}";
        
        if (! $this->componentExists($icon)) {
            $icon = "lucide-{$this->fallback}";
        }

        return view('umpak::components.icon', [
            'iconComponent' => $icon
        ]);
    }

    /**
     * Check if a blade component exists.
     */
    protected function componentExists(string $name): bool
    {
        // 1. Check if it's a registered class component
        $compiler = app(\Illuminate\View\Compilers\BladeCompiler::class);
        if (method_exists($compiler, 'getClassComponentAliases')) {
            $aliases = $compiler->getClassComponentAliases();
            if (isset($aliases[$name])) {
                return true;
            }
        }

        // 2. Check if it's an anonymous component from Blade Icons
        // Blade icons typically registers views like "lucide::components.home"
        $iconName = Str::after($name, 'lucide-');
        if (view()->exists("lucide::components.{$iconName}")) {
            return true;
        }

        return false;
    }
}
