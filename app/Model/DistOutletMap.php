<?php

App::uses('AppModel', 'Model');

/**
 * Territory Model
 *
 * @property Office $Office
 * @property Market $Market
 * @property TerritoryPerson $TerritoryPerson
 */
class DistOutletMap extends AppModel
{

    /**
     * Display field
     *
     * @var string
     */
    //public $displayField = 'name';
    public $useDbConfig = 'default_06';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'dist_distributor_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Distributor Id field required.'
            )
        ),
        'office_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Office Id field required.'
            )
        ),
        'outlet_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Outlet Id field required.'
            )
        )
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

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
        'Outlet' => array(
            'className' => 'Outlet',
            'foreignKey' => 'outlet_id',
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

    public function filter($params, $conditions)
    {
        $conditions = array();
        $conditions[] = array('DistOutletMap.office_id' => $params['DistOutletMap.office_id']);
        return $conditions;
    }
}
