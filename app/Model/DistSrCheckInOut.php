<?php
App::uses('AppModel', 'Model');
/**
 * DistSrCheckInOut Model
 *
 * @property Office $Office
 * @property Db $Db
 * @property Sr $Sr
 */
class DistSrCheckInOut extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'dist_sr_check_in_out';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DistDistributor' => array(
			'className' => 'DistDistributor',
			'foreignKey' => 'db_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DistSalesRepresentative' => array(
			'className' => 'DistSalesRepresentative',
			'foreignKey' => 'sr_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
