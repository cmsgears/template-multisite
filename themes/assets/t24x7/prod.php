<?php

return [
	'yii\web\JqueryAsset' => false,
	'lazy' => [
		'class' => 'yii\web\AssetBundle',
		'basePath' => '@webroot',
		'baseUrl' => '@web',
		'js' => [ 't24x7/lzyb24x7-20190405.js' ]
	],
	'fa' => [
		'class' => 'yii\web\AssetBundle',
		'basePath' => '@webroot',
		'baseUrl' => '@web',
		'css' => [ 't24x7/fawb24x7-20190405.css' ]
	],
	'common' => [
		'class' => 'yii\web\AssetBundle',
		'basePath' => '@webroot',
		'baseUrl' => '@web',
		'css' => [ 't24x7/cmnb24x7-20190405.css' ],
		'js' => [ 't24x7/cmnb24x7-20190405.js' ],
		'depends' => [ 'lazy', 'fa', 'cmsgears\assets\jquery\Jquery' ]
	],
	'landing' => [
		'class' => 'yii\web\AssetBundle',
		'basePath' => '@webroot',
		'baseUrl' => '@web',
		'css' => [ 't24x7/ladb24x7-20190405.css' ],
		'js' => [ 't24x7/ladb24x7-20190405.js' ],
		'depends' => [ 'common' ]
	],
	'public' => [
		'class' => 'yii\web\AssetBundle',
		'basePath' => '@webroot',
		'baseUrl' => '@web',
		'css' => [ 't24x7/pubb24x7-20190405.css' ],
		'js' => [ 't24x7/pubb24x7-20190405.js' ],
		'depends' => [ 'common' ]
	],
	'private' => [
		'class' => 'yii\web\AssetBundle',
		'basePath' => '@webroot',
		'baseUrl' => '@web',
		'css' => [ 't24x7/prvb24x7-20190405.css' ],
		'js' => [ 't24x7/prvb24x7-20190405.js' ],
		'depends' => [ 'common' ]
	]
];
