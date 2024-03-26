<?php

App::uses('AppModel', 'Model');

/**
 * DistNotification Model
 */
class DistNotification extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'product_id';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'product_id' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty')
            )
        ),
        'office_id' => array(
             'notEmpty' => array(
                'rule' => array('notEmpty')
            )
        )
    );

  
    /**
     * belongsTo associations
     *
     * @var array
     */
    
    public $belongsTo = array(
        'Office' => array(
            'className' => 'Office',
            'foreignKey' => 'office_id',
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
        )
    );
    
    /*
    public $hasOne = array(
        'NotificationUserMap' => array(
            'className' => 'NotificationUserMap',
            'foreignKey' => 'dist_notice_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    */
    
    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array();

    public function filter($params, $conditions) {
        $conditions = array();
        return $conditions;
    }

}
