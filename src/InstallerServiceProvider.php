<?php
namespace Softmax\Installer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Softmax\Installer\Services\InstallerService;
use Softmax\Installer\Http\Middleware\RedirectIfNotInstalled;
use Softmax\Installer\Console\Commands\InstallationStatusCommand;
use Softmax\Installer\Console\Commands\ResetInstallationCommand;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Merge package config
        $this->mergeConfigFrom(__DIR__.'/../config/softmax-installer.php', 'softmax-installer');

        // Bind singleton service
        $this->app->singleton('softmax.installer', function($app) {
            return new InstallerService($app);
        });

        // Alias for facade auto resolution
        $this->app->alias('softmax.installer', InstallerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'softmax-installer');

        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/softmax-installer.php' => config_path('softmax-installer.php'),
        ], 'softmax-installer-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/softmax-installer'),
        ], 'softmax-installer-views');

        // Register middleware
        $router = $this->app[Router::class];
        $router->aliasMiddleware('installer.check', RedirectIfNotInstalled::class);
        
        // Apply middleware to web routes
        $router->pushMiddlewareToGroup('web', RedirectIfNotInstalled::class);

        // Load additional configuration in production
        if ($this->app->environment('production')) {
            $this->loadProductionConfiguration();
        }

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallationStatusCommand::class,
                ResetInstallationCommand::class,
            ]);
        }
    }

    /**
     * Load production-specific configuration
     */
    protected function loadProductionConfiguration()
    {
        // Additional production settings can be loaded here
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides()
    {
        return [
            'softmax.installer',
            InstallerService::class,
        ];
    }
}
