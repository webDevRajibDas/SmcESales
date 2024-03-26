<?php
App::uses('AppModel', 'Model');
/**
 * Memo Model
 *
 */
class CollectionEditablePermission extends AppModel {

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
		'Collection' => array(
			'className' => 'Collection',
			'foreignKey' => 'collection_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
	);

}
