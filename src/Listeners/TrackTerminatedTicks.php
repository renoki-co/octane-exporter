<?php

namespace RenokiCo\OctaneExporter\Listeners;

use Laravel\Octane\Events\TickTerminated;
use Laravel\Octane\Facades\Octane;

class TrackTerminatedTicks
{
    /**
     * Handle the event.
     *
     * @param  \Laravel\Octane\Events\TickTerminated  $event
     * @return void
     */
    public function handle(TickTerminated $event): void
    {
        $this->decrementActiveTicksCount();
    }

    /**
     * Decrement the active ticks count.
     *
     * @return mixed
     */
    protected function decrementActiveTicksCount()
    {
        return Octane::table('octane_exporter_ticks')->decr('ticks', 'active_count');
    }
}
