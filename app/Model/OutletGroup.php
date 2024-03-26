<?php
App::uses('AppModel', 'Model');
/**
 * OutletGroup Model
 *
 * @property Product $Product
 */
class OutletGroup extends AppModel {

public $displayField = 'name';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'name' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			),
			'isUnique' => array(
					'rule' => 'isUnique',
					'message'=> 'Name already exists.'
			)
		),
		

	);
/**
 * belongsTo associations
 *
 * @var array
 */
	/*public $belongsTo = array(
		'OutletGroupToOutlet' => array(
			'className' => 'OutletGroupToOutlet',
			'foreignKey' => 'outlet_group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);*/
	
	public $hasMany = array(
		'OutletGroupToOutlet' => array(
			'className' => 'OutletGroupToOutlet',
			'foreignKey' => 'outlet_group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	// data filter
	public function filter($params, $conditions) {
		if (!empty($params['OutletGroup.product_id'])) {
            $conditions = array('OutletGroup.product_id' => $params['OutletGroup.product_id']);
        }
        return $conditions;
    }

}
