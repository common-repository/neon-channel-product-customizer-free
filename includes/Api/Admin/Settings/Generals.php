<?php
namespace NCPC\Api\Admin\Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching generals settings
 */
class NCPC_Api_General_Settings extends WP_REST_Controller {
    
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/settings/generals';
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
                    'callback'            => array( $this, 'get_generals_settings' ),
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
            '/' . $this->rest_base."/customizer",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_customizer_options_generals_settings' ),
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
            '/' . $this->rest_base."/mobile",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_mobile_options_generals_settings' ),
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
            '/' . $this->rest_base."/product",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_product_options_generals_settings' ),
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
    }

    /**
     * Get all generals settings
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function get_generals_settings($request){
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["message" => "No Settings found"]);
                }else{
                    if(isset($meta_value["settings"]["generals"])){
                        return rest_ensure_response($meta_value["settings"]["generals"]);
                    }
                    return rest_ensure_response(["message" => "No generals Settings found"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update customizer options of generals settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_customizer_options_generals_settings($request){
        $id = $request->get_param('config_id');
        $customizer_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["generals"]['customizer'] = $customizer_options;
                }else{
                    $meta_value["settings"]["generals"]['customizer'] = $customizer_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "customizer options in generals settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add customizer options in generals settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update mobile options of generals settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_mobile_options_generals_settings($request){
        $id = $request->get_param('config_id');
        $mobile_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["generals"]['mobile'] = $mobile_options;
                }else{
                    $meta_value["settings"]["generals"]['mobile'] = $mobile_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "mobile options in generals settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add mobile options in generals settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update product options of generals settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_product_options_generals_settings($request){
        $id = $request->get_param('config_id');
        $product_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["generals"]['product'] = $product_options;
                }else{
                    $meta_value["settings"]["generals"]['product'] = $product_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "product options in generals settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add product options in generals settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }

    /**
     * Update output file options of generals settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    /**
     * Checks if a given request has access to read the items.
     *
     * @param  \WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_config_permissions_check( $request ) {
        return true;
    }
}