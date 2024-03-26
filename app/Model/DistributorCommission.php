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
class DistributorCommission extends AppModel {
	public $validate = array(
		'product_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message'=> 'Product is required.'
			)
		),
		'commission_amount' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message'=> 'Distributor commission amount is required.'
			)
		),
		'effective_date' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message'=> 'Effective Date is required.'
			)
		)
	);

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
        )
    );

}
