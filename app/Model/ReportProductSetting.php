<?php
App::uses('AppModel', 'Model');
/**
 * ReportProductSetting Model
 *
 * @property Product $Product
 */
class ReportProductSetting extends AppModel {

public $displayField = 'min_qty';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'product_id' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Product Field is required.'
			),
			'isUnique' => array(
					'rule' => 'isUnique',
					'message'=> 'Product already exists.'
			)
		),
		
		'sort' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			),
			/*'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Sort must be unique.'
			)*/
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
		),
	);

	// data filter
	public function filter($params, $conditions) {
		if (!empty($params['ReportProductSetting.product_id'])) {
            $conditions = array('ReportProductSetting.product_id' => $params['ReportProductSetting.product_id']);
        }
        return $conditions;
    }

}
