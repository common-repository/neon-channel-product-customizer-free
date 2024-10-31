<?php

use NCPC\classes\NCPC_Config;
use NCPC\classes\NCPC_Design;

/*
Plugin Name: Neon Channel Product Customizer Free
Plugin URI: https://signsdesigner.us/
Plugin URI: https://signsdesigner.us/
Description: The ultimate custom neon and channel sign configurator for woocommerce. Our custom neon signs configurator allows you to extend your business of personalization of neon signs by offering you a nice configurator to allow your customers to customize signs in neon, acrylic, metal, 2D and 3D, thanks to a highly configurable sign product builder.

Version: 1.2.0
Author: Vertim Coders
Author URI: https://vertimcoders.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: neon-channel-product-customizer-free
Domain Path: /languages
*/

/**
 * Copyright (c) 2023 Vertim Coders. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 * 
 * Inspired by: https://github.com/tareq1988/vue-wp-starter
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if (!defined('ABSPATH'))
    exit;

/**
 * Neon_Channel_Product_Customizer class
 *
 * @class Neon_Channel_Product_Customizer The class that holds the entire Neon_Channel_Product_Customizer plugin
 */
final class NCPC_Neon_Channel_Product_Customizer
{

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.2.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the NCPC_Neon_Channel_Product_Customizer class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct()
    {

        $this->define_constants();

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        add_action('plugins_loaded', array($this, 'init_plugin'));

        add_action('admin_notices', [$this, 'check_woocommerce_install_and_version']);
        add_action('admin_notices', [$this, 'check_config_pageselected']);
        add_action('admin_notices', [$this, 'go_to_pro_notice']);
        add_action('admin_notices', [$this, 'permalink_notice']);
    }

    /**
     * Initializes the NCPC_Neon_Channel_Product_Customizer() class
     *
     * Checks for an existing NCPC_Neon_Channel_Product_Customizer() instance
     * and if it doesn't find one, creates it.
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new NCPC_Neon_Channel_Product_Customizer();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container)) {
            return $this->container[$prop];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('NCPC_VERSION', $this->version);
        define('NCPC_FILE', __FILE__);
        define('NCPC_PATH', dirname(NCPC_FILE));
        define('NCPC_CLASSES', NCPC_PATH . '/classes');
        define('NCPC_INCLUDES', NCPC_PATH . '/includes');
        define('NCPC_URL', plugins_url('', NCPC_FILE));
        define('NCPC_ASSETS', NCPC_URL . '/assets');

        $upload_dir = wp_upload_dir();
        $generation_path = $upload_dir['basedir'] . "/NCPC/";
        $generation_url = $upload_dir['baseurl'] . "/NCPC/";

        define('NCPC_IMAGE_PATH', $generation_path . "image");
        define('NCPC_IMAGE_URL', $generation_url . "image");

        define('NCPC_ORDER_PATH', $generation_path . "ORDER");
        define('NCPC_ORDER_URL', $generation_url . "ORDER");
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate()
    {

        $installed = get_option('ncpc_installed');

        if (!$installed) {
            update_option('ncpc_installed', time());
        }

        update_option('ncpc_version', NCPC_VERSION);
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate()
    {
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {

        if ($this->is_request('admin')) {
            require_once NCPC_INCLUDES . '/Admin.php';
        }

        if ($this->is_request('frontend')) {
            require_once NCPC_INCLUDES . '/Frontend.php';
        }

        require_once NCPC_INCLUDES . '/Api/Api.php';
        require_once NCPC_INCLUDES . '/Public.php';
        require_once NCPC_CLASSES . '/ncpc-config.php';
        require_once NCPC_CLASSES . '/ncpc-product-config.php';
        require_once NCPC_CLASSES . '/ncpc-design.php';
        require_once NCPC_INCLUDES . '/functions.php';
    }

    public function go_to_pro_notice()
    {
?>
        <div class="notice notice-info ncpc-notice-nux is-dismissible" id="ncpc_gotopro_notice">
            <span class="ncpc-icon">
                <img src='<?php echo esc_url(NCPC_ASSETS . '/images/3.png') ?>' alt="" />
            </span>
            <div>
                <h2 class="ncpc-notice-title"><?php esc_html_e('To have more features like backboards, additional options and even more upgrade to one of our higher versions, Pro or Starter 🤘', 'neon-channel-product-customizer-free') ?>
                </h2>
                <p><?php esc_html_e('Do it now', "neon-channel-product-customizer-free") ?> <a href="https://signsdesigner.us/pricing/" target="_blank"><?php echo esc_html_e("Go To Pro", "neon-channel-product-customizer-free") ?></a></p>
            </div>
        </div>
        <?php
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks()
    {

        // Localize our plugin
        add_action('init', array($this, 'localization_setup'));

        //NCPC Config hooks
        $ncpc_config = new NCPC_Config();
        $ncpc_config->init_hooks();

        //NCPC Product Config hooks
        $ncpc_product_config = new NCPC_Product_Config();
        $ncpc_product_config->init_hooks();

        //NCPC Design hooks
        $ncpc_design = new NCPC_Design();
        $ncpc_design->init_hooks();
        add_action('init', array($this, 'init_classes'));
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {

        if ($this->is_request('admin')) {
            $this->container['admin'] = new NCPC\Admin();
        }

        if ($this->is_request('frontend')) {
            $this->container['frontend'] = new NCPC\Frontend();
        }

        /* if ( $this->is_request( 'ajax' ) ) {
        // $this->container['ajax'] =  new NCPC\Ajax();
    } */

