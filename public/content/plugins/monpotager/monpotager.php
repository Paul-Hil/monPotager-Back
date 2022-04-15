<?php

/**
 * Plugin Name: monPotager
 */

use monPotager\Plugin;
use monPotager\Api;

require __DIR__ . '/vendor-monpotager/autoload.php';

$monPotager = new Plugin();

$api = new Api();

register_activation_hook(
   __FILE__,
   [$monPotager, 'activate']
);


register_deactivation_hook(
   __FILE__,
   [$monPotager, 'deactivate']
);

add_filter('rest_user_query', 'remove_has_published_posts_from_api_user_query', 10, 1); // Hook / Callback / Priority / Accepted arguments

add_filter( 'excerpt_length', 'ExcerptLength', 50);

add_filter('excerpt_more', 'ExcerptMore', 50);

remove_filter('the_content','wpautop');

remove_filter('the_excerpt','wpautop');

function ExcerptLength($length){
   return 31 ;
}

function ExcerptMore ($more) {
   return '[...]';
};

function remove_has_published_posts_from_api_user_query($prepared_args)
{
    unset($prepared_args['has_published_posts']);

    return $prepared_args;
}


