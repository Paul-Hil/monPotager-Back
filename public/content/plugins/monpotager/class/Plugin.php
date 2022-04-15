<?php

namespace monPotager;

class Plugin
{
    /**
     * Constructeur de la classe Plugin
     * rajoute les hooks pour créer les taxo et CPT
     */
    public function __construct()
    {
        $metaPeriod = new MetaPeriod();
        $userPlanting = new User_planting;
        $event = new Event();

        add_action('init', [$this, 'createPlanteCPT']);

        add_action('init', [$this, 'createPlanteTypeTaxonomy']);

        add_action('init', [$this, 'createPlanteRegionsTaxonomy']);

        add_action('add_meta_boxes', [$metaPeriod, 'metaboxesloadSemi']);
        add_action('save_post', [$metaPeriod, 'save_metaboxe']);

        add_action('rest_api_init', [$this, 'api_meta']);

        add_action('add_meta_boxes', [$userPlanting, 'user_Metaboxes_Planting']);
        add_action('save_post', [$userPlanting, 'saveUserMetaboxesDaysPlantation']);

        add_action('rest_api_init', [$event, 'initialize']);
    }
    
    public function activate()
    {
        $this->registerGardenerRole();

        $gardenerplant = new GardenerPlantation;
        $gardenerplant->createTable();
    }

    public function deactivate()
    {
        remove_role('gardener');

        $gardenerplant = new GardenerPlantation;
        $gardenerplant->dropTable();
    }

    public function registerGardenerRole()
    {
        add_role(
            'gardener',
            'Jardinier'
        );
    }

    /**
     * Rajoute un nouveau post type à wp
     * Cette fonction doit être appelée par un hook, si possible lors de l'action 'init'
     */
    public function createPlanteCPT()
    {
        register_post_type('plante', [

            'labels' => [
                'name'          => 'Plantes',
                'singular_name' => 'Plante',
                'add_new'       => 'Ajouter une plante',
                'add_new_item'  => 'Ajouter une plante',
                'not_found'     => 'Aucun plante trouvée',
                'edit_item'     => 'Modifier la plante',
            ],

            'public' => true,
            'menu_icon' => 'dashicons-carrot',
            add_theme_support('post-thumbnails'),

            //  Je veux que mes plantes apparaissent dans l'API fournis par WP
            'show_in_rest' => true,

            'supports' => [
                'title',
                'thumbnail',
                'editor',
                'excerpt'
            ],
            
        ]);
    }

    /**
     * Crée la taxonomie 'Ingrédient', liée au cpt Recipe
     */
    public function createPlanteRegionsTaxonomy()
    {
        register_taxonomy(
            'regions',
            ['plante'],
            [
                'label' => 'Régions',
                'show_in_rest'  => true,
                'hierarchical'  => false,
                'public'        => true,
            ],
        );
    }

    public function createPlanteTypeTaxonomy()
    {
        register_taxonomy(
            'plante_type',
            ['plante'],
            [
                'label' => 'Type de plante',
                'show_in_rest'  => true,
                'hierarchical'  => false,
                'public'        => true,
            ],
        );
    }

    public function api_meta()
    {
        register_rest_field(
            'plante',
            'periode_regions',
            array(
                'get_callback' => [$this,'get_post_meta_for_api'],
                'schema' => null,
                'posts_per_page'=>-1 
            )
        );
    }

    public function get_post_meta_for_api($object)
    {
        $post_id = $object['id'];
        
        
        return get_post_meta($post_id);
    }   
}
