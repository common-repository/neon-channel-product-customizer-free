<?php
namespace NCPC;
use NCPC_Product_Config;
use WP_Query;

/**
 * Frontend Pages Handler
 */
class Frontend {

    public function __construct() {
        add_shortcode( 'ncpc-configurator', [ $this, 'display_configurator' ] );
        add_shortcode('ncpc-products',[$this,'get_products_display']);
    }

    /**
     * Render frontend app
     *
     * @param  array $atts
     * @param  string $content
     *
     * @return string
     */
    public function display_configurator( $atts, $content = '' ) {
        wp_enqueue_style( 'ncpc-frontend', NCPC_ASSETS . '/css/frontend.css',array(), NCPC_VERSION, 'all');
        wp_enqueue_style( 'ncpc-style',NCPC_ASSETS . '/css/style.css',array(), NCPC_VERSION, 'all' );
        wp_enqueue_script( 'ncpc-runtime',NCPC_ASSETS . '/js/runtime.js',[],NCPC_VERSION,true );
        wp_enqueue_script( 'ncpc-vendor',NCPC_ASSETS . '/js/vendors.js',[],NCPC_VERSION,true );
        wp_enqueue_script( 'ncpc-frontend',NCPC_ASSETS . '/js/frontend.js',[ 'jquery' ],NCPC_VERSION,true );

        
        extract( // phpcs:ignore
			shortcode_atts(
				array(
					'productid' => '0'
				),
				$atts,
                'ncpc-products'
			)
		);
        $product =wc_get_product($productid);
        ob_start();
        ?>
            <div id="ncpc-configurator-loader" style="height:90vh;">
                <div style="display: flex; flex: 1;width: 100%; height: 100%">
                    <div
                        style="display: flex; flex-direction: column; width: 92%; height: 100%; margin-left: 10px; margin-right: 10px;">
                        <div style="width: 100%; height: 10%; margin-top: 10px; display: flex; justify-content: space-between;">
                            <div style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 21.33%; height: 100%; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                            </div>
                            <div style="width: 31.33%; height: 100%; display: flex; justify-content: space-between;">
                                <div
                                    style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 21.33%; height: 100%; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                                <div
                                    style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 21.33%; height: 100%; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                                <div
                                    style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 21.33%; height: 100%; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                            </div>
                            <div style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 21.33%; height: 100%; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                            </div>
                        </div>

                        <div style="width: 100%; height: 90%; margin-top: 20px;">
                            <div style="width: 100%; height: 80%; display: flex; justify-content: center; align-items: center;">
                                <div style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 40%; height: 50%; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                            </div>

                            <div
                                style="width: 100%; height: 20%; display: flex; justify-items: center; justify-content: center; align-items: center;">
                                <div
                                    style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 7%; height: 60%; margin-left: 5px; margin-right: 5px; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                                <div
                                    style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 7%; height: 60%; margin-left: 5px; margin-right: 5px; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                                <div
                                    style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 7%; height: 60%; margin-left: 5px; margin-right: 5px; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                                <div
                                    style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 7%; height: 60%; margin-left: 5px; margin-right: 5px; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                                <div
                                    style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 7%; height: 60%; margin-left: 5px; margin-right: 5px; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                                <div
                                    style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 7%; height: 60%; margin-left: 5px; margin-right: 5px; animation:ncpc-pulse-gradient 1.5s ease infinite;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; width: 8%; height: 100%; margin-left: 20px; margin-right: 20px;">
                        <div
                            style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 100%; height: 10%; margin-top: 10px; animation: ncpc-pulse-gradient 1.5s ease infinite;">
                        </div>
                        <div
                            style="width: 100%; height: 90%; display: flex; flex-direction: column; justify-items: center; justify-content: center; align-items: center;">
                            <div
                                style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 90%; height: 10%; animation: ncpc-pulse-gradient 1.5s ease infinite;">
                            </div>
                            <div
                                style="background: linear-gradient(to right, #a1a1a1, #e4e4e7, #a1a1a1); width: 90%; height: 10%; margin-top: 20px; animation: ncpc-pulse-gradient 1.5s ease infinite;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            if($product){

                $meta = get_post_meta($productid,'product-ncpc-metas',true);
                
                if(!empty($meta) && isset($meta[$productid]['config-id'])){
                    $configId = $meta[$productid]['config-id'];
                    if($configId !=0){
                        $config = get_post_meta($configId,"ncpc-configs-meta",true);
                        $othersData = get_option("ncpc_global_settings_others_data",[]);
                        if(is_array($config) && !empty($config)){
                            $config["others"] = $othersData;
                            $types = wp_get_post_terms( $configId,'ncpc-products-type');
                            $type ='';
                            foreach ($types as $key => $value) {
                            $type = $value->name;
                            }
                            $modes = wp_get_post_terms( $configId,'ncpc-pricings-mode');
                            $mode = '';
                            foreach ($modes as $key => $value) {
                                $mode = $value->name;
                            }
                            $configData = array(
                                'id'           => $configId,
                                'title'        => get_the_title(),
                                'productType' => strtolower($type),
                                'pricingMode'   => strtolower($mode),
                                "data" => $config
                            );
                            $fonts = $config['requiredOptions']['fontOptions']['fonts'];
                            $product = wc_get_product($productid);
                            $product_price   = $product->get_price();
                            //$price_format  = ncpc_pro_get_price_format();
                            $available_variations = array();
                            if ( $product->get_type() === 'variable' ) {
                                $available_variations = $product->get_available_variations();
                            }
                            
                            $NCPC = array(
                                'skin' => $config['settings']['themes']['skin'],
                                'productID' => $productid,
                                'product'   => $product,
                                'currentConfig' => $configData,
                                'regularPrice'       => trim($product_price) != '' ? $product_price : 0 ,
                                'thousandSep'        => wc_get_price_thousand_separator(),
                                'decimalSep'         => wc_get_price_decimal_separator(),
                                'decimals'           => wc_get_price_decimals(),
                                'nbDecimals'         => wc_get_price_decimals(),
                                'currencySymbol'     => html_entity_decode(get_woocommerce_currency_symbol()),
                                'currency_pos'       => get_option('woocommerce_currency_pos'),
                                'variations'          => $available_variations,
                                "ncpc_rest_url"=> get_rest_url().'ncpc/v1',
                                
                            );
                            $this->includes_config_fonts($fonts);
                            if( isset($config['settings']['themes']['customCSS']) &&  !empty($config['settings']['themes']['customCSS'])){
                                wp_add_inline_style( 'ncpc-frontend',$config['settings']['themes']['customCSS'] );
                            }
                            wp_localize_script("ncpc-frontend","ncpcData",$NCPC);
                            ?>
                                <div id='ncpc-frontend-app'></div>
                            <?php 
                            wp_localize_script("ncpc-frontend","ncpc_ajax",["ajax_url"=>admin_url('admin-ajax.php'),"nonce"=>wp_create_nonce('ncpc_add_to_cart_after_custom')]);

                        }
                    }
                }
            }
        $content.=ob_get_clean();
        return $content;
    }

    private function includes_config_fonts($fonts) {
        foreach ( $fonts as $font ) {
            if ( ! empty( $font['url'] ) && $font['isGoogleFont'] ) {
                 $font_url   = str_replace( 'http://', '//', $font['url'] );
                 $this->include_config_ttf_font_style($font['label'],$font_url);
            } elseif ( ! empty( $font['url'] ) ) {
                $this->include_config_ttf_font_style($font['label'],$font['url']);
            }
        }
    }
    
    private function include_config_ttf_font_style( $font_label,$url ) {
        $font_label = str_replace(" ", "-", $font_label);
        $inline_style = "@font-face {
            font-family: ".esc_html( $font_label).";
            src: url('".esc_url($url)."') format('truetype');
        }";
        wp_add_inline_style( 'ncpc-frontend', $inline_style );
    }
    
}
