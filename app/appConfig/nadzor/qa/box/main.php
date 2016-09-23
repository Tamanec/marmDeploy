<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'MARM Box',
	'defaultController' => 'check',

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
			'connectionString' => 'pgsql:host=marm-postgres;dbname=collective',
			'username' => 'pguser',
			'password' => 'Qwerty123',
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
				array('host' => 'marm-memcached', 'port' => 11211),
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
			'connectionString' => 'mongodb://marm-mongo:27017',
			'username' => 'mongouser',
			'password' => 'Qwerty123',
		),

		'integratorLink' => 'http://marm-server-integrator/',
		'printFormLink' => 'http://marm-server-printform/',
		'boxExternalLink' => 'http://172.29.12.2:8081/',
		'userfilesPath' => '/var/www/userfiles/',
		'userfilesPrefix' => '/userfiles/',
		'nadzor' => array(
			'auth' => array(
				'externalOnly' => true,
				'caseSensitive' => false,
			)
		),
	)
);