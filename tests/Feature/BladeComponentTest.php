<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Tests\Fixtures\Button;
use Tests\Fixtures\Circle;

describe('tailwindClass', function () {
    it('provides a blade directive to merge tailwind classes', function () {
        Blade::component('circle', Circle::class);

        expect(Blade::render('<x-circle class="bg-blue-500" />'))
            ->toContain('class="w-10 h-10 rounded-full bg-blue-500"')
            ->toMatchSnapshot();
    });
});

describe('forAttributes', function () {
    it('provides a blade directive to merge tailwind classes on a specific element', function () {
        Blade::component('button', Button::class);

        expect(Blade::render('<x-button component:icon:class="text-blue-500" />'))
            ->toContain('class="h-5 w-5 text-blue-500"')
            ->toMatchSnapshot();
    });

    it('provides a blade directive to merge tailwind classes on a specific element with component prefix', function () {
        Config::set('tailwind-class-merge.attribute_prefix', 'mycomponent:');

        Blade::component('button', Button::class);

        expect(Blade::render('<x-button mycomponent:icon:class="text-blue-500" />'))
            ->toContain('class="h-5 w-5 text-blue-500"')
            ->toMatchSnapshot();
    });

    it('does nothing if no classes are provided', function () {
        Blade::component('button', Button::class);

        expect(Blade::render('<x-button />'))
            ->toContain('class="h-5 w-5 text-gray-500"')
            ->toMatchSnapshot();
    });
});

describe('withoutForAttributes', function () {
    it('removes all class attributes that were merged with tailwindClass', function () {
        Blade::component('button', Button::class);

        expect(Blade::render('<x-button class="bg-red-500" component:icon:class="text-blue-500" />'))
            ->toContain('bg-red-500')
            ->not->toContain('icon:class')
            ->toMatchSnapshot();
    });

    it('removes all class attributes that were merged with tailwindClass with component prefix', function () {
        Config::set('tailwind-class-merge.attribute_prefix', 'mycomponent:');

        Blade::component('button', Button::class);

        expect(Blade::render('<x-button class="bg-red-500" mycomponent:icon:class="text-blue-500" />'))
            ->toContain('bg-red-500')
            ->not->toContain('icon:class')
            ->toMatchSnapshot();
    });
});
