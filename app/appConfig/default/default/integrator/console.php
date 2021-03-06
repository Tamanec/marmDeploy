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
			'connectionString' => 'mongodb://127.0.0.1:27017',
			'username' => 'mongouser',
			'password' => 'jA2QWe21F4',
		),
		'marmBoxLink' => 'http://box.local/',
		'marmBoxUser' => array(
			'login' => 'integrator',
			'password' => 'iknowyouwantmetoauthorize'
		),
		'userfilesPath' => 'http://dev.box.marm2.altarix.org/userfiles/',
		'sgtn' => array(
			'fsPath' => '/var/www/html/fs/sgtn/printForm/',
			'fsUrl' => 'http://dev.fs.marm2.altarix.org/sgtn/printForm/',
            'connection' => array(
                'useStub' => false,
                'wsdl' => 'http://87.245.154.37/sgtn_mobile/sgtn_mobile.svc?wsdl',
                'connectionTimeout' => 15,
                'requestTimeout' => 30,

                'auth' => array(
                    'login' => 'sgtn_mobile',
                    'password' => 'v8EbU1Vfxb'
                )
            ),
            'sendPhoto' => array(
                'attemptCount' => 5,
                'limit' => 60,
                'allowedStatus' => array('new', 'error')
            )
        ),
        'nadzor' => array(
            'export' => array(
                'inspectionAct' => array(
                    'printFormRequired' => false
                )
            )
        ),
	),
);