<p align="center">
    <img src="https://raw.githubusercontent.com/devanoxltd/tailwind-class-merge-laravel/main/art/example.png" width="600" alt="TailwindClassMerge for Laravel">
    <p align="center">
        <a href="https://github.com/devanoxltd/tailwind-class-merge-laravel/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/devanoxltd/tailwind-class-merge-laravel/tests.yml?branch=main&label=tests&style=round-square"></a>
        <a href="https://packagist.org/packages/devanoxltd/tailwind-class-merge-laravel"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/devanoxltd/tailwind-class-merge-laravel"></a>
        <a href="https://packagist.org/packages/devanoxltd/tailwind-class-merge-laravel"><img alt="Latest Version" src="https://img.shields.io/packagist/v/devanoxltd/tailwind-class-merge-laravel"></a>
        <a href="https://packagist.org/packages/devanoxltd/tailwind-class-merge-laravel"><img alt="License" src="https://img.shields.io/github/license/devanoxltd/tailwind-class-merge-laravel"></a>
    </p>
</p>

------

**TailwindClassMerge for Laravel** allows you to merge multiple [Tailwind CSS](https://tailwindcss.com/) classes and automatically resolves conflicts between classes by removing classes conflicting with a class defined later. This is especially helpful when you want to override Tailwind CSS classes in your Blade components.

A Laravel / PHP port of [tailwind-merge](https://github.com/dcastil/tailwind-merge) by [dcastil](https://github.com/dcastil).

Supports Tailwind v3.0 up to v3.3.

If you find this package helpful, please consider sponsoring the maintainer:
- Devanox Private Limited: **[github.com/sponsors/devanoxltd](https://github.com/sponsors/devanoxltd)**

> If you are **NOT** using Laravel, you can use the [TailwindClassMerge for PHP](https://github.com/devanoxltd/tailwind-class-merge-php) directly.

## Table of Contents
- [Get Started](#get-started)
- [Usage](#usage)
  - [Laravel Blade Components](#use-in-laravel-blade-components)
  - [Laravel Blade Directive](#use-laravel-blade-directive)
  - [Everywhere else in Laravel](#everywhere-else-in-laravel)
- [Configuration](#configuration)
  - [Custom Tailwind Config](#custom-tailwind-config)
- [Contributing](#contributing)

## Get Started
> **Requires [Laravel 10](https://github.com/laravel/laravel)**

First, install `TailwindClassMerge for Laravel` via the [Composer](https://getcomposer.org/) package manager:

```bash
composer require devanoxltd/tailwind-class-merge-laravel
```

Optionally, publish the configuration file:

```bash
php artisan vendor:publish --provider="TailwindClassMerge\Laravel\TailwindClassMergeServiceProvider"
```

This will create a `config/tailwind-class-merge.php` configuration file in your project, which you can modify to your needs
using environment variables. For more information, see the [Configuration](#configuration) section:

```env
TAILWIND_MERGE_PREFIX=tw-
```

Finally, you may use `TailwindClassMerge` in various places like your Blade components:

```php
// circle.blade.php
<div {{ $attributes->tailwindClass('w-10 h-10 rounded-full bg-red-500') }}></div>

// your-view.blade.php
<x-circle class="bg-blue-500" />

// output
<div class="w-10 h-10 rounded-full bg-blue-500"></div>
```

`TailwindClassMerge` is not only capable of resolving conflicts between basic Tailwind CSS classes, but also handles more complex scenarios:

```php
use TailwindClassMerge\Laravel\Facades\TailwindClassMerge;

// conflicting classes
TailwindClassMerge::merge('block inline'); // inline
TailwindClassMerge::merge('pl-4 px-6'); // px-6

// non-conflicting classes
TailwindClassMerge::merge('text-xl text-black'); // text-xl text-black

// with breakpoints
TailwindClassMerge::merge('h-10 lg:h-12 lg:h-20'); // h-10 lg:h-20

// dark mode
TailwindClassMerge::merge('text-black dark:text-white dark:text-gray-700'); // text-black dark:text-gray-700

// with hover, focus and other states
TailwindClassMerge::merge('hover:block hover:inline'); // hover:inline

// with the important modifier
TailwindClassMerge::merge('!font-medium !font-bold'); // !font-bold

// arbitrary values
TailwindClassMerge::merge('z-10 z-[999]'); // z-[999]

// arbitrary variants
TailwindClassMerge::merge('[&>*]:underline [&>*]:line-through'); // [&>*]:line-through

// non tailwind classes
TailwindClassMerge::merge('non-tailwind-class block inline'); // non-tailwind-class inline
```

It's possible to pass the classes as a string, an array or a combination of both:

```php
TailwindClassMerge::merge('h-10 h-20'); // h-20
TailwindClassMerge::merge(['h-10', 'h-20']); // h-20
TailwindClassMerge::merge(['h-10', 'h-20'], 'h-30'); // h-30
TailwindClassMerge::merge(['h-10', 'h-20'], 'h-30', ['h-40']); // h-40
```

## Usage

For in depth documentation and general PHP examples, take a look at the [devanoxltd/tailwind-class-merge-php](https://github.com/devanoxltd/tailwind-class-merge-php) repository.

### Use in Laravel Blade Components

Create your Blade components as you normally would, but instead of specifying the `class` attribute directly, use the `mergeClasses` method:

```php
// circle.blade.php
<div {{ $attributes->tailwindClass('w-10 h-10 rounded-full bg-red-500') }}></div>
```

Now you can use your Blade components and pass additional classes to merge:

```php
// your-view.blade.php
<x-circle class="bg-blue-500" />
```

This will render the following HTML:

```html
<div class="w-10 h-10 rounded-full bg-blue-500"></div>
```

> **Note:** Usage of `$attributes->merge(['class' => '...'])` is currently not supported due to limitations in Laravel.

#### Merge classes on multiple elements
By default Laravel allows you to only merge classes in one place. But with `TailwindClassMerge` you can merge classes on multiple elements by using `forAttributes()`:

```blade
// button.blade.php
<button {{ $attributes->withoutForAttributes()->tailwindClass('p-2 bg-gray-900 text-white') }}>
    <svg {{ $attributes->forAttributes('icon')->tailwindClass('h-4 text-gray-500') }} viewBox="0 0 448 512"><path d="..."/></svg>

    {{ $slot }}
</button>
```

You can now specify additional classes for the button and the svg icon:

```blade
// your-view.blade.php
<x-button class="bg-blue-900" component:icon:class="text-blue-500">
  Click Me
</x-button>
```

This will render the following HTML:

```html
<button class="p-2 bg-blue-900 text-white">
  <svg class="h-4 text-blue-500" viewBox="0 0 448 512"><path d="..."/></svg>

  Click Me
</button>
```

> Note: Use `withoutForAttributes()` on your main attributes bag, otherwise all `component:xyz:class` attributes will be rendered in the output.


If you want to rename the blade component prefix, you can do so in the `config/tailwind-class-merge.php` configuration file:

```php
// config/tailwind-class-merge.php
return [
    'attribute_prefix' => 'component:',
];
```

### Use Laravel Blade Directive
The package registers a Blade directive which can be used to merge classes in your Blade views:

```php
@tailwindClass('w-10 h-10 rounded-full bg-red-500 bg-blue-500') // class="w-10 h-10 rounded-full bg-blue-500"

// or multiple arguments
@tailwindClass('w-10 h-10 rounded-full bg-red-500', 'bg-blue-500') // class="w-10 h-10 rounded-full bg-blue-500"
```

If you want to rename the blade directive, you can do so in the `config/tailwind-class-merge.php` configuration file:

```php
// config/tailwind-class-merge.php
return [
    'blade_directive' => 'customTwMerge',
];
```

You could even disable the directive completely by setting it to `null`:

```php
// config/tailwind-class-merge.php
return [
    'blade_directive' => null,
];
```

### Everywhere else in Laravel
If you don't use Laravel Blade, you can still use `TailwindClassMerge` by using the Facade or the helper method directly:

#### Facade
```php
use TailwindClassMerge\Laravel\Facades\TailwindClassMerge;

TailwindClassMerge::merge('w-10 h-10 rounded-full bg-red-500 bg-blue-500'); // w-10 h-10 rounded-full bg-blue-500
```

#### Helper Method
```php
tailwindClass('w-10 h-10 rounded-full bg-red-500 bg-blue-500'); // w-10 h-10 rounded-full bg-blue-500
```

### More usage examples
Take a look at the [TailwindClassMerge for PHP](https://github.com/devanoxltd/tailwind-class-merge-php) repository.

## Configuration

If you are using Tailwind CSS without any extra config, you can use TailwindClassMerge right away. And stop reading here.

If you're using a custom Tailwind config, you may need to configure TailwindClassMerge as well to merge classes properly.

By default TailwindClassMerge is configured in a way that you can still use it if all the following apply to your Tailwind config:

- Only using color names which don't clash with other Tailwind class names
- Only deviating by number values from number-based Tailwind classes
- Only using font-family classes which don't clash with default font-weight classes
- Sticking to default Tailwind config for everything else

If some of these points don't apply to you, you need to customize the configuration.

### Configure Prefix

You can configure the prefix directly in the `config/tailwind-class-merge.php` configuration file or by setting the environment variable:

```env
TAILWIND_MERGE_PREFIX=tw-
```

### Modify merge process

If TailwindClassMerge is not able to merge your changes properly you can modify the merge process by modifying existing class groups or adding new class groups.

For example, if you want to add a custom font size of "very-large":

```php
// config/tailwind-class-merge.php

return [
  'classGroups' => [
      'font-size' => [
          ['text' => ['very-large']]
      ],
  ],
];
```


For a more detailed explanation of the configuration options, visit the [original package documentation](https://github.com/dcastil/tailwind-merge/blob/v1.14.0/docs/configuration.md).

## Contributing

Thank you for considering contributing to `TailwindClassMerge for Laravel`! The contribution guide can be found in the [CONTRIBUTING.md](CONTRIBUTING.md) file.


---

`TailwindClassMerge for PHP` is an open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.


