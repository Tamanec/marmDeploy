<?php

// test

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

		'db' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:dbname=box',
			'username' => 'pguser',
			'password' => 'jA2QWe21F4',
		),
		'db_collective' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:dbname=collective',
			'username' => 'pguser',
			'password' => 'jA2QWe21F4',
		),
		'db_oati' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:dbname=oati',
			'username' => 'pguser',
			'password' => 'jA2QWe21F4',
		),
		'db_nadzor' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:dbname=nadzor',
			'username' => 'pguser',
			'password' => 'jA2QWe21F4',
		),
		'db_mgi' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:dbname=mgi',
			'username' => 'pguser',
			'password' => 'jA2QWe21F4',
		),
		'db_mgi_logs' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:dbname=mgi_logs',
			'username' => 'pguser',
			'password' => 'jA2QWe21F4',
		),
		'db_mgsn' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:dbname=mgsn',
			'username' => 'pguser',
			'password' => 'jA2QWe21F4',
		),
		'db_sgtn' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'pgsql:dbname=sgtn',
            'username' => 'pguser',
            'password' => 'jA2QWe21F4',
        ),

		'db_test' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:dbname=test',
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
        'db_lic' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'pgsql:dbname=lic',
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
			'connectionString' => 'mongodb://127.0.0.1:27017',
			'username' => 'mongouser',
			'password' => 'jA2QWe21F4',
		),

		'printLink' => 'http://dev.print.marm2.altarix.org/', // deprecated
		'integratorLink' => 'http://integrator.local/',
		'printFormLink' => 'http://printForm.local/',
		'boxExternalLink' => 'http://dev.box.marm2.altarix.org/',
		'userfilesPath' => '/var/www/userfiles/',
		'userfilesPrefix' => '/userfiles/',
		'nadzor' => array(
			'auth' => array(
				'externalOnly' => true,
				'caseSensitive' => false,
			)
		),
		'sgtn' => array(
            'auth' => array(
                'externalOnly' => true,
                'caseSensitive' => false,
            )
        ),
        'lic' => array(
            'auth' => array(
                'externalOnly' => true,
                'caseSensitive' => false,
            )
        ),
	)
);