<?php
namespace NCPC\Api\Admin\Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching language images settings
 */
class NCPC_Api_Language_Images_Settings extends WP_REST_Controller {
    
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/settings/language-images';
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
                    'callback'            => array( $this, 'get_language_images_settings' ),
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
            '/' . $this->rest_base."/main",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_main_options_language_images_settings' ),
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
            '/' . $this->rest_base."/custom-design",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_custom_design_options_language_images_settings' ),
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
                    'callback'            => array( $this, 'update_visualizer_options_language_images_settings' ),
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
            '/' . $this->rest_base."/review-screen",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_review_screen_language_images_settings' ),
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
                    'callback'            => array( $this, 'update_notifications_language_images_settings' ),
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
                    'callback'            => array( $this, 'update_images_language_images_settings' ),
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
                    'callback'            => array( $this, 'update_product_language_images_settings' ),
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
     * Get all language images settings
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function get_language_images_settings($request){
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["message" => "No Settings found"]);
                }else{
                    if(isset($meta_value["settings"]["languageImages"])){
                        return rest_ensure_response($meta_value["settings"]["languageImages"]);
                    }
                    return rest_ensure_response(["message" => "No language images Settings found"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update main options of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_main_options_language_images_settings($request){
        $id = $request->get_param('config_id');
        $main_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["languageImages"]['main'] = $main_options;
                }else{
                    $meta_value["settings"]["languageImages"]['main'] = $main_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "main options in _ settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add main options in _ settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update loading screen options of _ settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_custom_design_options_language_images_settings($request){
        $id = $request->get_param('config_id');
        $custom_design_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["languageImages"]['customDesign'] = $custom_design_options;
                }else{
                    $meta_value["settings"]["languageImages"]['customDesign'] = $custom_design_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "custom design options in language images settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add custom design options in language images settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }
    /**
     * Update visualizer of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_visualizer_options_language_images_settings($request){
        $id = $request->get_param('config_id');
        $visualizer_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["languageImages"]['visualizer'] = $visualizer_options;
                }else{
                    $meta_value["settings"]["languageImages"]['visualizer'] = $visualizer_options;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "visualizer options in language images settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add visualizer options in language images settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }

    /**
     * Update review screen of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_review_screen_language_images_settings($request){
        $id = $request->get_param('config_id');
        $review_screen = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["languageImages"]['reviewScreen'] = $review_screen;
                }else{
                    $meta_value["settings"]["languageImages"]['reviewScreen'] = $review_screen;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "reviews screen  options in language images settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add reviews screen  options in language images settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }

    /**
     * Update notifications of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_notifications_language_images_settings($request){
        $id = $request->get_param('config_id');
        $notifications = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["languageImages"]['notifications'] = $notifications;
                }else{
                    $meta_value["settings"]["languageImages"]['notifications'] = $notifications;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "update notifications in language images settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add notifications in language images settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }

    /**
     * Update images of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_images_language_images_settings($request){
        $id = $request->get_param('config_id');
        $images = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["languageImages"]['images'] = $images;
                }else{
                    $meta_value["settings"]["languageImages"]['images'] = $images;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "update images in language images settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add images in language images settings failed"]);
                }
            }else{
                return rest_ensure_response(["message" => "Custom ID invalid"]);
            }
        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
    }

    /**
     * Update product of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_product_language_images_settings($request){
        $id = $request->get_param('config_id');
        $product = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    $meta_value=array();
                    $meta_value["settings"]["languageImages"]['product'] = $product;
                }else{
                    $meta_value["settings"]["languageImages"]['product'] = $product;
                }
                $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success" => "update product in language images settings added successfully"]);
                }else{
                    return rest_ensure_response(["message" => "add product in language images settings failed"]);
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