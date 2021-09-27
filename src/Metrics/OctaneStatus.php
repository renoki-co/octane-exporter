<?php

namespace RenokiCo\OctaneExporter\Metrics;

use RenokiCo\LaravelExporter\GaugeMetric;

class OctaneStatus extends GaugeMetric
{
    /**
     * The group this metric gets shown into.
     *
     * @var string|null
     */
    public static $showsOnGroup = 'octane-metrics';

    /**
     * Perform the update call on the collector.
     *
     * @return void
     */
    public function update(): void
    {
        $this->set(value: 1);
    }

    /**
     * Get the metric name.
     *
     * @return string
     */
    protected function name(): string
    {
        return 'octane_status';
    }

    /**
     * Get the metric help.
     *
     * @return string
     */
    protected function help(): string
    {
        return 'Check if the octane service is running. 1 = active, 0 = inactive';
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
