<?php

namespace AllCommerce\DepartmentStore;

use Illuminate\Support\ServiceProvider;
use AllCommerce\DepartmentStore\Auth\AccessToken;
use AllCommerce\DepartmentStore\Services\LibraryService;

class DepartmentStoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadConfigs();

        $this->publishFiles();

        if ($this->runningInConsole()) {

        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDepartmentStore();
    }

    /**
     * Register ServiceDesk as a singleton.
     *
     * @return void
     */
    protected function registerDepartmentStore()
    {
        $this->app->singleton(DepartmentStore::class, function ($app) {

            return new  DepartmentStore(new LibraryService(), new AccessToken(config('dept-store.deets.oauth_token')));
        });
    }

    /**
     * Determine if we are running in the console.
     *
     * Copied from Laravel's Application class, since we need to support 6.x.
     *
     * @return bool
     */
    protected function runningInConsole()
    {
        return php_sapi_name() == 'cli' || php_sapi_name() == 'phpdbg';
    }

    public function loadConfigs()
    {
        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(__DIR__.'/config/dept-store.php', 'dept-store');

        // add the root disk to filesystem configuration
        app()->config['filesystems.disks.'.config('dept-store.root_disk_name')] = [
            'driver' => 'local',
            'root'   => base_path(),
        ];
    }

    public function publishFiles()
    {
        $capeandbay_config_files = [__DIR__.'/config' => config_path()];

        $minimum = array_merge(
            $capeandbay_config_files
        );

        // register all possible publish commands and assign tags to each
        $this->publishes($capeandbay_config_files, 'config');
        $this->publishes($minimum, 'minimum');
    }
}
