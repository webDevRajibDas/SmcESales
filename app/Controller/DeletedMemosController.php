<?php
App::uses('AppController', 'Controller');

/**
 * DeletedMemos Controller
 *
 * @property DeletedMemo $DeletedMemo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DeletedMemosController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('DeletedMemo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'DeletedMemoDetail', 'MeasurementUnit');
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
        $product_name = $this->Product->find('all',array(
            'fields'=>array('Product.name','Product.id','MU.name as mes_name','Product.product_category_id'),
            'joins'=>array(
                array(
                    'table'=>'measurement_units',
                    'alias'=>'MU',
                    'type' => 'LEFT',
                    'conditions'=>array('MU.id= Product.sales_measurement_unit_id')
                    )
                ),
            'conditions'=>array('NOT'=>array('Product.product_category_id'=>32)),
            'order'=>'Product.product_category_id',
            'recursive'=>-1
            ));
        $this->set('page_title', 'Deleted Memo List');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->set('office_parent_id', $office_parent_id);
        if ($office_parent_id == 0) {
            $conditions = array('DeletedMemo.Memo_date >=' => $this->current_date() . ' 00:00:00', 'DeletedMemo.Memo_date <=' => $this->current_date() . ' 23:59:59');
            $office_conditions = array(
            'office_type_id' => 2,
            "NOT" => array( "id" => array(30, 31, 37))
            );
        } else {
            $conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId(), 'DeletedMemo.DeletedMemo_date >=' => $this->current_date() . ' 00:00:00', 'DeletedMemo.DeletedMemo_date <=' => $this->current_date() . ' 23:59:59');
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
        /*$group = array('DeletedMemo.id','DeletedMemo.Memo_no','DeletedMemo.from_app','DeletedMemo.Memo_reference_no','DeletedMemo.gross_value','DeletedMemo.DeletedMemo_time','DeletedMemo.credit_amount','DeletedMemo.DeletedMemo_editable','DeletedMemo.status','DeletedMemo.action','Outlet.name','Market.name','Territory.name','DeletedMemo.outlet_id','DeletedMemo.territory_id','DeletedMemo.market_id');*/
        /*$fields=array('DeletedMemo.id','DeletedMemo.Memo_no','DeletedMemo.from_app','DeletedMemo.Memo_reference_no','DeletedMemo.gross_value','DeletedMemo.Memo_time','DeletedMemo.credit_amount','DeletedMemo.status','DeletedMemo.Memo_editable','DeletedMemo.deleted_at','DeletedMemo.is_delete','DeletedMemo.action','Outlet.name','Market.name','Territory.name','DeletedMemo.outlet_id','DeletedMemo.territory_id','DeletedMemo.market_id',
            /*'CASE 
                WHEN SUM(Collection.collectionAmount) is null THEN 1 
                WHEN SUM(Collection.collectionAmount) < DeletedMemo.gross_value THEN 1 
                ELSE 2 END as payment_status');*/

        $fields=array('DeletedMemo.Memo_no','DeletedMemo.Memo_reference_no','Office.office_name','Outlet.name','Market.name','Territory.name','Thana.name','DeletedMemo.outlet_id','DeletedMemo.territory_id','DeletedMemo.market_id',' MAX(MAX([DeletedMemo].[is_delete])) OVER(PARTITION BY [DeletedMemo].[Memo_no] ORDER BY MAX([DeletedMemo].[deleted_at]) desc) as deletion_status');
        // pr($fields);exit;
        $group=array('DeletedMemo.Memo_no','DeletedMemo.Memo_reference_no','Office.office_name','Outlet.name','Market.name','Territory.name','Thana.name','DeletedMemo.outlet_id','DeletedMemo.territory_id','DeletedMemo.market_id');

        $joins=array(
            array(
                'table'=>'territories',
                'alias'=>'Territory',
                'conditions'=>'Territory.id=DeletedMemo.territory_id'
                ),
            array(
                'table'=>'outlets',
                'alias'=>'Outlet',
                'conditions'=>'Outlet.id=DeletedMemo.outlet_id'
                ),
            array(
                'table'=>'markets',
                'alias'=>'Market',
                'conditions'=>'Market.id=DeletedMemo.market_id'
                ),
            array(
                'table'=>'offices',
                'alias'=>'Office',
                'conditions'=>'Office.id=DeletedMemo.office_id'
                ),
            array(
                'table'=>'thanas',
                'alias'=>'Thana',
                'conditions'=>'Thana.id=DeletedMemo.thana_id'
                ),
            /*array(
                'table'=>'sales_people',
                'alias'=>'SalesPerson',
                'conditions'=>'SalesPerson.id=DeletedMemo.sales_person_id'
                ),*/
            );
        // $this->DeletedMemo->recursive = 0;
        $this->paginate = array(
            'fields'=>$fields,
            'conditions' => $conditions,
            'joins'=>$joins,
            'group'=>$group,
            /*'order' => array('DeletedMemo.id' => 'desc'),*/
            'limit' => 100,
            'recursive'=>-1
            );
        /*pr($this->paginate());
        echo $this->DeletedMemo->getLastQuery();
        exit;*/    
        $this->set('DeletedMemos', $this->paginate());
        $this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $office_id = isset($this->request->data['DeletedMemo']['office_id']) != '' ? $this->request->data['DeletedMemo']['office_id'] : 0;
        $territory_id = isset($this->request->data['DeletedMemo']['territory_id']) != '' ? $this->request->data['DeletedMemo']['territory_id'] : 0;
        $market_id = isset($this->request->data['DeletedMemo']['market_id']) != '' ? $this->request->data['DeletedMemo']['market_id'] : 0;
        
        
        $this->loadModel('Territory');
        $territory = $this->Territory->find('all', array(
            'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => 0
        ));
                    
        $data_array = array();
        
        foreach($territory as $key => $value)
        {
            $t_id=$value['Territory']['id'];
            $t_val=$value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
            $data_array[$t_id] =$t_val;
        }
                
         $territories =$data_array;
        
        /*
        $territories = $this->DeletedMemo->Territory->find('list', array(
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc')
            ));
        */
            
        if($territory_id){
            $markets = $this->DeletedMemo->Market->find('list', array(
            'conditions' => array('Market.territory_id' => $territory_id),
            'order' => array('Market.name' => 'asc')
            ));
        }else{
            $markets = array();
        }
            
        $outlets = $this->DeletedMemo->Outlet->find('list', array(
            'conditions' => array('Outlet.market_id' => $market_id),
            'order' => array('Outlet.name' => 'asc')
            ));
        $current_date = date('d-m-Y', strtotime($this->current_date()));

        /*
         * Report generation query start ;
         */
        if(!empty($requested_data)){
            if (!empty($requested_data['DeletedMemo']['office_id'])) {
                $office_id = $requested_data['DeletedMemo']['office_id'];
                $this->DeletedMemo->recursive=-1;
                $sales_people=$this->DeletedMemo->find('all',array(
                    'fields'=>array('DISTINCT(sales_person_id) as sales_person_id','SalesPerson.name'),
                    'joins'=>array(
                        array('table' => 'sales_people',
                            'alias' => 'SalesPerson',
                            'type' => 'INNER',
                            'conditions' => array(
                                ' SalesPerson.id=DeletedMemo.sales_person_id',
                                'SalesPerson.office_id'=>$office_id
                                )
                            )
                        ),
                    'conditions'=>array(
                        'DeletedMemo.DeletedMemo_date BETWEEN ? and ?'=>array(date('Y-m-d', strtotime($requested_data['DeletedMemo']['date_from'])),date('Y-m-d', strtotime($requested_data['DeletedMemo']['date_to'])))
                        ),
                    ));
                
                $sales_person=array();
                foreach ($sales_people as  $data) {
                    $sales_person[]=$data['0']['sales_person_id'];
                }
                $sales_person=implode(',', $sales_person);
       //pr($sales_person);
                if (!empty($sales_person)) {
                    $product_quantity=$this->DeletedMemo->query(" SELECT m.sales_person_id,md.product_id,SUM(md.sales_qty) as pro_quantity
                       FROM DeletedMemos m RIGHT JOIN DeletedMemo_details md on md.DeletedMemo_id=m.id
                       WHERE (m.DeletedMemo_date BETWEEN  '".date('Y-m-d', strtotime($requested_data['DeletedMemo']['date_from']))."' AND '". date('Y-m-d', strtotime($requested_data['DeletedMemo']['date_to']))."') AND sales_person_id IN (".$sales_person.")  GROUP BY m.sales_person_id,md.product_id");
                    $this->set(compact('product_quantity','sales_people'));
                }
            }
        }

        $this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'requested_data', 'product_name'));
    }
    
    
    

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($memo_no = null) {
        $this->set('page_title', 'DeletedMemo Details');
         $product_name = $this->Product->find('all',array(
            'fields'=>array('Product.name','Product.id','MU.name as mes_name','Product.product_category_id'),
            'joins'=>array(
                array(
                    'table'=>'measurement_units',
                    'alias'=>'MU',
                    'type' => 'LEFT',
                    'conditions'=>array('MU.id= Product.sales_measurement_unit_id')
                    )
                ),
            /*'conditions'=>array('NOT'=>array('Product.product_category_id'=>32)),*/
            'order'=>'Product.product_category_id',
            'recursive'=>-1
            ));
         $product=array();
         foreach($product_name as $data)
         {
            $product[$data['Product']['id']]=array(
                'name'=>$data['Product']['name'],
                'measurement_unit'=>$data['0']['mes_name'],
                'category_id'=>$data['Product']['product_category_id'],
                );
         }
        $DeletedMemo = $this->DeletedMemo->find('all', array(
            'conditions' => array('DeletedMemo.memo_no' => $memo_no),
            'order'=>array('DeletedMemo.deleted_at ASC'),
            /*'fields'=>array('DeletedMemo.*','SalesPerson.*','Outlet.*','Territory.*','Market.*','DeletedMemoDetail.*','Product.*'),
            'joins'=>array(
                array(
                    'table'=>'deleted_memo_details',
                    'alias'=>'DeletedMemoDetail',
                    'conditions'=>'DeletedMemo.id=DeletedMemoDetail.memo_id'
                    ),
                    array(
                    'table'=>'products',
                    'alias'=>'Product',
                    'conditions'=>'Product.id=DeletedMemoDetail.product_id'
                    ),
                )*/
            ));
        $this->DeletedMemo->unbindModel(array('hasMany' => array('DeletedMemoDetail')));
        $DeletedMemo_basic = $this->DeletedMemo->find('first', array(
            'conditions' => array('DeletedMemo.memo_no' => $memo_no),
            'joins'=>array(
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'conditions'=>'Office.id=DeletedMemo.office_id'
                    ),
                array(
                    'table'=>'thanas',
                    'alias'=>'Thana',
                    'conditions'=>'Thana.id=DeletedMemo.thana_id'
                    ),
                ),
            'fields'=>array('DeletedMemo.*','SalesPerson.*','Outlet.*','Territory.*','Market.*','Office.office_name','Thana.name'),
            ));
        $this->LoadModel('Memo');
        
        $current_memo=$this->Memo->find('first',array(
            'conditions'=>array('Memo.memo_no'=>$memo_no),
            'recursive'=>1
            ));
        // echo $this->Memo->getLastQuery();exit;
        // pr($current_memo);exit;
        $this->set(compact('DeletedMemo', 'DeletedMemo_basic','product','current_memo'));
    }
    
    

    /**
     * admin_delete method
     *
     * @return void
     */
  

   /* ----- ajax methods ----- */

   public function market_list() {
        $rs = array(array('id' => '', 'name' => '---- Select Market -----'));
        $thana_id = $this->request->data['thana_id'];
        //$thana_id = 2;
        $market_list = $this->Market->find('all', array(
            'conditions' => array('Market.thana_id' => $thana_id)
            ));
        $data_array = Set::extract($market_list, '{n}.Market');
        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }


   public function get_sales_officer_list() {
        $user_office_id = $this->UserAuth->getOfficeId();
        //$user_office_id = 2;
        $rs = array(array('id' => '', 'name' => '---- Select Market -----'));
        $sale_type_id = $this->request->data['sale_type_id'];
        //$sale_type_id = 1;
        if ($sale_type_id == 1 || $sale_type_id == 2 || $sale_type_id == 3 || $sale_type_id == 4) {
            $so_list = $this->SalesPerson->find('all', array(
                'conditions' => array('SalesPerson.office_id' => $user_office_id, 'User.user_group_id' => 4),
                'fields' => array('SalesPerson.id', 'SalesPerson.name')
                ));
            $person_list = array();
            foreach ($so_list as $key => $val) {
                $list['id'] = $val['SalesPerson']['id'];
                $list['name'] = $val['SalesPerson']['name'];
                $person_list[] = $list;
            }
        }
        if (!empty($person_list)) {
            echo json_encode(array_merge($rs, $person_list));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_outlet_list() {
        $rs = array(array('id' => '', 'name' => '---- Select outlet -----'));
        $market_id = $this->request->data['market_id'];
        $outlet_list = $this->Outlet->find('all', array(
            'conditions' => array('Outlet.market_id' => $market_id)
            ));
        $data_array = Set::extract($outlet_list, '{n}.Outlet');

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    function get_thana_by_territory_id()
    {
        $territory_id=$this->request->data['territory_id'];
        $output="<option value=''>--- Select Thana ---</option>";
        if($territory_id)
        {
            $thana=$this->Thana->find('list',array(
                'conditions'=>array('ThanaTerritory.territory_id'=>$territory_id),
                'joins'=>array(
                    array(
                        'table'=>'thana_territories',
                        'alias'=>'ThanaTerritory',
                        'conditions'=>'ThanaTerritory.thana_id=Thana.id'
                        )
                    )
                ));

            if($thana)
            {
                foreach($thana as $key=>$data)
                {
                    $output.="<option value='$key'>$data</option>";
                }
            }
        }
        
        echo $output;
        $this->autoRender=false;
    }
}



