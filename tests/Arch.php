<?php

declare(strict_types=1);

test('facades')
    ->expect('TailwindClassMerge\Laravel\Facades\TailwindClassMerge')
    ->toOnlyUse([
        'Illuminate\Support\Facades\Facade',
    ]);

test('service providers')
    ->expect('TailwindClassMerge\Laravel\TailwindClassMergeServiceProvider')
    ->toOnlyUse([
        'Illuminate\Contracts\Support\DeferrableProvider',
        'Illuminate\Support\ServiceProvider',
        'Illuminate\View\Compilers\BladeCompiler',
        'Illuminate\View\ComponentAttributeBag',
        'TailwindClassMerge',

        // helpers...
        'app',
        'config',
        'config_path',
        'resolve',
        'str',
    ]);
