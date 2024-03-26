<?php

App::uses('AppModel', 'Model');

/**
 * Territory Model
 *
 * @property Office $Office
 * @property Market $Market
 * @property TerritoryPerson $TerritoryPerson
 */
class DistDeliveryMan extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

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
            /*'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'SR already exists.'
            )*/
        ),
        'office_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Office Id field required.'
            )
        ),
        'code' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Code field required.'
            )
        ),
        'mobile_number' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Mobile Number field required.'
            )
        ),
        'dist_distributor_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Distributor is required.'
            )
        ),
        
        
    );

    
    public function check_available_code($code,$id=0,$office_id) {
        if($id)
        {
            $conditions=array('office_id'=>$office_id,'code'=>$code,'is_active'=>1,'NOT'=>array('id'=>$id));
        }
        else 
        {
            $conditions=array('office_id'=>$office_id,'code'=>$code,'is_active'=>1);
        }
        $existingCount = $this->find('count', array('conditions' =>$conditions,'recursive' => -1));
        return $existingCount;
    }
    
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
        )
        
    );
	
	/*
    public $hasOne = array(
        'SalesPerson' => array(
            'className' => 'SalesPerson',
            'foreignKey' => 'territory_id',
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
    public $hasMany = array(
        'DistMarket' => array(
            'className' => 'DistMarket',
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
            
    );

    public function filter($params, $conditions) {

        $conditions = array();
        $conditions[] = array('DistDeliveryMan.office_id' => $params['DistDeliveryMan.office_id']);
        if (!empty($params['DistDeliveryMan.name'])) {
            $conditions[] = array('DistDeliveryMan.name' => $params['DistDeliveryMan.name']);
        }
        if (!empty($params['DistDeliveryMan.dist_distributor_id'])) {
            $conditions[] = array('DistDeliveryMan.dist_distributor_id' => $params['DistDeliveryMan.dist_distributor_id']);
        }else{
            if(CakeSession::read('UserAuth.User.user_group_id') == 1034){
                App::import('Model', 'DistUserMapping');
                $sp_id = CakeSession::read('UserAuth.User.sales_person_id');
                $this->DistUserMapping = new DistUserMapping();
                $data = $this->DistUserMapping->find('first',array('conditions'=>array('DistUserMapping.sales_person_id'=>$sp_id)));
                $distributor_id = $data['DistUserMapping']['dist_distributor_id'];
                $conditions[] = array('DistDeliveryMan.dist_distributor_id' => $distributor_id);
            }
        }
        return $conditions;
    }

}
