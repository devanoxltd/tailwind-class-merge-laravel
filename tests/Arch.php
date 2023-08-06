<?php

test('facades')
    ->expect('TailwindClassMerge\Laravel\Facades\TailwindClassMerge')
    ->toOnlyUse([
        'Illuminate\Support\Facades\Facade',
    ]);

test('service providers')
    ->expect('TailwindClassMerge\Laravel\TailwindClassMergeProvider')
    ->toOnlyUse([
        'Illuminate\Contracts\Support\DeferrableProvider',
        'Illuminate\Support\ServiceProvider',
        'Illuminate\View\Compilers\BladeCompiler',
        'Illuminate\View\ComponentAttributeBag',
        'TailwindClassMerge',

        // helpers...
        'config',
        'config_path',
        'resolve',
    ]);
