<?php
App::uses('AppModel', 'Model');
/**
 * ProgramSession Model
 *
 * @property ProgramSession $ProgramSession
 */
class ProgramSession extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $useTable = 'sessions';
	public $displayField = 'id';


		// data filter
	public function filter($params, $conditions) {
        $conditions = array();
		if (!empty($params['Session.office_id'])) {
            $conditions[] = array('Territory.office_id' => $params['Session.office_id']);
        }
		if (!empty($params['Session.territory_id'])) {
            $conditions[] = array('ProgramSession.territory_id' => $params['Session.territory_id']);
        }
		if (isset($params['Session.date_from'])!='') {
            $conditions[] = array('ProgramSession.session_date >=' => Date('Y-m-d',strtotime($params['Session.date_from'])));
        }
		if (isset($params['Session.date_to'])!='') {
            $conditions[] = array('ProgramSession.session_date <=' => Date('Y-m-d',strtotime($params['Session.date_to'])));
        }
        if(isset($params['Session.session_type_id'])!=''){
        	$conditions[] = array('ProgramSession.session_type_id' =>$params['Session.session_type_id']);
        }	
        return $conditions;
    }
	
	
	public $validate = array(
		'name' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Title field required.'
			)
		),
		'office_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Office field required.'
			)
		),
		'territory_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Territory field required.'
			)
		),
		'so_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'SO field required.'
			)
		),
		'session_date' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Session Date type_id field required.'
			)
		),
		'total_participant' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Total participant field required.'
			)
		)
	);
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'so_id',
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
		),
		'SessionType'=>array(
			'className' => 'SessionType',
			'foreignKey' => 'session_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);	
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'SessionDetail' => array(
			'className' => 'SessionDetail',
			'foreignKey' => 'session_id',
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
