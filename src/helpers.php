<?php

declare(strict_types=1);

if (! function_exists('tailwindClass')) {
    /**
     * @param  array<array-key, string|array<array-key, string>>  ...$args
     */
    function tailwindClass(...$args): string
    {
        return app('tailwind-class-merge')->merge(...$args); // @phpstan-ignore-line
    }
}
