<?php
namespace NCPC\Api\Admin\Required_Options;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching sizes
 */
class NCPC_Api_Sizes extends WP_REST_Controller {
     /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/sizes';
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
                    'callback'            => array( $this, 'get_size_setting' ),
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
                    'callback'            => array( $this, 'create_sizes_setting' ),
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
                    'callback'            => array( $this, 'add_size_in_sizes_setting' ),
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
            '/' . $this->rest_base."/(?P<size_id>\d+)",
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_size_info' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'size_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_size_in_sizes_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'size_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_size_in_sizes_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'size_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                )
            )
        );
        
    }

    /**
     * Create if a size configuration doesn't exist in an NCPC configuration and update else  
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_sizes_setting($request){
        $id = $request->get_param('config_id');
        $size_setting = json_decode($request->get_body(),true);
        

        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value = array();
                }
                $meta_value["requiredOptions"]["sizeOptions"] = $size_setting;
                

                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
                
                if($response){
                    return rest_ensure_response(["success" => true,"message"=>__("Size settings added successfully")]);
                }else{
                    return rest_ensure_response(["sucess"=>false,"message" => __("size settings add fail","neon-channel-product-customizer-starter")]);
                }
            
            }else{
                return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Get all sizes of ncpc configuration ID
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

    public function get_size_setting($request) {
        $id = $request->get_param('config_id');
        if($id!=0){
            $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
            if (!empty($meta_value)) {
                if(isset($meta_value["requiredOptions"]["sizeOptions"])){
                    return rest_ensure_response( $meta_value["requiredOptions"]["sizeOptions"] );
                }
                return rest_ensure_response(["sucess"=>false,"message" => __("No data found","neon-channel-product-customizer-starter")]);
            }else{
                return rest_ensure_response(["sucess"=>false,"message" => __("No data found","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }

    }
    /**
     * Get a size with ID of ncpc configuration ID
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

     public function get_size_info($request) {
        $id = $request->get_param('config_id');
        $size_id = $request->get_param('size_id');
        if($id!=0){
            $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
            
            if (!empty($meta_value)) {

                if (isset($meta_value["requiredOptions"]["sizeOptions"]['sizes'])) {
                    if($meta_value["requiredOptions"]["sizeOptions"]['sizes'][$size_id]){
                        return rest_ensure_response($meta_value["requiredOptions"]["sizeOptions"]['sizes'][$size_id] );
                    }
                    return rest_ensure_response(array("message" => __("No Size found","neon-channel-product-customizer-starter") ) );
                }else{
                    return rest_ensure_response( array("message" => __("No Size found","neon-channel-product-customizer-starter") ) );
                }
                
                
            }else{
                return rest_ensure_response(["sucess"=>false,"message" => __("No data found","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }

    }

    /**
     * Add size in size configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function add_size_in_sizes_setting($request){

        $id = $request->get_param('config_id');
        $size = json_decode($request->get_body(),true);
        
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);

                if(empty($meta_value)){
                    return rest_ensure_response(["sucess"=>false,"message" => __("Add size failed","neon-channel-product-customizer-starter")]);
                }else{
                    if(isset($meta_value["requiredOptions"]["sizeOptions"]['sizes'])){
                        array_push($meta_value["requiredOptions"]["sizeOptions"]['sizes'],$size);
                    }else{
                        $meta_value["requiredOptions"]["sizeOptions"]['sizes'][0] = $size;
                    }
                    $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
    
                    if($response){
                        return rest_ensure_response(["success" => true,"message" => __("Size added successfully","neon-channel-product-customizer-starter")]);
                    }else{
                        return rest_ensure_response(["sucess"=>false,"message" => __("Add size failed","neon-channel-product-customizer-starter")]);
                    }
                }

            }
            else{
                return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }
    
    /**
     * Update a size in the size configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_size_in_sizes_setting($request){
        $id = $request->get_param('config_id');
        $size_id = $request->get_param('size_id');
        
        $size = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if ($post) {
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(!empty($meta_value)) {
                    if($meta_value["requiredOptions"]["sizeOptions"]['sizes'][$size_id]){ 
                        if($meta_value["requiredOptions"]["sizeOptions"]['sizes'][$size_id] != $size){

                            $meta_value["requiredOptions"]["sizeOptions"]['sizes'][$size_id] = $size;
        
                            $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
        
                            if($response){
                                return rest_ensure_response(["success" => true, "message"=>__("Size updated successfully","neon-channel-product-customizer-starter")]);
                            }else{
                                return rest_ensure_response(["sucess"=>false,"message" => __("Update size failed","neon-channel-product-customizer-starter")]);
                            }
                        }else{
                            return rest_ensure_response(["success"=>"same","message" => __("No change observe on size","neon-channel-product-customizer-starter")]);
                        }                      
                    }else{                        
                        return rest_ensure_response(["sucess"=>false,"message" => __("Update size failed","neon-channel-product-customizer-starter")]);
                    }
                }else {
                    return rest_ensure_response(["sucess"=>false,"message" => __("No size setting found","neon-channel-product-customizer-starter")]);
                }
                
            }else{
                return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Delete a size in the size configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_size_in_sizes_setting($request){
        $id = $request->get_param('config_id');
        $size_id = $request->get_param('size_id');
        if($id!=0){
            $post = get_post($id);
            if ($post) {
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(!empty($meta_value["requiredOptions"]["sizeOptions"])){
                    if($meta_value["requiredOptions"]["sizeOptions"]['sizes'][$size_id]){                        
                        array_splice($meta_value["requiredOptions"]["sizeOptions"]['sizes'],$size_id,1);
    
                        $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
    
                        if($response){
                            return rest_ensure_response(["success" => true,"message"=>__("Size deleted successfully","neon-channel-product-customizer-starter")]);
                        }else{
                            return rest_ensure_response(["sucess"=>false,"message" => __("Delete size failed","neon-channel-product-customizer-starter")]);
                        }
                    }else{                        
                        return rest_ensure_response(["sucess"=>false,"message" => __("Delete size failed","neon-channel-product-customizer-starter")]);
                    }

                }else{
                    return rest_ensure_response(["sucess"=>false,"message" => __("No size found","neon-channel-product-customizer-starter")]);
                }
                
            }else{
                return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
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