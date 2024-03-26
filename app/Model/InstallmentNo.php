<?php
App::uses('AppModel', 'Model');
/**
 * InstallmentNo Model
 *
 * @property InstallmentNo $InstallmentNo
 * @property Payment $Payment
 * @property So $So
 * @property InstallmentNo $InstallmentNo
 */
class InstallmentNo extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'installment_no';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'installment_no_id';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'InstallmentNo' => array(
			'className' => 'InstallmentNo',
			'foreignKey' => 'installment_no_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Payment' => array(
			'className' => 'Payment',
			'foreignKey' => 'payment_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'So' => array(
			'className' => 'So',
			'foreignKey' => 'so_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'InstallmentNo' => array(
			'className' => 'InstallmentNo',
			'foreignKey' => 'installment_no_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
