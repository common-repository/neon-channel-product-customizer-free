<?php
namespace NCPC;

use NCPC\Api\Admin\NCPC_Api_GoogleFonts;
use NCPC\Api\Admin\Required_Options\NCPC_Api_Colors;
use NCPC\Api\Admin\NCPC_Api_Configs;
use NCPC\Api\Admin\Required_Options\NCPC_Api_Fonts;
use NCPC\Api\Admin\Required_Options\NCPC_Api_Prices;
use NCPC\Api\Admin\Required_Options\NCPC_Api_Sizes;
use NCPC\Api\Admin\Settings\NCPC_Api_Custom_Design_Settings;
use NCPC\Api\Admin\Settings\NCPC_Api_General_Settings;
use NCPC\Api\Admin\Settings\NCPC_Api_Language_Images_Settings;
use NCPC\Api\Admin\Settings\NCPC_Api_Sort_Options_Settings;
use NCPC\Api\Admin\Settings\NCPC_Api_Theme_Settings;
use NCPC\Api\Globals_Settings\NCPC_Api_Globals_Settings;
use WP_REST_Controller;

/**
 * REST_API Handler
 */
class Api extends WP_REST_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->includes();

        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /**
     * Include the controller classes
     *
     * @return void
     */
    private function includes() {
        //include config class file
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Configs'  ) ) {
            require_once __DIR__ . '/Admin/Configs.php';
        }

        //include all required options class files
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Required-Options\Sizes'  ) ) {
            require_once __DIR__ . '/Admin/Required-Options/Sizes.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Required-Options\Prices'  ) ) {
            require_once __DIR__ . '/Admin/Required-Options/Prices.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Required-Options\Colors'  ) ) {
            require_once __DIR__ . '/Admin/Required-Options/Colors.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Required-Options\Fonts'  ) ) {
            require_once __DIR__ . '/Admin/Required-Options/Fonts.php';
        }        

        //include all settings class files
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Settings\Generals'  ) ) {
            require_once __DIR__ . '/Admin/Settings/Generals.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Settings\Language-Images'  ) ) {
            require_once __DIR__ . '/Admin/Settings/Language-Images.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Settings\Themes'  ) ) {
            require_once __DIR__ . '/Admin/Settings/Themes.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Settings\Custom-Design'  ) ) {
            require_once __DIR__ . '/Admin/Settings/Custom-Design.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\Settings\Sort-Options'  ) ) {
            require_once __DIR__ . '/Admin/Settings/Sort-Options.php';
        }

        if ( !class_exists( __NAMESPACE__ . '\Api\Admin\googleFonts'  ) ) {
            require_once __DIR__ . '/Admin/googleFonts.php';
        }
        //include all global settings files
        if ( !class_exists( __NAMESPACE__ . '\Api\Globals-Settings\Global-Settings'  ) ) {
            require_once __DIR__ . '/Globals-Settings/Global-Settings.php';
        }
        
    }

    /**
     * Register the API routes
     *
     * @return void
     */
    public function register_routes() {

        //add all required options routes
        (new NCPC_Api_Configs())->register_routes();
        (new NCPC_Api_Sizes())->register_routes();
        (new NCPC_Api_Prices())->register_routes();
        (new NCPC_Api_Colors())->register_routes();
        (new NCPC_Api_Fonts())->register_routes();

        //add all settings options routes
        (new NCPC_Api_General_Settings())->register_routes();
        (new NCPC_Api_Language_Images_Settings())->register_routes();
        (new NCPC_Api_Custom_Design_Settings())->register_routes();
        (new NCPC_Api_Theme_Settings())->register_routes();
        (new NCPC_Api_Sort_Options_Settings())->register_routes();

        //add al globals settings routes

        (new NCPC_Api_Globals_Settings())->register_routes();

        (new NCPC_Api_GoogleFonts())->register_routes();
    }

}
