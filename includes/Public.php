<?php
namespace NCPC;

/**
 * public Pages Handler
 */
class NCPC_Public {

    public function __construct() {
        $this->render_public();
    }

    /**
     * Render public app
     *
     * @param  array $atts
     * @param  string $content
     *
     * @return string
     */
    public function render_public( $content = '' ) {
        wp_enqueue_style( 'ncpc-style',NCPC_ASSETS . '/css/style.css',array(), NCPC_VERSION, 'all' );
        wp_enqueue_style( 'ncpc-omodal-css',NCPC_ASSETS.'/utilities/modal.min.css',array(), NCPC_VERSION, 'all' );
        wp_enqueue_script('ncpc-omodal',NCPC_ASSETS.'/utilities/modal.min.js',array(),NCPC_VERSION,true);
        wp_enqueue_script('ncpc-product-design',NCPC_ASSETS.'/utilities/product-design.js',array('jquery'), NCPC_VERSION, true);
        return $content;
    }
}
