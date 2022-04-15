<?php

namespace monPotager;

class MetaPeriod
{
    const calendrier = [
        'none'      => '',
        'Janvier'   => '-01-01',
        'Février'   => '-02-01',
        'Mars'      => '-03-01',
        'Avril'     => '-04-01',
        'Mai'       => '-05-01',
        'Juin'      => '-06-01',
        'Juillet'   => '-07-01',
        'Aout'      => '-08-01',
        'Septembre' => '-09-01',
        'Octobre'   => '-10-01',
        'Novembre'  => '-11-01',
        'Décembre'  => '-12-01'
    ];

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
   
    public function metaboxesloadSemi()
    {
        add_meta_box('regions', 'Periode de culture ', [$this, 'loadRegions'], 'plante', 'normal');
    }

    public function loadRegions($post)
    {   
        // Nouveau tableau vide
        $newCalendar = [];

        // Boucle sur le tableau calendrier
        foreach(self::calendrier as $month => $monthValue) { 
            
            //  Formate une date, l'année actuelle
            $year = date('Y'); 

            // Concaténation de l'année générée et 
            // de la date provenant du tableau
            $newDate = $year . $monthValue; 
            if($month === 'none') {
                $newDate = '';
            }

            // Remplissage du tableau en gardant la 
            // stucture que celui précédent
            $newCalendar[$month] = $newDate; 
        }
            
        foreach (self::regions as $region => $value) { // Boucle sur le tableau regions

            // *************** START SEMIS ****************** //

            // Récupère la valeur de notre champ 
            $valueMonthBeginsSemis = get_post_meta($post->ID, 'debut_semi' . $value, true); 

            echo "<div style='border:solid 2px #c3c4c7; margin-bottom: 1rem;padding:0.5rem;'>";
            echo "<h2>$region :</h2>";
            echo '<label for="dispo_meta">Indiquez la periode de semis - Début : </label>';

            echo '<select name="start_semi' . $value . '">'; // Elément <select>
            foreach ($newCalendar as $month => $TabMonthValue) { // Boucle sur le tableau du calendrier 'dynamique'

                echo '<option' . selected($TabMonthValue, $valueMonthBeginsSemis) . ' value="' . $TabMonthValue . '" >' . $month . '</option>';
            }
            echo '</select>';

            // *************** END SEMIS ****************** //
            $valueMonthEndsSemis = get_post_meta($post->ID, 'fin_semi' . $value, true);

            echo '<label for="dispo_meta"> Fin : </label>';
            echo '<select name="end_semi' . $value . '">';

            foreach ($newCalendar as $month => $TabValue) {
                echo '<option' . selected($TabValue, $valueMonthEndsSemis) . ' value="' . $TabValue . '">' . $month . '</option>';
            }
            echo '</select><br>';


            // *************** START PLANTATION ******************* //
            //*******************************************************/

            $valueMonthBeginsPlants = get_post_meta($post->ID, 'debut_plant' . $value, true);

            echo '<label for="dispo_meta">Indiquez la periode de plantation - Début : </label>';
            echo '<select name="start_plant' . $value . '">';

            foreach ($newCalendar as $month => $TabValue) {
                echo '<option' . selected($TabValue, $valueMonthBeginsPlants) . ' value="' . $TabValue . '">' . $month . '</option>';
            }

            echo '</select>';


            // *************** END PLANTATION *************** //
            $valueMonthEndsPlants = get_post_meta($post->ID, 'fin_plant' . $value, true);

            echo '<label for="dispo_meta"> Fin : </label>';
            echo '<select name="end_plant' . $value . '">';

            foreach ($newCalendar as $month => $TabValue) {
                echo '<option' . selected($TabValue, $valueMonthEndsPlants) . ' value="' . $TabValue . '">' . $month . '</option>';
            }
            echo '</select><br>';



            // *********** START HARVEST *************** //
            //********************************************/

            $valueMonthBeginsHarvest = get_post_meta($post->ID, 'debut_recolte' . $value, true);

            echo '<label for="dispo_meta">Indiquez la periode de récolte - Début : </label>';
            echo '<select name="start_harvest'.$value.'">';

            foreach ($newCalendar as $month => $TabValue) {
                echo '<option ' . selected($TabValue, $valueMonthBeginsHarvest) . ' value="' . $TabValue . '">' . $month . '</option>';
            }

            echo '</select>';


            // *********** END HARVEST *************** //
            $valueMonthEndsHarveset = get_post_meta($post->ID, 'fin_recolte' . $value, true);

            echo '<label for="dispo_meta"> Fin : </label>';
            echo '<select name="end_harvest' .$value. '">';

            foreach ($newCalendar as $month => $TabValue) {
                echo '<option' . selected($TabValue, $valueMonthEndsHarveset) . ' value="' . $TabValue . '" >' . $month . '</option>';
            }
            echo '</select></div>';
        }
    }

    public function save_metaboxe($post_ID)
    {
        $newCalendar = [];

        foreach(self::calendrier as $month => $monthValue) {
            $year = date('Y');
            $newDate = $year . $monthValue;
            if($month === 'none') {
                $newDate = '';
            }

            $newCalendar[$month] = $newDate;
        }

        $cultureList = [
            'start_semi' => 'debut_semi',
            'end_semi' => 'fin_semi',
    
            'start_plant' => 'debut_plant',
            'end_plant' => 'fin_plant',
    
            'start_harvest' => 'debut_recolte',
            'end_harvest' => 'fin_recolte'
        ];
    
        // Boucle sur le tableau des régions
        foreach (self::regions as $region => $value) { 
            // Boucle sur le tableau des periodes des étapes de cultures
            foreach($cultureList as $cultureTypePost => $cultureTypeMeta) 
            {
                // Vérifie si la variable est bien déclarée
                if (isset($_POST[$cultureTypePost . $value])) {  
                
                    // On récupère la date stocké dans la variable POST de la méthode HTTP
                    $dataSelected = $_POST[$cultureTypePost . $value]; 

                    // Récupère le mois associé en comparant les valeurs du calendrier et celle récupérée
                    $month = array_search($dataSelected, $newCalendar);

                    if($month === 'none') {
                        // Supprime le champ 'meta'
                        delete_post_meta($post_ID, $cultureTypeMeta . $value,);
                    } else {
                        // Créer ou modifier les champs du post enregistrés en tant que 'meta'
                        update_post_meta($post_ID, $cultureTypeMeta . $value, esc_html($dataSelected)); 
        }}}}
    }
}
