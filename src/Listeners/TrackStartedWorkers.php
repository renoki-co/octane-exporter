<?php

namespace RenokiCo\OctaneExporter\Listeners;

use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Facades\Octane;

class TrackStartedWorkers
{
    /**
     * Handle the event.
     *
     * @param  \Laravel\Octane\Events\WorkerStarting  $event
     * @return void
     */
    public function handle(WorkerStarting $event): void
    {
        $this->incrementActiveWorkersCount();
    }

    /**
     * Increment the active workers count.
     *
     * @return mixed
     */
    protected function incrementActiveWorkersCount()
    {
        return Octane::table('octane_exporter_workers')->incr('workers', 'active_count');
    }
}
