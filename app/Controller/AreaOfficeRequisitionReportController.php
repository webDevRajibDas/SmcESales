<?php
App::uses('AppController', 'Controller');

/**
 * Orders Controller
 *
 * @property Order $Order
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class AreaOfficeRequisitionReportController extends AppController
{

    public $uses = array('Order','Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'User', 'Combination', 'OrderDetail', 'MeasurementUnit');
    public $components = array('Paginator', 'Session', 'Filter.Filter');


    public function tsoReport()
    {
        $sales_qty = $this->Order->find('all', array(
            'fields' => array('Office.id', 'Office.office_name', 'Product.name', 'Product.id', 'MU.name', 'SUM(OrderDetail.sales_qty) as qty'),
            'group' => array('Office.id', 'Office.office_name', 'Product.name', 'Product.id', 'MU.name'),
            'joins' => array(
                array(
                    'table' => 'order_details',
                    'alias' => 'OrderDetail',
                    'conditions' => 'Order.id=OrderDetail.order_id',
                    'type' => 'inner'
                ),
                array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'conditions' => 'Product.id= case when (OrderDetail.virtual_product_id = 0 or OrderDetail.virtual_product_id is null) then OrderDetail.product_id else OrderDetail.virtual_product_id end ',
                    'type' => 'inner'
                ),
                array(
                    'table' => 'measurement_units',
                    'alias' => 'MU',
                    'type' => 'inner',
                    'conditions' => array('MU.id= OrderDetail.measurement_unit_id')
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'inner',
                    'conditions' => array('Office.id= Order.office_id')
                )
            ),
            'conditions' => array(
                'Order.order_date' =>'2023-03-30',
                'Order.office_id' =>18,
                'OrderDetail.price >'=>0
            ),
            'recursive' => -1
        ));


       // $this->dd($sales_qty);exit();



        $bonus_qty = $this->Order->find('all', array(
            'fields' => array('Office.id', 'Office.office_name', 'Product.name', 'Product.id', 'MU.name', 'SMU.name', 'SUM(OrderDetail.sales_qty) as qty',
                '
				ROUND(SUM(
				(ROUND(
						(CASE WHEN OrderDetail.price=0 THEN OrderDetail.sales_qty END) * 
						(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
					,0)) / 
					(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
					
				),2,1
				) AS bonus'
            ),
            'group' => array('Office.id', 'Office.office_name', 'Product.name', 'Product.id', 'MU.name', 'SMU.name'),
            'joins' => array(
                array(
                    'table' => 'order_details',
                    'alias' => 'OrderDetail',
                    'conditions' => 'Order.id=OrderDetail.order_id',
                    'type' => 'inner'
                ),
                array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'conditions' => 'Product.id= case when (OrderDetail.virtual_product_id = 0 or OrderDetail.virtual_product_id is null) then OrderDetail.product_id else OrderDetail.virtual_product_id end ',
                    'type' => 'inner'
                ),
                array(
                    'table' => 'measurement_units',
                    'alias' => 'SMU',
                    'type' => 'inner',
                    'conditions' => array('SMU.id= Product.sales_measurement_unit_id')
                ),
                array(
                    'alias' => 'ProductMeasurement',
                    'table' => 'product_measurements',
                    'type' => 'LEFT',
                    'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (OrderDetail.measurement_unit_id is null or OrderDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								OrderDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
                ),
                array(
                    'alias' => 'ProductMeasurementSales',
                    'table' => 'product_measurements',
                    'type' => 'LEFT',
                    'conditions' => '
							Product.id = ProductMeasurementSales.product_id 
							AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
                ),
                array(
                    'table' => 'measurement_units',
                    'alias' => 'MU',
                    'type' => 'inner',
                    'conditions' => array('MU.id= OrderDetail.measurement_unit_id')
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'inner',
                    'conditions' => array('Office.id= Order.office_id')
                )
            ),
            'conditions' => array(
                'Order.order_date' =>'2023-03-30',
                'Order.office_id' =>18,
                'OrderDetail.price <'=>1
            ),
            'recursive' => -1
        ));
        $bonus_array = array();

        foreach( $bonus_qty as $val ){

            $bonus_array[$val['Product']['id']] = array(
                'id'=>$val['Product']['id'],
                'office'=>$val['Office']['office_name'],
                'name'=>$val['Product']['name'],
                'b_uom'=>$val['MU']['name'],
                's_uom'=>$val['SMU']['name'],
                'sqty'=>0,
                'b_qty'=>$val[0]['qty'] + 0,
                's_qty'=>$val[0]['bonus'] + 0,
            );

        }
        $sales_array = array();
        foreach( $sales_qty as $val ){
            $bonus_info = $bonus_array[$val['Product']['id']];
            if(!empty($bonus_info)){
                $buom = $bonus_info['b_uom'];
                $bqty = $bonus_info['b_qty'];
                $bsqty = $bonus_info['s_qty'];

                unset($bonus_array[$val['Product']['id']]);
            }else{
                $buom = '';
                $bqty = 0;
                $bsqty = 0;
            }

            $sales_array[] = array(
                'id'=>$val['Product']['id'],
                'office'=>$val['Office']['office_name'],
                'name'=>$val['Product']['name'],
                's_uom'=>$val['MU']['name'],
                'b_uom'=>$buom,
                's_qty'=>$val[0]['qty'] + 0,
                'b_qty'=>$bqty,
                'b_s_qty'=>$bsqty,
            );

        }
        $this->set(compact('sales_array', 'bonus_array'));

        
    }
    


    public function admin_index(){
        $office_id = $this->UserAuth->getOfficeId();
        $sales_qty = $this->Order->find('all',array(
            'fields' => array('SUM(OrderDetail.sales_qty) as qty','Product.id','Product.name','MEUN.name','Office.office_name'),
            'group' => array('Product.name', 'Product.id','MEUN.name','Office.office_name'),
            'joins' => array(
                array(
                    'table' => 'order_details',
                    'alias' => 'OrderDetail',
                    'type' => 'INNER',
                    'conditions' => 'Order.id=OrderDetail.order_id'
                ),
                array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'type' => 'INNER',
                    'conditions' => 'Product.id = CASE when(OrderDetail.virtual_product_id = 0 or OrderDetail.virtual_product_id = null ) then OrderDetail.product_id else OrderDetail.virtual_product_id END',
                    //'conditions' => 'Product.id = OrderDetail.virtual_product_id',
                ),

                array(
                    'table' => 'measurement_units',
                    'alias' => 'MEUN',
                    'type' => 'INNER',
                    'conditions' => 'MEUN.id = OrderDetail.measurement_unit_id'
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'INNER',
                    'conditions' => 'Office.id = Order.office_id'
                ),
            ),
            'conditions' => array(
                //'Order.order_date' =>'2023-03-30',
                'OrderDetail.price >' => 0,
                'Office.id' => $office_id
            ),
            'recursive' => -1
        ));


        $bonus_qty = $this->Order->find('all',array(
            'fields' => array('SUM(OrderDetail.sales_qty) as qty','Office.office_name','MEUN.name','Product.name','Product.id','SMU.name'),
            'group' =>array('Office.id','Office.office_name','MEUN.name','Product.name','Product.id','SMU.name'),

            'joins' => array(
                array(
                    'table' => 'order_details',
                    'alias' => 'OrderDetail',
                    'type' => 'INNER',
                    'conditions' => 'Order.id = OrderDetail.order_id'
                ),
                array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'type' => 'INNER',
                    'conditions' => 'Product.id = CASE when(OrderDetail.virtual_product_id = 0 or OrderDetail.virtual_product_id = null ) then OrderDetail.product_id else OrderDetail.virtual_product_id END',

                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'inner',
                    'conditions' => array('Office.id= Order.office_id')
                ),
                array(
                    'table' => 'measurement_units',
                    'alias' => 'MEUN',
                    'type' => 'inner',
                    'conditions' => array('MEUN.id = OrderDetail.measurement_unit_id')
                ),
                array(
                    'table' => 'measurement_units',
                    'alias' => 'SMU',
                    'type' => 'inner',
                    'conditions' => array('SMU.id= Product.sales_measurement_unit_id')
                ),
                array(
                    'table' => 'product_measurements',
                    'alias' => 'ProdMeasUn',
                    'type' => 'left',
                    'conditions' => 'Product.id = ProdMeasUn.product_id AND CASE when(OrderDetail.measurement_unit_id = 0 or OrderDetail.measurement_unit_id = null) THEN Product.sales_measurement_unit_id ELSE OrderDetail.measurement_unit_id END = ProdMeasUn.measurement_unit_id'
                ),
                array(
                    'table' => 'product_measurements',
                    'alias' => 'ProductMeasurementSales',
                    'type' => 'LEFT',
                    'conditions' => '
							Product.id = ProductMeasurementSales.product_id 
							AND Product.sales_measurement_unit_id = ProductMeasurementSales.measurement_unit_id'
                ),
            ),
            'conditions' =>array(
                'OrderDetail.price >' => 0,
                'Office.id' =>  $office_id
            ),
            'recursive' => -1
        ));


        echo "<pre>";
        print_r($bonus_qty);exit();
        echo "</pre>";


        echo "<pre>";
        print_r($sales_qty);exit();
        echo "</pre>";


    }






    public function admin_index2()
    {
        //echo 'hello';exit;
        //pr($this->Session->read('Office.id'));die();
        $product_name = $this->Product->find('all', array(
            'fields' => array('Product.name', 'Product.id', 'MU.name as mes_name', 'Product.product_category_id'),
            'joins' => array(
                array(
                    'table' => 'measurement_units',
                    'alias' => 'MU',
                    'type' => 'LEFT',
                    'conditions' => array('MU.id= Product.sales_measurement_unit_id')
                )
            ),
            'conditions' => array('NOT' => array('Product.product_category_id' => 32)),
            'order' => 'Product.product_category_id',
            'recursive' => -1
        ));

        //pr($product_name);
        $requested_data = $this->request->data;
        //pr($requested_data);die();
        $this->set('page_title', 'Distributor Product Issues');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');

        $confirmation_status_optn = array(3 => 'Pending', 1 => 'Processing', 2 => 'Deliverd');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_group_id = $this->Session->read('Office.group_id');

        $designation_id = $this->Session->read('Office.designation_id');

        $this->set('office_parent_id', $office_parent_id);


        if ($office_parent_id == 0) {
            //$conditions = array('Order.confirm_status >'=> 0);
            $conditions = array('Order.confirmed >' => 0, 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array("id" => array(30, 31, 37))
            );
        } else {
            $conditions = array('Order.confirmed >' => 0, 'Order.office_id' => $this->Session->read('Office.id'), 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
            $office_conditions = array(
                'office_type_id' => 2,
                'id' => $this->Session->read('Office.id'),
            );
        }
        //pr($conditions);
        //exit;

        // pr($conditions);die();



        $group = array('Order.id', 'Order.order_no', 'Order.order_date', 'Order.confirmed', 'Order.from_app', 'Order.memo_reference_no', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.is_closed', 'Order.confirm_status', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.market_id');
        if (isset($requested_data['Order']['payment_status'])) {
            if ($requested_data['Order']['payment_status'] == 1) {
                $group = array(
                    'Order.id', 'Order.order_no', 'Order.order_date', 'Order.from_app', 'Order.memo_reference_no', 'Order.confirmed', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.is_closed', 'Order.confirm_status', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.market_id 
					HAVING SUM(Collection.collectionAmount) is null OR SUM(Collection.collectionAmount) < Order.gross_value'
                );
            } elseif ($requested_data['Order']['payment_status'] == 2) {
                $group = array('Order.id', 'Order.order_no', 'Order.order_date', 'Order.from_app', 'Order.memo_reference_no', 'Order.confirmed', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.is_closed', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.confirm_status', 'Order.market_id 
					HAVING SUM(Collection.collectionAmount) is not null AND SUM(Collection.collectionAmount) = Order.gross_value');
            }
        }

        $this->Order->recursive = 0;

        $conditions1 = array(
            "NOT" => array("Order.id" => strtotime("now"))
        );

        $conditions2 = array_merge($conditions, $conditions1);

        $this->paginate = array(
            'fields' => array(
                'Order.id', 'Order.order_no', 'Order.from_app', 'Order.order_date', 'Order.confirmed', 'Order.memo_reference_no', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.status', 'Order.is_closed', 'Order.memo_editable', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.market_id', 'Order.confirm_status'
                /*'CASE
                    WHEN SUM(Collection.collectionAmount) is null THEN 1
                    WHEN SUM(Collection.collectionAmount) < Order.gross_value THEN 1
                    ELSE 2 END as payment_status'*/
            ),
            'conditions' => $conditions2,
            'joins' => array(

                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'conditions' => 'Order.office_id=Office.id',
                    'type' => 'Left'
                ),
                array(
                    'table' => 'markets',
                    'alias' => 'Market',
                    'conditions' => 'Order.market_id=Market.id',
                    'type' => 'Left'
                ),
                array(
                    'table' => 'outlets',
                    'alias' => 'Outlet',
                    'conditions' => 'Order.outlet_id=Outlet.id',
                    'type' => 'Left'
                ),
                array(
                    'table' => 'dist_outlet_maps',
                    'alias' => 'DistOutletMap',
                    'conditions' => 'Order.outlet_id=DistOutletMap.outlet_id',
                    'type' => 'Left'
                ),
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'conditions' => 'Territory.id=Order.territory_id',
                    'type' => 'Left'
                ),

            ),
            'group' => $group,
            'order' => array('Order.status' => 'asc', 'Order.confirm_status' => 'asc'),
            'limit' => 100
        );


        //pr($this->paginate());exit;
        $this->set('orders', $this->paginate());

        //$order = array();
        //$this->set('orders', $order);

        $this->set('office_id', $this->UserAuth->getOfficeId());

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $office_id = isset($this->request->data['Order']['office_id']) != '' ? $this->request->data['Order']['office_id'] : 0;
        $territory_id = isset($this->request->data['Order']['territory_id']) != '' ? $this->request->data['Order']['territory_id'] : 0;
        $market_id = isset($this->request->data['Order']['market_id']) != '' ? $this->request->data['Order']['market_id'] : 0;
        $distribut_outlet_id = isset($this->request->data['Order']['distribut_outlet_id']) != '' ? $this->request->data['Order']['distribut_outlet_id'] : 0;
        $distributors = array();
        //pr($this->request->data);die();
        if ($office_id) {
            $this->loadModel('DistDistributor');
            $distributor_info = $this->DistDistributor->find('all', array(
                'conditions' => array(
                    'DistDistributor.office_id' => $office_id,
                    'DistDistributor.is_active' => 1
                ),
                'order' => array('DistDistributor.name' => 'asc'),
                // 'recursive'=> -1
            ));

            foreach ($distributor_info as $key => $value) {
                if ($value['DistOutletMap']['outlet_id'] != null) {
                    $distributors[$value['DistOutletMap']['outlet_id']] = $value['DistDistributor']['name'];
                }
            }
        }
        $this->loadModel('Territory');
        $territory = $this->Territory->find('all', array(
            'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => 0
        ));
        //$this->dd($territory);
        $data_array = array();

        foreach ($territory as $key => $value) {
            $t_id = $value['Territory']['id'];
            $t_val = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
            $data_array[$t_id] = $t_val;
        }

        $territories = $data_array;

        /*
        $territories = $this->Order->Territory->find('list', array(
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc')
            ));
        */

        if ($territory_id) {
            $markets = $this->Order->Market->find('list', array(
                'conditions' => array('Market.territory_id' => $territory_id),
                'order' => array('Market.name' => 'asc')
            ));
        } else {
            $markets = array();
        }

        $outlets = $this->Order->Outlet->find('list', array(
            'conditions' => array('Outlet.market_id' => $market_id),
            'order' => array('Outlet.name' => 'asc')
        ));
        // print_r($outlets);die();
        $this->loadModel('DistDistributor');
        $distributers = $this->DistDistributor->find('list', array(
            'conditions' => array('DistDistributor.office_id' => $office_id),
            'order' => array('DistDistributor.name' => 'asc')
        ));
        //print_r($distributers);die();
        $current_date = date('d-m-Y', strtotime($this->current_date()));

        /*
         * Report generation query start ;
         */
        if (!empty($requested_data)) {
            if (!empty($requested_data['Order']['office_id'])) {


                $office_id = $requested_data['Order']['office_id'];
                $this->Order->recursive = -1;
                $sales_people = $this->Order->find('all', array(
                    'fields' => array('DISTINCT(sales_person_id) as sales_person_id', 'SalesPerson.name'),
                    'joins' => array(
                        array(
                            'table' => 'sales_people',
                            'alias' => 'SalesPerson',
                            'type' => 'INNER',
                            'conditions' => array(
                                ' SalesPerson.id=Order.sales_person_id',
                                'SalesPerson.office_id' => $office_id
                            )
                        )
                    ),
                    'conditions' => array(
                        'Order.order_date BETWEEN ? and ?' => array(date('Y-m-d', strtotime($requested_data['Order']['date_from'])), date('Y-m-d', strtotime($requested_data['Order']['date_to'])))
                    ),
                ));

                $sales_person = array();
                foreach ($sales_people as  $data) {
                    $sales_person[] = $data['0']['sales_person_id'];
                }
                $sales_person = implode(',', $sales_person);

                //pr($sales_person);

                if (!empty($sales_person)) {
                    $product_quantity = $this->Order->query(" SELECT m.sales_person_id,md.product_id,SUM(md.sales_qty) as pro_quantity
					   FROM orders m RIGHT JOIN order_details md on md.order_id=m.id
					   WHERE (m.order_date BETWEEN  '" . date('Y-m-d', strtotime($requested_data['Order']['date_from'])) . "' AND '" . date('Y-m-d', strtotime($requested_data['Order']['date_to'])) . "') AND sales_person_id IN (" . $sales_person . ")  GROUP BY m.sales_person_id,md.product_id");
                    $this->set(compact('product_quantity', 'sales_people'));
                }
            }
        }

        $this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'distribut_outlet_id', 'requested_data', 'product_name', 'distributers', 'confirmation_status_optn', 'distributors'));
    }



    public function joinQuery(){
        $this->SenasaPedidosFacturadosSds->recursive = -1;

        $where = array(
          'joins' => array(
              array(
                  'table' => 'usuarios',
                  'alias' => 'Usuarios',
                  'type' => 'INNER',
                  'conditions' => array(
                      'Usuarios.usuario_id = SenasaPedidosFacturadosSds.usuarios_id'
                  )
              ),
              array(
                  'table' => 'senasa_pedidos',
                  'alias' => 'SenasaPedidos',
                  'type' => 'INNER',
                  'conditions' => array(
                      'SenasaPedidos.id = SenasaPedidosFacturadosSds.senasa_pedidos_id'
                  )
              ),
              array(
                  'table' => 'clientes',
                  'alias' => 'Clientes',
                  'type' => 'INNER',
                  'conditions' => array(
                      'Clientes.id_cliente = SenasaPedidos.clientes_id'
                  )
              ),

          ),
            'fields'=> array(
                'SenasaPedidosFacturadosSds.*',
                'Usuarios.usuario_id',
                'Usuarios.apellido_nombre',
                'Usuarios.senasa_establecimientos_id',
                'Clientes.id_cliente',
                'Clientes.consolida_doc_sanitaria',
                'Clientes.requiere_senasa',
                'Clientes.razon_social',
                'SenasaPedidos.id',
                'SenasaPedidos.domicilio_entrega',
                'SenasaPedidos.sds',
                'SenasaPedidos.pt_ptr'
            ),
            'conditions'=>array(
                'Clientes.requiere_senasa'=>1
            ),

            'order' => 'SenasaPedidosFacturadosSds.created DESC',
            'limit'=>100

        );

        $this->paginate = $where;
        $data = $this->Paginator->paginate();
        exit(debug($data));


        $result = $this->Order->find('all',array(
            'fields' => array('Order.field_name','Table2.field_name','Table3.field_name'),
            'groups' => array('Order.id','Table2.field_name','Table3.field_name'),
            'joins' => array(
                array(
                    'table'=>'table_name',
                    'alias'=>'Table2',
                    'type'=>'inner',
                    'conditions' => array('Order.id = Table2.id')
                ),

                array(
                    'table'=>'table_name',
                    'alias'=>'Table3',
                    'type'=>'inner',
                    'conditions' => array('Order.id = Table3.id')
                ),
            )

        ));

        print_r($result);exit();

        //Active conditions query
        //$this->SenasaPedidosFacturadosSds




    }











}
