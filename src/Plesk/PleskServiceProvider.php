<?php

namespace AbuseIO\Findcontact\Ripe;

use Illuminate\Support\ServiceProvider;

class PleskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * merge the config
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->mergeConfigFrom(base_path('vendor/abuseio/findcontact-plesk').'/config/Plesk.php', 'Findcontact');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}