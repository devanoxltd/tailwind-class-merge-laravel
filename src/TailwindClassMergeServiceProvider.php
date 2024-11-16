<?php

declare(strict_types=1);

namespace TailwindClassMerge\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
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
            // @phpstan-ignore-next-line
            $this->offsetSet('class', resolve(TailwindClassMergeContract::class)->merge($args, ($this->get('class', ''))));

            return $this;
        });

        ComponentAttributeBag::macro('forAttributes', function (string $for): ComponentAttributeBag {
            $prefix = (string) config('tailwind-class-merge.attribute_prefix', 'component:'); // @phpstan-ignore-line
            $prefix = Str::finish($prefix, ':');
            $prefixFor = $for . ':';

            if (! Str::of($for)->startsWith($prefix)) {
                $prefixFor = Str::finish($prefix . $for, ':');
            }

            $attrBag = new ComponentAttributeBag;
            $forAttributes = [];

            /** @var ComponentAttributeBag $this */
            foreach ($this->getAttributes() as $key => $value) { // @phpstan-ignore-line
                if (Str::of($key)->startsWith($prefixFor)) {
                    $attributes = Str::of($key)->after($for . ':')->__toString();
                    $forAttributes[] = $attributes;

                    $attrBag->offsetSet($attributes, $value);
                }
            }

            return $attrBag->only($forAttributes);
        });

        ComponentAttributeBag::macro('withoutForAttributes', function (): ComponentAttributeBag {
            $forAttributes = [];
            $prefix = (string) config('tailwind-class-merge.attribute_prefix', 'component:'); // @phpstan-ignore-line
            $prefix = Str::finish($prefix, ':');

            /** @var ComponentAttributeBag $this */
            foreach (array_keys($this->getAttributes()) as $key) { // @phpstan-ignore-line
                if (Str::of((string) $key)->startsWith($prefix)) {
                    $forAttributes[] = $key;
                }
            }

            /** @var ComponentAttributeBag $this */
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
