<?php
App::uses('AppModel', 'Model');
App::uses('UserModel', 'Model');
/**
 * SalesPerson Model
 *
 * @property Designation $Designation
 * @property SalesPerson $ParentSalesPerson
 * @property Office $Office
 * @property MarketPerson $MarketPerson
 * @property OfficePerson $OfficePerson
 * @property SalesPerson $ChildSalesPerson
 * @property TerritoryPerson $TerritoryPerson
 */
class SalesPerson extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	
	// data filter
	/* public function filter($params, $conditions) {
        if (!empty($params['SalesPerson.name'])) {
            $conditions = array('SalesPerson.name' => $params['SalesPerson.name']);
        }
		if (!empty($params['SalesPerson.code'])) {
            $conditions = array('SalesPerson.code' => $params['SalesPerson.code']);
        }
		if (!empty($params['SalesPerson.designation_id'])) {
            $conditions = array('SalesPerson.designation_id' => $params['SalesPerson.designation_id']);
        }
		if (!empty($params['SalesPerson.office_id'])) {
            $conditions = array('SalesPerson.office_id' => $params['SalesPerson.office_id']);
        }
        return $conditions;
    } */
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	
	public $validate = array(
		'name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Name field is required.'
					)
		),
		'designation_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Designation field is required.'
					)
		),
		'office_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Office field is required.'
					),
			'unique' => array(
						'rule' => array('checkUnique'),
						'message' => 'ASO already exist on this office.',
					)
		)
	); 
	
	
	function checkUnique($data, $fields)
	{		
		return true;
		// comment for disable duplicate ASO check
		/* if($this->data[$this->name]['user_group_id']==3)
		{
			$user = new User();
			$user_group_id = $this->data[$this->name]['user_group_id'];
			if(isset($this->data['SalesPerson']['id'])>0)
			{
				$conditions = array(
					'User.user_group_id' => $user_group_id,
					'User.active' => 1,
					'SalesPerson.office_id' => $this->data['SalesPerson']['office_id'],
					'SalesPerson.id !=' => $this->data['SalesPerson']['id']
				);
			}else{
				$conditions = array(
					'User.user_group_id' => $user_group_id,
					'User.active' => 1,
					'SalesPerson.office_id' => $this->data['SalesPerson']['office_id']
				);
			}	
			$count_user = $user->find('count',array(
				'conditions' => $conditions,
				'recursive'=> 0
			));
			if($count_user>0)
			{
				return false;
			}
			return true;
		}else{
			return true;
		} */
	}
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Designation' => array(
			'className' => 'Designation',
			'foreignKey' => 'designation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	public $hasOne = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'sales_person_id',
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
	
/**
 * hasMany associations
 *
 * @var array
 */
/* 	public $hasMany = array(
		'MarketPerson' => array(
			'className' => 'MarketPerson',
			'foreignKey' => 'sales_person_id',
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
			'foreignKey' => 'sales_person_id',
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
		'ChildSalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'parent_id',
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
		'TerritoryPerson' => array(
			'className' => 'TerritoryPerson',
			'foreignKey' => 'sales_person_id',
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
	*/

}
