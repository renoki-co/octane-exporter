<?php

namespace RenokiCo\OctaneExporter\Test;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TaskTerminated;
use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Events\TickTerminated;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\WorkerStopping;
use RenokiCo\LaravelExporter\Exporter;
use RenokiCo\OctaneExporter\Listeners\TrackReceivedTasks;
use RenokiCo\OctaneExporter\Listeners\TrackReceivedTicks;
use RenokiCo\OctaneExporter\Listeners\TrackStartedWorkers;
use RenokiCo\OctaneExporter\Listeners\TrackStoppedWorkers;
use RenokiCo\OctaneExporter\Listeners\TrackTerminatedRequests;
use RenokiCo\OctaneExporter\Listeners\TrackTerminatedTasks;
use RenokiCo\OctaneExporter\Listeners\TrackTerminatedTicks;

class MetricsTest extends TestCase
{
    public function test_octane_status()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/exporter/group/octane-metrics', 'GET'),
        ]);

        $app->bind('test-binding', function ($app) {
            return $app['request'];
        });

        $app['router']->get('/exporter/group/octane-metrics', function (Application $app) {
            return Exporter::exportAsPlainText('octane-metrics');
        });

        $worker->run();

        $this->assertStringContainsString(
            'laravel_octane_status{remote_addr="",addr="",name=""} 1',
            $client->responses[0]->original,
        );
    }

    public function test_octane_requests()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/test1', 'GET'),
            Request::create('/test2', 'GET'),
            Request::create('/test3', 'GET'),
            Request::create('/exporter/group/octane-metrics', 'GET'),
        ]);

        $app->bind('test-binding', function ($app) {
            return $app['request'];
        });

        $app['router']->get('/test1', function (Application $app) {
            (new TrackTerminatedRequests)->handle(new RequestTerminated(
                $app,
                $app,
                Request::create('/test1', 'GET'),
                new Response('ok', 200),
            ));

            $this->assertStringContainsString(
                '} 1',
                Exporter::exportAsPlainText('octane-metrics'),
            );

            return ['ok' => true];
        });

        $app['router']->get('/test2', function (Application $app) {
            (new TrackTerminatedRequests)->handle(new RequestTerminated(
                $app,
                $app,
                Request::create('/test1', 'GET'),
                new Response('ok', 200),
            ));

            $this->assertStringContainsString(
                '} 2',
                Exporter::exportAsPlainText('octane-metrics'),
            );

            return ['ok' => true];
        });

        $app['router']->get('/test3', function (Application $app) {
            (new TrackTerminatedRequests)->handle(new RequestTerminated(
                $app,
                $app,
                Request::create('/test1', 'GET'),
                new Response('ok', 401),
            ));

            $this->assertStringContainsString(
                '} 3',
                Exporter::exportAsPlainText('octane-metrics'),
            );

            return ['ok' => true];
        });

        $app['router']->get('/exporter/group/octane-metrics', function (Application $app) {
            return Exporter::exportAsPlainText('octane-metrics');
        });

        $worker->run();

        $this->assertStringContainsString(
            'status="total_count"} 3',
            $client->responses[3]->original
        );

        $this->assertStringContainsString(
            'status="2xx_count"} 2',
            $client->responses[3]->original
        );

        $this->assertStringContainsString(
            'status="4xx_count"} 1',
            $client->responses[3]->original
        );
    }

    public function test_octane_workers()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/exporter/group/octane-metrics', 'GET'),
        ]);

        $app->bind('test-binding', function ($app) {
            return $app['request'];
        });

        (new TrackStartedWorkers)->handle(new WorkerStarting($app));
        (new TrackStartedWorkers)->handle(new WorkerStarting($app));
        (new TrackStartedWorkers)->handle(new WorkerStarting($app));
        (new TrackStartedWorkers)->handle(new WorkerStarting($app));

        (new TrackStoppedWorkers)->handle(new WorkerStopping($app));

        $app['router']->get('/exporter/group/octane-metrics', function (Application $app) {
            return Exporter::exportAsPlainText('octane-metrics');
        });

        $worker->run();

        $this->assertStringContainsString(
            'laravel_octane_active_workers_count{remote_addr="",addr="",name=""} 3',
            $client->responses[0]->original,
        );
    }

    public function test_octane_tasks()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/exporter/group/octane-metrics', 'GET'),
        ]);

        $app->bind('test-binding', function ($app) {
            return $app['request'];
        });

        (new TrackReceivedTasks)->handle(new TaskReceived($app, $app, []));
        (new TrackReceivedTasks)->handle(new TaskReceived($app, $app, []));
        (new TrackReceivedTasks)->handle(new TaskReceived($app, $app, []));

        (new TrackTerminatedTasks)->handle(new TaskTerminated($app, $app, [], []));

        $app['router']->get('/exporter/group/octane-metrics', function (Application $app) {
            return Exporter::exportAsPlainText('octane-metrics');
        });

        $worker->run();

        $this->assertStringContainsString(
            'laravel_octane_total_tasks_count{remote_addr="",addr="",name=""} 3',
            $client->responses[0]->original,
        );

        $this->assertStringContainsString(
            'laravel_octane_active_tasks_count{remote_addr="",addr="",name=""} 2',
            $client->responses[0]->original,
        );
    }

    public function test_octane_ticks()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/exporter/group/octane-metrics', 'GET'),
        ]);

        $app->bind('test-binding', function ($app) {
            return $app['request'];
        });

        (new TrackReceivedTicks)->handle(new TickReceived($app, $app));
        (new TrackReceivedTicks)->handle(new TickReceived($app, $app));
        (new TrackReceivedTicks)->handle(new TickReceived($app, $app));

        (new TrackTerminatedTicks)->handle(new TickTerminated($app, $app));

        $app['router']->get('/exporter/group/octane-metrics', function (Application $app) {
            return Exporter::exportAsPlainText('octane-metrics');
        });

        $worker->run();

        $this->assertStringContainsString(
            'laravel_octane_total_ticks_count{remote_addr="",addr="",name=""} 3',
            $client->responses[0]->original,
        );

        $this->assertStringContainsString(
            'laravel_octane_active_ticks_count{remote_addr="",addr="",name=""} 2',
            $client->responses[0]->original,
        );
    }
}
