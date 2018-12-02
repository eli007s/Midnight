<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . '/../../jinxup.php';

    $env    = 'dev';
    $config = $jinxup->config->get();
    $config = $config['database'];

    $jinxup->db->fuel(
        $config[$env]['alias'],
        $config[$env]['host'],
        $config[$env]['name'],
        $config[$env]['user'],
        $config[$env]['pass']
    );

    /*
     * Load the app
     */
    $jinxup->view->engine('smarty');

    $jinxup->load();