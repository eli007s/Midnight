<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . '/../../jinxup.php';

    /*
     * Determine what environment you are running, this is useful if your configuration has environment specific entries
     */
    $env = 'live';
    $env = preg_match('/staging\./', $_SERVER['HTTP_HOST']) ? 'staging' : $env;
    $env = $_SERVER['SERVER_ADDR'] == '127.0.0.1' ? 'dev' : $env;

    /*
     * We pass the environment to the framework for internal referencing
     */
    $jinxup->env($env);

    $config = $jinxup->config->get();

    define('AWS_ACCESS_KEY_ID', $config['aws']['key']);
    define('AWS_SECRET_ACCESS_KEY', $config['aws']['secret']);
    define('AWS_REGION', $config['aws']['region']);
    define('ENV', $env);

    /*
     * Load the app
     */
    $jinxup->load();