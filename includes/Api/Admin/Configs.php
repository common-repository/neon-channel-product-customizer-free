<?php
namespace NCPC\Api\Admin;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * REST_API Handler
 */
class NCPC_Api_Configs extends WP_REST_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'configs';
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
                    'callback'            => array( $this, 'get_configs' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' )
                ),
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_config_post' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                ),
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/(?P<config_id>\d+)",
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_config_post' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_config_post' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_config'),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
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
     * Create ncpc product configuration
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_config_post( $request ) {
        $params=json_decode($request->get_body(),true);
        $data = [
            'post_title' => $params["title"],
            'post_type' => 'ncpc-configs',
            'post_meta' => [
                "ncpc-configs-meta"=>[]
            ],
            'post_status' => 'publish'
        ];
        $args = array(
            'post_type' => 'ncpc-configs',
            'post_status' => 'publish',
            'numberposts' => -1
        );
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'ncpc-pricings-mode',
                'field'    => 'slug',
                'terms'    => 'simple',
            ),
        );
        // Get custom post types using WP_Query
        $query = new WP_Query( $args );
    
        // Check the results and return the response
        $posts_data = array();
        
        if ( $query->have_posts() ) {
      
            while ( $query->have_posts() ) {
                $query->the_post();
                $id=get_the_ID();
                $types = wp_get_post_terms( $id,'ncpc-products-type');
                $type ='';
                foreach ($types as $key => $value) {
                   $type = $value->name;
                }
                $modes = wp_get_post_terms( $id,'ncpc-pricings-mode');
                $mode = '';
                foreach ($modes as $key => $value) {
                    $mode = $value->name;
                }
                $post_data = array(
                    'id'           => $id,
                    'title'        => get_the_title(),
                    'productType' => strtolower($type),
                    'pricingMode'   => strtolower($mode)
                );
                $posts_data[] = $post_data;
                
            }
        }
        if(count($posts_data) == 0){
            
            if(isset($params["productType"]) && isset($params["pricingMode"])){
                $post_id = wp_insert_post($data);
                if($post_id != 0){
                    wp_set_object_terms( $post_id, $params["productType"], 'ncpc-products-type',false);
                    wp_set_object_terms( $post_id, $params["pricingMode"], 'ncpc-pricings-mode',false);
                    if(isset($params["data"]) && !empty($params["data"])){
                        update_post_meta($post_id,'ncpc-configs-meta',$params["data"]);
                    }
                    
                    return rest_ensure_response( ["post_id"=>$post_id] );
                    
                }else{
                    return rest_ensure_response(["message" => __("Registration failed","neon-channel-product-customizer-free")]);
                }
            }else{
                return rest_ensure_response(["message" => __("Missing parameters error","neon-channel-product-customizer-free")]);
            }

        }else{
            return rest_ensure_response(["message"=> __("You can only create one configuration in this version of the plugin","neon-channel-product-customizer-free")]);
        }
    }
    /**
     * Get config info for $post id
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_config_post($request){
        $id=$request->get_param('config_id');
        if($id!=0) {
            $types = wp_get_post_terms( $id,'ncpc-products-type');
            $modes = wp_get_post_terms( $id,'ncpc-pricings-mode');
            $meta_value = get_post_meta($id, 'ncpc-configs-meta', true);
            if(is_array($types) && is_array($modes)){
                $type ='';
                foreach ($types as $key => $value) {
                $type = $value->name;
                }
                
                $mode = '';
                foreach ($modes as $key => $value) {
                    $mode = $value->name;
                }
                $post_data = array(
                    'id'           => $id,
                    'title'        => get_the_title($id),
                    'productType' => strtolower($type),
                    'pricingMode'   => strtolower($mode),
                    'data' => !empty($meta_value) ? $meta_value : []
                );
                return rest_ensure_response($post_data);
            }else{
                return rest_ensure_response(["message" => "Not NCPC Config Post"]);
            }
            

        }else{
            return rest_ensure_response(["message" => "Custom ID invalid"]);
        }
        
        
    }
     /**
     * Update of ncpc produit configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

    public function update_config_post($request){
        $params=json_decode($request->get_body(),true); 
        $args=array(
            'ID'         => $request->get_param( 'config_id' ),
            'post_title' => $params["title"],
        );

        $updatePosts = wp_update_post($args);
        if($updatePosts instanceof WP_Error){
            return rest_ensure_response(array('success' => false ) );
        }
        else{
            return rest_ensure_response(array('success' => true ) );
        }
        
    }

    /**
     * Remove ncpc configuration from ID in request
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return $success message if is ok and fail otherwise. 
    */
    public function delete_config($request){

        $id=$request->get_param( 'config_id' );

        if($id!=0){
            wp_delete_post( $id, true );
            return rest_ensure_response( array('success'=>true) );
        }
        else{
            return rest_ensure_response( array('success'=>false) );
        }
    }

    /**
     * Get all ncpc produits configurations with or no per_page,page param in api url
     *
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

    public function get_configs( $request ) {
        $args = array(
            'post_type' => 'ncpc-configs',
            'post_status' => 'publish',
            'numberposts' => -1
        );
    
        //displays 10 results per page if per_page is not specified
        $per_page = $request->get_param( 'per_page' );
        if ( ! empty( $per_page ) ) {
            $args['posts_per_page'] = absint( $per_page );
        } else {
            $args['posts_per_page'] = 5; 
        }

        // Make Pagination
        $page = $request->get_param( 'page' );
        if ( ! empty( $page ) ) {
            $args['paged'] = absint( $page );
        }
    
        // Make search 
        $search_query = $request->get_param( 'search' );
        if ( ! empty( $search_query ) ) {
            $args['s'] = sanitize_text_field( $search_query );
        }
    
        $productType = $request->get_param( 'productType' );
    
        if ( ! empty( $productType )) {
            $args['tax_query'] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'ncpc-products-type',
                    'field'    => 'slug',
                    'terms'    => $productType,
                ),
                array(
                    'taxonomy' => 'ncpc-pricings-mode',
                    'field'    => 'slug',
                    'terms'    => 'simple',
                ),
            );
        }
    
        // Get custom post types using WP_Query
        $query = new WP_Query( $args );
    
        // Check the results and return the response
        if ( $query->have_posts() ) {

            $posts_data = array();
      
            while ( $query->have_posts() ) {
                $query->the_post();
                $id=get_the_ID();
                $types = wp_get_post_terms( $id,'ncpc-products-type');
                $type ='';
                foreach ($types as $key => $value) {
                   $type = $value->name;
                }
                $modes = wp_get_post_terms( $id,'ncpc-pricings-mode');
                $mode = '';
                foreach ($modes as $key => $value) {
                    $mode = $value->name;
                }
                $post_data = array(
                    'id'           => $id,
                    'title'        => get_the_title(),
                    'productType' => strtolower($type),
                    'pricingMode'   => strtolower($mode)
                );
                $posts_data[] = $post_data;
                
            }
            $i =0 ;
            foreach ($posts_data as $key => $value) {
                if($value['pricingMode'] == 'advanced' || $value['productType'] == 'channel') {
                    wp_delete_post($value['id'],true);
                }else{
                    if($value['pricingMode'] == 'simple' && $i == 0) {
                        $i++;
                    }else if($i!=0){
                        wp_delete_post($value['id'],true);
                    }
                }
            }
            return rest_ensure_response( [$posts_data[0]] );
    
        } else {
            return rest_ensure_response( [] );
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
        
        // If the user is logged in and has the rights to the posts, access to the route is authorized.
        return true;
    }

    /**
     * Retrieves the query params for the items collection.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
        return [];
    }
}
