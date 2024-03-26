<?php
App::uses('AppModel', 'Model');
/**
 * OutletCategory Model
 *
 */
class DistOutletCategory extends AppModel {

	public $useDbConfig = 'default_06';

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
