<?php
namespace NCPC\Api\Admin\Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching themes settings
 */
class NCPC_Api_Theme_Settings extends WP_REST_Controller {
    
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/settings/themes';
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
                    'callback'            => array( $this, 'get_theme_settings' ),
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
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_themes_settings' ),
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
     * Get all themes settings
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function get_theme_settings($request){
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["message" => __("No Settings found","NCPC")]);
                }else{
                    if(isset($meta_value["settings"]["themes"])){
                        return rest_ensure_response($meta_value["settings"]["themes"]);
                    }
                    return rest_ensure_response(["message" => __("No themes Settings found","NCPC")]);
                }
            }else{
                return rest_ensure_response(["message" => __("Custom ID invalid","NCPC")]);
            }
        }else{
            return rest_ensure_response(["message" => __("Custom ID invalid","NCPC")]);
        }
    }
    /**
     * Update themes options of themes settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_themes_settings($request){
        $id = $request->get_param('config_id');
        $skin_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["success"=>false,"message" => __("Update of skin options in themes settings failed","NCPC")]);
                }else{
                    if($meta_value["settings"]["themes"] !== $skin_options){
                        $meta_value["settings"]["themes"] = $skin_options;
                        $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
                        
                        if($response){
                            return rest_ensure_response(["success"=>true,"message" => __("Skin options in themes settings updated successfully","NCPC")]);
                        }else{
                            return rest_ensure_response(["success"=>false,"message" => __("Update of skin options in themes settings failed","NCPC")]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>"same","message" => __("No change observed skin options in themes settings failed","NCPC")]);
                    }
                }
                
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","NCPC")]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","NCPC")]);
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