<?php
App::uses('AppModel', 'Model');
/**
 * Program Model
 *
 * @property Outlet $Outlet
 * @property ProgramType $ProgramType
 * @property Market $Market
 * @property Territory $Territory
 */
class Program extends AppModel
{

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';

	// data filter
	public function filter($params, $conditions)
	{
		$conditions = array();
		if (!empty($params['Program.program_type_id'])) {
			$conditions[] = array('Program.program_type_id' => $params['Program.program_type_id']);
		}
		if (!empty($params['Program.office_id'])) {
			$conditions[] = array('Program.officer_id' => $params['Program.office_id']);
		}
		if (!empty($params['Program.territory_id'])) {
			$conditions[] = array('Territory.id' => $params['Program.territory_id']);
		}
		if (!empty($params['Program.market_id'])) {
			$conditions[] = array('Market.id' => $params['Program.market_id']);
		}
		if (!empty($params['Program.thana_id'])) {
			$conditions[] = array('Market.thana_id' => $params['Program.thana_id']);
		}
		if (!empty($params['Program.status'])) {
			$conditions[] = array('Program.status' => $params['Program.status']);
		}
		return $conditions;
	}

	public $validate = array(
		'name' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Name field is required.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Name already exists.'
			)
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'ProgramType' => array(
			'className' => 'ProgramType',
			'foreignKey' => 'program_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Outlet' => array(
			'className' => 'Outlet',
			'foreignKey' => 'outlet_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		/* 'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'market_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		), */

		/*'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),*/
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'officer_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Doctor' => array(
			'className' => 'Doctor',
			'foreignKey' => 'doctor_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
