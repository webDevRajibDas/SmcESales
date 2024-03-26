<?php
App::uses('AppModel', 'Model');
/**
 * ReportEsalesSetting Model
 *
 * @property Esales $Esales
 */
class ReportEsalesSetting extends AppModel {

	//public $displayField = 'min_qty';
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'name' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Title is required.'
			)
			/*'isUnique' => array(
					'rule' => 'isUnique',
					'message'=> 'Title already exists.'
			)*/
		),
		
		'type' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			)
		),
		
		/*'range_start' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Value must be numeric.'
			)
		),
		
		
		'operator_1' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			)
		),*/
		
		
		/*'range_end' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Value must be numeric.'
			)
		)*/
		
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	/*public $belongsTo = array(
		'Esales' => array(
			'className' => 'Esales',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);*/

	// data filter
	/*public function filter($params, $conditions) {
		if (!empty($params['ReportEsalesSetting.product_id'])) {
            $conditions = array('ReportEsalesSetting.product_id' => $params['ReportEsalesSetting.product_id']);
        }
        return $conditions;
    }*/

}
