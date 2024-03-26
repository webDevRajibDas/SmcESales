<?php
App::uses('AppModel', 'Model');
/**
 * ProductPrice Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Institute $Institute
 */
class DistSrProductPrice extends AppModel {

	public function filter($params, $conditions) {
		$conditions = array();
		
		if (!empty($params['Product.product_category_id'])) {
            $conditions[] = array('DistSrProductPrice.id' => $params['Product.product_category_id']);
        }
		if (!empty($params['DistSrProductPrice.institute_id'])) {
            $conditions[] = array('DistSrProductPrice.institute_id' => $params['DistSrProductPrice.institute_id']);
        }
		if (!empty($params['DistSrProductPrice.target_custommer'])) {
            $conditions[] = array('DistSrProductPrice.target_custommer' => $params['DistSrProductPrice.target_custommer']);
        }
        return $conditions;
    }
	
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'product_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Product is required'
			)
		),
		'target_custommer' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Target Customer is required'
			)
		),
		'measurement_unit_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Measurement Unit is required'
			)
		),
		'institute_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Institute is required'
			)
		),
		'effective_date' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Effective Date is required'
			)
		),
		'general_price' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'General Price is required'
			)
		),
		'end_date' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'End Date is required'
			)
		),
		'name' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Name is required'
			)
		)
		
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'measurement_unit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Institute' => array(
			'className' => 'Institute',
			'foreignKey' => 'institute_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public $hasMany = array(
		'DistSrProductCombination' => array(
			'className' => 'DistSrProductCombination',
			'foreignKey' => 'product_price_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		),
		'OutletNgoPrice' => array(
			'className' => 'OutletNgoPrice',
			'foreignKey' => 'product_price_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	// data filter
	
}
