<?php

App::uses('AppController', 'Controller');

/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OpeningBalancesController extends AppController {
    /**
     * Components
     *
     * @var array
     */
	 
	 public $uses = array('OpeningBalance', 'Office', 'Territory', 'FiscalYear');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index($get_fiscal_year_id = null) 
	{
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId(); 	//get office parent id
		$office_id 			= 	$this->UserAuth->getOfficeId(); 		//get office id
		$user_id 			= 	$this->UserAuth->getUserId(); 			//get user id
		
		$fiscal_year_id = 0;
		$area_office_id = 0;
		$opening_result = array();
				
		if($this->request->is('post'))
		{
				//pr($this->request->data);
				
				//$fiscal_year_id = $this->request->data['OpeningBalance']['fiscal_year_id'];
				$office_id = $this->request->data['OpeningBalance']['office_id'];
				$area_office_id = $this->request->data['OpeningBalance']['office_id'];
				
				
				
				$opening_result = $this->OpeningBalance->find('all', array(
							'conditions' => array('OpeningBalance.office_id' => $this->request->data['OpeningBalance']['office_id']),
							//'order' => array('name' => 'asc'),
							'recursive'=> 0
						));
								
				
				
				
				//for multi save
				$data_array = array();
				
				//$data['OpeningBalance']['fiscal_year_id'] = $fiscal_year_id;
				$data['OpeningBalance']['office_id'] = $this->request->data['OpeningBalance']['office_id'];
				
				foreach($this->request->data['OpeningBalance']['territories'] as $key => $val)
				{		
					if($opening_result){$data['OpeningBalance']['id'] = $key;}		
					$data['OpeningBalance']['territory_id'] = $val['territory_id'];	
					$data['OpeningBalance']['total_sales'] = $val['total_sales'];	
					$data['OpeningBalance']['total_ncp_collection'] = $val['total_ncp_collection'];	
					$data['OpeningBalance']['total_achivement'] = $val['total_achivement'];	
					$data['OpeningBalance']['total_outstanding'] = $val['total_outstanding'];	
					
					if(!$opening_result){
						$data['OpeningBalance']['created_at'] = $this->current_datetime();
						$data['OpeningBalance']['created_by'] = $this->UserAuth->getUserId();
					}
					$data['OpeningBalance']['updated_at'] = $this->current_datetime(); 
					
					$data['OpeningBalance']['updated_by'] = $this->UserAuth->getUserId();
					$data_array[] = $data;
				}
			
			
			//pr($data_array);
			//exit;	
									
			//$this->OpeningBalance->saveAll($data_array);
			
			if($this->OpeningBalance->saveAll($data_array))
			{
				$opening_result = $this->OpeningBalance->find('all', array(
							'conditions' => array('OpeningBalance.office_id' => $this->request->data['OpeningBalance']['office_id']),
							//'order' => array('name' => 'asc'),
							'recursive'=> 0
						));
						
				$this->Session->setFlash(__('Opening Balances has been save successfully!'), 'flash/success');
				//$this->redirect(array('action' => 'index'));
			}		
			
		}
		
		$this->set(compact('office_parent_id', 'office_id', 'user_id'));
		$this->set(compact('fiscal_year_id'));
		$this->set(compact('area_office_id', 'opening_result'));
		
			
        $this->set('page_title', 'Opening Balances List');
        
		//$this->OpeningBalance->recursive = 0;
        //$this->Office->recursive = 1;
		
        //get Office list
		if(!$office_parent_id)
		{
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			$this->set(compact('offices'));
		}
		else
		{
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					'id' => $office_id
					), 
				'order'=>array('office_name'=>'asc')
			));
		}
		
		
		
						
        $fiscalYears = $this->FiscalYear->find('list', array('fields' => array('year_code')));
      
	    $this->set(compact('fiscalYears', 'offices'));
		
	}

    /**
     * admin_get_national_sales_data method
     *
     * @return void
     */
    public function BalanceListByAreaOffice() {

        $json = array();
		
		$office_id = $this->request->data['office_id']; 
		//$fiscal_year_id = $this->request->data['fiscal_year_id']; 
		
		
		$opening_result = $this->OpeningBalance->find('all', array(
							'conditions' => array('OpeningBalance.office_id' => $office_id),
							//'order' => array('name' => 'asc'),
							'recursive'=>0
						));
		
		//pr($opening_result);
		
		if($opening_result)
		{
			$output = '';
			
			
			foreach($opening_result as $result)
			{
				$output .= '<tr>';
				$output .= '<td class="text-center">
				'.$result['Territory']['name'].'
				<input type="hidden" value="'.$result['OpeningBalance']['territory_id'].'" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][territory_id]">
				</td>';
				
				$output .= '<td class="text-right"><input type="number" value="'.$result['OpeningBalance']['total_sales'].'" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][total_sales]"></td>';
				$output .= '<td class="text-right"><input type="number" value="'.$result['OpeningBalance']['total_outstanding'].'" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][total_outstanding]"></td>';
				
				$output .= '<td style="display:none;" class="text-right"><input type="number" value="'.$result['OpeningBalance']['total_ncp_collection'].'" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][total_ncp_collection]"></td>';
				
				$output .= '<td style="display:none;" class="text-right"><input type="number" value="'.$result['OpeningBalance']['total_achivement'].'" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][total_achivement]"></td>';
				
				
				$output .= '</tr>';
			}
		}
		else
		{
		
			$territories = $this->Territory->find('all', array(
								'conditions' => array('office_id' => $office_id),
								'order' => array('name' => 'asc'),
								'recursive'=>-1
							));
			
			//pr($territories);
			
			$output = '';
			
			$i=0;
			foreach($territories as $result)
			{
				$output .= '<tr>';
				$output .= '<td class="text-center">
				'.$result['Territory']['name'].'
				<input type="hidden" value="'.$result['Territory']['id'].'" name="data[OpeningBalance][territories]['.$i.'][territory_id]">
				</td>';
				
				$output .= '<td class="text-right"><input type="number" value="0" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$i.'][total_sales]"></td>';
				
				$output .= '<td class="text-right"><input type="number" value="0" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$i.'][total_outstanding]"></td>';
				
				$output .= '<td style="display:none;" class="text-right"><input type="number" value="0" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$i.'][total_ncp_collection]"></td>';
				
				$output .= '<td style="display:none;" class="text-right"><input type="number" value="0" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$i.'][total_achivement]"></td>';
				
				
				$output .= '</tr>';
				
				$i++;
			}
		
		}
		
		echo $output; 
		
		$this->autoRender = false; 
    }

}
