<?php
App::uses('AppController', 'Controller');

/**
 * Memos Controller
 *
 * @property Memo $Memo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */

Configure::write('debug', 2);
class AutoGenerateMemosController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('Memo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'DistProductPrice', 'DistProductCombination', 'DistCombination', 'MemoDetail', 'MeasurementUnit', 'User');
    public $components = array('Paginator', 'Session', 'Filter.Filter');
    public function admin_create_auto_memo()
    {
        Configure::write('debug', 2);
        date_default_timezone_set('Asia/Dhaka');
        $this->set('page_title', 'Create Memo');
        $this->loadModel('MemoDetail');
        //where is_auto_memo_created=0 and tt.office_id=26 and tt.id=20178 and mk.id=139681 and ot.id=297349
        $query = "
			SELECT
				ot.id outlet_id,
				mk.id market_id,
				mk.thana_id as thana_id,
				m.sales_person_id memo_sales_person_id,
				sp.id sales_person_id,
				tt.id territory_id,
				ot.category_id outlet_category_id,
				tt.office_id,
				FLOOR(sum(qty_in_disp)) as qty
			FROM ors_tornedo_offer oto 
			inner join memos m on m.memo_no=oto.memo_no
			inner join outlets ot on ot.id=m.outlet_id
			inner join markets mk on ot.market_id=mk.id
			inner join territories tt on tt.id=mk.territory_id
			left join sales_people sp on sp.territory_id=tt.id
			where is_auto_memo_created=0 
			group by
				ot.id,
				mk.id,
				mk.territory_id,
				mk.thana_id,
				m.sales_person_id,
				sp.id,
				tt.id,
				ot.category_id,
				tt.office_id
		";
        $memo_data = $this->Memo->query($query);

        foreach ($memo_data as $data) {

            $query = "
			SELECT
				m.memo_no as memo_no
			FROM ors_tornedo_offer oto 
			inner join memos m on m.memo_no=oto.memo_no
			inner join outlets ot on ot.id=m.outlet_id
			where ot.id=" . $data[0]['outlet_id'];

            $outlet_all_memo_query = $this->Memo->query($query);
            $memo_no_text = '';
            $memo_no_where = '';
            $is_are = count($outlet_all_memo_query) > 1 ? 'are' : 'is';
            foreach ($outlet_all_memo_query as $data_m) {
                $memo_no_text .= $data_m['0']['memo_no'] . ',';
                $memo_no_where .= '\'' . $data_m['0']['memo_no'] . '\',';
            }
            $memo_no_text = rtrim($memo_no_text, ',');
            $memo_no_where = rtrim($memo_no_where, ',');
            $sp_id = $data['0']['sales_person_id'] ? $data['0']['sales_person_id'] : $data['0']['memo_sales_person_id'];

            $generate_memo_no = $sp_id. rand(1,99).date('ymdHis');

            $memoData['office_id'] = $data['0']['office_id'];
            $memoData['sale_type_id'] = 1;
            $memoData['territory_id'] = $data['0']['territory_id'];
            $memoData['market_id'] = $data['0']['market_id'];
            $memoData['outlet_id'] = $data['0']['outlet_id'];
            //$memoData['entry_date'] = $this->request->data['Memo']['entry_date'];
            $memoData['memo_date'] = '2022-09-08';
            $memoData['memo_no'] = $generate_memo_no;
            $memoData['gross_value'] = 0;
            $memoData['cash_recieved'] = 0;
            $memoData['credit_amount'] = 0;
            $memoData['is_active'] = 1;
            $memoData['status'] = 2;
            //$memoData['memo_time'] = $this->current_datetime();
            $memoData['memo_time'] = date('2022-09-08 H:i:s');
            $memoData['sales_person_id'] = $data['0']['sales_person_id'] ? $data['0']['sales_person_id'] : $data['0']['memo_sales_person_id'];
            $memoData['from_app'] = 0;
            $memoData['action'] = 1;
            $memoData['is_program'] = 0;

            //$memoData['memo_reference_no'] =;

            $memoData['created_at'] = $this->current_datetime();
            $memoData['created_by'] = $this->UserAuth->getUserId();
            $memoData['updated_at'] = $this->current_datetime();
            $memoData['updated_by'] = $this->UserAuth->getUserId();

            $memoData['thana_id'] = $data['0']['thana_id'];

            $memoData['is_distributor'] = 0;
            //exit;
            $memoData['total_discount'] = 0;
            $memoData['remarks'] = "This is system generated memo for ORS Tornedo Offer (22 June 2022 To 05 July 2022).
            Reference memo number $is_are ($memo_no_text)";

            $this->loadModel('Store');
            $store_find = $this->Store->find('first', array(
                'conditions' => array('Store.territory_id' => $data['0']['territory_id'])
            ));
            $store_id = $store_find['Store']['id'];
            $this->Memo->create();
            $datasource = $this->Memo->getDataSource();
            try {
                $datasource->begin();
                if (!$this->Memo->save($memoData)) {
					echo 'memo not saved'.$memo_no_where;
                    throw new Exception();
                } else {
                    $memo_id = $this->Memo->getLastInsertId();
                    /* -------------- memo details :start  ------------------ */
                    $memo_details_bonus['MemoDetail']['memo_id'] = $memo_id;
                    $memo_details_bonus['MemoDetail']['is_bonus'] = 0;
                    $memo_details_bonus['MemoDetail']['product_id'] = 47;
                    $memo_details_bonus['MemoDetail']['measurement_unit_id'] = 7;
                    $memo_details_bonus['MemoDetail']['other_info'] = '{"selected_set":"7"}';
                    $memo_details_bonus['MemoDetail']['price'] = 0.0;
                    $memo_details_bonus['MemoDetail']['sales_qty'] = $data[0]['qty'];
                    /* ------ stock deduction -------------- */
                    $products = $this->Product->find(
                        'all',
                        array(
                            'conditions' => array('Product.id' => 47),
                            'fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'),
                            'recursive' => -1
                        )
                    );
                    $product_list = Set::extract($products, '{n}.Product');
                    $bonus_qty = $data[0]['qty'];
                    $punits_pre = $this->search_array(47, 'id', $product_list);
                    if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                        $base_quantity = $bonus_qty;
                    } else {
                        $base_quantity = $this->unit_convert(47, $punits_pre['sales_measurement_unit_id'], $bonus_qty);
                    }
					//echo $base_quantity.'<br>';
                    $update_type = 'deduct';
                    if (!$this->update_current_inventory($base_quantity, 47, $store_id, $update_type, 11, date('Y-m-d'))) {
                        echo $memo_no_text . " This memo not created for stock problem.<br>";
                        throw new Exception();
                    }
                    if (!$this->MemoDetail->saveAll($memo_details_bonus)) {
                        echo $memo_no_text . " This memo not created for memo detail problem.<br>";
                        throw new Exception();
                    }
                    /* ------ stock deduction -------------- */

                    /* -------------- memo details :end ------------------ */
                }
                $update_tornedo_table_sql = "UPDATE ors_tornedo_offer set is_auto_memo_created=1,refference_memo_no=$generate_memo_no WHERE memo_no IN ($memo_no_where)";
                if (!$this->Memo->query($update_tornedo_table_sql)) {
					echo 'ors tornedo table not updated for'.$memo_no_where;
                    throw new Exception();
                }
                $datasource->commit();
            } catch (Exception $e) {
				
				echo $e;exit;
                $datasource->rollback();
                continue;
            }
        }
        $this->autoRender = false;
    }

    public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '')
    {

        $this->loadModel('CurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add') {
            $find_type = 'first';
        }

        $inventory_info = $this->CurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'CurrentInventory.store_id' => $store_id,
                'CurrentInventory.inventory_status_id' => 1,
                'CurrentInventory.product_id' => $product_id
            ),
            'order' => array('CurrentInventory.expire_date' => 'asc'),
            'recursive' => -1
        ));




        if ($update_type == 'deduct') {

            foreach ($inventory_info as $val) {
                if ($quantity <= $val['CurrentInventory']['qty']) {

                    $this->CurrentInventory->id = $val['CurrentInventory']['id'];
                    $this->CurrentInventory->updateAll(
                        array(
                            'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity,
                            'CurrentInventory.transaction_type_id' => $transaction_type_id,
                            'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('CurrentInventory.id' => $val['CurrentInventory']['id'])
                    );
                    $quantity = 0;
                    break;
                } else {
                    

                    if ($val['CurrentInventory']['qty'] > 0) {
                        $this->CurrentInventory->id = $val['CurrentInventory']['id'];
                        $this->CurrentInventory->updateAll(
                            array(
                                'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $val['CurrentInventory']['qty'],
                                'CurrentInventory.transaction_type_id' => $transaction_type_id,
                                'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                            ),
                            array('CurrentInventory.id' => $val['CurrentInventory']['id'])
                        );
						$quantity = $quantity - $val['CurrentInventory']['qty'];
                    }
                }
            }
            if ($quantity > 0 && $inventory_info) {
                $this->CurrentInventory->id = $inventory_info[count($inventory_info) - 1]['CurrentInventory']['id'];
                $this->CurrentInventory->updateAll(
                    array(
                        'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity,
                        'CurrentInventory.transaction_type_id' => $transaction_type_id,
                        'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                        'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                    ),
                    array('CurrentInventory.id' => $inventory_info[count($inventory_info) - 1]['CurrentInventory']['id'])
                );
                $quantity = 0;
                //return false;
            }
        } else {
            /* $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])); */
            if (!empty($inventory_info)) {
                $this->CurrentInventory->updateAll(
                    array('CurrentInventory.qty' => 'CurrentInventory.qty + ' . $quantity, 'CurrentInventory.transaction_type_id' => $transaction_type_id, 'CurrentInventory.store_id' => $store_id, 'CurrentInventory.transaction_date' => "'" . $transaction_date . "'"),
                    array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])
                );
            }
        }

        return true;
    }
    public function search_array($value, $key, $array)
    {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return $array[$k];
            }
        }
        return null;
    }
}
