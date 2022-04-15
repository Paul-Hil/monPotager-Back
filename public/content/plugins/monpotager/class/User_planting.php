<?php

namespace monPotager;

class User_planting
{

    public function user_Metaboxes_Planting()
    {

        add_meta_box('id_planting', 'Calendrier de la plante', [$this, 'semi_days_planting'], 'plante', 'side');
    }

    public function semi_days_planting($post)
    {
        $valueSemi = get_post_meta($post->ID, 'nb_jours_semi_plantation', true);
        $valuePlanting = get_post_meta($post->ID, 'nb_jours_plantation', true);
        $valueHarvest = get_post_meta($post->ID, 'nb_jours_recolte', true);


        echo '<label for="days_planting_semi">Nombres de jours semi plantation : </label>';
        echo '<input id="days_planting_semi" type="text" name="days_planting_semi" value="' . $valueSemi . '" />';
        echo '<br>'; 
        echo '<label for="days_planting">Nombres de jours pour une plantation : </label>';
        echo '<input id="days_planting" type="text" name="days_planting" value="' . $valuePlanting . '" />'; 
        echo '<br>'; 
        echo '<label for="days_harvest">Nombres de jours pour une récolte : </label>';
        echo '<input id="days_harvest" type="text" name="days_harvest" value="' . $valueHarvest . '" />';
        
    }

    public function saveUserMetaboxesDaysPlantation($post_ID)
    {

        if (isset($_POST['days_planting_semi'])&& $_POST['days_planting_semi'] !=='') {
            update_post_meta($post_ID, 'nb_jours_semi_plantation', esc_html($_POST['days_planting_semi']));
        } else {
            
            delete_post_meta($post_ID, 'nb_jours_semi_plantation');
            
        }


        if (isset($_POST['days_planting'])&& $_POST['days_planting'] !=='') {
            update_post_meta($post_ID, 'nb_jours_plantation', esc_html($_POST['days_planting']));
        } else {
            
            delete_post_meta($post_ID, 'nb_jours_plantation');
            
        }


        if (isset($_POST['days_harvest'])&& $_POST['days_harvest'] !=='') {
            update_post_meta($post_ID, 'nb_jours_recolte', esc_html($_POST['days_harvest']));
        } else {
            
            delete_post_meta($post_ID, 'nb_jours_recolte');
        }
    }


}