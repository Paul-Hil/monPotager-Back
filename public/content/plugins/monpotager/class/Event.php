<?php

namespace monPotager;

use WP_REST_Request;
use WP_Query;
use WP_Term_Query;

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
            '/evenement', // the endpoint that will be put after the name of the api
            [
                'methods' => 'post', // the method used
                'callback' => [$this, 'eventFunction']
            ]
        );
    }

    public function eventFunction(WP_REST_Request $request)
    {   
        // id de la région
        $RegionSelected = $request->get_param('idRegionSelected');
        $idRegionSelected = strval($RegionSelected);  

        // Vérifie si l'id à bien été reçu
        if(isset($idRegionSelected)) {

            // Récupère les régions enregistrées dans le back office
            $termsRegions = new WP_Term_Query( array( 
                'taxonomy'   => 'regions', // Nom de la taxonomie
                'hide_empty' => false, // Pour inclure toutes les régions
            ));

              // Boucle sur l'objet contenant les régions
              foreach($termsRegions->terms as $region) {

                // Test l'identifiant de la région récupérés avec ceux du BO 
                if ($region->term_id == $idRegionSelected) {
                    $regionSelectedName = $region->name; // Stock le nom de la région
                    $regionSelectedSlug = $region->slug; // et le slug
                }
            }
            if(!$regionSelectedName) {
                return 'Error: Identifiant de la région invalide';
            }
        } else {
            return 'Error: Identifiant de la région attendu';
        }
        
        // Récupère la liste des plantes enregistrés
        $postsPlante = new WP_Query(
            array(
                'post_type' => 'plante',
                'posts_per_page'=> -1, 
            )
        ); 
        
        // Boucle sur l'objet contenant la liste des plantes
        foreach ($postsPlante->posts as $plantes) {
            $planteId = $plantes->ID; // Stock l'id de la plante
            $planteTitle = $plantes->post_title; // Stock le nom de la plante

            // Récupère les metabox (les periodes) du post
            $periodeMetaBox = get_post_meta($planteId); 

            // Boucle sur le tableau des régions
            foreach (self::regions as $region => $regionFormate) { 

                // Vérifie la région en cours de trie avec celle selectionnée
                if ($regionSelectedName === $region) {

                    // Stocke la date des début de culture
                    $debut_semi = $periodeMetaBox['debut_semi' . $regionFormate]; 
                    $debut_plant = $periodeMetaBox['debut_plant' . $regionFormate];
                    $debut_recolte = $periodeMetaBox['debut_recolte' . $regionFormate];

                    // Filtre les date récupérées pour ne garder que le mois
                    $semis = substr($debut_semi[0], 5, 2); 
                    $plantations = substr($debut_plant[0], 5, 2);
                    $recoltes = substr($debut_recolte[0], 5, 2);

                    // Stocke l'id de la plante dans un tableau
                    $listOfPeriodsPerPlante[$planteTitle]['id'] = $planteId; 

                    // Stock les mois récupéré dans le tableau
                    $listOfPeriodsPerPlante[$planteTitle]['debut_semi'] = ($semis); 
                    $listOfPeriodsPerPlante[$planteTitle]['debut_plant'] = ($plantations);
                    $listOfPeriodsPerPlante[$planteTitle]['debut_recolte'] = ($recoltes);
                }
            }
        }

        // Création du tableau à retourner pour l'API
        $listEvents = [];

        // Stocke le détail sur la région séléctionnée
        $listEvents['selectedRegion'] = 
                [
                    'id' => $idRegionSelected,
                    'name' => $regionSelectedSlug
                ];
        
        // récupère le mois ciblé
        $nextMonth = self::generateMonth();

        // génère une date complète en chiffres
        $fullDate = date('Y-'.$nextMonth.'-d');
       
        // change les informations de localisations défini
        setlocale (LC_TIME, 'fr_FR.utf8', 'fra');

        // conversion de la date en un timestamp Unix
        // pour ne récupèrer que le mois en lettre
        $monthReturn = strftime("%B", strtotime($fullDate));

        // Stocke le mois dans le tableau
        $listEvents['selectedPeriod']['startDate'] = $monthReturn;
        
        // Boucle sur le tableau listant les débuts de periodes
        // des cultures par plante pour une région séléctionnée 
        foreach($listOfPeriodsPerPlante as $plante => $data) {

            // Test chaque mois des étapes de culture,
            // pour vérifier si elles correspond au mois ciblé
            $periode_semi = ($data['debut_semi'] === $nextMonth) ? $data['debut_semi'] : null;
            $periode_plantation = ($data['debut_plant'] === $nextMonth) ? $data['debut_plant'] : null;
            $periode_recolte = ($data['debut_recolte'] === $nextMonth) ? $data['debut_recolte'] : null;

            // Stocke les informations de la plante dans une variable
            $arrayPlant = array('id' => $data['id'], 'name' => $plante);

            // Si la periode a été récupérée, celle-ci correspond
            if($periode_semi) {
                // stocke alors la plante dans le tableau
                // avec l'étape de culture associé
                $listEvents['semis'][] = $arrayPlant;
            }
            if($periode_plantation) {
                $listEvents['plantation'][] = $arrayPlant;
            }
            if($periode_recolte) {
                $listEvents['recolte'][] = $arrayPlant;
            }
        }
        // Lorsque toutes les plantes sont passées,
        // Envoie du tableau en réponse à l'API
        return $listEvents;
    }


    /**
     * Génère le mois suivant sur 2 chiffres
     *
     * @return void
     */
    public static function generateMonth() {
        // génère le mois sur 2 chiffres
        $ActualMonth = date('m');

        // Si on est en décembre
        if($ActualMonth === '12') {
            $nextMonth = '01';
        } else { // Sinon
            // Incrémente d'un au mois généré
            $nextMonthInt = $ActualMonth + 1;
            // Convertis la variable en string
            $nextMonth = strval($nextMonthInt);  
        }
        return $nextMonth;
    }
}

