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
class DistCurrentInventory extends AppModel
{
    public $useDbConfig = 'default_06';
    // data filter
    public function filter($params, $conditions)
    {
        $conditions = array();
        if (CakeSession::read('Office.parent_office_id') != 0) {
            $conditions[] = array('DistStore.office_id' => CakeSession::read('Office.id'));
        }
        if (!empty($params['DistCurrentInventory.product_code'])) {
            $conditions[] = array('Product.product_code' => $params['DistCurrentInventory.product_code']);
        }
        if (!empty($params['DistCurrentInventory.inventory_status_id'])) {
            $conditions[] = array('DistCurrentInventory.inventory_status_id' => $params['DistCurrentInventory.inventory_status_id']);
        } else {
            $conditions[] = array('DistCurrentInventory.inventory_status_id !=' => 2);
        }
        if (!empty($params['DistCurrentInventory.product_id'])) {
            $conditions[] = array('DistCurrentInventory.product_id' => $params['DistCurrentInventory.product_id']);
        }

        if (!empty($params['DistCurrentInventory.store_id'])) {
            $conditions[] = array('DistCurrentInventory.store_id' => $params['DistCurrentInventory.store_id']);
        } else {
            if (CakeSession::read('UserAuth.User.user_group_id') == 1034) {
                App::import('Model', 'DistUserMapping');
                App::import('Model', 'DistStore');
                $sp_id = CakeSession::read('UserAuth.User.sales_person_id');
                $this->DistUserMapping = new DistUserMapping();

                $data = $this->DistUserMapping->find('first', array('conditions' => array('DistUserMapping.sales_person_id' => $sp_id)));
                $distributor_id = $data['DistUserMapping']['dist_distributor_id'];

                $this->DistStore = new DistStore();
                $dist_store = $this->DistStore->find('first', array('conditions' => array('DistStore.dist_distributor_id' => $distributor_id)));

                $dist_store_id = $dist_store['DistStore']['id'];

                $conditions[] = array('DistCurrentInventory.store_id' => $dist_store_id);
            }
        }
        if (!empty($params['DistCurrentInventory.product_categories_id'])) {
            $conditions[] = array('ProductCategory.id' => $params['DistCurrentInventory.product_categories_id']);
        }
        return $conditions;
    }

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'DistStore' => array(
            'className' => 'DistStore',
            'foreignKey' => 'store_id',
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
        ),
        'InventoryStatuses' => array(
            'className' => 'InventoryStatuses',
            'foreignKey' => 'inventory_status_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'TransactionType' => array(
            'className' => 'TransactionType',
            'foreignKey' => 'transaction_type_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
