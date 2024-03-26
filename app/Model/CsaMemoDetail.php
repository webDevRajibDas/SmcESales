<?php
App::uses('AppModel', 'Model');
/**
 * CsaMemo Model
 *
 */
class CsaMemoDetail extends AppModel {

/**
 * Display field
 *
 * @var string
 */

	public $validate = array(
		/* 'name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'District Name field is required.'
					),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'District Name already exist.'
					),
		) */
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		/* 'Thana' => array(
			'className' => 'Thana',
			'foreignKey' => 'district_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		) */
	);
	
/**
 * BelongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'CsaMemo' => array(
			'className' => 'CsaMemo',
			'foreignKey' => 'csa_memo_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'MeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'measurement_unit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	


}
