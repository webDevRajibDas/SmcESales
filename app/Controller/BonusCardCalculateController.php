<?php 
	App::uses('AppController', 'Controller');

	class BonusCardCalculateController extends AppController 
	{
		public $components = array('Paginator', 'Session');
		public $uses = array('Product','ProductType','BonusCard','StoreBonusCard','Memo','MemoDetail','Outlet','BonusEligibleOutlet','Office');
		public function admin_index() 
		{
			ini_set('max_execution_time', 99999);
			ini_set('memory_limit', '-1');
			$this->set('page_title','Bonus Card Calculate');
			$fiscalYears = $this->BonusCard->FiscalYear->find('list',array('fields'=>array('year_code')));
			// $bonusCards = $this->BonusCard->find('list');
			$this->set(compact('fiscalYears'));
			$office_conditions = array('office_type_id'=>2);
            

	        $offices = $this->Office->find('list', array(
	            'conditions'=> $office_conditions,
	            'fields'=>array('office_name')
	            ));
	        $this->set(compact('offices'));
			if($this->request->is('post'))
			{
				if(isset($this->request->data['calculate']))
				{
					$fiscal_year_id = $this->request->data['search']['fiscal_year_id'];
					$office_id = $this->request->data['search']['office_id'];
					$fiscal_year_info = $this->BonusCard->FiscalYear->find('first',array('conditions'=>array('FiscalYear.id'=>$fiscal_year_id)));
					$bonus_card = $this->request->data['search']['bonus_card_id'];
					$bonus_card_info=$this->BonusCard->find('first',array(
						'conditions'=>array('BonusCard.id'=>$bonus_card),
						'recursive'=>-1
						));
					$bonus_type_id=$bonus_card_info['BonusCard']['bonus_card_type_id'];
					// pr($bonus_card_info);exit;
					/*$bonus_outlet=$this->Outlet->find('list',array(
						'conditions'=>array('Outlet.bonus_type_id'=>$bonus_card_info['BonusCard']['bonus_card_type_id'])
						));
					// echo $this->Outlet->getLastQuery();exit;*/
					//$this->StoreBonusCard->deleteAll(array('fiscal_year_id'=>$fiscal_year_id,'bonus_card_id'=>$bonus_card));
					$delete_conditions="fiscal_year_id=$fiscal_year_id AND bonus_card_id=$bonus_card";
					if($office_id)
					{
						$delete_conditions.="AND t.office_id=$office_id";
					}
					$this->StoreBonusCard->query("DELETE sbc FROM store_bonus_cards sbc
												inner join territories t on t.id=sbc.territory_id
												WHERE $delete_conditions
						");
					// $this->BonusEligibleOutlet->deleteAll(array('BonusEligibleOutlet.fiscal_year_id'=>$fiscal_year_id,'BonusEligibleOutlet.bonus_card_id'=>$bonus_card));
					$this->BonusEligibleOutlet->query("DELETE beo FROM bonus_eligible_outlets beo
														inner join outlets ot on ot.id=beo.outlet_id
														inner join markets mk on mk.id=ot.market_id
														inner join territories t on t.id=mk.territory_id
														WHERE $delete_conditions
													");

					/*------------------- DELETE OTHER Transaction Data : START -----------------------*/
					$delete_conditions_other_stamp='';
					if($office_id)
					{
						$delete_conditions_other_stamp="and t.office_id=$office_id";
					}
					$this->StoreBonusCard->query("DELETE FROM store_bonus_cards 
														WHERE 
															fiscal_year_id=$fiscal_year_id 
															AND bonus_card_type_id!=$bonus_type_id
															AND product_id=".$bonus_card_info['BonusCard']['product_id']." 
															AND outlet_id in (
																				SELECT [Outlet].[id] AS [Outlet__id] FROM [outlets] AS [Outlet] 
																				inner join markets mk on mk.id=Outlet.market_id
																				inner join territories t on t.id=mk.territory_id
																				WHERE 
																					[Outlet].[bonus_type_id] = $bonus_type_id
																					$delete_conditions_other_stamp
																			)
													"
													);
					
					$this->BonusEligibleOutlet->query("DELETE beo FROM bonus_eligible_outlets beo
														inner join bonus_cards bc on bc.id=beo.bonus_card_id
														WHERE 
															beo.fiscal_year_id=$fiscal_year_id
															AND bonus_type_id!=$bonus_type_id 
															AND bc.product_id= ".$bonus_card_info['BonusCard']['product_id']." 
															AND beo.outlet_id in (
																				SELECT [Outlet].[id] AS [Outlet__id] FROM [outlets] AS [Outlet] 
																				inner join markets mk on mk.id=Outlet.market_id
																				inner join territories t on t.id=mk.territory_id
																				WHERE 
																					[Outlet].[bonus_type_id] = $bonus_type_id
																					$delete_conditions_other_stamp
																			)

															"
													);
					//echo $this->StoreBonusCard->getLastQuery();exit;
						/*------------------- DELETE OTHER Transaction Data : START -----------------------*/
					
					$min_qty_per_memo=$bonus_card_info['BonusCard']['min_qty_per_memo'];
					/*$all_memo_for_bonus=$this->Memo->find('all',array(
						'fields'=>array('Memo.*','MemoDetail.*'),
						'conditions'=>array(
							'Memo.outlet_id'=>array_keys($bonus_outlet),
							'MemoDetail.product_id'=>$bonus_card_info['BonusCard']['product_id'],
							'MemoDetail.sales_qty >='=>$min_qty_per_memo,
							'Memo.memo_date BETWEEN ? AND ?'=>array($fiscal_year_info['FiscalYear']['start_date'],$fiscal_year_info['FiscalYear']['end_date'])
							),
						'joins'=>array(
							array(
								'table'=>'memo_details',
								'alias'=>'MemoDetail',
								'conditions'=>'Memo.id=MemoDetail.memo_id'
								)
							),
						'recursive'=>-1
						));
					echo $this->Memo->getLastQuery();*/
					
					$memo_conditions=array(
							// 'Memo.outlet_id'=>array_keys($bonus_outlet),
							'MemoDetail.product_id'=>$bonus_card_info['BonusCard']['product_id'],
							'Outlet.bonus_type_id'=>$bonus_type_id,
							'MemoDetail.sales_qty >='=>$min_qty_per_memo,
							'MemoDetail.price >'=>0.00,
							'Memo.memo_date BETWEEN ? AND ?'=>array($fiscal_year_info['FiscalYear']['start_date'],$fiscal_year_info['FiscalYear']['end_date'])
							);
					if($office_id)
					{
						$memo_conditions['Territory.office_id']=$office_id;
					}
					$all_memo_for_bonus=$this->Memo->find('all',array(
						'fields'=>array('Memo.*','MemoDetail.*'),
						'conditions'=>$memo_conditions,
						'joins'=>array(
							array(
								'table'=>'memo_details',
								'alias'=>'MemoDetail',
								'conditions'=>'Memo.id=MemoDetail.memo_id'
								),
							array(
								'table'=>'outlets',
								'alias'=>'Outlet',
								'conditions'=>'Memo.outlet_id=Outlet.id'
								),
							array(
								'table'=>'markets',
								'alias'=>'Market',
								'conditions'=>'Outlet.market_id=Market.id'
								),
							array(
								'table'=>'territories',
								'alias'=>'Territory',
								'conditions'=>'Market.territory_id=Territory.id'
								)
							),
						'recursive'=>-1
						));
					// echo $this->Memo->getLastQuery();exit;
					$data_array=array();
					$eligible_outlet_array=array();
					$eligible_outlet_data_array=array();
					$i=0;
					foreach($all_memo_for_bonus as $key=>$data)
					{
						/*if(!in_array($data['Memo']['outlet_id'],$eligible_outlet_array))
						{
							$eligible_outlet_array[]=$data['Memo']['outlet_id'];
						}*/
						// $eligible_outlet_array[$data['Memo']['outlet_id']]=$data['Memo']['outlet_id'];

						
					
						$quantity=$data['MemoDetail']['sales_qty'];
						$stamp_no=floor($quantity / $min_qty_per_memo);
						$log_data = array();
						$log_data['StoreBonusCard']['created_at'] = $this->current_datetime();
						$log_data['StoreBonusCard']['territory_id'] = $data['Memo']['territory_id'];
						$log_data['StoreBonusCard']['outlet_id'] = $data['Memo']['outlet_id'];
						$log_data['StoreBonusCard']['market_id'] = $data['Memo']['market_id'];
						$log_data['StoreBonusCard']['product_id'] = $data['MemoDetail']['product_id'];
						$log_data['StoreBonusCard']['quantity'] =$quantity;
						$log_data['StoreBonusCard']['no_of_stamp'] = $stamp_no;
						$log_data['StoreBonusCard']['bonus_card_id'] = $bonus_card_info['BonusCard']['id'];
						$log_data['StoreBonusCard']['bonus_card_type_id'] = $bonus_card_info['BonusCard']['bonus_card_type_id'];
						$log_data['StoreBonusCard']['fiscal_year_id'] = $bonus_card_info['BonusCard']['fiscal_year_id'];
						$log_data['StoreBonusCard']['memo_no'] = $data['Memo']['memo_no'];
						$log_data['StoreBonusCard']['memo_id'] = $data['Memo']['id'];
						$data_array[]=$log_data;
						unset($log_data);

						/*-------------------------- Eligible outlet data: Start -----------------------------------*/
						$eligible_outlet_data['bonus_card_id']=$bonus_card;
						$eligible_outlet_data['bonus_type_id']=$bonus_type_id;
						$eligible_outlet_data['fiscal_year_id']=$fiscal_year_id;
						$eligible_outlet_data['outlet_id']=$data['Memo']['outlet_id'];
						$eligible_outlet_data['is_eligible']=1;
						$eligible_outlet_data_array[$data['Memo']['outlet_id']]=$eligible_outlet_data;
						unset($eligible_outlet_data);
						/*-------------------------- Eligible outlet data: END   -----------------------------------*/
						if($i==300)
						{
							$i=0;
							$this->StoreBonusCard->saveAll($data_array);
							//$this->BonusEligibleOutlet->saveAll($eligible_outlet_data_array);
							$data_array=array();
						}
						else
						{
							$i++;
						}
					}
					/*pr($eligible_outlet_data_array);
					pr($data_array);exit;*/
					if($this->StoreBonusCard->saveAll($data_array))
					{
						/*$data_array=array();
						$bonus_type_id=$bonus_card_info['BonusCard']['bonus_card_type_id'];
						$eligible_outlet_array=array_values($eligible_outlet_array);
						foreach($eligible_outlet_array as $key)
						{
							$previous_data= $this->BonusEligibleOutlet->find('first',array(
								'conditions'=>array(
									'bonus_card_id'=>$bonus_card,
									'fiscal_year_id'=>$fiscal_year_id,
									'outlet_id'=>$key,
									),
								'recursive'=>-1
								));
							if($previous_data)
							{
								$data['id']=$previous_data['BonusEligibleOutlet']['id'];
								$data['is_eligible']=$post_data['eligible_outlet'][$key];

							}
							else
							{
								$data['bonus_card_id']=$bonus_card;
								$data['bonus_type_id']=$bonus_type_id;
								$data['fiscal_year_id']=$fiscal_year_id;
								$data['outlet_id']=$key;
								$data['is_eligible']=1;
							// }
							$data_array[]=$data;
						}*/
						// $this->StoreBonusCard->saveAll($data_array);
						$this->BonusEligibleOutlet->saveAll($eligible_outlet_data_array);
						$this->Session->setFlash(__('Bonus Card Has Been Calculated'), 'flash/success');
						$this->redirect(array('action' => 'index'));		
					}
					else
					{
						$this->Session->setFlash(__('Please Try Again!'), 'flash/warning');
					}
				}
			}
		}
		public function admin_get_bonus_card()
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
		public function admin_check_calculate_before()
		{
			$conditions=array(
					'fiscal_year_id'=>$this->request->data['fiscal_year_id'],
					'bonus_card_id'=>$this->request->data['bonus_card_id']
					);
			if($this->request->data['office_id'])
			{
				$conditions['Territory.office_id']=$this->request->data['office_id'];
			}
			$check_exist=$this->StoreBonusCard->find('first',array(
				'fields'=>array('COUNT(StoreBonusCard.id) as total_data','MAX(StoreBonusCard.created_at) as created_at'),
				'conditions'=>$conditions,
				'joins'=>array(
					array(
						'table'=>'territories',
						'alias'=>'Territory',
						'conditions'=>'Territory.id=StoreBonusCard.territory_id'
						)
					),
				'recursive'=>-1
				));
			if($check_exist[0]['total_data']>0)
			{
				$exist='<div class="alert alert-warning">Already Calcuted in <b>'.date("d F Y",strtotime($check_exist[0]['created_at'])).'</b> !!!!!</div>';
			}
			else
			{
				$exist='';
			}
			echo $exist;
			$this->autoRender=false;
		}
	}
 ?>