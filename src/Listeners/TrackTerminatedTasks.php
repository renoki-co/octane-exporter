<?php

namespace RenokiCo\OctaneExporter\Listeners;

use Laravel\Octane\Events\TaskTerminated;
use Laravel\Octane\Facades\Octane;

class TrackTerminatedTasks
{
    /**
     * Handle the event.
     *
     * @param  \Laravel\Octane\Events\TaskTerminated  $event
     * @return void
     */
    public function handle(TaskTerminated $event): void
    {
        $this->decrementActiveTasksCount();
    }

    /**
     * Decrement the active tasks count.
     *
     * @return mixed
     */
    protected function decrementActiveTasksCount()
    {
        return Octane::table('octane_exporter_tasks')->decr('tasks', 'active_count');
    }
}
