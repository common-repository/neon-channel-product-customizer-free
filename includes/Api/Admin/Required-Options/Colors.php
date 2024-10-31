<?php
namespace NCPC\Api\Admin\Required_Options;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching colors
 */
class NCPC_Api_Colors extends WP_REST_Controller {
     /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/colors';
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
                    'callback'            => array( $this, 'get_all_colors' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'create_colors_setting' ),
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
            '/' . $this->rest_base."/new",
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'add_color_to_colors_setting' ),
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
            '/' . $this->rest_base."/(?P<color_id>\d+)",
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_color_info' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'color_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_color_in_colors_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'color_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_color_in_colors_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'color_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                )
            )
        );
        
    }

    /**
     * Create if a color configuration doesn't exit and update if it exists in an NCPC configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_colors_setting($request){
        $id = $request->get_param('config_id');
        $color_setting = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value = array();
                }
                $meta_value["requiredOptions"]["colorOptions"] = $color_setting;
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
                
                if($response){
                    return rest_ensure_response(["success" => true,"message"=>__("Color settings updated successfully","neon-channel-product-customizer-starter")]);
                }else{
                    return rest_ensure_response(["message" => "color operation fail"]);
                }
            
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }

    /**
     * Get all colors of ncpc configuration ID
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

    public function get_all_colors($request) {
        $id = $request->get_param('config_id');
        if($id!=0){
            $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
            
            if (!empty($meta_value)) {
                if(isset($meta_value["requiredOptions"]["colorOptions"])){
                    $color_options = $meta_value["requiredOptions"]["colorOptions"];
                    return rest_ensure_response( $color_options );
                }
                return rest_ensure_response( ["message" => __("No data found","neon-channel-product-customizer-starter")] );
            }else{
                return rest_ensure_response(["message" => __("No data found","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }

    }
    /**
     * Get a color with ID of ncpc configuration ID
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

     public function get_color_info($request) {
        $id = $request->get_param('config_id');
        $color_id = $request->get_param('color_id');
        if($id!=0){
            $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
            
            if (!empty($meta_value)) {

                if (isset($meta_value["requiredOptions"]["colorOptions"]['colors'])) {
                    $colors= $meta_value["requiredOptions"]["colorOptions"]['colors'];
                    for ($i=0; $i < $colors; $i++) { 
                        if($i===$color_id){
                            return rest_ensure_response( $colors[$color_id] );
                        }
                    }
                    return rest_ensure_response(array("message" => __("No color found","neon-channel-product-customizer-starter") ) );
                }else{
                    return rest_ensure_response( array("message" => __("No color found","neon-channel-product-customizer-starter") ) );
                }
                
                
            }else{
                return rest_ensure_response(["message" => __("No data found","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }

    }

    /**
     * Add color in color configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function add_color_to_colors_setting($request){

        $id = $request->get_param('config_id');
        $color = json_decode($request->get_body(),true);
        
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);

                if(empty($meta_value)){
                    return rest_ensure_response(["message" => __("Add color failed","neon-channel-product-customizer-starter")]);
                }else{

                    if(isset($meta_value["requiredOptions"]["colorOptions"]['colors'])) {
                        array_push($meta_value["requiredOptions"]["colorOptions"]['colors'],$color);
                    }else{
                        $meta_value["requiredOptions"]["colorOptions"]['colors'][0]=$color;
                    }
                    $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
    
                    if($response){
                        return rest_ensure_response(["success" => true, "message"=>__("Color added successfully","neon-channel-product-customizer-starter")]);
                    }else{
                        return rest_ensure_response(["message" => __("Add color failed","neon-channel-product-customizer-starter")]);
                    }
                }
            }
            else{
                return rest_ensure_response(["message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Update a color in the color configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_color_in_colors_setting($request){
        $id = $request->get_param('config_id');
        $color_id = $request->get_param('color_id');
        
        $color = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if ($post) {
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(!empty($meta_value)) {

                    $colors = $meta_value["requiredOptions"]["colorOptions"]['colors'];
                    
                    if(isset($colors[$color_id])){

                        if($colors[$color_id] !== $color) {
                            $colors[$color_id] = $color;
                            $meta_value["requiredOptions"]["colorOptions"]['colors'] = $colors;
                            $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
        
                            if($response){
                                return rest_ensure_response(["success" => true,"message"=>__("Update color successfully","neon-channel-product-customizer-starter")]);
                            }else{
                                return rest_ensure_response(["success" => false,"message" => __("Update color failed","neon-channel-product-customizer-starter")]);
                            }
                        }else{
                            return rest_ensure_response(["success" => "same","message" => __("No change observed on color","neon-channel-product-customizer-starter")]);
                        }
                    }else{
                        return rest_ensure_response(["success" => false,"message" => __("No color found","neon-channel-product-customizer-starter")]);
                    }


                }else {
                    return rest_ensure_response(["success" => false,"message" => __("No color setting found","neon-channel-product-customizer-starter")]);
                }
                
            }else{
                return rest_ensure_response(["success" => false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["success" => false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Delete a color in the color configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_color_in_colors_setting($request){
        $id = $request->get_param('config_id');
        $color_id = $request->get_param('color_id');
        if($id!=0){
            $post = get_post($id);
            if ($post) {
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(!empty($meta_value["requiredOptions"]["colorOptions"])){
                    $colors = $meta_value["requiredOptions"]["colorOptions"]['colors'];
                    for ($i=0; $i < count($colors); $i++) { 
                        if($color_id === $i) {
                                array_splice($colors,$color_id,1);
                        }
                    }

                    $meta_value["requiredOptions"]["colorOptions"]['colors'] = $colors;

                    $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                    if($response){
                        return rest_ensure_response(["success" => true,"message"=>__("Color deleted successfully","neon-channel-product-customizer-starter")]);
                    }else{
                        return rest_ensure_response(["message" => __("Delete color failed","neon-channel-product-customizer-starter")]);
                    }
                }else{
                    return rest_ensure_response(["message" => __("No color setting found","neon-channel-product-customizer-starter")]);
                }
                
            }else{
                return rest_ensure_response(["message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
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