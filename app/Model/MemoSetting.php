<?php
App::uses('AppModel', 'Model');
/**
 * MemoSetting Model
 *
 * @property Memo $Memo
 */
class MemoSetting extends AppModel {

public $displayField = 'min_qty';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'name' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Memo Field is required.'
			),
			'isUnique' => array(
					'rule' => 'isUnique',
					'message'=> 'Name already exists.'
			)
		),
		
		
	);





}
