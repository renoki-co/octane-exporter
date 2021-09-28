<?php

namespace RenokiCo\OctaneExporter\Listeners;

use Laravel\Octane\Events\WorkerStopping;
use Laravel\Octane\Facades\Octane;

class TrackStoppedWorkers
{
    /**
     * Handle the event.
     *
     * @param  \Laravel\Octane\Events\WorkerStopping  $event
     * @return void
     */
    public function handle(WorkerStopping $event): void
    {
        $this->decrementActiveWorkersCount();
    }

    /**
     * Decrement the active workers count.
     *
     * @return mixed
     */
    protected function decrementActiveWorkersCount()
    {
        return Octane::table('octane_exporter_workers')->decr('workers', 'active_count');
    }
}
