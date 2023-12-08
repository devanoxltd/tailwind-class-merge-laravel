<?php

use Illuminate\Config\Repository;
use TailwindClassMerge\Contracts\TailwindClassMergeContract;
use TailwindClassMerge\Laravel\TailwindClassMergeServiceProvider;
use TailwindClassMerge\TailwindClassMerge;

it('binds the tailwind merge on the container', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'tailwind-class-merge' => [
        ],
    ]));

    (new TailwindClassMergeServiceProvider($app))->register();

    expect($app->get(TailwindClassMerge::class))->toBeInstanceOf(TailwindClassMerge::class);
});

it('binds the client on the container as singleton', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'tailwind-class-merge' => [
        ],
    ]));

    (new TailwindClassMergeServiceProvider($app))->register();

    $tailwindClass = $app->get(TailwindClassMerge::class);

    expect($app->get(TailwindClassMerge::class))->toBe($tailwindClass);
});

it('uses the prefix from the configuration', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'tailwind-class-merge' => [
            'prefix' => 'tw-',
        ],
    ]));

    (new TailwindClassMergeServiceProvider($app))->register();

    $tailwindClass = $app->get(TailwindClassMerge::class);

    expect($tailwindClass->merge('tw-h-4 tw-h-6'))->toBe('tw-h-6');
});

it('provides', function () {
    $app = app();

    $provides = (new TailwindClassMergeServiceProvider($app))->provides();

    expect($provides)->toBe([
        TailwindClassMerge::class,
        TailwindClassMergeContract::class,
        'tailwind-class-merge',
    ]);
});
