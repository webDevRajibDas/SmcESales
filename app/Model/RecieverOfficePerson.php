<?php
App::uses('AppModel', 'Model');
/**
 * RecieverOfficePerson Model
 *
 * @property Office $Office
 * @property SalesPerson $SalesPerson
 */
class RecieverOfficePerson extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'reciever_office_person';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	public $validate = array(
		'receive_type' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Receive type is required.'
					)
		),
		'office_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Office field is required.'
					)
		),
		'sales_person_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales person field is required.'
					),
			'unique' => array(
						'rule' => array('checkUnique', array('receive_type', 'office_id', 'sales_person_id')),
						'message' => 'Setting should be unique.',
					)		
		)
	);
	
	function checkUnique($data, $fields)
	{
		// check if the param contains multiple columns or a single one
		if (!is_array($fields))
		{
			$fields = array($fields);
		}
		 
		// go trough all columns and get their values from the parameters
		foreach($fields as $key)
		{
			$unique[$key] = $this->data[$this->name][$key];
		}
		 
		// primary key value must be different from the posted value
		if (isset($this->data[$this->name][$this->primaryKey]))
		{
			$unique[$this->primaryKey] = "<>" . $this->data[$this->name][$this->primaryKey];
		}
		 
		// use the model's isUnique function to check the unique rule
		return $this->isUnique($unique, false);
	}
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'sales_person_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
