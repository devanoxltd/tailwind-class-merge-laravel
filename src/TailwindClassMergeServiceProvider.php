<?php

declare(strict_types=1);

namespace TailwindClassMerge\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use TailwindClassMerge\Contracts\TailwindClassMergeContract;
use TailwindClassMerge\Factory;
use TailwindClassMerge\TailwindClassMerge;

class TailwindClassMergeServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app
            ->singleton(
                TailwindClassMergeContract::class,
                static fn (): TailwindClassMerge => tap(TailwindClassMerge::factory()->withConfiguration(config('tailwind-class-merge', [])), function (Factory $factory): void {
                    $cache = config('tailwind-class-merge.cache');

                    if ($cache !== false) {
                        $factory->withCache(app('cache')->store($cache)); // @phpstan-ignore-line
                    }
                })->make()
            );

        $this->app->alias(TailwindClassMergeContract::class, 'tailwind-class-merge');
        $this->app->alias(TailwindClassMergeContract::class, TailwindClassMerge::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/tailwind-class-merge.php' => config_path('tailwind-class-merge.php'),
            ]);
        }

        $this->registerBladeDirectives();
        $this->registerAttributesBagMacros();
    }

    protected function registerBladeDirectives(): void
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler): void {
            $name = config('tailwind-class-merge.blade_directive', 'tailwindClass');

            if ($name === null) {
                return;
            }

            $bladeCompiler->directive(
                $name,
                fn (?string $expression): string => "<?php echo 'class=\"' . tailwindClass({$expression}) . '\"'; ?>"
            );
        });
    }

    protected function registerAttributesBagMacros(): void
    {
        /** @param array<int, string|array<array-key, string>> ...$args */
        ComponentAttributeBag::macro('tailwindClass', function (...$args): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */

            $existingClass = $this->get('class', '');

            if (! is_string($existingClass)) {
                $existingClass = '';
            }

            /** @var array<int, string|array<array-key, string>> $arguments */
            $arguments = array_merge($args, [$existingClass]);

            $this->offsetSet('class', resolve(TailwindClassMergeContract::class)->merge(...$arguments));

            return $this;
        });

        /** @param array<int, string|array<array-key, string>> ...$args */
        ComponentAttributeBag::macro('forAttributes', function (string $for, ...$args): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */

            /** @var TailwindClassMergeContract $instance */
            $instance = resolve(TailwindClassMergeContract::class);

            $attribute = 'class' . ($for !== '' ? ':' . $for : '');

            $classes = $this->get($attribute, '');

            if (! is_string($classes)) {
                $classes = '';
            }

            /** @var array<int, string|array<array-key, string>> $arguments */
            $arguments = array_merge($args, [$classes]);

            $this->offsetSet('class', $instance->merge(...$arguments));

            return $this->only('class');
        });

        ComponentAttributeBag::macro('withoutForAttributes', function (): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */
            return $this->whereDoesntStartWith('class:');
        });
    }

    /**
     * @return array<class-string<TailwindClassMergeContract>>|string[]
     */
    public function provides(): array
    {
        return [
            TailwindClassMerge::class,
            TailwindClassMergeContract::class,
            'tailwind-class-merge',
        ];
    }
}
