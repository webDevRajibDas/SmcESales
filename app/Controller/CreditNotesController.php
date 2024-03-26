<?php
App::uses('AppController', 'Controller');
/**
 * ProxySells Controller
 *
 * @property ProxySell $ProxySell
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CreditNotesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
public $components = array('Paginator', 'Session', 'Filter.Filter');
	public $uses = array('CreditNote', 'CreditNoteDetail', 'Office', 'Market', 'Territory', 'Outlet', 'Memo', 'MemoDetail');
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title','Credit Note List');
		$this->CreditNote->recursive = 0;
		$this->paginate = array(
			'joins'=>array(
				array(
					'alias'=>'Office',
					'table'=>'offices',
					'type'=>'left',
					'conditions'=>'Office.id=CreditNote.office_id'
				),
				array(
					'alias'=>'Territory',
					'table'=>'territories',
					'type'=>'left',
					'conditions'=>'Territory.id=CreditNote.territory_id'
				),
				array(
					'alias'=>'Market',
					'table'=>'markets',
					'type'=>'left',
					'conditions'=>'Market.id=CreditNote.market_id'
				),
				array(
					'alias'=>'Outlet',
					'table'=>'outlets',
					'type'=>'left',
					'conditions'=>'Outlet.id=CreditNote.outlet_id'
				),
			),
			'fields'=>array(
				'CreditNote.*',
				'Office.office_name',
				'Territory.name',
				'Market.name',
				'Outlet.name',
			),
			'order' => array('CreditNote.id' => 'DESC'),
			'recursive'=>-1
		);		
		$this->set('creditnotes', $this->paginate());

		$office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
	
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
    
		$this->set(compact('offices'));

	}

	public function admin_view($id){

		$this->set('page_title','Credit Note View');

        $this->CreditNote->id = $id;
		if (!$this->CreditNote->exists($id)) {
			throw new NotFoundException(__('Invalid Credit Note'));
		}

		$credit_info = $this->CreditNote->find('first', array(
			'conditions'=>array(
				'CreditNote.id'=>$id
			),
			'joins'=>array(
				array(
					'alias'=>'Outlet',
					'table'=>'outlets',
					'type'=>'left',
					'conditions'=>'Outlet.id=CreditNote.outlet_id',
				)
			),
			'fields'=>array(
				'CreditNote.*',
				'Outlet.name',
				'Outlet.address',
			),
			'recursive'=>-1
		));

		$exiting_data = $this->CreditNoteDetail->find('all', array(
			'conditions'=>array(
				'CreditNoteDetail.credit_note_id'=>$id
			),
			'joins'=>array(
				/* array(
					'alias'=>'CreditNote',
					'table'=>'credit_notes',
					'type'=>'left',
					'conditions'=>'CreditNote.id=CreditNoteDetail.credit_note_id'
				), */
				array(
					'alias'=>'MemoDetail',
					'table'=>'memo_details',
					'type'=>'left',
					'conditions'=>'MemoDetail.id=CreditNoteDetail.memo_details_id'
				),
				array(
					'alias'=>'Memo',
					'table'=>'memos',
					'type'=>'left',
					'conditions'=>'Memo.id=MemoDetail.memo_id'
				),
				array(
					'alias'=>'Product',
					'table'=>'products',
					'type'=>'left',
					'conditions'=>'Product.id=MemoDetail.product_id'
				) 
				
			),
			'fields'=>array(
				//'Memo.id', 
				//'MemoDetail.id',
				'Product.name',
				'Memo.memo_date', 
				'Memo.memo_no', 
				'MemoDetail.product_id', 
				'MemoDetail.sales_qty', 
				'SUM(MemoDetail.sales_qty * MemoDetail.price) as value',
				'SUM(CreditNoteDetail.return_qty * MemoDetail.price) as r_value',
				'CreditNoteDetail.return_qty',
				'CreditNoteDetail.reason',
			),
			'group'=>array(
				'Product.name',
				'Memo.memo_date', 
				'Memo.memo_no', 
				'MemoDetail.product_id', 
				'MemoDetail.sales_qty',
				'CreditNoteDetail.return_qty',
				'CreditNoteDetail.reason',
			),
			'recursive'=>-1

		));

		$this->set(compact('exiting_data', 'credit_info'));

		//echo '<pre>';print_r($credit_info);exit;

	}


	public function admin_add()
	{	

		
		$this->LoadModel('CreditNoteDetail');

		if ($this->request->is('post')) {			
			
			//echo '<pre>';print_r($this->request->data);exit;

			$creditnote['CreditNote']['office_id'] = $this->request->data['CreditNote']['office_id'];
			$creditnote['CreditNote']['territory_id'] = $this->request->data['CreditNote']['territory_id'];
			$creditnote['CreditNote']['market_id'] = $this->request->data['CreditNote']['market_id'];
			$creditnote['CreditNote']['outlet_id'] =$this->request->data['CreditNote']['outlet_id'];
			$creditnote['CreditNote']['created_at'] = $this->current_datetime();
			$creditnote['CreditNote']['created_by'] = $this->UserAuth->getUserId();
			$creditnote['CreditNote']['updated_at'] = $this->current_datetime();
			$creditnote['CreditNote']['updated_by'] = $this->UserAuth->getUserId();
			
			$this->CreditNote->create();
			if ($this->CreditNote->save($creditnote)) 
			{	
				$id = $this->CreditNote->id;
				$udata['id'] = $id;
				$udata['credit_number'] = 'CN' . (10000 + $this->CreditNote->id);
				$this->CreditNote->save($udata);
				$insertCnDetilas = array();
				foreach($this->request->data['memo_no_check'] as $key => $v){

					$md_id = $this->request->data['memo_no_check'][$key];
					$return_qty = $this->request->data['return_qty'][$key];
					$reason = $this->request->data['reason'][$key];

					$cndetails['CreditNoteDetail']['credit_note_id'] = $id;
					$cndetails['CreditNoteDetail']['memo_details_id'] = $md_id;
					$cndetails['CreditNoteDetail']['return_qty'] = $return_qty;
					$cndetails['CreditNoteDetail']['reason'] = $reason;
					
					$cndetails['CreditNoteDetail']['created_at'] = $this->current_datetime();
					$cndetails['CreditNoteDetail']['created_by'] = $this->UserAuth->getUserId();
					$cndetails['CreditNoteDetail']['updated_at'] = $this->current_datetime();
					$cndetails['CreditNoteDetail']['updated_by'] = $this->UserAuth->getUserId();
					$insertCnDetilas[] = $cndetails;
				}	

				if(!empty($insertCnDetilas)){
					$this->CreditNoteDetail->saveAll($insertCnDetilas); 
				}

				
			}
			
			$this->Session->setFlash(__('Credit Note has been successfully added.'), 'flash/success');
			$this->redirect(array('action' => 'index'));

		}


		$office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
	
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
    
		$this->set(compact('offices'));

	}

	public function admin_edit($id = null) {

	
		$this->set('page_title','Edit Credit Note');
        $this->CreditNote->id = $id;
		if (!$this->CreditNote->exists($id)) {
			throw new NotFoundException(__('Invalid Credit Note'));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			//echo '<pre>';print_r($this->request->data);exit;

			$creditnote['CreditNote']['id'] = $this->request->data['CreditNote']['id'];
			$creditnote['CreditNote']['office_id'] = $this->request->data['CreditNote']['office_id'];
			$creditnote['CreditNote']['territory_id'] = $this->request->data['CreditNote']['territory_id'];
			$creditnote['CreditNote']['market_id'] = $this->request->data['CreditNote']['market_id'];
			$creditnote['CreditNote']['outlet_id'] =$this->request->data['CreditNote']['outlet_id'];
			$creditnote['CreditNote']['updated_at'] = $this->current_datetime();
			$creditnote['CreditNote']['updated_by'] = $this->UserAuth->getUserId();

			if ($this->CreditNote->save($creditnote)) 
			{	
				$id = $this->request->data['CreditNote']['id'];

				$this->CreditNoteDetail->deleteAll(array('CreditNoteDetail.credit_note_id' => $id));

				$insertCnDetilas = array();
				foreach($this->request->data['memo_no_check'] as $key => $v){

					$md_id = $this->request->data['memo_no_check'][$key];
					$return_qty = $this->request->data['return_qty'][$key];
					$reason = $this->request->data['reason'][$key];

					$cndetails['CreditNoteDetail']['credit_note_id'] = $id;
					$cndetails['CreditNoteDetail']['memo_details_id'] = $md_id;
					$cndetails['CreditNoteDetail']['return_qty'] = $return_qty;
					$cndetails['CreditNoteDetail']['reason'] = $reason;
					
					$cndetails['CreditNoteDetail']['created_at'] = $this->current_datetime();
					$cndetails['CreditNoteDetail']['created_by'] = $this->UserAuth->getUserId();
					$cndetails['CreditNoteDetail']['updated_at'] = $this->current_datetime();
					$cndetails['CreditNoteDetail']['updated_by'] = $this->UserAuth->getUserId();
					$insertCnDetilas[] = $cndetails;
				}	

				if(!empty($insertCnDetilas)){
					$this->CreditNoteDetail->saveAll($insertCnDetilas); 
				}

				$this->Session->setFlash(__('Credit Note update successfully'), 'flash/success');
				$this->redirect(array('action' => 'index'));
				
			}else{
				$this->Session->setFlash(__('please try again'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}

			
			
		} 
		else 
		{
			$options = array('conditions' => array('CreditNote.' . $this->CreditNote->primaryKey => $id));
			
			$this->request->data = $this->CreditNote->find('first', $options);
			
			$exiting_data = $this->CreditNoteDetail->find('all', array(
				'conditions'=>array(
					'CreditNoteDetail.credit_note_id'=>$id
				),
				'joins'=>array(
					array(
						'alias'=>'MemoDetail',
						'table'=>'memo_details',
						'type'=>'left',
						'conditions'=>'MemoDetail.id=CreditNoteDetail.memo_details_id'
					),
					array(
						'alias'=>'Memo',
						'table'=>'memos',
						'type'=>'left',
						'conditions'=>'Memo.id=MemoDetail.memo_id'
					),
					array(
						'alias'=>'Product',
						'table'=>'products',
						'type'=>'left',
						'conditions'=>'Product.id=MemoDetail.product_id'
					)
					
				),
				'fields'=>array(
					'Memo.id', 
					'Memo.memo_date', 
					'Memo.memo_no', 
					'MemoDetail.product_id', 
					'MemoDetail.id', 
					'MemoDetail.sales_qty', 
					'MemoDetail.price',
					'Product.name',
					'CreditNoteDetail.return_qty',
					'CreditNoteDetail.reason',
				),
				'recursive'=>-1

			));

			//echo $this->CreditNoteDetail->getLastquery();exit;

			//echo '<pre>';print_r($exiting_data);exit;

			
			$offices = $this->Office->find('list', array(
				'conditions' => array('Office.id'=>$this->request->data['CreditNote']['office_id']), 
				'order' => array('office_name' => 'asc')
			));
			

			$territory_info = $this->Territory->find('first', array(
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'conditions' => array('Territory.id' => $this->request->data['CreditNote']['territory_id']),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));

			$territory_list[$territory_info['Territory']['id']] = $territory_info['Territory']['name'].' ('.$territory_info['SalesPerson']['name'].')';
		

			$market_list = $this->Market->find('list', array(
                'conditions' => array('Market.id' => $this->request->data['CreditNote']['market_id']),
                
            ));
	

			$outlet_list = $this->Outlet->find('list', array(
                'conditions' => array('Outlet.id' => $this->request->data['CreditNote']['outlet_id']),
                
            ));
		

			$this->set(compact('offices', 'exiting_data', 'territory_list', 'market_list', 'outlet_list'));
			
			
		}


	}

	public function get_territory_list(){

		
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$office_id = $this->request->data['office_id'];
		if($office_id)
		{
				
			$child_territory_parent_id = $this->Territory->find('list',array(
				'conditions'=> array(
					'parent_id !=' => 0,
					
				),
				'fields'=>array('Territory.parent_id','Territory.name'),
				
			));
			
			$territory = $this->Territory->find('all', array(
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'conditions' => array('Territory.office_id' => $office_id,'NOT'=>array('Territory.id'=>array_keys($child_territory_parent_id))),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));

			// echo $this->Territory->getLastquery();exit;
			
    	}
		
		
		$data_array = array();
		
		foreach($territory as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['Territory']['id'],
				'name' => $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')',
			);
		}
		
		//pr($data_array);
		
		if(!empty($territory)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}

	function get_territory_list_for_edit()
    {
        $this->LoadModel('Territory');
        $office_id = $this->request->data['office_id'];
        $output = "<option value=''>--- Select Territory ---</option>";
        
        if($office_id)
		{
			$child_territory_parent_id = $this->Territory->find('list',array(
				'conditions'=> array(
					'parent_id !=' => 0,
					
				),
				'fields'=>array('Territory.parent_id','Territory.name'),
				
			));
			
			$territory = $this->Territory->find('all', array(
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'conditions' => array('Territory.office_id' => $office_id,'NOT'=>array('Territory.id'=>array_keys($child_territory_parent_id))),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));
    	
            if ($territory) {
                foreach ($territory as $key => $value) {
					$id = $value['Territory']['id'];
					$name= $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
                    $output .= "<option value='$id'>$name</option>";
                }
            }
        }

        echo $output;
        $this->autoRender = false;
    }

	function get_market_list()
    {
       
        $territory_id = $this->request->data['territory_id'];
        $output = "<option value=''>--- Select Market ---</option>";
        if ($territory_id) {
            $territory = $this->Market->find('list', array(
                'conditions' => array('Market.territory_id' => $territory_id),
                
            ));
            if ($territory) {
                foreach ($territory as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }

        echo $output;
        $this->autoRender = false;
    }

	public function get_outlet_list()
    {
		
        $market_id = $this->request->data['market_id'];
		$output = "<option value=''>--- Select Outlet ---</option>";
		if($market_id){
			$outlet_list = $this->Outlet->find('list', array(
				'conditions' => array('Outlet.market_id' => $market_id),
				'order' => array('Outlet.name' => 'asc')
			));
	
			if ($outlet_list) {
				foreach ($outlet_list as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}
        
		echo $output;
        $this->autoRender = false;
        
    }

	public function get_memo_list()
    {
		
        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $market_id = $this->request->data['market_id'];
        $outlet_id = $this->request->data['outlet_id'];
        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
		
		

		$memo_list = $this->Memo->find('list', array(
			'conditions' => array(
				'Memo.office_id' => $office_id,
				'Memo.territory_id' => $territory_id,
				'Memo.market_id' => $market_id,
				'Memo.outlet_id' => $outlet_id,
				'Memo.memo_date >=' => date('Y-m-d', strtotime($date_from)),
				'Memo.memo_date <=' => date('Y-m-d', strtotime($date_to)),
			),
			'fields' => array('Memo.id', 'Memo.memo_no'),
			'order' => array('Memo.memo_no' => 'asc'),
			'recursive' => -1,
		));
		$output = "<option value=''>--- Select Memo ---</option>";
		if ($memo_list) {
			foreach ($memo_list as $key => $data) {
				$output .= "<option value='$key'>$data</option>";
			}
		}

        
		echo $output;
        $this->autoRender = false;
        
    }

	public function get_memo_product_list()
    {
		
        $memo_id = $this->request->data['memo_id'];

		

		$plist = $this->MemoDetail->find('list', array(
			'conditions' => array(
				'MemoDetail.memo_id' => $memo_id,
				'MemoDetail.price >' => 0
			),
			'joins'=>array(
				array(
					'alias'=>'Product',
					'table'=>'products',
					'type'=>'left',
					'conditions'=>'Product.id=MemoDetail.product_id'
				)
			),
			'fields' => array('MemoDetail.id', 'Product.name'),
			'recursive' => -1,
		));
		$output = "<option value=''>--- Select Product ---</option>";
		if ($plist) {
			foreach ($plist as $key => $data) {
				$output .= "<option value='$key'>$data</option>";
			}
		}

        
		echo $output;
        $this->autoRender = false;
        
    }

	public function get_product_memo_details()
    {
		
        $memo_details_product_id = $this->request->data['memo_details_product_id'];

		

		$qty = $this->MemoDetail->find('first', array(
			'conditions' => array(
				'MemoDetail.id' => $memo_details_product_id
			),
			'fields' => array('MemoDetail.sales_qty'),
			'recursive' => -1,
		));
		
		$qty = $qty['MemoDetail']['sales_qty'];

		$rs['sales_qty'] = $qty;
		echo json_encode($rs);
        $this->autoRender = false;
        
    }

	public function get_product_memo_detils()
    {
		
        $memodetail_product_id = $this->request->data['memodetail_product_id'];

		

		$details = $this->MemoDetail->find('first', array(
			'conditions' => array(
				'MemoDetail.id' => $memodetail_product_id
			),
			'joins'=>array(
				array(
					'alias'=>'Memo',
					'table'=>'memos',
					'type'=>'left',
					'conditions'=>'Memo.id=MemoDetail.memo_id'
				),
				array(
					'alias'=>'Product',
					'table'=>'products',
					'type'=>'left',
					'conditions'=>'Product.id=MemoDetail.product_id'
				)
			),
			'fields' => array(
				'Memo.id', 
				'Memo.memo_date', 
				'Memo.memo_no', 
				'MemoDetail.product_id', 
				'MemoDetail.id', 
				'MemoDetail.sales_qty', 
				'MemoDetail.price',
				'Product.name',
			),
			'recursive' => -1,
		));
		
		$resutl = array(
			'm_id'=>$details['Memo']['id'],
			'memo_date'=>$details['Memo']['memo_date'],
			'memo_no'=>$details['Memo']['memo_no'],
			'product_id'=>$details['MemoDetail']['product_id'],
			'p_name'=>$details['Product']['name'],
			'md_id'=>$details['MemoDetail']['id'],
			'sales_qty'=>$details['MemoDetail']['sales_qty'],
			'price'=>$details['MemoDetail']['price'],
		);

		$rs['info'] = $resutl;
		echo json_encode($rs);
        $this->autoRender = false;
        
    }


}
