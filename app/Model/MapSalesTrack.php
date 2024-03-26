<?php
App::uses('AppModel', 'Model');
/**
 * MessageList Model
 *
 * @property MessageCategory $MessageCategory
 * @property Sender $Sender
 */
class MapSalesTrack extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		/*'start_time' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Start time field required.'
			)
		),
		),*/
		
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
		)
	);
	
	/**
 * hasMany associations
 *
 * @var array
 */
	/*public $hasMany = array(
		'MessageReceiver' => array(
			'className' => 'MessageReceiver',
			'foreignKey' => 'message_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);*/
	
	
	/*public function filter($params, $conditions) {
		$conditions = array();
		if (!empty($params['MapSalesTrack.user_id'])) {
			$conditions[] = array('MapSalesTrack.so_id' => $params['MapSalesTrack.so_id']);
		}

		if (!empty($params['MapSalesTrack.created'])) {
			$conditions[] = array('MapSalesTrack.created' => date('Y-m-d', strtotime($params['MapSalesTrack.created'])));
		}


		return $conditions;
	}*/
	
}
