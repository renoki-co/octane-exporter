<?php

namespace RenokiCo\OctaneExporter;

use Illuminate\Support\ServiceProvider;
use RenokiCo\LaravelExporter\Exporter;

class OctaneExporterServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        Exporter::metrics([
            Metrics\OctaneRequestsCount::class,
            Metrics\OctaneStatus::class,
            Metrics\OctaneTotalRequestsCount::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
