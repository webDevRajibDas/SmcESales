<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class BonusSummeryReportController extends AppController
{
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('Product', 'ProductType', 'Office', 'BonusCard', 'StoreBonusCard');

    public function admin_index()
    {
        ini_set('max_execution_time', 99999);
        ini_set('memory_limit', '-1');
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

        // $this->set(compact('offices','bonusCards','fiscalYears'));
        if ($this->request->is('post')) {
            $from_date = date('Y-m-d', strtotime($this->request->data['search']['date_from']));
            $to_date = date('Y-m-d', strtotime($this->request->data['search']['date_to']));
            $fiscal_year_id = $this->request->data['search']['fiscal_year_id'];
            $office_id = $this->request->data['search']['office_id'];
            $territory_id = $this->request->data['search']['territory_id'];
            $territory_id = implode(',', $territory_id);
            $condition = '';
            if ($territory_id) {
                $condition = "AND ter.id in ($territory_id)";
            }
            // pr($territory_id);exit;
            $bonus_card = $this->request->data['search']['bonus_card_id'];

            $sql = "SELECT SUM(sbc.quantity) as qty,SUM(sbc.no_of_stamp) as stamp,FORMAT(memo_date,'yyyy-MM') as month,ter.name as territory_name,ter.id as territory_id,sp.name  as sales_person,ot.name as outlets, mkt.name as market,th.name as thana,MIN(bc.min_qty_per_memo) as min_qty,ot.id as outlet_id FROM store_bonus_cards sbc
        					INNER JOIN outlets ot ON ot.id=sbc.outlet_id
        					INNER JOIN markets mkt on mkt.id=sbc.market_id
        					INNER jOIN thanas th on th.id=mkt.thana_id
        					INNER JOIN memos m ON m.id=sbc.memo_id
                            INNER JOIN territories ter on ter.id=mkt.territory_id
                            LEFT JOIN  sales_people sp on sp.territory_id=ter.id
                            INNER JOIN bonus_cards  bc ON bc.id=sbc.bonus_card_id
                            INNER jOIN bonus_eligible_outlets eo ON eo.outlet_id=ot.id and eo.bonus_card_id=sbc.bonus_card_id
        					WHERE sbc.bonus_card_id=$bonus_card AND m.memo_date BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND ter.office_id =$office_id $condition  AND eo.is_eligible=1
        					GROUP BY FORMAT(memo_date,'yyyy-MM'),ter.id,ter.name,sp.name,ot.name,mkt.name,th.name,ot.id
        					ORDER BY ter.id,FORMAT(memo_date,'yyyy-MM'),th.name,mkt.name
        	";
            $result = $this->StoreBonusCard->query($sql);
            // pr($result);die;
            $res_format_all = array();
            $calculated_outlet = array();
            foreach ($result as $data) {
                $res_format = array();
                $res_format['outlet'] = $data[0]['outlets'];
                $res_format['territory_name'] = $data[0]['territory_name'] . '(' . $data[0]['sales_person'] . ')';
                $res_format['territory_id'] = $data[0]['territory_id'];
                $res_format['outlet_id'] = $data[0]['outlet_id'];
                $res_format['market'] = $data[0]['market'];
                $res_format['thana'] = $data[0]['thana'];

                $total_qty = 0;
                $total_stamp = 0;
                if (!in_array($data[0]['outlet_id'], $calculated_outlet)) {

                    foreach ($result as $data1) {
                        if ($data[0]['outlet_id'] == $data1[0]['outlet_id']) {
                            // echo 'here'.$data[0]['outlet_id'].'---'.$data1[0]['outlet_id'].'<br>';
                            // echo array_search($data[0]['outlet_id'],$calculated_outlet).'<br>';

                            $res_format['sales_qty_' . $data1[0]['month']] = (isset($res_format['sales_qty_' . $data1[0]['month']]) ? $res_format['sales_qty_' . $data1[0]['month']] : 0) + $data1[0]['qty'];
                            //$res_format['stamp_'.$data1[0]['month']]=round($data1[0]['qty']/$data1[0]['min_qty'],2);
                            $res_format['stamp_' . $data1[0]['month']] = (isset($res_format['stamp_' . $data1[0]['month']]) ? $res_format['stamp_' . $data1[0]['month']] : 0) + $data1[0]['stamp'];

                            $total_qty += $data1[0]['qty'];
                            $total_stamp += $data1[0]['stamp'];
                        }
                    }
                    $calculated_outlet[] = $data[0]['outlet_id'];
                    $res_format['total_qty'] = $total_qty;
                    $res_format['total_stamp'] = $total_stamp;
                    $res_format_all[] = $res_format;
                    unset($res_format);
                    unset($data);
                }
            }
            $this->loadModel('Territory');
            // pr($res_format_all);die;
            $territory_list_find = $this->Territory->find('all', array(
                'fields' => array('Territory.id', 'SalesPerson.name', 'Territory.name'),
                'conditions' => array(
                    'Territory.office_id' => $office_id,
                ),
                'order' => array('Territory.id'),
                'recursive' => 0
            ));
            foreach ($territory_list_find as $key => $value) {
                $territory_list[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
            }
            $this->set(compact('territory_list'));
            $this->set('result', $res_format_all);

            $bonusCards = $this->BonusCard->find('list', array('conditions' => array('BonusCard.fiscal_year_id' => $fiscal_year_id)));
        } else {
            $bonusCards = $this->BonusCard->find('list');
        }
        $fiscalYears = $this->BonusCard->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('offices', 'bonusCards', 'fiscalYears'));
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
    public function get_territory_so_list()
    {
        $this->loadModel('Territory');
        $view = new View($this);

        $form = $view->loadHelper('Form');

        $office_id = $this->request->data['office_id'];

        //get SO list
        $territory_list = $this->Territory->find('all', array(
            'fields' => array('Territory.id', 'SalesPerson.name', 'Territory.name'),
            'conditions' => array(
                'Territory.office_id' => $office_id,
            ),
            'order' => array('Territory.id'),
            'recursive' => 0
        ));
        // pr($territory_list);exit;

        foreach ($territory_list as $key => $value) {
            $territory_list_box[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
        }
        // pr($territory_list);exit;

        if ($territory_list_box) {
            $form->create('search', array('role' => 'form', 'action' => 'index'));

            echo $form->input('territory_id', array('label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $territory_list_box));
            $form->end();
        } else {
            echo '';
        }


        $this->autoRender = false;
    }
}
