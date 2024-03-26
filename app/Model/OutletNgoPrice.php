<?php
App::uses('AppModel', 'Model');
/**
 * OutletNgoPrice Model
 *
 */
class OutletNgoPrice extends AppModel {


/**
 * belongsTo associations
 *
 * @var array
 */
	
	public $belongsTo = array(
		'ProductPrice' => array(
			'className' => 'ProductPrice',
			'foreignKey' => 'product_price_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	/* public $belongsTo = array(
		'ProductPrice' => array(
			'className' => 'ProductPrice',
			'foreignKey' => 'product_price_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	); */
	
}
