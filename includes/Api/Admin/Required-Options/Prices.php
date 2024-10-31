<?php
namespace NCPC\Api\Admin\Required_Options;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching prices
 */
class NCPC_Api_Prices extends WP_REST_Controller {
     /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/prices';
    }
    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_all_prices' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'add_price_to_prices_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/(?P<pricing_id>\d+)",
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_price_info_in_prices_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'pricing_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_price_in_prices_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'pricing_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_price_in_prices_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'pricing_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        ),
                    ),
                )
            )
        );
        
    }

    /**
     * Add price in price configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function add_price_to_prices_setting($request){
        $id = $request->get_param('config_id');
        $price = json_decode($request->get_body(),true);
        

        if($id!=0){
            
            $post = get_post($id);
            
            if($post){
                
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                
                if(empty($meta_value)){
                    return rest_ensure_response(["success"=>false,"message" => __("Pricing add failed","neon-channel-product-customizer-starter")]);
                }else{
                    if(isset($meta_value["requiredOptions"]["priceOptions"])){
                        array_push($meta_value["requiredOptions"]["priceOptions"], $price);
                    }else {
                        $meta_value=array();
                        $meta_value["requiredOptions"]["priceOptions"][0] = $price;
                    }
                }
                
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
                
                if($response){
                    return rest_ensure_response(["success" => true,"message" => __("Pricing added successfully","neon-channel-product-customizer-starter")]);
                }else{
                    return rest_ensure_response(["success"=>false,"message" => __("Pricing add failed","neon-channel-product-customizer-starter")]);
                }
            
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Get all prices of ncpc configuration ID
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

    public function get_all_prices($request) {
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
            
                if (isset($meta_value["requiredOptions"]["priceOptions"])) {
                    return rest_ensure_response( $meta_value["requiredOptions"]["priceOptions"] );
                }else{
                    return rest_ensure_response(["success"=>false,"message" => __("No data found","neon-channel-product-customizer-starter")]);
                }
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }

    }

    /**
     * Get a price where is in $pricing_id position info
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_price_info_in_prices_setting($request){

        $id = $request->get_param('config_id');
        $pricing_id = $request->get_param('pricing_id');

        if($id!=0){
            $post = get_post($id);
            if ($post) {
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(!empty($meta_value)) {

                    if(isset($meta_value["requiredOptions"]["priceOptions"])){
                        $prices = $meta_value["requiredOptions"]["priceOptions"];
                            if($prices[$pricing_id]) {
                                return rest_ensure_response($prices[$pricing_id]);
                            }
                        return rest_ensure_response(["success"=>false,"message" => __("No pricing found","neon-channel-product-customizer-starter")]);
                    }else{
                        return rest_ensure_response(["success"=>false,"message" => __("No pricing found","neon-channel-product-customizer-starter")]);
                    }
                }else {
                    return rest_ensure_response(["success"=>false,"message" => __("No pricing setting found","neon-channel-product-customizer-starter")]);
                }
                
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }
    /**
     * Update a price in the price configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_price_in_prices_setting($request){

        $id = $request->get_param('config_id');
        $pricing_id = $request->get_param('pricing_id');

        $price = json_decode($request->get_body(),true);

        if($id!=0){
            $post = get_post($id);
            if ($post) {
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(!empty($meta_value)) {

                    if($meta_value["requiredOptions"]["priceOptions"][$pricing_id]){
                        if($meta_value["requiredOptions"]["priceOptions"][$pricing_id] !== $price){                            
                            $meta_value["requiredOptions"]["priceOptions"][$pricing_id] = $price;
        
                            $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
        
                            if($response){
                                return rest_ensure_response(["success" => true,"message"=>__("Pricing updated successfully","neon-channel-product-customizer-starter")]);
                            }else{
                                return rest_ensure_response(["success" => false,"message" => __("Update price failed","neon-channel-product-customizer-starter")]);
                            }
                        }else{                            
                            return rest_ensure_response(["success" => "same","message" => __("No change observed on price","neon-channel-product-customizer-starter")]);
                        }
                    }else{                        
                        return rest_ensure_response(["success" => false,"message" => __("Update price failed","neon-channel-product-customizer-starter")]);
                    }
                    
                }else {
                    return rest_ensure_response(["success"=>false,"message" => "No pricing setting found"]);
                }
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Delete a price in the price configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_price_in_prices_setting($request){
        $id = $request->get_param('config_id');
        $pricing_id = $request->get_param('pricing_id');
        if($id!=0){
            $post = get_post($id);
            if ($post) {
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(!empty($meta_value["requiredOptions"]["priceOptions"])){

                    if($meta_value["requiredOptions"]["priceOptions"][$pricing_id]){
                        array_splice($meta_value["requiredOptions"]["priceOptions"],$pricing_id,1); 
    
                        $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
    
                        if($response){
                            return rest_ensure_response(["success" => true,"message"=>__("Picing deleted successfully","neon-channel-product-customizer-starter")]);
                        }else{
                            return rest_ensure_response(["success"=>false,"message" => __("Delete price failed","neon-channel-product-customizer-starter")]);
                        }
                    }else{                        
                        return rest_ensure_response(["success"=>false,"message" => __("Delete price failed","neon-channel-product-customizer-starter")]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false,"message" => __("No pricing setting found","neon-channel-product-customizer-starter")]);
                }
                
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_config_permissions_check( $request ) {
        return true;
    }
}