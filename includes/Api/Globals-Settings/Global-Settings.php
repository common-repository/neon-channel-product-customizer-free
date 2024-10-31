<?php
namespace NCPC\Api\Globals_Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * REST_API Handler
 */
class NCPC_Api_Globals_Settings extends WP_REST_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'ncpc/v1';
        $this->rest_base = 'globals-settings';
    }

    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/config-page",
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'save_config_page' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_config_page' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                ),
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/pages",
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_new_page' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_available_pages' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                ),
            )
        );
    }

    /**
     * Add config page
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function save_config_page( $request ) {
        $params=json_decode($request->get_body());
        if(isset($params->configPage)){
            $option = update_option("ncpc_config_page",$params->configPage);
            if($option){
                return rest_ensure_response(["success" => "Config page added successfully"]);
            }else{
                return rest_ensure_response(["message" => "Adding Config page failed"]);
            }
        }
        return rest_ensure_response(["message" => "Config page not found"]);
    }

    /**
     * Get config page
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_config_page( $request ) {

        $option = get_option("ncpc_config_page");
        
        if($option == false || empty($option) ){
            return rest_ensure_response(["message" => "Config page not found"]);
        }else{
            return rest_ensure_response($option);
        }
    }

    /**
     * Show all published pages
     *  @param  \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    function get_available_pages() {
        $args = array(
            'post_type' => 'page', // Type de contenu "page"
            'post_status' => 'publish', // Statut "publié"
            'posts_per_page' => -1, // Tous les articles (-1 pour afficher tous les articles)
        );
    
        $existing_pages = get_posts($args);

        $pages[0] = esc_html__("None","neon-channel-product-customizer-free");
        
        foreach ($existing_pages as $page) {
            $pages[$page->ID] = $page->post_title;
        }
    
        return rest_ensure_response($pages);
    }

    /**
     * Add new page 
     * 
     * @param  \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    function create_new_page($request) {
        $params=json_decode($request->get_body());
        if(isset($params->title)){
            
            $new_page = array(
                'post_title' => $params->title,
                'post_status' => 'publish', 
                'post_type' => 'page'
            );
        
            $page_id = wp_insert_post($new_page);
            
            if($page_id){
                return rest_ensure_response($page_id);
            }else{
                return rest_ensure_response(["message"=>"Page was not created"]);
            }

        }else{
            return rest_ensure_response(["message"=>"Page was not created"]);
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
        
       /* if ( ! is_user_logged_in() ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'Vous devez être connecté pour accéder à cette ressource.', 'text-domain' ), array( 'status' => rest_authorization_required_code() ) );
        }
    
        // Check if the user has the ability to edit posts (can be admin or any other role with this ability)
        if ( ! current_user_can( 'edit_posts' ) ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette ressource.', 'text-domain' ), array( 'status' => rest_authorization_required_code() ) );
        }*/
    
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
