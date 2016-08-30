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
		'db_nadzor' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:dbname=nadzor',
			'username' => 'pguser',
			'password' => 'jA2QWe21F4',
		),
		'db_sgtn' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'pgsql:dbname=sgtn',
            'username' => 'pguser',
            'password' => 'jA2QWe21F4',
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
		//MongoDB settings
		'mongo' => array(
			'db' => 'integrator',
			'connectionString' => 'mongodb://127.0.0.1:27017',
			'username' => 'mongouser',
			'password' => 'jA2QWe21F4',
		),
		'marmBoxLink' => 'http://marm-server-box/',
		'marmBoxUser' => array(
			'login' => 'integrator',
			'password' => 'iknowyouwantmetoauthorize'
		),
		'userfilesPath' => 'http://dev.box.marm2.altarix.org/userfiles/',
        'nadzor' => array(
            'export' => array(
                'inspectionAct' => array(
                    'printFormRequired' => false
                )
            )
        ),
	),
);