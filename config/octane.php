<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Octane Server
    |--------------------------------------------------------------------------
    |
    | This value determines the default "server" that will be used by Octane
    | when starting, restarting, or stopping your application. You may specify
    | any of the servers defined in the "servers" configuration array below.
    |
    | Supported: "roadrunner", "swoole", "frankenphp"
    |
    */

    'server' => env('OCTANE_SERVER', 'frankenphp'),

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS
    |--------------------------------------------------------------------------
    |
    | When this configuration value is set to "true", Octane will inform the
    | framework that all incoming requests should be served over HTTPS. In
    | addition, all responses redirecting to an absolute URL will be sent
    | to a secure URL. This should only be enabled when behind a proxy.
    |
    */

    'https' => env('OCTANE_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | Octane Listeners
    |--------------------------------------------------------------------------
    |
    | All of the event listeners listed below will be registered with the
    | application when Octane is handling requests. These listeners are not
    | required for Octane to function and can be safely removed if needed.
    |
    */

    'listeners' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Cache
    |--------------------------------------------------------------------------
    |
    | The Octane cache provides a dedicated place to store your application's
    | cache. This table may be used by multiple Octane workers to share
    | application state. You may use the `Cache::store('octane')`
    | driver to interact with this cache store.
    |
    */

    'cache' => [
        'driver' => 'octane',
        'rows' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Tables
    |--------------------------------------------------------------------------
    |
    | The following configuration options allow you to configure the Octane
    | cache table as well as any other custom tables you may want to create
    | for your application. These tables are available via the `Octane`
    | facade and provide extremely fast read and write access.
    |
    */

    'tables' => [
        'example:1000' => [
            'name' => 'string:1000',
            'votes' => 'int',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Servers
    |--------------------------------------------------------------------------
    |
    | This configuration array contains the definitions for each of the servers
    | that are supported by Octane. You may customize the settings for each
    | server, including the number of workers and max requests per worker.
    |
    */

    'servers' => [
        'frankenphp' => [
            'host' => env('OCTANE_HOST', '127.0.0.1'),
            'port' => env('OCTANE_PORT', '8000'),
            'workers' => env('OCTANE_WORKERS', 'auto'),
            'max_requests' => env('OCTANE_MAX_REQUESTS', 500),
            'frankenphp_binary' => base_path('.frankenphp/frankenphp-linux-x86_64'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Watch
    |--------------------------------------------------------------------------
    |
    | The following array of files and directories will be watched for changes
    | and will cause the Octane server to automatically reload. By default,
    | Octane will watch the entire application directory.
    |
    */

    'watch' => [
        'app',
        'bootstrap',
        'config',
        'database',
        'public/**/*.php',
        'resources/**/*.php',
        'routes',
        'composer.lock',
        '.env',
    ],

    /*
    |--------------------------------------------------------------------------
    | Garbage Collection Threshold
    |--------------------------------------------------------------------------
    |
    | When executing long-running Octane applications, you may want to manually
    | garbage collect PHP memory cycles after a certain number of requests
    | have been handled by a worker. You can specify that threshold here.
    |
    */

    'garbage' => 50,

    /*
    |--------------------------------------------------------------------------
    | Octane Route Cache
    |--------------------------------------------------------------------------
    |
    | The Octane route cache is a separate route cache that is used by Octane
    | to quickly resolve routes. This cache is automatically cleared when
    | the Octane server is reloaded. You may disable this cache here.
    |
    */

    'warm' => [
        
    ],

    'flush' => true,
];
