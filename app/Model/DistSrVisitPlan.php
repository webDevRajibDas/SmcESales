
<?php
App::uses('AppModel', 'Model');
/**
 * Brand Model
 *
 * @property Product $Product
 */
class DistSrVisitPlan extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $validate = array();
	//$company_id=CakeSession::read('Office.company_id');
	//pr($company_id);die();
	/**
	 * model validation array
	 *
	 * @var array
	 */
	function addValidate() {}
	/**
	 * Used to check permissions of group
	 *
	 * @access public
	 * @param string $controller controller name
	 * @param string $action action name
	 * @param integer $userGroupID group id
	 * @return boolean
	 */
	
	public $hasMany = array(
		'DistSrVisitPlanDetail' => array(
			'className' => 'DistSrVisitPlanDetail',
			'foreignKey' => 'dist_sr_visit_plan_id',
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
	
	function checkUnique($data, $fields) {
		//pr($this->data[$this->name]);die();
        $unique['company_id'] = $company_id;
        $unique['name'] = $this->data[$this->name]['name'];
        //$unique['name'] = $this->data[$this->name]['name'];
        $unique['name'] = $this->data[$this->name]['alias_name'];
        return $this->isUnique($unique, false);
    }
}
