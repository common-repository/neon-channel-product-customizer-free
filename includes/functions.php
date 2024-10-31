<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	/**
	 * save preview image
	 */
	function ncpc_save_canvas_image( $image, $file_name) {
		$upload_dirs = NCPC_IMAGE_PATH;
		wp_mkdir_p( $upload_dirs );
		$upload_dir = $upload_dirs . DIRECTORY_SEPARATOR;
		$img        = str_replace( 'data:image/png;base64,', '', $image );
		$img        = str_replace( ' ', '+', $img );
		$data       = base64_decode( $img ); // phpcs:ignore
		$file       = $upload_dir . $file_name . '.png';
		$success1    = file_put_contents( $file, $data ); // phpcs:ignore
        $preview_img = NCPC_IMAGE_URL . '/' . $file_name . '.png';
		return $preview_img;
	}
	
	/**
	 * add or edit product to cart
	 */
	function ncpc_add_custom_design_to_cart_ajax() {
        if (wp_verify_nonce(sanitize_text_field( wp_unslash ($_POST['nonce'])), 'ncpc_add_to_cart_after_custom')) {
            if (isset($_POST['data']['variation_id'])) {
                $main_variation_id = intval($_POST['data']['variation_id']);
                $quantity = isset($_POST['data']['quantity']) ? intval($_POST['data']['quantity']) : 1; 
                $cart_item_key = isset($_POST['data']['cart_item_key']) ? sanitize_key($_POST['data']['cart_item_key']): false;
                $preview_img  = isset($_POST['data']['ncpc_preview_img']) ? sanitize_text_field($_POST['data']['ncpc_preview_img']) : '';
                $recaps = isset($_POST['data']['ncpc_recaps']) ? map_deep( $_POST['data']['ncpc_recaps'], 'sanitize_text_field' ) : [];
                $message = '';
                
                $cart_url = wc_get_cart_url();
                $_SESSION['ncpc_calculated_totals'] = false;
                
                $newly_added_cart_item_key = false;
                $file_name                     = uniqid( 'ncpc-' );
                $preview_img = ncpc_save_canvas_image( $preview_img, $file_name );
                if ( $cart_item_key ) {
                    WC()->cart->cart_contents[ $cart_item_key ]['ncpc_recaps'] = $recaps;
                    WC()->cart->cart_contents[ $cart_item_key ]['ncpc_preview_img']    = $preview_img;
                    WC()->cart->calculate_totals();
                    wp_send_json(array(
                        'success'     => true
                    ));
                } else {
                    $newly_added_cart_item_key = ncpc_add_designs_to_cart($main_variation_id, $recaps, $preview_img,$quantity);
                    
                    if ( $newly_added_cart_item_key ) {
                        $message =  __( 'Product successfully added to cart.', 'neon-channel-product-customizer-free');
                        wp_send_json(array(
                            'success'     => true,
                            'cart_item_key'     => $newly_added_cart_item_key,
                            'message'     => $message,
                            'url'         => $cart_url,
                            'form_fields' => $recaps,
            
                        ));
                    } else {
                        $message = __( 'A problem occured while adding the product to the cart. Please try again.', 'neon-channel-product-customizer-free');
                        wp_send_json(array(
                            'success'     => false,
                            'message'     => $message,
            
                        ));
                    }
                
                }

                /* ob_start();
                woocommerce_mini_cart();
                $mini_cart = ob_get_clean(); */
            } else {
                wp_send_json(array('message' => 'ID produit manquant.'));
            }
        }
	}

	/**
	 *  add product to cart
	 */
	function ncpc_add_designs_to_cart( int $product_id,array $recaps, string $preview_img,int $quantity) {
		$newly_added_cart_item_key = false;
        $product = wc_get_product( $product_id );
        $parent_id = $product->get_parent_id();
        if($parent_id == 0){
            $newly_added_cart_item_key = WC()->cart->add_to_cart(
                $product_id,
                $quantity,
                0,
                array(),
                array(
                    'ncpc_recaps' => $recaps,
                    'ncpc_preview_img'    => $preview_img,
                )
            );
        }else{
            $variation  = $product->get_variation_attributes();
            $newly_added_cart_item_key = WC()->cart->add_to_cart(
                $product_id,
                $quantity,
                0,
                $variation,
                array(
                    'ncpc_recaps' => $recaps,
                    'ncpc_preview_img'    => $preview_img,
                )
            );
        }

			/* if ( isset( $_SESSION['npd_key'] ) ) {
				$variations = get_transient( $_SESSION['npd_key'] );
			}

			foreach ( $variation as $key => $value ) {
				if ( isset( $variations[ $key ] ) && '' === $value ) {
					$variation[ $key ] = $variations[ $key ];
				}
			}

			if ( isset( $_SESSION['combinaison'][ $variation_name ] ) ) {
				$variation = $_SESSION['combinaison'][ $variation_name ];
			} */

			if ( method_exists( WC()->cart, 'maybe_set_cart_cookies' ) ) {
				WC()->cart->maybe_set_cart_cookies();
			}
		return $newly_added_cart_item_key;
	}

    /**
     *  includes ajax in plugin 
     */
    add_action( 'wp_ajax_ncpc_add_custom_design_to_cart', 'ncpc_add_custom_design_to_cart_ajax' );
    add_action( 'wp_ajax_nopriv_ncpc_add_custom_design_to_cart', 'ncpc_add_custom_design_to_cart_ajax' );
    function ncpc_get_price_format() {
        $currency_pos = get_option( 'woocommerce_currency_pos' );
        $format       = '%s%v';

        switch ( $currency_pos ) {
            case 'left':
                $format = '%s%v';
                break;
            case 'right':
                $format = '%v%s';
                break;
            case 'left_space':
                $format = '%s %v';
                break;
            case 'right_space':
                $format = '%v %s';
                break;
            default:
                $format = '%s%v';
                break;
        }
        return $format;
    }
    function ncpc_get_custom_products() {
        global $wpdb;
        
        // Requête initiale avec des espaces réservés pour la clé de méta et des conditions dynamiques.
        $query = "SELECT p.id FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} pm ON pm.post_id = p.id WHERE p.post_type = 'product' AND pm.meta_key = %s";
        
        // Tableau de paramètres initial avec la clé de méta.
        $params = ['product-ncpc-metas'];
        
        // Initialiser le tableau des conditions.
        $conditions = [];
        for ($i = 1; $i <= 9; $i++) {
            // Ajouter des espaces réservés pour les conditions dans la requête elle-même.
            $conditions[] = "pm.meta_value LIKE %s";
            // Préparer la valeur correspondante pour chaque espace réservé.
            $params[] = '%config-id";s:' . $i . ':%';
        }
        
        // Si les conditions ne sont pas vides, les ajouter à la requête.
        if (!empty($conditions)) {
            $query .= " AND (" . implode(' OR ', $conditions) . ")";
        }
        
        // Exécuter la requête préparée de manière sécurisée.
        $products = $wpdb->get_results(
            $wpdb->prepare(
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                $query, 
                $params
            )
        );
        
        return $products;
    }
    
    
    