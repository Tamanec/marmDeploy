<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'MARM Integrator Console',

    // preloading 'log' component
    'preload' => array('log'),

    'import' => array(
        'application.models.*',
        'application.components.*',
        'ext.connector.*',
        'ext.api.*',
    ),

    // application components
    'components' => array(
        'db_sgtn' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'pgsql:host=172.29.134.17;dbname=sgtn',
            'username' => 'pguser',
            'password' => 'jA2QWe21F4',
        ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info, trace',
                    'except' => 'project.*, system.CModule'
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info, trace',
                    'categories' => 'project.sgtn.service.*',
                    'logFile' => 'SgtnService.log',
                    'logPath' => '/var/log/marm/sgtn/service/',
                    'maxFileSize' => 1024000,
                    'maxLogFiles' => 5,
                ),
            ),
        ),
    ),
    'params' => array(
        //MongoDB settings
        'mongo' => array(
            'db' => 'integrator',
            'connectionString' => 'mongodb://172.29.134.17:27017',
            'username' => 'mongouser',
            'password' => 'jA2QWe21F4',
        ),
        'marmBoxLink' => 'http://marm-server-box/',
        'marmBoxUser' => array(
            'login' => 'integrator',
            'password' => 'iknowyouwantmetoauthorize'
        ),
        'userfilesPath' => 'http://dev.box.marm2.altarix.org/userfiles/',
        'sgtn' => array(
            'fsUrl' => 'http://dev.fs.marm2.altarix.org/sgtn/printForm/',
            'fsPath' => '/var/www/html/fs/sgtn/printForm/',
            'connection' => array(
                'useStub' => false,
                'wsdl' => 'http://87.245.154.37/sgtn_mobileTest/sgtn_mobile.svc?wsdl',
                'connectionTimeout' => 15,
                'requestTimeout' => 600,
                'auth' => array(
                    'login' => 'sgtn_mobile',
                    'password' => 'v8EbU1Vfxb'
                )
            )
        ),
    ),
);