<?php
App::uses('AppController', 'Controller');
/**
 * Deposits Controller
 *
 * @property Deposit $Deposit
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DepositsController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session','Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index(){
			$this->set('page_title','Deposit List');
			$office_parent_id = $this->UserAuth->getOfficeParentId();
			if($office_parent_id == 0){
				$conditions = array('Deposit.deposit_date' => $this->current_date());
		
			}else{
				$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId(),'Deposit.deposit_date' => $this->current_date());
			}

			/*$this->Deposit->SalesPerson->unbindModel(array('belongsTo' => array('Designation','Office','Territory')));
			$this->Deposit->SalesPerson->unbindModel(array('hasOne' => array('User')));
			$this->Deposit->BankBranch->unbindModel(array('hasMany' => array('BankAccount')));
			$this->Deposit->Week->unbindModel(array('belongsTo' => array('Month')));
			$this->Deposit->Month->unbindModel(array('belongsTo' => array('FiscalYear')));
			$this->Deposit->Month->unbindModel(array('hasMany' => array('Week')));
			$this->Deposit->unbindModel(array('belongsTo' => array('FiscalYear')));
			$this->Deposit->unbindModel(array('hasMany' => array('Collection')));
			$this->Deposit->Memo->unbindModel(array('hasMany' => array('MemoDetail')));
			$this->Deposit->Memo->unbindModel(array('belongsTo' => array('SalesPerson','Territory','Market')));*/
	        
			
			$this->Deposit->recursive = 1;
			$this->paginate = array(
				'conditions' => $conditions,
				'order' => array('Deposit.id' => 'DESC'),
				/*'limit' => 50*/
				);	
			/*pr($this->paginate());	
			echo $this->Deposit->getLastQuery();exit;*/


			$this->set('deposits', $this->paginate());
			
			$this->loadModel('Territory');
			$this->loadModel('Market');
			$this->loadModel('Outlet');
			$this->loadModel('Office');
			$this->LoadModel('InstrumentType');
			
			$offices = array();
			if($office_parent_id!=0)
			{
				$office_type = $this->Office->find('first',array(
					'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
					'recursive'=>-1
					));
				$office_type_id = $office_type['Office']['office_type_id'];
			}


			if ($office_parent_id == 0) 
			{
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


			$office_id = isset($this->request->data['Deposit']['office_id'])!='' ? $this->request->data['Deposit']['office_id'] : 0;
			$territory_id = isset($this->request->data['Deposit']['territory_id'])!='' ? $this->request->data['Deposit']['territory_id'] : 0;
			$market_id = isset($this->request->data['Deposit']['market_id'])!='' ? $this->request->data['Deposit']['market_id'] : 0;
			$territory = $this->Territory->find('all', array(
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'conditions' => array('Territory.office_id' => $office_id),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
				));
			$territories=array();
			foreach($territory as $key => $value)
			{
				$territories[$value['Territory']['id']]=$value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
			}
			$markets = $this->Market->find('list',array(
				'conditions' => array('Market.territory_id'=> $territory_id),
				'order' => array('Market.name'=>'asc')
				));
			$outlets = $this->Outlet->find('list',array(
				'conditions' => array('Outlet.market_id'=> $market_id),
				'order' => array('Outlet.name'=>'asc')
				));
			$current_date = date('d-m-Y',strtotime($this->current_date()));	
			
			$instrument_types = array(
				'1'=>'Cash',
				'2'=>'Instrument'
				);
			$instrument_type = $this->InstrumentType->find('list');

			$this->set(compact('territories','markets','offices','outlets','current_date','instrument_type','instrument_types'));
	}
	public function download_xl($data)
	{

	   $jdata=json_decode($data, true);
	   $office_id=$jdata[0];
	   $territory_id=$jdata[1];
	   $instrument_type=$jdata[2];
	   $DepositDateFrom=date('Y-m-d', strtotime($jdata[3]));
	   $DepositDateTo=date('Y-m-d', strtotime($jdata[4]));
	   
	   $office_parent_id = $this->UserAuth->getOfficeParentId();
        $conditions = array();
		if(CakeSession::read('Office.parent_office_id') != 0)
		{
			$conditions[] = array('SalesPerson.office_id' => CakeSession::read('Office.id'));
		}	
		elseif(!empty($office_id))
		{
			$conditions[] = array('SalesPerson.office_id' => $office_id);
		}
			
		if (!empty($territory_id)) {
            $conditions[] = array('Deposit.territory_id' => $territory_id);
        } 
		
		/*if (!empty($params['Deposit.market_id'])) {
            $conditions[] = array('Memo.market_id' => $params['Deposit.market_id']);
        }
		if (!empty($params['Deposit.outlet_id'])) {
            $conditions[] = array('Memo.outlet_id' => $params['Deposit.outlet_id']);
        }*/
		
		if (!empty($instrument_type)) {
            $conditions[] = array('Deposit.type' => $instrument_type);
        }
			
		if (isset($DepositDateFrom)!='') {
            $conditions[] = array('Deposit.deposit_date >=' => Date('Y-m-d',strtotime($DepositDateFrom)));
        }
		if (isset($DepositDateTo)!='') {
            $conditions[] = array('Deposit.deposit_date <=' => Date('Y-m-d',strtotime($DepositDateTo)));
        }
        $this->loadModel('Deposit');

        if($DepositDateFrom != "" && $DepositDateTo != ""){
        $deposits = $this->Deposit->find('all',array('conditions' =>$conditions,
		'recursive'=> 1));
	    }
	    
		$this->LoadModel('InstrumentType');
			
			
		$instrument_types = array(
			'1'=>'Cash',
			'2'=>'Instrument'
		);
		$instrument_type = $this->InstrumentType->find('list');
		$cash='';
        $depostsType='';

	    $table='<table border="1"><tbody>
	    <tr>
	        <td>Id</td>
	        <td>Memo no</td>
	        <td>Outlet</td>
			<td>Territory</td>
	        <td>Bank Branch</td>
	        <td>Bank</td>
	        <td>Type</td>
	        <td>Deposit Type</td>
	        <td>Transaction Type</td>
	        <td>Slip Number</td>
	        <td>Deposit Amount</td>
	        <td>Deposit Date</td>
	        <td>Week</td>
	        
	    </tr>
	    ';

	    $DepositsController = new DepositsController();

	    foreach($deposits as $part)
	    {
	    	$outlet= ($part['Deposit']['type']==2)?$DepositsController->getOutletName($part['Memo']['outlet_id']):'';
	    	$instrument=@$part['Deposit']['instrument_type']!=1?@$instrument_type[$part['Deposit']['instrument_type']]:'';
	    	$memo_no='';
	    	$cash='';
	    	//pr($part);die();
	    	 if($part['Deposit']['instrument_type']==1)
	    	 	{ $cash= 'Cash'; }
	    	 else if($part['Deposit']['instrument_type']==3)
	    	 { $cash= 'Cheque'; }; 
			if($part['Deposit']['instrument_type']==1)
				{ $cash= 'Cash'; }
			else if($part['Deposit']['instrument_type']==3)
				{ $cash= 'Cheque'; }; 

			if($part['Deposit']['type']==2)
				{ $memo_no= $part['Deposit']['memo_no']; }




	        $table.='<tr>
		        <td>'.$part['Deposit']['id'].'</td>
		        <td style="mso-number-format:\@;">'.$memo_no.'</td>
		        <td>'.$outlet.'<td>
		        <td>'.$part['Territory']['name'].'</td>
		        <td>'.$part['BankBranch']['name'].'</td>
				<td>'.$DepositsController->getBankName($part['BankBranch']['bank_id']).'</td>
				<td>'.$cash.'</td>
		        <td>'.$instrument_type[$part['Deposit']['type']].'</td>
		        <td>'.$instrument.'</td>
		        <td>'.$part['Deposit']['slip_no'].'</td>
		        <td>'.$part['Deposit']['deposit_amount'].'</td>
		        <td>'.$part['Deposit']['deposit_date'].'</td>
		        <td>'.$part['Week']['week_name'].'</td>
	        
		    </tr>
		    ';
		}
		$table.='</tbody></table>';
		header('Content-Type:application/force-download');
		header('Content-Disposition: attachment; filename="Deposit.xls"');
		header("Cache-Control: ");
		header("Pragma: ");
		echo $table;
		$this->autoRender=false;
	}
	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		$this->set('page_title','Deposit Details');
		if (!$this->Deposit->exists($id)) {
			throw new NotFoundException(__('Invalid deposit'));
		}
		$options = array('conditions' => array('Deposit.' . $this->Deposit->primaryKey => $id));
		$this->set('deposit', $this->Deposit->find('first', $options));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add() {
		$this->set('page_title','Add Deposit');
		if ($this->request->is('post')) {
			$this->Deposit->create();
			$this->request->data['Deposit']['created_at'] = $this->current_datetime();
			$this->request->data['Deposit']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->Deposit->save($this->request->data)) {
				$this->Session->setFlash(__('The deposit has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The deposit could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$memos = $this->Deposit->Memo->find('list');
		$salesPeople = $this->Deposit->SalesPerson->find('list');
		$bankAccounts = $this->Deposit->BankAccount->find('list');
		$this->set(compact('memos', 'salesPeople', 'bankAccounts'));
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Deposit');
		$this->Deposit->id = $id;
		if (!$this->Deposit->exists($id)) {
			throw new NotFoundException(__('Invalid deposit'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Deposit']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Deposit']['updated_at'] = $this->current_datetime();
			$this->request->data['Deposit']['editable'] = 0;
			if ($this->Deposit->save($this->request->data)) {
				$so_id=$this->request->data['Deposit']['sales_person_id'];
				$this->update_territory_wise_collection_deposit_balance($so_id);
				$this->Session->setFlash(__('The deposit has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The deposit could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Deposit.' . $this->Deposit->primaryKey => $id));
			$this->request->data = $this->Deposit->find('first', $options);
			if($this->request->data['Deposit']['is_distributor']==1)
			{
				$this->Session->setFlash(__("Can't Update Distributor Deposit"), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			if($this->request->data['Deposit']['editable']==0)
			{
				$this->Session->setFlash(__("Deposit Not Editable."), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			$this->request->data['Deposit']['deposit_amount']=sprintf("%0.2f",$this->request->data['Deposit']['deposit_amount']);
		}
		$this->LoadModel('Bank');
		$this->LoadModel('InstrumentType');
		$this->LoadModel('Week');
		$banks = $this->Bank->find('list',array('fields'=>array('id','name')));
		$bankBranches=$this->Deposit->BankBranch->find(
			'list',
			array(
				'conditions'=>array(
					'BankBranch.bank_id'=>$this->request->data['BankBranch']['bank_id'],
					'OR'=>array(
						array('BankBranch.territory_id'=>NULL),
						array('BankBranch.territory_id'=>$this->request->data['Deposit']['territory_id']),
						)
					),
				)
			);
		$types=$this->InstrumentType->find('list',array('conditions'=>array('id'=>array(1,2))));
		$instrument_types=$this->InstrumentType->find('list',array('conditions'=>array('NOT'=>array('id'=>array(1,2)))));
		$weeks=$this->Week->find('list',array(
			'conditions'=>array(
				'start_date >='=>date('Y-m-d',strtotime($this->request->data['Deposit']['deposit_date'].'-3 months')),
				'end_date <='=>date('Y-m-d',strtotime($this->request->data['Deposit']['deposit_date'].'+3 months'))
				)
			)
		);
		$this->set(compact('memos', 'salesPeople', 'banks','bankBranches','types','instrument_types','weeks'));
	}
	private function update_territory_wise_collection_deposit_balance($so_id) 
	{
        $this->LoadModel('InstrumentType');
        if ($so_id == 0) {
            return false;
        }
        $this->loadModel('Collection');
        $this->loadModel('Deposit');
        $this->loadModel('SalesPerson');
        $this->loadModel('TerritoryWiseCollectionDepositBalance');
        $terrtory_id = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.territory_id'),
            'conditions' => array('SalesPerson.id' => $so_id),
            'recursive' => -1
        ));
        $territory_id = $terrtory_id ['SalesPerson']['territory_id'];
        /* $instrument_type=array(
          '1'=>'Cash',
          '2'=>'Cheque'
          ); */
        $instrument_type = $this->InstrumentType->find('list', array(
            'conditions' => array('InstrumentType.id' => array('1','2'))
        ));
        $updated_array = array();
        foreach ($instrument_type as $key => $ins_data) {
            $collection_data = $this->Collection->find('first', array(
                'fields' => array('SUM(Collection.collectionAmount) as total_collection'),
                'conditions' => array(
                    'Collection.territory_id' => $territory_id, 
                    'Collection.type' => $key,
                    'Collection.so_id'=>$so_id,
                    'Collection.memo_date >='=>'2018-10-01',
                    ),
                'joins' => array(
                    array(
                        'table' => 'sales_people',
                        'alias' => 'SalesPerson',
                        'type' => 'inner',
                        'conditions' => 'SalesPerson.id=Collection.so_id'
                    )
                ),
                'recursive' => -1
            ));
            $deposit_data = $this->Deposit->find('first', array(
                'fields' => array('SUM(Deposit.deposit_amount) as total_deposit'),
                'conditions' => array('Deposit.territory_id' => $territory_id, 'Deposit.type' => $key,'Deposit.deposit_date >='=>'2018-10-01','Deposit.sales_person_id'=>$so_id),
                'joins' => array(
                    array(
                        'table' => 'sales_people',
                        'alias' => 'SalesPerson',
                        'type' => 'inner',
                        'conditions' => 'SalesPerson.id=Deposit.sales_person_id'
                    )
                ),
                'recursive' => -1
            ));
            $exist_data = $this->TerritoryWiseCollectionDepositBalance->find('first', array(
                'conditions' => array(
                    'TerritoryWiseCollectionDepositBalance.territory_id' => $territory_id,
                    'TerritoryWiseCollectionDepositBalance.so_id' => $so_id,
                    'TerritoryWiseCollectionDepositBalance.instrument_type_id' => $key
                )
            ));
            $data['total_deposit'] = $deposit_data[0]['total_deposit'];
            $data['total_collection'] = $collection_data[0]['total_collection'];
            $data['territory_id'] = $territory_id;
            $data['so_id'] = $so_id;
            $data['instrument_type_id'] = $key;
            $balance = $data['total_collection'] - $data['total_deposit'];
            // $data['hands_of_so'] = $balance < 0 ? 0 : $balance;
			$data['hands_of_so'] = $balance;
            $data['updated_at'] = $this->current_datetime();
            $data['updated_by'] = $so_id;
            if ($exist_data) {
                $data['id'] = $exist_data['TerritoryWiseCollectionDepositBalance']['id'];
            } else {
                $data['created_at'] = $this->current_datetime();
                $data['created_by'] = $so_id;
            }
            $updated_array[] = $data;
        }
        if ($updated_array) {
            $this->TerritoryWiseCollectionDepositBalance->saveAll($updated_array);
        }
        return true;
    }
	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) 
	{
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Deposit->id = $id;
		if (!$this->Deposit->exists()) {
			throw new NotFoundException(__('Invalid deposit'));
		}
		$options = array('conditions' => array('Deposit.' . $this->Deposit->primaryKey => $id));
		$deposits = $this->Deposit->find('first', $options);
		if($deposits['Deposit']['type']==2)
		{
			$deposit_arr['id'] = $id;
			$deposit_arr['editable'] = 0;
			$this->Deposit->Query("ALTER TABLE deposits DISABLE TRIGGER deposit_logs_after_update");
			$this->Deposit->save($deposit_arr);
			$this->Deposit->Query("ALTER TABLE deposits ENABLE TRIGGER deposit_logs_after_update");
			$this->Session->setFlash(__('Instrument deposit Can not be deleted'), 'flash/error');
			$this->redirect(array('action' => 'index'));
		}
		$so_id=$deposits['Deposit']['sales_person_id'];
		if ($this->Deposit->delete()) {
			$this->update_territory_wise_collection_deposit_balance($so_id);
			$this->Session->setFlash(__('Deposit deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Deposit was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}


	public function getOutletName($id=0) 
	{
		$this->loadModel('Outlet'); 
		
		$reuslt = $this->Outlet->find('first', array(
			'conditions' => array(
				'id' => $id
				),
			'recursive' => -1
			));
	    

	    if(empty($reuslt)) { return ""; }
	     
	    $outletsName=$reuslt['Outlet']['name'];
		return $outletsName;
	}


	public function getBankName($id=0) 
	{
		$this->loadModel('Bank'); 
		
		$reuslt = $this->Bank->find('first', array(
			'conditions' => array(
				'id' => $id
				),
			'recursive' => -1
			));

		return $reuslt['Bank']['name'];
	}

	public function get_bank_branch()
	{
		$bank_id=$this->request->data['bank_id'];
		$territory_id=$this->request->data['territory_id'];
		$bankBranches=$this->Deposit->BankBranch->find(
		'list',
		array(
			'conditions'=>array(
				'BankBranch.bank_id'=>$bank_id,
				'OR'=>array(
					array('BankBranch.territory_id'=>NULL),
					array('BankBranch.territory_id'=>$territory_id),
					)
				),
			)
		);
		$view = new View($this);
		$form = $view->loadHelper('Form');
		if($bankBranches)
		{	
			$form->create('Deposit', array('role' => 'form', 'action'=>'index'))	;
			
			echo $form->input('bank_branch_id', array(
							'class' => 'form-control',
							'label' => false,
							'div' => false,
							'options'=>$bankBranches,
							'empty'=>'--- Select ---')
				);
			$form->end();
		}
		else
		{
			echo '';	
		}
		$this->autoRender=false;
	}
	public function get_week()
	{
		$this->loadModel('Week');
		$deposit_date=$this->request->data['deposit_date'];
		$weeks=$this->Week->find('list',array(
			'conditions'=>array(
				'start_date >='=>date('Y-m-d',strtotime($deposit_date.'-3 months')),
				'end_date <='=>date('Y-m-d',strtotime($deposit_date.'+3 months'))
				)
			)
		);
		$view = new View($this);
		$form = $view->loadHelper('Form');
		if($weeks)
		{	
			$form->create('Deposit', array('role' => 'form', 'action'=>'index'))	;
			
			echo $form->input('week_id', array(
							'class' => 'form-control',
							'label' => false,
							'div' => false,
							'options'=>$weeks,
							'empty'=>'--- Select ---')
				);
			$form->end();
		}
		else
		{
			echo '';	
		}
		$this->autoRender=false;
	}
	public function get_collection_amount_by_payment_id()
	{
		$payment_id=$this->request->data['payment_id'];
		$this->loadModel("Collection");
		$collection_data=$this->Collection->find("first",array(
			'conditions'=>array('Collection.payment_id'=>$payment_id),
			'recursive'=>-1
			)
		);
		echo sprintf('%0.2f',$collection_data['Collection']['collectionAmount']);
		$this->autoRender=false;
	}

}