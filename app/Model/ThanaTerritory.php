<?php
App::uses('AppModel', 'Model');
/**
 * Thana Model
 *
 * @property District $District
 * @property Market $Market
 */
class ThanaTerritory extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';


	

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Thana' => array(
			'className' => 'Thana',
			'foreignKey' => 'thana_id',
			'conditions' => '',
			'type' => 'INNER',
			'fields' => 'Thana.id,Thana.name,Thana.district_id',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	

}
