<?php
/**
 * Class monpotagerTest
 *
 * @package Monpotager
 */

use monPotager\Api;

/**
 * Test de l'ajout d'un utilisateur
 */
class monpotagerTest extends WP_UnitTestCase {

	public function test_userCreate()
    {
		// Création de l'utilisateur
        $userId = wp_create_user(
            'testUser',
            'testUser',
            'testUser@testUser.com'
        );

		// Objet de l'utilisateur créer
		$user = new WP_User($userId);

		// Suppression du rôle
		$user->remove_role('subscriber');
		// Ajoût du rôle
		$user->add_role('gardener');

        if(is_int($userId) && (in_array('gardener', (array) $user->caps))){
            $result = true;
        } else {
            $result = false;
        }

        $this->assertTrue($result);
    }

	public function test_userCreateFail()
    {
        wp_create_user(
            'kezak',
            'kezak',
            'kezak@kezak.com'
        );

        // création du second user ; wp doit ABSOLUMENT ne pas créer cet utilisateur (même email et même login)
        $userId2 = wp_create_user(
            'kezak',
            'kezak',
            'kezak@kezak.com'
        );
		
        if(is_int($userId2)){
            $result = false;
        } else {
            $result = true;
        }

        $this->assertTrue($result);
    }

	public function test_planteCreate() {

		$postId = wp_insert_post(array(
			'post_title'=>'randomPlante', 
			'post_type'=>'plante', 
			'post_content'=>'demo content'
		  ));

		  if(is_int($postId)){
            $result = true;
        } else {
            $result = false;
        }

        $this->assertTrue($result);
	}

	public function test_createTerm_Region() {

		$termRegion = wp_insert_term("randomRegion", "regions");

		if(is_int($termRegion['term_id'])){
            $result = true;
        } else {
            $result = false;
        }

        $this->assertTrue($result);
	}


	public function test_createTermFailed_Region() {

		wp_insert_term("randomRegion", "regions");
		// Le second terme génére un objet de type 'Wp_error', car il est déjà éxistant
		$termRegion = wp_insert_term("randomRegion", "regions");

		if(!is_object($termRegion)){
            $result = false;
        } else {
            $result = true;
        }

        $this->assertTrue($result);
	}

	/**
	 * Test l'insertion d'un nouveau type de plante  
	 */
	public function test_createTerm_PlanteType() {

		$termPlanteType = wp_insert_term("randomPlante_Type", "plante_type");

		if(is_int($termPlanteType['term_id'])){
            $result = true;
        } else {
            $result = false;
        }

        $this->assertTrue($result);
	}

	/**
	 * Test si Wp génère bien une erreur à l'insertion d'un doublon
	 */
	public function test_createTermFailed_PlanteType() {

		wp_insert_term("randomPlante_Type", "plante_type");

		// Le second terme génére un objet de type 'Wp_error', car il est déjà existant
		$termPlanteType = wp_insert_term("randomPlante_Type", "plante_type");

		if(is_object($termPlanteType)){
            $result = true;
        } else {
            $result = false;
        }
        $this->assertTrue($result);
	}
}

