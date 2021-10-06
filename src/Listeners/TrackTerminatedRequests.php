<?php

namespace RenokiCo\OctaneExporter\Listeners;

use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Facades\Octane;

class TrackTerminatedRequests
{
    /**
     * Handle the event.
     *
     * @param  \Laravel\Octane\Events\RequestTerminated  $event
     * @return void
     */
    public function handle(RequestTerminated $event): void
    {
        if ($event->request->route()->getName() === 'laravel-exporter.metrics') {
            return;
        }

        $this->incrementRequestsCount();

        $statusCode = $event->response->getStatusCode();
        $categoryCode = (int) ($statusCode / 100);

        if (in_array($categoryCode, [2, 3, 4, 5])) {
            $this->incrementRequestsForStatus("{$categoryCode}xx");
        }
    }

    /**
     * Increment the requests count.
     *
     * @return mixed
     */
    protected function incrementRequestsCount()
    {
        return Octane::table('octane_exporter_requests')->incr('requests', 'total_count');
    }

    /**
     * Increment status codes requests for category (i.e. 2xx, 4xx, etc.).
     *
     * @param  string  $code
     * @return mixed
     */
    protected function incrementRequestsForStatus(string $code = '2xx')
    {
        return Octane::table('octane_exporter_requests')->incr('requests', "{$code}_count");
    }
}
