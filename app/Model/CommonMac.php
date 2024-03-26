<?php
App::uses('AppModel', 'Model');
/**
 * CommonMac Model
 *
 */
class CommonMac extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'common_macs';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'mac_id';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'mac_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
}
