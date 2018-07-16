<?php

    /*
     * Optional
     */
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once 'jinxup.php';

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

    /*
     * Specify which templating engine to use for your project. Currently only support Smarty
     */
    $jinxup->view->engine('smarty');

    /*
     * Pass in some template variables
     */
    $app = [
        'name' => 'Sample App',
        'basePath' => '/apps/main'
    ];

    $jinxup->view->assign('APP', $app);

    /*
     * Specify which app you want to use
     */
    $jinxup->load('main');