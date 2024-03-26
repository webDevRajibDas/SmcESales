<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'php-excel-reader/excel_reader2');
/**
 * XlsMarketOutlets Controller
 *
 * @property XlsMarketOutlet $XlsMarketOutlet
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class XlsMarketOutletsController extends AppController {

/**
 * Components
 *
 * @var array
 */
 	public $uses = array('Office', 'District', 'Territory', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'ThanaTerritory');
	public $components = array('Paginator', 'Session','Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
				
		$this->set('page_title','Market and Outlet Import');
		//$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 1800); //300 seconds = 5 minutes
		
		if ($this->request->is('post')) 
		{
			
			//pr($indicators);
			//pr($this->request->data);
			
			/*$territories = $this->Territory->find('list', array(
					'conditions' => array('office_id' => 16),
					'order' => array('name' => 'asc')
				));
			pr($territories);*/
			

			
			/*$outlet_list = $this->Outlet->find('all', array(
				//'conditions' => array('market_id' => $market_id),
				'Market.territory_id' => array(125, 20096, 42, 41, 82, 20091, 128, 124, 129, 126, 127), // link as IN query
				'order' => array('Outlet.name' => 'asc'),
				'limit' => 100,
		    ));	*/
			
			//pr($outlet_list);
			
			//exit;
			
			//pr($_FILES);
			//exit;
			
			
			$types = array('application/vnd.ms-excel');
			
			//if(in_array($_FILES['import']['type'], $types))
			//{
				$target_dir = WWW_ROOT.'files/';
				$target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
				$imageFileType = pathinfo($_FILES["import"]["name"],PATHINFO_EXTENSION);
				
				//if (move_uploaded_file($_FILES["import"]["tmp_name"], $target_dir.$target_file.'.'.$imageFileType)) 
				//{
					$data_ex = new Spreadsheet_Excel_Reader('C:\xampp\htdocs\smc_sales\smc_sales_area\app\webroot\files/format-Final list of Outlet and Market Dhaka West-Checked.xls', true);
					//format-Final list of Outlet and Market Comilla (2)
					//$data_ex = new Spreadsheet_Excel_Reader($target_dir.$target_file.'.'.$imageFileType, true);
					
					$file_data = $data_ex->dumptoarray();
					$m_te=0;
					$m_th=0;
					$c_m=0;
					$c_o=0;
					$f_o=0;
					
					$f_o_n = '';
					$f_m_n = '';
					
					$e_m = 0;
					$e_o = 0;
					$total_row = 0;
					
					
					$market_ids = array();
					$outlet_ids = array();
					$te_ids = array();
					
					$thana_name = '';
					
					//pr($file_data);
					//exit;
					
					foreach ($file_data as $key => $val) 
					{
						//pr($val);
						
									
						if($key>1 && !empty($val[1]))
						{
							
							if(!empty($val[1]))
							{
							//$office_id 			= $this->get_office_id(trim($val[2]));
							
							$location_type_id 	= strtolower(trim($val[6]))=='urban'?6:7;
							
							//$thana_id	 		= $this->get_thana_id(strtolower(trim($val[4])));
							$thana_id	 		= $this->get_thana_id(strtolower(trim($val[4])), strtolower(trim($val[3])));
							
							//echo $territory_id	 	= $this->get_territory_id(strtolower(trim($val[1])));
							
							$territory_id	 	= $this->get_territory_id_2(strtolower(trim($val[1])));
							
							$market_name 		= trim($val[5]);
							$outlet_name 		= trim($val[7]);
							$category_id 		= $this->get_category_id(strtolower(trim($val[8])));
							$is_pharma_type 	= strtolower(trim($val[9]))=='pharma'?1:0;
							
							//echo $category_id .'/'.$val[8].'<br>';
							
							//exit;
							
							if($val[10] && $val[12] && $val[12]!='NULL')
							{
								$is_ngo 			= trim($val[10]);
								$institute_id 		= $this->get_institute_id(trim($val[12]));;
							}
							else
							{
								$is_ngo 			= 0;
								$institute_id 		= 0;
							}
							
							
							$market_data = array();
							$outlet_data = array();
														
							if($territory_id && $category_id)
							{	
								if($thana_id)
								{	
									$market_id 			= $this->get_market_id($market_name, $territory_id);
									
									if(!$market_id)
									{
										$market_data['Market']['code'] 				= 0;
										$market_data['Market']['name'] 				= $market_name;
										$market_data['Market']['location_type_id'] 	= $location_type_id;
										$market_data['Market']['thana_id'] 			= $thana_id;
										$market_data['Market']['territory_id'] 		= $territory_id;
										$market_data['Market']['is_active'] 		= 1;
										 
										$market_data['Market']['created_at'] 		= $this->current_datetime(); 
										$market_data['Market']['updated_at'] 		= $this->current_datetime(); 
										$market_data['Market']['created_by'] 		= $this->UserAuth->getUserId();
										$market_data['Market']['updated_by'] 		= $this->UserAuth->getUserId();
										
										
										$market_data['Market']['action'] 			= 1;
										
										$this->Market->create();
										if ($this->Market->save($market_data)) 
										{
											$market_id = $this->Market->getLastInsertId();
											
											$outlet_id = $this->get_outlet_id($outlet_name, $market_id);
											
											if(!$outlet_id)
											{
												$outlet_data['Outlet']['code'] 				= 0;
												$outlet_data['Outlet']['name'] 				= $outlet_name;
												$outlet_data['Outlet']['address'] 			= '';
												$outlet_data['Outlet']['location_type_id'] 	= $location_type_id;
												$outlet_data['Outlet']['market_id'] 		= $market_id;
												$outlet_data['Outlet']['territory_id'] 		= $territory_id;
												
												$outlet_data['Outlet']['category_id'] 		= $category_id;
												$outlet_data['Outlet']['is_pharma_type'] 	= $is_pharma_type;
												$outlet_data['Outlet']['is_ngo'] 			= $is_ngo;
												$outlet_data['Outlet']['institute_id'] 		= $institute_id;
												 
												$outlet_data['Outlet']['created_at'] 		= $this->current_datetime(); 
												$outlet_data['Outlet']['updated_at'] 		= $this->current_datetime(); 
												$outlet_data['Outlet']['created_by'] 		=$this->UserAuth->getUserId();
												$outlet_data['Outlet']['updated_by'] 		=$this->UserAuth->getUserId();
												
											
												
												$this->Outlet->create();
												if ($this->Outlet->save($outlet_data)) 
												{
													//$market_id = $this->Market->getLastInsertId();
													//echo $market_id;
													$c_o++;
												}
												else
												{
													$f_o++;
													$f_o_n.=$outlet_name.',';
												}
												
											}
											else
											{
												$e_o++;
												if (!in_array($outlet_name, $outlet_ids))
												{
													array_push($outlet_ids, $outlet_name);
												}
											}
										
											$c_m++;
										}
										else
										{
											$f_m_n.=$market_name.', ';
										}
										
									}
									else
									{
										//echo $market_id;
										
										$outlet_id = $this->get_outlet_id($outlet_name, $market_id);
										
										if(!$outlet_id)
										{
											$outlet_data['Outlet']['code'] 				= 0;
											$outlet_data['Outlet']['name'] 				= $outlet_name;
											$outlet_data['Outlet']['address'] 			= '';
											$outlet_data['Outlet']['location_type_id'] 	= $location_type_id;
											$outlet_data['Outlet']['market_id'] 		= $market_id;
											$outlet_data['Outlet']['territory_id'] 		= $territory_id;
											
											$outlet_data['Outlet']['category_id'] 		= $category_id;
											$outlet_data['Outlet']['is_pharma_type'] 	= $is_pharma_type;
											$outlet_data['Outlet']['is_ngo'] 			= $is_ngo;
											$outlet_data['Outlet']['institute_id'] 		= $institute_id;
											 
											$outlet_data['Outlet']['created_at'] 		= $this->current_datetime(); 
											$outlet_data['Outlet']['updated_at'] 		= $this->current_datetime(); 
											$outlet_data['Outlet']['created_by'] 		= $this->UserAuth->getUserId();
											$outlet_data['Outlet']['updated_by'] 		= $this->UserAuth->getUserId();
																			
											$this->Outlet->create();
											if ($this->Outlet->save($outlet_data)) 
											{
												//$market_id = $this->Market->getLastInsertId();
												//echo $market_id;
												$c_o++;
											}
											else
											{
												$f_o++;	
												$f_o_n.=$outlet_name.', ';
											}
											
											
										}
										else
										{
											
											//update outlet query
											$outlet_update_data['Outlet']['id'] = $outlet_id;
											$outlet_update_data['Outlet']['category_id'] = $category_id;
											
											$this->Outlet->id = $outlet_id;
											if ($this->Outlet->id)
											{
												if($this->Outlet->saveField('category_id', $category_id))
												{
													$e_o++;
												}
												else
												{
													echo $category_id.'/'.$outlet_id.'/'.$outlet_name;
													break;
												}
											}
											//end update outlet query
											
											//$e_o++;
											
											
											if (!in_array($outlet_name, $outlet_ids))
											{
												array_push($outlet_ids, $outlet_name);
											}
											
											
										}
										
										$e_m++;
										if (!in_array($market_name, $market_ids))
										{
										  	array_push($market_ids, $market_name);
										}
									}
									
									$total_row++;
									
								}
								else
								{
									$m_th++;
									$thana_name .= $val[4].',<br>';
									break;
								}
								
								
								
							}
							else
							{
								$m_te++;
								if (!in_array($val[1], $te_ids))
								{
									array_push($te_ids, $val[1]);
								}
								break;
							}
							
							
						  }
						    else
						    {
								echo $total_row;
								break;  
						    }
						    
							//if($key==300)break;
						}
												
					}
					
					//exit;
					
					
					if($file_data)
					{
						
						$this->set(compact('m_te', 'm_th', 'c_m', 'c_o','thana_name', 'e_m', 'e_o', 'market_ids', 'outlet_ids', 'f_o', 'f_o_n', 'f_m_n', 'te_ids', 'total_row'));
						
						/*$this->set('missing_territory', $te);
						$this->set('missing_thana', $th);
						$this->set('create_market', $c_m);
						$this->set('create_outlet', $c_o);*/
						
						$this->Session->setFlash(__('The import has been successfully!'), 'flash/success');
					}
					else
					{
						$this->Session->setFlash(__('error!'), 'flash/error');
					}
				//}
				
				
			//}
			//else
			//{
				//$this->Session->setFlash(__('Upload file must be XLS format!'), 'flash/error');
			//}
			
			//$this->redirect(array('action' => 'index'));
						
		}
		
		
	}
	
	

	
	private function get_institute_id($name='')
	{

		if($name)
		{
			$info =  $this->Outlet->Institute->find('first', array(
				'conditions' => array(
					'Institute.name' => $name,
					),
				'fields' => 'Institute.id',
				'recursive' => -1
				)
			);
			
			//pr($info);
			
			if($info){
				return $info['Institute']['id'];	
			}else{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	
	private function get_category_id($name='')
	{
		//echo $name = 'Grocery';
		//$name = 'Grocery Shop';
		if($name=='others' || $name=='')$name='Other';
		
		if($name=='pharma')
		{
			$name='pharmacy';
		}
		
		
		$name = explode(' ', $name);
		//pr($name);
		//echo $name[0];die;
		
		if($name)
		{
			$info =  $this->Outlet->OutletCategory->find('first', array(
				'conditions' => array(
					'OutletCategory.category_name LIKE' => "$name[0]%",
					),
				'fields' => 'OutletCategory.id',
				'recursive' => -1
				)
			);
			
			//pr($info);
			
			if($info){
				return $info['OutletCategory']['id'];	
			}else{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	
	private function get_territory_id($so_name=0)
	{
		if($so_name)
		{
			$info = $this->SalesPerson->find('first', array(
				'conditions' => array(
					'SalesPerson.name' => $so_name,
					),
				'fields' => 'SalesPerson.territory_id',
				'recursive' => -1
				)
			);
			//pr($info);
			if($info){
				return $info['SalesPerson']['territory_id'];	
			}else{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	
	private function get_territory_id_2($name=0)
	{
		if($name)
		{
			$info = $this->Territory->find('first', array(
				'conditions' => array(
					'Territory.name' => $name,
					),
				'fields' => 'Territory.id',
				'recursive' => -1
				)
			);
			
			if($info){
				return $info['Territory']['id'];	
			}else{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	
	private function get_thana_id($name='', $dis='')
	{
		
		if($name)
		{
			$info = $this->District->find('first', array(
				'conditions' => array(
					'District.name' => $dis,
					),
				'fields' => 'District.id',
				'recursive' => -1
				)
			);
			
			//pr($thana_info);
			
			if($info){
				$district_id = $info['District']['id'];	
			}else{
				$district_id = 0;	
			}
			
			if($district_id){
				$conditions = array(
					'Thana.name' => $name,
					'Thana.district_id' => $district_id,
					);
			}else{
				$conditions = array(
					'Thana.name' => $name,
					);
			}
			
			$thana_info = $this->Thana->find('first', array(
				'conditions' => $conditions,
				'fields' => 'Thana.id',
				'recursive' => -1
				)
			);
			
			//pr($thana_info);
			
			if($thana_info){
				return $thana_info['Thana']['id'];	
			}else{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	
	private function get_market_id($name='', $territory_id=0)
	{
		
		if($name && $territory_id)
		{
			$name = strtolower($name);
			$market_info = $this->Market->find('first', array(
				'conditions' => array(
					'Market.name' => $name,
					'Market.territory_id' => $territory_id,
					),
				'fields' => 'Market.id',
				'recursive' => -1
				)
			);
			
			//pr($market_info);
			
			if($market_info){
				return $market_info['Market']['id'];	
			}else{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	
	private function get_outlet_id($name='', $market_id=0)
	{
		//echo $name;
		
		if($name && $market_id)
		{
			$name = strtolower($name);
			
			$outlet_info = $this->Outlet->find('first', array(
				'conditions' => array(
					'Outlet.name' 		=> $name,
					'Outlet.market_id'  => $market_id,
					),
				'fields' => 'Outlet.id',
				'recursive' => -1
				)
			);
			if($outlet_info){
				return $outlet_info['Outlet']['id'];	
			}else{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	
	
}
