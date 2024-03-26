<?php
App::uses('AppModel', 'Model');
/**
 * Brand Model
 *
 * @property Product $Product
 */
class SoDailySale extends AppModel {

    public $hasMany = array(
		'SoDailySaleSumary' => array(
			'className' => 'SoDailySaleSumary',
			'foreignKey' => 'so_daily_sale_id',
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
	);

}