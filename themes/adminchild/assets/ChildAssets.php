<?php
/**
 * This file is part of CMSGears Framework. Please view License file distributed
 * with the source code for license details.
 *
 * @link https://www.cmsgears.org/
 * @copyright Copyright (c) 2015 VulpineCode Technologies Pvt. Ltd.
 */

namespace themes\adminchild\assets;

// Yii Imports
use yii\web\AssetBundle;
use yii\web\View;

class ChildAssets extends AssetBundle {

	// Variables ---------------------------------------------------

	// Public ----

	// Path Configuration
	public $sourcePath	= '@themes/adminchild/resources';

	// Load CSS
    public $css	= [
        'styles/main.css'
    ];

	// Load Javascript
    public $js	= [
        'scripts/main.js'
    ];

	// Define the Position to load Assets
    public $jsOptions = [
        'position' => View::POS_END
    ];

}
