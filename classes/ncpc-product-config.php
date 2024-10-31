<?php

 /**
 * the class summarizing the actions of ncpc on a product
 */
 class NCPC_Product_Config {

    //ID of a product variant or not
    public $variation_id;

    //product root  ID
    public $root_product_id;

    //the product itself
    public $product;

    //product related settings
    public $settings;

    //Class contructor
    public function __construct( $id=0 ) {
		if ( $id!=0 ) {
			$this->root_product_id = $this->get_parent( $id );
			// If it's a variable product
			if ( $id !== $this->root_product_id ) {
				$this->variation_id = $id;
			}// Simple product and others
			else {
				$this->variation_id = $this->root_product_id;
			}
			$this->product = wc_get_product( $id );

			$config        = get_post_meta( $this->variation_id, 'product-ncpc-metas', true );

			if ( isset( $config[ $this->variation_id ] ) ) {
				$config_id = $config[ $this->variation_id ];
				if ( isset($config_id['config-id']) ) {
					$this->settings = get_post_meta( $config_id['config-id'], 'ncpc-configs-meta', true );
				}
			}
		}
    }
	/**
	 * All hooks related to attaching a product config
	 */
	public function init_hooks(){
		add_action( 'woocommerce_product_options_general_product_data', array($this,'get_product_config_selector'));
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'get_variation_product_config_selector' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array($this, 'save_config') );
		add_action( 'save_post_product', array($this, 'save_config' ));
		add_filter( 'manage_edit-product_columns', array($this, 'get_product_columns'),10 );
		add_filter( 'woocommerce_cart_item_thumbnail', [$this, 'get_ncpc_data_image'], 99, 3 );
		add_filter('woocommerce_after_cart_item_name', [$this,'display_previewBtn_editBtn_in_cart'], 10, 2);
		add_action( 'woocommerce_before_calculate_totals', [$this, 'get_cart_item_price'], 10 );
		add_action( 'manage_product_posts_custom_column', array($this, 'get_products_columns_values'), 10, 2 );
		add_action( 'woocommerce_after_add_to_cart_button', array($this, 'hide_cart_button' ));
		add_action( 'woocommerce_after_add_to_cart_button', array($this, 'get_customize_btn' ));
		add_filter( 'woocommerce_loop_add_to_cart_link', array($this, 'get_customize_btn_on_shop_page'), 10, 2 );
		add_action( 'woocommerce_single_product_summary', array($this,'get_button_on_single_product_summary'), 5 );
		add_action('woocommerce_cart_item_removed', [$this,'delete_product_file_when_delete_product'], 10, 2);
	}

	public function delete_product_file_when_delete_product($cart_item_key, $cart) {
		$cart_item_content = $cart->removed_cart_contents[$cart_item_key];
		if (!empty($cart_item_content)  && isset($cart_item_content["ncpc_preview_img"])) {
			$path_parts = pathinfo($cart_item_content["ncpc_preview_img"]);
			if (isset($path_parts["filename"], $path_parts['extension'])) {
				$file = NCPC_IMAGE_PATH . DIRECTORY_SEPARATOR . $path_parts["filename"] . '.' . $path_parts['extension'];
				if (file_exists($file)) {
					wp_delete_file($file);
				}
			}
		}
	}

	/**
	 * 
	 */
	public function get_cart_item_price( $cart ) {
		if (is_admin() && !defined('DOING_AJAX')) return;

    	if (did_action('woocommerce_before_calculate_totals') >= 2) return;

		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
			if ( $cart_item['variation_id'] ) {
				$variation_id = $cart_item['variation_id'];
			} else {
				$variation_id = $cart_item['product_id'];
			}


			if ( isset( $cart_item['ncpc_recaps'] ) ) {
				$a_price = 0;
				if ( isset( $cart_item['ncpc_recaps']['ncpc_custom_price'] ) ) {
					$a_price += $cart_item['ncpc_recaps']['ncpc_custom_price'];
				}
				
				$item_price = $a_price;
				$item_price = apply_filters( 'ncpc_cart_item_price', $item_price, $variation_id );
	
				$cart_item['data']->set_price( $item_price );
			}

			// Ajout d'un filtre pour mettre à jour le prix total de l'element dans le panier.
		}
	}

	/**
	 * 
	 */
	public function display_previewBtn_editBtn_in_cart($cart_item, $cart_item_key){
		
		$product = $cart_item['data'];
		
		// Construisez les URL pour les aperçus et les éditions (ajustez selon vos besoins)
		$preview_url = get_permalink($product->get_id());

		//$preview_data = get_transient( 'preview_' . $product->get_id() );

		$npd_product = new NCPC_Product_Config( $product->get_id() );

		$edit_url = $npd_product->get_design_url() . '?edit=' . $cart_item_key;
		$product_name = '';
		if(isset($cart_item['ncpc_recaps'])){
			$modal_id = uniqid();
			ob_start();
			?>
			
			<div class="omodal fade o-modal wpc_part" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="omodal-dialog">
					<div class="omodal-content">
						<div class="omodal-header">
							<button type="button" class="close" data-dismiss="omodal" aria-hidden="true">&times;</button>
						</div>
						<div class="omodal-body">
							<?php foreach( $cart_item['ncpc_recaps'] as $key=> $value) {?>
								<?php if($key !='ncpc_custom_price' && $key != 'ncpc_additional_option') {
									$name = explode('_', $key);
									if(in_array('color', $name) || in_array('face', $name) || in_array('trim', $name) || in_array('side', $name) || in_array('back', $name)) {
										
										$name = $value['label'];
										
										$value = explode('/', $value['value']);
										$resultat = preg_split("/\)|,|\(/", $value[1], -1, PREG_SPLIT_NO_EMPTY);
										$resultat = implode(",", $resultat);
										$elements = count(explode(",", $resultat));
										$resultat=trim($resultat);
										?>
										<div class="ncpc-custom-options-info">
											<label for=""><?php echo esc_html($name)?>: </label>
											<span><?php echo esc_html($value[0])?></span>
											<?php if($elements > 1) { ?>
												<div class="ncpc-cart-color-option" style="background:linear-gradient(to right bottom,<?php echo esc_attr($resultat)?>);"></div>
											<?php }else{ ?>
												<div class="ncpc-cart-color-option" style="background:<?php echo esc_attr($resultat)?>;"></div>
											<?php } ?>
										</div>
							<?php	}else{
										$name = $value['label'];
										if($key == "ncpc_text"){ ?>
											<div class="ncpc-custom-options-info">
												<div style="display:flex; justify-content: center;">
													<label for=""><?php echo esc_html($name)?>: </label>
													<div style="display:flex; flex-direction:column; justify-content:center;">
														<?php
															foreach ($value['value'] as $key => $v) {?>
																<span><?php echo esc_html($v)?></span>
														<?php } ?>
													</div>
												</div>
											</div>
										<?php }else{ ?>
										
												<div class="ncpc-custom-options-info">
													<label for=""><?php echo esc_html($name)?>: </label>
													<span><?php echo esc_html($value['value'])?></span>
												</div>
									<?php 	}
									
									}
									
								}else if($key == 'ncpc_additional_option'){
									foreach($value as $value2) { ?>
										<div class="ncpc-custom-options-info">
											<label for=""><?php echo esc_html($value2["label"])?>: </label>
											<span><?php echo esc_html($value2["value"])?></span>
										</div>
							<?php 	}
								}
									?>
					<?php 	} ?>
						</div>
					</div>
				</div>
			</div>
			<div class="ncpc-product-links">
				<span class="ncpc-cart-product-preview o-modal-trigger button" data-toggle="o-modal" data-target="#<?php echo esc_attr($modal_id); ?>"><?php echo esc_html__("Custom Options","neon-channel-product-customizer-free")?></span>
			</div>
			<?php
			$product_name.=ob_get_clean();		
		}
		echo wp_kses_post($product_name);
	}
	
	/**
	 * 
	 */
	public function get_ncpc_data_image( $product_image_code, $values, $cart_item_key ) {
		if ( $values['variation_id'] ) {
			$product_id = $values['variation_id'];
		} else {
			$product_id = $values['product_id'];
		}
		if ( isset( $values['ncpc_preview_img'] ) && !empty( $values['ncpc_preview_img'] ) ) {
			$product_image_url  = $values['ncpc_preview_img'];
			$product_image_code = "<img class='ncpc-cartitem-img' src='" . $product_image_url . "'>";
		}

		return $product_image_code;
	}
    /**
     * @param int $id lidentifier of the product
     * @return int the identifier of the parent of the product 
     * if it is a variable product otherwise returns the same identifier
     */
    public function get_product_parent_ID(int $id){
        $variable_product = wc_get_product( $id );
		if ( ! $variable_product ) {
			return false;
		}
		if ( $variable_product->is_type('variable') ) {
			$product_id = $id;
		} else {
			$product_id = $variable_product->get_parent_id();
		}

		return $product_id;
    }

	/**
	 * Configuration Selector from product admin page
	 */
	public function get_product_config_selector() {
		$id = get_the_ID();

		$args        = array(
			'post_type' => 'ncpc-configs',
			'nopaging'  => true,
		);
		$configs     = get_posts( $args );
		
		$configs_ids = array( ['' => 'None'] );
		foreach ( $configs as $config ) {
			$configs_ids[ $config->ID ] = ['title'=>$config->post_title];
		}
		?>
		<div class="ncpc_config_data show_if_simple">
			<?php
			$this->display_ncpc_config_on_WC_product_config( $id, $configs_ids, __('Attach this product to a neon or channel configuration of NCPC',"neon-channel-product-customizer-free") );
			?>
		</div>
		<?php
	}

	/**
	* Assign configuration to product
	*/
	private function display_ncpc_config_on_WC_product_config( $pid, $configs_ids, $title ) {
		$meta = get_post_meta($pid, 'product-ncpc-metas', true);
		$container = "<div class='bg-black p-4'>" .
			"<h2>" . esc_html($title) . "</h2>" .
			"<div>" .
			"<select name='product-ncpc-metas[" . esc_attr($pid) . "][config-id]'>";
	
		foreach ($configs_ids as $id => $values) {
			foreach ($values as $value) {
				$container .= "<option value='" . esc_attr($id) . "' ";
				if (isset($meta[$pid]["config-id"]) && $meta[$pid]["config-id"] == $id) {
					$container .= ' selected';
				}
				$container .= ">" . esc_html($value) . "</option>";
			}
		}
	
		$container .= "</select>";
		$container .= "<input type='hidden' name='ncpc_config_nonce' value='" . wp_create_nonce('ncpc_config_nonce') . "'/>";
		$container .= "</div></div>";
	
		// Définition des balises HTML autorisées et de leurs attributs
		$allowed_html = array(
			'div' => array(
				'class' => array(),
			),
			'h2' => array(),
			'select' => array(
				'name' => array(),
			),
			'option' => array(
				'value' => array(),
				'selected' => array(),
			),
			'input' => array(
				'type' => array(),
				'name' => array(),
				'value' => array(),
			),
		);
	
		// Affichage sécurisé de $container
		echo wp_kses($container, $allowed_html);
	}	

	/**
	 * Assign configuration to product variation
	 */
	public function get_variation_product_config_selector( $loop, $variation_data, $variation ) {
		$id = $variation->ID;

		// Ici, vous pouvez ajouter un code spécifique si nécessaire pour chaque variation
		// par exemple, vérifier les propriétés ou les attributs de la variation, si vous avez besoin de personnaliser davantage.

		
		$args        = array(
			'post_type' => 'ncpc-configs',
			'nopaging'  => true,
		);
		$configs     = get_posts( $args );
		$configs_ids = array( ['' => 'None'] );
		foreach ( $configs as $config ) {
			$configs_ids[ $config->ID ] = ['title'=> $config->post_title];
		}
		?>
		<tr>
			<td>
				<div class="ncpc_config_data show_if_simple">
				<?php
				$this->display_ncpc_config_on_WC_product_config( $id, $configs_ids, __('Attach this product variation to a neon or channel configuration of NCPC',"neon-channel-product-customizer-free") );
				?>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save the chosen configuration to the product
	 */
	public function save_config( $post_id ) {
		$meta_key = 'product-ncpc-metas';
		if ( isset($_POST['ncpc_config_nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['ncpc_config_nonce'] ) ) , 'ncpc_config_nonce' ) && isset( $_POST[ $meta_key ] ) ) {
			$meta_value = map_deep( $_POST[ $meta_key ], 'sanitize_text_field' );
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}
	
	/**
	 * Adds the Custom column to the default products list to help identify which ones are custom
	 *
	 * @param array $defaults Default columns
	 * @return array $defaults result
	 */
	function get_product_columns( $defaults ) {
		$defaults['is_ncpc_customizable'] = __( 'NCPC', 'neon-channel-product-customizer-free' );
		return $defaults;
	}
	/**
	 * Sets the Custom column value on the products list to help identify which ones are custom
	 *
	 * @param string $column_name Column name
	 * @param int $id Product ID
	 */
	public function get_products_columns_values( $column_name, $id ) {
		if ( $column_name === 'is_ncpc_customizable' ) {
			$ncpc_metas = get_post_meta( $id, 'product-ncpc-metas', true );
			if ( isset( $ncpc_metas[ $id ]['config-id'] ) && get_post($ncpc_metas[ $id ]['config-id'])) {
				if ( empty( $ncpc_metas[ $id ]['config-id'] ) ) {
					esc_attr_e( 'No', 'neon-channel-product-customizer-free' );
				} else {
					$types = wp_get_post_terms($ncpc_metas[ $id ]['config-id'],'ncpc-products-type');
					$type ='';
					foreach ($types as $key => $value) {
						$type = $value->name;
					}
					esc_attr_e( "Neon", 'neon-channel-product-customizer-free' );
				}
			} else {
				esc_attr_e( 'No', 'neon-channel-product-customizer-free' );
			}
		}
	}

	public function is_ncpc_customizable() {
		return ( ! empty( $this->settings ) );
	}


	public function get_buttons( $with_upload = false ) {
		ob_start();
		$content      = '';
		$product      =  $this->product;
		$ncpc_metas    = $this->settings;
		$product_page = get_permalink( $product->get_id() );

		if ( $this->variation_id ) {
			$item_id = $this->variation_id;
		} else {
			$item_id = $this->root_product_id;
		}
		
		if ( $product->is_type('variable')) {
			$variations = $product->get_available_variations();
			foreach ( $variations as $variation ) {
				if ( ! $variation['is_purchasable'] || ! $variation['is_in_stock'] ) {
					continue;
				}
				$ncpc_product = new NCPC_Product_Config( $variation['variation_id'] );
				if( $ncpc_product->is_ncpc_customizable() ) {
					echo wp_kses_post($ncpc_product->get_buttons( $with_upload ));
				}
			}
			
		} else {
			
			?>
			<div class="ncpc-buttons-wrap-<?php echo esc_attr($product->get_type()); ?>" data-id="<?php echo esc_attr($this->variation_id); ?>">
					
			<?php

			
			$default_design_btn_url = $this->get_design_url();
			$content               .= '<a  href="' . $default_design_btn_url . '" class="button ncpc-design-product">' . apply_filters( 'ncpc_default_design_btn_filter', __( 'Customize the product', 'neon-channel-product-customizer-free' ) ) . '</a>';

			if ( ! isset( $item_id ) ) {
				$item_id = '';
			}
			if ( ! isset( $default_design_btn_url ) ) {
				$default_design_btn_url = '';
			}
			echo wp_kses_post(apply_filters( 'ncpc_show_customization_buttons_in_modal', wp_kses_post($content), $item_id, $default_design_btn_url, $product->get_type() ));
			?>
			</div>
			<?php
		}
		
		$output = ob_get_clean();
		return $output;
	}

		/**
	 * Returns the customization page URL
	 *
	 * @global array $ncpc_settings
	 * @param int   $design_index Saved design index to load
	 * @param mixed $cart_item_key Cart item key to edit
	 * @param int   $order_item_id Order item ID to load
	 * @param int   $tpl_id ID of the template to load
	 * @return string
	 */
	public function get_design_url( $design_index = false, $cart_item_key = false, $order_item_id = false, $tpl_id = false ) {
		global $ncpc_settings;
		//global $wp_query;
		if ( $this->variation_id ) {
			$item_id = $this->variation_id;
		} else {
			$item_id = $this->root_product_id;
		}


		$configPage     = $ncpc_settings['ncpc_config_page'];
		if( isset($configPage) && !empty($configPage) && $configPage != 0){
			$ncpc_page_id = $configPage;
		}
		else {
			$ncpc_page_id = false;
		}


		$ncpc_page_url = '';
		if ( $ncpc_page_id ) {

			$ncpc_page_url = get_permalink( $ncpc_page_id );

			if ( $item_id ) {
				$query = wp_parse_url( $ncpc_page_url, PHP_URL_QUERY );
				// Returns a string if the URL has parameters or NULL if not
				if ( get_option( 'permalink_structure' ) ) {
					if ( substr( $ncpc_page_url, -1 ) !== '/' ) {
						$ncpc_page_url .= '/';
					}elseif ( $order_item_id ) {
						$ncpc_page_url .= "ordered-design/$item_id/$order_item_id/";
						$ncpc_page_url  = apply_filters( 'ncpc_customized_order_page_url', $ncpc_page_url );
					} else {
						$ncpc_page_url .= 'design/' . $item_id . '/';
						if ( $tpl_id ) {
							$ncpc_page_url .= "$tpl_id/";
						}
					}
				} else {
					if ( $design_index !== false ) {
						$ncpc_page_url .= '&product_id=' . $item_id . '&design_index=' . $design_index;
					}elseif ( $order_item_id ) {
						$ncpc_page_url .= '&product_id=' . $item_id . '&vcid=' . $order_item_id;
					} else {
						$ncpc_page_url .= '&product_id=' . $item_id;
						if ( $tpl_id ) {
							$ncpc_page_url .= "&tpl=$tpl_id";
						}
					}
				}
			}
		}

		return $ncpc_page_url;
	}
	/**
	 * Hide add to cart button on product details page
	 */
	public function hide_cart_button() {
		global $product;
		$pid                 = $product->get_id();
		$configs = get_post_meta($pid,"product-ncpc-metas",true);
		if ( isset( $configs[ $pid ]['config-id'] )){
			$config_id = $configs[$pid];
			$meta_value = get_post_meta((int)$config_id['config-id'],"ncpc-configs-meta",true);
			$general_options  = $meta_value["settings"]["generals"]["product"]??null;
			$custom_products     = ncpc_get_custom_products();
			$anonymous_function  = function ( $vc ) {
				return $vc->id;
			};
			$custom_products_ids = array_map( $anonymous_function, $custom_products );
			if ( in_array( $pid, $custom_products_ids ) && $general_options!=null && $general_options['hideAddToCartButtonCustomProducts'] ) {
				wp_localize_script("ncpc-product-design","cart_hide_button",[
					"hide_cart_button"=>$general_options['hideAddToCartButtonCustomProducts']
				]);
				add_action( 'wp_footer', [$this,'ncpc_product_page_script_init'] );
			}
		}
	}
	public function ncpc_product_page_script_init() { 
		global $product;
		$pid                 = $product->get_id();
		
		$inline_script = "		
			jQuery(\"[value='".esc_html($pid). "']\").parent().find('.add_to_cart_button').hide();
			jQuery(\"[value='".esc_html($pid). "']\").parent().find('.single_add_to_cart_button').hide();
		";
        wp_add_inline_script( 'ncpc-product-design', $inline_script );
		?>
	<?php
	}
	/**
	 * Add Custom button on details product page
	 */
	public function get_customize_btn() {
		$product_id  = get_the_ID();
		$ncpc_product = new NCPC_Product_Config( $product_id );
		$product     = wc_get_product( $product_id );
		
		if ( $ncpc_product->is_ncpc_customizable() && 'simple' === $product->get_type() ) {
			echo wp_kses_post($ncpc_product->get_buttons( true ));
		} elseif ( 'variable' === $product->get_type() ) {
			echo wp_kses_post($ncpc_product->get_buttons( true ));
		}
	}
	/**
	 * Add a personalization button on the shop page
	 */
	public function get_customize_btn_on_shop_page( $html, $product ) {
		
		$configs = get_post_meta($product->get_id(),"product-ncpc-metas",true);
		$meta_value = isset($configs[$product->get_id()]['config-id']) ? get_post_meta((int)$configs[$product->get_id()]['config-id'],"ncpc-configs-meta",true) : [];
		$general_options  = $meta_value["settings"]["generals"]["product"]??null;
		
		if ( $general_options==null || $general_options["hideDesignButtonsOnShopPage"] ) {
			return $html;
		}

		$product_class = get_class( $product );
		
		if ( $product_class == 'WC_Product_Simple' ) {
			$ncpc_product = new NCPC_Product_Config( $product->get_id() );
			if ( $ncpc_product->is_ncpc_customizable() ) {
				$html .= wp_kses_post($ncpc_product->get_buttons());
			}
			
			return $html;
		}
	}

	/**
	 * Add css class on detail product page in order to hide or no add to cart button 
	 */
	public function get_button_on_single_product_summary(  ) {
		global $post;
		global $product;
		$pid                 = $post->ID;
		$configs = get_post_meta($pid,"product-ncpc-metas",true);
		if(isset($configs[$pid]['config-id'])) {
			$config_id = $configs[$pid]['config-id'];
			$meta_value = get_post_meta($config_id,"ncpc-configs-meta",true);
			
			$general_options  = $meta_value["settings"]["generals"]["product"] ?? null;
			$custom_products     = ncpc_get_custom_products();
			$anonymous_function  = function ( $vc ) {
				return $vc->id;
			};
			$custom_products_ids = array_map( $anonymous_function, $custom_products );
			
			if ( in_array( $pid, $custom_products_ids ) ) {
				if ( $general_options!=null && !$general_options['hideAddToCartButtonCustomProducts'] ) {
					$ncpc_product = new NCPC_Product_Config( $product->get_id() );
					if ( $ncpc_product->is_ncpc_customizable() ) {
						echo wp_kses_post($ncpc_product->get_buttons());
					}
				}
			}
		}
	}

		/**
	 * Returns a variation root product ID
	 *
	 * @param int $variation_id Variation ID
	 * @return int
	 */
	public function get_parent( $variation_id ) {
		$variable_product = wc_get_product( $variation_id );
		if ( ! $variable_product ) {
			return false;
		}
		if ( $variable_product->get_type() !== 'variation' ) {
			$product_id = $variation_id;
		} else {
			$product_id = $variable_product->get_parent_id();
		}

		return $product_id;
	}


 }