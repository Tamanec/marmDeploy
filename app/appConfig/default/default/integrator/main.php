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
		'db_gspts' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'sqlite:/var/www/html/integrator/protected/data/gspts.db',
		),

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

        'db_lic' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'pgsql:dbname=lic',
            'username' => 'pguser',
            'password' => 'jA2QWe21F4',
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
                    'categories' => 'project.sgtn.service.*',
                    'logFile' => 'SgtnService.log',
                    'logPath' => '/var/log/marm/sgtn/service/',
                    'maxFileSize' => 1024000,
                    'maxLogFiles' => 5,
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info, trace',
                    'categories' => 'project.lic.service.*',
                    'logFile' => 'LicService.log',
                    'logPath' => '/var/log/marm/lic/service/',
                    'maxFileSize' => 1024000,
                    'maxLogFiles' => 5,
                ),
			),
		),
	),

	'params' => array(
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
		'printFormLink' => 'http://printForm.local/',
		'userfilesPath' => 'http://dev.box.marm2.altarix.org/userfiles/',
		'altarixGibddLink' => 'http://91.228.152.72:8085',
		'nadzor' => array(
			'cryptKey' => 'QR1hz1p9FpUqQulu1KNPuWmzKNJi4MGWkkejMEfy6IUUxAGD2KhFfpZJ2HTiBA7Pe9BsSFKz7TQboqzbV4A6wJu6RwCIwbazOH14ffBHHL5nFA9pgqIhkm4BDrgCaltf'
		),
		'sgtn' => array(
            'connection' => array(
                'useStub' => false,
                'wsdl' => 'http://87.245.154.37/sgtn_mobile/sgtn_mobile.svc?wsdl',
                'connectionTimeout' => 15,
                'requestTimeout' => 30,
                'auth' => array(
                    'login' => 'sgtn_mobile',
                    'password' => 'v8EbU1Vfxb'
                )
            )
        ),
        'lic' => array(
            'connection' => array(
                'endPoint' => 'http://edcapi',
                'connectionTimeout' => 10,
                'requestTimeout' => 60,
                'auth' => array(
                    'login' => 'demo',
                    'password' => 'demo'
                )
            )
        ),
	),
);
