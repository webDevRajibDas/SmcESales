<?php

App::uses('AppModel', 'Model');

/**
 * Store Model
 *
 * @property Office $Office
 * @property Territory $Territory
 * @property CurrentInventory $CurrentInventory
 */
class DistStore extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    // data filter
    public function filter($params, $conditions) {
        $conditions = array();
        if (CakeSession::read('Office.parent_office_id') != 0) {
            $conditions[] = array('DistStore.office_id' => CakeSession::read('Office.id'));
        }
        if (!empty($params['DistStore.store_type_id'])) {
            $conditions[] = array('DistStore.store_type_id' => $params['DistStore.store_type_id']);
        }
        if (!empty($params['DistStore.office_id'])) {
            $conditions[] = array('DistStore.office_id' => $params['DistStore.office_id']);
        }
        return $conditions;
    }

    public $validate = array(
        'name' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Distributor Store name is required.'
            )
        ),
        'store_type_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Distributor Store Type field is required.'
            ),
            'unique' => array(
                'rule' => array('checkUnique'),
                'message' => 'Setting should be unique.',
            )
        ),
        'office_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Office field is required.'
            )
        ),
        'dist_distributor_id' => array(
            'territory_check' => array(
                'rule' => 'territory_check',
                'message' => 'Distributor field is required.'
            )
        )
    );

    function checkUnique($data, $fields) {
        if ($this->data[$this->name]['store_type_id'] == 1 OR $this->data[$this->name]['store_type_id'] == 2) {
            $unique['store_type_id'] = $this->data[$this->name]['store_type_id'];
            $unique['office_id'] = $this->data[$this->name]['office_id'];
        } else {
            $unique['store_type_id'] = $this->data[$this->name]['store_type_id'];
            $unique['office_id'] = $this->data[$this->name]['office_id'];
            $unique['dist_distributor_id'] = $this->data[$this->name]['dist_distributor_id'];
        }
        /* if (isset($this->data[$this->name][$this->primaryKey]))
          {
          $unique[$this->primaryKey] = "<>" . $this->data[$this->name][$this->primaryKey];
          } */
        return $this->isUnique($unique, false);
    }

    function territory_check($data, $fields) {
        if ($this->data[$this->name]['store_type_id'] == 3 AND $this->data[$this->name]['dist_distributor_id'] == '') {
            return false;
        } else {
            return true;
        }
    }

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
        'DistDistributor' => array(
            'className' => 'DistDistributor',
            'foreignKey' => 'dist_distributor_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'StoreType' => array(
            'className' => 'StoreType',
            'foreignKey' => 'store_type_id',
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
    public $hasMany = array(
        'CurrentInventory' => array(
            'className' => 'CurrentInventory',
            'foreignKey' => 'store_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

}
