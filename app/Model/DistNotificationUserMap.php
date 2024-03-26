<?php

App::uses('AppModel', 'Model');

/**
 * NotificationUserMap Model
 */
class DistNotificationUserMap extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    //public $displayField = '';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'user_id' => array(
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
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    
    
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
