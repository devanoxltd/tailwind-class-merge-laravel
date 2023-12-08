<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use TailwindClassMerge\TailwindClassMerge;

it('uses caching', function () {
    Event::fake();

    $tailwindClass = $this->app->get(TailwindClassMerge::class);

    expect($tailwindClass->merge('h-4 h-6'))->toBe('h-6');

    Event::assertDispatched(\Illuminate\Cache\Events\CacheMissed::class, 1);
    Event::assertNotDispatched(\Illuminate\Cache\Events\CacheHit::class);

    expect($tailwindClass->merge('h-4 h-6'))->toBe('h-6');

    Event::assertDispatched(\Illuminate\Cache\Events\CacheMissed::class, 1);
    Event::assertDispatched(\Illuminate\Cache\Events\CacheHit::class, 2);
});
