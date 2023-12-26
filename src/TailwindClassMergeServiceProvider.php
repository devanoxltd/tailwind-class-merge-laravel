<?php

declare(strict_types=1);

namespace TailwindClassMerge\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use TailwindClassMerge\Contracts\TailwindClassMergeContract;
use TailwindClassMerge\TailwindClassMerge;

class TailwindClassMergeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app
            ->singleton(
                TailwindClassMergeContract::class,
                static fn (): TailwindClassMerge => TailwindClassMerge::factory()
                    ->withConfiguration(config('tailwind-class-merge', []))
                    ->withCache(app('cache')->store()) // @phpstan-ignore-line
                    ->make()
            );

        $this->app->alias(TailwindClassMergeContract::class, 'tailwind-class-merge');
        $this->app->alias(TailwindClassMergeContract::class, TailwindClassMerge::class);

        $this->registerBladeDirectives();
        $this->registerAttributesBagMacros();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/tailwind-class-merge.php' => config_path('tailwind-class-merge.php'),
            ]);
        }
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

        ComponentAttributeBag::macro('for', function (string $for): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */

            $attrBag = new ComponentAttributeBag();
            $forAttributes = [];

            foreach ($this->attributes as $key => $value) {
                if (str($key)->endsWith(':' . $for)) {
                    $attributes = str($key)->beforeLast(':' . $for)->__toString();
                    $forAttributes[] = $attributes;

                    $attrBag->offsetSet($attributes, $value);
                }
            }

            return $attrBag->only($forAttributes);
        });

        ComponentAttributeBag::macro('withoutFor', function (): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */

            $forAttributes = [];

            foreach ($this->attributes as $key => $value) {
                if (str($key)->contains(':')) {
                    $forAttributes[] = $key;
                }
            }

            return $this->except($forAttributes);
        });
    }

    /**
     * @return array<class-string<\TailwindClassMerge\Contracts\TailwindClassMergeContract>>|string[]
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
