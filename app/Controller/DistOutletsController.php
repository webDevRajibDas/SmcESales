<?php

App::uses('AppController', 'Controller');

/**
 * Outlets Controller
 *
 * @property Outlet $Outlet
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistOutletsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {

        $this->set('page_title', 'Distributor Outlet List');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistBonusCardType');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $dist_con = array();
        if ($office_parent_id == 0) {
            $outlet_conditions = $conditions = array('Office.office_type_id' => 2);
            $route_conditions = array();
            $dist_con = array('DistDistributor.is_active' => 1);
        } else {
            $this->loadModel('DistAreaExecutive');
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list', array(
                        'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                        'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
                    ));

                    $dist_tso_id = array_keys($dist_tso_info);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }

                $tso_dist_list = $this->DistTsoMapping->find('list', array(
                    'conditions' => array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
                ));
                $route_list = $this->DistRouteMapping->find('list', array(
                    'conditions' => array('dist_distributor_id' => array_keys($tso_dist_list)),
                    'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id'),
                ));
                $route_conditions = array('DistRoute.id' => array_keys($route_list));
                $outlet_conditions = array('DistOutlet.dist_route_id' => array_keys($route_list));
                $dist_con = array('DistDistributor.id' => array_keys($tso_dist_list), 'DistDistributor.is_active' => 1);
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $route_list = $this->DistRouteMapping->find('list', array(
                    'conditions' => array('dist_distributor_id' => $distributor_id),
                    'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id'),
                ));
                $route_conditions = array('DistRoute.id' => array_keys($route_list));
                $outlet_conditions = array('DistOutlet.dist_route_id' => array_keys($route_list));
                $dist_con = array('DistDistributor.id' => $distributor_id, 'DistDistributor.is_active' => 1);
            } else {
                $outlet_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
                $route_conditions = array('office_id' => $this->UserAuth->getOfficeId());
                $dist_con = array('DistDistributor.office_id' => $this->UserAuth->getOfficeId(), 'DistDistributor.is_active' => 1);
            }
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $this->loadModel('DistDistributor');

        $distributors = $this->DistDistributor->find('list', array(
            'conditions' => $dist_con
        ));

        $this->set(compact('distributors'));

        $this->DistOutlet->virtualFields = array(
            'thana_name' => 'Thana.name'
        );
        $this->paginate = array(
            'conditions' => $outlet_conditions,
            'joins' => array(
                array(
                    'alias' => 'Territory',
                    'table' => 'territories',
                    'type' => 'INNER',
                    'conditions' => 'DistMarket.territory_id = Territory.id'
                ),
                array(
                    'alias' => 'Office',
                    'table' => 'offices',
                    'type' => 'INNER',
                    'conditions' => 'Territory.office_id = Office.id'
                ),
                array(
                    'alias' => 'Thana',
                    'table' => 'thanas',
                    'type' => 'INNER',
                    'conditions' => 'Thana.id = DistMarket.thana_id'
                ),
                array(
                    'alias' => 'DistRoute',
                    'table' => 'dist_routes',
                    'type' => 'left',
                    'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
                )
            ),
            'fields' => array(
                'DistOutlet.id', 'DistOutlet.code', 'DistOutlet.name', 'DistOutlet.in_charge', 'DistOutlet.address',
                'DistOutlet.telephone', 'DistOutlet.mobile', 'DistOutlet.dist_market_id', 'DistOutlet.category_id',
                'DistOutlet.is_pharma_type', 'DistOutlet.is_ngo', 'DistOutlet.bonus_type_id', 'DistOutlet.is_active', 'Institute.name',
                'DistMarket.id', 'DistMarket.code', 'DistMarket.name', 'Territory.id', 'Territory.name', 'Office.id', 'Office.office_code', 'Office.office_name', 'Office.office_type_id', 'OutletCategory.category_name', 'Thana.id', 'Thana.name', 'DistRoute.id', 'DistRoute.name'
            ),
            'order' => array('DistOutlet.id' => 'desc')
        );
        $this->set('outlets', $this->paginate());

        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('DistRoute');

        $conditions['NOT'] = array("id" => array(30, 31, 37));
        $conditions['Office.office_type_id'] = 2;
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

        $office_id = (isset($this->request->data['DistOutlet']['office_id']) ? $this->request->data['DistOutlet']['office_id'] : 0);
        $territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

        $territory_id = (isset($this->request->data['DistOutlet']['territory_id']) ? $this->request->data['DistOutlet']['territory_id'] : 0);
        $thana_id = (isset($this->request->data['DistOutlet']['thana_id']) ? $this->request->data['DistOutlet']['thana_id'] : 0);

        $distributor_id = (isset($this->request->data['DistOutlet']['distributor_id']) ? $this->request->data['DistOutlet']['distributor_id'] : 0);
        $dist_sr_id = (isset($this->request->data['DistOutlet']['sr_id']) ? $this->request->data['DistOutlet']['sr_id'] : 0);

        $dist_market_id = (isset($this->request->data['DistOutlet']['dist_market_id']) ? $this->request->data['DistOutlet']['dist_market_id'] : 0);

        $dist_route_id = (isset($this->request->data['DistOutlet']['dist_route_id']) ? $this->request->data['DistOutlet']['dist_route_id'] : 0);
        if ($distributor_id) {
            $this->loadmodel('DistSalesRepresentative');
            $sr_list = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id, 'DistSalesRepresentative.is_active' => 1), 'order' => array('DistSalesRepresentative.name' => 'asc')
            ));
        } else {
            $sr_list = array();
        }
        if ($dist_route_id) {
            $this->loadmodel('DistMarket');
            $market_list = $this->DistMarket->find('list', array(
                'conditions' => array('DistMarket.dist_route_id' => $dist_route_id), 'order' => array('DistMarket.name' => 'asc')
            ));
        } else {
            $market_list = array();
        }
        $this->set(compact('sr_list', 'market_list'));
        if ($territory_id) {
            //for thana list

            if ($territory_id) {
                $this->loadModel('ThanaTerritory');
                $conditions = array('ThanaTerritory.territory_id' => $territory_id);

                $rs = array(array('id' => '', 'name' => '---- Select -----'));
                $thana_lists = $this->ThanaTerritory->find('all', array(
                    'conditions' => $conditions,
                    //'order' => array('Thana.name'=>'ASC'),
                    'recursive' => 1
                ));

                foreach ($thana_lists as $thana_info) {
                    $thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
                }
            }
        } else {
            $thanas = array();
        }



        /* if ($thana_id) {
            $distmarkets = $this->DistOutlet->DistMarket->find('list', array(
                'conditions' => array('DistMarket.thana_id' => $thana_id),
                'order' => array('DistMarket.name' => 'ASC'),
                'recursive' => 0
            ));
        } else {
            $distmarkets = array();
        }*/

        /* For Route start */

        //$distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
        $distRoutes = $this->DistRoute->find('list', array('conditions' => $route_conditions, 'order' => array('name' => 'asc')));

        /* For Route End */



        $categories = $this->DistOutlet->OutletCategory->find('list');
        // pr($catego)
        $bonus_types = $this->DistBonusCardType->find('list');
        $this->set(compact('categories', 'offices', 'distmarkets', 'territories', 'dist_market_id', 'dist_route_id', 'thanas', 'distRoutes', 'bonus_types'));
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view_bak($id = null)
    {
        $this->set('page_title', 'Distributor Outlet Details');
        if (!$this->DistOutlet->exists($id)) {
            throw new NotFoundException(__('Invalid outlet'));
        }
        $joins = array(
            array(
                'table' => 'location_types',
                'alias' => 'LocationType',
                'type' => 'INNER',
                'conditions' => array(
                    'LocationType.id = DistOutlet.location_type_id'
                )
            )
        );
        $options = array(
            // 'joins' => $joins,
            //'fields' => array('DistOutlet.*','OutletCategory.*','DistMarket.*','Territory.*','Institute.*','Office.*'),
            'fields' => array('DistOutlet.*', 'OutletCategory.*', 'DistMarket.*'),
            'conditions' => array('DistOutlet.id' => $id),
            'order' => array('DistOutlet.name' => 'ASC'),
            'recursive' => 2
        );

        $options = $this->DistOutlet->find('all', $options);
        pr($options);
        exit;
        $this->set('outlet', $options);
    }

    public function admin_view($id = null)
    {

        $this->loadModel('DistOutlet');
        $this->set('page_title', 'Distributor Outlet Details');
        if (!$this->DistOutlet->exists($id)) {
            throw new NotFoundException(__('Invalid outlet'));
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2, 'DistOutlet.id' => $id);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2, 'DistOutlet.id' => $id);
        }
        $this->DistOutlet->virtualFields = array(
            'thana_name' => 'Thana.name'
        );
        $this->paginate = array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'alias' => 'Territory',
                    'table' => 'territories',
                    'type' => 'INNER',
                    'conditions' => 'DistMarket.territory_id = Territory.id'
                ),
                array(
                    'alias' => 'Office',
                    'table' => 'offices',
                    'type' => 'INNER',
                    'conditions' => 'Territory.office_id = Office.id'
                ),
                array(
                    'alias' => 'Thana',
                    'table' => 'thanas',
                    'type' => 'INNER',
                    'conditions' => 'Thana.id = DistMarket.thana_id'
                ),
                array(
                    'alias' => 'DistRoute',
                    'table' => 'dist_routes',
                    'type' => 'left',
                    'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
                )
            ),
            'fields' => array(
                'DistOutlet.id', 'DistOutlet.code', 'DistOutlet.ownar_name', 'DistOutlet.name', 'DistOutlet.in_charge', 'DistOutlet.address',
                'DistOutlet.telephone', 'DistOutlet.mobile', 'DistOutlet.dist_market_id', 'DistOutlet.category_id',
                'DistOutlet.is_pharma_type', 'DistOutlet.is_ngo', 'DistOutlet.bonus_type_id', 'Institute.name',
                'DistMarket.id', 'DistMarket.code', 'DistMarket.name', 'Territory.id', 'Territory.name', 'Office.id', 'Office.office_code', 'Office.office_name', 'Office.office_type_id', 'OutletCategory.category_name', 'Thana.id', 'Thana.name', 'DistRoute.id', 'DistRoute.name'
            ),
            'order' => array('DistOutlet.id' => 'desc')
        );

        $this->set('outlets', $this->paginate());
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add()
    {
        $this->set('page_title', 'Add Distributor Outlet');
        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            $this->request->data['DistOutlet']['created_at'] = $this->current_datetime();
            $this->request->data['DistOutlet']['updated_at'] = $this->current_datetime();
            $this->request->data['DistOutlet']['created_by'] = $this->UserAuth->getUserId();
            $this->DistOutlet->create();
            if ($this->DistOutlet->save($this->request->data)) {
                $this->Session->delete('from_outlet');
                $dist_outlet_id = $this->DistOutlet->getLastInsertID();
                if (array_key_exists('tag', $this->request->data['DistOutlet'])) {
                    /* --------------Add dist_distributor_id in index----------------- */

                    $this->request->data['DistOutlet']['distributor_id'] = $this->request->data['DistOutlet']['market_distributor_id'];
                    unset($this->request->data['DistOutlet']['market_distributor_id']);

                    /*
                    $this->loadModel('DistRouteMapping');
                    $distRouteMappings = $this->DistRouteMapping->find('all', array(
                        'conditions' => array('DistRouteMapping.dist_route_id' => $this->request->data['DistOutlet']['dist_route_id']),
                        'recursive' => -1
                    ));
                    if (count($distRouteMappings) > 0) {
                        $this->request->data['DistOutlet']['distributor_id'] = $distRouteMappings[0]['DistRouteMapping']['distributor_id'];
                    }

                     */

                    /* --------------Add dist_sr_id in index----------------- */
                    $this->loadModel('DistSalesRepresentative');
                    /*
                    $sr = $this->DistSalesRepresentative->find('all', array(
                        'conditions' => array(
                            'DistSalesRepresentative.office_id' => $this->request->data['DistOutlet']['office_id'],
                            'DistSalesRepresentative.dist_distributor_id' => $distRouteMappings[0]['DistRouteMapping']['dist_distributor_id'],
                        )
                    ));
                    if (count($sr) > 0) {
                        $this->request->data['DistOutlet']['dist_sales_representative_id'] = $sr[0]['DistSalesRepresentative']['id'];
                    }
                    */

                    $this->request->data['DistOutlet']['dist_sales_representative_id'] = $this->request->data['DistOutlet']['market_sr_id'];
                    unset($this->request->data['DistOutlet']['market_sr_id']);

                    $this->request->data['DistOutlet']['dist_route_id'] = $this->request->data['DistOutlet']['market_route_id'];
                    unset($this->request->data['DistOutlet']['market_route_id']);

                    $this->request->data['DistOutlet']['memo_date'] = $this->request->data['DistOutlet']['market_memo_date'];
                    unset($this->request->data['DistOutlet']['market_memo_date']);

                    $this->request->data['DistOutlet']['ae_id'] = $this->request->data['DistOutlet']['market_ae_id'];
                    unset($this->request->data['DistOutlet']['market_ae_id']);

                    $this->request->data['DistOutlet']['tso_id'] = $this->request->data['DistOutlet']['market_tso_id'];
                    unset($this->request->data['DistOutlet']['market_tso_id']);

                    $this->request->data['DistOutlet']['dist_market_id'] = $this->request->data['DistOutlet']['market_market_id'];
                    unset($this->request->data['DistOutlet']['market_market_id']);

                    $this->request->data['DistOutlet']['memo_reference_no'] = $this->request->data['DistOutlet']['market_memo_reference_no'];
                    unset($this->request->data['DistOutlet']['market_memo_reference_no']);

                    $this->request->data['DistOutlet']['dist_outlet_id'] = $dist_outlet_id;
                    $this->request->data['DistOutlet']['identity'] = 'from_outlet';
                    $this->Session->write('from_outlet', $this->request->data);
                    $this->redirect(array('controller' => 'DistMemos', 'action' => 'admin_create_memo'));
                }
                $this->Session->setFlash(__('The Distributor Outlet has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                //$this->Session->setFlash(__('The outlet could not be saved. Please, try again.'), 'flash/error');
            }
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2);
        } else {
            $office_conditions = array('id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('DistRoute');
        $this->loadModel('DistBonusCardType');
        $dist_bonus_card_type = $this->DistBonusCardType->find('list');
        //pr($dist_bonus_card_type);die();
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $office_id = (isset($this->request->data['DistOutlet']['office_id']) ? $this->request->data['DistOutlet']['office_id'] : 0);
        $territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

        $distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

        $territory_id = (isset($this->request->data['DistOutlet']['territory_id']) ? $this->request->data['DistOutlet']['territory_id'] : 0);
        $markets = $this->DistOutlet->DistMarket->find('list', array(
            'conditions' => array('DistMarket.territory_id' => $territory_id),
            'order' => array('DistMarket.name' => 'ASC'),
            'recursive' => 0
        ));

        $locationTypes = $this->DistOutlet->DistMarket->LocationType->find('list');

        $instituteTypes = array(1 => 'NGO', 2 => 'Institute');
        $categories = $this->DistOutlet->OutletCategory->find('list', array('conditions' => array('is_active' => 1)));
        unset($categories[17]);
        $institute_type_id = (isset($this->request->data['Institute']['type']) ? $this->request->data['Institute']['type'] : 0);
        $institutes = $this->DistOutlet->Institute->find('list', array('conditions' => array('type' => $institute_type_id, 'is_active' => 1), 'order' => array('name' => 'asc')));
        $this->set(compact('markets', 'categories', 'institutes', 'offices', 'territories', 'locationTypes', 'instituteTypes', 'distRoutes', 'dist_bonus_card_type'));
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
        $this->set('page_title', 'Edit Distributor Outlet');
        $this->DistOutlet->id = $id;
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');


        if (!$this->DistOutlet->exists($id)) {
            throw new NotFoundException(__('Invalid outlet'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['DistOutlet']['updated_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistOutlet']['updated_at'] = $this->current_datetime();
            if ($this->DistOutlet->save($this->request->data)) {
                $this->Session->setFlash(__('The Distributor outlet has been updated'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $joins = array(
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Territory.id = DistMarket.territory_id'
                    )
                )
            );
            $options = array(
                'joins' => $joins,
                'fields' => array('DistOutlet.*', 'DistMarket.*', 'Territory.office_id', 'Institute.*'),
                'conditions' => array('DistOutlet.' . $this->DistOutlet->primaryKey => $id),
                'recursive' => 0
            );
            $this->request->data = $this->DistOutlet->find('first', $options);
            // pr($this->request->data);
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2);
            $route_conditions = array('office_id' => $office_id);
        } else {
            $this->loadModel('DistAreaExecutive');
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list', array(
                        'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                        'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
                    ));

                    $dist_tso_id = array_keys($dist_tso_info);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }

                $tso_dist_list = $this->DistTsoMapping->find('list', array(
                    'conditions' => array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
                ));
                $route_list = $this->DistRouteMapping->find('list', array(
                    'conditions' => array('dist_distributor_id' => array_keys($tso_dist_list)),
                    'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id'),
                ));
                $route_conditions = array('id' => array_keys($route_list));
                $market_conditions = array('DistRoute.id' => array_keys($route_list));
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $route_list = $this->DistRouteMapping->find('list', array(
                    'conditions' => array('dist_distributor_id' => $distributor_id),
                    'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id'),
                ));
                $route_conditions = array('DistRoute.id' => array_keys($route_list));
                $outlet_conditions = array('DistRoute.id' => array_keys($route_list));
            } else {
                $route_conditions = array('office_id' => $this->UserAuth->getOfficeId());
            }

            $office_conditions = array('id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $outlets = $this->request->data;
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('Thana');
        $this->loadModel('DistRoute');
        $this->loadModel('DistBonusCardType');

        $dist_bonus_card_type = $this->DistBonusCardType->find('list');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $office_id = $this->request->data['Territory']['office_id'];
        $territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

        $territory_id = $this->request->data['DistMarket']['territory_id'];
        $thana_id = $this->request->data['DistMarket']['thana_id'];
        $conditions = array('ThanaTerritory.territory_id' => $territory_id);
        $joins = array(
            array(
                'table' => 'thana_territories',
                'alias' => 'ThanaTerritory',
                'type' => 'Inner',
                'conditions' => 'ThanaTerritory.thana_id=Thana.id'
            )
        );
        $thanas = $this->Thana->find('list', array(
            'conditions' => $conditions,
            'joins' => $joins,
            'order' => array('Thana.name' => 'ASC'),
            'recursive' => -1
        ));
        $distmarkets = $this->DistOutlet->DistMarket->find('list', array(
            'conditions' => array('DistMarket.territory_id' => $territory_id, 'DistMarket.thana_id' => $thana_id),
            'order' => array('DistMarket.name' => 'ASC'),
            'recursive' => 0
        ));
        //pr($distmarkets);die();
        $locationTypes = $this->DistOutlet->DistMarket->LocationType->find('list');
        $location_type_id = $this->request->data['DistMarket']['location_type_id'];
        $instituteTypes = array(1 => 'NGO', 2 => 'Institute');
        //$categories = $this->Outlet->OutletCategory->find('list');
        $categories = $this->DistOutlet->OutletCategory->find('list', array('conditions' => array('is_active' => 1)));
        unset($categories[17]);
        $institute_type_id = $this->request->data['Institute']['type'];
        $institutes = $this->DistOutlet->Institute->find('list', array('conditions' => array('type' => $institute_type_id, 'is_active' => 1), 'order' => array('name' => 'asc')));

        //$distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
        $distRoutes = $this->DistRoute->find('list', array('conditions' => $route_conditions, 'order' => array('name' => 'asc')));

        $this->set(compact('outlets', 'distmarkets', 'categories', 'institutes', 'offices', 'office_id', 'territories', 'territory_id', 'locationTypes', 'location_type_id', 'instituteTypes', 'thanas', 'thana_id', 'dist_route_id', 'distRoutes', 'dist_bonus_card_type'));
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
        $this->DistOutlet->id = $id;
        if (!$this->DistOutlet->exists()) {
            throw new NotFoundException(__('Invalid outlet'));
        }
        if ($this->DistOutlet->delete()) {
            $this->Session->setFlash(__('Distributor Outlet deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Distributor Outlet was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function get_thana_list()
    {
        $this->LoadModel('Thana');
        $territory_id = $this->request->data['territory_id'];
        $conditions = array('ThanaTerritory.territory_id' => $territory_id);
        $joins = array(
            array(
                'table' => 'thana_territories',
                'alias' => 'ThanaTerritory',
                'type' => 'Inner',
                'conditions' => 'ThanaTerritory.thana_id=Thana.id'
            )
        );
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $thana_list = $this->Thana->find('all', array(
            'conditions' => $conditions,
            'joins' => $joins,
            'order' => array('Thana.name' => 'ASC'),
            'recursive' => -1
        ));
        $data_array = Set::extract($thana_list, '{n}.Thana');
        if (!empty($thana_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_route_list()
    {
        $office_id = $this->request->data['office_id'];
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $this->loadModel('DistRoute');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        if ($user_group_id == 1029 || $user_group_id == 1028) {
            if ($user_group_id == 1028) {
                $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                    'conditions' => array('DistAreaExecutive.user_id' => $user_id),
                    'recursive' => -1,
                ));
                $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                $dist_tso_info = $this->DistTso->find('list', array(
                    'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                    'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
                ));

                $dist_tso_id = array_keys($dist_tso_info);
            } else {
                $dist_tso_info = $this->DistTso->find('first', array(
                    'conditions' => array('DistTso.user_id' => $user_id),
                    'recursive' => -1,
                ));
                $dist_tso_id = $dist_tso_info['DistTso']['id'];
            }

            $tso_dist_list = $this->DistTsoMapping->find('list', array(
                'conditions' => array(
                    'dist_tso_id' => $dist_tso_id,
                ),
                'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
            ));
            $route_list = $this->DistRouteMapping->find('list', array(
                'conditions' => array('dist_distributor_id' => array_keys($tso_dist_list)),
                'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id'),
            ));
            $route_conditions = array('DistRoute.id' => array_keys($route_list));
        } elseif ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            $route_list = $this->DistRouteMapping->find('list', array(
                'conditions' => array('dist_distributor_id' => $distributor_id),
                'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id'),
            ));
            $route_conditions = array('DistRoute.id' => array_keys($route_list));
        } else {
            $route_conditions = array('DistRoute.office_id' => $office_id);
        }
        $output = "<option value=''>--- Select ---</option>";
        if ($office_id) {
            $route = $this->DistRoute->find('list', array(
                'conditions' => $route_conditions,
                'order' => array('DistRoute.name ASC'),
            ));
            if ($route) {
                foreach ($route as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }
        echo $output;
        $this->autoRender = false;
    }

    public function get_market_list()
    {
        $dist_route_id = $this->request->data['dist_route_id'];
        $conditions = array('is_active' => 1);
        if ($dist_route_id) {
            $conditions['dist_route_id'] = $dist_route_id;
        }
        $this->loadModel('DistMarket');

        $output = "<option value=''>--- Select ---</option>";

        $market_list = $this->DistMarket->find('list', array(
            'conditions' => $conditions,
            'order' => array('DistMarket.name' => 'ASC'),
            'recursive' => -1
        ));

        if ($market_list) {
            foreach ($market_list as $key => $data) {
                $output .= "<option value='$key'>$data</option>";
            }
        }

        echo $output;
        $this->autoRender = false;
    }
    /*public function get_market_list() {

        $territory_id = $this->request->data['territory_id'];
        $dist_route_id = $this->request->data['dist_route_id'];
        $thana_id = $this->request->data['thana_id'];


        $conditions = array('is_active' => 1);

        if ($territory_id) {
            $conditions['territory_id'] = $territory_id;
        }


        if ($dist_route_id) {
            $conditions['dist_route_id'] = $dist_route_id;
        }

        if ($thana_id) {
            $conditions['thana_id'] = $thana_id;
        }

        $this->loadModel('DistMarket');

        $output = "<option value=''>--- Select ---</option>";

        $market_list = $this->DistMarket->find('list', array(
            'conditions' => $conditions,
            'order' => array('DistMarket.name' => 'ASC'),
            'recursive' => -1
        ));

        if ($market_list) {
            foreach ($market_list as $key => $data) {
                $output .= "<option value='$key'>$data</option>";
            }
        }

        echo $output;
        $this->autoRender = false;
    }*/
    function get_sr_list_by_distributot_id()
    {
        $distributor_id = $this->request->data['distributor_id'];
        $output = "<option value=''>--- Select SR ---</option>";
        $this->loadmodel('DistSalesRepresentative');
        if ($distributor_id) {
            $sr = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id, 'DistSalesRepresentative.is_active' => 1), 'order' => array('DistSalesRepresentative.name' => 'asc')
            ));

            if ($sr) {
                foreach ($sr as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }

        echo $output;
        $this->autoRender = false;
    }

    function get_route_list_by_sr_id()
    {
        $sr_id = $this->request->data['sr_id'];
        $output = "<option value=''>--- Select Route ---</option>";
        $this->loadmodel('DistSrRouteMapping');
        if ($sr_id) {
            $routes = $this->DistSrRouteMapping->find('list', array(
                'conditions' => array('DistSrRouteMapping.dist_sr_id' => $sr_id),
                'joins' => array(
                    array(
                        'table' => 'dist_routes',
                        'alias' => 'DistRoute',
                        'conditions' => 'DistRoute.id=DistSrRouteMapping.dist_route_id'
                    )
                ),
                'fields' => array('DistRoute.id', 'DistRoute.name')
            ));
            if ($routes) {
                foreach ($routes as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }

        echo $output;
        $this->autoRender = false;
    }
    public function download_xl()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        $params = $this->request->query['data'];
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $outlet_conditions = array();
        if ($office_parent_id == 0) {
            $outlet_conditions = $conditions = array('Office.office_type_id' => 2);
        } else {
            $this->loadModel('DistAreaExecutive');
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list', array(
                        'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                        'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
                    ));

                    $dist_tso_id = array_keys($dist_tso_info);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }

                $tso_dist_list = $this->DistTsoMapping->find('list', array(
                    'conditions' => array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
                ));
                $route_list = $this->DistRouteMapping->find('list', array(
                    'conditions' => array('dist_distributor_id' => array_keys($tso_dist_list)),
                    'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id'),
                ));
                $outlet_conditions = array('DistOutlet.dist_route_id' => array_keys($route_list));
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $route_list = $this->DistRouteMapping->find('list', array(
                    'conditions' => array('dist_distributor_id' => $distributor_id),
                    'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id'),
                ));
                $outlet_conditions = array('DistOutlet.dist_route_id' => array_keys($route_list));
            } else {
                $outlet_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            }
        }


        if (!empty($params['DistOutlet']['name'])) {
            $outlet_conditions['DistOutlet.name LIKE'] = '%' . $params['DistOutlet']['name'] . '%';
        }
        if (!empty($params['DistOutlet']['mobile'])) {
            $outlet_conditions['DistOutlet.mobile'] = $params['DistOutlet']['mobile'];
        }
        if (!empty($params['DistOutlet']['category_id'])) {
            $outlet_conditions['DistOutlet.category_id'] = $params['DistOutlet']['category_id'];
        }
        if (!empty($params['DistOutlet']['office_id'])) {
            $outlet_conditions['Territory.office_id'] = $params['DistOutlet']['office_id'];
        }
        if (!empty($params['DistOutlet']['territory_id'])) {
            $outlet_conditions['DistMarket.territory_id'] = $params['DistOutlet']['territory_id'];
        }
        if (!empty($params['DistOutlet']['dist_market_id'])) {
            $outlet_conditions['DistOutlet.dist_market_id'] = $params['DistOutlet']['dist_market_id'];
        }
        if (!empty($params['DistOutlet']['thana_id'])) {
            $outlet_conditions['Thana.id'] = ['DistOutlet']['thana_id'];
        }

        if (!empty($params['DistOutlet.dist_route_id'])) {
            $outlet_conditions['distRoute.id'] = $params['DistOutlet.dist_route_id'];
        }
        if (!empty($params['DistOutlet.bonus_type'])) {
            $outlet_conditions['DistOutlet.bonus_type_id'] = $params['DistOutlet.bonus_type'];
        }
        if (!empty($params['DistOutlet']['status'])) {
            $outlet_conditions['DistOutlet.is_active'] = ($params['DistOutlet']['status'] == 1 ? 1 : 0);
        }


        $this->DistOutlet->virtualFields = array(
            'thana_name' => 'Thana.name'
        );
        $distoutlet = $this->DistOutlet->find('all', array(
            'conditions' => $outlet_conditions,
            'joins' => array(
                array(
                    'alias' => 'Territory',
                    'table' => 'territories',
                    'type' => 'INNER',
                    'conditions' => 'DistMarket.territory_id = Territory.id'
                ),
                array(
                    'alias' => 'Office',
                    'table' => 'offices',
                    'type' => 'INNER',
                    'conditions' => 'Territory.office_id = Office.id'
                ),
                array(
                    'alias' => 'Thana',
                    'table' => 'thanas',
                    'type' => 'INNER',
                    'conditions' => 'Thana.id = DistMarket.thana_id'
                ),
                array(
                    'alias' => 'DistRoute',
                    'table' => 'dist_routes',
                    'type' => 'left',
                    'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
                )
            ),
            'fields' => array(
                'DistOutlet.id', 'DistOutlet.code', 'DistOutlet.name', 'DistOutlet.in_charge', 'DistOutlet.address',
                'DistOutlet.telephone', 'DistOutlet.mobile', 'DistOutlet.dist_market_id', 'DistOutlet.category_id',
                'DistOutlet.is_pharma_type', 'DistOutlet.is_ngo', 'DistOutlet.bonus_type_id', 'DistOutlet.is_active', 'Institute.name',
                'DistMarket.id', 'DistMarket.code', 'DistMarket.name', 'Territory.id', 'Territory.name', 'Office.id', 'Office.office_code', 'Office.office_name', 'Office.office_type_id', 'OutletCategory.category_name', 'Thana.id', 'Thana.name', 'DistRoute.id', 'DistRoute.name'
            ),
            'order' => array('DistOutlet.id' => 'desc'),
            'recursive' => 0
        ));
        /*  echo $this->DistOutlet->getLastQuery();
        pr($distoutlet);
        exit; */
        $table = '<table border="1"><tbody>
        <tr>
            <td>Id</td>
            <td>Name</td>
            <td>Mobile</td>
            <td>Market</td>
            <td>Route/Beat</td>
            <td>Thana</td>
            <td>Office</td>
            <td>Outlet Type</td>
            <td>Bonus Type</td>
            <td>Status</td>
        </tr>
        ';
        /* pr($disttso);
        exit; */
        foreach ($distoutlet as $dis_data) {


            if ($dis_data['DistOutlet']['is_active'] == 1) {
                $status = 'Active';
            } else {
                $status = 'In-Active';
            }
            if ($dis_data['DistOutlet']['bonus_type_id'] == 2) {
                $bonus_type = h('Big Bonus');
            } elseif ($dis_data['DistOutlet']['bonus_type_id'] == 1) {
                $bonus_type = h('Small Bonus');
            } else {
                $bonus_type = h('Not Applicable');
            }
            $table .= '<tr>
                    <td>' . $dis_data['DistOutlet']['id'] . '</td>
                    <td>' . $dis_data['DistOutlet']['name'] . '</td>
                    <td style="mso-number-format:\'\@\';">' . $dis_data['DistOutlet']['mobile'] . '</td>
                    <td>' . $dis_data['DistMarket']['name'] . '</td>
                    <td>' . $dis_data['DistRoute']['name'] . '</td>
                    <td>' . $dis_data['Thana']['name'] . '</td>
                    <td>' . $dis_data['Office']['office_name'] . '</td>
                    <td>' . $dis_data['OutletCategory']['category_name'] . '</td>
                    <td>' . $bonus_type . '</td>
                    <td>' . $status . '</td>
                </tr>
                ';
        }
        $table .= '</tbody></table>';

        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="DistOutlets.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        header("Expires: 0");
        echo $table;
        $this->autoRender = false;
    }
}
