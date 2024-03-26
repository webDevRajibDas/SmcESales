<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
 
class OrderMemosController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $uses = array('Order', 'OrderDetail', 'ProductBatchInfo', 'Memo', 'MemoDetail', 'Product');
	public $components = array('Paginator', 'Session');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index____bk_memo() {
		Configure::write('debug', 2);
		
		//select * from orders where office_id=28 and order_time >='2023-05-24 16:18:02.000' and order_time <='2023-05-28 16:18:02.000' and confirm_status=2
		
		$order_ids = $this->Order->find('list', array(
			'conditions' => array(
				'Order.order_date >=' => '2023-05-24',
				'Order.order_date <=' => '2023-05-28',
				'Order.office_id' => 25,
				'Order.confirm_status' => 2,
			),
			'fields'=>array(
				'Order.id',
				'Order.order_no',
				
			),
			//'limit'=>10,
			'recursive' => -1
		));
		
		echo '<pre>';print_r($order_ids);exit;
		
		$orderid = array_keys($order_ids);
		//exit;
		$order_list = $this->OrderDetail->find('all', array(
			'conditions' => array(
				'OrderDetail.order_id' => $orderid,
				//'OrderDetail.order_id' => 113619,
				//'Order.confirm_status' => 2,
				//'Order.status' => 2,
				'OrderDetail.price <' => 1,
				'(OrderDetail.deliverd_qty is null)',
			),
			'recursive' => -1
		));
		$details = array();
		
		$j=1;
		foreach($order_list as $val){
			
			$pbinfo = $this->ProductBatchInfo->find('first', array(
				'conditions' => array(
					'ProductBatchInfo.order_details_id' => $val['OrderDetail']['id'],
					'ProductBatchInfo.memo_details_id' => 0,
				),
				'recursive' => -1
			));
			if(!empty($pbinfo)){
				$val['OrderDetail']['current_inventory_id'] = $pbinfo['ProductBatchInfo']['current_inventory_id'];
				$val['OrderDetail']['product_batch_id'] = $pbinfo['ProductBatchInfo']['id'];
				$val['OrderDetail']['given_stock'] = $pbinfo['ProductBatchInfo']['given_stock'];
				$details[$val['OrderDetail']['order_id']][] = $val;
			}
			
		}
		
		//echo '<pre>';print_r($details);exit;
		
		foreach($details as $key => $orderDeta){
			
			$memono = $order_ids[$key];
			
			$odcount = count($orderDeta);
			
			$memo_info = $this->Memo->find('first', array(
				'conditions' => array(
					'Memo.memo_no' => $memono
				),
				'fields'=>array('Memo.id'),
				'recursive' => -1
			));
			
			$memocount = $this->Memo->find('count', array(
				'conditions' => array(
					'Memo.memo_no' => $memono,
					'MD.price <' => 1,
				),
				'joins'=>array(
					array(
						'alias'=>'MD',
						'table'=>'memo_details',
						'type'=>'left',
						'conditions'=>'MD.memo_id=Memo.id',
					),
				),
				'recursive' => -1
			));
			
			$memo_id = $memo_info['Memo']['id'];
			
			if($memocount < $odcount AND $memocount == 0){
				$l = 1;
				foreach ($orderDeta as $order_detail_result) {

					$orderdetailsid = $order_detail_result['OrderDetail']['id'];
					$product_id = $order_detail_result['OrderDetail']['product_id'];

					$virtual_product_id = $order_detail_result['OrderDetail']['virtual_product_id'];

					$memo_details['MemoDetail']['memo_id'] = $memo_id;

					if ($virtual_product_id > 0) {
						$product_id = $virtual_product_id;
					}


					$product_details = $this->Product->find('first', array(
						'fields' => array('id', 'is_virtual', 'parent_id'),
						'conditions' => array('Product.id' => $product_id),
						'recursive' => -1
					));

					if ($product_details['Product']['is_virtual'] == 1) {
						$memo_details['MemoDetail']['virtual_product_id'] = $product_id;
						$memo_details['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
					} else {
						$memo_details['MemoDetail']['virtual_product_id'] = 0;
						$memo_details['MemoDetail']['product_id'] = $product_details['Product']['id'];
					}


					$memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
					$memo_details['MemoDetail']['actual_price'] = $order_detail_result['OrderDetail']['price'];
					$memo_details['MemoDetail']['price'] = $order_detail_result['OrderDetail']['price'] - $order_detail_result['OrderDetail']['discount_amount'];
					$memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['sales_qty'];

					$memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
					$memo_details['MemoDetail']['bonus_qty'] = NULL;
					$memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
					$memo_details['MemoDetail']['bonus_product_id'] = NULL;
					$memo_details['MemoDetail']['bonus_id'] = NULL;
					$memo_details['MemoDetail']['bonus_scheme_id'] = NULL;
					$memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
					$memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
					$memo_details['MemoDetail']['is_bonus'] = $order_detail_result['OrderDetail']['is_bonus'];
					$memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['sales_qty'];

					$memo_details['MemoDetail']['discount_type'] = $order_detail_result['OrderDetail']['discount_type'];
					$memo_details['MemoDetail']['discount_amount'] = $order_detail_result['OrderDetail']['discount_amount'];
					$memo_details['MemoDetail']['policy_type'] = $order_detail_result['OrderDetail']['policy_type'];
					$memo_details['MemoDetail']['policy_id'] = $order_detail_result['OrderDetail']['policy_id'];
					$memo_details['MemoDetail']['is_bonus'] = 0;
					if ($order_detail_result['OrderDetail']['is_bonus'] == 3)
						$memo_details['MemoDetail']['is_bonus'] = 3;
					
					//$total_product_data[] = $memo_details;
					
					$total_product_data = $memo_details;
					
					
					
					$product_batch_id = $order_detail_result['OrderDetail']['product_batch_id'];
					
					$current_inventory_id = $order_detail_result['OrderDetail']['current_inventory_id'];
					$quantity = $order_detail_result['OrderDetail']['given_stock'];
					
					//--------------one by one memo details insert---------\\
					$this->MemoDetail->create();
					//$this->MemoDetail->save($total_product_data);
					
					//$memo_details_id = $this->MemoDetail->getLastInsertId();
					if($memo_details_id){
						
						$mdidsupdate['ProductBatchInfo']['id'] = $product_batch_id;
						$mdidsupdate['ProductBatchInfo']['memo_details_id'] = $memo_details_id;
						
						//$this->ProductBatchInfo->save($mdidsupdate);
						
						//$this->new_update_current_inventory($current_inventory_id, $quantity);
						
						$l++;
						
						echo $l . '<br>';
						
					}
						
					
				}
				
			}
			
			
		}
		
		echo '<pre>';print_r($l);exit;
		
	}
	
	public function new_update_current_inventory($current_inventory_id, $quantity)
    {

       

        $this->loadModel('CurrentInventory');

        //--------------end---------------\\

        $inventory_info = $this->CurrentInventory->find('first', array(
            'conditions' => array(
                'CurrentInventory.id' => $current_inventory_id,
                'CurrentInventory.inventory_status_id' => 1,
            ),
            'recursive' => -1
        ));
       
        $transaction_type_id = 11;
        $transaction_date = date('Y-m-d');
           
        if ($quantity <= $inventory_info['CurrentInventory']['qty']) {
            $this->CurrentInventory->id = $inventory_info['CurrentInventory']['id'];
            if (!$this->CurrentInventory->updateAll(
                array(
                    'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity,
                    'CurrentInventory.transaction_type_id' => $transaction_type_id,
                    'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                    'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                ),
                array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])
            )) {
                return false;
            }
        }else{
            return false;
        }
            
        return true;

    }
}
