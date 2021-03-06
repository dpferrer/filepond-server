<?php

namespace Itsdp\FilepondServer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class FilepondServerServiceProvider extends ServiceProvider
{
    public function boot() {
        $this->registerRoutes();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigFile(),
            'filepond'
        );
    }

    /**
     * Register the Horizon routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => 'filepond',
            'namespace' => 'Itsdp\FilepondServer\Http\Controllers',
            'middleware' => config('filepond.middleware', null),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * @return string
     */
    protected function getConfigFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'filepond.php';
    }
}
