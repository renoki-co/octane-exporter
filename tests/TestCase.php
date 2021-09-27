<?php

namespace RenokiCo\OctaneExporter\Test;

use Illuminate\Support\Arr;
use Laravel\Octane\ApplicationFactory;
use Laravel\Octane\Octane;
use Laravel\Octane\OctaneServiceProvider;
use Laravel\Octane\Testing\Fakes\FakeClient;
use Laravel\Octane\Testing\Fakes\FakeWorker;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $factory = new ApplicationFactory(realpath(__DIR__.'/../vendor/orchestra/testbench-core/laravel'));

        $app = $this->appFactory()->createApplication();

        $factory->warm($app, Octane::defaultServicesToWarm());

        $app['config']['app.providers'] = array_merge($app['config']['app.providers'] ?? [], $this->getPackageProviders($app));

        return $app;
    }

    protected function appFactory()
    {
        return new ApplicationFactory(realpath(__DIR__.'/../vendor/orchestra/testbench-core/laravel'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            \Laravel\Octane\OctaneServiceProvider::class,
            \RenokiCo\LaravelExporter\LaravelExporterServiceProvider::class,
            \RenokiCo\OctaneExporter\OctaneExporterServiceProvider::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'wslxrEFGWY6GfGhvN9L3wH3KSRJQQpBD');
        $app['config']->set('ray.enable', false);

        $app['config']->set('octane.warm', Arr::except(Octane::defaultServicesToWarm(), [
            'cache',
            'cache.store',
            'db',
            'db.factory',
        ]));
    }

    /**
     * Create a new octane context with requests to run.
     *
     * @param  array  $requests
     * @return array
     */
    protected function createOctaneContext(array $requests)
    {
        $appFactory = Mockery::mock(ApplicationFactory::class);

        $appFactory->shouldReceive('createApplication')->andReturn($app = $this->createApplication());

        $app->register(new OctaneServiceProvider($app));

        foreach ($this->getPackageProviders($app) as $provider) {
            $app->register(new $provider($app));
        }

        $worker = new FakeWorker($appFactory, $client = new FakeClient($requests));

        $app->bind(Client::class, fn () => $client);

        $worker->boot();

        return [$app, $worker, $client];
    }
}
