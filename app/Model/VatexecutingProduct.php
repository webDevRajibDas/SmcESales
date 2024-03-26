<?php
App::uses('AppModel', 'Model');
/**
 * ProductPrice Model
 *
 */
class VatexecutingProduct extends AppModel {

    public $validate = array(
		'product_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Product is required'
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
		'ProductType' => array(
			'className' => 'ProductType',
			'foreignKey' => 'product_type',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
}

?>