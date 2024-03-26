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
class DistDistributorAuditDetail extends AppModel {

    public $validate = array(
        'dist_distributor_audit_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Distributor Audit field is required.'
            )
        ),
        'product_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Product field is required.'
            ),
        ),
        'measurement_unit_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Measurement field is required.'
            ),
        )
    );

    // data filter
    public function filter($params, $conditions) {
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
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'DistDistributorAudit' => array(
            'className' => 'DistDistributorAudit',
            'foreignKey' => 'dist_distributor_audit_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'MeasurementUnit' => array(
            'className' => 'MeasurementUnit',
            'foreignKey' => 'measurement_unit_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
