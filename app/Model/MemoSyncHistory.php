<?php
App::uses('AppModel', 'Model');
/**
 * MemoSyncHistory Model
 *
 * @property SalesPerson $SalesPerson
 */
class MemoSyncHistory extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';
	
	
	/*public $belongsTo = array(		
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'so_id',
			'conditions' => '',
			'fields' => 'name,office_id',
			'order' => '',
		)
		
	);*/

}
