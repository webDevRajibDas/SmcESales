<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class DistBonusCardSummeryReportsController extends AppController 
{
	public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('Product','ProductType','Office','BonusCard','DistStoreBonusCard','DistMemo','DistMemoDetail','DistOutlet','SalesPerson');

    public function admin_index()
    {
        $this->set('page_title', 'Bonus Summery Report');
        // $offices = $this->Office->find('list', array('conditions'=>array('Office.id !='=>14)));
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if($office_parent_id!=0)
        {
            $office_type = $this->Office->find('first',array(
                'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
                'recursive'=>-1
                ));
            $office_type_id = $office_type['Office']['office_type_id'];
        }


        if ($office_parent_id == 0) {
            $region_office_condition=array('office_type_id'=>3);
            $office_conditions = array('office_type_id'=>2, "NOT" => array( "id" => array(30, 31, 37)));
        } 
        else 
        {
            if($office_type_id==3)
            {
                $region_office_condition=array('office_type_id'=>3,'Office.id' => $this->UserAuth->getOfficeId());
                $office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id'=>2);
            }
            elseif($office_type_id==2)
            {
                $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'office_type_id'=>2);
            }

        }

        $offices = $this->Office->find('list', array(
            'conditions'=> $office_conditions,
            'fields'=>array('office_name')
            ));

        if(isset($region_office_condition))
        {
            $region_offices = $this->Office->find('list', array(
                'conditions' => $region_office_condition, 
                'order' => array('office_name' => 'asc')
                ));

            $this->set(compact('region_offices'));
        }
        $bonusCards = $this->BonusCard->find('list');
        
        if($this->request->is('post'))
        {
            $from_date=date('Y-m-d',strtotime($this->request->data['search']['date_from']));
            $to_date = date('Y-m-d',strtotime($this->request->data['search']['date_to']));
            $office_id = $this->request->data['search']['office_id'];
            $bonus_card = $this->request->data['search']['bonus_card_id'];
            $fiscal_year_id=$this->request->data['search']['fiscal_year_id'];

            $sql="SELECT COUNT(Distinct(m.id))as memo,min(md.price) as value,COUNT(Distinct(ot.id)) as outlet,SUM(sbc.quantity) as qty,SUM(sbc.no_of_stamp) as stamp,rt.name as route_name,p.name as product,rt.id as route_id,p.id as p_id,ot.is_pharma_type, db.name as db_name, dsr.name as sr_name, dt.name as tso_name FROM dist_store_bonus_cards sbc
                            INNER JOIN dist_outlets ot ON ot.id=sbc.outlet_id
                            INNER JOIN dist_markets mkt on mkt.id=sbc.market_id
                            INNER JOIN dist_memos m ON m.id=sbc.memo_id
                            INNER JOIN dist_memo_details md on md.dist_memo_id=m.id AND md.product_id=sbc.product_id and md.price>0
                            INNER JOIN products p ON p.id = sbc.product_id
                            INNER JOIN dist_routes rt on rt.id=mkt.dist_route_id
                            INNER JOIN dist_bonus_eligible_outlets eo on eo.outlet_id=ot.id and eo.bonus_card_id=sbc.bonus_card_id
                            INNER JOIN dist_distributors  db ON db.id=m.distributor_id
                            INNER JOIN dist_sales_representatives  dsr ON dsr.id=m.sr_id
                            INNER JOIN dist_tsos  dt ON dt.id=m.tso_id
                            WHERE sbc.bonus_card_id=$bonus_card AND m.memo_date BETWEEN '".$from_date."' AND '".$to_date."' AND rt.office_id =$office_id AND eo.is_eligible=1
                            GROUP BY rt.id,rt.name,ot.is_pharma_type,p.name,p.id, db.name, dsr.name, dt.name
                            ORDER BY rt.name,ot.is_pharma_type
            ";
            $result = $this->DistStoreBonusCard->query($sql);


           // echo $this->StoreBonusCard->getLastQuery();
            //pr($result);exit;
            $total_value['pharma']=0;
            $total_qty['pharma']=0;
            $total_stamp['pharma']=0;

            $total_value['non_pharma']=0;
            $total_qty['non_pharma']=0;
            $total_stamp['non_pharma']=0;
            $res_format_all=array();
            foreach($result as $data)
            {
                $res_data['db_name']=$data[0]['db_name'];
                $res_data['sr_name']=$data[0]['sr_name'];
                $res_data['tso_name']=$data[0]['tso_name'];
                $res_data['route']=$data[0]['route_name'];
                $res_data['route_id']=$data[0]['route_id'];
                $res_data['outlet_type']=$data[0]['is_pharma_type']==0?"Non-Pharma":"Pharma";
                $res_data['outlet_type_id']=$data[0]['is_pharma_type'];
                $res_data['value']=$data[0]['value']*$data[0]['qty'];
                $res_data['qty']=$data[0]['qty'];
                $res_data['stamp']=$data[0]['stamp'];
                $res_data['product']=$data[0]['product'];
                // $res_data['outlet']=$outlet[0]['outlet'];
                $res_data['outlet']=$data[0]['outlet'];
                // $res_data['memo']=$memo[0]['memo'];
                $res_data['memo']=$data[0]['memo'];

                if($res_data['outlet_type_id']==1)
                {
                    $total_value['pharma']+=$res_data['value'];
                    $total_qty['pharma']+=$res_data['qty'];
                    $total_stamp['pharma']+=$res_data['stamp'];
                }
                else
                {
                    $total_value['non_pharma']+=$res_data['value'];
                    $total_qty['non_pharma']+=$res_data['qty'];
                    $total_stamp['non_pharma']+=$res_data['stamp'];
                }
                $res_format_all[]=$res_data;
                unset($res_data);
            }
            // pr($res_format_all);die;
            $this->set('result',$res_format_all);
            $this->set(compact('total_value','total_qty','total_stamp'));
            $bonusCards = $this->BonusCard->find('list',array('conditions'=>array('BonusCard.fiscal_year_id'=>$fiscal_year_id)));
        }
        else
        {
            $bonusCards = $this->BonusCard->find('list');
        }
        
        $fiscalYears = $this->BonusCard->FiscalYear->find('list',array('fields'=>array('year_code')));
        $this->set(compact('offices','bonusCards','fiscalYears'));
    }
    public function detail_bonus_card_report($route_id,$bonus_id,$date_from,$date_to,$office_id,$fetch_all_route=null,$outlet_type=null)
    {
        ini_set('max_execution_time', 99999);
        ini_set('memory_limit', '-1');
        if($fetch_all_route)
        {
            $condition="rt.office_id =$office_id";
        }
        else 
        {
            $condition="rt.id =$route_id";
        }
        $condition_outlet_type='';
        if(isset($outlet_type))
        {
            $condition_outlet_type="AND ot.is_pharma_type=$outlet_type";
        }
       $sql="SELECT md.price as value,sbc.quantity as qty,sbc.no_of_stamp as stamp,p.name as product,p.id as p_id,rt.id as route_id,ot.name as outlet,mt.name as market_name,rt.name as route_name,ot.id as outlet_id,m.memo_date,m.dist_memo_no
       FROM dist_store_bonus_cards sbc
       INNER JOIN dist_outlets ot ON ot.id=sbc.outlet_id
       INNER JOIN dist_markets mt ON mt.id=sbc.market_id
       INNER JOIN dist_routes rt ON rt.id=mt.dist_route_id
       INNER JOIN dist_memos m ON m.id=sbc.memo_id
       INNER JOIN dist_memo_details md on md.dist_memo_id=m.id AND md.product_id=sbc.product_id AND md.price>0
       INNER JOIN products p ON p.id = sbc.product_id
       INNER JOIN dist_bonus_eligible_outlets eo on eo.outlet_id=ot.id and eo.bonus_card_id=sbc.bonus_card_id
       WHERE sbc.bonus_card_id=$bonus_id AND m.memo_date BETWEEN '".$date_from."' AND '".$date_to."' AND  $condition AND eo.is_eligible=1 $condition_outlet_type
       ORDER BY rt.name,ot.name,mt.name,m.memo_date
       "; 
       $result = $this->DistStoreBonusCard->query($sql);
       /*echo $this->DistStoreBonusCard->getLastQuery();
       pr($result);exit; */ 
       $data_array=array();
       $calculated_outlet=array();
       $j=0;
        foreach($result as $data)
        {
            // $res_format['outlet']=$data[0]['outlet'].'--'.$data[0]['market_name'].'--'.$data[0]['thana_name'];
            $res_format['product']='';
           /* if($j==0)
            {
                $res_format['product']=$data[0]['product'];
            }*/
            $res_format['outlet_id']=$data[0]['outlet_id'];
            $total_qty=0;
            $total_stamp=0;
            $total_value=0;
            if(!in_array($data[0]['outlet_id'], $calculated_outlet))
            {
                $memo_data=array();
                $i=0;
                foreach($result as $data1)
                {
                    if($data[0]['outlet_id']==$data1[0]['outlet_id'])
                    {
                        $memo['outlet']='';
                        $memo['product']='';
                        $memo['route_name']=$data[0]['route_name'];
                        $memo['route_id']=$data[0]['route_id'];
                        if($j==0 && $i==0)
                        {
                            $memo['product']=$data[0]['product'];
                        }
                        if($i==0)
                        {
                            $memo['outlet']=$data[0]['outlet'].'--'.$data[0]['market_name'].'--'.$data[0]['route_name'];
                        }
                        $memo['inv_date']=$data1[0]['memo_date'];
                        $memo['inv_no']=$data1[0]['dist_memo_no'];
                        $memo['qty']=$data1[0]['qty'];
                        $memo['value']=$data1[0]['qty']*$data1[0]['value'];
                        $memo['stamp']=$data1[0]['stamp'];
                        $total_qty+=$memo['qty'];
                        $total_stamp+=$memo['stamp'];
                        $total_value+=$memo['value'];
                        $memo_data[]=$memo;
                        $i++;
                    }
                }
                $calculated_outlet[]=$data[0]['outlet_id'];
                $res_format['total_qty']=$total_qty;
                $res_format['total_stamp']=$total_stamp;
                $res_format['total_value']=$total_value;
                $res_format[$data[0]['outlet_id']]=$memo_data;
                $data_array[]=$res_format;
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
        $request_data['route_id']=$route_id;
        $request_data['office_id']=$office_id;
        $request_data['date_from']=$date_from;
        $request_data['date_to']=$date_to;
        $request_data['bonus_id']=$bonus_id;
        $offices=$this->Office->find('list');
        // pr($data_array);exit;
        $this->set(compact('so_info','data_array','offices','request_data','bonusCards'));
        $this->set('page_title', 'Bonus Card Detail Report');
    }
    public function get_bonus_card()
    {
        $fiscal_year_id=$this->request->data['fiscal_year_id'];
        $bonusCards = $this->BonusCard->find('list',array('conditions'=>array('BonusCard.fiscal_year_id'=>$fiscal_year_id)));
        if($bonusCards)
        {   
            $output='<option value="">---- Select Bonus Card ----</option>';
            foreach($bonusCards as $key=>$value)
            {
                $output.="<option value='$key'>$value</option>";
            }
            echo $output;
        }
        else
        {
            echo '<option value="">---- Select Bonus Card ----</option>';   
        }

        $this->autoRender=false;
    }
}