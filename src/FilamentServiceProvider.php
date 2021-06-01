<?php

declare(strict_types = 1);

namespace Filament;

use Filament\Models\User;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Finder\SplFileInfo;

class FilamentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament')
            ->hasConfigFile()
            ->hasRoutes(['web'])
            ->hasTranslations()
            ->hasViewComponents('filament')
            ->hasViews();
    }

    protected function configure()
    {
        $this->app->booted(function () {
            $this->app['config']->set('auth.guards.filament', [
                'driver' => 'session',
                'provider' => 'filament_users',
            ]);

            $this->app['config']->set('auth.passwords.filament_users', [
                'provider' => 'filament_users',
                'table' => 'filament_password_resets',
                'expire' => 60,
                'throttle' => 60,
            ]);

            $this->app['config']->set('auth.providers.filament_users', [
                'driver' => 'eloquent',
                'model' => User::class,
            ]);

            $this->app['config']->set('forms', [
                'default_attachment_upload_route' => 'filament.form-attachments.upload',
                'default_filesystem_disk' => $this->app['config']->get('filament.default_filesystem_disk'),
            ]);
        });
    }

    protected function mergeConfig(array $original, array $merging): array
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            if ($key === 'middleware') {
                continue;
            }

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }

    protected function mergeConfigFrom($path, $key)
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app['config']->get($key, []);

            $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
        }
    }

    protected function registerLivewireComponentDirectory(string $directory, string $namespace, string $aliasPrefix = ''): void
    {
        $filesystem = new Filesystem();

        if (! $filesystem->isDirectory($directory)) {
            return;
        }

        collect($filesystem->allFiles($directory))
            ->map(function (SplFileInfo $file) use ($namespace) {
                return (string) Str::of($namespace)
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Component::class) && ! (new ReflectionClass($class))->isAbstract();
            })
            ->each(function ($class) use ($namespace, $aliasPrefix) {
                $alias = Str::of($class)
                    ->after($namespace . '\\')
                    ->replace(['/', '\\'], '.')
                    ->prepend($aliasPrefix)
                    ->explode('.')
                    ->map([Str::class, 'kebab'])
                    ->implode('.');

                Livewire::component($alias, $class);
            });
    }
}