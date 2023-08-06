<?php

use Illuminate\Config\Repository;
use TailwindClassMerge\Laravel\Facades\TailwindClassMerge;
use TailwindClassMerge\Laravel\TailwindClassMergeProvider;

it('resolves resources', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'tailwind-class-merge' => [
        ],
    ]));

    (new TailwindClassMergeProvider($app))->register();

    TailwindClassMerge::setFacadeApplication($app);

    expect(TailwindClassMerge::merge('h-4 h-6'))
        ->toBe('h-6');
});
