<?php
namespace NCPC\classes;
/**
 * Contains all methods and hooks callbacks related to the product configuration
 *
 * @author Vertim Coders
 */
class NCPC_Config {
	
    /**
     * Set all ncpc configuration initialization hooks
    */
    public function init_hooks() {
        add_action('init',array($this,'register_ncpc_config'));
        add_action('init',array($this,'init_globals'));
        add_action('init', array($this,'register_ncpc_config_meta'));
        add_action( 'init', array($this,'register_ncpc_config_taxonomy'),0,2);
		add_filter( 'the_content', array($this,'get_editor_shortcode_handler'));
		add_filter( 'init', array($this,'ncpc_add_rewrite_rules'), 99 );
		add_filter( 'query_vars', array($this, 'ncpc_add_query_vars' ));
    }

	/* public function register_short_codes(){
		add_shortcode( 'ncpc-editor', array( $this, 'get_editor_shortcode_handler' ) );
	} */
	/**
	 * Register of ncpc configuration post type
	 */
	public function register_ncpc_config() {

		$labels = array(
			'name'               => esc_html__( 'NCPC Configurations', 'neon-channel-product-customizer-free'),
			'singular_name'      => esc_html__( 'NCPC Configurations', 'neon-channel-product-customizer-free'),
			'add_new'            => esc_html__( 'New NCPC configuration', 'neon-channel-product-customizer-free'),
			'add_new_item'       => esc_html__( 'New NCPC configuration', 'neon-channel-product-customizer-free'),
			'edit_item'          => esc_html__( 'Edit NCPC configuration', 'neon-channel-product-customizer-free'),
			'new_item'           => esc_html__( 'New NCPC configuration', 'neon-channel-product-customizer-free'),
			'view_item'          => esc_html__( 'View NCPC configuration', 'neon-channel-product-customizer-free'),
			'not_found'          => esc_html__( 'No NCPC configuration found', 'neon-channel-product-customizer-free'),
			'not_found_in_trash' => esc_html__( 'No NCPC configuration in the trash', 'neon-channel-product-customizer-free'),
			'menu_name'          => esc_html__( 'Neon Product Designer', 'neon-channel-product-customizer-free'),
			'all_items'          => esc_html__( 'NCPC Configurations', 'neon-channel-product-customizer-free'),
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'NCPC Configurations',
			'supports'            => array( 'title' ),
			'public'              => false,
			'show_in_rest' 		  => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
		);

		register_post_type( 'ncpc-configs', $args );
	}

