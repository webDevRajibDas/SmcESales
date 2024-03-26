<?php
App::uses('AppController', 'Controller');
/**
 * Deposits Controller
 *
 * @property Deposit $Deposit
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CollectionLogsController extends AppController {

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
			$this->set('page_title','Collection log List');
		
			$office_parent_id = $this->UserAuth->getOfficeParentId();
			
			$conditions = array();

			if($office_parent_id == 0){


				
				$conditions = array('CollectionLog.collectionDate' => $this->current_date());

				$OfficeConditions = array();
		
			}else{
				$conditions = array(
					'CollectionLog.office_id' => $this->UserAuth->getOfficeId(),
					'CollectionLog.collectionDate' => $this->current_date());

				$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
			}

		$this->CollectionLog->recursive = 0;

		$this->paginate = array(
			'joins' => array(
				array(
	                'table'=>'territories',
	                'alias'=>'Territory',
	                'conditions'=>'Territory.id=CollectionLog.territory_id'
	            ),
	            array(
	                'table'=>'memos',
	                'alias'=>'Memo',
	                'conditions'=>'Memo.id=CollectionLog.memo_id'
	            ),
	            array(
					'table' => 'outlets',
					'alias' => 'Outlet',
					'type' => 'INNER',
					'conditions' => array(
						'Outlet.id = CollectionLog.outlet_id'
					)
				),
	        ),
			'fields' => array('CollectionLog.*', 'Outlet.name'),
				'conditions' => $conditions,
				'order' => array('CollectionLog.id' => 'DESC'),
			);
		//pr($conditions);exit();
		$this->set('collections', $this->paginate());

	       
        $this->loadModel('Territory');
        $this->loadModel('Market');
        $this->loadModel('Office');
        $this->LoadModel('InstrumentType');

        $offices = $this->Office->find('list', array('conditions' => $OfficeConditions, 'order' => array('office_name' => 'asc')));
        $office_id = isset($this->request->data['CollectionLog']['office_id']) != '' ? $this->request->data['CollectionLog']['office_id'] : 0;
        $territory_id = isset($this->request->data['CollectionLog']['territory_id']) != '' ? $this->request->data['CollectionLog']['territory_id'] : 0;
        $market_id = isset($this->request->data['CollectionLog']['market_id']) != '' ? $this->request->data['CollectionLog']['market_id'] : 0;
        
        //pr($office_id);

        $territories = $this->Territory->find('list', array(
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc')
        ));

        //pr($territories);exit();
        $markets = $this->Market->find('list', array(
            'conditions' => array('Market.territory_id' => $territory_id),
            'order' => array('Market.name' => 'asc')
        ));

       // pr($territories);exit();
       
        $current_date = date('d-m-Y', strtotime($this->current_date()));
        $instrument_type = $this->InstrumentType->find('list');
        $this->set(compact('territories', 'markets', 'offices', 'current_date','instrument_type'));
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		$this->set('page_title','Collection Log Details');
		$joins=array(
	            array(
	                'table' => 'outlets',
						'alias' => 'Outlet',
						'type' => 'INNER',
						'conditions' => array(
							'Outlet.id = CollectionLog.outlet_id'
						)
	            ),
	            array(
	                'table' => 'instrument_type',
						'alias' => 'InstrumentType',
						'type' => 'INNER',
						'conditions' => array(
							'CollectionLog.instrument_type = InstrumentType.id'
						)
	            ),
	            array(
	                'table' => 'memos',
						'alias' => 'Memo',
						'type' => 'INNER',
						'conditions' => array(
							'Memo.id = CollectionLog.memo_id'
						)
	            ),
	            
            );
		$options=array(
			'conditions'=>array(
				'CollectionLog.collection_id'=>$id
				),
			'joins'=>$joins,
			'fields'=>array(
				'CollectionLog.*',
				'Outlet.name',
				'InstrumentType.name',
				'Memo.outlet_id'
				),
			'order' => array('CollectionLog.id' => 'DESC'),
			//'limit' => 1,
			'recursive'=>-1
			);
		$this->set('collection_log', $this->CollectionLog->find('all', $options));

		$this->LoadModel('Collection');

		$joins=array(
				array(
	                'table' => 'memos',
						'alias' => 'Memo',
						'type' => 'INNER',
						'conditions' => array(
							'Memo.id = Collection.memo_id'
						)
	            ),
	            array(
	                'table' => 'outlets',
						'alias' => 'Outlet',
						'type' => 'INNER',
						'conditions' => array(
							'Outlet.id = Memo.outlet_id'
						)
	            ),
	            array(
	                'table' => 'instrument_type',
						'alias' => 'InstrumentType',
						'type' => 'INNER',
						'conditions' => array(
							'Collection.instrument_type = InstrumentType.id'
						)
	            ),
            );
		$options=array(
			'conditions'=>array(
				'Collection.id'=>$id
				),
			'joins'=>$joins,
			'fields' => array('Collection.*', 'Outlet.name',
				'InstrumentType.name',),
			'recursive'=>-1
			);
		$this->set('current_collection', $this->Collection->find('all', $options));
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
		if ($this->Deposit->delete()) {
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