        $this->container['api'] = new NCPC\Api();
        $this->container['public'] = new NCPC\NCPC_Public();
        //$this->container['assets'] = new NCPC\Assets();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup()
    {
        load_plugin_textdomain('neon-channel-product-customizer-free', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();

            case 'ajax':
                return defined('DOING_AJAX');

            case 'rest':
                return defined('REST_REQUEST');

            case 'cron':
                return defined('DOING_CRON');

            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }
    }

    /**
     * Check if Config Page is selected and is valid
     */
    public function check_config_pageselected()
    {
        if (class_exists('WooCommerce')) {
            if (empty(get_option('ncpc_config_page'))) {
        ?>
                <div class="notice notice-warning ncpc-notice-nux is-dismissible" id="ncpc_config_page_notice">
                    <span class="ncpc-icon">
                        <img src="<?php echo esc_url(NCPC_ASSETS . '/images/3.png') ?>" alt="" />
                    </span>
                    <div>
                        <h2 class="ncpc-notice-title"><?php esc_html_e('Customization page not found', 'neon-channel-product-customizer-free') ?>
                        </h2>
                        <p><?php esc_html_e('To display the configurator on a page without a short code, please select the page on which it should be displayed. Click ', 'neon-channel-product-customizer-free') ?>
                            <a href="admin.php?page=ncpc#/global-settings"><?php esc_html_e('here', 'neon-channel-product-customizer-free') ?></a>
                        </p>
                    </div>
                </div>
                <?php
            } else {
                if (get_option('ncpc_config_page') != 0 && !get_post_status(get_option('ncpc_config_page'))) {
                ?>
                    <div class="notice notice-warning ncpc-notice-nux is-dismissible" id="ncpc_config_page_notice">
                        <span class="ncpc-icon">
                            <img src="<?php echo esc_url(NCPC_ASSETS . '/images/3.png') ?>" alt="" />
                        </span>
                        <div>
                            <h2 class="ncpc-notice-title"><?php esc_html_e('Customization page not found', 'neon-channel-product-customizer-free') ?>
                            </h2>
                            <p><?php esc_html_e('To display the configurator on a page without a short code, please select the page on which it should be displayed. Click ', 'neon-channel-product-customizer-free') ?>
                                <a href="admin.php?page=ncpc#/global-settings"><?php esc_html_e('here', 'neon-channel-product-customizer-free') ?></a>
                            </p>
                        </div>
                    </div>
                <?php
                }
            }
        }
    }
    /**
     * Check if Woocommerce is installed
     */
    public function check_woocommerce_install_and_version($version = '3.4.0')
    {
        if (class_exists('WooCommerce')) {
            global $woocommerce;
            if (version_compare($woocommerce->version, $version, '<')) {
                ?>
                <div class="notice notice-info ncpc-notice-nux is-dismissible" id="ncpc_woo_notice">
                    <span class="ncpc-icon">
                        <img src="<?php echo esc_url(NCPC_ASSETS . '/images/3.png') ?>" alt="" />
                    </span>
                    <div>
                        <h2 class="ncpc-notice-title"><?php esc_html_e('Thanks for installing NCPC, you glow! 🤘', 'neon-channel-product-customizer-free') ?>
                        </h2>
                        <p><?php esc_html_e('To avoid performance problems we recommend at least version 3.4 of Woocommerce.', 'neon-channel-product-customizer-free'); ?>
                        </p>
                        <p><?php $this->install_plugin_button('woocommerce', 'woocommerce.php', 'WooCommerce', array(), __('WooCommerce activated', 'neon-channel-product-customizer-free'), __('Activate WooCommerce', 'neon-channel-product-customizer-free'), __('Install WooCommerce', 'neon-channel-product-customizer-free')); ?>
                        </p>
                    </div>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="notice notice-info ncpc-notice-nux is-dismissible" id="ncpc_woo_notice">
                <span class="ncpc-icon">
                    <img src="<?php echo esc_url(NCPC_ASSETS . '/images/3.png') ?>" alt="" />
                </span>
                <div>
                    <h2 class="ncpc-notice-title"><?php esc_html_e('Thanks for installing NCPC, you glow! 🤘', 'neon-channel-product-customizer-free') ?>
                    </h2>
                    <p><?php esc_html_e('To enable eCommerce features you need to install or activate the WooCommerce plugin.', 'neon-channel-product-customizer-free'); ?>
                    </p>
                    <p><?php $this->install_plugin_button('woocommerce', 'woocommerce.php', 'WooCommerce', array(), __('WooCommerce activated', 'neon-channel-product-customizer-free'), __('Activate WooCommerce', 'neon-channel-product-customizer-free'), __('Install WooCommerce', 'neon-channel-product-customizer-free')); ?>
                    </p>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Output a button that will install or activate a plugin if it doesn't exist, or display a disabled button if the
     * plugin is already activated.
     *
     * @param string $plugin_slug The plugin slug.
     * @param string $plugin_file The plugin file.
     * @param string $plugin_name The plugin name.
     * @param array $classes CSS classes.
     * @param string $activated Button activated text.
     * @param string $activate Button activate text.
     * @param string $install Button install text.
     */
    public static function install_plugin_button($plugin_slug, $plugin_file, $plugin_name, $classes = array(), $activated = '', $activate = '', $install = '')
    {
        if (current_user_can('install_plugins') && current_user_can('activate_plugins')) {
            if (is_plugin_active($plugin_slug . '/' . $plugin_file)) {
                // The plugin is already active.
                $button = array(
                    'message' => esc_attr__('Activated', 'neon-channel-product-customizer-free'),
                    'url' => '#',
                    'classes' => array('storefront-button', 'disabled'),
                );

                if ('' !== $activated) {
                    $button['message'] = esc_attr($activated);
                }
            } elseif (self::is_plugin_installed($plugin_slug)) {
                $url = self::is_plugin_installed($plugin_slug);

                // The plugin exists but isn't activated yet.
                $button = array(
                    'message' => esc_attr__('Activate', 'neon-channel-product-customizer-free'),
                    'url' => $url,
                    'classes' => array('activate-now'),
                );

                if ('' !== $activate) {
                    $button['message'] = esc_attr($activate);
                }
            }

            if (!empty($classes)) {
                $button['classes'] = array_merge($button['classes'], $classes);
            }
            if (isset($button) && is_array($button)) {

                $button['classes'] = implode(' ', $button['classes']);

            ?>
                <span class="plugin-card-<?php echo esc_attr($plugin_slug); ?>">
                    <a href="<?php echo esc_url($button['url']); ?>" class="<?php echo esc_attr($button['classes']); ?>" data-originaltext="<?php echo esc_attr($button['message']); ?>" data-name="<?php echo esc_attr($plugin_name); ?>" data-slug="<?php echo esc_attr($plugin_slug); ?>" aria-label="<?php echo esc_attr($button['message']); ?>"><?php echo esc_html($button['message']); ?></a>
                </span>
                <?php echo /* translators: conjunction of two alternative options user can choose (in missing plugin admin notice). Example: "Activate WooCommerce or learn more" */ esc_html__('or', 'neon-channel-product-customizer-free'); ?>
                <a href="https://docs.signsdesigner.us" target="_blank"><?php esc_html_e('learn more', 'neon-channel-product-customizer-free'); ?></a>
            <?php
            }
        }
    }
    private static function is_plugin_installed( $plugin_slug ) {
        $plugins_folder = plugins_url();
        if (file_exists($plugins_folder . '/' . $plugin_slug)) {
            $plugins = get_plugins('/' . $plugin_slug);
            if (!empty($plugins)) {
                $keys        = array_keys($plugins);
                $plugin_file = $plugin_slug . '/' . $keys[0];
                $url = wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'activate',
                            'plugin' => $plugin_file,
                        ),
                        admin_url('plugins.php')
                    ),
                    'activate-plugin_' . $plugin_file
                );
                return $url;
            }
        }
        return false;
    }

    /**
     * 
     */
    public function permalink_notice()
    {

        $current_permalink_structure = get_option('permalink_structure');

        if ($current_permalink_structure !== '/%postname%/') { ?>

            <div class="notice notice-warning ncpc-notice-nux is-dismissible" id="ncpc_permalink_notice">
                <span class="ncpc-icon">
                    <img src='<?php echo esc_url(NCPC_ASSETS . '/images/3.png') ?>' alt="" />
                </span>
                <div>
                    <h2 class="ncpc-notice-title"><?php esc_html_e('We recommend setting your permalinks to "/%postname%/" to improve natural SEO.w! 🤘', 'neon-channel-product-customizer-free') ?>
                    </h2>
                    <p><?php esc_html_e('To do this, go to', "neon-channel-product-customizer-free") ?> <a href="<?php echo esc_url(admin_url('options-permalink.php')) ?>"><?php echo esc_html_e("Settings > Permanent links", "neon-channel-product-customizer-free") ?></a>
                    </p>
                </div>
            </div>
<?php }
    }
} // NCPC_Neon_Channel_Product_Customizer

$ncpc = NCPC_Neon_Channel_Product_Customizer::init();
