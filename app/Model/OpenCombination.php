<?php
App::uses('AppModel', 'Model');
/**
 * OpenCombination Model
 *
 * @property Product $Product
 */
class OpenCombination extends AppModel {
	
public $actsAs = array('Containable');

public $displayField = 'min_qty';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'name' => array(
			'name' => array(
				'rule'    => 'notEmpty',
				'message' => 'Name Field is required.'
			),
			'isUnique' => array(
					'rule' => 'isUnique',
					'message'=> 'Name already exists.'
			)
		),
		'description' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Description Field is required.'
			)
		),
		'start_date' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Start Date Field is required.'
			)
		),
		'end_date' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Start Date Field is required.'
			)
		)
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		/*'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)*/
	);
	
	public $hasMany = array(
		'OpenCombinationProduct' => array(
			'className' => 'OpenCombinationProduct',
			'foreignKey' => 'combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
            'dependent' => true
		)
	);
	
	// data filter
	public function filter($params, $conditions) {
		if (!empty($params['OpenCombination.product_id'])) {
            $conditions = array('OpenCombination.product_id' => $params['OpenCombination.product_id']);
        }
        return $conditions;
    }

}
