<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'MARM Box',

    // preloading 'log' component
    'preload' => array('log'),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'ext.CAdvancedArBehavior',
        'ext.api.*',
    ),

    'modules' => array(
        /*'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123456',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),*/
    ),

    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            // 'allowAutoLogin' => true,
        ),

        'urlManager' => array(
            'showScriptName' => false,
            'urlFormat' => 'path',
            'rules' => array(
                '<controller:userfiles>/<path:.*>' => '<controller>/index',
//				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
//				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
//				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),

        'db_collective' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'pgsql:host=172.29.134.17;dbname=collective',
            'username' => 'pguser',
            'password' => 'jA2QWe21F4',
        ),
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
                ),
                array(
                    'class' => 'ApiEmailLogRoute',
                    'levels' => 'error, warning',
                    'emails' => 'marm2-dev@altarix.ru',
                    'subject' => '[BOX] Notification',
                    'utf8' => true
                )

            ),
        ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        //MongoDB settings
        'mongo' => array(
            'db' => 'box',
            'connectionString' => 'mongodb://172.29.134.17:27017',
            'username' => 'mongouser',
            'password' => 'jA2QWe21F4',
        ),

        'printLink' => 'http://dev.print.marm2.altarix.org/', // deprecated
        'integratorLink' => 'http://marm-server-integrator/',
        'printFormLink' => 'http://printForm.local/',
        'boxExternalLink' => 'http://dev.box.marm2.altarix.org/',
        'userfilesPath' => '/var/www/userfiles/',
        'userfilesPrefix' => '/userfiles/',
        
        'sgtn' => array(
            'auth' => array(
                'externalOnly' => true,
                'caseSensitive' => false,
            )
        ),
    )
);