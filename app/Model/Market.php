<?php
App::uses('AppModel', 'Model');
/**
 * Market Model
 *
 * @property LocationType $LocationType
 * @property Thana $Thana
 * @property Territory $Territory
 * @property MarketPerson $MarketPerson
 * @property Outlet $Outlet
 */
class Market extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	/*-------- validation-------*/
		public $validate = array(
			'name' => array(
				'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Name field is required.'
				)
			),
			'location_type_id' => array(
				'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Location Type field is required.'
				)
			),
			'thana_id' => array(
				'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Thana field is required.'
				)
			),
			'territory_id' => array(
				'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Territory field is required.'
				)
			),
		);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'LocationType' => array(
			'className' => 'LocationType',
			'foreignKey' => 'location_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Thana' => array(
			'className' => 'Thana',
			'foreignKey' => 'thana_id',
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

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'MarketPerson' => array(
			'className' => 'MarketPerson',
			'foreignKey' => 'market_id',
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
		'Outlet' => array(
			'className' => 'Outlet',
			'foreignKey' => 'market_id',
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
	// data filter
	public function filter($params, $conditions) {
        $conditions = array();
		
		if(CakeSession::read('Office.parent_office_id') != 0)
		{
			$conditions[] = array('Territory.office_id' => CakeSession::read('Office.id'));
		}		
		if (!empty($params['Market.code'])) {
            $conditions[] = array('Market.code' => $params['Market.code']);
        }
		if (!empty($params['Market.name'])) {
            $conditions[] = array('Market.name LIKE' => '%'.$params['Market.name'].'%');
        }
		if (!empty($params['Market.location_type_id'])) {           
			$conditions[] = array('Market.location_type_id' => $params['Market.location_type_id']);
        }
		if (!empty($params['Market.district_id'])) {
            $conditions[] = array('Thana.district_id' => $params['Market.district_id']);
        }
		if (!empty($params['Market.thana_id'])) {
            $conditions[] = array('Market.thana_id' => $params['Market.thana_id']);
        }
		if (!empty($params['Market.office_id'])) {
			$conditions[] = array('Territory.office_id' => $params['Market.office_id']);
		}
		if (!empty($params['Market.territory_id'])) {
            $conditions[] = array('Market.territory_id' => $params['Market.territory_id']);
        }
        return $conditions;
    }

}
