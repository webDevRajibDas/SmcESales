<?php
App::uses('AppController', 'Controller');
/**
 * Brands Controller
 *
 * @property Brand $Brand
 * @property PaginatorComponent $Paginator
 */


class DBStockProcessesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0); //300 seconds = 5 minutes

		$this->LoadModel('Store');
		if ($this->request->is('get')) {
			$request_data = $this->request->query;
			$store_id = $request_data['store_id'];
			$product_id = $request_data['product_id'];

			$query = "
						 EXEC  [dbo].[db_stock_process_from_begining]
								@store_id = $store_id,
								@product_id =$product_id 
						";
			$result = $this->Store->Query($query);

			/* $query = "
						 EXEC  [dbo].[manual_rpt_insert_for_dms]
							@process_store_id = $store_id,
							@process_product_id =$product_id ,
							@process_tran_date = '$tran_date'
						";
			$result = $this->Store->Query($query); */

			echo 'Success';
			exit;
		}
	}
}
