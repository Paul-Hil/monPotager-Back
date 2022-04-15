<?php

namespace monPotager;

use WP_REST_Request;
use WP_User;

/**
 * Point de départ des routes customisées de
 * l'API de notre application
 */
class Api
{
    public function __construct()
    {
        // enregistrement de notre api personnalisée
        add_action('rest_api_init', [$this, 'register_route']);

        add_action('rest_api_init', [$this, 'api_meta']);
    }

    public function register_route()
    {
        register_rest_route(
            'monpotager/v1', 
            '/plantation-select', 
            [
                'methods' => 'get', 
                'callback' => [$this, 'plantationSelect'] 
            ]
        );
        
        // Créer une nouvelle route à l'API
        register_rest_route(
            'monpotager/v1', // nom de l'API
            '/plantation-save', // l'endpoint qui sera mis après le nom de l'api
            [
                'methods' => 'post', // la méthode http utilisée pour la requête
                'callback' => [$this, 'plantationSave'],
            ]
        );

        register_rest_route(
            'monpotager/v1',
            '/plantation-update', 
            [
                'methods' => 'patch',
                'callback' => [$this, 'plantationUpdate']
            ]
        );

        register_rest_route(
            'monpotager/v1',
            '/plantation-delete', 
            [
                'methods' => 'delete',
                'callback' => [$this, 'plantationDelete']
            ]
        );

        register_rest_route(
            'monpotager/v1', 
            '/inscription',
            [
                'methods' => 'post',
                'callback' => [$this, 'inscription']
            ]
        );

        register_rest_route(
            'monpotager/v1',
            '/user-update', 
            [
                'methods' => 'patch',
                'callback' => [$this, 'userUpdate']
            ]
        );

        register_rest_route(
            'monpotager/v1',
            '/user-delete', 
            [
                'methods' => 'delete',
                'callback' => [$this, 'userDelete']
            ]
        );
    }

    //*****/ Plantation Utilisateur /*****//

    /**
     * Ajouter la plantation d'un utilisateur
     * @param WP_REST_Request $request
     */
    public function plantationSave(WP_REST_Request $request) {

        // Récupère les données contenus avec la requête
        $id_plante = $request->get_param('id_plante');
        $id_calendar = $request->get_param('id_calendar');
        $title = $request->get_param('title');
        
        // Vérifie que tout les paramètres soient présents
        if (isset($id_plante, $title, $id_calendar)) {

            // Appliquer un filtre de nettoyage
            $id_plante = filter_var($id_plante, FILTER_SANITIZE_NUMBER_INT);
            $id_calendar = filter_var($id_calendar, FILTER_SANITIZE_NUMBER_INT);
            $title = filter_var($title, FILTER_SANITIZE_STRING);

            // Récupère l'utilisateur responsable de la requête
            $user = wp_get_current_user();
            $id_user = $user->id;

            // Vérifie le rôle associé à l'utilisateur
            if (in_array('gardener', (array) $user->roles)) {

                // Créer un objet
                $gardenerPlantation = new GardenerPlantation();
                // Exécute la requête inclus dans la méthode insert
                $result = $gardenerPlantation->insert($id_user, $id_plante, $title, $id_calendar);

                if (empty($result)) { // Si la ligne à bien été ajoutée
                    return [ // Tableau envoyé en réponse à la requête
                        'status'    => 'sucess',
                        'id_user'   => $id_user,
                        'id_plante' => $id_plante,
                        'id_calendar' => $id_calendar,
                        'title' => $title
                    ];
                } else {
                    return [
                        'status' => 'Error: Plantation non ajoutée',
                    ];
                }
            } else {
                return [
                    'status' => 'Error: Accès manquant',
                ];
            }
        } else {
            return [
                'status' => 'Error: Paramètre manquant',
            ];
        } 
    }

    /**
     * Lire les plantations d'un utilisateur
     */
    public function plantationSelect()
    {
        // Récupère l'utilisateur responsable de la requête
        $user = wp_get_current_user();
        $id_user = $user->id;

        // Vérifie le rôle associé à l'utilisateur
        if (in_array('gardener', (array) $user->roles)) {
            
            $gardenerPlantation = new GardenerPlantation();
            $result = $gardenerPlantation->getPlantationsByUserId($id_user);

            return [ // Tableau envoyé en réponse à la requête
                'status'     => 'sucess',
                'id_user'    => $id_user,
                'plantations' => $result
            ];
        } else  {
            return [
                'status'     => 'no access',
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

    /**
     * Inscription d'un utilisateur
     *
     * @param WP_REST_Request $request
     */
    public function inscription(WP_REST_Request $request)
    {
        // Récupère les données envoyé avec la requête
        $email = filter_var($request->get_param('email', FILTER_SANITIZE_EMAIL));
        $userName = filter_var($request->get_param('username'), FILTER_SANITIZE_STRING);
        $region = filter_var($region = $request->get_param('region'), FILTER_SANITIZE_STRING);
        $password = $request->get_param('password');
        
        // Vérifie que toutes les données sont bien rentrés
        if (isset($email, $password, $userName, $region) && !empty($email) && empty($userName) && empty($region)) {

            //  Créer un nouvel utilisateur
            $userCreateResult = 
            wp_create_user(
                    $userName,
                    $password,
                    $email,
                );

            // Vérifie que l'utilisateur ait bien été créé
            if (is_int($userCreateResult)) {

                add_user_meta($userCreateResult, 'region', $region);

                // Objet de l'utilisateur en cours
                $user = new WP_User($userCreateResult);

                // Suppression du rôle
                $user->remove_role('subscriber');
                // Ajoût du rôle
                $user->add_role('gardener');

                // Valeurs qui seront retournées par l'api
                return [
                    'success'   => true,
                    'userId'    => $userCreateResult,
                    'username'  => $userName,
                    'email'     => $email,
                    'region'    => $region,
                    'role'      => 'gardener'
                ];
            }
        }
        return [ // si l'utilisateur n'a pas été créé
            'success'=> false,
            'user' => $userCreateResult
        ];
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

    public function userDelete()
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
