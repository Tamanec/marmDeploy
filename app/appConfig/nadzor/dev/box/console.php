<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'MARM Box',

	// preloading 'log' component
	'preload' => array('log'),

	'import' => array(
		'application.models.*',
		'application.components.*',
		'ext.CAdvancedArBehavior',
		'ext.api.*',
	),

	// application components
	'components' => array(
		'db_collective' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:host=172.29.134.17;dbname=collective',
			'username' => 'pguser',
			'password' => 'jA2QWe21F4',
		),
		'db_nadzor' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'pgsql:host=172.29.134.17;dbname=nadzor',
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
			/*
			 * enableProfiling and enableParamLogging are working like normal; Default is false.
			 */
			/*            'enableProfiling' => true,
						'enableParamLogging' => true,*/
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning, debug, trace',
				),
				array(
					'class' => 'ApiEmailLogRoute',
					'levels' => 'error, warning',
					'emails' => 'marm2-dev@altarix.ru',
					'subject' => '[BOX] Notification',
					'utf8' => true
				)
			)
		)
	),

	'params' => array(
		'mongo' => array(
			'connectionString' => 'mongodb://172.29.134.17:27017',
			'username' => 'mongouser',
			'password' => 'jA2QWe21F4',
		),
		'userfilesPath' => '/var/www/userfiles/'
	)
);