<?php

namespace monPotager;

use WP_REST_Request;
use WP_User;

class Api
{
    /**
     * @var string
     */
    protected $baseURI;

    public function __construct()
    {
        // registration of our custom api
        add_action('rest_api_init', [$this, 'initialize']);

        add_action('rest_api_init', [$this, 'api_meta']);
    }


    public function initialize()
    {
        // retrieve a folder name from a file path 
        $this->baseURI = dirname($_SERVER['SCRIPT_NAME']);

        register_rest_route(
            'monpotager/v1',
            '/plantation-save', 
            [
                'methods' => 'post',
                'callback' => [$this, 'plantationSave']
            ]
        );

        register_rest_route(
            'monpotager/v1',
            '/plantation-select', 
            [
                'methods' => 'get',
                'callback' => [$this, 'plantationSelect']
            ]
        );

        register_rest_route(
            'monpotager/v1',
            '/plantation-update', 
            [
                'methods' => 'post',
                'callback' => [$this, 'plantationUpdate']
            ]
        );

        register_rest_route(
            'monpotager/v1',
            '/plantation-delete', 
            [
                'methods' => 'post',
                'callback' => [$this, 'plantationDelete']
            ]
        );

        // Create new API route
        register_rest_route(
            'monpotager/v1', // name of an API
            '/inscription', // the endpoint that will be put after the name of the api
            [
                'methods' => 'post', // the method used
                'callback' => [$this, 'inscription']
            ]
        );

        register_rest_route(
            'monpotager/v1',
            '/user-update', 
            [
                'methods' => 'post',
                'callback' => [$this, 'userUpdate']
            ]
        );

        register_rest_route(
            'monpotager/v1',
            '/user-delete', 
            [
                'methods' => 'get',
                'callback' => [$this, 'userDelete']
            ]
        );
    }

    //*****/ Plantation Utilisateur /*****//

    public function plantationSave(WP_REST_Request $request) {
        $id_plante = $request->get_param('id_plante');
        $calendarId = $request->get_param('calendarId');
        $title = $request->get_param('title');
        $start = $request->get_param('start');
        $end = $request->get_param('end');
        $category = $request->get_param('category');
        $color = $request->get_param('color');
        $bgColor = $request->get_param('bgColor');
        $dragBgColor = $request->get_param('dragBgColor');
        $borderColor = $request->get_param('dragBgColor');

        $user = wp_get_current_user();
        $id_user = $user->id;

        if (in_array('gardener', (array) $user->roles)) {
            $gardenerPlantation = new GardenerPlantation();
            $gardenerPlantation->insert($id_user, $id_plante, $calendarId, $title, $start, $end, $category, $color, $bgColor, $dragBgColor, $borderColor);

            return [
                'status'    => 'sucess',
                'id_user'   => $id_user,
                'id_plante' => $id_plante,
                'id_user' => $id_user,
                'id_plante' => $id_plante,
                'calendarId' => $calendarId,
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'category' => $category,
                'color' => $color,
                'bgColor' => $bgColor,
                'dragBgColor' => $dragBgColor,
                'borderColor' => $borderColor,
            ];
        } else  {
             return [
                 'status' => 'failed',
            ];
        }
    }

    public function plantationSelect()
    {
        $user = wp_get_current_user();
        $id_user = $user->id;

        if (in_array('gardener', (array) $user->roles)) {
            $gardenerPlantation = new GardenerPlantation();
            $result = $gardenerPlantation->getPlantationsByUserId($id_user);
    
            return [
            'status'     => 'sucess',
            'id_user'    => $id_user,
            'plantations' => $result
            ];
        }
    }

    public function plantationUpdate(WP_REST_Request $request)
    {
        $id_plantation = $request->get_param('id_plantation');
        $status = $request->get_param('status');
        $id_plante = $request->get_param('id_plante');
        $calendarId = $request->get_param('calendarId');
        $title = $request->get_param('title');
        $start = $request->get_param('start');
        $end = $request->get_param('end');
        $category = $request->get_param('category');
        $color = $request->get_param('color');
        $bgColor = $request->get_param('bgColor');
        $dragBgColor = $request->get_param('dragBgColor');
        $borderColor = $request->get_param('dragBgColor');

        $user = wp_get_current_user();
        $id_user = $user->id;

        if (in_array('gardener', (array) $user->roles)) {
            $gardenerPlantation = new GardenerPlantation();

            $result = $gardenerPlantation->update($id_user, $id_plante, $id_plantation, $calendarId, $title, $start, $end, $category, $color, $bgColor, $dragBgColor, $borderColor, $status);

            return $result; 
        }
    }

    public function plantationDelete(WP_REST_Request $request)
    {
        $id_plantation = $request->get_param('id_plantation');

        $user = wp_get_current_user();
        $id_user = $user->id;

        if (in_array('gardener', (array) $user->roles)) {
            $gardenerPlantation = new GardenerPlantation();
            $result = $gardenerPlantation->delete($id_user, $id_plantation);

            return $result;
        }
    }

    //*****/ Utilisateur /*****//

    public function inscription(WP_REST_Request $request)
    {
        $email = $request->get_param('email');
        $password = $request->get_param('password');
        $userName = $request->get_param('username');
        $region = $request->get_param('region');

        // Create new user
        $userCreateResult = wp_create_user(
            $userName,
            $password,
            $email,
        );

        // Verification that the user has been created
        if (is_int($userCreateResult)) {

            $user = new WP_User($userCreateResult);
            add_user_meta($user->id, 'region', $region, true);

            // Remove role
            $user->remove_role('subscriber');
            // Add role
            $user->add_role('gardener');

            // values that will be returned by the api 
            return [
                'success'   => true,
                'userId'    => $userCreateResult,
                'username'  => $userName,
                'email'     => $email,
                'region'    => $region,
                'role'      => 'gardener'
            ];
            
        } else {  // if the user was not created, the error occurred
            return [
                'success'=> false,
                'error' => $userCreateResult
            ];
        }
    }

    public function userUpdate(WP_REST_Request $request)
    {
        global $wpdb;
    
        $password = $request->get_param('password');
        $username = $request->get_param('username');
        $email = $request->get_param('email');
        $region = $request->get_param('region');

        $user = wp_get_current_user();
        $id_user = $user->id;


        if(isset($username)) {
            $wpdb->update(
                $wpdb->users, 
                ['user_login' => $username],
                ['ID' => $id_user]
                );       
                
            wp_update_user(array(
                'ID' => $id_user,
                'user_nicename' => $username,
                'display_name' => $username
                ));
        }

        if(isset($password)) {
            wp_set_password($password, $id_user);
        }

        if(isset($email)) {
            wp_update_user(array(
                'ID' => $id_user,
                'user_email' => $email,
                ));
        }       

        if(isset($region)) {
            update_user_meta($id_user, 'region', $region);
        }

        return 'sucess';
    }

    public function userDelete(WP_REST_Request $request)
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $user = wp_get_current_user();
        $id_user = $user->id;

         if(wp_delete_user($id_user))
         {
            return 'succes'; 
         } else {
            return 'user not found';
         }
    }


    public function api_meta()
    {
        register_rest_field(
            'user',
            'region',
            array(
                'get_callback' => [$this,'get_user_meta_for_api'],
                'schema' => null,
            )
        );
    }

    public function get_user_meta_for_api($object)
    {
        $user_id = $object['id'];
        //var_dump(get_post_meta($post_id));die;
        
        return get_user_meta( $user_id, 'region', true);
    }   
}
