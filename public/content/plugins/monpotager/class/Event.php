<?php

namespace monPotager;

use WP_REST_Request;
use WP_Query;

class Event
{
    const regions = [
        'Auvergne-Rhône-Alpes'       => '_auvergne',
        'Bourgogne-Franche-Comté'    => '_bourgogne',
        'Bretagne'                   => '_bretagne',
        'Centre-Val de Loire'        => '_centre',
        'Corse'                      => '_corse',
        'Grand Est'                  => '_est',
        'Hauts-de-France'            => '_hauts',
        'Île-de-France'              => '_ile',
        'Normandie'                  => '_normandie',
        'Nouvelle-Aquitaine'         => '_aquitaine',
        'Occitanie'                  => '_occitanie',
        'Pays de la Loire'           => '_loire',
        'Provence-Alpes-Côte d’Azur' => '_azur',
    ];

    /**
     * @var string
     */
    protected $baseURI;

    public function initialize()
    {
        // retrieve a folder name from a file path
        $this->baseURI = dirname($_SERVER['SCRIPT_NAME']);

        // Create new API route
        register_rest_route(
            'monpotager/v1', // name of an API
            '/event', // the endpoint that will be put after the name of the api
            [
                'methods' => 'post', // the method used
                'callback' => [$this, 'recoverAllDatas']
            ]
        );
    }

    public function recoverAllDatas(WP_REST_Request $request)
    {      
        $idRegionSelected = $request->get_param('idRegionSelected');

        $args = array(
            'post_type' => 'plante',
            'posts_per_page'=> -1, 
        );
    
        $post_query = new WP_Query($args); // Récupère la liste des posts 'plante'

        //$regiontest = get_term_by('name', 'regions', 'post_tag');

        foreach ($post_query->posts as $post) {

            $planteId = $post->ID; // Stock l'id du post
            $planteTitle = $post->post_title; // Stock le titre du post

            $periodeMetaBox = get_post_meta($planteId); // Récupère les metabox (les periodes) du post
            $termsRegions = get_terms( array( 
                'taxonomy' => 'regions') );
            
            foreach($termsRegions as $region) {
                if($region->term_id == $idRegionSelected) {
                    $regionSelectedName = $region->name;
                    $regionSelectedSlug = $region->slug;

                }
            }            

            foreach (self::regions as $region => $value) { // Boucle sur le tableau des régions
                if ($region === $regionSelectedName) {
                    $debut_semi = $periodeMetaBox['debut_semi' . $value]; // Stocke la valeur des periodes
                    $debut_plant = $periodeMetaBox['debut_plant' . $value];
                    $debut_recolte = $periodeMetaBox['debut_recolte' . $value];

                    $semis = substr($debut_semi[0], 5, 2); // Filtre pour ne garder que le mois
                    $plantations = substr($debut_plant[0], 5, 2);
                    $recoltes = substr($debut_recolte[0], 5, 2);

                    if(isset($regionSelected) || isset($idRegionSelected)) {
                        //return 'Error: Aucune région séléctionnée pour la plante';
                    }

                    $listPeriodeRegions[$planteTitle]['id'] = $planteId; // Place l'id de la plante dans le tableau

                    if ($semis !== false) {
                        $listPeriodeRegions[$planteTitle]['debut_semi'][$region] = $semis; // Stock la donnée dans un tableau
                    } else {
                        $listPeriodeRegions[$planteTitle]['debut_semi'][$region] = null;
                    }

                    if ($plantations !== false) {
                        $listPeriodeRegions[$planteTitle]['debut_plant'][$region] = $plantations;
                    } else {
                        $listPeriodeRegions[$planteTitle]['debut_plant'][$region] = null;
                    }

                    if ($recoltes !== false) {
                        $listPeriodeRegions[$planteTitle]['debut_recolte'][$region] = $recoltes;
                    } else {
                        $listPeriodeRegions[$planteTitle]['debut_recolte'][$region] = null;
                    }
                }
            }
            //$listPeriodeRegions[$planteTitle]['selectedRegion']['name'] = $regionSelectedSlug;
            //var_dump($listPeriodeRegions);exit;
        }
    
        $ActualMonth = date('m');
        if($ActualMonth === 12) {
            $nextMonth = '01';
        } else {
            $nextMonthInt = $ActualMonth + 1;
            $nextMonth = strval($nextMonthInt);  // INT --> String
        }
        
        setlocale(LC_TIME, "");
        setlocale (LC_TIME, 'fr_FR.utf8', 'fra'); 

        $fullDate = date('Y-'.$nextMonth.'-d');
        $monthReturn = strftime("%B",strtotime($fullDate));

        $listEvent = [];
        $listEvent['selectedRegion'] = array('id' => $idRegionSelected,
                                            'name' => $regionSelectedSlug);
        $listEvent['selectedPeriod']['startDate'] = $monthReturn;
        
        foreach($listPeriodeRegions as $plante => $data) {
            $regionSemi = array_keys($data['debut_semi'], $nextMonth);
            $regionPlant = array_keys($data['debut_plant'], $nextMonth);
            $regionRecolte = array_keys($data['debut_recolte'], $nextMonth);

            $arrayPlant = array('id' => $data['id'],
                               'name' => $plante);

            if($regionSemi) {
                $listEvent['semis'][] = $arrayPlant;
            }

            if($regionPlant) {
                $listEvent['plantation'][] = $arrayPlant;
            }

            if($regionRecolte) {
                $listEvent['recolte'][] = $arrayPlant;
            }
        }
        return $listEvent;
       
    }



}