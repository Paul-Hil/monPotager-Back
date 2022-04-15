<?php

namespace monPotager;

class MetaPeriod
{
    const calendrier = [
        'none'      => '',
        'Janvier'   => '2021-01-01',
        'Février'   => '2021-02-01',
        'Mars'      => '2021-03-01',
        'Avril'     => '2021-04-01',
        'Mai'       => '2021-05-01',
        'Juin'      => '2021-06-01',
        'Juillet'   => '2021-07-01',
        'Aout'      => '2021-08-01',
        'Septembre' => '2021-09-01',
        'Octobre'   => '2021-10-01',
        'Novembre'  => '2021-11-01',
        'Décembre'  => '2021-12-01'
    ];

    const colors = [
        'Légume'   => '#067106',
        'Fruit'    => '#0E5671',
        'Arôme'    => '#719C0F'
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
        $newCalendar = [];
        foreach(self::calendrier as $month => $value) {
            $years = date('Y');
            $newDate = substr_replace($value, $years, 0, 4);
            if($month === 'none') {
                $newDate = '';
            }
            $newCalendar[$month] = $newDate;
        }

        //var_dump($newCalendar);exit;

        foreach (self::regions as $region => $value) {
            // *************** START SEMIS ****************** //
            //*************************************************/

            $valueMonthBeginsSemis = get_post_meta($post->ID, 'debut_semi' . $value, true);

            echo "<div style='border:solid 2px #c3c4c7; margin-bottom: 1rem;padding:0.5rem;'>";
            echo "<h2>$region :</h2>";
            echo '<label for="dispo_meta">Indiquez la periode de semis - Début : </label>';
            echo '<select name="start_semi' . $value . '">';

            foreach ($newCalendar as $month => $TabValue) {
                echo '<option' . selected($TabValue, $valueMonthBeginsSemis) . ' value="' . $TabValue . '" >' . $month . '</option>';
            }
            echo '</select>';

            // *************** END SEMIS ****************** //
            $valueMonthEndsSemis = get_post_meta($post->ID, 'fin_semi' . $value, true);

            echo '<label for="dispo_meta"> Fin : </label>';
            echo '<select name="end_semi' . $value . '">';

            foreach (self::calendrier as $month => $TabValue) {
                echo '<option' . selected($TabValue, $valueMonthEndsSemis) . ' value="' . $TabValue . '">' . $month . '</option>';
            }
            echo '</select><br>';


            // *************** START PLANTATION ******************* //
            //*******************************************************/

            $valueMonthBeginsPlants = get_post_meta($post->ID, 'debut_plant' . $value, true);

            echo '<label for="dispo_meta">Indiquez la periode de plantation - Début : </label>';
            echo '<select name="start_plant' . $value . '">';

            foreach (self::calendrier as $month => $TabValue) {
                echo '<option' . selected($TabValue, $valueMonthBeginsPlants) . ' value="' . $TabValue . '">' . $month . '</option>';
            }

            echo '</select>';


            // *************** END PLANTATION *************** //
            $valueMonthEndsPlants = get_post_meta($post->ID, 'fin_plant' . $value, true);

            echo '<label for="dispo_meta"> Fin : </label>';
            echo '<select name="end_plant' . $value . '">';

            foreach (self::calendrier as $month => $TabValue) {
                echo '<option' . selected($TabValue, $valueMonthEndsPlants) . ' value="' . $TabValue . '">' . $month . '</option>';
            }
            echo '</select><br>';



            // *********** START HARVEST *************** //
            //********************************************/

            $valueMonthBeginsHarvest = get_post_meta($post->ID, 'debut_recolte' . $value, true);

            echo '<label for="dispo_meta">Indiquez la periode de récolte - Début : </label>';
            echo '<select name="start_harvest'.$value.'">';

            foreach (self::calendrier as $month => $TabValue) {
                echo '<option ' . selected($TabValue, $valueMonthBeginsHarvest) . ' value="' . $TabValue . '">' . $month . '</option>';
            }

            echo '</select>';


            // *********** END HARVEST *************** //
            $valueMonthEndsHarveset = get_post_meta($post->ID, 'fin_recolte' . $value, true);

            echo '<label for="dispo_meta"> Fin : </label>';
            echo '<select name="end_harvest' .$value. '">';

            foreach (self::calendrier as $month => $TabValue) {
                echo '<option' . selected($TabValue, $valueMonthEndsHarveset) . ' value="' . $TabValue . '" >' . $month . '</option>';
            }
            echo '</select></div>';
        }
    }

