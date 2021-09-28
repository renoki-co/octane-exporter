Laravel Octane Prometheus Exporter
==================================

![CI](https://github.com/renoki-co/octane-exporter/workflows/CI/badge.svg?branch=master)
[![codecov](https://codecov.io/gh/renoki-co/octane-exporter/branch/master/graph/badge.svg)](https://codecov.io/gh/renoki-co/octane-exporter/branch/master)
[![StyleCI](https://github.styleci.io/repos/410801641/shield?branch=master)](https://github.styleci.io/repos/410801641)
[![Latest Stable Version](https://poser.pugx.org/renoki-co/octane-exporter/v/stable)](https://packagist.org/packages/renoki-co/octane-exporter)
[![Total Downloads](https://poser.pugx.org/renoki-co/octane-exporter/downloads)](https://packagist.org/packages/renoki-co/octane-exporter)
[![Monthly Downloads](https://poser.pugx.org/renoki-co/octane-exporter/d/monthly)](https://packagist.org/packages/renoki-co/octane-exporter)
[![License](https://poser.pugx.org/renoki-co/octane-exporter/license)](https://packagist.org/packages/renoki-co/octane-exporter)

Export Laravel Octane metrics using this Prometheus exporter.

## 🤝 Supporting

If you are using one or more Renoki Co. open-source packages in your production apps, in presentation demos, hobby projects, school projects or so, spread some kind words about our work or sponsor our work via Patreon. 📦

You will sometimes get exclusive content on tips about Laravel, AWS or Kubernetes on Patreon and some early-access to projects or packages.

[<img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" height="41" width="175" />](https://www.patreon.com/bePatron?u=10965171)

## 🚀 Installation

You can install the package via composer:

```bash
composer require renoki-co/octane-exporter
```

In case you haven't published your Octane settings, do so:

```bash
php artisan octane:install
```

Next up, add the following Octane tables in your `config/octane.php`. These tables will be used to keep track of stats for the events:

```php
return [

    'tables' => [
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

        // ...
    ],

];
```

The final step is to add the following listeners after the already existent ones in `config/octane.php`:

```php
return [

    'listeners' => [
        WorkerStarting::class => [
            // ...
            \RenokiCo\OctaneExporter\Listeners\TrackStartedWorkers::class,
        ],

        RequestTerminated::class => [
            // ...
            \RenokiCo\OctaneExporter\Listeners\TrackTerminatedRequests::class,
        ],

        TaskReceived::class => [
            // ...
            \RenokiCo\OctaneExporter\Listeners\TrackReceivedTasks::class,
        ],

        TaskTerminated::class => [
            // ...
            \RenokiCo\OctaneExporter\Listeners\TrackTerminatedTasks::class,
        ],

        TickReceived::class => [
            // ...
            \RenokiCo\OctaneExporter\Listeners\TrackReceivedTicks::class,
        ],

        TickTerminated::class => [
            // ...
            \RenokiCo\OctaneExporter\Listeners\TrackTerminatedTicks::class,
        ],

        WorkerStopping::class => [
            // ...
            \RenokiCo\OctaneExporter\Listeners\TrackStoppedWorkers::class,
        ],
    ],

];
```

## 🙌 Usage

This package is pretty straightforward. Upon installing it, it will register the route at `/exporter/group/octane-metrics` and you can point Prometheus towards it for scraping.

Please keep in mind that the metrics are calculated by-process. Point your Prometheus scraper to all instances that run the Octane start command.

```
# HELP laravel_octane_active_tasks_count Get the number of active tasks that pass through Octane.
# TYPE laravel_octane_active_tasks_count gauge
laravel_octane_active_tasks_count{remote_addr="",addr="",name=""} 0
# HELP laravel_octane_active_ticks_count Get the number of active ticks that run currently in Octane.
# TYPE laravel_octane_active_ticks_count gauge
laravel_octane_active_ticks_count{remote_addr="",addr="",name=""} 0
# HELP laravel_octane_active_workers_count Get the number of active workers for Octane.
# TYPE laravel_octane_active_workers_count gauge
laravel_octane_active_workers_count{remote_addr="",addr="",name=""} 8
# HELP laravel_octane_requests_count Get the number of requests, by status, that passed through Octane.
# TYPE laravel_octane_requests_count gauge
laravel_octane_requests_count{remote_addr="",addr="",name="",status="2xx_count"} 7
laravel_octane_requests_count{remote_addr="",addr="",name="",status="3xx_count"} 0
laravel_octane_requests_count{remote_addr="",addr="",name="",status="4xx_count"} 0
laravel_octane_requests_count{remote_addr="",addr="",name="",status="5xx_count"} 0
laravel_octane_requests_count{remote_addr="",addr="",name="",status="total_count"} 7
# HELP laravel_octane_status Check if the octane service is running. 1 = active, 0 = inactive
# TYPE laravel_octane_status gauge
laravel_octane_status{remote_addr="",addr="",name=""} 1
# HELP laravel_octane_total_tasks_count Get the number of total tasks that passed through Octane.
# TYPE laravel_octane_total_tasks_count gauge
laravel_octane_total_tasks_count{remote_addr="",addr="",name=""} 0
# HELP laravel_octane_total_ticks_count Get the number of total ticks that got through Octane. This is the equivalent of seconds passed since this server is alive.
# TYPE laravel_octane_total_ticks_count gauge
laravel_octane_total_ticks_count{remote_addr="",addr="",name=""} 1242
# HELP php_info Information about the PHP environment.
# TYPE php_info gauge
php_info{version="8.0.11"} 1
```

## 🐛 Testing

``` bash
vendor/bin/phpunit
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒  Security

If you discover any security related issues, please email alex@renoki.org instead of using the issue tracker.

## 🎉 Credits

- [Alex Renoki](https://github.com/rennokki)
- [All Contributors](../../contributors)
