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

        'db_nadzor' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'pgsql:host=marm-postgres;dbname=nadzor',
            'username' => 'pguser',
            'password' => 'Qwerty123',
        ),

        'db_nsi' => array(
            'class' => 'ext.oci8Pdo.OciDbConnection',
            'connectionString' => 'oci:dbname=(DESCRIPTION =
                (ADDRESS = (PROTOCOL = TCP)(HOST = 10.11.32.87)(PORT = 1521))
                (CONNECT_DATA =
                    (SERVER = DEDICATED)
                    (SERVICE_NAME = gaiebdm)
                )
            );charset=AL32UTF8;',
            'username' => 'police_mobile',
            'password' => 'police_mobile',
            //'enableProfiling' => true,
            //'enableParamLogging' => true,
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
                    'categories' => 'project.nadzor.service.*',
                    'logFile' => 'NadzorService.log',
                    'logPath' => '/var/log/marm/nadzor/service/',
                    'maxFileSize' => 1024000,
                    'maxLogFiles' => 5,
                ),
            ),
        ),
    ),

    'params' => array(
        'mongo' => array(
            'db' => 'integrator',
            'connectionString' => 'mongodb://marm-mongo:27017',
            'username' => 'mongouser',
            'password' => 'Qwerty123',
        ),
        'marmBoxLink' => 'http://marm-server-box/',
        'marmBoxUser' => array(
            'login' => 'integrator',
            'password' => 'iknowyouwantmetoauthorize'
        ),
        'printFormLink' => 'http://marm-server-printform/',
        'userfilesPath' => 'http://172.29.12.2:8081/userfiles/',
        'altarixGibddLink' => 'http://77.247.243.35:1010',
        'nadzor' => array(
            'cryptKey' => 'QR1hz1p9FpUqQulu1KNPuWmzKNJi4MGWkkejMEfy6IUUxAGD2KhFfpZJ2HTiBA7Pe9BsSFKz7TQboqzbV4A6wJu6RwCIwbazOH14ffBHHL5nFA9pgqIhkm4BDrgCaltf'
        ),
    ),

);