<?php

namespace RenokiCo\OctaneExporter;

use Illuminate\Support\ServiceProvider;
use RenokiCo\LaravelExporter\Exporter;

class OctaneExporterServiceProvider extends ServiceProvider
{
    /**
     * The metrics to register.
     *
     * @var array
     */
    protected static $metrics = [
        Metrics\OctaneActiveTasksCount::class,
        Metrics\OctaneActiveTicksCount::class,
        Metrics\OctaneActiveWorkersCount::class,
        Metrics\OctaneMemoryUsage::class,
        Metrics\OctaneRequestsCount::class,
        Metrics\OctaneStatus::class,
        Metrics\OctaneTotalTasksCount::class,
        Metrics\OctaneTotalTicksCount::class,
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        foreach (static::$metrics as $metric) {
            Exporter::register($metric);
        }
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
