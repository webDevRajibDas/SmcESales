<?php
/**
 * TerritoryWiseCollectionDepositBalanceFixture
 *
 */
class TerritoryWiseCollectionDepositBalanceFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'territory_wise_collection_deposit_balance';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'territory_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'instrument_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'total_collection' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '13'),
		'total_deposit' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '13'),
		'hands_of_so' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '13'),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_by' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'updated_at' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'updated_by' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '',
			'territory_id' => '',
			'instrument_id' => '',
			'total_collection' => '',
			'total_deposit' => '',
			'hands_of_so' => '',
			'created_at' => '2018-04-25 13:09:15',
			'created_by' => '',
			'updated_at' => '2018-04-25 13:09:15',
			'updated_by' => ''
		),
	);

}
