<?php
namespace NCPC;

/**
 * Scripts and Styles Class
 */
class Assets {

    function __construct() {

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'register' ], 5 );
        } else {
            add_action( 'wp_enqueue_scripts', [ $this, 'register' ], 5 );
        }
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register() {
        $this->register_scripts( $this->get_scripts() );
        $this->register_styles( $this->get_styles() );
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    private function register_scripts( $scripts ) {
        // Return if JS folder not exists
        if (!is_dir(NCPC_PATH . '/assets/js')) {
            return;
        }

        foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
            $version   = isset( $script['version'] ) ? $script['version'] : NCPC_VERSION;

            wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function register_styles( $styles ) {
        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, NCPC_VERSION );
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_scripts() {
        // Return if JS folder not exists
        if (!is_dir(NCPC_PATH . '/assets/js')) {
            return;
        }

        $prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '';

        $scripts = [
            'ncpc-runtime' => [
                'src'       => NCPC_ASSETS . '/js/runtime.js',
                'version'   => filemtime( NCPC_PATH . '/assets/js/runtime.js' ),
                'in_footer' => true
            ],
            'ncpc-vendor' => [
                'src'       => NCPC_ASSETS . '/js/vendors.js',
                'version'   => filemtime( NCPC_PATH . '/assets/js/vendors.js' ),
                'in_footer' => true
            ],
            'ncpc-frontend' => [
                'src'       => NCPC_ASSETS . '/js/frontend.js',
                'deps'      => [ 'jquery', 'ncpc-vendor', 'ncpc-runtime' ],
                'version'   => filemtime( NCPC_PATH . '/assets/js/frontend.js' ),
                'in_footer' => true
            ],
            'ncpc-admin' => [
                'src'       => NCPC_ASSETS . '/js/admin.js',
                'deps'      => [ 'jquery', 'ncpc-vendor', 'ncpc-runtime' ],
                'version'   => filemtime( NCPC_PATH . '/assets/js/admin.js' ),
                'in_footer' => true
            ],/* 
            'ncpc-editor-scripts' => [
                'src' => NCPC_ASSETS.'/utilities/ncpc-editor-script.js',
                'in_footer' => true
            ], */
            'ncpc-product-design'=>[
                'src' => NCPC_ASSETS.'/utilities/product-design.js',
                'in_footer' => true
            ],
            'ncpc-product-pricing'=>[
                'src' => NCPC_ASSETS.'/utilities/ncpc-product-pricing.js',
                'in_footer' => true
            ],
            'ncpc-html2canvas'=>[
                'src' => NCPC_ASSETS.'/utilities/html2canvas.min.js',
                'in_footer' => true
            ],
            'ncpc-sortable'=>[
                'src' => NCPC_ASSETS.'/utilities/sortable.js',
                'in_footer' => true
            ],
            'ncpc-toast-js'=>[
                'src' => NCPC_ASSETS.'/utilities/toast.min.js',
                'in_footer' => true
            ],
            'ncpc-omodal'=>[
                'src' => NCPC_ASSETS.'/utilities/modal.min.js',
                'in_footer' => true
            ],
        ];

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles() {

        $styles = [
            'ncpc-style' => [
                'src' =>  NCPC_ASSETS . '/css/style.css'
            ],
            'ncpc-frontend' => [
                'src' =>  NCPC_ASSETS . '/css/frontend.css'
            ],
            'ncpc-admin' => [
                'src' =>  NCPC_ASSETS . '/css/admin.css'
            ],
            'ncpc-toast-css'=>[
                'src' => NCPC_ASSETS.'/utilities/toast.min.css'
            ]
            ,
            'ncpc-omodal-css'=>[
                'src' => NCPC_ASSETS.'/utilities/modal.min.css'
            ]
        ];

        return $styles;
    }

}