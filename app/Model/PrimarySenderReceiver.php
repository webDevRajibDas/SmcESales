<?php
App::uses('AppModel', 'Model');
/**
 * Client Model
 *
 * @property Month $Month
 */
class PrimarySenderReceiver extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	public $validate = array(
		'name' => array(
			'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message'=> ' Name field is required.'
			),
			
		)
		
	);
	
	
/**
 * belongsTo associations
 *
 * @var array
 */

}