    public function save_metaboxe($post_ID)
    {
        foreach (self::regions as $region => $value) {

            // *************** START SEMIS ****************** //
            //*************************************************/

            if (isset($_POST['start_semi' . $value])) {
                update_post_meta($post_ID, 'debut_semi' . $value, esc_html($_POST['start_semi' . $value]));
                $date = $_POST['start_semi' . $value];
                $month = array_search($date, self::calendrier);
                if($month === 'none') {
                    delete_post_meta($post_ID, 'debut_semi-month' . $value,);
                } else {
                    update_post_meta($post_ID, 'debut_semi-month' . $value, esc_html($month));
                }
            }

            if (isset($_POST['end_semi' . $value])) {
                update_post_meta($post_ID, 'fin_semi' . $value, esc_html($_POST['end_semi' . $value]));
                $date = $_POST['end_semi' . $value];
                $month = array_search($date, self::calendrier);
                if($month === 'none') {
                    delete_post_meta($post_ID, 'fin_semi-month' . $value,);
                } else {
                    update_post_meta($post_ID, 'fin_semi-month' . $value, esc_html($month));
                }
            }


            // *************** START PLANTATION ************** //
            //*************************************************/
            
            if (isset($_POST['start_plant' . $value])) {
                update_post_meta($post_ID, 'debut_plant' . $value, esc_html($_POST['start_plant' . $value]));
                $date = $_POST['start_plant' . $value];
                $month = array_search($date, self::calendrier);
                if($month === 'none') {
                    delete_post_meta($post_ID, 'debut_plant-month' . $value,);
                } else {
                    update_post_meta($post_ID, 'debut_plant-month' . $value, esc_html($month));
                }
            }

            if (isset($_POST['end_plant' . $value])) {
                update_post_meta($post_ID, 'fin_plant' . $value, esc_html($_POST['end_plant' . $value]));
                $date = $_POST['end_plant' . $value];
                $month = array_search($date, self::calendrier);
                if($month === 'none') {
                    delete_post_meta($post_ID, 'fin_plant-month' . $value,);
                } else {
                    update_post_meta($post_ID, 'fin_plant-month' . $value, esc_html($month));
                }
            }

            // *************** START HARVEST ****************** //
            //*************************************************/

            if (isset($_POST['start_harvest' .$value])) {
                update_post_meta($post_ID, 'debut_recolte'.$value, esc_html($_POST['start_harvest' .$value]));
                $date = $_POST['end_plant' . $value];
                $month = array_search($date, self::calendrier);
                if($month === 'none') {
                    delete_post_meta($post_ID, 'debut_recolte-month' . $value,);
                } else {
                    update_post_meta($post_ID, 'debut_recolte-month' . $value, esc_html($month));
                }
            }

            if (isset($_POST['end_harvest'.$value])) {
                update_post_meta($post_ID, 'fin_recolte'.$value, esc_html($_POST['end_harvest' .$value]));
                $date = $_POST['end_harvest' . $value];
                $month = array_search($date, self::calendrier);
                if($month === 'none') {
                    delete_post_meta($post_ID, 'fin_recolte-month' . $value,);
                } else {
                    update_post_meta($post_ID, 'fin_recolte-month' . $value, esc_html($month));
                }
            }
        }
    }
}
