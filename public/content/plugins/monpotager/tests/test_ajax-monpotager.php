<?php

use monPotager\Api;

class Test_monPotager extends WP_Ajax_UnitTestCase
{
    public $monPotager;

    public function setup()
    {
        parent::setup();
    
        require_once 'public/content/plugins/monpotager/class/Api.php';
        $this->monPotager = new Api();
    
        wp_set_current_user(1);
    }
}