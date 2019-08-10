<?php
// Yii Imports
use yii\helpers\Html;

// Blog Imports
use modules\core\common\config\CoreGlobal;

$core	= Yii::$app->core;
$user	= $core->getUser();
$siteId	= Yii::$app->core->siteId;
