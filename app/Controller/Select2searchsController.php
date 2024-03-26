
<?php
App::uses('AppController', 'Controller');
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
/*
Select to search controller
 */
class Select2searchsController extends AppController
{

	public $uses = array('ProductCategory', 'User', 'Office', 'Territory', 'SalesPerson');
	private $return_data = array();
	
	public function GetCategoryID(){
		if ($this->request->is('post')){
			$rdata = array();
			$getVar = isset($this->request->data['field_val'])?$this->request->data['field_val']:NULL;
			if(isset($getVar)){
				$condition = array(
					'OR' => array(
						array('ProductCategory.id LIKE' => "%".$getVar."%",),
						array('ProductCategory.name LIKE' => "%".$getVar."%",)
					)
				);
			}else{
				$condition = NULL;
			}
			$category_list = $this->ProductCategory->find('all', array(
				'fields'			=> array('ProductCategory.id','ProductCategory.name'),
				'order'				=> array('ProductCategory.name' => 'asc'),
				'conditions'		=> $condition,
				'limit' => isset($this->request->data['page_limit'])?$this->request->data['page_limit']:null,
				'recursive' => 0
			));
			foreach($category_list as $category){
				$data[]= array(
					'id'	=> $category['ProductCategory']['id'],
					'text'	=> utf8_encode($category['ProductCategory']['name'])
				);
			}
			$this->return_data['results'] = $data;
			echo json_encode($this->return_data);
		}
		$this->autoRender=false;
	}
	public function GetProgramOfficerId(){
		if ($this->request->is('post')){
			$rdata = array();
			$getVar = isset($this->request->data['field_val'])?$this->request->data['field_val']:NULL;
	
			$condition = array(
				'User.user_group_id'	=>1016,
				'User.active'			=>1,
			);
			
			if(isset($this->request->data['prVal'])){
				$condition['SalesPerson.office_id'] = $this->request->data['prVal'];
			}
			if(isset($getVar)){
				$condition['OR'] = array(
					array('User.id LIKE' => "%".$getVar."%",),
					array('SalesPerson.name LIKE' => "%".$getVar."%",)
				);
			}
			$data_list = $this->User->find('all', array(
				'fields'			=> array('User.id','SalesPerson.name'),
				'order'				=> array('SalesPerson.name' => 'asc'),
				'conditions'		=> $condition,
				'joins'=>array(
					array(
						'alias'=>'SalesPerson',
						'table'=>'sales_people',
						'type'=>'left',
						'conditions'=>'User.sales_person_id=SalesPerson.id'
					)
				),
				'limit' => isset($this->request->data['page_limit'])?$this->request->data['page_limit']:null,
				'recursive' => -1
			));
			foreach($data_list as $data){
				$rdata[]= array(
					'id'	=> $data['User']['id'],
					'text'	=> utf8_encode('['.$data['User']['id'].']-'.$data['SalesPerson']['name'])
				);
			}
			$this->return_data['results'] = $rdata;
			echo json_encode($this->return_data);
		}
		$this->autoRender=false;
	}
	public function GetOfficeId(){
		if ($this->request->is('post')){
			$rdata = array();
			$getVar = isset($this->request->data['field_val'])?$this->request->data['field_val']:NULL;

			$condition = array(
				'NOT' => array("id" => array(30, 31, 37)),
				'Office.office_type_id' => 2
			);

			if(isset($this->request->data['prVal'])){
				$condition['Office.parent_office_id'] = $this->request->data['prVal'];
			}
			if(isset($getVar)){
				$condition['OR'] = array(
					array('Office.id LIKE' => "%".$getVar."%"),
					array('Office.office_name LIKE' => "%".$getVar."%")
				);
			}
			$data_list = $this->Office->find('all', array(
				'fields'			=> array('id', 'office_name'),
				'order'				=> array('office_name' => 'asc'),
				'conditions'		=> $condition,
				'limit' => isset($this->request->data['page_limit'])?$this->request->data['page_limit']:null,
				'recursive' => -1
			));
			foreach($data_list as $data){
				$rdata[]= array(
					'id'	=> $data['Office']['id'],
					'text'	=> utf8_encode('['.$data['Office']['id'].']-'.$data['Office']['office_name'])
				);
			}
			$this->return_data['results'] = $rdata;
			echo json_encode($this->return_data);
		}
		$this->autoRender=false;
	}
	public function GetTerritoryId(){
		if ($this->request->is('post')){
			$rdata = array();
			$getVar = isset($this->request->data['field_val'])?$this->request->data['field_val']:NULL;

			/***Show Except Parent(Who has Child) Territory ***/
			$child_territory_parent_id = $this->Territory->find('list',array(
				'conditions'=> array(
					'parent_id !=' => 0,
				),
				'fields'=>array('Territory.parent_id','Territory.name'),
				
			));

			if(isset($this->request->data['prVal'])){
				$condition['Territory.office_id'] = $this->request->data['prVal'];
			}
			if(isset($getVar)){
				$condition['OR'] = array(
					array('Territory.id LIKE' => "%".$getVar."%"),
					array('Territory.name LIKE' => "%".$getVar."%")
				);
			}
			$condition['NOT'] = array('Territory.id'=>array_keys($child_territory_parent_id));

			$data_list = $this->Territory->find('all', array(
				'fields'			=> array('Territory.id', 'Territory.name','SalesPerson.name'),
				'order'				=> array('Territory.name' => 'asc'),
				'conditions'		=> $condition,
				'limit' => isset($this->request->data['page_limit'])?$this->request->data['page_limit']:null,
				'recursive' => 0
			));
			foreach($data_list as $data){
				$rdata[]= array(
					'id'	=> $data['Territory']['id'],
					'text'	=> utf8_encode($data['Territory']['name'].'-('.$data['SalesPerson']['name'].')')
				);
			}
			$this->return_data['results'] = $rdata;
			echo json_encode($this->return_data);
		}
		$this->autoRender=false;
	}
	public function GetSoId(){
		if ($this->request->is('post')){
			$rdata = array();

			$conditions = array(
				'User.user_group_id' => array(4, 1008),
				'User.active' => 1,
				'SalesPerson.territory_id >' => 0,
			);

			if(isset($this->request->data['prVal'])){
				$condition['SalesPerson.office_id'] = $this->request->data['prVal'];
				$conditions = array('Territory.office_id' => $this->request->data['prVal'], 'TerritoryAssignHistory.assign_type' => 2);
			}
			
			$getVar = isset($this->request->data['field_val'])?$this->request->data['field_val']:NULL;
			if(isset($getVar)){
				$condition['OR'] = array(
					array('SalesPerson.id LIKE' => "%".$getVar."%"),
					array('SalesPerson.name LIKE' => "%".$getVar."%")
				);
			}
			$data_list = $this->SalesPerson->find('all', array(
				'fields'			=> array('SalesPerson.id','SalesPerson.name'),
				'conditions'		=> $condition,
				'order'				=> array('SalesPerson.name' => 'asc'),
				'limit' => isset($this->request->data['page_limit'])?$this->request->data['page_limit']:null,
				'recursive' => 0
			));
			
			//// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
			//$conditions['TerritoryAssignHistory.date >= '] = $date_from;
			////pr($conditions);
			//$old_so_list = $this->TerritoryAssignHistory->find('all', array(
			//	'conditions' => $conditions,
			//	'order' =>  array('Territory.name' => 'asc'),
			//	'recursive' => 0
			//));
			////pr($old_so_list);
			////exit;
			//if ($old_so_list) {
			//	foreach ($old_so_list as $old_so) {
			//		$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
			//	}
			//}
			
			foreach($data_list as $data){
				$rdata[]= array(
					'id'	=> $data['SalesPerson']['id'],
					'text'	=> utf8_encode('['.$data['SalesPerson']['id'].']-'.$data['SalesPerson']['name'])
				);
			}
			$this->return_data['results'] = $rdata;
			echo json_encode($this->return_data);
		}
		$this->autoRender=false;
	}
}