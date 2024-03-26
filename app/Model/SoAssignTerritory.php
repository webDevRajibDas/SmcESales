<?php
App::uses('AppModel', 'Model');

class SoAssignTerritory extends AppModel {

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
	public $belongsTo = array();
}

?>