<?php

namespace RenokiCo\OctaneExporter\Metrics;

use Laravel\Octane\Facades\Octane;
use RenokiCo\LaravelExporter\CounterMetric;

class OctaneTotalRequestsCount extends CounterMetric
{
    /**
     * The group this metric gets shown into.
     *
     * @var string|null
     */
    public static $showsOnGroup = 'octane-metrics';

    /**
     * Get the metric name.
     *
     * @return string
     */
    protected function name(): string
    {
        return 'octane_total_requests_count';
    }

    /**
     * Get the metric help.
     *
     * @return string
     */
    protected function help(): string
    {
        return 'Get the number of total requests that passed through Octane.';
    }

    /**
     * Get the metric allowed labels.
     *
     * @return array
     */
    protected function allowedLabels(): array
    {
        return ['remote_addr', 'addr', 'name'];
    }

    /**
     * Define the default labels with their values.
     *
     * @return array
     */
    protected function defaultLabels(): array
    {
        return [
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
            'addr' => $_SERVER['SERVER_ADDR'] ?? null,
            'name' => $_SERVER['SERVER_NAME'] ?? null,
        ];
    }
}
