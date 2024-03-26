
<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
/**
 * VisitPlanLists Controller
 *
 * @property MapSalesTrack $MapSalesTrack
 * @property PaginatorComponent $Paginator
 */
class MapSalesTracksController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		//$this->set('page_title', 'Map Sale Track');

		//$this->paginate = array('conditions' => '', 'recursive' => 0);

		//pr($this->paginate());
		//exit;

		/*$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		if($office_parent_id)
		{
			$conditions = array('SalesPerson.office_id' => $this->UserAuth->getOfficeId());
			$this->paginate = array(
				'conditions' => $conditions,
				'order' => array('id' => 'DESC')
			);	
		}
		else
		{
			$this->paginate = array('order' => array('id' => 'DESC'));
		}
						
		$this->set('MapSalesTracks', $this->paginate());*/


		$this->set('page_title', 'View on Map');

		$datalists = array();

		$types = array(
			'1' => 'Location',
			'2' => 'Travel Path',
		);
		$this->set('types', $types);



		//pr($datalists);
		//exit;


		$request = '';
		if (isset($this->request->data['MapSalesTrack'])) {
			$request = $this->request->data;
		}








		//office
		$office_id = (isset($this->request->data['MapSalesTrack']['office_id']) ? $this->request->data['MapSalesTrack']['office_id'] : 0);

		$so_id = (isset($this->request->data['MapSalesTrack']['so_id']) ? $this->request->data['MapSalesTrack']['so_id'] : 0);


		$this->loadModel('Office');
		//$offices = $this->Office->find('list', array('conditions'=> '', 'order'=>array('office_name'=>'asc')));
		/*$offices = $this->Office->find('list', array(
			'conditions'=> array(
				'office_type_id' => 2,
				"NOT" => array( "id" => array(30, 31, 37))
				), 
			'order'=>array('office_name'=>'asc'),
			'recursive'=>-1
		));*/


		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id != 0) {
			$office_type = $this->Office->find('first', array(
				'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
				'recursive' => -1
			));
			$office_type_id = $office_type['Office']['office_type_id'];
		}

		if ($office_parent_id == 0) {
			$office_conditions = array('office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
		} else {
			if ($office_type_id == 3) {
				$office_parent_id = 0;
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			} elseif ($office_type_id == 2) {
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			}
		}

		$offices = $this->Office->find('list', array(
			'conditions' => $office_conditions,
			'fields' => array('office_name')
		));

		/*$offices_2 = array();
		foreach($offices as $key => $val){
			$offices_2[$key] = str_replace('Sales Office', '', $val);
		}
		$this->set('offices_2', $offices_2);*/
		//pr($offices_2);
		//exit;


		//for so user by office id
		$this->loadModel('User');
		$this->loadModel('SalesPerson');
		// $office_parent_id = $this->UserAuth->getOfficeParentId();		

		if ($office_parent_id == 0) {
			if ($office_type_id == 3) {
				$user_conditions = array(
					'active' => 1,
					'user_group_id' => array(4, 1008), //change here for SPO
					'SalesPeople.territory_id >' => 0,
					'Office.parent_office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
					"NOT" => array("SalesPeople.office_id" => array(30, 31, 37))
				);
			} else {
				$user_conditions = array(
					'active' => 1,
					'user_group_id' => array(4, 1008), //change here for SPO
					'SalesPeople.territory_id >' => 0,
					"NOT" => array("SalesPeople.office_id" => array(30, 31, 37))
				);
			}
		} else {
			$user_conditions = array(
				'active' => 1,
				'user_group_id' => array(4, 1008), //change here for SPO
				'SalesPeople.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
			);
		}

		if ($office_id && $office_parent_id == 0) {
			$user_conditions = array(
				'active' => 1,
				'user_group_id' => array(4, 1008), //change here for SPO
				'SalesPeople.office_id' => $office_id, //change here for SPO
			);
		}

		//pr($user_conditions);

		$so_user_dropdown = $this->SalesPerson->find(
			'all',
			array(
				'conditions' => $user_conditions,
				'joins' => array(
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'User.sales_person_id = SalesPeople.id'
					)
				),
				'fields' => 'User.username, User.sales_person_id, SalesPeople.name, SalesPeople.office_id, SalesPeople.territory_id',
				'order' => array('SalesPeople.office_id, SalesPeople.name' => 'asc')
			)
		);

		/*echo $this->SalesPerson->getLastQuery();
		pr($so_user_dropdown);
		exit;*/

		$so = array();
		foreach ($so_user_dropdown as $so_list) {
			$so[$so_list['User']['sales_person_id']] = $so_list['SalesPeople']['name'];
		}

		$so_user_dropdown = $so;

		//pr($so_user_dropdown);

		//end so user by office id

		//pr($request['MapSalesTrack']);



		if (@$request['MapSalesTrack']['type'] == 2) {
			$datalists = array();
			$so_datalists = array();

			$date = date('Y-m-d', strtotime($request['MapSalesTrack']['date']));

			foreach ($so_user_dropdown as $key => $value) {
				$conditions = array(
					'so_id' => $key,
					'MapSalesTrack.latitude >' => 0,
					'MapSalesTrack.longitude >' => 0,
					'Convert(Date, created) <=' => $date
				);

				//pr($conditions);

				$last_dates = $this->MapSalesTrack->find(
					'first',
					array(
						'conditions' => $conditions,
						'fields' => array('MapSalesTrack.created'),
						'order' => array('MapSalesTrack.created' => 'desc'),
						'recursive' => -1
					)
				);
				//pr($last_dates);

				foreach ($last_dates as $date_val) {
					//pr($date_val);
					//echo $date_val['created'];

					$conditions = array(
						'so_id' => $key,
						'MapSalesTrack.latitude >' => 0,
						'MapSalesTrack.longitude >' => 0,
						'Convert(Date, created)' => date('Y-m-d', strtotime($date_val['created']))
					);

					$datalists = $this->MapSalesTrack->find(
						'all',
						array(
							'conditions' => $conditions,
							'joins' => array(
								array(
									'alias' => 'Territory',
									'table' => 'territories',
									'type' => 'INNER',
									'conditions' => 'SalesPerson.territory_id = Territory.id'
								)
							),
							'joins' => array(
								array(
									'alias' => 'Office',
									'table' => 'offices',
									'type' => 'INNER',
									'conditions' => 'Office.id = SalesPerson.office_id'
								)
							),
							'fields' => array('MapSalesTrack.*', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'Office.office_name'),
							'order' => array('MapSalesTrack.created' => 'desc'),
							'recursive' => 1
						)
					);
				}


				foreach ($datalists as $d_val) {
					//pr($d_val);
					$territory_name = $this->get_territory_name($d_val['SalesPerson']['territory_id']);

					$so_datalists[$d_val['SalesPerson']['territory_id']][] = array(
						'name' 				=> $d_val['SalesPerson']['name'],
						'office_id' 		=> $d_val['SalesPerson']['office_id'],
						'office_name' 		=> $d_val['Office']['office_name'],
						'territory_id' 		=> $d_val['SalesPerson']['territory_id'],
						'territory_name' 	=> $territory_name,
						'so_id' 			=> $d_val['MapSalesTrack']['so_id'],
						'latitude' 			=> $d_val['MapSalesTrack']['latitude'],
						'longitude' 		=> $d_val['MapSalesTrack']['longitude'],
						'created' 			=> $d_val['MapSalesTrack']['created'],

					);
				}

				//break;
			}

			//pr($so_datalists);
			$this->set('so_datalists', $so_datalists);


			$loaction = true;
			$territory_ids = array();
			if ($so_datalists) {
				foreach ($so_datalists as $key => $val) {
					if ($key)
						array_push($territory_ids, $key);
				}
			}
			/* pr($territory_ids);
			exit; */
			$this->set('territory_ids', $territory_ids);
		} else {
			$datalists = array();

			if (@$request['MapSalesTrack']['date']) {
				$date = date('Y-m-d', strtotime($request['MapSalesTrack']['date']));
			} else {
				$date = date('Y-m-d');
			}

			foreach ($so_user_dropdown as $key => $value) {
				$conditions = array(
					'so_id' => $key,
					'MapSalesTrack.latitude >' => 0,
					'MapSalesTrack.longitude >' => 0,
					'Convert(Date, created) <=' => $date
				);

				$datalists[] = $this->MapSalesTrack->find(
					'first',
					array(
						'conditions' => $conditions,
						'joins' => array(
							array(
								'alias' => 'Territory',
								'table' => 'territories',
								'type' => 'INNER',
								'conditions' => 'SalesPerson.territory_id = Territory.id'
							)
						),
						'joins' => array(
							array(
								'alias' => 'Office',
								'table' => 'offices',
								'type' => 'INNER',
								'conditions' => 'Office.id = SalesPerson.office_id'
							)
						),
						'fields' => array('MapSalesTrack.*', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.contact', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'Office.office_name'),
						'order' => array('MapSalesTrack.id' => 'desc'),
						'recursive' => 1
					)
				);
			}


			//pr($datalists);
			//exit;

			$latitude = 23.778904;
			$longitude = 90.414734;
			$loaction = '';
			$territory_ids = array();
			if ($datalists) {
				$i = 1;
				foreach ($datalists as $datalist) {
					if ($datalist) {
						if ($datalist['MapSalesTrack']['latitude'] > 0 && $datalist['MapSalesTrack']['longitude'] > 0 && $datalist['SalesPerson']['territory_id'] > 0) {
							if ($i == 3) {
								$latitude = $datalist['MapSalesTrack']['latitude'];
								$longitude = $datalist['MapSalesTrack']['longitude'];
							}

							$territory_name = $this->get_territory_name($datalist['SalesPerson']['territory_id']);
							//$territory_name = $datalist['Territory']['name'];

							$date_check = (date('Y-m-d', strtotime($datalist['MapSalesTrack']['created'])) == $date) ? 0 : 1;

							if ((@$request['MapSalesTrack']['office_id']) || $office_parent_id) {
								$loaction .= '["<h4>' . $datalist['SalesPerson']['name'] . '</h4><p>Territory : ' . $territory_name . '<br>Office : ' . $datalist['Office']['office_name'] . '<br>Contact : ' . $datalist['SalesPerson']['contact'] . '<br>Datetime : ' . date('d M, Y, h:i a', strtotime($datalist['MapSalesTrack']['created'])) . '</p><a href=' . BASE_URL . 'admin/map_sales_tracks/view?so_id=' . $datalist['SalesPerson']['id'] . '&office_id=' . $datalist['SalesPerson']['office_id'] . '&date=' . date('d-m-Y', strtotime($datalist['MapSalesTrack']['created'])) . '>Travel Path</a>", ' . $datalist['MapSalesTrack']['latitude'] . ', ' . $datalist['MapSalesTrack']['longitude'] . ', ' . $datalist['SalesPerson']['territory_id'] . ', ' . $date_check . '],';
							} else {
								$loaction .= '["<h4>' . $datalist['SalesPerson']['name'] . '</h4><p>Territory : ' . $territory_name . '<br>Office : ' . $datalist['Office']['office_name'] . '<br>Contact : ' . $datalist['Office']['contact'] . '<br>Datetime : ' . date('d M, Y, h:i a', strtotime($datalist['MapSalesTrack']['created'])) . '</p><a href=' . BASE_URL . 'admin/map_sales_tracks/view?so_id=' . $datalist['SalesPerson']['id'] . '&office_id=' . $datalist['SalesPerson']['office_id'] . '&date=' . date('d-m-Y', strtotime($datalist['MapSalesTrack']['created'])) . '>Travel Path</a>", ' . $datalist['MapSalesTrack']['latitude'] . ', ' . $datalist['MapSalesTrack']['longitude'] . ', ' . $datalist['SalesPerson']['office_id'] . ', ' . $date_check . '],';
							}

							array_push($territory_ids, $datalist['SalesPerson']['territory_id']);

							$i++;
						}
					}
				}
			}
		}

		$total_sos = $i;





		if ($office_parent_id) {
			$office_id = $this->UserAuth->getOfficeId();
		}




		if ((@$request['MapSalesTrack']['office_id']) || $office_parent_id) {
			$zoom = 9;
			$this->loadModel('Territory');
			$territories = $this->Territory->find('list', array(
				'conditions' => array('id' => $territory_ids, 'office_id' => $office_id),
				'order' => array('name' => 'asc')
			));


			//Start Create Folder
			$path = ROOT . '/app/View/Themed/CakeAdminLTE//webroot/img/map-icon/office/' . $office_id;
			$folder = new Folder($path, true, 0755);

			$path = ROOT . '/app/View/Themed/CakeAdminLTE//webroot/img/map-icon/office/' . $office_id . '/x/';
			$folder_x = new Folder($path, true, 0755);
			if ($folder_x->path) {
				//Start Image Copy
				$imagePath = ROOT . '/app/View/Themed/CakeAdminLTE/webroot/img/map-icon/' . $office_id . '.png';
				$newPath = ROOT . '/app/View/Themed/CakeAdminLTE//webroot/img/map-icon/office/' . $office_id . '/';
				$ext = '.png';
				foreach ($territories as $key => $val) {
					$newName  = $newPath . "$key" . $ext;
					$copied = copy($imagePath, $newName);
				}

				//for cross image
				$imagePath = ROOT . '/app/View/Themed/CakeAdminLTE/webroot/img/map-icon/x/' . $office_id . '-x.png';
				$newPath = ROOT . '/app/View/Themed/CakeAdminLTE//webroot/img/map-icon/office/' . $office_id . '/x/';
				foreach ($territories as $key => $val) {
					$newName  = $newPath . "$key" . '-x' . $ext;
					$copied = copy($imagePath, $newName);
				}
				//End Image Copy
			}
			//End Create Folder

			//exit;
		} else {
			$zoom = 7;
			$territories = '';
		}



		$this->set(compact('so_user_dropdown', 'latitude', 'longitude', 'userCoor', 'userCoorPath', 'offices', 'office_parent_id', 'office_id', 'loaction', 'zoom', 'territories', 'request', 'total_sos'));
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view()
	{
		$this->set('page_title', 'View on Map');

		$datalists = array();



		if (isset($this->params['url']['so_id'])) {
			$this->request->data['MapSalesTrack']['date'] = $this->params['url']['date'];
			//$this->request->data['MapSalesTrack']['date'] = '02-01-2018';
			$this->request->data['MapSalesTrack']['so_id'] = $this->params['url']['so_id'];
			$this->request->data['MapSalesTrack']['office_id'] = $this->params['url']['office_id'];
		}


		$datalists = array();

		/*$office_parent_id = $this->UserAuth->getOfficeParentId();*/
		$this->LoadModel('Office');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id != 0) {
			$office_type = $this->Office->find('first', array(
				'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
				'recursive' => -1
			));
			$office_type_id = $office_type['Office']['office_type_id'];
		}

		if ($office_parent_id == 0) {
			$office_conditions = array('office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
		} else {
			if ($office_type_id == 3) {
				$office_parent_id = 0;
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			} elseif ($office_type_id == 2) {
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			}
		}

		if ($office_parent_id) {
			if ($office_type_id != 3) {
				$office_id = $this->UserAuth->getOfficeId();
			}
		} else {
			$office_id = @$this->request->data['MapSalesTrack']['office_id'] ? $this->request->data['MapSalesTrack']['office_id'] : 0;
		}

		$offices = $this->Office->find('list', array(
			'conditions' => $office_conditions,
			'fields' => array('office_name')
		));
		//echo $this->request->data['MapSalesTrack']['date'];
		//exit;


		if (@$this->request->data) {
			$date = date('Y-m-d', strtotime($this->request->data['MapSalesTrack']['date']));


			if (@$this->request->data['MapSalesTrack']['so_id']) {
				$conditions = array(
					'so_id' => $this->request->data['MapSalesTrack']['so_id'],
					'MapSalesTrack.latitude >' => 0,
					'MapSalesTrack.longitude >' => 0,
					'Convert(Date, created)' => $date
				);

				//pr($conditions);


				$datalists = $this->MapSalesTrack->find(
					'all',
					array(
						'conditions' => $conditions,
						'order' => array('MapSalesTrack.id' => 'asc'),
						'recursive' => 1
					)
				);


				$this->set('datalists', $datalists);

				$latitude = 23.778904;
				$longitude = 90.414734;

				if ($datalists) {
					foreach ($datalists as $datalist) {
						if ($datalist['MapSalesTrack']['latitude'] > 0 && $datalist['MapSalesTrack']['longitude'] > 0) {
							$latitude = $datalist['MapSalesTrack']['latitude'];
							$longitude = $datalist['MapSalesTrack']['longitude'];
							break;
						}
					}
				}

				$userCoor = '';
				$userCoorPath = '';

				foreach ($datalists as $datalist) {
					//pr($datalist);
					if ($datalist['MapSalesTrack']['latitude'] > 0 && $datalist['MapSalesTrack']['longitude'] > 0) {
						$userCoor .= '["<p>Time : ' . date('h:i A', strtotime($datalist['MapSalesTrack']['created'])) . '</p>' . $datalist['MapSalesTrack']['latitude'] . ', ' . $datalist['MapSalesTrack']['longitude'] . '", ' . $datalist['MapSalesTrack']['latitude'] . ', ' . $datalist['MapSalesTrack']['longitude'] . '],';
						$userCoorPath .= '{lat: ' . $datalist['MapSalesTrack']['latitude'] . ', lng: ' . $datalist['MapSalesTrack']['longitude'] . '},';
					}
				}

				//pr($datalists);
				//exit;

			} else {
				echo 'here_else_data';
				$this->loadModel('SalesPerson');




				if ($office_id) {
					$user_conditions = array(
						'active' => 1,
						'user_group_id' => array(4, 1008), //change here for SPO
						'SalesPeople.territory_id >' => 0,
						'SalesPeople.office_id' => $office_id, //change here for SPO
					);
				} else {
					$user_conditions = array(
						'active' => 1,
						'user_group_id' => array(4, 1008), //change here for SPO
						'SalesPeople.territory_id >' => 0,
						'Office.parent_office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
						"NOT" => array("SalesPeople.office_id" => array(30, 31, 37))
					);
				}
				//pr($user_conditions);

				$so_user_dropdown = $this->SalesPerson->find(
					'all',
					array(
						'conditions' => $user_conditions,
						'joins' => array(
							array(
								'alias' => 'SalesPeople',
								'table' => 'sales_people',
								'type' => 'INNER',
								'conditions' => 'User.sales_person_id = SalesPeople.id'
							)
						),
						'fields' => 'User.username, User.sales_person_id, SalesPeople.name, SalesPeople.office_id, SalesPeople.territory_id',
						'order' => array('SalesPeople.office_id, SalesPeople.name' => 'asc')
					)
				);


				$so = array();
				foreach ($so_user_dropdown as $so_list) {
					$so[$so_list['User']['sales_person_id']] = $so_list['SalesPeople']['name'];
				}

				$so_user_dropdown = $so;


				$datalists = array();
				$so_datalists = array();



				foreach ($so_user_dropdown as $key => $value) {
					$conditions = array(
						'so_id' => $key,
						'MapSalesTrack.latitude >' => 0,
						'MapSalesTrack.longitude >' => 0,
						'Convert(Date, created)' => $date
					);

					//pr($conditions);

					$last_dates = $this->MapSalesTrack->find(
						'first',
						array(
							'conditions' => $conditions,
							'fields' => array('MapSalesTrack.created'),
							'order' => array('MapSalesTrack.created' => 'desc'),
							'recursive' => -1
						)
					);
					//pr($last_dates);

					foreach ($last_dates as $date_val) {
						//pr($date_val);
						//echo $date_val['created'];

						$conditions = array(
							'so_id' => $key,
							'MapSalesTrack.latitude >' => 0,
							'MapSalesTrack.longitude >' => 0,
							'Convert(Date, created)' => date('Y-m-d', strtotime($date_val['created']))
						);

						$datalists = $this->MapSalesTrack->find(
							'all',
							array(
								'conditions' => $conditions,
								'joins' => array(
									array(
										'alias' => 'Territory',
										'table' => 'territories',
										'type' => 'INNER',
										'conditions' => 'SalesPerson.territory_id = Territory.id'
									)
								),
								'joins' => array(
									array(
										'alias' => 'Office',
										'table' => 'offices',
										'type' => 'INNER',
										'conditions' => 'Office.id = SalesPerson.office_id'
									)
								),
								'fields' => array('MapSalesTrack.*', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'Office.office_name'),
								'order' => array('MapSalesTrack.created' => 'desc'),
								'recursive' => 1
							)
						);
					}


					foreach ($datalists as $d_val) {
						//pr($d_val);
						$territory_name = $this->get_territory_name($d_val['SalesPerson']['territory_id']);

						$so_datalists[$d_val['SalesPerson']['territory_id']][] = array(
							'name' 				=> $d_val['SalesPerson']['name'],
							'office_id' 		=> $d_val['SalesPerson']['office_id'],
							'office_name' 		=> $d_val['Office']['office_name'],
							'territory_id' 		=> $d_val['SalesPerson']['territory_id'],
							'territory_name' 	=> $territory_name,
							'so_id' 			=> $d_val['MapSalesTrack']['so_id'],
							'latitude' 			=> $d_val['MapSalesTrack']['latitude'],
							'longitude' 		=> $d_val['MapSalesTrack']['longitude'],
							'created' 			=> $d_val['MapSalesTrack']['created'],

						);
					}

					//break;
				}

				//pr($so_datalists);
				$this->set('so_datalists', $so_datalists);


				$loaction = true;
				$territory_ids = array();
				if ($so_datalists) {
					foreach ($so_datalists as $key => $val) {
						array_push($territory_ids, $key);
					}
				}
				//pr($territory_ids);
				$this->set('territory_ids', $territory_ids);
				//exit;




				if ((@$this->request->data['MapSalesTrack']['office_id'])) {

					//echo 11111111111111;

					$zoom = 10;
					$this->loadModel('Territory');
					$territories = $this->Territory->find('list', array(
						'conditions' => array('id' => $territory_ids, 'office_id' => $office_id),
						'order' => array('name' => 'asc')
					));


					//Start Create Folder
					$path = ROOT . '/app/View/Themed/CakeAdminLTE//webroot/img/map-icon/office/' . $office_id;
					$folder = new Folder($path, true, 0755);

					$path = ROOT . '/app/View/Themed/CakeAdminLTE//webroot/img/map-icon/office/' . $office_id . '/x/';
					$folder_x = new Folder($path, true, 0755);
					if ($folder_x->path) {
						//Start Image Copy
						$imagePath = ROOT . '/app/View/Themed/CakeAdminLTE/webroot/img/map-icon/' . $office_id . '.png';
						$newPath = ROOT . '/app/View/Themed/CakeAdminLTE//webroot/img/map-icon/office/' . $office_id . '/';
						$ext = '.png';
						foreach ($territories as $key => $val) {
							$newName  = $newPath . "$key" . $ext;
							$copied = copy($imagePath, $newName);
						}

						//for cross image
						$imagePath = ROOT . '/app/View/Themed/CakeAdminLTE/webroot/img/map-icon/x/' . $office_id . '-x.png';
						$newPath = ROOT . '/app/View/Themed/CakeAdminLTE//webroot/img/map-icon/office/' . $office_id . '/x/';
						foreach ($territories as $key => $val) {
							$newName  = $newPath . "$key" . '-x' . $ext;
							$copied = copy($imagePath, $newName);
						}
						//End Image Copy
					}
					//End Create Folder

					//exit;
				} else {
					$zoom = 7;
					$territories = '';
				}



				$this->set(compact('so_user_dropdown', 'latitude', 'longitude', 'userCoor', 'userCoorPath', 'offices', 'office_parent_id', 'office_id', 'loaction', 'zoom', 'territories', 'request'));
			}
		}
		/*else
		{
			$this->Session->setFlash(__('Please select Area Office and Date.'), 'flash/error');
			$this->redirect(array('action' => 'view'));
		}*/










		//office
		$office_id = (isset($this->request->data['MapSalesTrack']['office_id']) ? $this->request->data['MapSalesTrack']['office_id'] : 0);

		$so_id = (isset($this->request->data['MapSalesTrack']['so_id']) ? $this->request->data['MapSalesTrack']['so_id'] : 0);



		// $office_parent_id = $this->UserAuth->getOfficeParentId();		

		if ($office_parent_id) {
			$office_id = $this->UserAuth->getOfficeId();
		}




		// $this->loadModel('Office');
		//$offices = $this->Office->find('list', array('conditions'=> '', 'order'=>array('office_name'=>'asc')));
		/*$offices = $this->Office->find('list', array(
			'conditions'=> array(
				'office_type_id' => 2,
				"NOT" => array( "id" => array(30, 31, 37))
				), 
			'order'=>array('office_name'=>'asc'),
			'recursive'=>-1
		));*/

		//for so user by office id
		$this->loadModel('User');
		// $office_parent_id = $this->UserAuth->getOfficeParentId();		
		if ($office_parent_id == 0) {
			if ($office_type_id == 3) {
				$user_conditions = array(
					'active' => 1,
					'user_group_id' => array(4, 1008), //change here for SPO
					'SalesPeople.territory_id >' => 0,
					'Office.parent_office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
					"NOT" => array("SalesPeople.office_id" => array(30, 31, 37))
				);
			} else {
				$user_conditions = array(
					'active' => 1,
					'user_group_id' => array(4, 1008), //change here for SPO
					'SalesPeople.territory_id >' => 0,
					"NOT" => array("SalesPeople.office_id" => array(30, 31, 37))
				);
			}
		} else {
			$user_conditions = array(
				'active' => 1,
				'user_group_id' => array(4, 1008), //change here for SPO
				'SalesPeople.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
			);
		}



		if ($office_id && $office_parent_id == 0) {
			$user_conditions = array(
				'active' => 1,
				'user_group_id' => array(4, 1008), //change here for SPO
				'SalesPeople.office_id' => $office_id, //change here for SPO
			);
		}

		//pr($user_conditions);
		$so_user_dropdown = $this->User->find(
			'all',
			array(
				'conditions' => $user_conditions,
				'joins' => array(
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'User.sales_person_id = SalesPeople.id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'SalesPeople.territory_id = Territory.id'
					),
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Office.id = SalesPeople.office_id'
					),
				),
				'fields' => 'User.username, User.sales_person_id, SalesPeople.name, Territory.name',
				'order' => array('User.username' => 'asc')
			)
		);
		//pr($so_user_dropdown);
		$so = array();
		foreach ($so_user_dropdown as $so_list) {
			$so[$so_list['User']['sales_person_id']] = $so_list['SalesPeople']['name'] . ' (' . $so_list['Territory']['name'] . ')';
		}

		$so_user_dropdown = $so;

		//pr($so_user_dropdown);

		//end so user by office id


		if (empty($this->request->data['MapSalesTrack']['office_id'])  && $office_parent_id == 0) {
			$so_user_dropdown = array();
		}

		$request_data = $this->request->data;

		$this->set(compact('so_user_dropdown', 'latitude', 'longitude', 'userCoor', 'userCoorPath', 'offices', 'office_parent_id', 'office_id', 'request_data'));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'Add Sales Track');

		$interval = array();

		for ($i = 1; $i <= 60; $i++) {
			$interval[$i] = $i . ' Min';
		}

		if ($this->request->is('post')) {
			$this->request->data['MapSalesTrack']['active'] = 0;

			$this->MapSalesTrack->create();
			if ($this->MapSalesTrack->save($this->request->data)) {
				$this->Session->setFlash(__('The sale traking has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}

		$this->set(compact('interval'));
	}





	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null)
	{
		$this->set('page_title', 'Edit Visit plan list');
		$this->MapSalesTrack->id = $id;

		$start_time = '9:30 am';

		if (!$this->MapSalesTrack->exists($id)) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['MapSalesTrack']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->MapSalesTrack->save($this->request->data)) {
				$this->Session->setFlash(__('The visit plan list has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The visit plan list could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('MapSalesTrack.' . $this->MapSalesTrack->primaryKey => $id));
			$this->request->data = $this->MapSalesTrack->find('first', $options);
		}

		$interval = array();

		for ($i = 1; $i <= 60; $i++) {
			$interval[$i] = $i . ' Min';
		}

		$results = $this->MapSalesTrack->find('first', array('conditions' => array('id' => $id)));
		$start_time = date('h:m a', strtotime($results['MapSalesTrack']['start_time']));
		$end_time = date('h:m a', strtotime($results['MapSalesTrack']['end_time']));

		$this->set(compact('interval', 'start_time', 'end_time'));
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
		$this->MapSalesTrack->id = $id;
		if (!$this->MapSalesTrack->exists()) {
			throw new NotFoundException(__('Invalid traking list'));
		}
		if ($this->MapSalesTrack->delete($id)) {
			$this->Session->setFlash(__('Traking list deleted!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Traking was not deleted!'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}


	public function get_so_list_by_office()
	{
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$office_id = $this->request->data['office_id'];

		$this->loadModel('User');

		$user_conditions = array(
			'active' => 1,
			'user_group_id' => array(4, 1008), //SO group id
			'SalesPeople.office_id' => $office_id, //change here for SPO
		);

		$so_lists = $this->User->find(
			'all',
			array(
				'conditions' => $user_conditions,
				'joins' => array(
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'User.sales_person_id = SalesPeople.id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'SalesPeople.territory_id = Territory.id'
					)
				),
				'fields' => 'User.username, User.sales_person_id, SalesPeople.name, Territory.name',
				'order' => array('User.username' => 'asc')
			)
		);

		/*$so = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id', 'SalesPerson.name'),
			'conditions' => array('SalesPerson.office_id' => $office_id),
			'order' => array('SalesPerson.name' => 'asc'),
			'recursive' => -1
		));*/

		//pr($so_lists);
		//exit;

		$so = array();
		foreach ($so_lists as $so_list) {
			$so[] = array(
				'id' => $so_list['User']['sales_person_id'],
				'name' => $so_list['SalesPeople']['name'] . ' (' . $so_list['Territory']['name'] . ')',
			);
		}

		//pr($so);

		$data_array = Set::extract($so, '{n}');

		if (!empty($so)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_territory_name($territory_id = 0)
	{
		$this->loadModel('Territory');
		$territory = $this->Territory->find(
			'first',
			array(
				'conditions' => array('Territory.id' => $territory_id),
				'fields' => array('Territory.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => -1
			)
		);
		return @$territory['Territory']['name'];
	}

	public function get_terrytory_list()
	{
		$office_id = $this->request->data['office_id'];
		$this->loadModel('Territory');
		$territories = $this->Territory->find(
			'list',
			array(
				'conditions' => array('Territory.office_id' => $office_id),
				'fields' => array('Territory.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => -1
			)
		);

		$ids = array();
		foreach ($territories as $key => $val) {
			array_push($ids, $key);
		}

		echo json_encode($ids);
		$this->autoRender = false;
	}
}
