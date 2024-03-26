<?php
/**
 * ProxySellFixture
 *
 */
class ProxySellFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'proxy_sell';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'proxy_for_so_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'proxy_by_so_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'proxy_for_territory_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'proxy_by_territory_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'from_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'to_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'remarks' => array('type' => 'text', 'null' => true, 'default' => null, 'length' => '16'),
		'updated_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated_by' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_by' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
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
			'id' => 1,
			'proxy_for_so_id' => 1,
			'proxy_by_so_id' => 1,
			'proxy_for_territory_id' => 1,
			'proxy_by_territory_id' => 1,
			'from_date' => '2017-08-16',
			'to_date' => '2017-08-16',
			'remarks' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'updated_at' => '2017-08-16 18:21:29',
			'updated_by' => 1,
			'created_at' => '2017-08-16 18:21:29',
			'created_by' => 1
		),
	);

}
