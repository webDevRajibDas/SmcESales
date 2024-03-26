<?php

App::uses('AppModel', 'Model');

/**
 * Outlet Model
 *
 * @property Market $Market
 * @property Category $Category
 */
class DistOrderDeliveryScheduleOrderDetail extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    /* ========== validate=============== */
    public $validate = array();

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array();
    public $hasOne = array();
	
	
	public $hasMany = array();

    // data filter
    public function filter($params, $conditions) {}

}
