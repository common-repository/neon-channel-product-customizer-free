<?php
namespace NCPC\Api\Admin\Required_Options;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching fonts
 */
class NCPC_Api_Fonts extends WP_REST_Controller {
     /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/fonts';
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
                    'callback'            => array( $this, 'get_all_fonts' ),
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
                    'callback'            => array( $this, 'create_fonts_setting' ),
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
                    'callback'            => array( $this, 'add_font_to_fonts_setting' ),
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
            '/' . $this->rest_base."/(?P<font_id>\d+)",
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_font_info' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'font_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_font_in_fonts_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'font_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_font_in_fonts_setting' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'font_id'    => array (
                            'type'     => 'integer',
                            'required' => true
                        )
                    ),
                )
            )
        );
        
    }

    /**
     * Create if a font configuration doesn't exit and update if it exists in an NCPC configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_fonts_setting($request){
        $id = $request->get_param('config_id');
        $font_setting = json_decode($request->get_body(),true);
        

        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["success"=>false,"message" => __("Updated font settings failed","neon-channel-product-customizer-starter")]);
                }else{
                    $meta_value["requiredOptions"]["fontOptions"] = $font_setting;
    
                    $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
                    
                    if($response){
                        return rest_ensure_response(["success" => true,"message"=> __("Font settings updated successfully","neon-channel-product-customizer-starter")]);
                    }else{
                        return rest_ensure_response(["success"=>false,"message" => __("Updated font settings failed","neon-channel-product-customizer-starter")]);
                    }

                }
            
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Get all fonts of ncpc configuration ID
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

    public function get_all_fonts($request) {
        $id = $request->get_param('config_id');
        if($id!=0){
            $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
            if (!empty($meta_value)) {
                return rest_ensure_response( ["allFonts"=>$meta_value["requiredOptions"]["fontOptions"],
                    'pricings'=>$meta_value["requiredOptions"]["priceOptions"],
                    'sizes'=>$meta_value["requiredOptions"]["sizeOptions"]["sizes"],
                ] );
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("No data found","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }

    }
    /**
     * Get a font with ID of ncpc configuration ID
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

     public function get_font_info($request) {
        $id = $request->get_param('config_id');
        $font_id = $request->get_param('font_id');
        if($id!=0){
            $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
            
            if (!empty($meta_value)) {

                if (isset($meta_value["requiredOptions"]["fontOptions"]['fonts'])) {
                    $fonts= $meta_value["requiredOptions"]["fontOptions"]['fonts'];
                    for ($i=0; $i < $fonts; $i++) { 
                        if($i===$font_id){
                            return rest_ensure_response( $fonts[$font_id] );
                        }
                    }
                    return rest_ensure_response(array("message" => __("No font found","neon-channel-product-customizer-starter") ) );
                }else{
                    return rest_ensure_response( array("message" => __("No font found","neon-channel-product-customizer-starter") ) );
                }
                
                
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("No font found","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }

    }

    /**
     * Add font in font configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function add_font_to_fonts_setting($request){

        $id = $request->get_param('config_id');
        $font = json_decode($request->get_body(),true);
        
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);

                if(empty($meta_value)){
                    return rest_ensure_response(["success"=>false,"message" => __("Add font failed","neon-channel-product-customizer-starter")]);
                }else{
                    
                    if(isset($meta_value["requiredOptions"]["fontOptions"]['fonts'])) {
                        array_push($meta_value["requiredOptions"]["fontOptions"]['fonts'],$font);
                    }else{
                        $meta_value["requiredOptions"]["fontOptions"]['fonts'][0] = $font;
                    }
                    $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
    
                    if($response){
                        return rest_ensure_response(["success" => true,"message" => __("Font added successfully","neon-channel-product-customizer-starter")]);
                    }else{
                        return rest_ensure_response(["success"=>false,"message" => __("Add font failed","neon-channel-product-customizer-starter")]);
                    }
                }
            }
            else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Update a font in the font configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_font_in_fonts_setting($request){
        $id = $request->get_param('config_id');
        $font_id = $request->get_param('font_id');
        
        $font = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if ($post) {
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(!empty($meta_value)) {

                    $meta_value["requiredOptions"]["fontOptions"]['fonts'];

                    if(isset($meta_value["requiredOptions"]["fontOptions"]['fonts'][$font_id])){
                        if($meta_value["requiredOptions"]["fontOptions"]['fonts'][$font_id] !== $font){
                            $meta_value["requiredOptions"]["fontOptions"]['fonts'][$font_id] = $font;
        
                            $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
        
                            if($response){
                                return rest_ensure_response(["success" => true,"message"=>__("Font updated successfully","neon-channel-product-customizer-starter")]);
                            }else{
                                return rest_ensure_response(["success"=>false,"message" => __("Update font failed","neon-channel-product-customizer-starter")]);
                            }
                        }else{
                            return rest_ensure_response(["success"=>"same","message" => __("No change observed on font","neon-channel-product-customizer-starter")]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>false,"message" => __("No font found","neon-channel-product-customizer-starter")]);
                    }

                }else {
                    return rest_ensure_response(["success"=>false,"message" => __("No font found","neon-channel-product-customizer-starter")]);
                }
                
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid","neon-channel-product-customizer-starter")]);
        }
    }

    /**
     * Delete a font in the font configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_font_in_fonts_setting($request){
        $id = $request->get_param('config_id');
        $font_id = $request->get_param('font_id');
        if($id!=0){
            $post = get_post($id);
            if ($post) {
                $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
                if(!empty($meta_value["requiredOptions"]["fontOptions"])){
                    
                    if(isset($meta_value["requiredOptions"]["fontOptions"]['fonts'][$font_id])){
                        array_splice($meta_value["requiredOptions"]["fontOptions"]['fonts'],$font_id,1);
    
                        $response = update_post_meta($id,'ncpc-configs-meta',$meta_value);
    
                        if($response){
                            return rest_ensure_response(["success" => true,"message"=>__("Font deleted successfully","neon-channel-product-customizer-starter")]);
                        }else{
                            return rest_ensure_response(["success"=>false,"message" => __("Delete font failed","neon-channel-product-customizer-starter")]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>false,"message" => __("No font found","neon-channel-product-customizer-starter")]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false,"message" => __("No font found","neon-channel-product-customizer-starter")]);
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