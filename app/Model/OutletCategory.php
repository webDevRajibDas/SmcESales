<?php
App::uses('AppModel', 'Model');
/**
 * OutletCategory Model
 *
 */
class OutletCategory extends AppModel {

	public $displayField = 'category_name';

	public $validate = array(
		'category_name' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Category Name field required.'
			)
		)
	);

}
