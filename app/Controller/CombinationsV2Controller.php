<?php
App::uses('AppController', 'Controller');
/**
 * ProductCombinations Controller
 *
 * @property ProductCombination $ProductCombination
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CombinationsV2Controller extends AppController {

/**
 * Components
 *
 * @var array
 */
public $components = array('Paginator', 'Session');
/**
 * admin_index method
 *
 * @return void
 */
public function admin_index($id = null) 
{
	$this->loadModel('CombinationsV2');
	$this->loadModel('OutletCategory');
	$this->loadModel('DistOutletCategory');
	$this->set('page_title','Combination List');
	$this->CombinationsV2->recursive = 0;
	$joins= array();
	$conditions = array();
	$this->CombinationsV2->unbindModel(array(
		'belongsTo'=>array('SoOutletCategory','SrOutletCategory')
		));
	$this->paginate = array(	
		'conditions' => $conditions,
		'order'=>'CombinationsV2.id DESC',

		);
		$this->set('combinations', $this->paginate('CombinationsV2'));
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join combinations c on c.id=md.product_combination_id 
				where md.product_combination_id is not NULL 
				group by product_combination_id";
				
        $data_sql = $this->CombinationsV2->Query($sql);
		$com_ids=array();
		foreach ($data_sql as $each_data)
		{
			$id=$each_data[0]['com_id'];
		    $com_ids[$id]=$each_data[0]['id_num'];
		}
		/*$outlet_categories = $this->OutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$this->set(compact('outlet_categories'));
		
		$sr_outlet_categories = $this->DistOutletCategory->find('list',array('conditions'=>array('is_active'=>1)));

		$create_for=array('1'=>'SO','2'=>'SR','3'=>'DB','4'=>'SO Outlet Category','5'=>'SR Outlet Category','6'=>'SO Special Group','7'=>'SR Special Group');*/
		$this->set(compact('sr_outlet_categories','create_for'));
		$this->set('com_ids', $com_ids);
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Product Combination Details');
		if (!$this->ProductCombination->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('ProductCombination.' . $this->ProductCombination->primaryKey => $id));
		$this->set('productCombination', $this->ProductCombination->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Combination');
		$this->loadModel('CombinationsV2');
		$this->loadModel('CombinationDetailsV2');
		$this->loadModel('Product');
		$this->loadModel('ProductPricesV2');
		$this->loadModel('OutletCategory');
		$this->loadModel('DistOutletCategory');
		
		if ($this->request->is('post')) {
			// pr($this->request->data);exit;
			$this->request->data['Combination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['Combination']['effective_date']));
			$this->request->data['Combination']['created_at'] = $this->current_datetime(); 
			$this->request->data['Combination']['updated_at'] = $this->current_datetime(); 
			$this->request->data['Combination']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['Combination']['updated_by'] = $this->UserAuth->getUserId();
			$this->ProductPricesV2->create();
			$datasource = $this->CombinationsV2->getDataSource();
			try 
			{
			    $datasource->begin();
			    if(!$this->CombinationsV2->save($this->request->data['Combination']))
			    {
			    	throw new Exception();
			    }    
			    else
			    {
			    	$combination_id=$this->CombinationsV2->getInsertID();
			    	$insert_data['combination_id']=$combination_id;
			    	foreach($this->request->data['ProductCombination']['product_id'] as $key=>$p_id)
			    	{
			    		$insert_data['product_id']=$p_id;
			    		// $insert_data['product_combination_id']=$this->request->data['ProductCombination']['parent_slab_id'][$key];
			    		$this->CombinationDetailsV2->create();
			    		if(!$this->CombinationDetailsV2->save($insert_data))
					    {
					    	throw new Exception();
					    } 
			    	}
			    }
			    $datasource->commit();
			}
			catch(Exception $e) 
			{
			    $datasource->rollback();
			    $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			$this->Session->setFlash(__('Combinations Successfully Created'), 'flash/success');
				$this->redirect(array('action' => 'index'));
		}
		$products = $this->Product->find('list',array('order'=>array('Product.order' => 'ASC')));
		$this->html = '';
		foreach($products as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';		
		}
		$product_list = $this->html;

		/*$outlet_categories = $this->OutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$this->set(compact('outlet_categories'));
		
		$sr_outlet_categories = $this->DistOutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$this->set(compact('sr_outlet_categories'));

		$create_for=array('1'=>'SO','2'=>'SR','3'=>'DB','4'=>'SO Outlet Category','5'=>'SR Outlet Category','6'=>'SO Special Group','7'=>'SR Special Group');*/
		$this->set(compact('product_list','products','product_id','create_for'));
		
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null, $product_id = '') {
		$this->set('page_title','Edit Combination');
		$this->loadModel('CombinationsV2');
		$this->loadModel('CombinationDetailsV2');
		$this->loadModel('Product');
		$this->loadModel('ProductPricesV2');
		$this->loadModel('OutletCategory');
		$this->loadModel('DistOutletCategory');
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join combinations c on c.id=md.product_combination_id 
				where md.product_combination_id is not NULL 
				group by product_combination_id";
				
        $data_sql = $this->CombinationsV2->Query($sql);
		$com_ids=array();
		foreach ($data_sql as $each_data)
		{
			$ids=$each_data[0]['com_id'];
		    $com_ids[$ids]=$each_data[0]['id_num'];
		}
		$this->set('com_ids', $com_ids);
		$this->set('edit_id', $id);
		if ($this->request->is('post')) {
			// pr($this->request->data);exit;
			$this->request->data['Combination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['Combination']['effective_date'])); 
			$this->request->data['Combination']['updated_at'] = $this->current_datetime(); 
			$this->request->data['Combination']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Combination']['id'] = $id;
			$datasource = $this->CombinationsV2->getDataSource();
			try 
			{
			    $datasource->begin();
			    if(!$this->CombinationsV2->save($this->request->data['Combination']))
			    {
			    	throw new Exception();
			    }    
			    else
			    {
			    	$combination_id=$id;
			    	if(!$this->CombinationDetailsV2->deleteAll(array('CombinationDetailsV2.combination_id' => $combination_id), false))
			    	{
			    		throw new Exception();
			    	}
			    	$insert_data['combination_id']=$combination_id;
			    	foreach($this->request->data['ProductCombination']['product_id'] as $key=>$p_id)
			    	{
			    		$insert_data['product_id']=$p_id;
			    		// $insert_data['product_combination_id']=$this->request->data['ProductCombination']['parent_slab_id'][$key];
			    		$this->CombinationDetailsV2->create();
			    		if(!$this->CombinationDetailsV2->save($insert_data))
					    {
					    	throw new Exception();
					    } 
			    	}
			    }
			    $datasource->commit();
			}
			catch(Exception $e) 
			{
			    $datasource->rollback();
			    $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			$this->Session->setFlash(__('Combinations Successfully Created'), 'flash/success');
				$this->redirect(array('action' => 'index'));
		}
		$this->CombinationsV2->unbindModel(array(
		'belongsTo'=>array('SoOutletCategory','SrOutletCategory','SoSpecialGroup','SrSpecialGroup')
		));
		$options = array('conditions' => array('CombinationsV2.' . $this->CombinationsV2->primaryKey => $id));
		$this->request->data = $this->CombinationsV2->find('first', $options);
		$products_list = $this->Product->find('list',array('order'=>array('Product.order' => 'ASC')));
		
		
		
		$this->html = '';
		foreach($products_list as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';
		}
		$products = $this->html;
		/*$outlet_categories = $this->OutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$this->set(compact('outlet_categories'));
		
		$sr_outlet_categories = $this->DistOutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$this->set(compact('sr_outlet_categories'));

		$this->LoadModel('SpecialGroup');
		$price_effective_date=date('Y-m-d',strtotime($this->request->data['CombinationsV2']['effective_date']));
		
		$so_special_group=$this->SpecialGroup->find('list',array(
			'conditions'=>array('SpecialGroup.is_dist'=>0,'SpecialGroup.end_date >='=>$price_effective_date),
			'fields'=>array('SpecialGroup.id','SpecialGroup.name'),
			'order'=>array('SpecialGroup.id'),
			'recursive'=>-1
		));
		
		$sr_special_group=$this->SpecialGroup->find('list',array(
			'conditions'=>array('SpecialGroup.is_dist'=>1,'SpecialGroup.end_date >='=>$price_effective_date),
			'fields'=>array('SpecialGroup.id','SpecialGroup.name'),
			'order'=>array('SpecialGroup.id'),
			'recursive'=>-1
		));
		

		$create_for=array('1'=>'SO','2'=>'SR','3'=>'DB','4'=>'SO Outlet Category','5'=>'SR Outlet Category','6'=>'SO Special Group','7'=>'SR Special Group');*/
		$this->set(compact('slab_list','products_list','products','create_for','so_special_group','sr_special_group'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null,$product_id = null) {
		$this->loadModel('Combination');
		$this->loadModel('ProductCombination');
		$this->loadModel('ProductPrice');
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Combination->id = $id;
		if (!$this->Combination->exists()) {
			throw new NotFoundException(__('Invalid Combination'));
		}
		/*-------- start deleted row info ---------*/
		if(!empty($id)){
			$deleted_data = $this->Combination->find('first',array(
				'conditions' => array('Combination.id' => $id)
			));
			$combined_product_id = $deleted_data['Combination']['all_products_in_combination'];
			$combined_product_array = explode(',',$combined_product_id);
			$deleted_effective_date = $deleted_data['ProductCombination'][0]['effective_date'];
			if(!empty($deleted_data)){
			$delete_price_id_list = array();
				foreach($deleted_data['ProductCombination'] as $p_price_key=>$p_price_val){
					array_push($delete_price_id_list,$p_price_val['product_price_id']);
				}
			}
		}
		/*-------- end deleted row info ---------*/
		
		/*----- start prev date and data --------*/
		$this->Combination->unbindModel(
			array('hasMany' => array('ProductCombination'))
		);
		$prev_combination_date = $this->Combination->find('all',array(
			'conditions' => array(
				'Combination.all_products_in_combination' => $combined_product_id,
				'ProductCombination.effective_date <' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'ProductCombination',
					'table' => 'product_combinations',
					'type' => 'INNER',
					'conditions' => 'Combination.id = ProductCombination.combination_id'
				)
			),
			'fields' => array(
				'Combination.*','ProductCombination.*'
			)
		));
		if(!empty($prev_combination_date)){
			$prev_short_list = array();
			foreach($prev_combination_date as $prev_com_key=>$prev_com_val){
				$prev_com_val['ProductCombination']['effective_date'];
				array_push($prev_short_list,$prev_com_val['ProductCombination']['effective_date']);
			}
			asort($prev_short_list);
			$immediate_pre_date = array_pop($prev_short_list);
			/*---------- start prev data ----------*/
			$this->ProductPrice->unbindModel(
				array('hasMany' => array('ProductCombination'))
			);
			$previous_row_data = $this->ProductPrice->find('all',array(
				'conditions' => array(
					'ProductPrice.effective_date' => date('Y-m-d',strtotime($immediate_pre_date)),
					'ProductPrice.has_combination' => 1,
					'ProductPrice.institute_id' => 0,
					'ProductPrice.product_id' => $combined_product_array
				),
				'joins' => array(
					array(
						'alias' => 'ProductCombination',
						'table' => 'product_combinations',
						'type' => 'INNER',
						'conditions' => 'ProductPrice.id = ProductCombination.product_price_id'
					)
				),
				'fields' => array(
					'ProductPrice.*','ProductCombination.*'
				)
			));
			/*---------- end prev data ----------*/
			
		}
		/*----- end prev date and data --------*/
		
		/*------ start next date -------*/
		$this->Combination->unbindModel(
			array('hasMany' => array('ProductCombination'))
		);
		$next_combination_date = $this->Combination->find('all',array(
			'conditions' => array(
				'Combination.all_products_in_combination' => $combined_product_id,
				'ProductCombination.effective_date >' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'ProductCombination',
					'table' => 'product_combinations',
					'type' => 'INNER',
					'conditions' => 'Combination.id = ProductCombination.combination_id'
				)
			),
			'fields' => array(
				'Combination.*','ProductCombination.*'
			)
		));
		if(!empty($next_combination_date)){
			$next_short_list = array();
			foreach($next_combination_date as $next_com_key=>$next_com_val){
				array_push($next_short_list,$next_com_val['ProductCombination']['effective_date']);
			}
			rsort($next_short_list);
			$immediate_next_date = array_pop($next_short_list);
		}
		/*------ end next date -------*/

		if ($this->Combination->delete()) {
			$this->ProductPrice->deleteAll(array('id' => $delete_price_id_list), false);
			
			/*-------- start update prev by next ---------*/
			if(!empty($immediate_next_date) && !empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['ProductPrice']['id'] = $update_prev_data_val['ProductPrice']['id'];
					$update_prev_product_price['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
					if($this->ProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['ProductCombination']['id'] = $update_prev_data_val['ProductCombination']['id'];
						$update_prev_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
						$this->ProductCombination->save($update_prev_product_combination);
					}
				}
			}else if(!empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['ProductPrice']['id'] = $update_prev_data_val['ProductPrice']['id'];
					$update_prev_product_price['ProductPrice']['end_date'] = NULL;
					if($this->ProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['ProductCombination']['id'] = $update_prev_data_val['ProductCombination']['id'];
						$update_prev_product_combination['ProductCombination']['end_date'] = NULL;
						$this->ProductCombination->save($update_prev_product_combination);
					}
				}
			}
			/*-------- end update prev by next ---------*/
			
			$this->Session->setFlash(__('Combination deleted'), 'flash/success');
			$this->redirect(array('action' => 'index/'.$product_id));
		}
		$this->Session->setFlash(__('Combination was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index/'.$product_id));
	}
	
	
	public function get_product_list(){
		$rs = array(array('id' => '', 'title' => '---- Select Category -----'));
		$product_category_id = $this->request->data['product_category_id'];
        $product_list = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name as title'),
			'conditions' => array('Product.product_category_id' => $product_category_id),
			'recursive' => -1
		));
		$data_array = Set::extract($product_list, '{n}.Product');
		if(!empty($product_list)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
		
	}
	public function admin_get_slab_list(){
		$this->loadModel('ProductPricesV2');
		$this->loadModel('ProductCombinationsV2');
		$effective_date = date('Y-m-d',strtotime($this->request->data['effective_date']));
		$product_id = $this->request->data['product_id'];
		$create_for = $this->request->data['create_for'];
		
		/*------- start get prev effective date and next effective date ---------*/
		$product_price_id = $this->ProductPricesV2->find('first',
			array(
				'conditions' => array(
					'ProductPricesV2.product_id' => $product_id,
					'ProductPricesV2.effective_date <=' => $effective_date,
					'ProductPricesV2.institute_id'=>0,
					'ProductPricesV2.has_combination'=>0
					),
				'order'=>array('ProductPricesV2.effective_date desc'),
				'recursive'=>-1
			)
		);
		$price_id=$product_price_id['ProductPricesV2']['id'];
		if($create_for==1)
		{
			$condition_value['ProductPriceSectionV2.is_so'] = 1;
			$condition_value['ProductCombinationsV2.price >'] = 0;
		}
		elseif($create_for==2)
		{
			$condition_value['ProductPriceSectionV2.is_sr'] = 1;
			$condition_value['ProductCombinationsV2.sr_price >'] = 0;
		}
		elseif($create_for==3)
		{
			$condition_value['ProductPriceSectionV2.is_db'] = 1;
		}
		elseif($create_for==4)
		{
			$so_outlet_category = $this->request->data['so_outlet_category'];
			$condition_value['ProductPriceSectionV2.is_so'] = 1;
			$condition_value['ProductPriceOtherSlabs.price_for'] = 1; // 1=For SO
			$condition_value['ProductPriceOtherSlabs.type'] = 2; //2=outlet category
			$condition_value['ProductPriceOtherSlabs.reffrence_id'] = $so_outlet_category; 
		}
		elseif($create_for==5)
		{
			$sr_outlet_category = $this->request->data['sr_outlet_category'];
			$condition_value['ProductPriceSectionV2.is_sr'] = 1;
			$condition_value['ProductPriceOtherSlabs.price_for'] = 2; // 2=For SR
			$condition_value['ProductPriceOtherSlabs.type'] = 2; //2=outlet category
			$condition_value['ProductPriceOtherSlabs.reffrence_id'] = $sr_outlet_category; 
		}
		elseif($create_for==6)
		{
			$so_special_group = $this->request->data['so_special_group'];
			$condition_value['ProductPriceSectionV2.is_so'] = 1;
			$condition_value['ProductPriceOtherSlabs.price_for'] = 1; // 1=For SO
			$condition_value['ProductPriceOtherSlabs.type'] = 1; //1=Special Group
			$condition_value['ProductPriceOtherSlabs.reffrence_id'] = $so_special_group; 
		}
		elseif($create_for==7)
		{
			$sr_special_group = $this->request->data['sr_special_group'];
			$condition_value['ProductPriceSectionV2.is_sr'] = 1;
			$condition_value['ProductPriceOtherSlabs.price_for'] = 2; // 2=For SR
			$condition_value['ProductPriceOtherSlabs.type'] = 1; //1=Special Group
			$condition_value['ProductPriceOtherSlabs.reffrence_id'] = $sr_special_group; 
		}
		$condition_value['ProductCombinationsV2.product_price_id'] = $price_id;
		$condition_value['ProductCombinationsV2.product_id'] = $product_id;
		$condition_value['ProductCombinationsV2.combination_id'] = 0;
		/*------- end get prev effective date and next effective date ---------*/
		
		
		if(!empty($product_id)){
			$slab_list = $this->ProductCombinationsV2->find('all',array(
			'conditions' => array($condition_value),
			'joins'=>array(
					array(
						'table'=>'product_price_section_v2',
						'alias'=>'ProductPriceSectionV2',
						'conditions'=>'ProductPriceSectionV2.id=ProductCombinationsV2.section_id'
						),
					array(
						'table'=>'product_price_other_for_slabs_v2',
						'alias'=>'ProductPriceOtherSlabs',
						'conditions'=>'ProductCombinationsV2.id=ProductPriceOtherSlabs.product_combination_id',
						'type'=>'left'
						)
				),
			'group'=>array('ProductCombinationsV2.id','ProductCombinationsV2.effective_date','ProductCombinationsV2.min_qty'),
			'fields'=>array('ProductCombinationsV2.id','ProductCombinationsV2.effective_date','ProductCombinationsV2.min_qty'),
			'recursive'=>-1
			));
		}else{
			$slab_list = '';
		}
		$parent_slab_id = isset($this->request->data['parent_slab_id'])?$this->request->data['parent_slab_id']:0;
		
		$html = '<option value="">---- Select Slab ----</option>';
		foreach($slab_list as $key=>$val)
		{
			if($parent_slab_id==$val['ProductCombinationsV2']['id']){
				$html = $html.'<option selected value="'.$val['ProductCombinationsV2']['id'].'">'.$val['ProductCombinationsV2']['min_qty'].' ('.$val['ProductCombinationsV2']['effective_date'].') </option>';
			}else{
				// $html = $html.'<option value="'.$key.'">'.$val.'</option>';
				$html = $html.'<option value="'.$val['ProductCombinationsV2']['id'].'">'.$val['ProductCombinationsV2']['min_qty'].' ('.$val['ProductCombinationsV2']['effective_date'].') </option>';
			}
		}
		echo $html;
		$this->autoRender = false;
	}
	public function get_so_sr_special_group()
	{
		$this->LoadModel('SpecialGroup');
		$price_effective_date=date('Y-m-d',strtotime($this->request->data['effective_date']));
		$results_output=array();
		$so_special_group=$this->SpecialGroup->find('list',array(
			'conditions'=>array('SpecialGroup.is_dist'=>0,'SpecialGroup.end_date >='=>$price_effective_date),
			'fields'=>array('SpecialGroup.id','SpecialGroup.name'),
			'order'=>array('SpecialGroup.id'),
			'recursive'=>-1
		));
		$output = '<option value="">--- Select ---</option>';
		if($so_special_group)
		{	
			foreach($so_special_group as $key => $val)
			{
				$output.= '<option value="'.$key.'">'.$val.'</option>';
			}
		}
		$results_output['so_special']=$output;
		$sr_special_group=$this->SpecialGroup->find('list',array(
			'conditions'=>array('SpecialGroup.is_dist'=>1,'SpecialGroup.end_date >='=>$price_effective_date),
			'fields'=>array('SpecialGroup.id','SpecialGroup.name'),
			'order'=>array('SpecialGroup.id'),
			'recursive'=>-1
		));
		$output = '<option value="">--- Select ---</option>';
		if($sr_special_group)
		{	
			foreach($sr_special_group as $key => $val)
			{
				$output.= '<option value="'.$key.'">'.$val.'</option>';
			}
		}
		$results_output['sr_special']=$output;
		echo json_encode($results_output);
		exit;
	}
}
