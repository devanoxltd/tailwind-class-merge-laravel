<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\Fixtures\Circle;

describe('tailwindClass', function () {
    it('provides a blade directive to merge tailwind classes', function () {
        Blade::component('circle', Circle::class);

        expect(Blade::render('<x-circle class="bg-blue-500" />'))
            ->toContain('class="w-10 h-10 rounded-full bg-blue-500"')
            ->toMatchSnapshot();
    });
});
