<?php
App::uses('AppModel', 'Model');
/**
 * Bonus Model
 *
 * @property SalesPerson $SalesPerson
 */
class Bonus extends AppModel {
	
	
	public $validate = array(
		'mother_product_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Mother product field is required.'
			),
			/*'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Product already exists.'
			)*/
		),
		'mother_product_quantity' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Mother product quantity field is required.'
			)
		),
		
		
		'bonus_product_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Bonus product field is required.'
			),
			'identicalFieldValues' => array(
				'rule' => array('identicalFieldValues', 'mother_product_id'),
				'message' => 'Bonus product must be same as Mother product.'
			)
		),
		
		
		
		'bonus_product_quantity' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Bonus product quantity field is required.'
			)
		),
		'effective_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Start Date field is required.'
			)
		),
		'end_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Start Date field is required.'
			)
		)
	);
	
	
	//Bonus Product Check
	function identicalFieldValues( $field=array(), $compare_field=null )  
    { 
        foreach( $field as $key => $value ){ 
            $v1 = $value; 
            $v2 = $this->data[$this->name][ $compare_field ];                  
            if($v1 !== $v2) { 
                return FALSE; 
            } else { 
                continue; 
            } 
        } 
        return TRUE; 
    } 

	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'MotherProduct' => array(
			'className' => 'Product',
			'foreignKey' => 'mother_product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'BonusProduct' => array(
			'className' => 'Product',
			'foreignKey' => 'bonus_product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
