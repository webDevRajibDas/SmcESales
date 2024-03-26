<?php
App::uses('AppModel', 'Model');
/**
 * UserDoctorVisitPlan Model
 *
 * @property FiscalYear $FiscalYear
 * @property User $User
 */
class UserDoctorVisitPlan extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'user_doctor_visit_plan';

/**
 * Validation rules
 *
 * @var array
 */
public function filter($params, $conditions) {
	// pr($params);exit;
		$conditions = array();
		if (!empty($params['UserDoctorVisitPlan.user_id'])) {
			$conditions[] = array('UserDoctorVisitPlan.user_id' => $params['UserDoctorVisitPlanList.user_id']);
		}
		if (!empty($params['UserDoctorVisitPlanList.territory_id'])) {
			$conditions[] = array('UserDoctorVisitPlanList.territory_id' => $params['UserDoctorVisitPlanList.territory_id']);
		}
		if (!empty($params['UserDoctorVisitPlanList.market_id'])) {
			$conditions[] = array('UserDoctorVisitPlanList.market_id' => $params['UserDoctorVisitPlanList.market_id']);
		}
		if (!empty($params['UserDoctorVisitPlanList.date'])) {
			$conditions[] = array('UserDoctorVisitPlanList.visited_date' => date('Y-m-d', strtotime($params['UserDoctorVisitPlanList.date'])));
		}
		if (!empty($params['UserDoctorVisitPlanList.visit_status'])) {
			$conditions[] = array('UserDoctorVisitPlanList.visit_status LIKE' => '%'.$params['UserDoctorVisitPlanList.visit_status'].'%');
		}

		return $conditions;
	}
	public $validate = array(
		'user_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		
		),
		'fiscal_year_id' => array(
			'unique' => array(
				'rule' => array('identicalFieldValues','user_id'),
				'required' => 'true',
				'message' => 'Duplicate Entry',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			
		),
		'created_at' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'created_by' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'updated_at' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'updated_by' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	 function identicalFieldValues( $field=array(), $compare_field=null )  
    { 
        foreach( $field as $key => $value ){ 

            if((trim($this->field($key))==trim($this->data[$this->alias]['fiscal_year_id'])) && (trim($this->field($compare_field))==trim($this->data[$this->alias]['user_id'])))	
            {
            	// echo 'if' ;exit;
            	return false;
            }
            
        } 
        return TRUE; 
    } 
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'FiscalYear' => array(
			'className' => 'FiscalYear',
			'foreignKey' => 'fiscal_year_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


}
