<?php
namespace NCPC\Api\Admin\Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching Sort_Options settings
 */
class NCPC_Api_Sort_Options_Settings extends WP_REST_Controller {
    
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/settings/sort-options';
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
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_sort_options_settings' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_sort_options_settings' ),
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
     * Get all sort Options settings
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function get_sort_options_settings($request){
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["message" => "No Settings found"]);
                }else{
                    if(isset($meta_value["settings"]["sortOptions"])){
                        return rest_ensure_response($meta_value["settings"]["sortOptions"]);
                    }
                    return rest_ensure_response(["message" => "No sort Options Settings found"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update sort options of Sort_Optionss settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_sort_options_settings($request){
        $id = $request->get_param('config_id');
        $sort_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]['sortOptions'] = $sort_options;
                }else{
                    $meta_value["settings"]['sortOptions'] = $sort_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "sort options in settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add sort options in settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
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