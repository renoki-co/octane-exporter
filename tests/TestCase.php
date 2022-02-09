<?php

namespace RenokiCo\OctaneExporter\Test;

use Illuminate\Support\Arr;
use Laravel\Octane\ApplicationFactory;
use Laravel\Octane\Facades\Octane as FacadesOctane;
use Laravel\Octane\Octane;
use Laravel\Octane\OctaneServiceProvider;
use Laravel\Octane\Testing\Fakes\FakeClient;
use Laravel\Octane\Testing\Fakes\FakeWorker;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;
use RenokiCo\OctaneExporter\Test\Fixtures\Table;

abstract class TestCase extends Orchestra
{
    /**
     * The Swoole tables for testing.
     *
     * @var SwooleTable[]
     */
    protected $tables = [];

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSwooleTables();
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $factory = new ApplicationFactory(realpath(__DIR__.'/../vendor/orchestra/testbench-core/laravel'));

        $app = $this->appFactory()->createApplication();

        $factory->warm($app, Octane::defaultServicesToWarm());

        $this->createSwooleTables();

        return $app;
    }

    /**
     * Create a new application factory.
     *
     * @return ApplicationFactory
     */
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

    /**
     * Create Swoole tables.
     *
     * @return void
     */
    protected function createSwooleTables(): void
    {
        $tables = [
            'octane_exporter_requests:1' => [
                'total_count' => 'int',
                '2xx_count' => 'int',
                '3xx_count' => 'int',
                '4xx_count' => 'int',
                '5xx_count' => 'int',
            ],
            'octane_exporter_tasks:1' => [
                'total_count' => 'int',
                'active_count' => 'int',
            ],
            'octane_exporter_workers:1' => [
                'active_count' => 'int',
            ],
            'octane_exporter_ticks:1' => [
                'total_count' => 'int',
                'active_count' => 'int',
            ],
        ];

        $mock = FacadesOctane::partialMock();

        foreach ($tables as $name => $columns) {
            $table = new Table(explode(':', $name)[1] ?? 1000);

            foreach ($columns ?? [] as $columnName => $column) {
                $table->column($columnName, match (explode(':', $column)[0] ?? 'string') {
                    'string' => Table::TYPE_STRING,
                    'int' => Table::TYPE_INT,
                    'float' => Table::TYPE_FLOAT,
                }, explode(':', $column)[1] ?? 1000);
            }

            $table->create();

            $tableName = explode(':', $name)[0];

            $this->tables[$tableName] = $table;
        }

        foreach ($this->tables as $tableName => $table) {
            $mock->allows('table')
                ->with($tableName)
                ->andReturn($this->tables[$tableName]);
        }
    }
}