	/**
	 * Create meta data of ncpc-configs-meta
	*/
	public function register_ncpc_config_meta(){
		register_meta(
			'ncpc-configs',
			'ncpc-configs-meta',
			array(
				'show_in_rest' => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type'  => 'array',
							'items' => array(
								'type'        => 'mixed'
							)
						)
					)
				),
				'type' => 'array',
				'single' => true,
			)
		);
	}
	/**
	 * Register of ncpc taxonomies
	*/
	public function register_ncpc_config_taxonomy(){

		// added taxonomy ncpc-product-type
		$labels_product_type = array(
			'name' => esc_html__( 'NCPC Product Types', 'neon-channel-product-customizer-free'),
			'singular_name' => esc_html__( 'NCPC Product Type', 'neon-channel-product-customizer-free'),
			'search_items' =>  esc_html__( 'Search NCPC Product Types', 'neon-channel-product-customizer-free'),
			'popular_items' => esc_html__( 'Popular NCPC Product Types', 'neon-channel-product-customizer-free'),
			'all_items' => esc_html__( 'All NCPC Product Type', 'neon-channel-product-customizer-free'),
			'edit_item' => esc_html__( 'Edit NCPC Product Type', 'neon-channel-product-customizer-free'),
			'update_item' => esc_html__( 'Update NCPC Product Type', 'neon-channel-product-customizer-free'),
			'add_new_item' => esc_html__( 'Add New NCPC Product Type', 'neon-channel-product-customizer-free'),
			'new_item_name' => esc_html__( 'New NCPC Product Type Name', 'neon-channel-product-customizer-free'),
			'separate_items_with_commas' => esc_html__( 'Separate NCPC Product Type with commas', 'neon-channel-product-customizer-free'),
			'add_or_remove_items' => esc_html__( 'Add or remove NCPC Product Type', 'neon-channel-product-customizer-free'),
			'choose_from_most_used' => esc_html__( 'Choose from the most used NCPC Product Type', 'neon-channel-product-customizer-free'),
			'not_found' => esc_html__( 'No NCPC Product Type found.', 'neon-channel-product-customizer-free'),
			'menu_name' => esc_html__( 'NCPC Product Type', 'neon-channel-product-customizer-free'),
		);

		$args_product_type = array(
			'hierarchical'          => false,
			'labels'                => $labels_product_type,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'ncpc-products-type' ),
		);
		register_taxonomy( 'ncpc-products-type', 'ncpc-configs', $args_product_type );

		//added taxonomy ncpc-pricing-mode

		$labels_pricing_mode = array(
			'name' => esc_html__( 'NCPC Pricing Modes','neon-channel-product-customizer-free'),
			'singular_name' => esc_html__( 'NCPC Pricing Mode', 'neon-channel-product-customizer-free'),
			'search_items' =>  esc_html__( 'Search NCPC Pricing Modes', 'neon-channel-product-customizer-free'),
			'popular_items' => esc_html__( 'Popular NCPC Pricing Modes', 'neon-channel-product-customizer-free'),
			'all_items' => esc_html__( 'All NCPC Pricing Mode', 'neon-channel-product-customizer-free'),
			'edit_item' => esc_html__( 'Edit NCPC Pricing Mode', 'neon-channel-product-customizer-free'),
			'update_item' => esc_html__( 'Update NCPC Pricing Mode', 'neon-channel-product-customizer-free'),
			'add_new_item' => esc_html__( 'Add New NCPC Pricing Mode', 'neon-channel-product-customizer-free'),
			'new_item_name' => esc_html__( 'New NCPC Pricing Mode Name', 'neon-channel-product-customizer-free'),
			'separate_items_with_commas' => esc_html__( 'Separate NCPC Pricing Mode with commas', 'neon-channel-product-customizer-free'),
			'add_or_remove_items' => esc_html__( 'Add or remove NCPC Pricing Mode', 'neon-channel-product-customizer-free'),
			'choose_from_most_used' => esc_html__( 'Choose from the most used NCPC Pricing Mode', 'neon-channel-product-customizer-free'),
			'not_found' => esc_html__( 'No NCPC Pricing Mode found.', 'neon-channel-product-customizer-free'),
			'menu_name' => esc_html__( 'NCPC Pricing Mode', 'neon-channel-product-customizer-free'),
		);

		$args_pricing_mode = array(
			'hierarchical'          => false,
			'labels'                => $labels_pricing_mode,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'ncpc-pricings-mode' ),
		);
		register_taxonomy( 'ncpc-pricings-mode', 'ncpc-configs', $args_pricing_mode );
	}

	/**
	 * Gets the settings and put them in a global variable
	 *
	 * @global array $ncpc_settings Settings
	 */
	public function init_globals() {
		global $ncpc_settings;
		$ncpc_settings['ncpc_config_page'] = get_option( 'ncpc_config_page' );
		$ncpc_settings['ncpc_starter_license']         = get_option( 'ncpc_starter_license' );
	}

	/**
	 * Add short code on config page
	 */

	public function get_editor_shortcode_handler( $content ) {
		global $wp_query;
		$config_page_id = get_option("ncpc_config_page");
		if ( get_the_ID() == $config_page_id && !isset( $wp_query->query_vars['product-id'] ) ) {
					ob_start();
		?>
		<div class="config-page-error">
			<div>				
				<p><?php echo esc_html__( "You are trying to access the personalization page without a product to personalize or your permalink is not on postname.", 'neon-channel-product-customizer-free');?></p>
				<p><?php echo esc_html__( "This page should only be accessed using one of the customization buttons.", 'neon-channel-product-customizer-free');?></p>
			</div>
		</div>
		<?php			
				
					$content .= ob_get_clean();
		}elseif ( isset( $wp_query->query_vars['product-id'] ) ) {
			$productid = $wp_query->query_vars['product-id'];

			if( is_page($config_page_id) ) {
				$content .= do_shortcode("[ncpc-configurator productid='$productid' ]");
			}
		}
		return $content;
	}
	public function ncpc_add_query_vars( $a_vars ) {
		$a_vars[] = 'product-id';
		$a_vars[] = 'tpl';
		$a_vars[] = 'edit';
		$a_vars[] = 'design-index';
		$a_vars[] = 'vcid';
		return $a_vars;
	}
	public function ncpc_add_rewrite_rules( $param ) {
		global $wp_rewrite;
		$option = get_option("ncpc_config_page");
		if ( !empty($option) && $option != false ) {
			$ncpc_page_id = $option;
		} else {
			$ncpc_page_id = false;
		}

		if ( function_exists( 'icl_object_id' ) ) {
			$ncpc_page_id = icl_object_id( $ncpc_page_id, 'page', false, ICL_LANGUAGE_CODE );
		}
		$ncpc_page = get_post( $ncpc_page_id );
		if ( is_object( $ncpc_page ) ) {
			$raw_slug = get_permalink( $ncpc_page->ID );
			$home_url = home_url( '/' );
			$slug     = str_replace( $home_url, '', $raw_slug );
			// If the slug does not have the trailing slash, we get 404 (ex postname = /%postname%)
			$sep = '';
			if ( substr( $slug, -1 ) != '/' ) {
				$sep = '/';
			}
			add_rewrite_rule(
					// The regex to match the incoming URL
				$slug . $sep . 'design' . '/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress
					// `pagename` because we use this WordPress page
					// `designer_slug` because we assign the first captured regex part to this variable
					'index.php?pagename=' . $slug . '&product-id=$matches[1]',
				// This is a rather specific URL, so we add it to the top of the list
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win"
					'top'
			);
			add_rewrite_rule(
					// The regex to match the incoming URL
				$slug . $sep . 'design' . '/([^/]+)/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress
					// `pagename` because we use this WordPress page
					// `designer_slug` because we assign the first captured regex part to this variable
					'index.php?pagename=' . $slug . '&product-id=$matches[1]&tpl=$matches[2]',
				// This is a rather specific URL, so we add it to the top of the list
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win"
					'top'
			);
			add_rewrite_rule(
					// The regex to match the incoming URL
				$slug . $sep . 'edit' . '/([^/]+)/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress
					// `pagename` because we use this WordPress page
					// `designer_slug` because we assign the first captured regex part to this variable
					'index.php?pagename=' . $slug . '&product-id=$matches[1]&edit=$matches[2]',
				// This is a rather specific URL, so we add it to the top of the list
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win"
					'top'
			);
			add_rewrite_rule(
					// The regex to match the incoming URL
				$slug . $sep . 'ordered-design' . '/([^/]+)/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress
					// `pagename` because we use this WordPress page
					// `designer_slug` because we assign the first captured regex part to this variable
					'index.php?pagename=' . $slug . '&product-id=$matches[1]&vcid=$matches[2]',
				// This is a rather specific URL, so we add it to the top of the list
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win"
					'top'
			);

			add_rewrite_rule(
					// The regex to match the incoming URL
				$slug . $sep . 'saved-design' . '/([^/]+)/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress
					// `pagename` because we use this WordPress page
					// `designer_slug` because we assign the first captured regex part to this variable
					'index.php?pagename=' . $slug . '&product-id=$matches[1]&design-index=$matches[2]',
				// This is a rather specific URL, so we add it to the top of the list
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win"
					'top'
			);

			$wp_rewrite->flush_rules( false );
		}
	}
}


