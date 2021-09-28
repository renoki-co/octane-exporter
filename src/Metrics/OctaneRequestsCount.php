<?php

namespace RenokiCo\OctaneExporter\Metrics;

use Laravel\Octane\Facades\Octane;
use RenokiCo\LaravelExporter\GaugeMetric;

class OctaneRequestsCount extends GaugeMetric
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
        $requests = Octane::table('octane_exporter_requests')->get('requests');

        if (! is_array($requests)) {
            $metrics = [
                'total_count',
                '2xx_count',
                '3xx_count',
                '4xx_count',
                '5xx_count',
            ];

            foreach ($metrics as $metric) {
                $this->set(value: 0, labels: ['status' => $metric]);
            }

            return;
        }

        foreach ($requests as $metric => $value) {
            $this->set(
                value: $value,
                labels: ['status' => $metric],
            );
        }
    }

    /**
     * Get the metric name.
     *
     * @return string
     */
    protected function name(): string
    {
        return 'octane_requests_count';
    }

    /**
     * Get the metric help.
     *
     * @return string
     */
    protected function help(): string
    {
        return 'Get the number of requests, by status, that passed through Octane.';
    }

    /**
     * Get the metric allowed labels.
     *
     * @return array
     */
    protected function allowedLabels(): array
    {
        return ['remote_addr', 'addr', 'name', 'status'];
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
