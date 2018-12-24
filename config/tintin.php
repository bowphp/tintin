<?php

return [
    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | paths that should be checked for your views.
    |
    */
    'path' => resource_path('views'),

    /*
    |--------------------------------------------------------------------------
    | Extension
    |--------------------------------------------------------------------------
    |
    | File extension for Tintin view files.
    |
    */
    'extension' => 'tintin.php',


    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Tintin templates will be
    | stored for your application.
    |
    */
    'cache' => realpath(storage_path('framework/views'))
];
