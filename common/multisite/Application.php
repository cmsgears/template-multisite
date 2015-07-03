<?php
namespace common\multisite;

// Yii Imports
use \Yii;

// CMSGears Imports
use cmsgears\core\common\services\SiteService;

class Application extends \yii\web\Application {

    public function createController( $route ) {

		// find whether multisite is enabled
		if( Yii::$app->cmgCore->multiSite && Yii::$app->cmgCore->subDirectory ) {

	        if( $route === '' ) {
	
	            $route = $this->defaultRoute;
	        }
	
	        // double slashes or leading/ending slashes may cause substr problem
	        $route = trim( $route, '/' );

	        if( strpos( $route, '//' ) !== false ) {

	            return false;
	        }

	        if( strpos( $route, '/' ) !== false ) {

				list ( $site, $siteRoute ) = explode( '/', $route, 2 );

				// Find Site
				$site = SiteService::findByName( $site );

				// Site Found
				if( isset( $site ) ) {

					Yii::$app->cmgCore->siteName = $site->name;

					return parent::createController( $siteRoute );	
				}
	        }
		}

       return parent::createController( $route );
    }
}

?>