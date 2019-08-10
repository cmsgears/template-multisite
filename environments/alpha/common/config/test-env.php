<?php
return yii\helpers\ArrayHelper::merge(
	require( __DIR__ . '/main.php' ),
	require( __DIR__ . '/main-env.php' ),
	require( __DIR__ . '/test.php' ),
	[
		'components' => [
			'db' => [
				'class' => 'yii\db\Connection',
				'dsn' => 'mysql:host=localhost;dbname=multisitedemo_test',
				'username' => 'multisitedemo',
				'password' => 'Demo#Mls4E*6',
				'charset' => 'utf8',
				// Cache schema specific queries
				// Disable it while doing DB designing
				// The cache in backend/frontend runtime can be cleaned in case it's enabled and DB is changed
				'enableSchemaCache' => true,
				'schemaCache' => 'cache',
				'schemaCacheDuration' => 86400
			]
		]
	]
);
