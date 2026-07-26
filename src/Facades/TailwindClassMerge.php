<?php

declare(strict_types=1);

namespace TailwindClassMerge\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string merge(...$args)
 */
class TailwindClassMerge extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'tailwind-class-merge';
    }
}
