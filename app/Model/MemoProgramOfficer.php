<?php
App::uses('AppModel', 'Model');
/**
 * MemoProgramOfficer Model
 *
 */
class MemoProgramOfficer extends AppModel {

/**
 * Display field
 *
 * @var string
 */

	public $validate = array();

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array();
	
/**
 * BelongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		/* 'Memo' => array(
			'className' => 'Memo',
			'foreignKey' => 'memo_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		) */
	);
}
