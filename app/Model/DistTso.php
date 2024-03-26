<?php

App::uses('AppModel', 'Model');

/**
 * Territory Model
 *
 * @property Office $Office
 * @property Market $Market
 * @property TerritoryPerson $TerritoryPerson
 */
class DistTso extends AppModel
{

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';
    public $useDbConfig = 'default_06';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'name' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty')
            ),
            /* 'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Distributor already exists.'
            )*/
        ),
        'office_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Office Id field required.'
            )
        ),
        'user_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'user Id field required.'
            )
        ),
        'dist_area_executive_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Area Executive field required.'
            )
        ),
        'effective_date' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Effective Date is field required.'
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
        'DistAreaExecutive' => array(
            'className' => 'DistAreaExecutive',
            'foreignKey' => 'dist_area_executive_id',
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
    public $hasMany = array(
        'DistTsoMapping' => array(
            'className' => 'DistTsoMapping',
            'foreignKey' => 'dist_tso_id',
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

    public function filter($params, $conditions)
    {
        $conditions = array();
        if (CakeSession::read('Office.parent_office_id') != 0) {
            $conditions[] = array('DistTso.office_id' => CakeSession::read('Office.id'));
        } else {
            if (!empty($params['DistTso.office_id'])) {
                $conditions[] = array('DistTso.office_id' => $params['DistTso.office_id']);
            }
        }

        if (!empty($params['DistTso.name'])) {
            $conditions[] = array('DistTso.name LIKE' => '%' . $params['DistTso.name'] . '%');
        }
        if (!empty($params['DistTso.status'])) {
            $conditions[] = array('DistTso.is_active' => ($params['DistTso.status'] == 1 ? 1 : 0));
        }
        return $conditions;
    }
}
