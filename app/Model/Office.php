<?php
App::uses('AppModel', 'Model');
/**
 * Office Model
 *
 * @property OfficeType $OfficeType
 * @property ParentOffice $ParentOffice
 * @property OfficeHead $OfficeHead
 * @property OfficePerson $OfficePerson
 * @property SalesPerson $SalesPerson
 * @property Territory $Territory
 */
class Office extends AppModel {

	
	public $displayField = 'office_name';
	
	public $actsAs = array('Tree');
	
	
	/*============== validation==============*/
	public $validate = array(
			'office_name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Office Name field is required.'
				),
				'isUnique' => array(
							'rule' => 'isUnique',
							'message'=> 'Office name already exists.'
				)
			),
			'office_type_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Office Type field is required.'
				)
			)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'OfficeType' => array(
			'className' => 'OfficeType',
			'foreignKey' => 'office_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ParentOffice' => array(
			'className' => 'Office',
			'foreignKey' => 'parent_office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	/* 	'OfficeHead' => array(
			'className' => 'OfficeHead',
			'foreignKey' => 'office_head_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		) */
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ChildOffice' => array(
			'className' => 'Office',
			'foreignKey' => 'parent_office_id',
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
		'OfficePerson' => array(
			'className' => 'OfficePerson',
			'foreignKey' => 'office_id',
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
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'office_id',
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
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'office_id',
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
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'office_id',
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
		'SaleTarget' => array(
			'className' => 'SaleTarget',
			'foreignKey' => 'aso_id',
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
	);

}
