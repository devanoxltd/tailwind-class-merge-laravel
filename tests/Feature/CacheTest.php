<?php

declare(strict_types=1);

use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Support\Facades\Event;
use TailwindClassMerge\TailwindClassMerge;

it('uses caching', function () {
    Event::fake();

    $tailwindClass = $this->app->get(TailwindClassMerge::class);

    expect($tailwindClass->merge('h-4 h-6'))->toBe('h-6');

    Event::assertDispatched(CacheMissed::class, 1);
    Event::assertNotDispatched(CacheHit::class);

    expect($tailwindClass->merge('h-4 h-6'))->toBe('h-6');

    Event::assertDispatched(CacheMissed::class, 1);
    Event::assertDispatched(CacheHit::class, 2);
});
