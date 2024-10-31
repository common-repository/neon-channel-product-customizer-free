<?php
namespace NCPC\Api\Admin\Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching custom design settings
 */
class NCPC_Api_Custom_Design_Settings extends WP_REST_Controller {
    
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/settings/custom-designs';
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
                    'callback'            => array( $this, 'get_customDesign_settings' ),
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
            '/' . $this->rest_base."/link",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_link_customDesign_settings' ),
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
            '/' . $this->rest_base."/screen",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_screen_customDesign_settings' ),
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
            '/' . $this->rest_base."/visualizer",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_visualizer_customDesign_settings' ),
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
            '/' . $this->rest_base."/screen-review",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_screenReview_customDesign_settings' ),
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
            '/' . $this->rest_base."/notifications",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_notifications_customDesign_settings' ),
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
            '/' . $this->rest_base."/images",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_images_customDesign_settings' ),
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
                    'callback'            => array( $this, 'update_product_customDesign_settings' ),
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
     * Get all custom design settings
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function get_customDesign_settings($request){
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["message" => "No Settings found"]);
                }else{
                    if(isset($meta_value["settings"]["customDesign"])){
                        return rest_ensure_response($meta_value["settings"]["customDesign"]);
                    }
                    return rest_ensure_response(["message" => "No Custom Design Settings found"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update link options of customDesign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_link_customDesign_settings($request){
        $id = $request->get_param('config_id');
        $link_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["customDesign"]['link'] = $link_options;
                }else{
                    $meta_value["settings"]["customDesign"]['link'] = $link_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["message" => "link in customDesign settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add link in customDesign settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update screen options of customDesign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_screen_customDesign_settings($request){
        $id = $request->get_param('config_id');
        $screen_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["customDesign"]['screen'] = $screen_options;
                }else{
                    $meta_value["settings"]["customDesign"]['screen'] = $screen_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["message" => "screen in customDesign settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add screen in customDesign settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update visualizer options of customDesign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_visualizer_customDesign_settings($request){
        $id = $request->get_param('config_id');
        $visualizer_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["customDesign"]['visualizer'] = $visualizer_options;
                }else{
                    $meta_value["settings"]["customDesign"]['visualizer'] = $visualizer_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["message" => "visualizer options in customDesign settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add visualizer options in customDesign settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }

    /**
     * Update screenReview options of customDesign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_screenReview_customDesign_settings($request){
        $id = $request->get_param('config_id');
        $screenReview = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["customDesign"]['screenReview'] = $screenReview;
                }else{
                    $meta_value["settings"]["customDesign"]['screenReview'] = $screenReview;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["message" => "screenReview in customDesign settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add screenReview in customDesign settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update notifications options of customDesign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_notifications_customDesign_settings($request){
        $id = $request->get_param('config_id');
        $notifications = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["customDesign"]['notifications'] = $notifications;
                }else{
                    $meta_value["settings"]["customDesign"]['notifications'] = $notifications;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["message" => "notifications in customDesign settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add notifications in customDesign settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        } 
    }

    /**
     * Update images options of customDesign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_images_customDesign_settings($request){
        $id = $request->get_param('config_id');
        $images = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["customDesign"]['images'] = $images;
                }else{
                    $meta_value["settings"]["customDesign"]['images'] = $images;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["message" => "images in customDesign settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add images in customDesign settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }  
    }

    /**
     * Update images options of customDesign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_product_customDesign_settings($request){
        $id = $request->get_param('config_id');
        $product = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["customDesign"]['product'] = $product;
                }else{
                    $meta_value["settings"]["customDesign"]['product'] = $product;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["message" => "product in customDesign settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add product in customDesign settings failed"]);
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