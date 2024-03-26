<?php
App::uses('AppModel', 'Model');
/**
 * Week Model
 *
 * @property Month $Month
 */
class Week extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'week_name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	public $validate = array(
		'week_name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Week Name field is required.'
					)
		),
		'start_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Start date field is required.'
					)
		),
		'end_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'End date field is required.'
					)
		),
		'month_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Month field is required.'
					)
		)
	);
	
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Month' => array(
			'className' => 'Month',
			'foreignKey' => 'month_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
