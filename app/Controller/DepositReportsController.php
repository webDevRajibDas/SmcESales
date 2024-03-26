<?php
App::uses('AppController', 'Controller');

/**
 *
 */
class DepositReportsController extends AppController
{
    public $uses = array('Memo', 'SalesPerson', 'Office', 'TerritoryAssignHistory', 'Collection');

    public $components = array('Paginator', 'Session', 'Filter.Filter');

    public function admin_index(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000); //3000 seconds = 5 minutes

        date_default_timezone_set('Asia/Dhaka');
        $this->set('page_title', 'Deposit Report');
        $this->loadModel('Office');
        $this->loadModel('Collection');
        $this->loadModel('Deposit');
        $this->loadModel('Territory');


        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_id = 0;
        } else {
            $office_id = $this->UserAuth->getOfficeId();
        }


        $so_list = array();

        if ($office_id || $this->request->is('post') || $this->request->is('put')) {
            //$this->dd($this->request->data); exit;
            $office_id = isset($this->request->data['OutletSalesReports']['office_id']) != '' ? $this->request->data['OutletSalesReports']['office_id'] : $office_id;

            //get SO list
            $so_list_r = $this->SalesPerson->find('all', array(
                'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
                'conditions' => array(
                    'SalesPerson.office_id' => $office_id,
                    'SalesPerson.territory_id >' => 0,
                    'User.user_group_id' => array(4, 1008),
                ),
                'recursive' => 0
            ));

            foreach ($so_list_r as $key => $value) {
                $so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
            }

        }
        $this->set(compact('so_list'));
        //$this->Session->write('so_list', $so_list);

        //$this->dd($value.PHP_EOL.'php');exit();

        $offices = array();

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id != 0) {
            $office_type = $this->Office->find('first', array(
                'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
                'recursive' => -1
            ));
            $office_type_id = $office_type['Office']['office_type_id'];
        }


        if ($office_parent_id == 0) {
            $region_office_condition = array('office_type_id' => 3);
            $office_conditions = array('office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
        } else {
            if ($office_type_id == 3) {
                $region_office_condition = array('office_type_id' => 3, 'Office.id' => $this->UserAuth->getOfficeId());
                $office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
            } elseif ($office_type_id == 2) {
                $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
            }

        }

        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions,
            'fields' => array('office_name')
        ));

        if ($this->request->is('post')) {
            @$region_office_id = isset($this->request->data['OutletSalesReports']['region_office_id']) != '' ? $this->request->data['OutletSalesReports']['region_office_id'] : $region_office_id;
            $this->set(compact('region_office_id'));
            if ($region_office_id) $office_conditions['parent_office_id'] = $region_office_id;

            $office_id = isset($this->request->data['OutletSalesReports']['office_id']) != '' ? $this->request->data['OutletSalesReports']['office_id'] : $office_id;

            $offices = $this->Office->find('list', array(
                'conditions' => $office_conditions,
                'fields' => array('office_name')
            ));

        } else {
            if (@$office_type_id == 3) {
                //pr($office_conditions);
                $offices = $this->Office->find('list', array(
                    'conditions' => $office_conditions,
                    'fields' => array('office_name')
                ));
            }
        }

        if (isset($region_office_condition)) {
            $region_offices = $this->Office->find('list', array(
                'conditions' => $region_office_condition,
                'order' => array('office_name' => 'asc')
            ));

            $this->set(compact('region_offices'));
        }
        $request_data = array();

        if ($this->request->is('post')) {
            //$this->dd($this->request->data);exit();

            $request_data = $this->request->data;
            $this->set(compact('request_data'));

            $office_id_all = array();
            if ($this->request->data['OutletSalesReports']['office_id']) {
                $office_id = $this->request->data['OutletSalesReports']['office_id'];
                $office_id_all[] = $this->request->data['OutletSalesReports']['office_id'];
                $this->set(compact('office_id'));
            } else {
                $all_office = $this->Office->find('all', array(
                    'conditions' => array('Office.parent_office_id' => $this->request->data['OutletSalesReports']['region_office_id'],
                        'Office.office_type_id' => 2
                    ),
                    'fields' => array('Office.id'),
                    'recursive' => -1
                ));
                foreach ($all_office as $o_data) {
                    $office_id_all[] = $o_data['Office']['id'];
                }
            }

            $date_from = $this->request->data['OutletSalesReports']['date_from'];
            $date_to = $this->request->data['OutletSalesReports']['date_to'];
            $last_date_opening = date('Y-m-d', strtotime($date_from . ' -1 day'));
            //$this->dd($last_date_opening);exit();
            $date_range_first = date('Y-m-d', strtotime($date_from));
            $date_range_last = date('Y-m-d', strtotime($date_to));
            $current_date = date('Y-m-d');

            $sales_people = array();

            $so_list1 = array();
            $so_offices = array();
            foreach ($office_id_all as $of_data) {
                $office_id = $of_data;
                $first_date_deposit = '2018-10-01';
                $first_date_memo = '2018-10-01';

                //NEW SO LIST GENERATE FROM MEMO TABLE
                $date_from1 = date('Y-m-d', strtotime($date_from));
                $date_to1 = date('Y-m-d', strtotime($date_to));
                $so_id = $this->request->data['OutletSalesReports']['so_id'] ? $this->request->data['OutletSalesReports']['so_id'] : 0;
                $con = array(
                    'Memo.memo_date BETWEEN ? and ?' => array($date_from1, $date_to1),
                    'Memo.office_id' => $office_id
                );
                if ($so_id) $con['Memo.sales_person_id'] = $so_id;

                $so_list_r = $this->SalesPerson->find('all', array(
                    'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Memo.territory_id', 'Territory.name', 'Memo.office_id',),
                    'joins' => array(
                        array('table' => 'memos',
                            'alias' => 'Memo',
                            'type' => 'INNER',
                            'conditions' => 'SalesPerson.id=Memo.sales_person_id',
                        ),
                        array(
                            'alias' => 'Territory',
                            'table' => 'territories',
                            'type' => 'INNER',
                            'conditions' => 'Memo.territory_id = Territory.id'
                        ),
                    ),
                    'conditions' => $con,
                    'order' => array('Territory.name' => 'ASC'),
                    'recursive' => -1
                ));
                $so_list_for_finding_data = array();
                foreach ($so_list_r as $key => $value) {
                    $so_list1[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
                    $so_list_for_finding_data[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
                    $so_offices[$value['SalesPerson']['id']] = $value['Memo']['office_id'];
                }

                //---------------------------------------\\
                if ($so_id) {
                    $con2['SalesPerson.id'] = $so_id;
                    $con2['NOT'] = array('SalesPerson.id' => array_keys($so_list_for_finding_data));

                    $so_list_r = $this->SalesPerson->find('all', array(
                        'fields' => array('SalesPerson.id', 'SalesPerson.name', 'SalesPerson.territory_id', 'Territory.name', 'SalesPerson.office_id',),
                        'joins' => array(
                            array(
                                'alias' => 'Territory',
                                'table' => 'territories',
                                'type' => 'INNER',
                                'conditions' => 'SalesPerson.territory_id = Territory.id'
                            ),
                        ),
                        'conditions' => $con2,
                        'order' => array('Territory.name' => 'ASC'),
                        'recursive' => -1
                    ));

                    foreach ($so_list_r as $key => $value) {
                        $so_list1[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
                        $so_list_for_finding_data[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
                        $so_offices[$value['SalesPerson']['id']] = $value['SalesPerson']['office_id'];
                    }

                }

                //----------------end--------------\\

                if (!$so_id) {
                    $conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
                    // $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
                    $conditions['TerritoryAssignHistory.date >= '] = $date_range_first;
                    $conditions['NOT'] = array('TerritoryAssignHistory.so_id' => array_keys($so_list1));
                    //pr($conditions);
                    $old_so_list = $this->TerritoryAssignHistory->find('all', array(
                        'conditions' => $conditions,
                        'order' => array('Territory.name' => 'asc'),
                        'recursive' => 0
                    ));
                    // pr($old_so_list);exit;
                    if ($old_so_list) {
                        foreach ($old_so_list as $old_so) {
                            $so_list1[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
                            $so_list_for_finding_data[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'] . ' (' . $old_so['Territory']['name'] . ')';
                            $so_offices[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['Territory']['office_id'];
                        }
                    }
                }
                if (!$so_id) {
                    $so_id_for_outstanding = array_keys($so_list1);
                    $so_id_for_outstanding = implode(',', $so_id_for_outstanding);
                    if ($so_id_for_outstanding) {
                        $HvWr = "SalesPerson.id not in ($so_id_for_outstanding) AND";
                    }
                    $sql = "select SalesPerson.id ,SalesPerson.name as sales_person,m.territory_id,Territory.name as territory_name,m.office_id	from memos m 
					inner join sales_people SalesPerson on SalesPerson.id=m.sales_person_id
					inner join territories Territory on Territory.id=m.territory_id
					left join collections cl on cl.memo_id=m.id and cl.collectionDate BETWEEN '2018-10-01' and '" . $last_date_opening . "' 
					where  m.office_id=$office_id 
					and m.memo_date BETWEEN '2018-10-01' and '" . $last_date_opening . "' 
					and m.credit_amount!=0
					and m.status>0
					group by SalesPerson.id,SalesPerson.name,m.territory_id,Territory.name,m.office_id,m.id
					having " . $HvWr . " max(m.gross_value) > CASE WHEN SUM(cl.collectionAmount) is not null THEN SUM(cl.collectionAmount) ELSE 0 END";
                    $outstanging_so = $this->Memo->query($sql);
                    // pr($outsta nging_so);exit;
                    foreach ($outstanging_so as $key => $value) {
                        $so_list1[$value[0]['id']] = $value['0']['sales_person'] . ' (' . $value[0]['territory_name'] . ')';

                        $so_list_for_finding_data[$value[0]['id']] = $value['0']['sales_person'] . ' (' . $value[0]['territory_name'] . ')';

                        $so_offices[$value[0]['id']] = $value[0]['office_id'];
                    }
                }

                if (empty($so_id)) {

                    $so_list_r = $this->SalesPerson->find('all', array(
                        'fields' => array('SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'Territory.name'),
                        'conditions' => array(
                            'SalesPerson.office_id' => $office_id,
                            'SalesPerson.territory_id >' => 0,
                            'User.user_group_id' => array(4, 1008),
                            'NOt' => array('SalesPerson.id' => array_keys($so_list_for_finding_data))
                        ),
                        'recursive' => 0
                    ));

                    foreach ($so_list_r as $key => $value) {
                        $so_list1[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';

                        $so_list_for_finding_data[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';

                        $so_offices[$value['SalesPerson']['id']] = $value['SalesPerson']['office_id'];
                    }
                }

                /*pr($so_list1);
                pr($so_list_for_finding_data);
                pr($so_offices);
                exit;*/

                foreach ($so_list_for_finding_data as $sales_person_id => $value) {

                    if (!empty($sales_person_id)) {
                        //Previus collection_amount_sum

                        $discountSql = "
							select 
								m.id as memo_id,
								Sum(md.sales_qty * CASE WHEN dbp.is_discount_exclude_from_value = 1 THEN md.discount_amount ELSE 0 END) AS discount
							from 
								collections c
							inner join 
									memos m on c.memo_id=m.id
							inner join 
									memo_details md on m.id=md.memo_id
							left join discount_bonus_policies dbp on dbp.id=md.policy_id
									
							where 
								c.collectiondate BETWEEN '2018-10-01' and '" . $date_range_last . "' and
								m.memo_date BETWEEN '2018-10-01' and '" . $date_range_last . "' and 
									m.office_id = " . $office_id . " AND 
									m.sales_person_id = " . $sales_person_id . "
									AND m.status > 0 
									
							group by m.id
						";

                        //echo $discountSql;exit;

                        $collection_amount_sum = $this->Collection->find('all', array(
                                'conditions' => array(
                                    'Memo.sales_person_id' => $sales_person_id,
                                    'Collection.collectionDate BETWEEN ? and ? ' => array('2018-10-01', $last_date_opening),
                                    'Memo.memo_date BETWEEN ? and ? ' => array('2018-10-01', $last_date_opening),
                                    //'Collection.collectionDate <' => $date_range_first,
                                    'Memo.office_id' => $office_id,
                                    'Memo.status >' => 0,
                                ),
                                'joins' => array(
                                    array(
                                        'alias' => 'Memo',
                                        'table' => 'memos',
                                        'type' => 'INNER',
                                        'conditions' => 'Collection.memo_id = Memo.id'
                                    ),
                                    array(
                                        'alias' => 'DiscountValue',
                                        'table' => "( $discountSql )",
                                        'type' => 'left',
                                        'conditions' => 'DiscountValue.memo_id = Memo.id'
                                    )

                                ),
                                'fields' => array('SUM(Collection.collectionAmount - CASE WHEN DiscountValue.discount > 0 THEN DiscountValue.discount ELSE 0 END) AS total_collection'),
                                //'fields'=> array('memo_id'),
                                //'group' => array('Collection.memo_id'),
                                'recursive' => -1
                            )
                        );

                        //echo $this->Collection->getLastquery();exit;

                        $collection_amount[$sales_person_id] = $collection_amount_sum;
                        //pr($collection_amount);
                        //End Previus collection_amount_sum

                        //Previus Deposite Amount
                        $deposit_amount_sum = $this->Deposit->query("select sum(d.deposit_amount) AS total_deposit 
							from deposits d
							left join memos m on m.id=d.memo_id
							inner join territories t on (t.id=d.territory_id)
							where d.sales_person_id = " . $sales_person_id . " 
							and m.id is null
							and t.office_id=$office_id
							and d.deposit_date BETWEEN '2018-10-01' and '" . $last_date_opening . "'");
                        $deposit_amount[$sales_person_id] = $deposit_amount_sum;

                        //Previus credit Deposite Amount
                        $deposit_amount_sum = $this->Deposit->query("select sum(d.deposit_amount) AS total_deposit 
							from deposits d
							inner join memos m on m.id=d.memo_id
							inner join territories t on (t.id=d.territory_id)
							where m.sales_person_id = " . $sales_person_id . " 
							and m.status>0
							and t.office_id=$office_id
							and d.deposit_date BETWEEN '2018-10-01' and '" . $last_date_opening . "'");
                        $deposit_amount[$sales_person_id][0][0]['total_deposit'] += $deposit_amount_sum[0][0]['total_deposit'];

                        //pr($deposit_amount);
                        //End Previus Deposite Amount


                        $current_credit_collection_sum = $this->Collection->query("select sum(collections.collectionAmount) AS total_current_credit_collection 
							from collections 
							left join memos on memos.id = collections.memo_id 
							where memos.office_id = " . $office_id . " 
							and memos.sales_person_id = " . $sales_person_id . " 
							and collections.is_credit_collection = 1 
							and memos.credit_amount > 0 
							and memos.status > 0 
							and memos.memo_date BETWEEN '" . $date_range_first . "' and '" . $date_range_last . "' 
							and collections.collectionDate BETWEEN '" . $date_range_first . "' and '" . $date_range_last . "'");
                        $current_credit_collection[$sales_person_id] = $current_credit_collection_sum;
                        //pr($current_credit_collection);


                        //previous credit collection
                        $sql = "select sum(collections.collectionAmount) AS total_previous_credit_collection 
							from collections 
							left join memos on memos.id = collections.memo_id 
							where memos.office_id = " . $office_id . " and memos.sales_person_id = " . $sales_person_id . " 
							and collections.is_credit_collection = 1 
							and memos.credit_amount > 0 
							and memos.status > 0 
							and memos.memo_date BETWEEN '" . $first_date_memo . "' and '" . $last_date_opening . "' 
							and collections.collectionDate BETWEEN '" . $date_range_first . "' and '" . $date_range_last . "'";
                        $previous_credit_collection_sum = $this->Collection->query($sql);
                        $previous_credit_collection[$sales_person_id] = $previous_credit_collection_sum;

                        //pr($previous_credit_collection_sum);
                        //exit;


                        /*ADD NEW BY DELWAR*/
                        //opening market outstading
                        $sql = "select (MAX(m.gross_value)-CASE WHEN SUM(cl.collectionAmount) is not null THEN SUM(cl.collectionAmount) ELSE 0 END) as total_outstading 	from memos m 
						left join collections cl on cl.memo_id=m.id and cl.collectionDate BETWEEN '2018-10-01' and '" . $last_date_opening . "' 
						where m.sales_person_id IN($sales_person_id) 
						and m.office_id=$office_id 
						and m.memo_date BETWEEN '2018-10-01' and '" . $last_date_opening . "' 
						and m.credit_amount!=0
						and m.status>0
						group by m.id
						having max(m.gross_value) > CASE WHEN SUM(cl.collectionAmount) is not null THEN SUM(cl.collectionAmount) ELSE 0 END";
                        $opening_market_outstading_results = $this->Memo->query($sql);
                        //pr($opening_market_outstading_results);
                        $opening_market_outstading_sum = 0;
                        foreach ($opening_market_outstading_results as $r) {
                            $opening_market_outstading_sum += $r[0]['total_outstading'];
                        }
                        //echo $opening_market_outstading_sum;
                        //exit;
                        $opening_market_outstading[$sales_person_id] = $opening_market_outstading_sum;
                        //exit;
                        /*END NEW BY DELWAR*/

                        //exit;


                        /*$cash_credit_amount_sum = $this->Memo->query("select sum(memos.cash_recieved) AS total_cash,sum(memos.credit_amount) AS total_credit, sum(memos.total_discount) AS total_discount
                            from memos
                            where sales_person_id = ".$sales_person_id."
                            and office_id=$office_id
                            and status>0
                            and memo_date BETWEEN '".$date_range_first."' and '".$date_range_last."'");

                        $cash_credit_amount[$sales_person_id] = $cash_credit_amount_sum;*/
                        //sum(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN md.discount_amount ELSE 0 END) as discount
                        //sum(mm.cash_recieved-discount_m.discount) AS total_cash,
                        //Sum(mm.cash_recieved - discount_m.discount+discount_m.discount_sum) AS total_cash,


                        $so_terr = $this->Memo->query("select * from sales_people where id='" . $sales_person_id . "' ");
                        //echo '<pre>';print_r($so_terr);exit;
                        $ttid = $so_terr[0][0]['territory_id'];
                        //echo $ttid;exit;
                        ////mm.territory_id = ".$ttid."
                        // 481--WHEN dbp.is_discount_exclude_from_value=1  and m.is_distributor != 1 and m.memo_date>'2022-09-01' THEN
                        $newsql = "
							select 
							m.id as memo_id,
								
								Sum(md.sales_qty*CASE
                              WHEN dbp.is_discount_exclude_from_value=1   and m.memo_date>'2022-09-01' THEN
                               md.discount_amount
                               ELSE 0
                             END) AS discount,
							 Sum(md.sales_qty*CASE
                               WHEN (dbp.is_discount_exclude_from_value=0   and m.memo_date<'2022-09-01')or  m.is_distributor = 1 THEN
                               md.discount_amount
                               ELSE 0
                             END) AS discount_sum

								from memos m 
								left join memo_details md on md.memo_id=m.id
								left join discount_bonus_policies dbp on dbp.id=md.policy_id
								where 
								m.sales_person_id = " . $sales_person_id . " 
								and m.office_id=$office_id
								and m.status>0
								
								and m.memo_date BETWEEN '" . $date_range_first . "' and '" . $date_range_last . "'
								group by m.id
						";


                        $cash_credit_amount_sum = $this->Memo->query(" select 
						Sum(mm.cash_recieved - case when discount_m.discount is null then 0 else discount_m.discount end + case when discount_m.discount_sum is null then 0 else discount_m.discount_sum end) AS total_cash,
						sum(mm.credit_amount) AS total_credit
							from memos mm
							left join ($newsql) discount_m on mm.id=discount_m.memo_id
							where mm.sales_person_id = " . $sales_person_id . " 
							and mm.office_id=$office_id
							and mm.status>0
							and mm.memo_date BETWEEN '" . $date_range_first . "' and '" . $date_range_last . "'");

                        $cash_credit_amount[$sales_person_id] = $cash_credit_amount_sum;

                        //pr($cash_credit_amount);


                        //current period deposite
                        $current_deposit_sum = $this->Deposit->query("select sum(d.deposit_amount) AS total_current_deposit 
							from deposits d
							left join memos m on m.id=d.memo_id
							inner join territories t on (t.id=d.territory_id)
							left join weeks on d.week_id = weeks.id 
							where d.sales_person_id = " . $sales_person_id . " 
							and t.office_id=$office_id
							and m.id is null
							and d.deposit_date BETWEEN '" . $date_range_first . "' and '" . $date_range_last . "' 
							and weeks.start_date >= '" . $date_range_first . "' and weeks.start_date <= '" . $date_range_last . "'");
                        $current_deposit[$sales_person_id] = $current_deposit_sum;

                        //current period credit deposite
                        $current_deposit_sum = $this->Deposit->query("select sum(d.deposit_amount) AS total_current_deposit 
							from deposits d
							inner join memos m on m.id=d.memo_id
							inner join territories t on (t.id=d.territory_id)
							left join weeks on d.week_id = weeks.id 
							where m.sales_person_id = " . $sales_person_id . " 
							and t.office_id=$office_id
							and m.status>0
							and d.deposit_date BETWEEN '" . $date_range_first . "' and '" . $date_range_last . "' 
							and weeks.start_date >= '" . $date_range_first . "' and weeks.start_date <= '" . $date_range_last . "'");
                        $current_deposit[$sales_person_id][0][0]['total_current_deposit'] += $current_deposit_sum[0][0]['total_current_deposit'];


                        //previus period cash deposit
                        $sql = "select sum(d.deposit_amount) AS total_previous_deposit 
							from deposits d
							inner join territories t on (t.id=d.territory_id)
							left join memos m on m.id=d.memo_id
							left join weeks on d.week_id = weeks.id 
							where d.sales_person_id = " . $sales_person_id . " 
							and t.office_id=$office_id
							
							and m.id is null
							and d.deposit_date BETWEEN '" . $date_range_first . "' and '" . $date_range_last . "' 
							and weeks.start_date < '" . $date_range_first . "'";

                        // previus period instrument deposit
                        $previous_deposit_sum = $this->Deposit->query($sql);
                        $previous_deposit[$sales_person_id] = $previous_deposit_sum;
                        $sql = "select sum(d.deposit_amount) AS total_previous_deposit 
							from deposits d
							inner join territories t on (t.id=d.territory_id)
							inner join memos m on m.id=d.memo_id
							left join weeks on d.week_id = weeks.id 
							where m.sales_person_id = " . $sales_person_id . " 
							and m.status>0
							and t.office_id=$office_id
							and d.deposit_date BETWEEN '" . $date_range_first . "' and '" . $date_range_last . "' 
							and weeks.start_date < '" . $date_range_first . "'";

                        $previous_credit_deposit_sum = $this->Deposit->query($sql);

                        $previous_deposit[$sales_person_id][0][0]['total_previous_deposit'] = $previous_deposit[$sales_person_id][0][0]['total_previous_deposit'] + $previous_credit_deposit_sum[0][0]['total_previous_deposit'];

                        /*pr($previous_deposit[$sales_person_id]);
                        pr($previous_credit_deposit_sum);exit;*/

                        /*----------------------- Closing Followup : Start ---------------------------------*/
                        /*------------------------ Added by Naser---------------*/
                        /*
                            $sql = "select cl.*	from memos m
                            inner join collections cl on cl.memo_id=m.id and cl.collectionDate >'".$date_range_last."'
                            where m.sales_person_id IN($sales_person_id)
                            and m.office_id=$office_id
                            and m.memo_date BETWEEN '2018-10-01' and '".$date_range_last."'
                            and m.credit_amount!=0

                            ";
                            $closing_followup = $this->Memo->query($sql);
                            // echo $this->Memo->getLastquery();
                            // pr($closing_followup);exit;
                            $closing_followup_result=array();
                            $closing_followup_result[$sales_person_id]['collection_date']='';
                            $closing_followup_result[$sales_person_id]['total_collection_amount']=0;
                            foreach($closing_followup as $data)
                            {
                                $closing_followup_result[$sales_person_id]['collection_date'].='<br><b>'.date("d-M-y",strtotime($data[0]['collectionDate'])).'</b>';
                                $closing_followup_result[$sales_person_id]['total_collection_amount']+=$data[0]['collectionAmount'];
                            }
                        */

                        $sql = "select sum(c.collectionAmount) AS total_collection 
							from collections c
							inner join memos m on (m.id=c.memo_id)
							where c.so_id = " . $sales_person_id . " 
							and m.office_id=$office_id AND c.type=1
							and m.memo_date >= '2018-10-01'
							and m.status > 0
							and c.collectionDate BETWEEN '2018-10-01' and '" . $date_range_last . "'";
                        $total_collection = $this->Collection->query($sql);
                        $sql = "select sum(d.deposit_amount) AS total_deposit 
							from deposits d
							inner join territories t on (t.id=d.territory_id)
							inner join weeks on d.week_id = weeks.id 
							where d.sales_person_id = " . $sales_person_id . " 
							and t.office_id=$office_id AND d.type=1
							and weeks.start_date >= '2018-10-01'
							and d.deposit_date BETWEEN '2018-10-01' and '" . $date_range_last . "'";
                        $total_deposit = $this->Deposit->query($sql);
                        $cash_hands_of_so = $total_collection[0][0]['total_collection'] - $total_deposit[0][0]['total_deposit'];
                        /*pr($total_collection);
                        pr($total_deposit);
                        echo $cash_hands_of_so;exit;*/
                        $sql = "select d.deposit_amount AS total_deposit, d.deposit_date
							from deposits d
							inner join territories t on (t.id=d.territory_id)
							where d.sales_person_id = " . $sales_person_id . " 
							and t.office_id=$office_id AND d.type=1
							and d.deposit_date >'" . $date_range_last . "'";
                        $cash_deposit_find_for_hands_of_so = $this->Deposit->query($sql);
                        // $closing_followup_result=array();
                        // $closing_followup_result[$sales_person_id]['deposited_date']='';
                        $closing_followup_result[$sales_person_id]['total_deposit_amount'] = 0;
                        $closing_followup_result[$sales_person_id]['cash_hands_of_so'] = $cash_hands_of_so;
                        foreach ($cash_deposit_find_for_hands_of_so as $data) {
                            if ($closing_followup_result[$sales_person_id]['total_deposit_amount'] >= $cash_hands_of_so) {
                                $closing_followup_result[$sales_person_id]['total_deposit_amount'] = $cash_hands_of_so;
                                //echo 'break : '.$sales_person_id.'---'.$data[0]['total_deposit'].'---'.$data[0]['deposit_date'].'----'.$closing_followup_result[$sales_person_id]['total_deposit_amount'].'<br>';
                                break;
                            }
                            // echo $sales_person_id.'---'.$data[0]['total_deposit'].'---'.$data[0]['deposit_date'].'----'.$closing_followup_result[$sales_person_id]['total_deposit_amount'].'<br>';
                            $closing_followup_result[$sales_person_id]['deposited_date'][date("d-M-y", strtotime($data[0]['deposit_date']))] = date("d-M-y", strtotime($data[0]['deposit_date']));
                            $closing_followup_result[$sales_person_id]['total_deposit_amount'] += $data[0]['total_deposit'];
                        }
                        /*----------------------- Closing Followup : END ---------------------------------*/
                    }
                    //break;
                }
                // pr($so_list1);
                // pr($closing_followup_result);
                // pr($previous_deposit);
                // pr($collection_amount);
                // die();
            }


        }
        // pr($closing_followup_result);
        // die();
        // pr($sales_people);
        // exit;

        $this->set(compact('offices', 'office_id', 'sales_people', 'collection_amount', 'deposit_amount', 'sale_amount', 'cash_credit_amount', 'current_credit_collection', 'previous_credit_collection', 'current_deposit', 'previous_deposit', 'date_range_first', 'date_range_last', 'current_date', 'office_parent_id', 'opening_market_outstading', 'so_list1', 'so_offices', 'closing_followup_result'));
    }


    //xls download
    public function admin_dwonload_xls()
    {
        $request_data = $this->Session->read('request_data');
        $products = $this->Session->read('products');

        $product_quantity = $this->Session->read('product_quantity');
        $office_id = $request_data['Memo']['office_id'];


        $header = "";
        $data1 = "";


        foreach ($this->data['e_orders'] as $e_orders) {

            //echo $key;

            foreach ($e_orders as $key => $e_order) {
                $data1 .= ucfirst($key . "\t");
            }

            break;

        }

        //exit;

        /*$data1 .= ucfirst("Order Date,"); //for Tab Delimitated use \t
        $data1 .= ucfirst("Order ID,");
        $data1 .= ucfirst("Before Discount,");
        $data1 .= ucfirst("Discount,");
        $data1 .= ucfirst("Net Product Price,");
        $data1 .= ucfirst("Shipping Cost,");
        $data1 .= ucfirst("Sub Total,");
        $data1 .= ucfirst("7% Tax Collected,");
        $data1 .= ucfirst("3.5% Tax Collected,");
        $data1 .= ucfirst("Total,");

        $data1 .= ucfirst("7% Taxable Total,");
        $data1 .= ucfirst("3.5% Taxable Total,");
        $data1 .= ucfirst("Tax Exempt Total,");*/

        $data1 .= "\n";

        foreach ($this->data['e_orders'] as $row1) {
            $line = '';
            foreach ($row1 as $value) {
                if ((!isset($value)) or ($value == "")) {
                    $value = "\t"; //for Tab Delimitated use \t
                } else {
                    $value = str_replace('"', '""', $value);
                    $value = '"' . $value . '"' . "\t"; //for Tab Delimitated use \t
                }
                $line .= $value;
            }
            $data1 .= trim($line) . "\n";
        }


        $data1 = str_replace("\r", "", $data1);
        if ($data1 == "") {
            $data1 = "\n(0) Records Found!\n";
        }

        header("Content-type: application/vnd.ms-excel; name='excel'");
        header("Content-Disposition: attachment; filename=\"Sales-Collection-Deposit-Reports-" . date("jS-F-Y-H:i:s") . ".xls\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $data1;
        exit;

        $this->autoRender = false;
    }

    public function get_office_list()
    {
        $this->loadModel('Office');
        $rs = array(array('id' => '', 'name' => '---- All -----'));

        $parent_office_id = $this->request->data['region_office_id'];

        $office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id' => 2);

        $offices = $this->Office->find('all', array(
                'fields' => array('id', 'office_name'),
                'conditions' => $office_conditions,
                'order' => array('office_name' => 'asc'),
                'recursive' => -1
            )
        );

        $data_array = array();
        foreach ($offices as $office) {
            $data_array[] = array(
                'id' => $office['Office']['id'],
                'name' => $office['Office']['office_name'],
            );
        }

        //$data_array = Set::extract($offices, '{n}.Office');

        if (!empty($offices)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }

        $this->autoRender = false;
    }

    public function closing_market_outstanding()
    {
        //$request_data = $this->request->query['request_data'];
        $request_data = $this->Session->read('request_data');
        // pr($request_data);exit;
        $date_from = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_from']));
        $date = $date_to = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']));
        $office_id = $request_data['OutletSalesReports']['office_id'];
        $region_office_id = $request_data['OutletSalesReports']['region_office_id'];
        @$so_id = $request_data['OutletSalesReports']['so_id'];
        if ($so_id) {
            //$so_ids = array_keys($so_id);
            $so_ids = join(",", $so_id);
        }


        $office_conditions = array('office_type_id' => 2);
        if ($region_office_id) $office_conditions['parent_office_id'] = $region_office_id;
        if ($office_id) $office_conditions['id'] = $office_id;
        //pr($office_conditions);
        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions,
            //'fields'=>array('office_name')
        ));
        $office_id = array_keys($offices);

        $office_id = join(",", $office_id);

        $so_query = ($so_id) ? " and m.sales_person_id IN(" . $so_ids . ")" : '';

        $sql = "
			select
				max(m.memo_no) as memo_no, 
				max(m.gross_value) memo_value, 
				max(m.memo_date) as memo_date,
				max(o.name) as outlet,
				max(sp.name) as so_name,
				max(t.name) as territory_name,
				max(office.office_name) as office_name,
				m.office_id as office_id,
				SUM(cl.collectionAmount) as collection_amount,
				max(cl.collectionDate) as collection_date from memos m
					left join collections cl on cl.memo_id=m.id AND cl.collectionDate BETWEEN '2018-10-01' and '" . $date_to . "'
					
					inner join outlets o on o.id=m.outlet_id
					inner join sales_people sp on m.sales_person_id=sp.id
					inner join territories t on m.territory_id=t.id
					inner join offices office on m.office_id=office.id
					
					where m.office_id IN(" . $office_id . ") $so_query 
					and m.memo_date BETWEEN '2018-10-01' and '" . $date_to . "' 
					and m.credit_amount!=0
					
					group by m.id, m.office_id, m.memo_date, m.sales_person_id
					having  max(m.gross_value) > CASE 
							WHEN SUM(cl.collectionAmount) is not null THEN  SUM(cl.collectionAmount)
							ELSE  0
						END
				  order by m.office_id, m.sales_person_id, m.memo_date asc			
		";

        $due_memo = $this->Memo->query($sql);
        /*pr($due_memo);
        exit;*/

        $results = array();

        $total_due = 0;
        foreach ($due_memo as $data) {
            $results[$data[0]['office_name']][$data[0]['memo_no']] = array(
                'office_name' => $data[0]['office_name'],
                'office_id' => $data[0]['office_id'],
                'so_name' => $data[0]['so_name'],
                'territory_name' => $data[0]['territory_name'],
                'memo_no' => $data[0]['memo_no'],
                'memo_date' => $data[0]['memo_date'],
                'outlet' => $data[0]['outlet'],
                'memo_value' => $data[0]['memo_value'],
                'collection_amount' => $data[0]['collection_amount'],
                'collection_date' => $data[0]['collection_date'],
                'memo_date' => $data[0]['memo_date'],
            );
            $total_due += $data[0]['memo_value'] - $data[0]['collection_amount'];
        }


        $this->set(compact('due_memo', 'total_due', 'so_name', 'results'));

    }


    public function get_collection_date($memo_no = 0, $date_to = null)
    {
        $conditions = array();
        $conditions['Collection.memo_no'] = $memo_no;
        if ($date_to) {
            $conditions['Collection.collectionDate >'] = $date_to;
        }
        $result = $this->Collection->find('first', array(
            'fields' => array('Collection.collectionDate'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
        if ($result) return $result['Collection']['collectionDate'];
        return false;
    }


    public function get_territory_so_list()
    {
        $view = new View($this);

        $form = $view->loadHelper('Form');

        $office_id = $this->request->data['office_id'];


        //get SO list
        $so_list_r = $this->SalesPerson->find('all', array(
            'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
            'conditions' => array(
                'SalesPerson.office_id' => $office_id,
                'SalesPerson.territory_id >' => 0,
                //'User.user_group_id' => 4,
                'User.user_group_id' => array(4, 1008),
            ),
            'recursive' => 0
        ));


        foreach ($so_list_r as $key => $value) {
            $so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
        }

        //add old so
        /*@$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
        @$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));
        $conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type'=>2);

        if($date_from && $date_to){
            $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
        }

        $old_so_list = $this->TerritoryAssignHistory->find('all', array(
            'conditions' => $conditions,
            'order'=>  array('Territory.name'=>'asc'),
            'recursive'=> 0
        ));
        if($old_so_list){
            foreach($old_so_list as $old_so){
            $so_list[$old_so['SalesPerson']['id']] = $old_so['SalesPerson']['name'].' ('.$old_so['Territory']['name'].')';
            }
        }*/
        //end add old so


        if ($so_list) {
            $form->create('OutletSalesReports', array('role' => 'form', 'action' => 'index'));

            echo $form->input('so_id', array('label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $so_list));
            $form->end();

        } else {
            echo '';
        }


        $this->autoRender = false;
    }



}

