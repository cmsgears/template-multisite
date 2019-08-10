<?php

return [
	'components' => [
		'urlManager' => [
			'class' => 'yii\web\UrlManager',
			'baseUrl' => 'https://demo.cmsgears.com/template/multisite'
		],
		// CMG Modules - Core
		'migration' => [
			'class' => 'cmsgears\core\common\components\Migration',
			'cmgPrefix' => 'cmg_',
			'sitePrefix' => 'mls_',
			'siteName' => 'Multisite',
			'siteTitle' => 'Multisite Demo',
			'siteMaster' => 'demomaster',
			'primaryDomain' => 'cmsgears.com',
			'defaultSite' => 'https://demo.cmsgears.com/template/multisite',
			'defaultAdmin' => 'https://demo.cmsgears.com/template/multisite/admin',
			'uploadsUrl' => 'https://demo.cmsgears.com/template/multisite/uploads'
		]
	]
];
