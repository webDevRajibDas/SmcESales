<?php

App::uses('AppModel', 'Model');
App::uses('UserAuthComponent', 'Usermgmt.Controller/Component');

/**
 * CurrentInventory Model
 *
 * @property InventoryStore $InventoryStore
 * @property InventoryStatus $InventoryStatus
 * @property Product $Product
 * @property Batch $Batch
 */
class DistDistributorAudit extends AppModel {

    public $validate = array(
        'office_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Office field is required.'
            )
        ),
        'dist_distributor_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Distributors field is required.'
            ),
        ),
    );

    // data filter
    public function filter($params, $conditions) {
		//pr($conditions);
		//pr($params);die();
        $conditions = array();
        /* if (CakeSession::read('Office.parent_office_id') != 0) {
          $conditions[] = array('Store.office_id' => CakeSession::read('Office.id'));
          } */
        if (!empty($params['DistDistributorAudit.office_id'])) {
            $conditions[] = array('DistDistributorAudit.office_id' => $params['DistDistributorAudit.office_id']);
        }
        if (!empty($params['DistDistributorAudit.dist_distributor_id'])) {
            $conditions[] = array('DistDistributorAudit.dist_distributor_id' => $params['DistDistributorAudit.dist_distributor_id']);
        }
        if (!empty($params['DistDistributorAudit.product_id'])) {
            $conditions[] = array('DistDistributorAudit.product_id' => $params['DistDistributorAudit.product_id']);
        }

        return $conditions;
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
    );
    public $hasMany = array(
        'DistDistributorAuditDetail' => array(
            'className' => 'DistDistributorAuditDetail',
            'foreignKey' => 'dist_distributor_audit_id',
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

}
