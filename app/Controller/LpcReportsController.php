<?php
App::uses('AppController', 'Controller');
/**
 *
 * @property LpcReport $LpcReportsController
 */
 //Configure::write('debug', 2);

class LpcReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Memo', 'MemoDetail', 'Office', 'Territory', 'SalesPerson');
	public $components = array('Paginator', 'Session');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function get_office_by_id($so_id){
		$office_id = $so_id;

		$offices = $this->Office->find('all', array(
			'fields'		=> array('Office.id', 'Office.office_name'),
			'conditions'	=> array(
				'id' 		=> $office_id,
			),
			'recursive'		=>-1
		));
		return $offices[0]['Office'];
	}
	public function get_territories_by_id($territory_id){
		$territories = $this->Territory->find('all', array(
			'fields' => array(
				'Territory.id',
				"Territory.name + ' -(' + SalesPerson.name + ')' AS Territory_Name"
			),
			'conditions' => array('Territory.id' => $territory_id),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));
		return array(
			'id'		=> $territories[0]['Territory']['id'],
			'name'		=> $territories[0][0]['Territory_Name']
		);
	}
	public function get_so_by_id($so_id){
		$SalesOfficer = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id', 'SalesPerson.name'),
			'conditions' => array(
				'SalesPerson.id' => $so_id
			),
			'recursive' => -1
		));
		return $SalesOfficer[0]['SalesPerson'];
	}
	public function admin_index($id = null){

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0); //300 seconds = 5 minutes
		$this->set('page_title', "Line Per Call Report");
		$territories = array();
		$request_data = array();
		$types = array();
		$so_list = array();

		$request_data = $this->request->data;

		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));


		//types
		$types = array(
			'region' => 'By Region',
			'area' => 'By Area',
			'territory' => 'By Territory',
			'so' => 'By SO'
		);

		$this->set(compact('types'));

		$region_office_id = 0;

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set(compact('office_parent_id'));

		if ($office_parent_id == 0) {
			$office_id = 0;
			if($this->request->data['office_id']){
				$offices = $this->get_office_by_id($this->request->data['office_id']);
			}
			if($this->request->data['territory_id']){
				$territories = $this->get_territories_by_id($this->request->data['territory_id']);
			}
			if($this->request->data['so_id']){
				$so_list = $this->get_so_by_id($this->request->data['so_id']);
			}
		}
		elseif ($office_parent_id == 14) {
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id' => 3, 'Office.id' => $region_office_id),
				'order' => array('office_name' => 'asc')
			));

			$office_conditions = array('Office.parent_office_id' => $region_office_id);
			$office_id = 0;
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));
			$office_ids = array_keys($offices);
			if ($office_ids) $conditions['Territory.office_id'] = $office_ids;
		}
		else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);

			$office_id = $this->UserAuth->getOfficeId();

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'id' 	=> $office_id,
				),
				'order' => array('office_name' => 'asc')
			));

            //$this->dd($offices);exit();
			/*$territories = $this->Territory->find('list', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'order' => array('Territory.name' => 'asc')
				));	*/

			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.id = User.sales_person_id'
					)
				),
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));

			$territories = array();

			foreach ($territory_list as $key => $value) {
				$territories[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			}

			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive' => 0
			));
			foreach ($so_list_r as $key => $value) {
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
			}
		}

        if ($this->request->is('post') || $this->request->is('put')) {
            //$this->dd($this->request->data[]);exit();


            $date_from = date('Y-m-d', strtotime($this->request->data['LpcReports']['date_from']));
            $date_to = date('Y-m-d', strtotime($this->request->data['LpcReports']['date_to']));
            $this->set(compact('date_from', 'date_to'));

            $type = $this->request->data['LpcReports']['type'];
            $this->set(compact('type'));


            if($this->request->data['LpcReports']['region_office_id']){
                $header_cap['region'] = $region_offices[$this->request->data['LpcReports']['region_office_id']];
                $where = " and o.parent_office_id=".$this->request->data['LpcReports']['region_office_id'];
            }
            if($this->request->data['office_id']){
                $office_info = $this->Office->find('list', array(
                    'conditions' => array(
                        'id' 	=> $this->request->data['office_id'],
                    ),
                    'recursive'	=>-1,
                ));
                $header_cap['office']= $office_info[$this->request->data['office_id']];
                $where = " and o.id=".$this->request->data['office_id'];
            }
            if($this->request->data['territory_id']){
                $territory_info = $this->Territory->find('list', array(
                    'conditions' => array('Territory.id' => $this->request->data['territory_id']),
                    'recursive' => -1
                ));
                $header_cap['territory']= $territory_info[$this->request->data['territory_id']];
                $where = " and t.id=".$this->request->data['territory_id'];
            }
            if($this->request->data['so_id']){
                $where = " and mv.sales_person_id=".$this->request->data['so_id'];
            }
            //$this->dd([$this->request->data,$header_cap]);

            if($this->request->data['submit']==='Submit'){
                if($this->request->data['LpcReports']['type']==='so'){
                    $columns = ['SL.','Area Office', 'Territory', 'SO', 'Total Memos', 'Total Quantity', 'LPC'];
                    $fields = "
							o.office_name,
							t.name as territory_name,
							s.name as so_name,
							mv.total_memos,
							mv.total_sales_qty";
                    $go_by ="
							ORDER BY
							mv.office_id,
							mv.territory_id,
							mv.sales_person_id";
                }elseif($this->request->data['LpcReports']['type']==='territory'){
                    $columns = ['SL.','Area Office', 'Territory', 'Total Memos', 'Total Quantity', 'LPC'];
                    $fields = "
							o.office_name,
							t.name as territory_name,
							sum(mv.total_memos) as total_memos,
							sum(mv.total_sales_qty) as total_sales_qty ";
                    $go_by ="
							group by 
							mv.office_id,
							o.office_name,
							mv.territory_id,
							t.name
							
							ORDER BY
							mv.office_id,
							mv.territory_id";
                }elseif($this->request->data['LpcReports']['type']==='area'){
                    $columns = ['SL.', 'Region', 'Area Office', 'Total Memos', 'Total Quantity', 'LPC'];
                    $fields = "
							o.parent_office_id as region,
							o.office_name,
							sum(mv.total_memos) as total_memos,
							sum(mv.total_sales_qty) as total_sales_qty ";
                    $go_by ="
							group by
							o.parent_office_id,
							mv.office_id,
							o.office_name
							
							ORDER BY
							o.parent_office_id,
							mv.office_id";
                }else{
                    $columns = ['SL.','Region', 'Total Memos', 'Total Quantity', 'LPC'];
                    $fields = "
							o.parent_office_id as region,
							sum(mv.total_memos) as total_memos,
							sum(mv.total_sales_qty) as total_sales_qty ";
                    $go_by ="
							group by
							o.parent_office_id
							ORDER BY
							o.parent_office_id";
                }
                $sql_cmd = "
					select
						".$fields."
					from (
						SELECT 
							mt.office_id, 
							mt.territory_id,
							mt.sales_person_id,
							count(mt.memo_id) as total_memos,
							sum(mt.msales_qty) as total_sales_qty
						FROM (
							SELECT
								m.id as memo_id,
								m.office_id,
								m.territory_id,
								m.sales_person_id,
								SUM(md.sales_qty) AS msales_qty
							from memo_details md
								LEFT JOIN memos m ON md.memo_id=m.id
							where 
								md.price>0
								AND m.memo_date BETWEEN '".$date_from."' AND '".$date_to."'
								AND m.is_distributor=0
							group by 
								m.office_id,
								m.territory_id,
								m.sales_person_id,
								m.id
						) mt
						group by
						mt.office_id, 
						mt.territory_id,
						mt.sales_person_id
					) mv
					LEFT JOIN offices o on mv.office_id=o.id
					LEFT JOIN territories t on mv.territory_id=t.id
					LEFT JOIN sales_people s on mv.sales_person_id=s.id
					WHERE 1=1 ".$where."
					".$go_by;
                //WHERE o.office_type_id='2'

            }
            $results = $this->MemoDetail->query($sql_cmd);

            if($this->request->data['LpcReports']['type']==='area'){
                $return_data = NULL;
                foreach($results as $result){
                    foreach($result as $val){
                        $val['region']	= $region_offices[$val['region']];
                        $return_data[] =$val;
                    }
                }
            }elseif($this->request->data['LpcReports']['type']==='region'){
                foreach($results as $result){
                    foreach($result as $val){
                        $val['region']	= $region_offices[$val['region']];
                        $return_data[] =$val;
                    }
                }
            }else{
                foreach($results as $result){
                    foreach($result as $val){
                        $return_data[] =$val;
                    }
                }
            }
            $results = $return_data;
            //$this->dd($val);
            //$this->dd($return_data);
            //$this->dd($columns,false);
            //$this->dd($results);
            $this->set(compact('columns','results'));
        }


		$this->set(compact('region_offices', 'offices', 'territories', 'so_list', 'office_id', 'request_data', 'header_cap'));
	}
}
