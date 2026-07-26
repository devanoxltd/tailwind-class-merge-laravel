<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use TailwindClassMerge\Laravel\Facades\TailwindClassMerge;
use TailwindClassMerge\Laravel\TailwindClassMergeServiceProvider;

it('resolves resources', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'tailwind-class-merge' => [
        ],
    ]));

    (new TailwindClassMergeServiceProvider($app))->register();

    TailwindClassMerge::setFacadeApplication($app);

    expect(TailwindClassMerge::merge('h-4 h-6'))
        ->toBe('h-6');
});
