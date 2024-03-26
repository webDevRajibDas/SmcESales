<?php

App::uses('AppModel', 'Model');

/**
 * SaleTarget Model
 *
 * @property SalesPerson $SalesPerson
 */
class DistSaleTarget extends AppModel {

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $validate = array(
        'fiscal_year_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Short fiscal year field is required.'
            )
        ),
        'product_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Short Product Name field is required.'
            )
        ),
        'aso_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Short office name field is required.'
            )
        ),
        'measurement_unit_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Short measurement unit field is required.'
            )
        ),
        'quantity' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Short quantity  field is required.'
            )
        ),
        'amount' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Short amount field is required.'
            )
        )
    );

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'FiscalYear' => array(
            'className' => 'FiscalYear',
            'foreignKey' => 'fiscal_year_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
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
        'MeasurementUnit' => array(
            'className' => 'MeasurementUnit',
            'foreignKey' => 'measurement_unit_id',
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
        'Office' => array(
            'className' => 'Office',
            'foreignKey' => 'aso_id',
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
            /* 'Territory' => array(
              'className' => 'Territory',
              'foreignKey' => 'territory_id',
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
              'SalesPerson' => array(
              'className' => 'SalesPerson',
              'foreignKey' => 'so_id',
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
