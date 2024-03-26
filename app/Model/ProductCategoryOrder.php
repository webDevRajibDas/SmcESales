<?php
App::uses('AppModel', 'Model');
/**
 * Memo Model
 *
 */
class ProductCategoryOrder extends AppModel {

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
		/*'Memo' => array(
			'className' => 'Memo',
			'foreignKey' => 'memo_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),*/
	);

}
