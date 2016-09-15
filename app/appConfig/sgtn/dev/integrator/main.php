<?php

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'MARM Integrator',

    // preloading 'log' component
    'preload' => array('log'),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'ext.connector.*',
        'ext.api.*',
    ),

    'modules' => array(
//		'gii'=>array(
////			'class'=>'system.gii.GiiModule',
////			'password'=>'123456',
////			// If removed, Gii defaults to localhost only. Edit carefully to taste.
////			'ipFilters'=>array('127.0.0.1','::1','192.168.56.1'),
//		),
    ),

    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            // 'allowAutoLogin'=>true,
        ),
        // uncomment the following to enable URLs in path-format

        'urlManager' => array(
            'showScriptName' => false,
            'urlFormat' => 'path',
//			'rules'=>array(
//				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
//				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
//				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
//			),
        ),

//		'db'=>array(
//			'class'=>'CDbConnection',
//			'connectionString' => 'pgsql:dbname=integrator',
//			'username'=>'pguser',
//			'password'=>'jA2QWe21F4',
//		),
        'db_sgtn' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'pgsql:host=172.29.134.17;dbname=sgtn',
            'username' => 'pguser',
            'password' => 'jA2QWe21F4',
        ),

        'cache' => array(
            'class' => 'system.caching.CMemCache',
            'servers' => array(
                array('host' => '127.0.0.1', 'port' => 11211),
            ),
        ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'throw/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info, trace',
                    'except' => 'project.*'
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
        'printFormLink' => 'http://printForm.local/',
        'userfilesPath' => 'http://dev.box.marm2.altarix.org/userfiles/',
        'altarixGibddLink' => 'http://77.247.243.35:1010',
        
        'sgtn' => array(
            'connection' => array(
                'useStub' => false,
                'wsdl' => 'http://87.245.154.37/sgtn_mobileTest/sgtn_mobile.svc?wsdl',
                'connectionTimeout' => 15,
                'requestTimeout' => 30,
                'auth' => array(
                    'login' => 'sgtn_mobile',
                    'password' => 'v8EbU1Vfxb'
                )
            )
        ),
    ),
);