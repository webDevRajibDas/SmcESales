<?php
App::uses('AppModel', 'Model');
/**
 * TerritoryWiseCollectionDepositBalance Model
 *
 * @property Territory $Territory
 * @property Instrument $Instrument
 */
class TerritoryWiseCollectionDepositBalance extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'territory_wise_collection_deposit_balance';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InstrumentType' => array(
			'className' => 'InstrumentType',
			'foreignKey' => 'instrument_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
