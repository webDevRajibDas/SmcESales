<?php
App::uses('AppModel', 'Model');
/**
 * Memo Model
 *
 */
class DepositEditablePermission extends AppModel {

/**
 * Display field
 *
 * @var string
 */
    
    
    // data filter
    public function filter($params, $conditions) {  
        $conditions = array();
        return $conditions;
    }

    public $belongsTo = array(		
		'Deposit' => array(
			'className' => 'Deposit',
			'foreignKey' => 'deposit_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
	);

}
