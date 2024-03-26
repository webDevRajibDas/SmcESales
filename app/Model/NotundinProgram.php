<?php
App::uses('AppModel', 'Model');
/**
 * NotundinProgram Model
 *

 */
class NotundinProgram extends AppModel {



/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	// data filter
	public function filter($params, $conditions) {
        $conditions = array();
		//pr($params);
		if (!empty($params['NotundinProgram.institute_name'])) {
            $conditions[] = array('Institute.name LIKE' => '%'.$params['NotundinProgram.institute_name'].'%');
        }
		if (!empty($params['NotundinProgram.status'])) {
            $conditions[] = array('NotundinProgram.status' => $params['NotundinProgram.status']);
        }			       				
        return $conditions;
    }

	public $validate = array(
			'name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Name field is required.'
				),
				'isUnique' => array(
							'rule' => 'isUnique',
							'message'=> 'Name already exists.'
				)
			)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Institute' => array(
			'className' => 'Institute',
			'foreignKey' => 'institute_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
			
	);
}
