<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Controller
    |--------------------------------------------------------------------------
    |
    | This controller will be used as a base controller of all controllers
    |
    */

    'base_controller'          => 'Quantimodo\Controller\AppBaseController',

    /*
    |--------------------------------------------------------------------------
    | Path for classes
    |--------------------------------------------------------------------------
    |
    | All Classes will be created on these relevant path
    |
    */

    'path_migration'           => base_path('database/migrations/'),

    'path_model'               => app_path('Models/'),

    'path_service'             => app_path('Services/'),

    'path_controller'          => app_path('Http/Controllers/'),

    'path_api_controller'      => app_path('Http/Controllers/'),

    'path_views'               => base_path('resources/views/'),

    'path_request'             => app_path('Http/Requests/'),

    'path_routes'              => app_path('Http/routes.php'),

    'path_api_routes'          => app_path('Http/routes.php'),

    /*
    |--------------------------------------------------------------------------
    | Namespace for classes
    |--------------------------------------------------------------------------
    |
    | All Classes will be created with these namespaces
    |
    */

    'namespace_model'          => 'App\Models',

    'namespace_repository'     => 'App\Services',

    'namespace_controller'     => 'App\Http\Controllers',

    'namespace_api_controller' => 'App\Http\Controllers',

    'namespace_request'        => 'App\Http\Requests',

    /*
    |--------------------------------------------------------------------------
    | API routes prefix
    |--------------------------------------------------------------------------
    |
    | By default "api" will be prefix
    |
    */

    'api_prefix'               => 'api',

    'api_version'              => 'v1',

    /*
    |--------------------------------------------------------------------------
    | dingo API integration
    |--------------------------------------------------------------------------
    |
    | By default dingo API Integration will not be enabled. Dingo API is in beta.
    |
    */

    'use_dingo_api'            => false,

];
