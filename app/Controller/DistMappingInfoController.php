<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class DistMappingInfoController extends AppController
{

  /**
   * Components
   *
   * @var array
   */
  public $components = array('Paginator', 'Session', 'Filter.Filter');
  public $uses = array('Office', 'DistTso', 'DistAreaExecutive', 'DistDistributor', 'DistSalesRepresentative', 'DistRoute');

  public function admin_index()
  {
    $this->set('page_title', 'Mapping Info');
    $office_parent_id = $this->UserAuth->getOfficeParentId();
    if ($office_parent_id == 0) {
      $storeCondition = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
      $tso_condition = array('DistTso.is_active' => 1);
      $ae_condition = array('DistAreaExecutive.is_active' => 1);
      $db_condition = array('DistDistributor.is_active' => 1);
      $sr_condition = array('DistSalesRepresentative.is_active' => 1);
      $route_condition = array('DistRoute.is_active' => 1);
    } else {
      $office_id = $this->UserAuth->getOfficeId();
      $storeCondition = array('Office.id' => $office_id);
      $tso_condition = array('DistTso.is_active' => 1, 'DistTso.office_id' => $office_id);
      $ae_condition = array('DistAreaExecutive.is_active' => 1, 'DistAreaExecutive.office_id' => $office_id);
      $db_condition = array('DistDistributor.is_active' => 1, 'DistDistributor.office_id' => $office_id);
      $sr_condition = array('DistSalesRepresentative.is_active' => 1, 'DistSalesRepresentative.office_id' => $office_id);
      $route_condition = array('DistRoute.is_active' => 1, 'DistRoute.office_id' => $office_id);
    }
    $offices = $this->Office->find('list', array('conditions' => $storeCondition, 'order' => array('Office.office_name' => 'ASC')));
    $tsos = $this->DistTso->find('list', array('conditions' => $tso_condition, 'order' => array('DistTso.name' => 'ASC')));
    $aes = $this->DistAreaExecutive->find('list', array('conditions' => $ae_condition, 'order' => array('DistAreaExecutive.name' => 'ASC')));
    $dbs = $this->DistDistributor->find('list', array('conditions' => $db_condition, 'order' => array('DistDistributor.name' => 'ASC')));
    $srs = $this->DistSalesRepresentative->find('list', array('conditions' => $sr_condition, 'order' => array('DistSalesRepresentative.name' => 'ASC')));

    $this->DistRoute->virtualFields = array(
      "route_with_thana" => "CONCAT(DistRoute.name, ' (', Thana.name,')')"
    );
    $routes = $this->DistRoute->find('list', array(
      'conditions' => $route_condition,
      'joins' => array(
        array(
          'table' => 'thanas',
          'alias' => 'Thana',
          'conditions' => 'Thana.id=DistRoute.thana_id',
          'type' => 'LEFT'
        ),
      ),
      'fields' => array('route_with_thana'),
      'order' => array('DistRoute.name')
    ));
    $listFor = array(
      'ae'     => 'AE',
      'tso'     => 'TSO',
      'db'    => 'DB',
      'sr'   => 'SR',
      'route'    => 'Route/Beat',
    );
    $this->set(compact('offices', 'listFor', 'tsos', 'aes', 'dbs', 'srs', 'routes', 'office_parent_id'));

    if ($this->request->is('post')) {
      /*$office_id=$this->request->data['DistMappingInfo']['office_id']?$this->request->data['DistMappingInfo']['office_id']:0;
      $ae_id=$this->request->data['DistMappingInfo']['ae_id']?$this->request->data['DistMappingInfo']['ae_id']:0;
      $tso_id=$this->request->data['DistMappingInfo']['tso_id']?$this->request->data['DistMappingInfo']['tso_id']:0;
      $db_id=$this->request->data['DistMappingInfo']['db_id']?$this->request->data['DistMappingInfo']['db_id']:0;
      $sr_id=$this->request->data['DistMappingInfo']['sr_id']?$this->request->data['DistMappingInfo']['sr_id']:0;
      $route_id=$this->request->data['DistMappingInfo']['route_id']?$this->request->data['DistMappingInfo']['route_id']:0;*/
      $list_for = $this->request->data['DistMappingInfo']['list_for'];
      if ($list_for == 'ae') {
        $column = array(
          'office' => 'Office',
          'ae' => 'AE Name',
        );
        $lists = $this->get_ae_information($this->request->data['DistMappingInfo']);
      } elseif ($list_for == 'tso') {
        $column = array(
          'office' => 'Office',
          'ae' => 'AE Name',
          'tso' => 'TSO Name',
        );
        $lists = $this->get_tso_information($this->request->data['DistMappingInfo']);
      } elseif ($list_for == 'db') {
        $column = array(
          'office' => 'Office',
          'ae' => 'AE Name',
          'tso' => 'TSO Name',
          'db' => 'Distributor Name',
        );
        $lists = $this->get_db_information($this->request->data['DistMappingInfo']);
      } elseif ($list_for == 'sr') {
        $column = array(
          'office' => 'Office',
          'ae' => 'AE Name',
          'tso' => 'TSO Name',
          'db' => 'Distributor Name',
          'sr' => 'SR Name',
        );
        $lists = $this->get_sr_information($this->request->data['DistMappingInfo']);
      } elseif ($list_for == 'route') {
        $column = array(
          'office' => 'Office',
          'ae' => 'AE Name',
          'tso' => 'TSO Name',
          'db' => 'Distributor Name',
          'sr' => 'SR Name',
          'thana' => 'Thana',
          'route' => 'Route/Beat',
        );
        $lists = $this->get_route_information($this->request->data['DistMappingInfo']);
      }
      $this->set(compact('column', 'lists', 'list_for'));
    }
  }

  private function get_route_information($request_data)
  {
    // pr($request_data);exit;
    $office_id = $request_data['office_id'] ? $request_data['office_id'] : 0;
    $ae_id = $request_data['ae_id'] ? $request_data['ae_id'] : 0;
    $tso_id = $request_data['tso_id'] ? $request_data['tso_id'] : 0;
    $db_id = $request_data['db_id'] ? $request_data['db_id'] : 0;
    $sr_id = $request_data['sr_id'] ? $request_data['sr_id'] : 0;
    $route_id = $request_data['route_id'] ? $request_data['route_id'] : 0;
    $route_condition = array('DistRoute.is_active' => 1);
    $search_text = $request_data['search_text'] ? $request_data['search_text'] : '';
    if ($search_text) {
      $route_condition['DistRoute.name like '] = "%" . $search_text . "%";
    }
    if ($office_id) {
      $route_condition['DistRoute.office_id'] = $office_id;
    }

    if ($ae_id) {
      $route_condition['DistTso.dist_area_executive_id'] = $ae_id;
    }

    if ($tso_id) {
      $route_condition['DistTso.id'] = $tso_id;
    }

    if ($db_id) {
      $route_condition['DistDistributor.id'] = $db_id;
    }
    if ($sr_id) {
      $route_condition['DistSrRouteMapping.dist_sr_id'] = $sr_id;
    }
    if ($route_id) {
      $route_condition['DistRoute.id'] = $route_id;
    }

    $route = $this->DistRoute->find('all', array(
      'conditions' => $route_condition,
      'joins' => array(
        array(
          'table' => 'dist_route_mappings',
          'alias' => 'DistRouteMapping',
          'conditions' => 'DistRouteMapping.dist_route_id=DistRoute.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_distributors',
          'alias' => 'DistDistributor',
          'conditions' => 'DistDistributor.id=DistRouteMapping.dist_distributor_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tso_mappings',
          'alias' => 'DistTsoMapping',
          'conditions' => 'DistTsoMapping.dist_distributor_id=DistDistributor.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tsos',
          'alias' => 'DistTso',
          'conditions' => 'DistTso.id=DistTsoMapping.dist_tso_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_sr_route_mappings',
          'alias' => 'DistSrRouteMapping',
          'conditions' => 'DistSrRouteMapping.dist_route_id=DistRoute.id AND DistSrRouteMapping.dist_distributor_id=DistDistributor.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_area_executives',
          'alias' => 'DistAreaExecutive',
          'conditions' => 'DistAreaExecutive.id=DistTso.dist_area_executive_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_sales_representatives',
          'alias' => 'DistSalesRepresentative',
          'conditions' => 'DistSalesRepresentative.id=DistSrRouteMapping.dist_sr_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'offices',
          'alias' => 'Office',
          'conditions' => 'Office.id=DistRoute.office_id',
          'type' => 'left'
        ),
        array(
          'table' => 'thanas',
          'alias' => 'Thana',
          'conditions' => 'Thana.id=DistRoute.thana_id',
          'type' => 'LEFT'
        ),
      ),
      'fields' => array('DistRoute.*', 'Thana.name', 'DistSalesRepresentative.name', 'DistDistributor.name', 'Office.office_name', 'DistTso.name', 'DistAreaExecutive.name'),
      'order' => array('Office.order', 'DistAreaExecutive.id', 'DistTso.id', 'DistDistributor.id', 'DistSalesRepresentative.id', 'DistRoute.name'),
      'recursive' => -1
    ));
    // pr($route);exit;
    $result_set = array();
    foreach ($route as $data) {
      $result_set[] = array(
        'office' => $data['Office']['office_name'],
        'ae' => $data['DistAreaExecutive']['name'],
        'tso' => $data['DistTso']['name'],
        'db' => $data['DistDistributor']['name'],
        'sr' => $data['DistSalesRepresentative']['name'],
        'thana' => $data['Thana']['name'],
        'route' => $data['DistRoute']['name'],
      );
    }
    return $result_set;
  }

  private function get_sr_information($request_data)
  {
    $office_id = $request_data['office_id'] ? $request_data['office_id'] : 0;
    $ae_id = $request_data['ae_id'] ? $request_data['ae_id'] : 0;
    $tso_id = $request_data['tso_id'] ? $request_data['tso_id'] : 0;
    $db_id = $request_data['db_id'] ? $request_data['db_id'] : 0;
    $sr_id = $request_data['sr_id'] ? $request_data['sr_id'] : 0;
    /*$route_id=$request_data['route_id']?$request_data['route_id']:0;*/
    $sr_condition = array('DistSalesRepresentative.is_active' => 1);
    $search_text = $request_data['search_text'] ? $request_data['search_text'] : '';
    if ($search_text) {
      $sr_condition['DistSalesRepresentative.name like '] = "%" . $search_text . "%";
    }
    if ($office_id) {
      $sr_condition['DistSalesRepresentative.office_id'] = $office_id;
    }

    if ($ae_id) {
      $sr_condition['DistTso.dist_area_executive_id'] = $ae_id;
    }

    if ($tso_id) {
      $sr_condition['DistTso.id'] = $tso_id;
    }

    if ($db_id) {
      $sr_condition['DistDistributor.id'] = $db_id;
    }

    if ($sr_id) {
      $sr_condition['DistSalesRepresentative.id'] = $sr_id;
    }


    $sr = $this->DistSalesRepresentative->find('all', array(
      'conditions' => $sr_condition,
      'joins' => array(
        array(
          'table' => 'dist_distributors',
          'alias' => 'DistDistributor',
          'conditions' => 'DistDistributor.id=DistSalesRepresentative.dist_distributor_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tso_mappings',
          'alias' => 'DistTsoMapping',
          'conditions' => 'DistTsoMapping.dist_distributor_id=DistDistributor.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tsos',
          'alias' => 'DistTso',
          'conditions' => 'DistTso.id=DistTsoMapping.dist_tso_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_area_executives',
          'alias' => 'DistAreaExecutive',
          'conditions' => 'DistAreaExecutive.id=DistTso.dist_area_executive_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'offices',
          'alias' => 'Office',
          'conditions' => 'Office.id=DistSalesRepresentative.office_id',
          'type' => 'left'
        ),
      ),
      'fields' => array('DistSalesRepresentative.*', 'DistDistributor.name', 'Office.office_name', 'DistTso.name', 'DistAreaExecutive.name'),
      'order' => array('Office.order', 'DistAreaExecutive.id', 'DistTso.id', 'DistDistributor.id', 'DistSalesRepresentative.name'),
      'recursive' => -1
    ));
    // pr($sr);exit;
    $result_set = array();
    foreach ($sr as $data) {
      $result_set[] = array(
        'office' => $data['Office']['office_name'],
        'ae' => $data['DistAreaExecutive']['name'],
        'tso' => $data['DistTso']['name'],
        'db' => $data['DistDistributor']['name'],
        'sr' => $data['DistSalesRepresentative']['name'],
      );
    }
    return $result_set;
  }
  private function get_db_information($request_data)
  {
    $office_id = $request_data['office_id'] ? $request_data['office_id'] : 0;
    $ae_id = $request_data['ae_id'] ? $request_data['ae_id'] : 0;
    $tso_id = $request_data['tso_id'] ? $request_data['tso_id'] : 0;
    $db_id = $request_data['db_id'] ? $request_data['db_id'] : 0;
    /*$sr_id=$request_data['sr_id']?$request_data['sr_id']:0;
    $route_id=$request_data['route_id']?$request_data['route_id']:0;*/
    $db_condition = array('DistDistributor.is_active' => 1);
    $search_text = $request_data['search_text'] ? $request_data['search_text'] : '';
    if ($search_text) {
      $db_condition['DistDistributor.name like '] = "%" . $search_text . "%";
    }
    if ($office_id) {
      $db_condition['DistDistributor.office_id'] = $office_id;
    }

    if ($ae_id) {
      $db_condition['DistAreaExecutive.id'] = $ae_id;
    }

    if ($tso_id) {
      $db_condition['DistTso.id'] = $tso_id;
    }

    if ($db_id) {
      $db_condition['DistDistributor.id'] = $db_id;
    }


    $db = $this->DistDistributor->find('all', array(
      'conditions' => $db_condition,
      'joins' => array(
        array(
          'table' => 'dist_tso_mappings',
          'alias' => 'DistTsoMapping',
          'conditions' => 'DistTsoMapping.dist_distributor_id=DistDistributor.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tsos',
          'alias' => 'DistTso',
          'conditions' => 'DistTso.id=DistTsoMapping.dist_tso_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_area_executives',
          'alias' => 'DistAreaExecutive',
          'conditions' => 'DistAreaExecutive.id=DistTso.dist_area_executive_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'offices',
          'alias' => 'Office',
          'conditions' => 'Office.id=DistDistributor.office_id',
          'type' => 'left'
        ),
      ),
      'fields' => array('DistDistributor.*', 'Office.office_name', 'DistTso.name', 'DistAreaExecutive.name'),
      'order' => array('Office.order', 'DistAreaExecutive.id', 'DistTso.id', 'DistDistributor.name'),
      'recursive' => -1
    ));
    // pr($db);exit;
    $result_set = array();
    foreach ($db as $data) {
      $result_set[] = array(
        'office' => $data['Office']['office_name'],
        'ae' => $data['DistAreaExecutive']['name'],
        'tso' => $data['DistTso']['name'],
        'db' => $data['DistDistributor']['name'],
      );
    }
    return $result_set;
  }
  private function get_tso_information($request_data)
  {
    $office_id = $request_data['office_id'] ? $request_data['office_id'] : 0;
    $ae_id = $request_data['ae_id'] ? $request_data['ae_id'] : 0;
    $tso_id = $request_data['tso_id'] ? $request_data['tso_id'] : 0;
    /*$db_id=$request_data['db_id']?$request_data['db_id']:0;
    $sr_id=$request_data['sr_id']?$request_data['sr_id']:0;
    $route_id=$request_data['route_id']?$request_data['route_id']:0;*/
    $tso_condition = array('DistTso.is_active' => 1);
    $search_text = $request_data['search_text'] ? $request_data['search_text'] : '';
    if ($search_text) {
      $tso_condition['DistTso.name like '] = "%" . $search_text . "%";
    }
    if ($office_id) {
      $tso_condition['DistTso.office_id'] = $office_id;
    }
    if ($ae_id) {
      $tso_condition['DistTso.dist_area_executive_id'] = $ae_id;
    }
    if ($tso_id) {
      $tso_condition['DistTso.id'] = $tso_id;
    }

    $tso = $this->DistTso->find('all', array(
      'conditions' => $tso_condition, 'order' => array('DistTso.name' => 'ASC'),
      'order' => array('Office.order', 'DistAreaExecutive.id'),
      'recursive' => 0
    ));
    $result_set = array();
    foreach ($tso as $data) {
      $result_set[] = array(
        'office' => $data['Office']['office_name'],
        'ae' => $data['DistAreaExecutive']['name'],
        'tso' => $data['DistTso']['name'],
      );
    }
    return $result_set;
  }
  private function  get_ae_information($request_data)
  {
    $office_id = $request_data['office_id'] ? $request_data['office_id'] : 0;
    $ae_id = $request_data['ae_id'] ? $request_data['ae_id'] : 0;
    /*$tso_id=$request_data['tso_id']?$request_data['tso_id']:0;
    $db_id=$request_data['db_id']?$request_data['db_id']:0;
    $sr_id=$request_data['sr_id']?$request_data['sr_id']:0;
    $route_id=$request_data['route_id']?$request_data['route_id']:0;*/
    $ae_condition = array();
    $ae_condition['DistAreaExecutive.is_active'] = 1;
    $search_text = $request_data['search_text'] ? $request_data['search_text'] : '';
    if ($search_text) {
      $ae_condition['DistAreaExecutive.name like '] = "%" . $search_text . "%";
    }
    if ($office_id) {
      $ae_condition['DistAreaExecutive.office_id'] = $office_id;
    }
    if ($ae_id) {
      $ae_condition['DistAreaExecutive.id'] = $ae_id;
    }
    $aes = $this->DistAreaExecutive->find('all', array(
      'conditions' => $ae_condition, 'order' => array('Office.order', 'DistAreaExecutive.name' => 'ASC'),
      'recursive' => 0
    ));
    $result_set = array();
    foreach ($aes as $data) {
      $result_set[] = array(
        'office' => $data['Office']['office_name'],
        'ae' => $data['DistAreaExecutive']['name']
      );
    }
    return $result_set;
  }
  public function get_ae_by_office_id()
  {
    $office_id = $this->request->data['office_id'];
    if ($office_id) {
      $ae_condition = array('DistAreaExecutive.is_active' => 1, 'DistAreaExecutive.office_id' => $office_id);
    } else {
      $ae_condition = array('DistAreaExecutive.is_active' => 1);
    }
    $aes = $this->DistAreaExecutive->find('list', array('conditions' => $ae_condition, 'order' => array('DistAreaExecutive.name' => 'ASC')));
    $output = "<option value=''>--- Select ---</option>";
    foreach ($aes as $id => $name) {
      $output .= "<option value='$id'>$name</option>";
    }
    echo $output;
    $this->autoRender = false;
  }

  public function get_tso_by_office_id_ae_id()
  {
    $office_id = $this->request->data['office_id'];
    $ae_id = $this->request->data['ae_id'];
    $tso_condition = array('DistTso.is_active' => 1);
    if ($office_id) {
      $tso_condition['DistTso.office_id'] = $office_id;
    }

    if ($ae_id) {
      $tso_condition['DistTso.dist_area_executive_id'] = $ae_id;
    }



    $aes = $this->DistTso->find('list', array('conditions' => $tso_condition, 'order' => array('DistTso.name' => 'ASC')));
    $output = "<option value=''>--- Select ---</option>";
    foreach ($aes as $id => $name) {
      $output .= "<option value='$id'>$name</option>";
    }
    echo $output;
    $this->autoRender = false;
  }

  public function get_db_by_office_id_ae_id_tso_id()
  {
    $office_id = $this->request->data['office_id'];
    $ae_id = $this->request->data['ae_id'];
    $tso_id = $this->request->data['tso_id'];
    $db_condition = array('DistDistributor.is_active' => 1);
    if ($office_id) {
      $db_condition['DistDistributor.office_id'] = $office_id;
    }

    if ($ae_id) {
      $db_condition['DistTso.dist_area_executive_id'] = $ae_id;
    }

    if ($tso_id) {
      $db_condition['DistTso.id'] = $tso_id;
    }


    $db = $this->DistDistributor->find('list', array(
      'conditions' => $db_condition,
      'joins' => array(
        array(
          'table' => 'dist_tso_mappings',
          'alias' => 'DistTsoMapping',
          'conditions' => 'DistTsoMapping.dist_distributor_id=DistDistributor.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tsos',
          'alias' => 'DistTso',
          'conditions' => 'DistTso.id=DistTsoMapping.dist_tso_id',
          'type' => 'Left'
        ),
      ),
      'order' => array('DistDistributor.name' => 'ASC'),
      'recursive' => -1
    ));
    $output = "<option value=''>--- Select ---</option>";
    foreach ($db as $id => $name) {
      $output .= "<option value='$id'>$name</option>";
    }
    echo $output;
    $this->autoRender = false;
  }

  public function get_sr_by_office_id_ae_id_tso_id_db_id()
  {
    $office_id = $this->request->data['office_id'];
    $ae_id = $this->request->data['ae_id'];
    $tso_id = $this->request->data['tso_id'];
    $db_id = $this->request->data['db_id'];
    $sr_condition = array('DistSalesRepresentative.is_active' => 1);
    if ($office_id) {
      $sr_condition['DistSalesRepresentative.office_id'] = $office_id;
    }

    if ($ae_id) {
      $sr_condition['DistTso.dist_area_executive_id'] = $ae_id;
    }

    if ($tso_id) {
      $sr_condition['DistTso.id'] = $tso_id;
    }

    if ($db_id) {
      $sr_condition['DistDistributor.id'] = $db_id;
    }


    $sr = $this->DistSalesRepresentative->find('list', array(
      'conditions' => $sr_condition,
      'joins' => array(
        array(
          'table' => 'dist_distributors',
          'alias' => 'DistDistributor',
          'conditions' => 'DistDistributor.id=DistSalesRepresentative.dist_distributor_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tso_mappings',
          'alias' => 'DistTsoMapping',
          'conditions' => 'DistTsoMapping.dist_distributor_id=DistDistributor.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tsos',
          'alias' => 'DistTso',
          'conditions' => 'DistTso.id=DistTsoMapping.dist_tso_id',
          'type' => 'Left'
        ),
      ),
      'order' => array('DistSalesRepresentative.name' => 'ASC'),
      'recursive' => -1
    ));
    $output = "<option value=''>--- Select ---</option>";
    foreach ($sr as $id => $name) {
      $output .= "<option value='$id'>$name</option>";
    }
    echo $output;
    $this->autoRender = false;
  }

  public function get_route_by_office_id_ae_id_tso_id_db_id_sr_id()
  {
    $office_id = $this->request->data['office_id'];
    $ae_id = $this->request->data['ae_id'];
    $tso_id = $this->request->data['tso_id'];
    $db_id = $this->request->data['db_id'];
    $sr_id = $this->request->data['sr_id'];
    $route_condition = array('DistRoute.is_active' => 1);
    if ($office_id) {
      $route_condition['DistRoute.office_id'] = $office_id;
    }

    if ($ae_id) {
      $route_condition['DistTso.dist_area_executive_id'] = $ae_id;
    }

    if ($tso_id) {
      $route_condition['DistTso.id'] = $tso_id;
    }

    if ($db_id) {
      $route_condition['DistDistributor.id'] = $db_id;
    }
    if ($sr_id) {
      $route_condition['DistSrRouteMapping.dist_sr_id'] = $sr_id;
    }
    $this->DistRoute->virtualFields = array(
      "route_with_thana" => "CONCAT(DistRoute.name, ' (', Thana.name,')')"
    );
    $route = $this->DistRoute->find('list', array(
      'conditions' => $route_condition,
      'joins' => array(
        array(
          'table' => 'dist_route_mappings',
          'alias' => 'DistRouteMapping',
          'conditions' => 'DistRouteMapping.dist_route_id=DistRoute.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_distributors',
          'alias' => 'DistDistributor',
          'conditions' => 'DistDistributor.id=DistRouteMapping.dist_distributor_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tso_mappings',
          'alias' => 'DistTsoMapping',
          'conditions' => 'DistTsoMapping.dist_distributor_id=DistDistributor.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_tsos',
          'alias' => 'DistTso',
          'conditions' => 'DistTso.id=DistTsoMapping.dist_tso_id',
          'type' => 'Left'
        ),
        array(
          'table' => 'dist_sr_route_mappings',
          'alias' => 'DistSrRouteMapping',
          'conditions' => 'DistSrRouteMapping.dist_route_id=DistRoute.id AND DistSrRouteMapping.dist_distributor_id=DistDistributor.id',
          'type' => 'Left'
        ),
        array(
          'table' => 'thanas',
          'alias' => 'Thana',
          'conditions' => 'Thana.id=DistRoute.thana_id',
          'type' => 'LEFT'
        ),
      ),
      'order' => array('DistRoute.name' => 'ASC'),
      'recursive' => -1
    ));
    // echo $this->DistRoute->getLastQuery();exit;
    $output = "<option value=''>--- Select ---</option>";
    foreach ($route as $id => $name) {
      $output .= "<option value='$id'>$name</option>";
    }
    echo $output;
    $this->autoRender = false;
  }
}
