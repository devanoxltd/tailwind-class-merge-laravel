<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Here you may specify the cache driver that should be used to cache
    | the merged classes. You can set it to false to disable caching
    | entirely. You can also use any other cache driver supported by Laravel.
    | To use default cache driver, set it to null.
    |
    */
    'cache' => env('TAILWIND_MERGE_CACHE', null),

    /*
    |--------------------------------------------------------------------------
    | Prefix
    |--------------------------------------------------------------------------
    |
    | If you are using a tailwind prefix, you can specify it here.
    */

    'prefix' => env('TAILWIND_MERGE_PREFIX', null),

    /*
    |--------------------------------------------------------------------------
    | Class groups
    |--------------------------------------------------------------------------
    |
    | If TailwindMerge is not able to merge your changes properly you can
    | modify the merge process by modifying existing class groups or adding
    | new class groups.
    |
    | For example, if you want to add a custom font size of 'very-large':
    | 'classGroups' => [
    |     'font-size' => [
    |         ['text' => ['very-large']]
    |     ],
    | ],
    */

    'classGroups' => [],

    /*
    |--------------------------------------------------------------------------
    | Blade directive
    |--------------------------------------------------------------------------
    |
    | Here you may specify the name of the blade directive that will be used.
    | Set it to null to entirely disable the blade directive.
    */

    'blade_directive' => 'tailwindClass',
];
