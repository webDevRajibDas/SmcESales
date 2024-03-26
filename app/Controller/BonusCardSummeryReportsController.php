<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class BonusCardSummeryReportsController extends AppController
{
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('Product', 'ProductType', 'Office', 'BonusCard', 'StoreBonusCard', 'Memo', 'MemoDetail', 'Outlet', 'SalesPerson');

    public function admin_index()
    {
        $this->set('page_title', 'Bonus Summery Report');
        // $offices = $this->Office->find('list', array('conditions'=>array('Office.id !='=>14)));
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

        if (isset($region_office_condition)) {
            $region_offices = $this->Office->find('list', array(
                'conditions' => $region_office_condition,
                'order' => array('office_name' => 'asc')
            ));

            $this->set(compact('region_offices'));
        }
        $bonusCards = $this->BonusCard->find('list');

        if ($this->request->is('post')) {
            $from_date = date('Y-m-d', strtotime($this->request->data['search']['date_from']));
            $to_date = date('Y-m-d', strtotime($this->request->data['search']['date_to']));
            $office_id = $this->request->data['search']['office_id'];
            $bonus_card = $this->request->data['search']['bonus_card_id'];
            $fiscal_year_id = $this->request->data['search']['fiscal_year_id'];

            $sql = "SELECT COUNT(Distinct(m.id))as memo,min(md.price) as value,COUNT(Distinct(ot.id)) as outlet,SUM(sbc.quantity) as qty,SUM(sbc.no_of_stamp) as stamp,sp.name as sales_person,p.name as product,sp.id as so_id,p.id as p_id,ter.id as territory_id,ter.name as territory_name,ot.is_pharma_type FROM store_bonus_cards sbc
                            INNER JOIN outlets ot ON ot.id=sbc.outlet_id
                            INNER JOIN markets mkt on mkt.id=sbc.market_id
                            INNER JOIN memos m ON m.id=sbc.memo_id
                            INNER JOIN memo_details md on md.memo_id=m.id AND md.product_id=sbc.product_id and md.price>0
                            INNER JOIN products p ON p.id = sbc.product_id
                            INNER JOIN territories ter on ter.id=mkt.territory_id
                            LEFT JOIN  sales_people sp on sp.territory_id=ter.id
                            INNER JOIN bonus_eligible_outlets eo on eo.outlet_id=ot.id and eo.bonus_card_id=sbc.bonus_card_id
                            WHERE sbc.bonus_card_id=$bonus_card AND m.memo_date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND ter.office_id =$office_id AND eo.is_eligible=1
                            GROUP BY sp.id,sp.name,ter.id,ter.name,ot.is_pharma_type,p.name,p.id
                            ORDER BY ter.id,ot.is_pharma_type
            ";
            $result = $this->StoreBonusCard->query($sql);


            /*echo $this->StoreBonusCard->getLastQuery();
            pr($result);exit;*/
            $total_value['pharma'] = 0;
            $total_qty['pharma'] = 0;
            $total_stamp['pharma'] = 0;

            $total_value['non_pharma'] = 0;
            $total_qty['non_pharma'] = 0;
            $total_stamp['non_pharma'] = 0;
            $res_format_all = array();
            foreach ($result as $data) {
                /*$outlet=$this->Outlet->find('first',array(
                'fields'=>array('COUNT(Outlet.id) as outlet'),
                'conditions'=>array('market.territory_id'=>$data[0]['territory_id'],'bc.id'=>$bonus_card,'eo.is_eligible'=>1),
                'joins'=>array(
                    array(
                        'table' => 'markets',
                        'alias' => 'market',
                        'type' => 'INNER',
                        'conditions' => array(
                            'market.id = Outlet.market_id',
                            )
                        ),
                    array(
                        'table' => 'bonus_cards',
                        'alias' => 'bc',
                        'type' => 'INNER',
                        'conditions' => array(
                            'bc.bonus_card_type_id = Outlet.bonus_type_id',
                            )
                        ),
                    array(
                        'table' => 'bonus_eligible_outlets',
                        'alias' => 'eo',
                        'type' => 'INNER',
                        'conditions' => array(
                            'eo.outlet_id = Outlet.id',
                            )
                        ),
                    ),
                'recursive'=>-1
                )
                );*/
                // echo $this->Outlet->getLastQuery().'<br>';
                /*$memo = $this->Memo->find('first',array(
                    'fields'=>array('COUNT(Memo.id) as memo'),
                    'conditions'=>array('Memo.sales_person_id'=>$data[0]['so_id'],'md.product_id'=>$data[0]['p_id'], 'Memo.memo_date BETWEEN ? AND ?' => array($from_date, $to_date), ),
                    'joins'=>array(
                        array(
                        'table' => 'memo_details',
                        'alias' => 'md',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Memo.id = md.memo_id',
                            )
                        ),
                    array(
                        'table' => 'sales_people',
                        'alias' => 'sp',
                        'type' => 'INNER',
                        'conditions' => array(
                            'sp.id = Memo.sales_person_id',
                            )
                        ),
                        ),
                    'recursive'=>-1
                    ));*/
                $res_data['sales_person'] = $data[0]['sales_person'] ? $data[0]['sales_person'] : $data[0]['territory_name'];
                $res_data['territory_id'] = $data[0]['territory_id'];
                $res_data['outlet_type'] = $data[0]['is_pharma_type'] == 0 ? "Non-Pharma" : "Pharma";
                $res_data['outlet_type_id'] = $data[0]['is_pharma_type'];
                $res_data['value'] = $data[0]['value'] * $data[0]['qty'];
                $res_data['qty'] = $data[0]['qty'];
                $res_data['stamp'] = $data[0]['stamp'];
                $res_data['product'] = $data[0]['product'];
                // $res_data['outlet']=$outlet[0]['outlet'];
                $res_data['outlet'] = $data[0]['outlet'];
                // $res_data['memo']=$memo[0]['memo'];
                $res_data['memo'] = $data[0]['memo'];

                if ($res_data['outlet_type_id'] == 1) {
                    $total_value['pharma'] += $res_data['value'];
                    $total_qty['pharma'] += $res_data['qty'];
                    $total_stamp['pharma'] += $res_data['stamp'];
                } else {
                    $total_value['non_pharma'] += $res_data['value'];
                    $total_qty['non_pharma'] += $res_data['qty'];
                    $total_stamp['non_pharma'] += $res_data['stamp'];
                }
                $res_format_all[] = $res_data;
                unset($res_data);
            }
            // pr($res_format_all);die;
            $this->set('result', $res_format_all);
            $this->set(compact('total_value', 'total_qty', 'total_stamp'));
            $bonusCards = $this->BonusCard->find('list', array('conditions' => array('BonusCard.fiscal_year_id' => $fiscal_year_id)));
        } else {
            $bonusCards = $this->BonusCard->find('list');
        }

        $fiscalYears = $this->BonusCard->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('offices', 'bonusCards', 'fiscalYears'));
    }
    public function detail_bonus_card_report($territory_id, $bonus_id, $date_from, $date_to, $office_id, $fetch_all_territory = null, $outlet_type = null)
    {
        ini_set('max_execution_time', 99999);
        ini_set('memory_limit', '-1');
        if ($fetch_all_territory) {
            $condition = "ter.office_id =$office_id";
        } else {
            $condition = "ter.id =$territory_id";
        }
        $condition_outlet_type = '';
        if (isset($outlet_type)) {
            $condition_outlet_type = "AND ot.is_pharma_type=$outlet_type";
        }
        $sql = "SELECT md.price as value,sbc.quantity as qty,sbc.no_of_stamp as stamp,sp.name as sales_person,p.name as product,sp.id as so_id,p.id as p_id,ter.id as territory_id,ot.name as outlet,mt.name as market_name,th.name as thana_name,ot.id as outlet_id,m.memo_date,m.memo_no,ter.name as territory_name
       FROM store_bonus_cards sbc
       INNER JOIN outlets ot ON ot.id=sbc.outlet_id
       INNER JOIN markets mt ON mt.id=sbc.market_id
       INNER JOIN thanas th ON th.id=mt.thana_id
       INNER JOIN memos m ON m.id=sbc.memo_id
       INNER JOIN memo_details md on md.memo_id=m.id AND md.product_id=sbc.product_id  and md.price>0
       INNER JOIN products p ON p.id = sbc.product_id
       INNER JOIN territories ter on ter.id=mt.territory_id
       LEFT JOIN  sales_people sp on sp.territory_id=ter.id
       INNER JOIN bonus_eligible_outlets eo on eo.outlet_id=ot.id and eo.bonus_card_id=sbc.bonus_card_id
       WHERE sbc.bonus_card_id=$bonus_id AND m.memo_date BETWEEN '" . $date_from . "' AND '" . $date_to . "' AND  $condition AND eo.is_eligible=1 $condition_outlet_type
       ORDER BY ter.id,ot.name,mt.name,m.memo_date
       ";
        $result = $this->StoreBonusCard->query($sql);
        // echo $this->StoreBonusCard->getLastQuery();exit;
        // pr($result);exit;
        $data_array = array();
        $calculated_outlet = array();
        $j = 0;
        foreach ($result as $data) {
            // $res_format['outlet']=$data[0]['outlet'].'--'.$data[0]['market_name'].'--'.$data[0]['thana_name'];
            $res_format['product'] = '';
            /* if($j==0)
            {
                $res_format['product']=$data[0]['product'];
            }*/
            $res_format['outlet_id'] = $data[0]['outlet_id'];
            $total_qty = 0;
            $total_stamp = 0;
            $total_value = 0;
            if (!in_array($data[0]['outlet_id'], $calculated_outlet)) {
                $memo_data = array();
                $i = 0;
                foreach ($result as $data1) {
                    if ($data[0]['outlet_id'] == $data1[0]['outlet_id']) {
                        $memo['outlet'] = '';
                        $memo['product'] = '';
                        $memo['sales_people_id'] = $data[0]['so_id'] ? $data[0]['so_id'] : 'T' . $data[0]['territory_id'];
                        $memo['sales_people_name'] = $data[0]['sales_person'] ? $data[0]['sales_person'] : $data[0]['territory_name'];
                        if ($j == 0 && $i == 0) {
                            $memo['product'] = $data[0]['product'];
                        }
                        if ($i == 0) {
                            $memo['outlet'] = $data[0]['outlet'] . '--' . $data[0]['market_name'] . '--' . $data[0]['thana_name'];
                        }
                        $memo['inv_date'] = $data1[0]['memo_date'];
                        $memo['inv_no'] = $data1[0]['memo_no'];
                        $memo['qty'] = $data1[0]['qty'];
                        $memo['value'] = $data1[0]['qty'] * $data1[0]['value'];
                        $memo['stamp'] = $data1[0]['stamp'];
                        $total_qty += $memo['qty'];
                        $total_stamp += $memo['stamp'];
                        $total_value += $memo['value'];
                        $memo_data[] = $memo;
                        $i++;
                    }
                }
                $calculated_outlet[] = $data[0]['outlet_id'];
                $res_format['total_qty'] = $total_qty;
                $res_format['total_stamp'] = $total_stamp;
                $res_format['total_value'] = $total_value;
                $res_format[$data[0]['outlet_id']] = $memo_data;
                $data_array[] = $res_format;
                unset($res_format);
                unset($data);
            }
            $j++;
        }
        /* $so_info=$this->SalesPerson->find('first',array(
            'conditions'=>array('SalesPerson.territory_id'=>$territory_id),
            'recursive'=>-1
            ));*/
        $bonusCards = $this->BonusCard->find('list');
        $request_data['territory_id'] = $territory_id;
        $request_data['office_id'] = $office_id;
        $request_data['date_from'] = $date_from;
        $request_data['date_to'] = $date_to;
        $request_data['bonus_id'] = $bonus_id;
        $offices = $this->Office->find('list');
        // pr($data_array);exit;
        $this->set(compact('so_info', 'data_array', 'offices', 'request_data', 'bonusCards'));
        $this->set('page_title', 'Bonus Card Detail Report');
    }
    public function get_bonus_card()
    {
        $fiscal_year_id = $this->request->data['fiscal_year_id'];
        $bonusCards = $this->BonusCard->find('list', array('conditions' => array('BonusCard.fiscal_year_id' => $fiscal_year_id)));
        if ($bonusCards) {
            $output = '<option value="">---- Select Bonus Card ----</option>';
            foreach ($bonusCards as $key => $value) {
                $output .= "<option value='$key'>$value</option>";
            }
            echo $output;
        } else {
            echo '<option value="">---- Select Bonus Card ----</option>';
        }

        $this->autoRender = false;
    }
}
