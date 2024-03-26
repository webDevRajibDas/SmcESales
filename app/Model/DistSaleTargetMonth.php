<?php

App::uses('AppModel', 'Model');

/**
 * SaleTarget Model
 *
 * @property SaleTargetMonth $SaleTargetMonth
 */
class DistSaleTargetMonth extends AppModel {

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $validate = array(
    );

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'DistSaleTarget' => array(
            'className' => 'DistSaleTarget',
            'foreignKey' => 'dist_sale_target_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'FiscalYear' => array(
            'className' => 'FiscalYear',
            'foreignKey' => 'fiscal_year_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Month' => array(
            'className' => 'Month',
            'foreignKey' => 'month_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

}
