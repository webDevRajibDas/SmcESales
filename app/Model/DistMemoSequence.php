<?php
App::uses('AppModel', 'Model');
/**
 * DistMemoSequence Model
 */
class DistMemoSequence extends AppModel {
	
	public $displayField = 'id';

	public $validate = array(
		'last_memo_index' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Last memo Index field is required.'
			),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Memo Index already exist.'
			),
		)
	);
        
        public $belongsTo = array(
        'Memo' => array(
            'className' => 'Memo',
            'foreignKey' => 'last_memo_index',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
