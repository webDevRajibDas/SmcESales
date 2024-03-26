<?php
App::uses('AppModel', 'Model');
/**
 * Brand Model
 *
 * @property Product $Product
 */
class SoDailySaleSumary extends AppModel {

    public $belongsTo = array(
		'SoDailySale' => array(
			'className' => 'SoDailySale',
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