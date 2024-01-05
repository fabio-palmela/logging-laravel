<?php
namespace Credicom\Log\Providers;

use Illuminate\Support\ServiceProvider;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $configPath = realpath(__DIR__.'/../config/logging.php');
        $this->publishes([$configPath => config_path('logging.php')], 'lib-log');
    }

}
