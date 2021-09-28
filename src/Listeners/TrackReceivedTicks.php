<?php

namespace RenokiCo\OctaneExporter\Listeners;

use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Facades\Octane;

class TrackReceivedTicks
{
    /**
     * Handle the event.
     *
     * @param  \Laravel\Octane\Events\TickReceived  $event
     * @return void
     */
    public function handle(TickReceived $event): void
    {
        $this->incrementTicksCount();
        $this->incrementActiveTicksCount();
    }

    /**
     * Increment the ticks count.
     *
     * @return mixed
     */
    protected function incrementTicksCount()
    {
        return Octane::table('octane_exporter_ticks')->incr('ticks', 'total_count');
    }

    /**
     * Increment the active ticks count.
     *
     * @return mixed
     */
    protected function incrementActiveTicksCount()
    {
        return Octane::table('octane_exporter_ticks')->incr('ticks', 'active_count');
    }
}
