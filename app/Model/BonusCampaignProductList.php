<?php
App::uses('AppModel', 'Model');
/**
 * BonusCampaignProductList Model
 */
class BonusCampaignProductList extends AppModel {
	
	public $displayField = 'id';
	
	/* public $validate = array(
		'name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Bank Name field is required.'
			),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Bank Name already exist.'
			),
		)
	); */

}
