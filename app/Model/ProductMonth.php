<?php
App::uses('AppModel', 'Model');
/**
 * ProductPrice Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Institute $Institute
 */
class ProductMonth extends AppModel {

    public $validate = array(
		'product_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Product is required'
			)
		),
		'day_month' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Month is required'
			)
		),
    );

	public $belongsTo = array(
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
}

?>