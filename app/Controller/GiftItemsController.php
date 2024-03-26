<?php
App::uses('AppController', 'Controller');
/**
 * GiftItems Controller
 *
 * @property Doctor $Doctor
 * @property PaginatorComponent $Paginator
 */
class GiftItemsController extends AppController {

/**
 * Components
 *
 * @var array
 */
public $components = array('Paginator', 'Session', 'Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
public function admin_index() {
	$this->set('page_title','Gift Item List');
	$conditions = array();
	$office_parent_id = $this->UserAuth->getOfficeParentId();
	$territory_conditions=array();
	$thana_conditions=array();
	if($office_parent_id !=0)
	{
		$this->LoadModel('Office');
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
			$territory_conditions=array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			$thana_conditions=array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			$conditions[] = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			$region_office_condition=array('office_type_id'=>3,'Office.id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id'=>2);
		}
		elseif($office_type_id==2)
		{

			$territory_conditions=array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$thana_conditions=array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'office_type_id'=>2);
			$conditions[] = array('SalesPerson.office_id' => $this->UserAuth->getOfficeId());
		}

	}
	$this->LoadModel('Territory');
	$this->LoadModel('ThanaTerritory');
	$thanas=$this->ThanaTerritory->find('all',
		array(
			'conditions'=>$thana_conditions,
			'joins'=>array(
				array(
					'table'=>'territories',
					'alias'=>'Territory',
					'type'=>'inner',
					'conditions'=>'Territory.id=ThanaTerritory.territory_id'
					),
				array(
					'table'=>'offices',
					'alias'=>'Office',
					'type'=>'inner',
					'conditions'=>'Office.id=Territory.office_id'
					),
				),
			'fields'=>array('Thana.id','Thana.name')
			)
		);
	$thana_list=array();
	foreach($thanas as $key => $value)
	{
		$thana_list[$value['Thana']['id']]=$value['Thana']['name'];
	}
	/*$this->Territory->unbindModel(
		array(
			'belongsTo'=>array('Office')
			)
			);*/
			$territory_list_r = $this->Territory->find('all', array(
				'fields' => array('Territory.id','Territory.name','SalesPerson.name'),
				'conditions' => $territory_conditions,
				'recursive'=>0
				)); 
			foreach($territory_list_r as $key => $value)
			{
				$territory_list[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
			}
			$this->set(compact('territory_list','thana_list'));
			$this->LoadModel('Office');

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
			$this->set(compact('offices', 'office_id'));

			$this->LoadModel('ProductType');
			$product_type_list = $this->ProductType->find('list', array(
				'conditions'=>array(
					'NOT'=>array(
						'ProductType.id'=>1
					)
				)
			));
			$this->set(compact('product_type_list'));

			$this->GiftItem->recursive = 0;
			$this->GiftItem->virtualFields=array(
				'thana'=>'Thana.name',
				'territory'=>'Territory.name',
				'market'=>'Market.name',
				'product'=>'Product.name',
				'quantity'=>'GiftItemDetail.quantity',
				);

			$conditions = array();
			
			if($this->request->is('post')){
				$request_data = $this->request->data;
				
				if (!empty($request_data['GiftItem']['thana_id'])) {
					$conditions[] = array('Thana.id' => $request_data['GiftItem']['thana_id']);
				}
				elseif (!empty($request_data['GiftItem']['territory_id'])) {
					$conditions[] = array('Territory.id' => $request_data['GiftItem']['territory_id']);
				}
		
				elseif (!empty($request_data['GiftItem']['office_id'])) {
					$conditions[] = array('SalesPerson.office_id' => $request_data['GiftItem']['office_id']);
				}
				elseif (!empty($request_data['GiftItem']['region_office_id'])) {
					$conditions[] = array('Office.parent_office_id' => $request_data['GiftItem']['region_office_id']);
				}
		
				if (!empty($request_data['GiftItem']['so_id'])) {
					$conditions[] = array('GiftItem.so_id' => $request_data['GiftItem']['so_id']);
				}
				if (isset($request_data['GiftItem']['date_from'])!='') {
					$conditions[] = array('GiftItem.date >=' => Date('Y-m-d',strtotime($request_data['GiftItem']['date_from'])));
				}
				if (isset($request_data['GiftItem']['date_to'])!='') {
					$conditions[] = array('GiftItem.date <=' => Date('Y-m-d',strtotime($request_data['GiftItem']['date_to'])));
				}
				
				if (isset($request_data['GiftItem']['product_type'])!='') {
					$conditions[] = array('Product.product_type_id' => $request_data['GiftItem']['product_type']);
				}
				if (isset($request_data['GiftItem']['product_id'])!='') {
					$conditions[] = array('Product.id' => $request_data['GiftItem']['product_id']);
				}

			}
			
			//echo '<pre>';print_r($conditions);exit;

			$this->paginate = array(
				'fields'=>array('GiftItem.*','Outlet.*','SalesPerson.*','Market.name','Thana.name','Territory.name','GiftItemDetail.quantity','Product.name'),
				'order' => array('Product.product_category_id'=>'ASC', 'GiftItem.id' => 'DESC'),
				'conditions'=>$conditions,
				'joins'=>array(
					array(
						'table'=>'markets',
						'alias'=>'Market',
						'type'=>'Inner',
						'conditions'=>'Market.id=Outlet.market_id'
						),
					array(
						'table'=>'territories',
						'alias'=>'Territory',
						'type'=>'Inner',
						'conditions'=>'Territory.id=SalesPerson.territory_id'
						),
					array(
						'table'=>'thanas',
						'alias'=>'Thana',
						'type'=>'Inner',
						'conditions'=>'Thana.id=Market.thana_id'
						),
					array(
						'table'=>'offices',
						'alias'=>'Office',
						'type'=>'Inner',
						'conditions'=>'Office.id=SalesPerson.office_id'
						),
					array(
						'table'=>'gift_item_details',
						'alias'=>'GiftItemDetail',
						'type'=>'Inner',
						'conditions'=>'GiftItem.id=GiftItemDetail.gift_item_id'
						),
					array(
						'table'=>'products',
						'alias'=>'Product',
						'type'=>'Inner',
						'conditions'=>'Product.id=GiftItemDetail.product_id'
						),											
					),
				);
			// pr($this->paginate());exit;
			$this->set('gift_items', $this->paginate());

		}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_view($id = null) {
	$this->set('page_title','Gift Item Details');
	if (!$this->GiftItem->exists($id)) {
		throw new NotFoundException(__('Invalid doctor'));
	}
	$options = array('conditions' => array('GiftItem.' . $this->GiftItem->primaryKey => $id),'recursive'=>0);
	$this->set('GiftItem', $this->GiftItem->find('first', $options));

	$GiftItemDetails = $this->GiftItem->GiftItemDetail->find('all',array(
		'conditions' => array('GiftItemDetail.gift_item_id'=> $id),
		'recursive' => 0
		));
	$this->set('GiftItemDetails',$GiftItemDetails);			
}
public function get_territory_list()
	{
		$this->LoadModel('Office');
		$this->LoadModel('Territory');
		$view = new View($this);

		$form = $view->loadHelper('Form');  

		$office_id = $this->request->data['office_id'];

        //get territory list
		$this->Territory->unbindModel(
			array(
				'belongsTo'=>array('Office')
				)
			);
		$territory_list_r = $this->Territory->find('all', array(
			'fields' => array('Territory.id','Territory.name','SalesPerson.name'),
			'conditions' => array(
				'Territory.office_id' => $office_id,
				),
			'recursive'=>0
			)); 
		foreach($territory_list_r as $key => $value)
		{
			$territory_list[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
		}


		if(isset($territory_list))
		{   
			$form->create('GiftItem', array('role' => 'form', 'action'=>'filter'))    ;

			echo $form->input('territory_id', array('class' => 'form-control territory_id','empty'=>'--- Select---','options' => $territory_list));
			$form->end();

		}
		else
		{
			echo '';    
		}


		$this->autoRender = false;
	}
	public function get_thana_list()
	{
		
		$view = new View($this);

		$form = $view->loadHelper('Form');  

		$territory_id = $this->request->data['territory_id'];

        //get territory list
		
		$this->LoadModel('ThanaTerritory');
		$thanas=$this->ThanaTerritory->find('all',
			array(
				'conditions'=>array('ThanaTerritory.territory_id'=>$territory_id),
				'fields'=>array('Thana.id','Thana.name')
				)
			);
		$thana_list=array();
		foreach($thanas as $key => $value)
		{
			$thana_list[$value['Thana']['id']]=$value['Thana']['name'];
		}

		if(isset($thana_list))
		{   
			$form->create('GiftItem', array('role' => 'form', 'action'=>'filter'))    ;

			echo $form->input('thana_id', array('class' => 'form-control thana_id','empty'=>'--- Select---','options' => $thana_list));
			$form->end();

		}
		else
		{
			echo '';    
		}


		$this->autoRender = false;
	}
	function download_xl()
	{
		// pr($this->request->data);exit;
		$params=$this->request->data;

		$conditions = array();
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		
		if($office_parent_id !=0)
		{
			$this->LoadModel('Office');
			$office_type = $this->Office->find('first',array(
			'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
			'recursive'=>-1
			));
			$office_type_id = $office_type['Office']['office_type_id'];
		}
		if ($office_parent_id != 0)
		{
			if($office_type_id==3)
			{
				if(!empty($params['office_id'])) {
            		$conditions[] = array('SalesPerson.office_id' => $params['office_id']);
        		}
        		else
        		{
        			$conditions[] = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
        		}
				
			}
			elseif($office_type_id==2)
			{
				if(!empty($params['office_id'])) {
            		$conditions[] = array('SalesPerson.office_id' => $params['office_id']);
        		}
        		else
        		{
        			$conditions[] = array('SalesPerson.office_id' => $this->UserAuth->getOfficeId());
        		}
			}
		}else{
			if (!empty($params['office_id'])) {
				$conditions[] = array('SalesPerson.office_id' => $params['office_id']);
			}
		}
		
		if (!empty($params['thana_id'])) {
            $conditions[] = array('Thana.id' => $params['thana_id']);
        }
        elseif (!empty($params['territory_id'])) {
            $conditions[] = array('Territory.id' => $params['territory_id']);
        }


		if (!empty($params['so_id'])) {
            $conditions[] = array('so_id' => $params['so_id']);
        }
		if (!empty($params['date_from'])!='') {
            $conditions[] = array('date >=' => Date('Y-m-d',strtotime($params['date_from'])));
        }
		if (!empty($params['date_to'])!='') {
            $conditions[] = array('date <=' => Date('Y-m-d',strtotime($params['date_to'])));
        }

		if (!empty($params['product_type'])!='') {
			$conditions[] = array('Product.product_type_id' => $params['product_type']);
		}
		if (!empty($params['product_id'])!='') {
			$conditions[] = array('Product.id' => $params['product_id']);
		}
		
		//echo '<pre>';print_r($conditions);exit;

        $this->GiftItem->recursive = 0;
			$this->GiftItem->virtualFields=array(
				'thana'=>'Thana.name',
				'territory'=>'Territory.name',
				'market'=>'Market.name',
				'product'=>'Product.name',
				'quantity'=>'GiftItemDetail.quantity',
				);
        $gift_items=$this->GiftItem->find('all',
        	array(
				'fields'=>array('GiftItem.*','Outlet.*','SalesPerson.*','Market.name','Thana.name','Territory.name','GiftItemDetail.quantity','Product.name'),
				'order' => array('GiftItem.id' => 'DESC', 'Product.product_category_id'=>'ASC'),
				'conditions'=>$conditions,
				'joins'=>array(
					array(
						'table'=>'markets',
						'alias'=>'Market',
						'type'=>'Inner',
						'conditions'=>'Market.id=Outlet.market_id'
						),
					array(
						'table'=>'territories',
						'alias'=>'Territory',
						'type'=>'Inner',
						'conditions'=>'Territory.id=SalesPerson.territory_id'
						),
					array(
						'table'=>'thanas',
						'alias'=>'Thana',
						'type'=>'Inner',
						'conditions'=>'Thana.id=Market.thana_id'
						),
					array(
						'table'=>'offices',
						'alias'=>'Office',
						'type'=>'Inner',
						'conditions'=>'Office.id=SalesPerson.office_id'
						),
					array(
						'table'=>'gift_item_details',
						'alias'=>'GiftItemDetail',
						'type'=>'Inner',
						'conditions'=>'GiftItem.id=GiftItemDetail.gift_item_id'
						),
					array(
						'table'=>'products',
						'alias'=>'Product',
						'type'=>'Inner',
						'conditions'=>'Product.id=GiftItemDetail.product_id'
						),											
					),
				)
        	);
		$this->autoRender = false;
        // pr($gift_items);exit;
        /*echo $this->GiftItem->getLastQuery();
        pr($gift_items);exit;*/	
         $View = new View($this, false);
    	$View->set(compact('gift_items'));
    	$html = $View->render('download_xl');
    	echo $html;
	}
	public function get_office_list()
    {
    	$this->loadModel('Office');
    	$rs = array(array('id' => '', 'name' => '---- All -----'));

    	$parent_office_id = $this->request->data['region_office_id'];

    	$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id'=>2);

    	$offices = $this->Office->find('all', array(
    		'fields' => array('id', 'office_name'),
    		'conditions' => $office_conditions, 
    		'order' => array('office_name' => 'asc'),
    		'recursive' => -1
    		)
    	);

    	$data_array = array();
    	foreach($offices as $office){
    		$data_array[] = array(
    			'id'=>$office['Office']['id'],
    			'name'=>$office['Office']['office_name'],
    			);
    	}

		//$data_array = Set::extract($offices, '{n}.Office');

    	if(!empty($offices)){
    		echo json_encode(array_merge($rs, $data_array));
    	}else{
    		echo json_encode($rs);
    	} 

    	$this->autoRender = false;
    }


	function get_product_list()
	{
		$view = new View($this);

		$this->loadModel('Product');
		
        $form = $view->loadHelper('Form');	
		
		$product_types=@$this->request->data['GiftItem']['product_type'];
		//echo $product_types;exit;
		
		$conditions=array();
		if($product_types)
		{
			$conditions['product_type_id']=$product_types;
			$conditions['is_active']=1;

			$product_list = $this->Product->find('list', array(
				'conditions'=> $conditions,
				'order'=>  array('order'=>'asc')
			));
			if($product_list)
			{	
				$form->create('GiftItem', array('role' => 'form', 'action'=>'index'))	;
				
				echo $form->input('product_id', array('id' => 'product_id', 'label'=>false, 'class' => 'product_id checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $product_list)); 
				$form->end();
				
			}
		}else
		{
			echo '';	
		}

        $this->autoRender = false;

	}





}
