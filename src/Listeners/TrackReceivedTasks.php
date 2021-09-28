<?php

namespace RenokiCo\OctaneExporter\Listeners;

use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Facades\Octane;

class TrackReceivedTasks
{
    /**
     * Handle the event.
     *
     * @param  \Laravel\Octane\Events\TaskReceived  $event
     * @return void
     */
    public function handle(TaskReceived $event): void
    {
        $this->incrementTasksCount();
        $this->incrementActiveTasksCount();
    }

    /**
     * Increment the tasks count.
     *
     * @return mixed
     */
    protected function incrementTasksCount()
    {
        return Octane::table('octane_exporter_tasks')->incr('tasks', 'total_count');
    }

    /**
     * Increment the active tasks count.
     *
     * @return mixed
     */
    protected function incrementActiveTasksCount()
    {
        return Octane::table('octane_exporter_tasks')->incr('tasks', 'active_count');
    }
}
