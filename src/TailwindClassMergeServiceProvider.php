<?php

declare(strict_types=1);

namespace TailwindClassMerge\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use TailwindClassMerge\Contracts\TailwindClassMergeContract;
use TailwindClassMerge\TailwindClassMerge;

class TailwindClassMergeServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app
            ->singleton(
                TailwindClassMergeContract::class,
                static fn (): TailwindClassMerge => tap(TailwindClassMerge::factory()->withConfiguration(config('tailwind-class-merge', [])), function ($factory): void {
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
        ComponentAttributeBag::macro('tailwindClass', function (...$args): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */
            $this->offsetSet('class', resolve(TailwindClassMergeContract::class)->merge($args, ($this->get('class', ''))));

            return $this;
        });

        ComponentAttributeBag::macro('forAttributes', function (string $for, ...$args): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */

            /** @var TailwindClassMergeContract $instance */
            $instance = resolve(TailwindClassMergeContract::class);

            $attribute = 'class' . ($for !== '' ? ':' . $for : '');

            /** @var string $classes */
            $classes = $this->get($attribute, '');

            $this->offsetSet('class', $instance->merge($args, $classes));

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
