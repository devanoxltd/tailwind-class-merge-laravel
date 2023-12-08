<?php

declare(strict_types=1);

namespace tests\Fixtures;

use Illuminate\View\Component;

class Button extends Component
{
    public function render()
    {
        return <<<'blade'
            <button type="button" {{ $attributes->withoutTailwindMergeClasses()->merge(['class' => 'p-4']) }}>
                <svg {{ $attributes->tailwindClassFor('icon', 'h-5 w-5 text-gray-500') }}></svg>
            </button>
        blade;
    }
}
