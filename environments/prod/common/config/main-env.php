<?php

return [
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;dbname=cmgdemomultisite',
			'username' => 'cmgdemomultisite',
			'password' => 'Cmg#Multi*90S',
			'charset' => 'utf8'
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail'
		]
	]
];
