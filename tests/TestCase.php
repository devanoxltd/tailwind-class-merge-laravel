<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use TailwindClassMerge\Laravel\TailwindClassMergeServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithViews;

    protected function getPackageProviders($app): array
    {
        return [
            TailwindClassMergeServiceProvider::class,
        ];
    }
}
