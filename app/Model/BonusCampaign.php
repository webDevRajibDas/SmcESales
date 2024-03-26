<?php
App::uses('AppModel', 'Model');
/**
 * Brand Model
 *
 * @property Product $Product
 */
class BonusCampaign extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	
	public function filter($params, $conditions) {
        $conditions = array();
		
		//echo '<pre>';print_r($params);exit;
		
		if (!empty($params['BonusCampaign.product_id'])) {
            $conditions[] = array('BonusCampaignProductList.product_id' => $params['BonusCampaign.product_id']);
        }
		
		if (isset($params['BonusCampaign.date_from'])!='') {
            $conditions[] = array('BonusCampaign.start_date >=' => Date('Y-m-d',strtotime($params['BonusCampaign.date_from'])));
        }
		if (isset($params['BonusCampaign.date_to'])!='') {
            $conditions[] = array('BonusCampaign.end_date <=' => Date('Y-m-d',strtotime($params['BonusCampaign.date_to'])));
        }
        
        return $conditions;
    }
	

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'date_from' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'date from field required.'
			),
		),
		'date_to' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'date to field required.'
			),
		),
		'bonus_details' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'bonus details field required.'
			),
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	
	public $hasMany = array(
		'BonusCampaignProductList' => array(
			'className' => 'BonusCampaignProductList',
			'foreignKey' => 'bonus_campaign_id',
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
