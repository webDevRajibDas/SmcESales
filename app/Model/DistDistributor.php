<?php

App::uses('AppModel', 'Model');

/**
 * Territory Model
 *
 * @property Office $Office
 * @property Market $Market
 * @property TerritoryPerson $TerritoryPerson
 */
class DistDistributor extends AppModel
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
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Distributor already exists.'
            )
        ),
        'office_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Office Id field required.'
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
        )
    );
    public $hasOne = array(
        'DistOutletMap' => array(
            'className' => 'DistOutletMap',
            'foreignKey' => 'dist_distributor_id',
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
        /*  'Territory' => array(
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
        'SaleTarget' => array(
            'className' => 'SaleTarget',
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
        'SaleTargetMonth' => array(
            'className' => 'SaleTargetMonth',
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
        )*/);

    public function filter($params, $conditions)
    {
        $conditions = array();


        if (CakeSession::read('Office.parent_office_id') != 0) {
            $conditions[] = array('DistDistributor.office_id' => CakeSession::read('Office.id'));
        } else {
            if (!empty($params['DistDistributor.office_id'])) {
                $conditions[] = array('DistDistributor.office_id' => $params['DistDistributor.office_id']);
            }
        }

        if (CakeSession::read('UserAuth.User.user_group_id') == 1034) {
            App::import('Model', 'DistUserMapping');
            $sp_id = CakeSession::read('UserAuth.User.sales_person_id');
            $this->DistUserMapping = new DistUserMapping();
            $data = $this->DistUserMapping->find('first', array('conditions' => array('DistUserMapping.sales_person_id' => $sp_id)));
            $distributor_id = $data['DistUserMapping']['dist_distributor_id'];
            $conditions[] = array('DistDistributor.id' => $distributor_id);
        }
        //adding db_code or name searching condition----------

        if (!empty($params['DistDistributor.name'])) {
            $conditions[] = array(
                'OR' => array(
                    'DistDistributor.name like' => "%" . $params['DistDistributor.name'] . "%",
                    'DistDistributor.db_code like' => "%" . $params['DistDistributor.name'] . "%"
                )
            );
        }

        if (!empty($params['DistDistributor.status'])) {
            $conditions[] = array('DistDistributor.is_active' => ($params['DistDistributor.status'] == 1 ? 1 : 0));
        }

        return $conditions;
    }
}
