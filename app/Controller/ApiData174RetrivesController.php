<?php
App::uses('AppController', 'Controller');

/**
 * Controller
 *
 * @property ApiDataRetrives $ApiDataRetrives
 * @property PaginatorComponent $Paginator
 */
class ApiData174RetrivesController extends AppController
{
    public $components = array('RequestHandler', 'Usermgmt.UserAuth');

    /* ------------------- User login --------------------- */

    public function user_login()
    {
        $this->loadModel('Usermgmt.User');
        $this->loadModel('Office');
        $this->loadModel('Store');
        $json_data = $this->request->input('json_decode', true);
        $username = $json_data['username'];
        $password = md5($json_data['password']);


        $path = APP . 'logs/';
        $myfile = fopen($path . "login_174.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
        //add new for version
        if ($json_data['version']) {
            $version = $json_data['version'];
        } else {
            $version = '';
        }
        $version_info = array();
        $c_date = date('Y-m-d');
        if ($version) {
            $sql = "SELECT TOP 1 * FROM app_versions WHERE name='$version' AND status=1 AND start_date<='$c_date' AND target_apk=1 ORDER BY ID DESC";
            $version_info = $this->User->query($sql);
        } else {
            $sql = "SELECT TOP 1 * FROM app_versions WHERE status=1 AND start_date>'$c_date' AND target_apk=1 ORDER BY ID DESC";
            $version_info = $this->User->query($sql);
        }
        //end for version


        if ($version_info) {
            $user_info = $this->User->find('first', array(
                'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'User.mac_id', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name'),
                'conditions' => array('User.username' => $username, 'User.password' => $password, 'User.active' => 1),
                'recursive' => 0
            ));

            // pr($user_info);exit;

            if (!empty($user_info)) {
                /*------------------------------mac check: Start ---------------*/
                if (empty($user_info['User']['mac_id'])) {
                    $mac_exist = $this->User->find('first', array(
                        'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'User.mac_id', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name'),
                        'conditions' => array('User.mac_id' => $json_data['mac']),
                        'recursive' => 0
                    ));
                    if ($mac_exist) {
                        $login['status'] = 0;
                        $login['message'] = 'Mac Already Configured';
                        $res = $login;
                        $this->set(array(
                            'user_info' => array($res),
                            '_serialize' => array('user_info')
                        ));
                        return 0;
                    } else {
                        $mac_data['mac_id'] = $json_data['mac'];
                        $mac_data['version'] = $version;
                        $mac_data['id'] = $user_info['User']['id'];
                        $this->User->saveAll($mac_data);



                        $office_info = $this->Office->find('first', array(
                            'fields' => array('Office.office_name', 'Office.phone', 'Office.email', 'Office.address', 'ParentOffice.office_name', 'ParentOffice.phone', 'ParentOffice.email', 'ParentOffice.address'),
                            'conditions' => array('Office.id' => $user_info['SalesPerson']['office_id']),
                            'recursive' => 0
                        ));

                        if (!empty($office_info['ParentOffice'])) {
                            $office['head_office']['head_office_name'] = $office_info['ParentOffice']['office_name'];
                            $office['head_office']['head_office_phone'] = $office_info['ParentOffice']['phone'];
                            $office['head_office']['head_office_email'] = $office_info['ParentOffice']['email'];
                            $office['head_office']['head_office_address'] = $office_info['ParentOffice']['address'];
                        } else {
                            $office['head_office_name'] = '';
                            $office['head_office_phone'] = '';
                            $office['head_office_email'] = '';
                            $office['head_office_address'] = '';
                        }

                        $office['office']['office_name'] = $office_info['Office']['office_name'];
                        $office['office']['office_phone'] = $office_info['Office']['phone'];
                        $office['office']['office_email'] = $office_info['Office']['email'];
                        $office['office']['office_address'] = $office_info['Office']['address'];

                        $store_info = $this->Store->find('first', array(
                            'fields' => array('Store.id'),
                            'conditions' => array('Store.store_type_id' => 3, 'Store.territory_id' => $user_info['SalesPerson']['territory_id']),
                            'recursive' => -1
                        ));
                        if (!empty($store_info)) {
                            $login['status'] = 1;
                            $login['message'] = 'Success';
                            $user_info['User']['outlet_delete_btn_hide_date'] = $version_info[0][0]['outlet_delete_btn_hide_date'];
                            $res = array_merge($login, $user_info, $store_info, $office);
                        } else {
                            $login['status'] = 0;
                            $login['message'] = 'Store not configured yet for this user.';
                            $res = $login;
                        }
                    }
                } else {
                    $this->LoadModel('CommonMac');
                    $common_mac_check = $this->CommonMac->find('first', array('conditions' => array('CommonMac.mac_id' => $json_data['mac'])));
                    if ($json_data['mac'] == $user_info['User']['mac_id'] || $common_mac_check) {
                        /*---- version update in user table : start -------------*/
                        $version_data['version'] = $version;
                        $version_data['id'] = $user_info['User']['id'];
                        $this->User->saveAll($version_data);
                        /*---- version update in user table : END -------------*/
                        $office_info = $this->Office->find('first', array(
                            'fields' => array('Office.office_name', 'Office.phone', 'Office.email', 'Office.address', 'ParentOffice.office_name', 'ParentOffice.phone', 'ParentOffice.email', 'ParentOffice.address'),
                            'conditions' => array('Office.id' => $user_info['SalesPerson']['office_id']),
                            'recursive' => 0
                        ));

                        if (!empty($office_info['ParentOffice'])) {
                            $office['head_office']['head_office_name'] = $office_info['ParentOffice']['office_name'];
                            $office['head_office']['head_office_phone'] = $office_info['ParentOffice']['phone'];
                            $office['head_office']['head_office_email'] = $office_info['ParentOffice']['email'];
                            $office['head_office']['head_office_address'] = $office_info['ParentOffice']['address'];
                        } else {
                            $office['head_office_name'] = '';
                            $office['head_office_phone'] = '';
                            $office['head_office_email'] = '';
                            $office['head_office_address'] = '';
                        }

                        $office['office']['office_name'] = $office_info['Office']['office_name'];
                        $office['office']['office_phone'] = $office_info['Office']['phone'];
                        $office['office']['office_email'] = $office_info['Office']['email'];
                        $office['office']['office_address'] = $office_info['Office']['address'];

                        $store_info = $this->Store->find('first', array(
                            'fields' => array('Store.id'),
                            'conditions' => array('Store.store_type_id' => 3, 'Store.territory_id' => $user_info['SalesPerson']['territory_id']),
                            'recursive' => -1
                        ));
                        if (!empty($store_info)) {
                            $login['status'] = 1;
                            $login['message'] = 'Success';
                            $user_info['User']['outlet_delete_btn_hide_date'] = $version_info[0][0]['outlet_delete_btn_hide_date'];
                            $res = array_merge($login, $user_info, $store_info, $office);
                        } else {
                            $login['status'] = 0;
                            $login['message'] = 'Store not configured yet for this user.';
                            $res = $login;
                        }
                    } else {
                        $login['status'] = 0;
                        $login['message'] = 'Mac Id Not Match';
                        $res = $login;
                    }
                }
            } else {
                $login['status'] = 0;
                $login['message'] = 'Username or Password does not match';
                $res = $login;
            }
        } else {
            $login['status'] = 0;
            $login['message'] = 'Version does not match!';
            $res = $login;
        }

        $this->set(array(
            'user_info' => array($res),
            '_serialize' => array('user_info')
        ));
    }

    /* ------------------- End User login ---------------------- */


    /*
     * Update data push time
     * @return json 
     */

    //Start user active check 
    public function user_status_check($so_id = 0)
    {

        $this->loadModel('Usermgmt.User');

        $res['status'] = 0;
        $res['message'] = 'User is inactive!';

        if ($so_id) {
            $user_info = $this->User->find('first', array(
                'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name'),
                'conditions' => array('SalesPerson.id' => $so_id, 'User.active' => 0),
                'recursive' => 0
            ));

            pr($user_info);

            if ($user_info) {
                $res['status'] = 1;
            } else {
                $res['status'] = 0;
                $this->set(array(
                    'memo' => $res,
                    '_serialize' => array('memo')
                ));
                exit;
            }
        }
        //return $res;
    }

    //End user active check

    public function update_data_push_time()
    {
        $this->loadModel('SalesPerson');
        $this->loadModel('Usermgmt.User');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];

        //add new for version
        if ($json_data['version']) {
            $version = $json_data['version'];
        } else {
            $version = '';
        }
        $version_info = array();
        $c_date = date('Y-m-d');
        if ($version) {
            $sql = "SELECT TOP 1 * FROM app_versions WHERE name='$version' AND status=1 AND start_date<='$c_date' ORDER BY ID DESC";
            $version_info = $this->SalesPerson->query($sql);
        } else {
            $sql = "SELECT TOP 1 * FROM app_versions WHERE status=1 AND start_date>'$c_date' ORDER BY ID DESC";
            $version_info = $this->SalesPerson->query($sql);
        }
        //end for version

        if ($version_info) {
            //check user status
            $user_info = $this->User->find('first', array(
                'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name'),
                'conditions' => array('SalesPerson.id' => $so_id, 'User.active' => 1),
                'recursive' => 0
            ));

            if ($user_info) {
                $data['last_data_push_time'] = $this->current_datetime();
                $this->SalesPerson->id = $json_data['so_id'];
                $this->SalesPerson->save($data);

                $res['status'] = 1;
                $res['message'] = 'Success';
            } else {
                $res['status'] = 100;
                $res['message'] = 'User is inactive!';
            }
        } else {
            $res['status'] = 100;
            $res['message'] = 'Version does not match!';
        }


        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }

    /* ------------------- start Update so current inventory ---------------------- */

    /*
     * Update so current inventory 
     * @return json 
     */

    public function update_so_current_inventory()
    {
        $this->loadModel('Store');
        $this->loadModel('CurrentInventory');
        $this->loadModel('Challan');
        $this->loadModel('ChallanDetail');
        $this->loadModel('Memo');
        $this->loadModel('MemoDetail');

        $json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }


        $data['last_data_push_time'] = $this->current_datetime();
        $cur_store_id = $json_data['store_id'];

        // update SO Store Qty=0
        $result = $this->CurrentInventory->query("Update ci SET ci.qty=0 FROM current_inventories  ci LEFT JOIN stores  st ON ci.store_id=st.id  where st.store_type_id=3 and st.id=$cur_store_id");

        // Scan all challans data and update SO Store Qty start

        $result_challan = $this->Challan->find('all', array(
            'joins' => array(
                array(
                    'table' => 'stores',
                    'alias' => 'st',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Challan.receiver_store_id = st.id'
                    )
                )
            ),
            'conditions' => array(
                'Challan.received_date >= ' => '2017-07-01',
                'Challan.receiver_store_id' => $cur_store_id
            ),
            'fields' => array('Challan.id as id,Challan.receiver_store_id as receiver_store_id'),
            'recursive' => -1
        ));



        foreach ($result_challan as $row) {
            /* retrieve challans details */
            $result_challan_details = "";
            $challan_id = $row[0]['id'];
            $receiver_store_id = $row[0]['receiver_store_id'];
            $result_challan_details = $this->ChallanDetail->find('all', array(
                'conditions' => array('challan_id' => $challan_id),
                'recursive' => -1
            ));

            foreach ($result_challan_details as $row_details) {

                $row_details = $row_details['ChallanDetail'];

                $product_id = $row_details['product_id'];
                $challan_id = $row_details['challan_id'];
                $measurement_unit_id = $row_details['measurement_unit_id'];
                $challan_qty = $this->unit_convert($product_id, $measurement_unit_id, $row_details['challan_qty']);
                $batch_no = $row_details['batch_no'];
                $expire_date = $row_details['expire_date'];

                /* update SO Qty */

                $result_up_each = $this->CurrentInventory->updateAll(
                    array('CurrentInventory.qty' => "CurrentInventory.qty+$challan_qty"),
                    array(
                        'CurrentInventory.store_id' => $receiver_store_id,
                        'CurrentInventory.product_id' => $product_id,
                        'CurrentInventory.batch_number' => $batch_no,
                        'CurrentInventory.expire_date' => $expire_date
                    )
                );

                $result_ch = $this->Challan->updateAll(array('Challan.status' => 2), array('Challan.id' => $challan_id));
            }
        }

        /* Scan all challans data and update SO Store Qty end */

        /* Scan all memo data and update SO Store Qty start */

        $result_memo = $this->Memo->find('all', array(
            'joins' => array(
                array(
                    'table' => 'stores',
                    'alias' => 'st',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Memo.territory_id = st.territory_id'
                    )
                )
            ),
            'conditions' => array(
                'Memo.memo_date >= ' => '2017-07-01',
                'st.id' => $cur_store_id
            ),
            'fields' => array('Memo.id as memo_id,Memo.memo_no as memo_no,st.id as store_id'),
            'recursive' => -1
        ));


        foreach ($result_memo as $row_memo) {
            /* retrieve memo details */
            $row_memo = $row_memo[0];
            $result_memo_details = "";
            $memo_no = $row_memo['memo_no'];
            $memo_id = $row_memo['memo_id'];
            $store_id = $row_memo['store_id'];

            //$result_memo_details = $this->MemoDetail->query("SELECT * from memo_details where memo_id=$memo_id");
            $result_memo_details = $this->MemoDetail->find('all', array(
                'conditions' => array('memo_id' => $memo_id),
                'recursive' => -1
            ));
            foreach ($result_memo_details as $row_memo_details) {

                $row_memo_details = $row_memo_details['MemoDetail'];
                $product_id = $row_memo_details['product_id'];
                $measurement_unit_id = $row_memo_details['measurement_unit_id'];
                $sales_qty = $this->unit_convert_from_memo_details($product_id, $row_memo_details['sales_qty']);

                /* retrieve query */
                // $result_get_id = $this->CurrentInventory->query("SELECT top 1 * from current_inventories where store_id=$store_id and product_id=$product_id");
                $result_get_id = $this->CurrentInventory->find('first', array(
                    'conditions' => array(
                        'store_id' => $store_id,
                        'product_id' => $product_id
                    ),
                    'recursive' => -1
                ));

                $current_inventory_id = "";
                if (!empty($result_get_id)) {
                    $current_inventory_id = $result_get_id['CurrentInventory']['id'];

                    /* update SO Qty */
                    //$result_up_each_memo = $this->CurrentInventory->query("update current_inventories set qty=qty-$sales_qty where store_id=$store_id and product_id=$product_id and id=$current_inventory_id");

                    $result_up_each_memo = $this->CurrentInventory->updateAll(
                        array('CurrentInventory.qty' => "CurrentInventory.qty-$sales_qty"),
                        array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id, 'CurrentInventory.id' => $current_inventory_id)
                    );
                }
            }
        }

        /* Scan all memo data and update SO Store Qty end */

        $res['status'] = 1;
        $res['message'] = 'Success';

        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }

    /* ------------------- end Update so current inventory ---------------------- */

    // unit convert to unit_convert_from_memo_details
    public function unit_convert_from_memo_details($product_id = '', $qty = '')
    {
        $this->loadModel('ProductMeasurement');
        $unit_info = $this->ProductMeasurement->find('first', array(
            'conditions' => array(
                'ProductMeasurement.product_id' => $product_id
            )
        ));
        if (!empty($unit_info)) {
            return sprintf('%.2f', ($unit_info['ProductMeasurement']['qty_in_base'] * $qty));
        } else {
            return sprintf('%.2f', $qty);
        }
    }

    /* ------------------- Start Thana --------------------- */

    public function get_thana()
    {
        $this->loadModel('ThanaTerritory');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $territory_id = $json_data['territory_id'];
        $last_update_date = $json_data['last_update_date'];


        if (!empty($json_data['child_territories'])) {
            $territory_id = array($territory_id);

            foreach ($json_data['child_territories'] as $key => $value) {
                array_push($territory_id, $value['id']);
                //echo $value['id'].'<br>';
            }
        }


        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('ThanaTerritory.territory_id' => $territory_id);
        } else {
            $conditions = array('ThanaTerritory.territory_id' => $territory_id, 'ThanaTerritory.updated_at >' => $last_update_date);
        }


        $thana_list = $this->ThanaTerritory->find('all', array(
            'fields' => array('Thana.id', 'Thana.name', 'ThanaTerritory.updated_at'),
            'conditions' => $conditions,
            'order' => array('Thana.id' => 'asc'),
            'recursive' => 0
        ));


        $data_array = array();

        foreach ($thana_list as $tl) {

            $dataT['id'] = $tl['Thana']['id'];
            $dataT['name'] = $tl['Thana']['name'];
            $dataTT['updated_at'] = $tl['ThanaTerritory']['updated_at'];
            $dataTT['action'] = 1;
            $data['Thana'] = $dataT;
            $data['ThanaTerritory'] = $dataTT;
            $data_array[] = $data;
        }



        $this->set(array(
            'thana_list' => $data_array,
            '_serialize' => array('thana_list')
        ));
    }

    public function get_territory_thana()
    {
        $this->loadModel('ThanaTerritory');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $territory_id = $json_data['territory_id'];
        $last_update_date = $json_data['last_update_date'];


        if (!empty($json_data['child_territories'])) {
            $territory_id = array($territory_id);

            foreach ($json_data['child_territories'] as $key => $value) {
                array_push($territory_id, $value['id']);
                //echo $value['id'].'<br>';
            }
        }


        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('ThanaTerritory.territory_id' => $territory_id);
        } else {
            $conditions = array('ThanaTerritory.territory_id' => $territory_id, 'ThanaTerritory.updated_at >' => $last_update_date);
        }


        $thana_list = $this->ThanaTerritory->find('all', array(
            'fields' => array('Thana.id', 'Thana.name', 'ThanaTerritory.territory_id', 'ThanaTerritory.updated_at'),
            'conditions' => $conditions,
            'order' => array('Thana.id' => 'asc'),
            'recursive' => 0
        ));

        //pr($thana_list);

        $data_array = array();

        foreach ($thana_list as $tl) {

            $dataT['id'] = $tl['Thana']['id'];
            $dataT['name'] = $tl['Thana']['name'];
            $dataT['territory_id'] = $tl['ThanaTerritory']['territory_id'];
            $dataTT['updated_at'] = $tl['ThanaTerritory']['updated_at'];
            $dataTT['action'] = 1;
            $data['Thana'] = $dataT;
            $data['ThanaTerritory'] = $dataTT;
            $data_array[] = $data;
        }



        $this->set(array(
            'thana_list' => $data_array,
            '_serialize' => array('thana_list')
        ));
    }

    /* ------------------- End Thana ---------------------- */


    /* ------------------- Start Market ---------------------- */

    public function get_market_list()
    {
        $this->loadModel('Market');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $territory_id = $json_data['territory_id'];
        //$so_id = $json_data['so_id'];
        $last_update_date = $json_data['last_update_date'];

        if (!empty($json_data['child_territories'])) {
            $territory_id = array($territory_id);

            foreach ($json_data['child_territories'] as $key => $value) {
                array_push($territory_id, $value['id']);
                //echo $value['id'].'<br>';
            }
        }

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('Market.territory_id' => $territory_id);
        } else {
            //$conditions = array('Market.action >' => 0, 'Market.territory_id' => $territory_id, 'Market.updated_at >=' => $last_update_date);
            $conditions = array('Market.territory_id' => $territory_id, 'Market.updated_at >=' => $last_update_date);
        }



        $market_list = $this->Market->find('all', array(
            'fields' => array('Market.id', 'Market.territory_id', 'Market.is_active', 'Market.name', 'Market.address', 'Market.location_type_id', 'Market.thana_id', 'Market.updated_at', 'Market.action'),
            'conditions' => $conditions,
            'order' => array('Market.updated_at' => 'asc'),
            'recursive' => -1
        ));

        //pr($market_list);


        $data_array = array();
        foreach ($market_list as $mk) {
            $data['id'] = $mk['Market']['id'];
            $data['territory_id'] = $mk['Market']['territory_id'];
            $data['name'] = $mk['Market']['name'];
            $data['is_active'] = $mk['Market']['is_active'];
            $data['thana_id'] = $mk['Market']['thana_id'];
            $data['address'] = $mk['Market']['address'];
            $data['location_type'] = $mk['Market']['location_type_id'];
            $data['updated_at'] = $mk['Market']['updated_at'];
            $data['action'] = ($res_status == 1 ? 1 : $mk['Market']['action']);
            $data_array[]['Market'] = $data;
        }

        $this->set(array(
            'market_list' => $data_array,
            '_serialize' => array('market_list')
        ));
    }

    public function callback_market_list()
    {
        $this->loadModel('Market');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        //$json_data = array(array('id'=>238),array('id'=>10249));

        if (!empty($json_data)) {
            $data_array = array();
            foreach ($json_data as $val) {
                $data['id'] = $val['id'];
                $data['action'] = 0;
                $data_array[] = $data;
            }
            $this->Market->saveAll($data_array);
        }
        $res['status'] = 1;
        $res['message'] = 'Success';

        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }

    public function create_market()
    {
        $this->loadModel('Market');

        $json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        $all_inserted = true;
        $relation_array = array();

        $path = APP . 'logs/';
        $myfile = fopen($path . "create_market.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        //pr($json_data['market_list']);

        if (!empty($json_data['market_list'])) {
            $market_array = array();
            foreach ($json_data['market_list'] as $val) {
                $data['temp_id'] = $val['temp_id'];
                $data['code'] = $val['code'];
                $data['name'] = $val['name'];
                $data['address'] = $val['address'];
                $data['location_type_id'] = $val['location_type_id'];
                $data['thana_id'] = $val['thana_id'];
                $data['territory_id'] = $val['territory_id'];
                /* $data['is_active'] = 1; */
                $data['is_active'] = ($val['is_active'] != '' ? $val['is_active'] : 1);
                $data['created_at'] = isset($val['updated_at']) && $val['updated_at'] ? $val['updated_at'] : $this->current_datetime();
                $data['created_by'] = $val['user_id'];
                $data['updated_at'] = isset($val['updated_at']) && $val['updated_at'] ? $val['updated_at'] : $this->current_datetime();


                $market_array = $data;

                if (is_numeric($val['temp_id'])) {
                    $data['id'] = $val['temp_id'];
                    if ($this->Market->save($data)) {
                        unset($data['id']);
                        $relation_array['new_id'] = $val['temp_id'];
                        $relation_array['previous_id'] = $val['temp_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
                } else {
                    $this->Market->create();
                    if ($this->Market->save($data)) {
                        unset($data['id']);
                        $relation_array['new_id'] = $this->Market->getLastInsertID();
                        $relation_array['previous_id'] = $val['temp_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
                }
            }

            $res['status'] = 1;
            if ($all_inserted) {

                $res['message'] = 'Market has been created successfuly completed.';
            } else {

                $res['message'] = 'One or More Market Failed to create.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }

        $this->set(array(
            'market' => $res,
            '_serialize' => array('market')
        ));
    }

    /* public function create_market() 
      {
      $this->loadModel('Market');

      $json_data = $this->request->input('json_decode', true );
      if(!empty($json_data['market_list']))
      {
      $market_array = array();
      foreach($json_data['market_list'] as $val)
      {
      $data['temp_id'] = $val['temp_id'];
      $data['code'] = $val['code'];
      $data['name'] = $val['name'];
      $data['address'] = $val['address'];
      $data['location_type_id'] = $val['location_type_id'];
      $data['thana_id'] = $val['thana_id'];
      $data['territory_id'] = $val['territory_id'];
      $data['is_active'] = 1;
      $data['created_at'] = $this->current_datetime();
      $data['created_by'] = $val['user_id'];
      $data['updated_at'] = $this->current_datetime();
      $market_array[] = $data;
      }

      if ($this->Market->saveAll($data)) {
      $res['status'] = 1;
      $res['message'] = 'Market has been created successfuly completed.';

      } else {
      $res['status'] = 0;
      $res['message'] = 'Market not created.';
      }
      }else{
      $res['status'] = 1;
      $res['message'] = 'Market has been created successfuly completed.';
      }

      $this->set(array(
      'market' => $res,
      '_serialize' => array('market')
      ));
      } */

    /* ------------------- End Market ---------------------- */


    /* ------------------- Start Outlet ---------------------- */

    public function get_outlet_type()
    {

        $outlet_types = array(
            array('id' => 1, 'title' => 'Pharma'),
            array('id' => 2, 'title' => 'NGO')
        );
        $this->set(array(
            'outlet_types' => $outlet_types,
            '_serialize' => array('outlet_types')
        ));
    }

    public function get_outlet_category()
    {
        $this->loadModel('OutletCategory');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('OutletCategory.is_active' => 1);
        } else {
            $conditions = array('OutletCategory.updated_at >' => $last_update_date, 'OutletCategory.is_active' => 1);
        }
        $outlet_category_list = $this->OutletCategory->find('all', array(
            'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
            'conditions' => $conditions,
            'order' => array('OutletCategory.updated_at' => 'asc'),
            'recursive' => -1
        ));

        $data_array = array();
        foreach ($outlet_category_list as $key => $val) {
            $outlet_category_list[$key]['OutletCategory']['action'] = 1;
        }

        $this->set(array(
            'outlet_category_list' => $outlet_category_list,
            '_serialize' => array('outlet_category_list')
        ));
    }

    public function get_outlet_list()
    {
        $this->loadModel('Outlet');
        $this->LoadModel('Thana');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $json = $this->request->input();
        $territory_id = $json_data['territory_id'];
        //$so_id = $json_data['so_id'];

        $last_update_date = $json_data['last_update_date'];

        if (!empty($json_data['child_territories'])) {
            $territory_id = array($territory_id);

            foreach ($json_data['child_territories'] as $key => $value) {
                array_push($territory_id, $value['id']);
                //echo $value['id'].'<br>';
            }
        }

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('Market.territory_id' => $territory_id);
        } else {
            //$conditions = array('Outlet.action >' => 0, 'Market.territory_id' => $territory_id, 'Outlet.updated_at >=' => $last_update_date);
            $conditions = array('Market.territory_id' => $territory_id, 'Outlet.updated_at >=' => $last_update_date);
        }



        $outlet_list = $this->Outlet->find('all', array(
            'fields' => array('Outlet.id', 'Outlet.code', 'Outlet.name', 'Outlet.in_charge', 'Outlet.ownar_name', 'Outlet.address', 'Outlet.mobile', 'Outlet.category_id', 'Outlet.is_pharma_type', 'Outlet.is_ngo', 'Outlet.updated_at', 'Outlet.market_id', 'Outlet.institute_id', 'Outlet.project_id', 'Outlet.latitude', 'Outlet.longitude', 'Outlet.action', 'Outlet.bonus_type_id', 'Outlet.is_active', 'Market.thana_id', 'Market.territory_id'),
            'conditions' => $conditions,
            'order' => array('Outlet.updated_at' => 'asc'),
            'recursive' => 0
        ));

        // echo $this->Outlet->getLastquery();exit;
        // pr($outlet_list);exit;

        $data_array = array();

        foreach ($outlet_list as $val) {
            $data['id'] = $val['Outlet']['id'];
            $data['code'] = $val['Outlet']['code'];
            //$data['name'] = $val['Outlet']['name'];
            $data['name'] = str_replace("'", "", $val['Outlet']['name']);
            $data['in_charge'] = $val['Outlet']['in_charge'];
            $data['ownar_name'] = $val['Outlet']['ownar_name'];
            $data['address'] = $val['Outlet']['address'];
            $data['mobile'] = $val['Outlet']['mobile'];
            $data['category_id'] = $val['Outlet']['category_id'];
            $data['is_pharma_type'] = $val['Outlet']['is_pharma_type'];
            $data['is_ngo'] = $val['Outlet']['is_ngo'];
            $data['bonus_type_id'] = $val['Outlet']['bonus_type_id'];
            $data['updated_at'] = $val['Outlet']['updated_at'];
            $data['market_id'] = $val['Outlet']['market_id'];
            $data['institute_id'] = $val['Outlet']['institute_id'];
            $data['project_id'] = $val['Outlet']['project_id'];
            $data['latitude'] = $val['Outlet']['latitude'];
            $data['longitude'] = $val['Outlet']['longitude'];
            $data['thana_id'] = $val['Market']['thana_id'];
            $data['is_active'] = $val['Outlet']['is_active'];
            $data['territory_id'] = $val['Market']['territory_id'];
            $data['action'] = ($res_status == 1 ? 1 : $val['Outlet']['action']);

            $data_array[]['Outlet'] = $data;
        }

        $this->set(array(
            'outlet_list' => $data_array,
            '_serialize' => array('outlet_list')
        ));

        /* $path = APP . 'test/';
          $myfile = fopen($path."get_outlet_list.txt", "w") or die("Unable to open file!");
          fwrite($myfile, $json."\n");
          fwrite($myfile, json_encode($outlet_list));
          fclose($myfile); */
    }

    public function callback_outlet_list()
    {
        $this->loadModel('Outlet');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        if (!empty($json_data)) {
            $data_array = array();
            foreach ($json_data as $val) {
                $data['id'] = $val['id'];
                $data['action'] = 0;
                $data_array[] = $data;
            }
            $this->Outlet->saveAll($data_array);
        }
        $res['status'] = 1;
        $res['message'] = 'Success';

        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }

    // end callback_outlet_list()



    public function create_outlet()
    {
        $this->loadModel('Outlet');
        $json_data = $this->request->input('json_decode', true);


        $json = $this->request->input();
        $all_inserted = true;
        $relation_array = array();

        /* removing unicode characters */
        $raw_json_data = str_replace("蹢", '"', $json);
        $json_data = json_decode($raw_json_data, TRUE);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "create_outlet.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);


        if (!empty($json_data['outlet_list'])) {

            // data array							
            $outlet_array = array();
            foreach ($json_data['outlet_list'] as $val) {
                if ($val['id'] == 'null' && $val['temp_id'] == 'null') {
                    $all_inserted = false;
                    continue;
                }
                $data = array();
                $data['temp_id'] = $val['temp_id'];
                $data['name'] = $val['name'];
                $data['in_charge'] = $val['in_charge'];
                $data['ownar_name'] = $val['owner_name'];
                $data['address'] = $val['address'];
                $data['mobile'] = $val['mobile'];
                $data['market_id'] = (is_numeric($val['market_id']) ? $val['market_id'] : 0);
                //$data['market_id'] = $val['market_id'];
                $data['category_id'] = $val['category_id'];
                $data['is_pharma_type'] = $val['is_pharma_type'];
                $data['is_ngo'] = $val['is_ngo_type'];
                $data['institute_id'] = ($val['institute_id'] != '' ? $val['institute_id'] : 0);
                $data['is_active'] = ($val['is_active'] != '' ? $val['is_active'] : 1);

                //echo $val['updated_at'];

                $data['created_at'] = isset($val['updated_at']) && $val['updated_at'] != 'null' ? $val['updated_at'] : $this->current_datetime();
                $data['created_by'] = $val['user_id'];

                $data['updated_at'] = isset($val['updated_at']) && $val['updated_at'] != 'null' ? $val['updated_at'] : $this->current_datetime();
                $data['updated_by'] = $val['user_id'];

                $data['latitude'] = (isset($val['latitude']) != '' ? $val['latitude'] : 0);
                $data['longitude'] = (isset($val['langitude']) != '' ? $val['langitude'] : 0);
                $data['bonus_type_id'] = (isset($val['bonus_party_type']) != '' && $val['bonus_party_type'] != 'null' ? $val['bonus_party_type'] : 0);



                unset($this->Outlet->validate['market_id']);
                unset($this->Outlet->validate['name']);
                unset($this->Outlet->validate['mobile']);
                unset($this->Outlet->validate['institute_id']);
                unset($this->Outlet->validate['category_id']);

                /* resolved data range issue */
                $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
                $data['updated_at'] = date("Y-m-d H:i:s", strtotime($data['updated_at']));
                if ($val['id'] != 0 && $val['temp_id'] != 'O') {
                    $data['id'] = $val['id'];
                    if ($this->Outlet->save($data)) {
                        unset($data);
                        $relation_array['new_id'] = $val['id'];
                        $relation_array['previous_id'] = $val['temp_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
                } else {

                    $this->Outlet->create();
                    if ($this->Outlet->save($data)) {
                        unset($data);
                        $relation_array['new_id'] = $this->Outlet->getLastInsertID();
                        $relation_array['previous_id'] = $val['temp_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
                }
            }

            $res['status'] = 1;
            if ($all_inserted) {

                $res['message'] = 'Outlet has been created successfuly completed.';
            } else {

                $res['message'] = 'One or More Outlet Failed to create.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "create_outlet_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . serialize($res));
        fclose($myfile);

        $this->set(array(
            'outlet' => $res,
            '_serialize' => array('outlet')
        ));
    }

    /* ------------------- End Outlet ---------------------- */


    /* ------------------- Start Product ---------------------- */

    public function get_product_types()
    {
        $this->loadModel('ProductType');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductType.updated_at >' => $last_update_date);
        }
        $product_types = $this->ProductType->find('all', array(
            'fields' => array('ProductType.id', 'ProductType.name', 'ProductType.updated_at'),
            'conditions' => $conditions,
            'order' => array('ProductType.id' => 'asc'),
            'recursive' => -1
        ));

        foreach ($product_types as $key => $val) {
            $product_types[$key]['ProductType']['action'] = 1;
        }

        $this->set(array(
            'product_types' => $product_types,
            '_serialize' => array('product_types')
        ));
    }

    public function get_product_categories()
    {
        $this->loadModel('ProductCategory');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductCategory.updated_at >' => $last_update_date);
        }
        $product_categories = $this->ProductCategory->find('all', array(
            'fields' => array('ProductCategory.id', 'ProductCategory.name', 'ProductCategory.parent_id', 'ProductCategory.is_pharma_product', 'ProductCategory.updated_at'),
            'conditions' => $conditions,
            'order' => array('ProductCategory.updated_at' => 'asc'),
            'recursive' => -1
        ));

        foreach ($product_categories as $key => $val) {
            $product_categories[$key]['ProductCategory']['action'] = 1;
        }

        $this->set(array(
            'product_categories' => $product_categories,
            '_serialize' => array('product_categories')
        ));
    }

    public function get_product_list()
    {
        $this->loadModel('Product');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('Product.updated_at >' => $last_update_date);
        }

        $products = $this->Product->find('all', array(
            'fields' => array('Product.id', 'Product.name', 'Product.product_code', 'Product.product_category_id', 'Product.sales_measurement_unit_id', 'Product.product_type_id', 'Product.updated_at', 'Product.is_pharma', 'Product.order', 'Product.is_injectable', 'CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END as qty_in_base', 'Product.base_measurement_unit_id',),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'product_measurements',
                    'alias' => 'ProductMeasurement',
                    'type' => 'Left',
                    'conditions' => 'ProductMeasurement.product_id=Product.id AND Product.sales_measurement_unit_id=ProductMeasurement.measurement_unit_id'
                ),
            ),
            'order' => array('Product.order' => 'asc'),
            'recursive' => -1
        ));

        // pr($products);exit;


        $this->loadModel('OpenCombinationProduct');
        $price_open = array();
        $bonus_open = array();

        foreach ($products as $key => $val) {
            $products[$key]['Product']['qty_in_base'] = $val[0]['qty_in_base'];
            unset($products[$key][0]);
            //for price opne
            $price_open = $this->OpenCombinationProduct->find('first', array(
                'conditions' => array(
                    'OpenCombinationProduct.product_id' => $val['Product']['id'],
                    'OpenCombination.is_bonus' => 0,
                    'OpenCombination.start_date <=' => $this->current_date(),
                    'OpenCombination.end_date >=' =>  $this->current_date()
                ),
            ));
            //pr($price_open);			

            if ($price_open) {
                $products[$key]['Product']['price_open_start'] = $price_open['OpenCombination']['start_date'];
                $products[$key]['Product']['price_open_end'] = $price_open['OpenCombination']['end_date'];
            } else {
                $products[$key]['Product']['price_open_start'] = '';
                $products[$key]['Product']['price_open_end'] = '';
            }


            //for bonus opne
            $bonus_open = $this->OpenCombinationProduct->find('first', array(
                'conditions' => array(
                    'OpenCombinationProduct.product_id' => $val['Product']['id'],
                    'OpenCombination.is_bonus' => 1,
                    'OpenCombination.start_date <=' => $this->current_date(),
                    'OpenCombination.end_date >=' =>  $this->current_date()
                ),
            ));
            //pr($bonus_open);			

            if ($bonus_open) {
                $products[$key]['Product']['price_bonus_start'] = $bonus_open['OpenCombination']['start_date'];
                $products[$key]['Product']['price_bonus_end'] = $bonus_open['OpenCombination']['end_date'];
            } else {
                $products[$key]['Product']['price_bonus_start'] = '';
                $products[$key]['Product']['price_bonus_end'] = '';
            }

            $products[$key]['Product']['action'] = 1;
        }

        $this->set(array(
            'products' => $products,
            '_serialize' => array('products')
        ));
    }
    /*
        open bonus and open price history 

     */
    public function get_product_wise_open_bonus_and_price_combination()
    {
        $this->loadModel('OpenCombinationProduct');
        $open_combination = $this->OpenCombinationProduct->find('all', array(
            /*'conditions'=>array('OpenCombination.end_date <='=>date('Y-m-d',strtotime('-4 Months'))),*/
            'conditions' => array(
                "OR" => array(
                    'OpenCombination.is_bonus' => 0,
                    'OpenCombination.id' => array(0)
                )
            ),
            'order' => array('OpenCombinationProduct.product_id', 'OpenCombination.is_bonus DESC')
        ));
        $open_data = array();
        foreach ($open_combination as $data) {
            $o_data['is_bonus'] = $data['OpenCombination']['is_bonus'];
            $o_data['start_date'] = $data['OpenCombination']['start_date'];
            $o_data['end_date'] = $data['OpenCombination']['end_date'];
            $o_data['product_id'] = $data['OpenCombinationProduct']['product_id'];
            $o_data['combination_id'] = $data['OpenCombinationProduct']['combination_id'];
            $open_data[] = $o_data;
            unset($o_data);
        }
        $this->set(array(
            'list' => $open_data,
            '_serialize' => array('list')
        ));
    }
    //// Didn't Find any implementation of this below method.get_products
    public function get_products()
    {
        $this->loadModel('Product');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        //$last_update_date = $json_data['last_update_date'];

        $this->loadModel('ProductUpdate');
        $so_id = $json_data['so_id'];

        $product_update = $this->ProductUpdate->find('first', array('conditions' => array('ProductUpdate.so_id' => $so_id), 'recursive' => -1));
        if (empty($product_update)) {
            $udata['so_id'] = $so_id;
            $udata['last_update_date'] = $this->current_datetime();
            $this->ProductUpdate->save($udata);
            $last_update_date = '2017-01-17 09:07:48';
        } else {
            $udata['id'] = $product_update['ProductUpdate']['id'];
            $udata['last_update_date'] = $this->current_datetime();
            $this->ProductUpdate->save($udata);
            $last_update_date = $product_update['ProductUpdate']['last_update_date'];
        }

        $products = $this->Product->find('all', array('fields' => array('Product.id', 'Product.name', 'Product.product_code', 'Product.product_category_id', 'Product.sales_measurement_unit_id', 'Product.product_type_id', 'Product.updated_at', 'Product.is_injectable'), 'conditions' => array('Product.updated_at >' => $last_update_date), 'order' => array('Product.id' => 'asc'), 'recursive' => -1));

        $this->set(array(
            'products' => $products,
            '_serialize' => array('products')
        ));
    }

    public function get_product_combination_list()
    {
        $this->loadModel('ProductCombination');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductCombination.updated_at >' => $last_update_date);
        }
        $conditions['OR'] = array('ProductPrice.project_id is null', 'ProductPrice.project_id' => 0);
        $product_combination = $this->ProductCombination->find('all', array(
            'conditions' => $conditions,
            'order' => array('ProductCombination.updated_at' => 'asc'),
            'joins' => array(
                array(
                    'table' => 'product_prices',
                    'alias' => 'ProductPrice',
                    'conditions' => 'ProductPrice.id=ProductCombination.product_price_id'
                ),
            ),
            'recursive' => -1
        ));
        foreach ($product_combination as $key => $val) {
            $product_combination[$key]['ProductCombination']['action'] = 1;
        }

        $this->set(array(
            'product_combination' => $product_combination,
            '_serialize' => array('product_combination')
        ));
    }
    public function get_product_price_v2()
    {
        $this->loadModel('ProductPricesV2');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductPricesV2.updated_at >' => $last_update_date);
        }
        $conditions['OR'] = array('ProductPricesV2.project_id is null', 'ProductPricesV2.project_id' => 0);
        $conditions['ProductPriceSectionV2.is_so'] = 1;
        $product_price = $this->ProductPricesV2->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'product_price_section_v2',
                    'alias' => 'ProductPriceSectionV2',
                    'conditions' => 'ProductPriceSectionV2.product_price_id=ProductPricesV2.id'
                ),
            ),
            'order' => array('ProductPricesV2.updated_at' => 'asc'),
            'recursive' => -1
        ));
        foreach ($product_price as $key => $val) {
            $product_price[$key]['ProductPricesV2']['action'] = 1;
        }

        $this->set(array(
            'product_price' => $product_price,
            '_serialize' => array('product_price')
        ));
    }
    public function get_product_combination_v2()
    {
        $this->loadModel('ProductCombinationsV2');
        $this->loadModel('ProductPriceOtherForSlabsV2');
        $this->loadModel('SpecialGroup');
        $json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $territory_id = $json_data['territory_id'];
        $this->loadModel('Territory');
        $territory_info = $this->Territory->find(
            'first',
            array(
                'conditions' => array('Territory.id' => $territory_id),
                'fields' => array('Office.id'),
                'recursive' => 0
            )
        );
        $office_id = $territory_info['Office']['id'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);

        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductCombinationsV2.updated_at >' => $last_update_date);
        }
        $conditions[] = array('ProductPriceSectionV2.is_so' => 1);

        $product_combination = $this->ProductCombinationsV2->find('all', array(
            'conditions' => $conditions,
            'order' => array('ProductCombinationsV2.updated_at' => 'asc'),
            'joins' => array(
                array(
                    'table' => 'product_price_section_v2',
                    'alias' => 'ProductPriceSectionV2',
                    'conditions' => 'ProductPriceSectionV2.id=ProductCombinationsV2.section_id'
                ),

            ),
            'recursive' => -1
            // 'fields'=>array('ProductCombinationsV2.*','ProductPriceOtherForSlabsV2.*')
        ));
        foreach ($product_combination as $key => $p_data) {
            $product_combination_id = $p_data['ProductCombinationsV2']['id'];
            $special_group_data = $this->ProductPriceOtherForSlabsV2->find('all', array(
                'conditions' => array(
                    'ProductPriceOtherForSlabsV2.price_for' => 1,
                    'ProductPriceOtherForSlabsV2.type' => 1,
                    'ProductPriceOtherForSlabsV2.product_combination_id' => $product_combination_id
                ),
                'fields' => array(
                    'ProductPriceOtherForSlabsV2.type as type',
                    'ProductPriceOtherForSlabsV2.reffrence_id as reffrence_id',
                    'ProductPriceOtherForSlabsV2.price as price',
                ),
                'group' => array(
                    'ProductPriceOtherForSlabsV2.type',
                    'ProductPriceOtherForSlabsV2.reffrence_id',
                    'ProductPriceOtherForSlabsV2.price',
                ),
                'recursive' => -1
            ));
            $assign_group = array();
            foreach ($special_group_data as $data) {
                $group_id = $data[0]['reffrence_id'];
                $special_group_details = $this->SpecialGroup->find('all', array(
                    'conditions' => array(
                        'SpecialGroup.id' => $group_id,
                        'SPO.reffrence_id' => $office_id
                    ),
                    'joins' => array(
                        array(
                            'table' => 'special_group_other_settings',
                            'alias' => 'SPO',
                            'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=1'
                        ),
                        array(
                            'table' => '(
                                        select t.office_id,spg.reffrence_id as territory_id from special_groups sps
                                        inner join special_group_other_settings spg on sps.id=spg.special_group_id and create_for=2
                                        inner join territories t on t.id=spg.reffrence_id
                                        where 
                                            sps.id=' . $group_id . '
                                    )',
                            'alias' => 'SPT',
                            'type' => 'left',
                            'conditions' => 'SPT.office_id=SPO.reffrence_id'
                        ),
                    ),
                    'group' => array('SPO.reffrence_id'),
                    'fields' => array('SPO.reffrence_id', 'COUNT(SPT.territory_id) as total_territory'),
                    'recursive' => -1
                ));
                if ($special_group_details) {
                    if ($special_group_details[0][0]['total_territory'] > 0) {
                        $special_group_territory_details = $this->SpecialGroup->find('all', array(
                            'conditions' => array(
                                'SpecialGroup.id' => $group_id,
                                'SPO.reffrence_id' => $territory_id
                            ),
                            'joins' => array(
                                array(
                                    'table' => 'special_group_other_settings',
                                    'alias' => 'SPO',
                                    'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=2'
                                )

                            ),
                            'recursive' => -1
                        ));
                        if ($special_group_territory_details) {
                            $assign_group[] = $data[0];
                        } else {
                            $outlet_group_data = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
                            if ($outlet_group_data) {
                                $assign_group[] = $data[0];
                            }
                        }
                    } else {

                        $assign_group[] = $data[0];
                    }
                } else {
                    $outlet_group_data = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
                    if ($outlet_group_data) {
                        $assign_group[] = $data[0];
                    }
                }
            }
            $outlet_category_data = $this->ProductPriceOtherForSlabsV2->find('all', array(
                'conditions' => array(
                    'ProductPriceOtherForSlabsV2.price_for' => 1,
                    'ProductPriceOtherForSlabsV2.type' => 2,
                    'ProductPriceOtherForSlabsV2.product_combination_id' => $product_combination_id
                ),
                'fields' => array(
                    'ProductPriceOtherForSlabsV2.type as type',
                    'ProductPriceOtherForSlabsV2.reffrence_id as reffrence_id',
                    'ProductPriceOtherForSlabsV2.price as price',
                ),
                'recursive' => -1
            ));
            unset($p_data['ProductCombinationsV2']['section_id']);
            unset($p_data['ProductCombinationsV2']['sr_price']);
            $product_combination[$key] = $p_data['ProductCombinationsV2'];
            $outlet_category_data = array_map(function ($data) {
                return $data[0];
            }, $outlet_category_data);
            $product_combination[$key]['other_pricing'] = array_merge($assign_group, $outlet_category_data);
        }

        $this->set(array(
            'product_combination' => $product_combination,
            '_serialize' => array('product_combination')
        ));
    }
    public function get_special_group()
    {
        $this->loadModel('SpecialGroup');
        $this->loadModel('SpecialGroupOtherSetting');
        $json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $territory_id = $json_data['territory_id'];
        $this->loadModel('Territory');
        $territory_info = $this->Territory->find(
            'first',
            array(
                'conditions' => array('Territory.id' => $territory_id),
                'fields' => array('Office.id'),
                'recursive' => 0
            )
        );
        $office_id = $territory_info['Office']['id'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);

        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('SpecialGroup.updated_at >' => $last_update_date);
        }
        $conditions[] = array('SpecialGroup.is_dist' => 0);

        $special_group_data = $this->SpecialGroup->find('all', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));
        $group_data = array();
        foreach ($special_group_data as $data) {
            $data_array = array();
            $outlet_group_id = array();
            $group_id = $data['SpecialGroup']['id'];
            $special_group_territory_details = array();
            $special_group_details = array();
            $special_group_details = $this->SpecialGroup->find('all', array(
                'conditions' => array(
                    'SpecialGroup.id' => $group_id,
                    'SPO.reffrence_id' => $office_id
                ),
                'joins' => array(
                    array(
                        'table' => 'special_group_other_settings',
                        'alias' => 'SPO',
                        'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=1'
                    ),
                    array(
                        'table' => '(
                                    select t.office_id,spg.reffrence_id as territory_id from special_groups sps
                                    inner join special_group_other_settings spg on sps.id=spg.special_group_id and create_for=2
                                    inner join territories t on t.id=spg.reffrence_id
                                    where 
                                        sps.id=' . $group_id . '
                                )',
                        'alias' => 'SPT',
                        'type' => 'left',
                        'conditions' => 'SPT.office_id=SPO.reffrence_id'
                    ),
                ),
                'group' => array('SPO.reffrence_id'),
                'fields' => array('SPO.reffrence_id', 'COUNT(SPT.territory_id) as total_territory'),
                'recursive' => -1
            ));
            if ($special_group_details) {
                if ($special_group_details[0][0]['total_territory'] > 0) {
                    $special_group_territory_details = $this->SpecialGroup->find('all', array(
                        'conditions' => array(
                            'SpecialGroup.id' => $group_id,
                            'SPO.reffrence_id' => $territory_id
                        ),
                        'joins' => array(
                            array(
                                'table' => 'special_group_other_settings',
                                'alias' => 'SPO',
                                'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=2'
                            )

                        ),
                        'recursive' => -1
                    ));
                    if ($special_group_territory_details) {
                        $data_array = $data;
                    }
                } else {
                    $data_array = $data;
                }
            }
            $outlet_group_id = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
            if ($outlet_group_id) {
                $data_array = $data;
            }
            if ($data_array) {
                $conditions = array('SpecialGroupOtherSetting.special_group_id' => $group_id);

                $other = '(';
                /*if(isset($special_group_territory_details))
                {
                    $other.='(SpecialGroupOtherSetting.create_for = 2 AND
                        SpecialGroupOtherSetting.reffrence_id = '.$territory_id .')';                      ;
                }*/
                if ($outlet_group_id) {
                    $other .= '(SpecialGroupOtherSetting.create_for = 4 AND
                        SpecialGroupOtherSetting.reffrence_id = ' . implode(',', $outlet_group_id) . ')';
                }
                if ((isset($special_group_territory_details) && !empty($special_group_territory_details)) || $special_group_details) {
                    if ($outlet_group_id)
                        $other .= ' OR ';
                    $other .= '(SpecialGroupOtherSetting.create_for = 3)';
                }
                $other .= ')';
                $conditions[] = $other;
                $details = $this->SpecialGroupOtherSetting->find('all', array(
                    'conditions' => $conditions,
                    'recursive' => -1
                ));
                $details = array_map(function ($data) {
                    return $data['SpecialGroupOtherSetting'];
                }, $details);
                $group_data[] = array_merge($data['SpecialGroup'], array('details' => $details));
            }
        }
        $this->set(array(
            'special_group' => $group_data,
            '_serialize' => array('special_group')
        ));
    }
    public function get_combination_list()
    {
        $this->loadModel('CombinationsV2');
        $json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $territory_id = $json_data['territory_id'];
        $this->loadModel('Territory');
        $territory_info = $this->Territory->find(
            'first',
            array(
                'conditions' => array('Territory.id' => $territory_id),
                'fields' => array('Office.id'),
                'recursive' => 0
            )
        );
        $office_id = $territory_info['Office']['id'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);

        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('CombinationsV2.updated_at >' => $last_update_date);
        }
        // $conditions[] = array('(CombinationsV2.create_for=1 OR CombinationsV2.create_for=4 OR CombinationsV2.create_for=6)');
        $this->CombinationsV2->unbindModel(array(
            'belongsTo' => array('SoOutletCategory', 'SrOutletCategory', 'SoSpecialGroup', 'SrSpecialGroup')
        ));
        $combination_list = $this->CombinationsV2->find('all', array(
            'conditions' => $conditions,
        ));
        $combination_data = array();
        foreach ($combination_list as $data) {
            $combination_data[] = array_merge($data['CombinationsV2'], array('details' => $data['CombinationDetailsV2']));
        }
        $this->set(array(
            'combinations' => $combination_data,
            '_serialize' => array('combinations')
        ));
    }
    private function get_special_group_outlet_group_by_territory_id($group_id, $territory_id)
    {
        $this->loadModel('SpecialGroupOtherSetting');
        $special_group_outlet_group_details = $this->SpecialGroupOtherSetting->find('list', array(
            'conditions' => array(
                'SpecialGroupOtherSetting.special_group_id' => $group_id,
                'SpecialGroupOtherSetting.create_for' => 4,
                'Market.territory_id' => $territory_id
            ),
            'joins' => array(
                array(
                    'table' => 'outlet_groups',
                    'alias' => 'OutletGroup',
                    'conditions' => 'OutletGroup.id=SpecialGroupOtherSetting.reffrence_id'
                ),
                array(
                    'table' => 'outlet_group_to_outlets',
                    'alias' => 'OutletGroupOutlet',
                    'conditions' => 'OutletGroup.id=OutletGroupOutlet.outlet_group_id'
                ),
                array(
                    'table' => 'outlets',
                    'alias' => 'Outlet',
                    'conditions' => 'Outlet.id=OutletGroupOutlet.outlet_id'
                ),
                array(
                    'table' => 'markets',
                    'alias' => 'Market',
                    'conditions' => 'Outlet.market_id=Market.id'
                ),
            ),
            'fields' => array('OutletGroup.id'),
            'recursive' => -1
        ));
        return $special_group_outlet_group_details;
    }
    public function get_product_combination()
    {
        $this->loadModel('ProductCombination');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        //$last_update_date = $json_data['last_update_date'];

        $this->loadModel('ProductCombinationUpdate');
        $so_id = $json_data['so_id'];

        $product_update = $this->ProductCombinationUpdate->find('first', array('conditions' => array('ProductCombinationUpdate.so_id' => $so_id), 'recursive' => -1));
        if (empty($product_update)) {
            $udata['so_id'] = $so_id;
            $udata['last_update_date'] = $this->current_datetime();
            $this->ProductCombinationUpdate->save($udata);
            $last_update_date = '2017-01-17 09:07:48';
        } else {
            $udata['id'] = $product_update['ProductCombinationUpdate']['id'];
            $udata['last_update_date'] = $this->current_datetime();
            $this->ProductCombinationUpdate->save($udata);
            $last_update_date = $product_update['ProductCombinationUpdate']['last_update_date'];
        }

        $product_combination = $this->ProductCombination->find('all', array(
            'conditions' => array('ProductCombination.updated_at >' => $last_update_date),
            'order' => array('ProductCombination.id' => 'asc'),
            'recursive' => -1
        ));

        foreach ($product_combination as $key => $val) {
            $product_combination[$key]['ProductCombination']['action'] = 1;
        }

        $this->set(array(
            'product_combination' => $product_combination,
            '_serialize' => array('product_combination')
        ));
    }

    public function get_product_price_list()
    {
        $this->loadModel('ProductPrice');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductPrice.updated_at >' => $last_update_date);
        }
        $conditions['OR'] = array('ProductPrice.project_id is null', 'ProductPrice.project_id' => 0);
        $product_price = $this->ProductPrice->find('all', array(
            'conditions' => $conditions,
            'order' => array('ProductPrice.updated_at' => 'asc'),
            'recursive' => -1
        ));
        foreach ($product_price as $key => $val) {
            $product_price[$key]['ProductPrice']['action'] = 1;
        }

        $this->set(array(
            'product_price' => $product_price,
            '_serialize' => array('product_price')
        ));
    }

    public function get_product_price()
    {
        $this->loadModel('ProductPrice');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        //$last_update_date = $json_data['last_update_date'];

        $this->loadModel('ProductPriceUpdate');
        $so_id = $json_data['so_id'];

        $product_update = $this->ProductPriceUpdate->find('first', array('conditions' => array('ProductPriceUpdate.so_id' => $so_id), 'recursive' => -1));
        if (empty($product_update)) {
            $udata['so_id'] = $so_id;
            $udata['last_update_date'] = $this->current_datetime();
            $this->ProductPriceUpdate->save($udata);
            $last_update_date = '2017-01-17 09:07:48';
        } else {
            $udata['id'] = $product_update['ProductPriceUpdate']['id'];
            $udata['last_update_date'] = $this->current_datetime();
            $this->ProductPriceUpdate->save($udata);
            $last_update_date = $product_update['ProductPriceUpdate']['last_update_date'];
        }
        foreach ($product_price as $key => $val) {
            $product_price[$key]['ProductPrice']['action'] = 1;
        }
        $product_price = $this->ProductPrice->find('all', array(
            'conditions' => array('ProductPrice.updated_at >' => $last_update_date),
            'order' => array('ProductPrice.id' => 'asc'),
            'recursive' => -1
        ));

        $this->set(array(
            'product_price' => $product_price,
            '_serialize' => array('product_price')
        ));
    }

    public function get_bonus_product_list()
    {
        $this->loadModel('Bonus');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('Bonus.updated_at >' => $last_update_date);
        }

        $bonus_product = $this->Bonus->find('all', array(
            'conditions' => $conditions,
            'order' => array('Bonus.updated_at' => 'asc'),
            'recursive' => -1
        ));

        foreach ($bonus_product as $key => $val) {
            $bonus_product[$key]['Bonus']['action'] = 1;
        }

        $this->set(array(
            'bonus_product' => $bonus_product,
            '_serialize' => array('bonus_product')
        ));
    }

    /* ------------------- End Outlet ---------------------- */


    /* ------------------- Start Measurement Units ---------------------- */

    public function get_measurement_units()
    {
        $this->loadModel('MeasurementUnit');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('MeasurementUnit.updated_at >' => $last_update_date);
        }
        $measurement_units = $this->MeasurementUnit->find('all', array(
            'fields' => array('MeasurementUnit.id', 'MeasurementUnit.name', 'MeasurementUnit.updated_at'),
            'conditions' => $conditions,
            'order' => array('MeasurementUnit.updated_at' => 'asc'),
            'recursive' => -1
        ));
        foreach ($measurement_units as $key => $val) {
            $measurement_units[$key]['MeasurementUnit']['action'] = 1;
        }
        $this->set(array(
            'measurement_units' => $measurement_units,
            '_serialize' => array('measurement_units')
        ));
    }

    /* ------------------- End Measurement Units ---------------------- */

    /* ------------------- Start Challan ---------------------- */

    public function get_challan_list()
    {
        $this->loadModel('Product');
        $this->loadModel('Challan');
        $this->loadModel('ReturnChallan');
        $json_data = $this->request->input('json_decode', true);


        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }


        /*
    	    $path = APP . 'logs/';
            $myfile = fopen($path . "get_challan_list.txt", "a") or die("Unable to open file!");
            fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
            fclose($myfile);
    	*/

        $store_id = $json_data['store_id'];
        $last_update_date = $json_data['last_update_date'];

        $pending_return_challan_count = $this->ReturnChallan->find('count', array(
            'conditions' => array(
                'ReturnChallan.sender_store_id' => $store_id,
                'ReturnChallan.status' => 1
            )
        ));
        if ($pending_return_challan_count == 0 || (isset($json_data['all']) && $json_data['all'] == 0)) {
            $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
            if ($res_status == 1) {
                $conditions = array(
                    'Challan.receiver_store_id' => $store_id,
                    'Challan.inventory_status_id' => 1,
                    /*'Challan.status >' => 0,
                    'Challan.challan_date >='=> date('Y-m-d',strtotime('-2 month'))*/
                    'OR' => array(
                        array('Challan.status ' => 1),
                        'AND' => array('Challan.challan_date >=' => date('Y-m-d', strtotime('-2 month')), 'Challan.status' => 2)
                    )
                );
            } else {
                $conditions = array(
                    'Challan.receiver_store_id' => $store_id,
                    'Challan.status' => 1,
                    'Challan.inventory_status_id' => 1,
                    'Challan.updated_at >' => $last_update_date
                );
            }

            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');

            $challans = $this->Challan->find('all', array(
                'conditions' => $conditions,
                'fields' => array('Challan.*'),
                'order' => array('Challan.updated_at' => 'asc'),
                'recursive' => 1
            ));
            // echo $this->Challan->getLastquery();exit;
            $challan_array = array();
            if (!empty($challans)) {
                foreach ($challans as $val) {
                    $data['id'] = $val['Challan']['id'];
                    $data['challan_no'] = $val['Challan']['challan_no'];
                    $data['challan_type'] = $val['Challan']['challan_type'];
                    $data['challan_date'] = $val['Challan']['challan_date'];
                    $data['remarks'] = $val['Challan']['remarks'];
                    $data['sender_store_id'] = $val['Challan']['sender_store_id'];
                    $data['transaction_type_id'] = $val['Challan']['transaction_type_id'];
                    $data['status'] = $val['Challan']['status'];
                    $data['receiver_store_id'] = $val['Challan']['receiver_store_id'];
                    $data['received_date'] = ($val['Challan']['received_date']) ? date('d/m/Y', strtotime($val['Challan']['received_date'])) : NULL;
                    $data['created_at'] = $val['Challan']['created_at'];
                    $data['created_by'] = $val['Challan']['created_by'];
                    $data['updated_at'] = $val['Challan']['updated_at'];
                    $data['updated_by'] = $val['Challan']['updated_by'];
                    $data['inventory_status_id'] = $val['Challan']['inventory_status_id'];
                    $data['action'] = 1;


                    $challan_details_array = array();
                    foreach ($val['ChallanDetail'] as $cd) {

                        $units = $this->search_array($cd['product_id'], 'id', $product_list);
                        if ($units['sales_measurement_unit_id'] == $units['challan_measurement_unit_id']) {
                            $received_qty = $cd['received_qty'];
                            $challan_quantity = $cd['challan_qty'];
                        } else {
                            $received_qty = $this->convert_unit_to_unit($cd['product_id'], $units['challan_measurement_unit_id'], $units['sales_measurement_unit_id'], $cd['received_qty']);
                            $challan_quantity = $this->convert_unit_to_unit($cd['product_id'], $units['challan_measurement_unit_id'], $units['sales_measurement_unit_id'], $cd['challan_qty']);
                        }

                        $details_data['id'] = $cd['id'];
                        $details_data['challan_id'] = $cd['challan_id'];
                        $details_data['product_id'] = $cd['product_id'];
                        $details_data['measurement_unit_id'] = $units['sales_measurement_unit_id'];
                        $details_data['challan_qty'] = $challan_quantity;
                        $details_data['received_qty'] = $received_qty;
                        $details_data['batch_no'] = $cd['batch_no'];
                        $details_data['expire_date'] = $cd['expire_date'];
                        $details_data['inventory_status_id'] = $cd['inventory_status_id'];
                        $details_data['remarks'] = $cd['remarks'];
                        $challan_details_array[] = $details_data;
                    }

                    $data['ChallanDetail'] = $challan_details_array;
                    $challan_array[] = $data;
                }
            }
        }

        $this->set(array(
            'challans' => $challan_array,
            '_serialize' => array('challans')
        ));
    }

    public function get_challans()
    {
        $this->loadModel('Product');
        $this->loadModel('Challan');
        $this->loadModel('ChallanUpdate');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        /*
		 $path = APP . 'logs/';
        $myfile = fopen($path . "get_challans.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
        */

        $so_id = $json_data['so_id'];
        $store_id = $json_data['store_id'];


        //$last_update_date = $json_data['last_update_date'];

        $challan_update = $this->ChallanUpdate->find('first', array('conditions' => array('ChallanUpdate.so_id' => $so_id), 'recursive' => -1));

        if (empty($challan_update)) {
            $udata['so_id'] = $so_id;
            $udata['last_update_date'] = $this->current_datetime();
            $this->ChallanUpdate->save($udata);
            $last_update_date = '2017-01-01 09:07:48';
        } else {
            $udata['id'] = $challan_update['ChallanUpdate']['id'];
            $udata['last_update_date'] = $this->current_datetime();
            $this->ChallanUpdate->save($udata);
            $last_update_date = $challan_update['ChallanUpdate']['last_update_date'];
        }


        $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
        $product_list = Set::extract($products, '{n}.Product');

        $challans = $this->Challan->find('all', array(
            'conditions' => array(
                'Challan.receiver_store_id' => $store_id,
                'Challan.status' => 1,
                'Challan.updated_at >' => $last_update_date
            ),
            'fields' => array('Challan.*'),
            'order' => array('Challan.id' => 'asc'),
            'recursive' => 1
        ));


        $challan_array = array();
        if (!empty($challans)) {
            foreach ($challans as $val) {
                $data['id'] = $val['Challan']['id'];
                $data['challan_no'] = $val['Challan']['challan_no'];
                $data['challan_type'] = $val['Challan']['challan_type'];
                $data['challan_date'] = $val['Challan']['challan_date'];
                $data['remarks'] = $val['Challan']['remarks'];
                $data['sender_store_id'] = $val['Challan']['sender_store_id'];
                $data['transaction_type_id'] = $val['Challan']['transaction_type_id'];
                $data['status'] = $val['Challan']['status'];
                $data['receiver_store_id'] = $val['Challan']['receiver_store_id'];
                $data['received_date'] = $val['Challan']['received_date'];
                $data['created_at'] = $val['Challan']['created_at'];
                $data['created_by'] = $val['Challan']['created_by'];
                $data['updated_at'] = $val['Challan']['updated_at'];
                $data['updated_by'] = $val['Challan']['updated_by'];
                $data['inventory_status_id'] = $val['Challan']['inventory_status_id'];

                $challan_details_array = array();
                foreach ($val['ChallanDetail'] as $cd) {

                    $units = $this->search_array($cd['product_id'], 'id', $product_list);
                    if ($units['sales_measurement_unit_id'] == $units['challan_measurement_unit_id']) {

                        $quantity = $cd['challan_qty'];
                    } else {
                        $quantity = $this->convert_unit_to_unit($cd['product_id'], $units['challan_measurement_unit_id'], $units['sales_measurement_unit_id'], $cd['challan_qty']);
                    }

                    $details_data['id'] = $cd['id'];
                    $details_data['challan_id'] = $cd['challan_id'];
                    $details_data['product_id'] = $cd['product_id'];
                    $details_data['measurement_unit_id'] = $units['sales_measurement_unit_id'];
                    $details_data['challan_qty'] = $quantity;
                    $details_data['batch_no'] = $cd['batch_no'];
                    $details_data['expire_date'] = $cd['expire_date'];
                    $details_data['inventory_status_id'] = $cd['inventory_status_id'];
                    $details_data['remarks'] = $cd['remarks'];
                    $challan_details_array[] = $details_data;
                }

                $data['ChallanDetail'] = $challan_details_array;
                $challan_array[] = $data;
            }
        }

        $this->set(array(
            'challans' => $challan_array,
            '_serialize' => array('challans')
        ));
    }

    function search_array($value, $key, $array)
    {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return $array[$k];
            }
        }
        return null;
    }

    function get_challan_quantity($product_id, $batch_no, $expire_date, $array)
    {
        foreach ($array as $val) {
            if ($val['product_id'] === $product_id && (string) $val['batch'] == $batch_no && $expire_date == $val['expire_date']) {

                if (isset($val['quantity'])) {
                    return $val['quantity'];
                } else if (isset($val['received_qty'])) {
                    return $val['received_qty'];
                } else {
                    return 0;
                }
            }
        }
        return null;
    }

    public function challan_received()
    {
        // param 
        // user_id,challan_id,received_date,store_id
        // product_id,batch_no,exoire_date,quantity
        $this->loadModel('Product');
        $this->loadModel('Challan');
        $this->loadModel('CurrentInventory');
        $this->loadModel('ChallanDetail');

        $path = APP . 'logs/';
        $myfile = fopen($path . "challan_received.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $path = APP . 'logs/';
            $myfile = fopen($path . "challan_received_response.txt", "a") or die("Unable to open file!");
            fwrite($myfile, "\n" . $this->current_datetime() . ':' . json_encode($res));
            fclose($myfile);
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        $json_data = $json_data['challan_list'];



        if (!empty($json_data)) {
            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');

            $challan_index = 0;
            foreach ($json_data as $val) {
                $challan_id = $val['challan_id'];
                if ($challan_id == 21218 || $challan_id == 21181) {
                    $res['challan_id'][] = $challan_id;
                    //echo $challan_id.'----here';
                    continue;
                }
                $challans_check = $this->Challan->find('first', array(
                    'conditions' => array('Challan.id' => $challan_id, 'challan.status' => 2),
                    'recursive' => -1
                ));
                if ($challans_check) {
                    $res['challan_id'][] = $challans_check['Challan']['id'];
                    continue;
                }

                $challan_details = $this->ChallanDetail->find('all', array('conditions' => array('challan_id' => $val['challan_id']), 'recursive' => -1));

                $chalan['id'] = $val['challan_id'];
                $chalan['status'] = 2;
                $chalan['received_date'] = date('Y-m-d', strtotime($val['received_date']));
                $chalan['updated_by'] = $val['so_id'];
                $chalan['transaction_type_id'] = 5; //ASO TO SO(Product Issue Received)
                $this->Challan->save($chalan);  // update challan

                $insert_data_array = array();
                $update_data_array = array();
                $challan_details_array = array();
                $json_challan_details = $val['challan_details'];


                if (!empty($challan_details)) {
                    foreach ($challan_details as $dval) {
                        $units = $this->search_array($dval['ChallanDetail']['product_id'], 'id', $product_list);

                        // $dval['ChallanDetail']['received_qty'] = $this->get_challan_quantity($dval['ChallanDetail']['product_id'], (string) $dval['ChallanDetail']['batch_no'], $dval['ChallanDetail']['expire_date'], $json_challan_details);

                        $dval['ChallanDetail']['received_qty'] = $dval['ChallanDetail']['challan_qty'];

                        $details_array['id'] = $dval['ChallanDetail']['id'];
                        $details_array['received_qty'] = $dval['ChallanDetail']['received_qty'];
                        $challan_details_array[] = $details_array;

                        if ($units['sales_measurement_unit_id'] == $units['base_measurement_unit_id']) {
                            $quantity = $dval['ChallanDetail']['received_qty'];
                        } else {
                            $quantity = $this->unit_convert($dval['ChallanDetail']['product_id'], $units['sales_measurement_unit_id'], $dval['ChallanDetail']['received_qty']);
                        }

                        // ------------ stock update --------------------		
                        $inventory_info = $this->CurrentInventory->find('first', array(
                            'conditions' => array(
                                'CurrentInventory.store_id' => $val['store_id'],
                                'CurrentInventory.inventory_status_id' => 1,
                                'CurrentInventory.product_id' => $dval['ChallanDetail']['product_id'],
                                'CurrentInventory.batch_number' => $dval['ChallanDetail']['batch_no'],
                                'CurrentInventory.expire_date' => $dval['ChallanDetail']['expire_date']
                            ),
                            'recursive' => -1
                        ));

                        if (!empty($inventory_info)) {
                            $update_data = array();
                            $update_data['id'] = $inventory_info['CurrentInventory']['id'];
                            $update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $quantity;

                            $update_data['updated_at'] = $this->current_datetime();

                            $update_data['transaction_type_id'] = 5; // ASO to SO (Product Issue received)
                            $update_data['updated_at'] = $this->current_datetime();
                            $update_data['transaction_date'] = date('Y-m-d', strtotime($val['received_date']));
                            $update_data_array[] = $update_data;
                            // Update inventory data
                            $this->CurrentInventory->saveAll($update_data);
                            unset($update_data);
                        } else {
                            $insert_data['store_id'] = $val['store_id'];
                            $insert_data['inventory_status_id'] = 1;
                            $insert_data['product_id'] = $dval['ChallanDetail']['product_id'];
                            $insert_data['batch_number'] = $dval['ChallanDetail']['batch_no'];
                            $insert_data['expire_date'] = $dval['ChallanDetail']['expire_date'];
                            $insert_data['qty'] = $quantity;
                            $insert_data['updated_at'] = $this->current_datetime();
                            $insert_data['transaction_type_id'] = 5; //  ASO to SO (Product Issue received)
                            $insert_data['transaction_date'] = date('Y-m-d', strtotime($val['received_date']));
                            $insert_data_array[] = $insert_data;
                        }
                        // -------------------- end stock update ----------------------
                    }
                    // insert inventory data
                    $this->CurrentInventory->saveAll($insert_data_array);

                    $this->ChallanDetail->saveAll($challan_details_array);

                    // Update inventory data
                    // $this->CurrentInventory->saveAll($update_data_array);
                }
                $res['challan_id'][] = $challan_id;
                unset($chalan);
                unset($insert_data_array);
                unset($challan_details_array);
            }
            $res['status'] = 1;
            $res['message'] = 'Challan has been received successfuly.';
        } else {
            $res['status'] = 0;
            $res['message'] = 'No Challan Found !';
        }
        $path = APP . 'logs/';
        $myfile = fopen($path . "challan_received_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . json_encode($res));
        fclose($myfile);
        $this->set(array(
            'challan_received' => $res,
            '_serialize' => array('challan_received')
        ));
    }

    /* ------------------- End Challan ---------------------- */


    /* ------------------- Start Memo ---------------------- */

    public function create_memo()
    {

        //$this->user_status_check(149);

        $this->loadModel('Product');
        $this->loadModel('Memo');
        $this->loadModel('MemoDetail');
        $this->loadModel('Deposit');
        $this->loadModel('Collection');
        $this->loadModel('MemoSyncHistory');
        $this->loadModel('StoreBonusCard');
        $this->loadModel('SalesPerson');
        $this->loadModel('InstallmentNo');
        $this->loadModel('SoCreditCollection');

        $json_data = $this->request->input('json_decode', true);
        $json_data = $json_data['memo_list'];

        //pr($json_data);
        //exit;

        $path = APP . 'logs/';
        $myfile = fopen($path . "create_memo.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        /* $res['status'] = 0;
          $res['message'] = 'Memo not found.';
          $this->set(array(
          'memo' => $res,
          '_serialize' => array('memo')
          ));
          return true; */



        if (!empty($json_data)) {

            /*---------------------------- Mac check --------------------------------*/
            $mac_check = $this->mac_check($json_data['mac'], $json_data['sales_person_id']);
            if (!$mac_check) {

                $mac['status'] = 0;
                $mac['message'] = 'Mac Id Not Match';
                $res = $mac;
                $path = APP . 'logs/';
                $myfile = fopen($path . "create_memo_response.txt", "a") or die("Unable to open file!");
                fwrite($myfile, "\n" . $this->current_datetime() . ':' . json_encode($res));
                fclose($myfile);
                $this->set(array(
                    'mac' => $res,
                    '_serialize' => array('mac')
                ));
                return 0;
            }

            // if territory id is null then find territory id by salesperson id
            if (!$json_data['territory_id']) {
                $territory_id_retrive = $this->SalesPerson->find('first', array('conditions' => array('SalesPerson.id' => $json_data['sales_person_id']), 'recursive' => -1));
                $json_data['territory_id'] = $territory_id_retrive['SalesPerson']['territory_id']; //re-asign $json_data['territory_id'] if null
            }

            /* START ADD NEW */
            //get office id 
            $this->loadModel('Territory');
            $territory_info = $this->Territory->find(
                'first',
                array(
                    'conditions' => array('Territory.id' => $json_data['territory_id']),
                    'fields' => 'Territory.office_id',
                    'order' => array('Territory.id' => 'asc'),
                    'recursive' => -1,
                    //'limit' => 100
                )
            );
            $office_id = $territory_info['Territory']['office_id'];

            //get thana id 
            $this->loadModel('Market');
            $market_info = $this->Market->find(
                'first',
                array(
                    'conditions' => array('Market.id' => $json_data['market_id']),
                    'fields' => 'Market.thana_id',
                    'order' => array('Market.id' => 'asc'),
                    'recursive' => -1,
                    //'limit' => 100
                )
            );
            $thana_id = $market_info['Market']['thana_id'];
            /* END ADD NEW */

            //start memo setting
            $this->loadModel('MemoSetting');
            $MemoSettings = $this->MemoSetting->find(
                'all',
                array(
                    //'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
                    'order' => array('id' => 'asc'),
                    'recursive' => 0,
                    //'limit' => 100
                )
            );

            foreach ($MemoSettings as $s_result) {
                //echo $s_result['MemoSetting']['name'].'<br>';
                if ($s_result['MemoSetting']['name'] == 'stock_validation') {
                    $stock_validation = $s_result['MemoSetting']['value'];
                }
                if ($s_result['MemoSetting']['name'] == 'stock_hit') {
                    $stock_hit = $s_result['MemoSetting']['value'];
                }

                if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
                    $ec_calculation = $s_result['MemoSetting']['value'];
                }
                if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
                    $oc_calculation = $s_result['MemoSetting']['value'];
                }

                if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
                    $sales_calculation = $s_result['MemoSetting']['value'];
                }
                if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
                    $stamp_calculation = $s_result['MemoSetting']['value'];
                }
                //pr($MemoSetting);
            }
            //end memo setting


            /*
             * This condition added for data synchronization 
             * Cteated by imrul in 09, April 2017
             * Duplicate memo check
             */
            $count = $this->Memo->find('count', array(
                'conditions' => array(
                    'Memo.memo_no' => $json_data['memo_no']
                )
            ));


            $this->loadModel('Outlet');
            $outlet_info = $this->Outlet->find('first', array(
                'conditions' => array('Outlet.id' => $json_data['outlet_id']),
                'recursive' => -1
            ));

            $is_csa_outlet = $outlet_info['Outlet']['is_csa'];

            /* If territory_id null then find territory_id from SO ID Start */

            if ($json_data['territory_id'] == "") {
                $territory_info = $this->SalesPerson->find('first', array(
                    'fields' => array('SalesPerson.territory_id'),
                    'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
                    'recursive' => -1
                ));

                if (!empty($territory_info)) {
                    $json_data['territory_id'] = $territory_info['SalesPerson']['territory_id'];
                }
            }

            /* If territory_id null then find territory_id from SO ID End */


            //----------------------------------

            if (is_numeric($json_data['market_id']) || true) {

                if ($count == 0) {

                    $stock_check = $this->stock_check($json_data['store_id'], $json_data['memo_details']);
                    // echo $stock_check;exit;
                    if (!$stock_check) {
                        $res['status'] = 0;
                        $res['message'] = 'Stock not available';
                        $this->set(array(
                            'memo' => $res,
                            '_serialize' => array('memo')
                        ));
                        return 0;
                    }

                    $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                    $product_list = Set::extract($products, '{n}.Product');

                    $memo_data['memo_no'] = $json_data['memo_no'];
                    $memo_data['memo_date'] = date('Y-m-d', strtotime($json_data['memo_date']));
                    $memo_data['memo_time'] = $json_data['memo_date'];
                    $memo_data['sales_person_id'] = $json_data['sales_person_id'];
                    $memo_data['sales_to'] = 0;
                    $memo_data['outlet_id'] = (is_numeric($json_data['outlet_id']) ? $json_data['outlet_id'] : 0);
                    $memo_data['market_id'] = (is_numeric($json_data['market_id']) ? $json_data['market_id'] : 0);
                    //$memo_data['market_id'] = $json_data['market_id'];
                    $memo_data['territory_id'] = $json_data['territory_id'];
                    $memo_data['gross_value'] = $json_data['gross_value'];
                    $memo_data['cash_recieved'] = $json_data['cash_recieved'] > $json_data['gross_value'] ? $json_data['gross_value'] : $json_data['cash_recieved'];
                    $memo_data['credit_amount'] = $credit_amount = $json_data['gross_value'] - $memo_data['cash_recieved'];
                    $memo_data['latitude'] = $json_data['latitude'];
                    $memo_data['longitude'] = $json_data['longitude'];
                    $memo_data['status'] = ($credit_amount <= 0 ? 2 : 1);
                    $memo_data['from_app'] = $json_data['from_app'];
                    $memo_data['action'] = 0;

                    $memo_data['created_at'] = $this->current_datetime();
                    $memo_data['updated_at'] = $this->current_datetime();
                    $so_id_created_by = $this->get_user_id_from_so_id($json_data['sales_person_id']);
                    $memo_data['created_by'] = $so_id_created_by;
                    $memo_data['updated_by'] = $so_id_created_by;

                    $memo_data['office_id'] = $office_id ? $office_id : 0;
                    $memo_data['thana_id'] = $thana_id ? $thana_id : 0;
                    $memo_data['is_distributor'] = $json_data['isDistibutor'] ? $json_data['isDistibutor'] : 0;

                    $memo_data['total_discount'] = $json_data['total_discount'];

                    $this->Memo->save($memo_data);


                    // EC Calculation 
                    if ($ec_calculation && !$is_csa_outlet) {
                        $this->ec_calculation($memo_data['gross_value'], $memo_data['outlet_id'], $memo_data['territory_id'], $memo_data['memo_date'], 1);
                    }

                    // OC Calculation 
                    if ($oc_calculation && !$is_csa_outlet) {
                        $this->oc_calculation($memo_data['territory_id'], $memo_data['gross_value'], $memo_data['outlet_id'], $memo_data['memo_date'], $memo_data['memo_time'], 1);
                    }


                    // update visit plan
                    $this->update_visit_plan($memo_data);


                    // memo notifications
                    if ($json_data['gross_value'] >= 50000) {
                        $notification_data['memo_no'] = $json_data['memo_no'];
                        $notification_data['memo_date'] = $json_data['memo_date'];
                        $notification_data['sales_person_id'] = $json_data['sales_person_id'];
                        $notification_data['outlet_id'] = (is_numeric($json_data['outlet_id']) ? $json_data['outlet_id'] : 0);
                        $notification_data['memo_amount'] = $json_data['gross_value'];
                        $notification_data['created_at'] = $this->current_datetime();
                        $this->create_memo_nofification($notification_data);
                    } // end memo notifications
                    // $all_product_id=array_column($json_data['memo_details'],'product_id');
                    $all_product_id = array_map(function ($element) {
                        return $element['product_id'];
                    }, $json_data['memo_details']);

                    $memo_details_array = array();
                    foreach ($json_data['memo_details'] as $val) {

                        $memo_details_array = array();

                        /*$units = $this->search_array($val['bonus_product_id'], 'id', $product_list);
                        if ($units['sales_measurement_unit_id'] == $units['base_measurement_unit_id']) {
                            $bonus_quantity = $val['bonus_qty'];
                        } else {
                            $bonus_quantity = $this->unit_convert($val['product_id'], $units['sales_measurement_unit_id'], $val['bonus_qty']);
                        }

                        $punits = $this->search_array($val['product_id'], 'id', $product_list);
                        if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
                            $sale_quantity = ROUND($val['sales_qty']);
                        } else {
                            $sale_quantity = $this->unit_convert($val['product_id'], $punits['sales_measurement_unit_id'], $val['sales_qty']);
                        }*/

                        $units = $this->search_array($val['bonus_product_id'], 'id', $product_list);
                        if ($val['measurement_unit_id'] > 0) {
                            if ($val['measurement_unit_id'] == $units['base_measurement_unit_id']) {
                                $bonus_quantity = $val['bonus_qty'];
                            } else {
                                $bonus_quantity = $this->unit_convert($val['product_id'], $val['measurement_unit_id'], $val['bonus_qty']);
                            }
                        } else {
                            if ($units['sales_measurement_unit_id'] == $units['base_measurement_unit_id']) {
                                $bonus_quantity = $val['bonus_qty'];
                            } else {
                                $bonus_quantity = $this->unit_convert($val['product_id'], $units['sales_measurement_unit_id'], $val['bonus_qty']);
                            }
                        }

                        $punits = $this->search_array($val['product_id'], 'id', $product_list);
                        if ($val['measurement_unit_id'] > 0) {
                            if ($val['measurement_unit_id'] == $punits['base_measurement_unit_id']) {
                                $sale_quantity = ROUND($val['sales_qty']);
                            } else {
                                $sale_quantity = $this->unit_convert($val['product_id'], $val['measurement_unit_id'], $val['sales_qty']);
                            }
                        } else {
                            if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
                                $sale_quantity = ROUND($val['sales_qty']);
                            } else {
                                $sale_quantity = $this->unit_convert($val['product_id'], $punits['sales_measurement_unit_id'], $val['sales_qty']);
                            }
                        }


                        $product_price_id = 0;
                        /*if ($val['price'] > 0) {
                            $product_price_id = $this->get_product_price_id($val['product_id'], $val['price'], $all_product_id,date('Y-m-d', strtotime($json_data['memo_date'])),$json_data['isDistibutor']);
                        }*/

                        //$sale_quantity = $this->unit_convert($val['product_id'],$val['measurement_unit_id'],$val['sales_qty']);

                        $memo_details['memo_id'] = $this->Memo->id;
                        $memo_details['product_id'] = $val['product_id'];
                        $memo_details['product_type'] = $val['product_type'];

                        if ($val['measurement_unit_id'] > 0) {
                            $memo_details['measurement_unit_id'] = $val['measurement_unit_id'];
                        } else {
                            $p_units = $this->search_array($val['product_id'], 'id', $product_list);
                            $memo_details['measurement_unit_id'] = $p_units['sales_measurement_unit_id'];
                        }

                        //$memo_details['measurement_unit_id'] = $val['measurement_unit_id'];

                        $memo_details['sales_qty'] = $val['sales_qty'];
                        $memo_details['actual_price'] = $val['price'];
                        $memo_details['price'] = $val['price'] > 0 ? ($val['price'] - (isset($val['discount_amount']) ? $val['discount_amount'] : 0)) : 0;
                        // $memo_details['product_price_id'] = $product_price_id;
                        // $memo_details['vat'] = $val['vat'];
                        $memo_details['product_price_id'] = $val['price_id'];
                        $memo_details['product_combination_id'] = $val['combination_id'];
                        $memo_details['bonus_qty'] = $val['bonus_qty'];
                        $memo_details['bonus_product_id'] = $val['bonus_product_id'];
                        $memo_details['current_inventory_id'] = $val['current_inventory_id'];
                        $memo_details['bonus_inventory_id'] = $val['bonus_inventory_id'];
                        $memo_details['is_bonus'] = $val['is_bonus'];
                        $memo_date = date('Y-m-d', strtotime($json_data['memo_date']));

                        $memo_details['discount_type'] = $val['discount_type'];
                        $memo_details['discount_amount'] = $val['discount_amount'];

                        $memo_details['policy_type'] = $val['policy_type'];
                        $memo_details['policy_id'] = $val['policy_id'];

                        $selected_set = '';
                        $other_info = array();
                        if (isset($val['selected_set'])) {
                            $selected_set = $val['selected_set'];
                        }
                        if ($selected_set) {
                            $other_info = array(
                                'selected_set' => $selected_set
                            );
                        }
                        $provided_qty = '';
                        if (isset($val['provided_qty'])) {
                            $provided_qty = $val['provided_qty'];
                        }
                        if ($provided_qty) {
                            $other_info['provided_qty'] = $provided_qty;
                        }
                        if ($other_info)
                            $memo_details['other_info'] = json_encode($other_info);

                        //START NEW FOR BOUNUS
                        if (!$val['price'] > 0) {
                            $b_product_id = $val['product_id'];
                            //$memo_date = date('Y-m-d', strtotime($json_data['memo_date']));
                            $memo_products = $json_data['memo_details'];

                            $bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date, $memo_products);

                            $memo_details['bonus_id'] = $bonus_result['bonus_id'];
                            $memo_details['bonus_scheme_id'] = $bonus_result['bonus_scheme_id'];
                        }
                        //END FOR BOUNUS



                        // current product update
                        // transaction type id 11 = Memo Sale	
                        $this->update_current_inventory($sale_quantity, $val['product_id'], $json_data['store_id'], 'deduct', 11, $memo_date);

                        // insert data into MemoDetail
                        $memo_details_array[] = $memo_details;
                        $this->MemoDetail->saveAll($memo_details_array);

                        // sales calculation
                        $tt_price = $memo_details['sales_qty'] * $memo_details['price'];
                        if ($sales_calculation) {
                            if ($val['price'] > 0)
                                $this->sales_calculation($memo_details['product_id'], $memo_data['territory_id'], $sale_quantity, $tt_price, $memo_data['memo_date'], 1);
                        }

                        //stamp calculation
                        if ($stamp_calculation) {
                            $this->stamp_calculation($memo_data['memo_no'], $memo_data['territory_id'], $memo_details['product_id'], $memo_data['outlet_id'], $memo_details['sales_qty'], $memo_data['memo_date'], 1, $tt_price, $memo_data['market_id']);
                        }

                        //bonus product update
                        //$this->update_current_inventory($val['bonus_inventory_id'],$bonus_quantity,$data);
                    }
                    /*---------- Collection json : start-------------*/
                    if ($json_data['payment_list']) {
                        $res['payments'] = $this->create_payment($json_data['payment_list']);
                    } else {
                        $res['payments'] = array('status' => 0, 'message' => 'No Data received');
                    }
                    /*---------- Collection json : END-------------*/

                    /*--------------- So Credit memo track: Start ----------*/
                    if ($json_data['credit_collection']) {
                        $res['credit_collection'] = $this->update_credit_collection($json_data['credit_collection']);
                    } else {
                        $res['credit_collection'] = array('status' => 0, 'message' => 'No Data received');
                    }
                    /*--------------- So Credit memo track: END ----------*/

                    /*------------------- All instrumnet no : Start----------*/

                    if ($json_data['intrument_value']) {
                        $res['intrument_value'] = $this->create_installment_no($json_data['intrument_value']);
                    } else {
                        $res['intrument_value'] = array('status' => 0, 'message' => 'No Data received');
                    }
                    /*------------------- All instrumnet no : END ----------*/

                    /*--------------- Deposit : STart-------------------*/
                    // $res['deposit_value']=array('status'=>0,'message'=>'No Data received');
                    //if($json_data['sales_person_id']!=147){
                    if ($json_data['deposit_value']) {
                        $res['deposit_value'] = $this->create_deposit($json_data['deposit_value']);
                    } else {
                        $res['deposit_value'] = array('status' => 0, 'message' => 'No Data received');
                    }
                    //}
                    /*--------------- Deposit : end-------------------*/

                    // insert MemoDetail data 
                    // below line is commented as we will insert data for each product 
                    //$this->MemoDetail->saveAll($memo_details_array);

                    $res['status'] = 1;
                    $res['memo_no'] = $json_data['memo_no'];
                    $res['message'] = 'Memo has been created successfuly.';


                    //start for memoSync 
                    $sync_date = date('Y-m-d', strtotime($json_data['memo_date']));
                    // $sync_date = date('Y-m-d');
                    $memo_sync_info = $this->MemoSyncHistory->find('first', array(
                        'conditions' => array(
                            'MemoSyncHistory.so_id' => $json_data['sales_person_id'],
                            'MemoSyncHistory.date >=' => $sync_date
                        ),
                        //'fields' => array('sum(MemoSyncHistory.total_memo) as total_memo'),
                        'order' => array('date desc', 'id desc'),
                        'recursive' => -1
                    ));
                    $memo_sync_id = $memo_sync_info['MemoSyncHistory']['id'];
                    $this->MemoSyncHistory->id = $memo_sync_id;
                    $this->MemoSyncHistory->updateAll(array(
                        'MemoSyncHistory.missed_memo' => 'MemoSyncHistory.missed_memo - 1'
                    ), array('MemoSyncHistory.id' => $memo_sync_id));
                    //end for memoSync
                } else {


                    $memo_id_arr = $this->Memo->find('first', array(
                        'conditions' => array(
                            'Memo.memo_no' => $json_data['memo_no']
                        )
                    ));


                    $memo_id = $memo_id_arr['Memo']['id'];
                    $stock_check = $this->stock_check($json_data['store_id'], $json_data['memo_details'], $memo_id);
                    // echo $stock_check;exit;
                    if (!$stock_check) {
                        $res['status'] = 0;
                        $res['message'] = 'Stock not available';
                        $this->set(array(
                            'memo' => $res,
                            '_serialize' => array('memo')
                        ));
                        return 0;
                    }
                    $this->MemoDetail->deleteAll(array('MemoDetail.memo_id' => $memo_id));
                    $this->Deposit->deleteAll(array('Deposit.memo_id' => $memo_id));
                    $this->Collection->deleteAll(array('Collection.memo_id' => $memo_id));
                    $this->InstallmentNo->deleteAll(array('InstallmentNo.memo_no' => $json_data['memo_no']), false);
                    $this->StoreBonusCard->deleteAll(array('StoreBonusCard.memo_no' => $json_data['memo_no']));
                    $this->SoCreditCollection->deleteAll(array('SoCreditCollection.memo_no' => $json_data['memo_no']), false);

                    $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                    $product_list = Set::extract($products, '{n}.Product');

                    $store_id = $json_data['store_id'];

                    for ($memo_detail_count = 0; $memo_detail_count < count($memo_id_arr['MemoDetail']); $memo_detail_count++) {
                        $product_id = $memo_id_arr['MemoDetail'][$memo_detail_count]['product_id'];
                        $sales_qty = $memo_id_arr['MemoDetail'][$memo_detail_count]['sales_qty'];
                        $sales_price = $memo_id_arr['MemoDetail'][$memo_detail_count]['price'];
                        $memo_territory_id = $memo_id_arr['Memo']['territory_id'];
                        $memo_no = $memo_id_arr['Memo']['memo_no'];
                        $memo_date = $memo_id_arr['Memo']['memo_date'];
                        $outlet_id = $memo_id_arr['Memo']['outlet_id'];
                        $market_id = $memo_id_arr['Memo']['market_id'];

                        /*$punits_pre = $this->search_array($product_id, 'id', $product_list);
                        if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                             $base_quantity = ROUND($sales_qty);
                        } else {
                            $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                        }*/

                        $measurement_unit_id = $memo_id_arr['MemoDetail'][$memo_detail_count]['measurement_unit_id'];
                        $punits_pre = $this->search_array($product_id, 'id', $product_list);
                        if ($measurement_unit_id > 0) {
                            if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                                $base_quantity = ROUND($sales_qty);
                            } else {
                                $base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);
                            }
                        } else {
                            if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                $base_quantity = ROUND($sales_qty);
                            } else {
                                $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                            }
                        }


                        $update_type = 'add';
                        $memo_date = date('Y-m-d', strtotime($json_data['memo_date']));
                        $this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 12, $memo_date);



                        // subract sales achievement and stamp achievemt 
                        // sales calculation
                        $t_price = $sales_qty * $sales_price;
                        $this->sales_calculation($product_id, $memo_territory_id, $base_quantity, $t_price, $memo_date, 2);

                        //stamp calculation
                        /* $this->stamp_calculation($memo_no, $memo_territory_id, $product_id, $outlet_id, $sales_qty, $memo_date, 2, $t_price, $market_id);
                         */
                    }



                    /* -----------------Same as Create Memo but only id included for updating memo------------------ */

                    $memo_data['memo_no'] = $json_data['memo_no'];
                    $memo_data['memo_date'] = date('Y-m-d', strtotime($json_data['memo_date']));
                    $memo_data['memo_time'] = $json_data['memo_date'];
                    $memo_data['sales_person_id'] = $json_data['sales_person_id'];
                    $memo_data['sales_to'] = 0;
                    $memo_data['outlet_id'] = (is_numeric($json_data['outlet_id']) ? $json_data['outlet_id'] : 0);
                    $memo_data['market_id'] = (is_numeric($json_data['market_id']) ? $json_data['market_id'] : 0);
                    //$memo_data['market_id'] = $json_data['market_id'];
                    $memo_data['territory_id'] = $json_data['territory_id'];
                    $memo_data['gross_value'] = $json_data['gross_value'];
                    $memo_data['cash_recieved'] = $json_data['cash_recieved'] > $json_data['gross_value'] ? $json_data['gross_value'] : $json_data['cash_recieved'];
                    $memo_data['credit_amount'] = $credit_amount = $json_data['gross_value'] - $memo_data['cash_recieved'];
                    $memo_data['latitude'] = $json_data['latitude'];
                    $memo_data['longitude'] = $json_data['longitude'];
                    $memo_data['status'] = ($credit_amount <= 0 ? 2 : 1);
                    $memo_data['id'] = $memo_id;

                    //$memo_data['created_at'] = $this->current_datetime();
                    $memo_data['updated_at'] = $this->current_datetime();
                    // $memo_data['created_by'] = $this->get_user_id_from_so_id($json_data['sales_person_id']);
                    $memo_data['updated_by'] = $this->get_user_id_from_so_id($json_data['sales_person_id']);

                    $memo_data['office_id'] = $office_id ? $office_id : 0;
                    $memo_data['thana_id'] = $thana_id ? $thana_id : 0;
                    $memo_data['is_distributor'] = $json_data['isDistibutor'] ? $json_data['isDistibutor'] : 0;

                    $memo_data['total_discount'] = $json_data['total_discount'];
                    $this->Memo->save($memo_data);


                    // update visit plan
                    $this->update_visit_plan($memo_data);


                    // memo notifications
                    if ($json_data['gross_value'] >= 50000) {
                        $notification_data['memo_no'] = $json_data['memo_no'];
                        $notification_data['memo_date'] = $json_data['memo_date'];
                        $notification_data['sales_person_id'] = $json_data['sales_person_id'];
                        $notification_data['outlet_id'] = (is_numeric($json_data['outlet_id']) ? $json_data['outlet_id'] : 0);
                        $notification_data['memo_amount'] = $json_data['gross_value'];
                        $notification_data['created_at'] = $this->current_datetime();
                        $this->create_memo_nofification($notification_data);
                    }
                    // end memo notifications


                    $memo_details_array = array();

                    //pr($json_data['memo_details']);
                    //exit;
                    // $all_product_id=array_column($json_data['memo_details'],'product_id');
                    $all_product_id = array_map(function ($element) {
                        return $element['product_id'];
                    }, $json_data['memo_details']);
                    foreach ($json_data['memo_details'] as $val) {

                        $memo_details_array = array();
                        /*$units = $this->search_array($val['bonus_product_id'], 'id', $product_list);
                        if ($units['sales_measurement_unit_id'] == $units['base_measurement_unit_id']) {
                            $bonus_quantity = $val['bonus_qty'];
                        } else {
                            $bonus_quantity = $this->unit_convert($val['product_id'], $units['sales_measurement_unit_id'], $val['bonus_qty']);
                        }

                        $punits = $this->search_array($val['product_id'], 'id', $product_list);
                        if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
                            $sale_quantity = ROUND($val['sales_qty']);
                        } else {
                            $sale_quantity = $this->unit_convert($val['product_id'], $punits['sales_measurement_unit_id'], $val['sales_qty']);
                        }*/


                        $units = $this->search_array($val['bonus_product_id'], 'id', $product_list);
                        if ($val['measurement_unit_id'] > 0) {
                            if ($val['measurement_unit_id'] == $units['base_measurement_unit_id']) {
                                $bonus_quantity = $val['bonus_qty'];
                            } else {
                                $bonus_quantity = $this->unit_convert($val['product_id'], $val['measurement_unit_id'], $val['bonus_qty']);
                            }
                        } else {
                            if ($units['sales_measurement_unit_id'] == $units['base_measurement_unit_id']) {
                                $bonus_quantity = $val['bonus_qty'];
                            } else {
                                $bonus_quantity = $this->unit_convert($val['product_id'], $units['sales_measurement_unit_id'], $val['bonus_qty']);
                            }
                        }

                        $punits = $this->search_array($val['product_id'], 'id', $product_list);
                        if ($val['measurement_unit_id'] > 0) {
                            if ($val['measurement_unit_id'] == $punits['base_measurement_unit_id']) {
                                $sale_quantity = ROUND($val['sales_qty']);
                            } else {
                                $sale_quantity = $this->unit_convert($val['product_id'], $val['measurement_unit_id'], $val['sales_qty']);
                            }
                        } else {
                            if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
                                $sale_quantity = ROUND($val['sales_qty']);
                            } else {
                                $sale_quantity = $this->unit_convert($val['product_id'], $punits['sales_measurement_unit_id'], $val['sales_qty']);
                            }
                        }


                        /*$product_price_id = 0;
                        if ($val['price'] > 0) {
                            $product_price_id = $this->get_product_price_id($val['product_id'], $val['price'], $all_product_id,date('Y-m-d', strtotime($json_data['memo_date'])),$json_data['isDistibutor']);
                           
                        }*/

                        $memo_details['memo_id'] = $this->Memo->id;
                        $memo_details['product_id'] = $val['product_id'];
                        $memo_details['product_type'] = $val['product_type'];

                        if ($val['measurement_unit_id'] > 0) {
                            $memo_details['measurement_unit_id'] = $val['measurement_unit_id'];
                        } else {
                            $p_units = $this->search_array($val['product_id'], 'id', $product_list);
                            $memo_details['measurement_unit_id'] = $p_units['sales_measurement_unit_id'];
                        }
                        //$memo_details['measurement_unit_id'] = $val['measurement_unit_id'];

                        $memo_details['sales_qty'] = $val['sales_qty'];
                        $memo_details['actual_price'] = $val['price'];
                        $memo_details['price'] = $val['price'] > 0 ? ($val['price'] - (isset($val['discount_amount']) ? $val['discount_amount'] : 0)) : 0;
                        // $memo_details['vat'] = $val['vat'];
                        $memo_details['product_price_id'] = $val['price_id'];
                        $memo_details['product_combination_id'] = $val['combination_id'];
                        $memo_details['bonus_qty'] = $val['bonus_qty'];
                        $memo_details['bonus_product_id'] = $val['bonus_product_id'];
                        $memo_details['current_inventory_id'] = $val['current_inventory_id'];
                        $memo_details['bonus_inventory_id'] = $val['bonus_inventory_id'];
                        $memo_details['is_bonus'] = $val['is_bonus'];
                        $memo_date = date('Y-m-d', strtotime($json_data['memo_date']));

                        $memo_details['discount_type'] = $val['discount_type'];
                        $memo_details['discount_amount'] = $val['discount_amount'];

                        $memo_details['policy_type'] = $val['policy_type'];
                        $memo_details['policy_id'] = $val['policy_id'];

                        $selected_set = '';
                        $other_info = array();
                        if (isset($val['selected_set'])) {
                            $selected_set = $val['selected_set'];
                        }
                        if ($selected_set) {
                            $other_info = array(
                                'selected_set' => $selected_set
                            );
                        }

                        $provided_qty = '';
                        if (isset($val['provided_qty'])) {
                            $provided_qty = $val['provided_qty'];
                        }
                        if ($provided_qty) {
                            $other_info['provided_qty'] = $provided_qty;
                        }
                        if ($other_info)
                            $memo_details['other_info'] = json_encode($other_info);


                        //START NEW FOR BOUNUS
                        if (!$val['price'] > 0) {
                            $b_product_id = $val['product_id'];
                            //$memo_date = date('Y-m-d', strtotime($json_data['memo_date']));
                            $memo_products = $json_data['memo_details'];

                            $bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date, $memo_products);

                            $memo_details['bonus_id'] = $bonus_result['bonus_id'];
                            $memo_details['bonus_scheme_id'] = $bonus_result['bonus_scheme_id'];
                        }
                        //END FOR BOUNUS






                        // current product update
                        $this->update_current_inventory($sale_quantity, $val['product_id'], $json_data['store_id'], 'deduct', 11, $memo_date);

                        // insert into MemoDetail
                        $memo_details_array[] = $memo_details;
                        $this->MemoDetail->saveAll($memo_details_array);

                        // sales calculation
                        $tt_price = $memo_details['sales_qty'] * $memo_details['price'];
                        $this->sales_calculation($memo_details['product_id'], $memo_data['territory_id'], $sale_quantity, $tt_price, $memo_data['memo_date'], 1);

                        //stamp calculation
                        $this->stamp_calculation($memo_data['memo_no'], $memo_data['territory_id'], $memo_details['product_id'], $memo_data['outlet_id'], $memo_details['sales_qty'], $memo_data['memo_date'], 1, $tt_price, $memo_data['market_id']);

                        //bonus product update
                        //$this->update_current_inventory($val['bonus_inventory_id'],$bonus_quantity,$data);
                    }
                    /*---------- Collection json : start-------------*/
                    if ($json_data['payment_list']) {
                        $res['payments'] = $this->create_payment($json_data['payment_list']);
                    } else {
                        $res['payments'] = array('status' => 0, 'message' => 'No Data received');
                    }
                    /*---------- Collection json : END-------------*/

                    /*--------------- So Credit memo track: Start ----------*/
                    if ($json_data['credit_collection']) {
                        $res['credit_collection'] = $this->update_credit_collection($json_data['credit_collection']);
                    } else {
                        $res['credit_collection'] = array('status' => 0, 'message' => 'No Data received');
                    }
                    /*--------------- So Credit memo track: END ----------*/

                    /*------------------- All instrumnet no : Start----------*/

                    if ($json_data['intrument_value']) {
                        $res['intrument_value'] = $this->create_installment_no($json_data['intrument_value']);
                    } else {
                        $res['intrument_value'] = array('status' => 0, 'message' => 'No Data received');
                    }
                    /*------------------- All instrumnet no : END ----------*/

                    /*--------------- Deposit : STart-------------------*/
                    // $res['deposit_value']=array('status'=>0,'message'=>'No Data received');

                    if ($json_data['deposit_value']) {
                        $res['deposit_value'] = $this->create_deposit($json_data['deposit_value']);
                    } else {
                        $res['deposit_value'] = array('status' => 0, 'message' => 'No Data received');
                    }

                    /*--------------- Deposit : end-------------------*/

                    // insert MemoDetail data
                    // Below line is commented as we will insert each product 
                    //$this->MemoDetail->saveAll($memo_details_array);


                    $res['status'] = 1;
                    $res['memo_no'] = $json_data['memo_no'];
                    $res['message'] = 'Memo has been updated successfuly.';
                }
            } else {
                $res['status'] = 0;
                $res['message'] = 'Market ID not valid.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'Memo not found.';
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "create_memo_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . json_encode($res));
        fclose($myfile);

        $this->set(array(
            'memo' => $res,
            '_serialize' => array('memo')
        ));
    }

    /*-------------------------------------------------------------------------------------------------
        parameters details 
        store id: store id 
        memo_details : send full memo details for checking is product qty available on stock
        description : checking quantity for memo product is that qty avalaible on stock
    ---------------------------------------------------------------------------------------------------*/
    function stock_check($store_id, $memo_details, $memo_id = 0)
    {
        $result = array();
        foreach ($memo_details as $element) {
            //pr($element)      
            if ($element['measurement_unit_id'] > 0) {
                $sales_qty = $element['sales_qty_in_dispencer'];
            } else {
                $sales_qty = $element['sales_qty'];
            }
            $result[$element['product_id']] = (isset($result[$element['product_id']]) ? $result[$element['product_id']] : 0) + $sales_qty;
        }
        $this->loadModel('CurrentInventory');
        $this->loadModel('MemoDetail');
        $this->loadModel('ProductMeasurement');
        $this->loadModel('Product');

        if ($memo_id) {
            $prev_data = $this->MemoDetail->find('all', array(
                'conditions' => array(
                    'MemoDetail.memo_id' => $memo_id
                ),
                'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'MemoDetail.product_id', 'MemoDetail.measurement_unit_id'),
                'group' => array('MemoDetail.product_id, MemoDetail.measurement_unit_id'),
                'recursive' => -1
            ));
            $prev_memo_detail = array();
            foreach ($prev_data as $data) {
                if ($data['MemoDetail']['measurement_unit_id']) {
                    $product_info = $this->Product->find('first', array(
                        'conditions' => array(
                            'Product.id' => $data['MemoDetail']['product_id'],
                        ),
                        'joins' => array(
                            array(
                                'table' => 'product_measurements',
                                'alias' => 'ProductMeasurement',
                                'conditions' => 'ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id AND Product.id=ProductMeasurement.product_id'
                            )
                        ),
                        'fields' => array('ProductMeasurement.qty_in_base'),
                        'recursive' => -1
                    ));
                    //pr($product_info);

                    $pro_measurement_info = $this->ProductMeasurement->find('first', array(
                        'conditions' => array(
                            'ProductMeasurement.product_id' => $data['MemoDetail']['product_id'],
                            'ProductMeasurement.measurement_unit_id' => $data['MemoDetail']['measurement_unit_id']
                        ),
                        'fields' => array('ProductMeasurement.qty_in_base'),
                        'recursive' => -1
                    ));

                    if ($pro_measurement_info && $pro_measurement_info['ProductMeasurement']['qty_in_base'] && $product_info) {
                        $prev_qty = isset($prev_memo_detail[$data['MemoDetail']['product_id']]) ? $prev_memo_detail[$data['MemoDetail']['product_id']] : 0;
                        $prev_memo_detail[$data['MemoDetail']['product_id']] = $prev_qty + ($data['0']['sales_qty'] * $pro_measurement_info['ProductMeasurement']['qty_in_base']) / $product_info['ProductMeasurement']['qty_in_base'];
                    } else {
                        $prev_qty = isset($prev_memo_detail[$data['MemoDetail']['product_id']]) ? $prev_memo_detail[$data['MemoDetail']['product_id']] : 0;
                        $prev_memo_detail[$data['MemoDetail']['product_id']] = $prev_qty + $data['0']['sales_qty'];
                    }
                } else {
                    $prev_qty = isset($prev_memo_detail[$data['MemoDetail']['product_id']]) ? $prev_memo_detail[$data['MemoDetail']['product_id']] : 0;
                    $prev_memo_detail[$data['MemoDetail']['product_id']] = $prev_qty + $data['0']['sales_qty'];
                }
            }

            foreach ($result as $product_id => $qty) {
                if (isset($prev_memo_detail[$product_id])) {
                    $current_inventory = $this->CurrentInventory->find('all', array(
                        'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id),
                        'joins' => array(
                            array(
                                'table' => 'product_measurements',
                                'alias' => 'ProductMeasurement',
                                'type' => 'LEFT',
                                'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                            )
                        ),
                        'group' => array('CurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'CurrentInventory.product_id HAVING (sum(CurrentInventory.qty) + ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $prev_memo_detail[$product_id] . ',0))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
                        'fields' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'sum(CurrentInventory.qty) as qty')
                    ));
                    if (!$current_inventory) {
                        return false;
                    }
                } else {
                    $current_inventory = $this->CurrentInventory->find('all', array(
                        'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id),
                        'joins' => array(
                            array(
                                'table' => 'product_measurements',
                                'alias' => 'ProductMeasurement',
                                'type' => 'LEFT',
                                'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                            )
                        ),
                        'group' => array('CurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'CurrentInventory.product_id HAVING (sum(CurrentInventory.qty))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
                        'fields' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'sum(CurrentInventory.qty) as qty')
                    ));
                    if (!$current_inventory) {
                        return false;
                    }
                }
            }
        } else {
            foreach ($result as $product_id => $qty) {
                $current_inventory = $this->CurrentInventory->find('all', array(
                    'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id),
                    'joins' => array(
                        array(
                            'table' => 'product_measurements',
                            'alias' => 'ProductMeasurement',
                            'type' => 'left',
                            'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                        )
                    ),
                    'group' => array('CurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
                    'fields' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'sum(CurrentInventory.qty) as qty')
                ));

                if (!$current_inventory) {
                    return false;
                }
            }
        }
        return true;
    }

    /*-------------------------------------------------------------------------------------------------
        parameters details 
        store id: store id 
        details :  product wise giving/sales qty, like details[product_id]=qty.
        prev_data :  product wise previous giving/sales qty, like details[product_id]=qty.
        description : checking quantity for session/doctor visist/gift item product is that qty avalaible on stock
    ---------------------------------------------------------------------------------------------------*/
    function stock_check_for_validation_for_other($store_id, $details, $prev_data = array())
    {
        //pr($details);
        //pr($prev_data);exit;
        $this->loadModel('CurrentInventory');
        if ($prev_data) {
            foreach ($details as $product_id => $qty) {
                if (isset($prev_data[$product_id])) {
                    $current_inventory = $this->CurrentInventory->find('all', array(
                        'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id),
                        'joins' => array(
                            array(
                                'table' => 'product_measurements',
                                'alias' => 'ProductMeasurement',
                                'type' => 'LEFT',
                                'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                            )
                        ),
                        'group' => array('CurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'CurrentInventory.product_id HAVING (sum(CurrentInventory.qty) + ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $prev_data[$product_id] . ',0))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
                        'fields' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'sum(CurrentInventory.qty) as qty')
                    ));
                    if (!$current_inventory) {
                        return false;
                    }
                } else {
                    $current_inventory = $this->CurrentInventory->find('all', array(
                        'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id),
                        'joins' => array(
                            array(
                                'table' => 'product_measurements',
                                'alias' => 'ProductMeasurement',
                                'type' => 'LEFT',
                                'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                            )
                        ),
                        'group' => array('CurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'CurrentInventory.product_id HAVING (sum(CurrentInventory.qty))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
                        'fields' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'sum(CurrentInventory.qty) as qty')
                    ));
                    if (!$current_inventory) {
                        return false;
                    }
                }
            }
        } else {
            foreach ($details as $product_id => $qty) {
                $current_inventory = $this->CurrentInventory->find('all', array(
                    'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id),
                    'joins' => array(
                        array(
                            'table' => 'product_measurements',
                            'alias' => 'ProductMeasurement',
                            'type' => 'left',
                            'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                        )
                    ),
                    'group' => array('CurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
                    'fields' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'sum(CurrentInventory.qty) as qty')
                ));

                if (!$current_inventory) {
                    return false;
                }
            }
        }
        return true;
    }
    //for bonus and bouns schema
    function bouns_and_scheme_id_set($b_product_id = 0, $memo_date = '', $memo_products = array())
    {
        $this->loadModel('Bonus');
        $this->loadModel('OpenCombination');
        $this->loadModel('OpenCombinationProduct');

        $bonus_result = array();

        $b_product_qty = 0;
        $bonus_id = 0;
        $bonus_scheme_id = 0;

        $bonus_info = $this->Bonus->find(
            'first',
            array(
                'conditions' => array(
                    'Bonus.effective_date <= ' => $memo_date,
                    'Bonus.end_date >= ' => $memo_date,
                    'Bonus.bonus_product_id' => $b_product_id
                ),
                'recursive' => -1,
            )
        );

        //pr($bonus_info);

        if ($bonus_info) {
            $bonus_table_id = $bonus_info['Bonus']['id'];
            $mother_product_id = $bonus_info['Bonus']['mother_product_id'];
            $mother_product_quantity = $bonus_info['Bonus']['mother_product_quantity'];


            foreach ($memo_products as $memo_product) {
                if ($memo_product['product_id'] == $b_product_id && $memo_product['price'] > 0) {
                    //pr($memo_product);
                    $mother_product_sales_quantity = $memo_product['sales_qty'];
                }

                if ($memo_product['product_id'] == $b_product_id && !$memo_product['price'] > 0) {
                    //pr($memo_product);
                    $b_product_qty += $memo_product['sales_qty'];
                }
            }



            //echo $mother_product_sales_quantity;								

            $org_bonus_qty = intval($mother_product_sales_quantity / $mother_product_quantity);

            //echo $org_bonus_qty;
            //echo '<br>';

            if ($org_bonus_qty < $b_product_qty) {
                $bonus_com_info = $this->OpenCombination->find(
                    'first',
                    array(
                        'joins' => array(
                            array(
                                'alias' => 'OpenCombinationProduct',
                                'table' => 'open_combination_products',
                                'type' => 'INNER',
                                'conditions' => 'OpenCombination.id = OpenCombinationProduct.combination_id'
                            )
                        ),
                        'conditions' => array(
                            'OpenCombination.start_date <= ' => $memo_date,
                            'OpenCombination.end_date >= ' => $memo_date,
                            'OpenCombination.is_bonus' => 1,
                            'OpenCombinationProduct.product_id' => $b_product_id
                        ),
                        'fields' => array('OpenCombination.id', 'OpenCombinationProduct.product_id'),
                        'recursive' => -1,
                    )
                );

                if ($bonus_com_info) {
                    $bonus_scheme_id = $bonus_com_info['OpenCombination']['id'];
                }
            } else {
                $bonus_id = $bonus_table_id;
            }

            //echo $bonus_id;
            //break;
        } else {
            $bonus_com_info = $this->OpenCombination->find(
                'first',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'OpenCombinationProduct',
                            'table' => 'open_combination_products',
                            'type' => 'INNER',
                            'conditions' => 'OpenCombination.id = OpenCombinationProduct.combination_id'
                        )
                    ),
                    'conditions' => array(
                        'OpenCombination.start_date <= ' => $memo_date,
                        'OpenCombination.end_date >= ' => $memo_date,
                        'OpenCombination.is_bonus' => 1,
                        'OpenCombinationProduct.product_id' => $b_product_id
                    ),
                    'fields' => array('OpenCombination.id', 'OpenCombinationProduct.product_id'),
                    'recursive' => -1,
                )
            );

            if ($bonus_com_info) {
                $bonus_scheme_id = $bonus_com_info['OpenCombination']['id'];
            }

            //echo $bonus_scheme_id;
            //pr($bonus_com_info);
        }

        //echo (16 % 12);

        /* echo 'Bonus = '.$bonus_id;
          echo '<br>';
          echo 'Bonus Scheme = '. $bonus_scheme_id;
          echo '<br>';
          echo '<br>';
          echo '<br>'; */

        $bonus_result['bonus_id'] = $bonus_id;
        $bonus_result['bonus_scheme_id'] = $bonus_scheme_id;


        return $bonus_result;
    }

    /*
     * create_total_memo
     * @return json 
     */

    function create_total_memo()
    {
        $this->loadModel('MemoSyncHistory');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        if (isset($json_data['so_id']) && isset($json_data['date']) && isset($json_data['total_memo'])) {
            $data['so_id'] = $json_data['so_id'];
            $data['date'] = $json_data['date'];
            $data['total_memo'] = $json_data['total_memo'];
            $data['missed_memo'] = $json_data['total_memo'];
            $data['datetime'] = $this->current_datetime();
            //$data['datetime'] = date('Y-m-d H:i:s');
            $this->MemoSyncHistory->create();
            $this->MemoSyncHistory->save($data);
        }

        $data_array['status'] = 1;
        $this->set(array(
            'total_memo' => $data_array,
            '_serialize' => array('total_memo')
        ));
    }

    // end create_total_memo


    /* ---------------------- Delete Memo by Memo no ------------------------------------ */

    public function delete_memo()
    {
        $this->loadModel('Product');
        $this->loadModel('Memo');
        $this->loadModel('MemoDetail');
        $this->loadModel('Deposit');
        $this->loadModel('Collection');
        $this->loadModel('StoreBonusCard');
        $this->loadModel('GiftItem');
        $this->loadModel('StoreBonusCard');

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $path = APP . 'logs/';
            $myfile = fopen($path . "delete_memo_response.txt", "a") or die("Unable to open file!");
            fwrite($myfile, "\n" . $this->current_datetime() . ':' . json_encode($res));
            fclose($myfile);
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "delete_memo.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        if (!empty($json_data)) {

            //start memo setting
            $this->loadModel('MemoSetting');
            $MemoSettings = $this->MemoSetting->find(
                'all',
                array(
                    //'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
                    'order' => array('id' => 'asc'),
                    'recursive' => 0,
                    //'limit' => 100
                )
            );

            foreach ($MemoSettings as $s_result) {
                //echo $s_result['MemoSetting']['name'].'<br>';
                if ($s_result['MemoSetting']['name'] == 'stock_validation') {
                    $stock_validation = $s_result['MemoSetting']['value'];
                }
                if ($s_result['MemoSetting']['name'] == 'stock_hit') {
                    $stock_hit = $s_result['MemoSetting']['value'];
                }

                if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
                    $ec_calculation = $s_result['MemoSetting']['value'];
                }
                if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
                    $oc_calculation = $s_result['MemoSetting']['value'];
                }

                if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
                    $sales_calculation = $s_result['MemoSetting']['value'];
                }
                if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
                    $stamp_calculation = $s_result['MemoSetting']['value'];
                }
                //pr($MemoSetting);
            }
            //end memo setting

            /*
             * This condition added for data synchronization 
             * Cteated by imrul in 09, April 2017
             * Duplicate memo check
             */


            foreach ($json_data['delete_memos'] as $val) {
                $count = $this->Memo->find('count', array(
                    'conditions' => array(
                        'Memo.memo_no' => $val['memo_no']
                    )
                ));


                if ($val['memo_no']) {

                    $memo_id_arr = $this->Memo->find('first', array(
                        'conditions' => array(
                            'Memo.memo_no' => $val['memo_no']
                        )
                    ));
                    $memo_id = $memo_id_arr['Memo']['id'];
                    $this->MemoDetail->deleteAll(array('MemoDetail.memo_id' => $memo_id));
                    $this->Deposit->deleteAll(array('Deposit.memo_id' => $memo_id));
                    $this->Collection->deleteAll(array('Collection.memo_id' => $memo_id));
                    $this->StoreBonusCard->deleteAll(array('StoreBonusCard.memo_no' => $val['memo_no']));
                    /* pr($memo_id_arr);
                      exit; */

                    //add new
                    $this->LoadModel('SoCreditCollection');
                    $this->SoCreditCollection->deleteAll(array('SoCreditCollection.memo_no' => $val['memo_no']));
                    //end add new
                    $this->LoadModel('InstallmentNo');
                    $this->InstallmentNo->deleteAll(array('InstallmentNo.memo_no' => $val['memo_no']), false);             //

                    /*--------------------- Gift Item Delete : Start ---------------*/
                    $this->GiftItem->query("DELETE FROM gift_item_details where gift_item_id = (SELECT id FROM gift_items WHERE memo_no='" . $val['memo_no'] . "')");
                    $this->GiftItem->deleteAll(array('GiftItem.memo_no' => $val['memo_no']));
                    /*--------------------- Gift Item Delete : END ---------------*/

                    $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                    $product_list = Set::extract($products, '{n}.Product');




                    // EC Calculation 
                    if ($ec_calculation) {
                        @$this->ec_calculation($memo_id_arr['Memo']['gross_value'], $memo_id_arr['Memo']['outlet_id'], $memo_id_arr['Memo']['territory_id'], $memo_id_arr['Memo']['memo_date'], 2);
                    }
                    // OC Calculation 
                    if ($ec_calculation) {
                        @$this->oc_calculation($memo_id_arr['Memo']['territory_id'], $memo_id_arr['Memo']['gross_value'], $memo_id_arr['Memo']['outlet_id'], $memo_id_arr['Memo']['memo_date'], $memo_id_arr['Memo']['memo_time'], 2);
                    }


                    $this->loadModel('Store');
                    $store_id_arr = $this->Store->find('first', array(
                        'conditions' => array(
                            'Store.territory_id' => $memo_id_arr['Memo']['territory_id']
                        )
                    ));
                    $store_id = $store_id_arr['Store']['id'];


                    //$store_id = $val['store_id'];


                    for ($memo_detail_count = 0; $memo_detail_count < count($memo_id_arr['MemoDetail']); $memo_detail_count++) {
                        $product_id = $memo_id_arr['MemoDetail'][$memo_detail_count]['product_id'];
                        $sales_qty = $memo_id_arr['MemoDetail'][$memo_detail_count]['sales_qty'];
                        $sales_price = $memo_id_arr['MemoDetail'][$memo_detail_count]['price'];
                        $memo_territory_id = $memo_id_arr['Memo']['territory_id'];
                        $memo_no = $memo_id_arr['Memo']['memo_no'];
                        $memo_date = $memo_id_arr['Memo']['memo_date'];
                        $outlet_id = $memo_id_arr['Memo']['outlet_id'];
                        $market_id = $memo_id_arr['Memo']['market_id'];

                        /*$punits_pre = $this->search_array($product_id, 'id', $product_list);
                        if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                            $base_quantity = ROUND($sales_qty);
                        } else {
                            $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                        }*/

                        $measurement_unit_id = $memo_id_arr['MemoDetail'][$memo_detail_count]['measurement_unit_id'];
                        $punits_pre = $this->search_array($product_id, 'id', $product_list);
                        if ($measurement_unit_id > 0) {
                            if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                                $base_quantity = ROUND($sales_qty);
                            } else {
                                $base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);
                            }
                        } else {
                            if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                $base_quantity = ROUND($sales_qty);
                            } else {
                                $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                            }
                        }

                        $update_type = 'add';
                        $this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 12, $memo_id_arr['Memo']['memo_date']);



                        // subract sales achievement and stamp achievemt 
                        // sales calculation
                        $t_price = $sales_qty * $sales_price;
                        if ($sales_calculation) {
                            $this->sales_calculation($product_id, $memo_territory_id, $sales_qty, $t_price, $memo_date, 2);
                        }

                        //stamp calculation
                        /* if($stamp_calculation){
                          $this->stamp_calculation($memo_no, $memo_territory_id, $product_id, $outlet_id, $sales_qty, $memo_date, 2, $t_price, $market_id);
                          } */
                    }

                    $memo_id = $memo_id_arr['Memo']['id'];
                    $memo_no = $memo_id_arr['Memo']['memo_no'];
                    $this->Memo->id = $memo_id;
                    $this->Memo->delete();

                    /* 
                        $this->MemoDetail->deleteAll(array('MemoDetail.memo_id' => $memo_id));
                          $this->Deposit->deleteAll(array('Deposit.memo_id' => $memo_id));
                          $this->Collection->deleteAll(array('Collection.memo_id' => $memo_id)); 
                    */
                }
            }
            $this->update_territory_wise_collection_deposit_balance($json_data['so_id']);
            $res['status'] = 1;
            $res['message'] = 'Memo has been deleted successfuly.';


            //start for memoSync 
            /* $sync_date = date('Y-m-d', strtotime($json_data['memo_date']));
              $memo_sync_info = $this->MemoSyncHistory->find('first', array(
              'conditions' => array(
              'MemoSyncHistory.so_id' => $json_data['sales_person_id'],
              'MemoSyncHistory.date >=' => $sync_date
              ),
              //'fields' => array('sum(MemoSyncHistory.total_memo) as total_memo'),
              'order' => array('date desc', 'id desc'),
              'recursive' => -1
              ));
              $memo_sync_id = $memo_sync_info['MemoSyncHistory']['id'];
              $this->MemoSyncHistory->id = $memo_sync_id;
              $this->MemoSyncHistory->updateAll(array(
              'MemoSyncHistory.missed_memo' => 'MemoSyncHistory.missed_memo - 1'),
              array('MemoSyncHistory.id' => $memo_sync_id)); */
            //end for memoSync
        } else {
            $res['status'] = 0;
            $res['message'] = 'Memo not found.';
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "delete_memo_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . json_encode($res));
        fclose($myfile);

        $this->set(array(
            'memo' => $res,
            '_serialize' => array('memo')
        ));
    }

    // update visit plan
    public function update_visit_plan($data)
    {
        $this->loadModel('VisitPlanList');
        $plan_info = $this->VisitPlanList->find('first', array(
            'conditions' => array(
                'VisitPlanList.so_id' => $data['sales_person_id'],
                'VisitPlanList.market_id' => $data['market_id'],
                'VisitPlanList.visit_plan_date' => date('Y-m-d', strtotime($data['memo_date']))
            ),
            'recursive' => -1
        ));
        if (!empty($plan_info)) {
            $udata['id'] = $plan_info['VisitPlanList']['id'];
            $udata['visited_date'] = date('Y-m-d', strtotime($data['memo_date']));
            $udata['visit_status'] = 'Visited';
            $udata['updated_at'] = $this->current_datetime();
            $this->VisitPlanList->save($udata);
        }
    }

    // send notification to ASO
    public function create_memo_nofification($data)
    {
        $this->loadModel('MemoNotification');
        $this->MemoNotification->save($data);
    }

    // it will be called from memo not from memo_details 
    // cal_type=1 means increment and 2 means deduction 

    public function ec_calculation($gross_value, $outlet_id, $terrority_id, $memo_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0

        if ($gross_value > 0) {
            $this->loadModel('Outlet');
            // from outlet_id, retrieve pharma or non-pharma
            $outlet_info = $this->Outlet->find('first', array(
                'conditions' => array(
                    'Outlet.id' => $outlet_id
                ),
                'recursive' => -1
            ));

            if (!empty($outlet_info)) {
                $is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
                // from memo_date , split month and get month name and compare month table with memo year
                $memoDate = strtotime($memo_date);
                $month = date("n", $memoDate);
                $year = date("Y", $memoDate);
                $memo_date = date('Y-m-d', $memoDate);
                $this->loadModel('Month');
                $this->loadModel('FiscalYear');

                // from outlet_id, retrieve pharma or non-pharma
                $fasical_month = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                        /* 'Month.year' => $year */
                    ),
                    'recursive' => -1
                ));
                $fasical_info = $this->FiscalYear->find('first', array(
                    'conditions' => array(
                        'FiscalYear.start_date <=' => $memo_date,
                        'FiscalYear.end_date >=' => $memo_date,
                    ),
                    'recursive' => -1
                ));

                if (!empty($fasical_info)) {
                    $this->loadModel('SaleTargetMonth');
                    if ($cal_type == 1) {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement+1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement+1");
                        }
                    } else {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement-1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement-1");
                        }
                    }

                    $conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['FiscalYear']['id'], 'SaleTargetMonth.month_id' => $fasical_month['Month']['id']);

                    $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                }
            }
        }
    }

    // cal_type=1 means increment and 2 means deduction 
    // it will be called from  memo_details 
    public function sales_calculation($product_id, $terrority_id, $quantity, $gross_value, $memo_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id : no need as we have territory_id
        // from memo_date , split month and get month name and compare month table with memo year
        $memoDate = strtotime($memo_date);
        $month = date("n", $memoDate);
        $year = date("Y", $memoDate);
        $memo_date = date('Y-m-d', $memoDate);
        $this->loadModel('Month');
        $this->loadModel('FiscalYear');
        // from outlet_id, retrieve pharma or non-pharma
        $fasical_month = $this->Month->find('first', array(
            'conditions' => array(
                'Month.month' => $month,
                /* 'Month.year' => $year */
            ),
            'recursive' => -1
        ));
        $fasical_info = $this->FiscalYear->find('first', array(
            'conditions' => array(
                'FiscalYear.start_date <=' => $memo_date,
                'FiscalYear.end_date >=' => $memo_date,
            ),
            'recursive' => -1
        ));

        if (!empty($fasical_info)) {
            $this->loadModel('SaleTargetMonth');
            if ($cal_type == 1) {
                $update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement+$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement+$gross_value");
            } else {
                $update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement-$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement-$gross_value");
            }

            $conditions_arr = array('SaleTargetMonth.product_id' => $product_id, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['FiscalYear']['id'], 'SaleTargetMonth.month_id' => $fasical_month['Month']['id']);
            $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
        }
    }

    // cal_type=1 means increment and 2 means deduction 
    // it will be called from memo not from memo_details 
    public function oc_calculation($terrority_id, $gross_value, $outlet_id, $memo_date, $memo_time, $cal_type)
    {

        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0
        if ($gross_value > 0) {
            $this->loadModel('Memo');
            // this will be updated monthly , if done then increment else no action
            $month_first_date = date('Y-m-01', strtotime($memo_date));
            $count = $this->Memo->find('count', array(
                'conditions' => array(
                    'Memo.outlet_id' => $outlet_id,
                    'Memo.memo_date >= ' => $month_first_date,
                    'Memo.memo_time < ' => $memo_time
                )
            ));

            if ($count == 0) {

                $this->loadModel('Outlet');
                // from outlet_id, retrieve pharma or non-pharma
                $outlet_info = $this->Outlet->find('first', array(
                    'conditions' => array(
                        'Outlet.id' => $outlet_id
                    ),
                    'recursive' => -1
                ));

                if (!empty($outlet_info)) {
                    $is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
                    // from memo_date , split month and get month name and compare month table with memo year
                    $memoDate = strtotime($memo_date);
                    $month = date("n", $memoDate);
                    $year = date("Y", $memoDate);
                    $memo_date = date('Y-m-d', $memoDate);
                    $this->loadModel('Month');
                    $this->loadModel('FiscalYear');
                    // from outlet_id, retrieve pharma or non-pharma
                    $fasical_month = $this->Month->find('first', array(
                        'conditions' => array(
                            'Month.month' => $month,
                            /* 'Month.year' => $year */
                        ),
                        'recursive' => -1
                    ));
                    $fasical_info = $this->FiscalYear->find('first', array(
                        'conditions' => array(
                            'FiscalYear.start_date <=' => $memo_date,
                            'FiscalYear.end_date >=' => $memo_date,
                        ),
                        'recursive' => -1
                    ));

                    if (!empty($fasical_info)) {
                        $this->loadModel('SaleTargetMonth');
                        if ($cal_type == 1) {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement+1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement+1");
                            }
                        } else {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement-1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement-1");
                            }
                        }

                        $conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['FiscalYear']['id'], 'SaleTargetMonth.month_id' => $fasical_month['Month']['id']);
                        $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                        // pr($conditions_arr);
                        //pr($update_fields_arr);
                        // exit;
                    }
                }
            }
        }
    }

    // it will be called from memo_details 
    public function stamp_calculation($memo_no, $terrority_id, $product_id, $outlet_id, $quantity, $memo_date, $cal_type, $gross_amount, $market_id)
    {
        // from outlet_id, get bonus_type_id and check if null then no action else action

        $this->loadModel('Outlet');
        // from outlet_id, retrieve pharma or non-pharma
        $outlet_info = $this->Outlet->find('first', array(
            'conditions' => array(
                'Outlet.id' => $outlet_id
            ),
            'recursive' => -1
        ));

        if (!empty($outlet_info) && $gross_amount > 0) {
            $bonus_type_id = $outlet_info['Outlet']['bonus_type_id'];
            if (($bonus_type_id === NULL) || (empty($bonus_type_id))) {
                // no action 
            } else {
                // from memo_date , split month and get month name and compare month table with memo year (get fascal year id)
                $memoDate = strtotime($memo_date);
                $month = date("n", $memoDate);
                $year = date("Y", $memoDate);
                /* $this->loadModel('Month');
                  $fasical_info = $this->Month->find('first', array(
                  'conditions' => array(
                  'Month.month' => $month,
                  'Month.year' => $year
                  ),
                  'recursive' => -1
                  )); */
                $memo_date = date('Y-m-d', $memoDate);
                $this->LoadModel('FiscalYear');
                $fasical_info = $this->FiscalYear->find('first', array(
                    'conditions' => array(
                        'FiscalYear.start_date <=' => $memo_date,
                        'FiscalYear.end_date >=' => $memo_date,
                    ),
                    'recursive' => -1
                ));
                if (!empty($fasical_info)) {
                    // check bonus card table , where is_active,and others  and get min qty per memo
                    $this->loadModel('BonusCard');
                    $bonus_card_info = $this->BonusCard->find('first', array(
                        'conditions' => array(
                            'BonusCard.fiscal_year_id' => $fasical_info['FiscalYear']['id'],
                            'BonusCard.is_active' => 1,
                            'BonusCard.product_id' => $product_id,
                            'BonusCard.bonus_card_type_id' => $bonus_type_id
                        ),
                        'recursive' => -1
                    ));

                    /* echo $this->BonusCard->getLastquery();
                      pr($bonus_card_info); */

                    // if exist min qty per memo , then stamp_no=mod(quantity/min qty per memo)
                    if (!empty($bonus_card_info)) {
                        $min_qty_per_memo = $bonus_card_info['BonusCard']['min_qty_per_memo'];
                        if ($min_qty_per_memo && $min_qty_per_memo <= $quantity) {
                            $stamp_no = floor($quantity / $min_qty_per_memo);
                            /* if ($cal_type != 1) {
                              $stamp_no = $stamp_no * (-1);
                              $quantity = $quantity * (-1);
                              } */


                            $this->loadModel('StoreBonusCard');
                            $log_data = array();
                            $log_data['StoreBonusCard']['created_at'] = $this->current_datetime();
                            $log_data['StoreBonusCard']['territory_id'] = $terrority_id;
                            $log_data['StoreBonusCard']['outlet_id'] = $outlet_id;
                            $log_data['StoreBonusCard']['market_id'] = $market_id;
                            $log_data['StoreBonusCard']['product_id'] = $product_id;
                            $log_data['StoreBonusCard']['quantity'] = $quantity;
                            $log_data['StoreBonusCard']['no_of_stamp'] = $stamp_no;
                            $log_data['StoreBonusCard']['bonus_card_id'] = $bonus_card_info['BonusCard']['id'];
                            $log_data['StoreBonusCard']['bonus_card_type_id'] = $bonus_type_id;
                            $log_data['StoreBonusCard']['fiscal_year_id'] = $bonus_card_info['BonusCard']['fiscal_year_id'];
                            $log_data['StoreBonusCard']['memo_no'] = $memo_no;

                            $this->StoreBonusCard->create();
                            $this->StoreBonusCard->save($log_data);
                        }
                    }
                }
            }
        }
    }

    public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '')
    {

        $this->loadModel('CurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        $inventory_info = $this->CurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'CurrentInventory.store_id' => $store_id,
                'CurrentInventory.inventory_status_id' => 1,
                'CurrentInventory.product_id' => $product_id
            ),
            'order' => array('CurrentInventory.expire_date' => 'asc'),
            'recursive' => -1
        ));




        if ($update_type == 'deduct') {
            foreach ($inventory_info as $val) {
                if ($quantity <= $val['CurrentInventory']['qty']) {
                    $this->CurrentInventory->id = $val['CurrentInventory']['id'];
                    $this->CurrentInventory->updateAll(
                        array(
                            'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity,
                            'CurrentInventory.transaction_type_id' => $transaction_type_id,
                            'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('CurrentInventory.id' => $val['CurrentInventory']['id'])
                    );
                    break;
                } else {

                    $quantity = $quantity - $val['CurrentInventory']['qty'];

                    if ($val['CurrentInventory']['qty'] > 0) {

                        $this->CurrentInventory->id = $val['CurrentInventory']['id'];
                        $this->CurrentInventory->updateAll(
                            array(
                                'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $val['CurrentInventory']['qty'],
                                'CurrentInventory.transaction_type_id' => $transaction_type_id,
                                'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                            ),
                            array('CurrentInventory.id' => $val['CurrentInventory']['id'])
                        );
                    }

                    /*if ($quantity!=0) {
                        $quantity = $quantity - $val['CurrentInventory']['qty'];

                        $this->CurrentInventory->id = $val['CurrentInventory']['id'];
                        $this->CurrentInventory->updateAll(
                                array(
                            'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $val['CurrentInventory']['qty'],
                            'CurrentInventory.transaction_type_id' => $transaction_type_id,
                            'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ), array('CurrentInventory.id' => $val['CurrentInventory']['id'])
                        );
                    }*/
                }
            }
        } else {
            /* $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])); */
            if (!empty($inventory_info)) {

                $this->CurrentInventory->updateAll(
                    array('CurrentInventory.qty' => 'CurrentInventory.qty + ' . $quantity, 'CurrentInventory.transaction_type_id' => $transaction_type_id, 'CurrentInventory.store_id' => $store_id, 'CurrentInventory.transaction_date' => "'" . $transaction_date . "'"),
                    array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])
                );
            }
        }

        return true;
    }

    public function get_memo_list()
    {
        $this->loadModel('Memo');
        $this->loadModel('MemoDetail');
        $this->loadModel('Product');

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        //pr($json_data);

        $so_id = $json_data['so_id'];
        $memo_type = $json_data['memo_type'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        $seven_days_pre = date('Y-m-d 00:00:00', strtotime('-40 days'));


        /* $conditions= array(
          'Memo.sales_person_id' => $so_id,

          'OR'=>array(
          array(
          'AND'=>array(
          array('Memo.action >' => 0),
          array('Memo.from_app' => 0)
          )
          ),
          array(
          'OR' => array(
          array('Memo.memo_editable' => 1),
          )
          )
          ),
          ); */


        $this->loadModel('SalesPerson');
        $terrtory_id = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.territory_id'),
            'conditions' => array('SalesPerson.id' => $so_id),
            'recursive' => -1
        ));
        //pr($terrtory_id);
        $territory_id = $terrtory_id['SalesPerson']['territory_id'];

        if ($memo_type == 'from_web') {
            $conditions = array('Memo.territory_id' => $territory_id, 'Memo.action >' => 0, 'Memo.from_app' => 0, 'Memo.status >' => 0);
        } elseif ($memo_type == 'memo_editable') {
            $conditions = array('Memo.territory_id' => $territory_id, 'Memo.memo_editable' => 1);
        } else {
            $conditions = array('Memo.territory_id' => $territory_id, 'Memo.action' => 0, 'Memo.memo_time >' => $seven_days_pre);
        }

        /*if ($memo_type == 'from_web') {
            $conditions = array('Memo.sales_person_id' => $so_id, 'Memo.action >' => 0, 'Memo.from_app' => 0,'Memo.status >'=>0);
        } elseif ($memo_type == 'memo_editable') {
            $conditions = array('Memo.sales_person_id' => $so_id, 'Memo.memo_editable' => 1);
        } else {
            $conditions = array('Memo.sales_person_id' => $so_id, 'Memo.action' => 0, 'Memo.memo_time >' => $seven_days_pre);
        }*/

        /* if ($res_status == 1) {
          //$conditions = array('Memo.sales_person_id' => $so_id, 'Memo.memo_time >' => $seven_days_pre);
          } else {
          //$conditions = array('Memo.sales_person_id' => $so_id, 'Memo.action >' => 0, 'Memo.memo_time >' => $seven_days_pre);
          $conditions = array('Memo.sales_person_id' => $so_id, 'Memo.action >' => 0,'Memo.from_app' => 0);
          } */



        $memo_list = $this->Memo->find('all', array(
            'fields' => array('Memo.*'),
            'conditions' => $conditions,
            'order' => array('Memo.memo_date ASC'),
            'recursive' => 1
        ));



        $memo_array = array();
        foreach ($memo_list as $m) {
            $mdata['id'] = $m['Memo']['id'];
            $mdata['memo_no'] = $m['Memo']['memo_no'];
            $mdata['memo_date'] = $m['Memo']['memo_date'];
            $mdata['sales_person_id'] = $m['Memo']['sales_person_id'];
            $mdata['outlet_id'] = $m['Memo']['outlet_id'];
            $mdata['market_id'] = $m['Memo']['market_id'];
            $mdata['territory_id'] = $m['Memo']['territory_id'];
            $mdata['gross_value'] = $m['Memo']['gross_value'];
            $mdata['adjustment_amount'] = $m['Memo']['adjustment_amount'];
            $mdata['adjustment_note'] = $m['Memo']['adjustment_note'];
            $mdata['cash_recieved'] = $m['Memo']['cash_recieved'];
            $mdata['credit_amount'] = $m['Memo']['credit_amount'];
            $mdata['latitude'] = $m['Memo']['latitude'];
            $mdata['longitude'] = $m['Memo']['longitude'];
            $mdata['from_app'] = $m['Memo']['from_app'];
            $mdata['is_distributor'] = $m['Memo']['is_distributor'];
            $mdata['from_app'] = $m['Memo']['from_app'];
            $mdata['total_discount'] = $m['Memo']['total_discount'];
            if ($m['Memo']['from_app'] == 0 && $m['Memo']['action'] == 1) {
                $mdata['updated_at'] = date('Y-m-d H:i:s', strtotime($m['Memo']['memo_date']));
            } else {
                $mdata['updated_at'] = $m['Memo']['memo_time'];
            }
            $mdata['action'] = ($res_status == 1 ? 1 : $m['Memo']['action']);
            $mdata['memo_details'] = $m['MemoDetail'];
            $mm = 0;

            foreach ($m['MemoDetail'] as $each_memo_details) {

                $product_id = $each_memo_details['product_id'];
                $product_info = $this->Product->find('first', array(
                    'fields' => array('product_type_id'),
                    'conditions' => array('id' => $product_id),
                    'recursive' => -1
                ));

                $type = "";
                if ($product_info['Product']['product_type_id'] == 1 && $each_memo_details['price'] > 0) {
                    $type = 0;
                } else if ($product_info['Product']['product_type_id'] == 3) {
                    $type = 1;
                } else if ($product_info['Product']['product_type_id'] == 1 && $each_memo_details['price'] < 1) {
                    $type = 2;
                }

                $mdata['memo_details'][$mm]['product_type_id'] = $type;
                $mdata['memo_details'][$mm]['price'] = $each_memo_details['actual_price'];

                $mdata['memo_details'][$mm]['vat'] = $this->get_vat_by_product_id_memo_date_v2($product_id, $m['Memo']['memo_date']);
                if ($each_memo_details['other_info']) {
                    $other_info = json_decode($each_memo_details['other_info'], 1);
                    $selected_set = $other_info['selected_set'];
                    $provided_qty = $other_info['provided_qty'];
                    $mdata['memo_details'][$mm]['selected_set'] = $selected_set;
                    $mdata['memo_details'][$mm]['provided_qty'] = $provided_qty;
                }
                $mm++;
            }


            $memo_array[]['Memo'] = $mdata;

            if ($memo_type == 'memo_editable') {
                $this->Memo->id = $m['Memo']['id'];
                if ($this->Memo->id) {
                    $this->Memo->saveField('memo_editable', 0);
                }
            }
        }

        $this->set(array(
            'memo_list' => $memo_array,
            '_serialize' => array('memo_list')
        ));
    }

    // end get_memo_list()


    public function callback_memo_list()
    {
        $this->loadModel('Memo');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        if (!empty($json_data)) {
            $data_array = array();
            foreach ($json_data['memo'] as $val) {
                echo $data['memo_no'] = $val['memo_no'];
                $memo_id = $this->Memo->find('first', array('fields' => array('id'), 'conditions' => array('Memo.memo_no' => $val["memo_no"]), 'recursive' => -1));
                //print_r($memo_id);
                $data['id'] = $memo_id['Memo']['id'];
                $data['action'] = 0;
                $data_array[] = $data;
            }
            $this->Memo->saveAll($data_array);
        }
        $res['status'] = 1;
        $res['message'] = 'Success';

        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }

    // end callback_memo_list()


    public function credit_collection_list()
    {
        $this->loadModel('Memo');
        $json_data = $this->request->input('json_decode', true);
        $territory_id = $json_data['territory_id'];

        /* $collection_list = $this->Memo->find('all', array(
          'fields' => array('Memo.id','Memo.memo_no','Memo.memo_date','Memo.outlet_id','Memo.gross_value','Memo.cash_recieved','Memo.credit_amount'),
          'conditions' => array('Memo.territory_id' => $territory_id,'Memo.status' => 1),
          'order' => array('Memo.id' => 'asc'),
          'recursive' => -1
          )); */

        $this->set(array(
            'collection_list' => array(),
            '_serialize' => array('collection_list')
        ));
    }

    /* ------------------- End Memo ---------------------- */


    /* ------------------- Start Current Inventory ---------------------- */

    public function get_current_stock()
    {
        $this->loadModel('CurrentInventory');
        $this->loadModel('ReturnChallan');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $store_id = $json_data['store_id'];
        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'Product.id = CurrentInventory.product_id',
                'CurrentInventory.store_id' => $store_id,
                'CurrentInventory.inventory_status_id !=' => 2
            );
        } else {
            $conditions = array(
                'CurrentInventory.receiver_store_id' => $store_id,
                'CurrentInventory.updated_at >' => $last_update_date,
                'CurrentInventory.inventory_status_id !=' => 2
            );
        }


        /* Check if SO has any pending Return Challans or NCP Return Challans */

        $pending_return_challan_count = $this->ReturnChallan->find('count', array(
            'conditions' => array(
                'ReturnChallan.sender_store_id' => $store_id,
                'ReturnChallan.status' => 1
            )
        ));

        $data_array = array();
        if ($pending_return_challan_count == 0) {
            $stock_info = $this->CurrentInventory->find('all', array(
                'fields' => array('CurrentInventory.*', 'Product.base_measurement_unit_id', 'Product.product_type_id', 'Product.sales_measurement_unit_id'),
                'conditions' => $conditions,
                'order' => array('CurrentInventory.id' => 'asc'),
                'recursive' => 0
            ));


            foreach ($stock_info as $val) {
                $data['id'] = $val['CurrentInventory']['id'];
                $data['store_id'] = $val['CurrentInventory']['store_id'];
                $data['inventory_status_id'] = $val['CurrentInventory']['inventory_status_id'];
                $data['product_id'] = $val['CurrentInventory']['product_id'];
                $data['batch_number'] = $val['CurrentInventory']['batch_number'];
                $data['expire_date'] = $val['CurrentInventory']['expire_date'];
                $data['product_type_id'] = $val['Product']['product_type_id'];
                $data['m_unit'] = $val['Product']['sales_measurement_unit_id'];
                $data['qty'] = $this->unit_convertfrombase($val['CurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['CurrentInventory']['qty']);
                $data['updated_at'] = $val['CurrentInventory']['updated_at'];
                $data_array['CurrentInventory'][] = $data;
            }
        } else {
            $data_array['is_return_challan_pending'] = 1;
        }

        $this->set(array(
            'stock_info' => $data_array,
            '_serialize' => array('stock_info')
        ));
    }


    public function get_current_stock_without_batch()
    {
        $this->loadModel('CurrentInventory');
        $this->loadModel('ReturnChallan');

        /* $path = APP . 'logs/';
        $myfile = fopen($path . "get_current_stock_without_batch.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);*/

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $store_id = $json_data['store_id'];
        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'Product.id = CurrentInventory.product_id',
                'CurrentInventory.store_id' => $store_id,
                'CurrentInventory.inventory_status_id !=' => 2
            );
        } else {
            $conditions = array(
                'CurrentInventory.receiver_store_id' => $store_id,
                'CurrentInventory.updated_at >' => $last_update_date,
                'CurrentInventory.inventory_status_id !=' => 2
            );
        }


        /* Check if SO has any pending Return Challans or NCP Return Challans */

        /* $pending_return_challan_count = $this->ReturnChallan->find('count', array(
            'conditions' => array(
                'ReturnChallan.sender_store_id' => $store_id,
                'ReturnChallan.status' => 1
            )
        ));*/

        $data_array = array();
        // having SUM(CurrentInventory.qty) >0
        /*f ($pending_return_challan_count == 0) {*/
        $stock_info = $this->CurrentInventory->find('all', array(
            'fields' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'SUM(CurrentInventory.qty) as qty', 'Product.sales_measurement_unit_id'),
            'conditions' => $conditions,
            'group' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'Product.sales_measurement_unit_id', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date'),
            'order' => array('CurrentInventory.product_id' => 'asc'),
            'recursive' => 0
        ));

        // pr($stock_info);exit;
        foreach ($stock_info as $val) {
            /*$data['id'] = $val['CurrentInventory']['id'];
                $data['store_id'] = $val['CurrentInventory']['store_id'];*/
            /*$data['inventory_status_id'] = $val['CurrentInventory']['inventory_status_id'];*/
            // $data['product_id'] = $val['CurrentInventory']['product_id'];
            /*$data['batch_number'] = $val['CurrentInventory']['batch_number'];
                $data['expire_date'] = $val['CurrentInventory']['expire_date'];
                $data['product_type_id'] = $val['Product']['product_type_id'];
                $data['m_unit'] = $val['Product']['sales_measurement_unit_id'];*/
            /*$data['qty'] = $this->unit_convertfrombase($val['CurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['CurrentInventory']['qty']);*/
            /*$data['updated_at'] = $val['CurrentInventory']['updated_at'];*/
            $data_array['CurrentInventory'][$val['CurrentInventory']['product_id']] = (isset($data_array['CurrentInventory'][$val['CurrentInventory']['product_id']]) ? $data_array['CurrentInventory'][$val['CurrentInventory']['product_id']] : 0) + $this->unit_convertfrombase($val['CurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['0']['qty']);
        }
        /* }
        else
        {
              $data_array['is_return_challan_pending']=1;
        }*/

        $this->set(array(
            'stock_info' => $data_array,
            '_serialize' => array('stock_info')
        ));
    }

    /* ------------------- End Current Inventory ---------------------- */


    /* ------------------- Start NGO ---------------------- */

    public function get_ngo_institutes()
    {
        $this->loadModel('Institute');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $area_id = $json_data['office_id'];
        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('mi.area_id' => $area_id);
        } else {
            $conditions = array('mi.area_id' => $area_id, 'mi.updated_at >' => $last_update_date);
        }

        /*
          $institutes = $this->Institute->find('all', array(
          'fields' => array('Institute.id','Institute.name','Institute.updated_at'),
          'conditions' => $conditions,
          'order' => array('Institute.updated_at' => 'asc'),
          'recursive' => -1
          ));
         */


        $institutes = $this->Institute->find('all', array(
            'joins' => array(
                array(
                    'table' => 'mapping_institute_to_areas',
                    'alias' => 'mi',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Institute.id=mi.institute_id'
                    )
                )
            ),
            'conditions' => $conditions,
            'fields' => array('Institute.id', 'Institute.short_name', 'Institute.name', 'Institute.type', 'Institute.address', 'Institute.email', 'Institute.telephone', 'Institute.contactname', 'Institute.credit_limit', 'Institute.is_active', 'Institute.created_at', 'Institute.created_by', 'Institute.updated_at', 'Institute.updated_by', 'mi.updated_at as mapping_updated_at'),
            'recursive' => -1
        ));

        foreach ($institutes as $key => $val) {
            $institutes[$key]['Institute']['action'] = 1;
            $institutes[$key]['Institute']['updated_at'] = $institutes[$key][0]['mapping_updated_at'];
        }


        $this->set(array(
            'institutes' => $institutes,
            '_serialize' => array('institutes')
        ));
    }

    /* ------------------- End NGO ---------------------- */

    /* ------------------- Start weeks ---------------------- */

    public function get_weeks()
    {
        $this->loadModel('Week');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'Week.start_date >=' => date('Y-m-d', strtotime(date('Y-m-01') . ' -2 months')),
                'Week.start_date <=' => date('Y-m-t')
            );
        } else {
            $conditions = array(
                'Week.updated_at >' => $last_update_date,
                'Week.start_date >=' => date('Y-m-d', strtotime(date('Y-m-01') . ' -2 months')),
                'Week.start_date <=' => date('Y-m-t')
            );
        }
        $weeks = $this->Week->find('all', array(
            'fields' => array('Week.id', 'Week.week_name', 'Week.updated_at', 'Week.start_date', 'Week.end_date'),
            'conditions' => $conditions,
            'order' => array('Week.updated_at' => 'asc'),
            'recursive' => -1
        ));
        // echo $this->Week->getLastquery();exit;
        $this->set(array(
            'weeks' => $weeks,
            '_serialize' => array('weeks')
        ));
    }

    /* ------------------- End weeks ---------------------- */

    /* ------------------- Start branch ---------------------- */

    public function get_bank_branch()
    {
        $this->loadModel('BankBranch');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }


        $last_update_date = $json_data['last_update_date'];

        $territory_id = $json_data['territory_id'];  /// added by palash 26th May 2017

        $this->loadModel('Territory');
        $territory_info = $this->Territory->find(
            'first',
            array(
                'conditions' => array('Territory.id' => $territory_id),
                'fields' => array('Office.id'),
                'recursive' => 0
            )
        );

        $office_id = $territory_info['Office']['id'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);

        if ($res_status == 1) {
            /* $conditions = array(
              //'BankBranch.territory_id' => $territory_id,
              'OR' => array(
              array('BankBranch.territory_id' => $territory_id),
              array('BankBranch.is_common' => 1),
              )
              ); */

            $conditions = array(
                //'BankBranch.territory_id' => $territory_id,
                'OR' => array(
                    array(
                        'AND' => array(
                            array('BankBranch.territory_id' => $territory_id),
                        )
                    ),
                    array(
                        'AND' => array(
                            array('BankBranch.office_id' => $office_id),
                            array('BankBranch.is_common' => 1),
                        )
                    ),
                    array(
                        'AND' => array(
                            array('BankBranch.office_id' => NULL),
                            array('BankBranch.territory_id' => NULL),
                            array('BankBranch.is_common' => 1),
                        )
                    ),
                )
            );
        } else {
            //$conditions = array('BankBranch.updated_at >' => $last_update_date, 'BankBranch.territory_id' => $territory_id);

            $conditions = array(
                'BankBranch.updated_at >' => $last_update_date,
                'OR' => array(
                    array(
                        'AND' => array(
                            array('BankBranch.territory_id' => $territory_id),
                        )
                    ),
                    array(
                        'AND' => array(
                            array('BankBranch.office_id' => $office_id),
                            array('BankBranch.is_common' => 1),
                        )
                    ),
                    array(
                        'AND' => array(
                            array('BankBranch.office_id' => NULL),
                            array('BankBranch.territory_id' => NULL),
                            array('BankBranch.is_common' => 1),
                        )
                    ),
                )
            );
        }

        $BankBranch = $this->BankBranch->find('all', array(
            'fields' => array('BankBranch.id', 'BankBranch.name', 'BankBranch.updated_at'),
            'conditions' => $conditions,
            'order' => array('BankBranch.id' => 'asc'),
            'recursive' => -1
        ));

        //pr($BankBranch);

        foreach ($BankBranch as $key => $val) {
            $BankBranch[$key]['BankBranch']['action'] = 1;
        }

        $this->set(array(
            'BankBranch' => $BankBranch,
            '_serialize' => array('BankBranch')
        ));
    }

    /* ------------------- End branch ---------------------- */

    /* ------------------- Start pharmacy ---------------------- */

    public function get_pharmacy()
    {
        $this->loadModel('Institute');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        //$store_id = $json_data['store_id'];
        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('Institute.type' => 2);
        } else {
            $conditions = array('Institute.type' => 2, 'Institute.updated_at >' => $last_update_date);
        }

        $pharmacy = $this->Institute->find('all', array(
            'fields' => array('Institute.id', 'Institute.name', 'Institute.updated_at'),
            'conditions' => $conditions,
            'order' => array('Institute.id' => 'asc'),
            'recursive' => -1
        ));

        $this->set(array(
            'pharmacy' => $pharmacy,
            '_serialize' => array('pharmacy')
        ));
    }

    /* ------------------- End pharmacy ---------------------- */


    /* ------------------- Target ---------------------- */

    public function get_product_target()
    {
        $this->loadModel('SaleTarget');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('SaleTarget.so_id' => $so_id);
        } else {
            $conditions = array('SaleTarget.so_id' => $so_id, 'SaleTarget.updated >' => $last_update_date);
        }
        $target = $this->SaleTarget->find('all', array(
            'fields' => array('SaleTarget.id', 'SaleTarget.quantity', 'SaleTarget.amount', 'Product.name'),
            'conditions' => $conditions,
            'order' => array('SaleTarget.id' => 'asc'),
            'recursive' => 0
        ));

        $this->set(array(
            'target' => $target,
            '_serialize' => array('target')
        ));
    }

    /* ------------------- End Target ---------------------- */


    /* ------------------- Message ---------------------- */

    /*
     * get user group
     * @return
     */

    public function get_user_groups()
    {
        $this->loadModel('UserGroup');
        $list = $this->UserGroup->find('all', array(
            'fields' => array('UserGroup.id', 'UserGroup.name'),
            'conditions' => array('UserGroup.id >=' => 3, 'UserGroup.id !=' => 4),
            'order' => array('UserGroup.id' => 'asc'),
            'recursive' => -1
        ));

        $this->set(array(
            'group_list' => $list,
            '_serialize' => array('group_list')
        ));
    }

    // end function get_user_groups() 


    /*
     * send_message
     * @return
     */
    public function send_message()
    {
        $this->loadModel('MessageList');
        $this->loadModel('User');
        $this->loadModel('SalesPerson');

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        if (!empty($json_data['message_list'])) {
            foreach ($json_data['message_list'] as $val) {
                $data['message_category_id'] = 0;
                $data['message'] = $val['message'];
                $data['sender_id'] = $val['so_id'];
                $data['message_type'] = 2;
                $data['is_promotional'] = 0;
                $data['created_at'] = $this->current_datetime();
                $data['created_by'] = $val['so_id'];
                $this->MessageList->save($data);

                $receiver = $this->SalesPerson->find('all', array(
                    'fields' => array('SalesPerson.id'),
                    'conditions' => array('User.user_group_id' => $val['user_group_id'], 'SalesPerson.office_id' => $val['office_id']),
                    'recursive' => 0
                ));

                if (!empty($receiver)) {
                    $receiver_array = array();
                    foreach ($receiver as $rec) {
                        $data['message_id'] = $this->MessageList->id;
                        $data['receiver_id'] = $rec['SalesPerson']['id'];
                        $receiver_array[] = $data;
                    }
                    $this->MessageList->MessageReceiver->saveAll($receiver_array);
                }
            }

            $res['status'] = 1;
            $res['message'] = 'Message has been sent.';
        } else {
            $res['status'] = 0;
            $res['message'] = 'Message not sent.';
        }
        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }

    // end function send_message() 



    public function get_inbox_message()
    {
        $this->loadModel('MessageReceiver');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'MessageReceiver.receiver_id' => $so_id,
                'OR' => array(
                    'MessageList.message_type' => 0,
                    'MessageList.message_type' => 2
                )
            );
        } else {
            $conditions = array(
                'MessageReceiver.receiver_id' => $so_id,
                'MessageList.created_at >' => $last_update_date,
                'OR' => array(
                    'MessageList.message_type' => 0,
                    'MessageList.message_type' => 2
                )
            );
        }

        $inbox_message = $this->MessageReceiver->find('all', array(
            'fields' => array('MessageList.id', 'MessageList.message', 'MessageList.created_at'),
            'conditions' => $conditions,
            'order' => array('MessageReceiver.id' => 'asc'),
            'recursive' => 0
        ));

        foreach ($inbox_message as $key => $val) {
            $inbox_message[$key]['MessageList']['action'] = 1;
        }

        $this->set(array(
            'inbox_message' => $inbox_message,
            '_serialize' => array('inbox_message')
        ));
    }

    public function get_ticker_message()
    {
        $this->loadModel('MessageReceiver');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'MessageReceiver.receiver_id' => $so_id,
                'OR' => array(
                    'MessageList.message_type' => 0,
                    'MessageList.message_type' => 1
                )
            );
        } else {
            $conditions = array(
                'MessageReceiver.receiver_id' => $so_id,
                'MessageList.created_at >' => $last_update_date,
                'OR' => array(
                    'MessageList.message_type' => 0,
                    'MessageList.message_type' => 1
                )
            );
        }
        $ticker_message = $this->MessageReceiver->find('all', array(
            'fields' => array('MessageList.id', 'MessageList.message', 'MessageList.created_at'),
            'conditions' => $conditions,
            'order' => array('MessageList.created_at' => 'asc'),
            'recursive' => 0
        ));

        foreach ($ticker_message as $key => $val) {
            $ticker_message[$key]['MessageList']['action'] = 1;
        }

        $this->set(array(
            'ticker_message' => $ticker_message,
            '_serialize' => array('ticker_message')
        ));
    }

    public function get_promotion_message()
    {
        $this->loadModel('MessageList');
        $this->loadModel('MessageProduct');

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'MessageList.is_promotional' => 1
            );
        } else {
            $conditions = array(
                'MessageList.is_promotional' => 1,
                'MessageList.created_at >' => $last_update_date
            );
        }
        $promotion_message = $this->MessageList->find('all', array(
            'fields' => array('MessageList.id', 'MessageList.message', 'MessageList.created_at'),
            'conditions' => $conditions,
            'order' => array('MessageList.created_at' => 'asc'),
            'recursive' => -1
        ));

        if (!empty($promotion_message)) {
            if ($res_status == 1) {
                $conditions = array(
                    'MessageList.is_promotional' => 1
                );
            } else {
                $conditions = array(
                    'MessageList.is_promotional' => 1,
                    'MessageList.created_at >' => $last_update_date
                );
            }

            foreach ($promotion_message as $key => $val) {
                $promotion_message[$key]['MessageList']['action'] = 1;
            }

            $MessageProduct['Message'] = $promotion_message;
            foreach ($promotion_message as $val) {
                $MessageProduct['Products'] = $this->MessageProduct->find('all', array(
                    'fields' => array('MessageProduct.id', 'MessageProduct.message_id', 'MessageProduct.product_id', 'Product.name'),
                    'conditions' => $conditions,
                    'order' => array('MessageList.created_at' => 'asc'),
                    'recursive' => 0
                ));
            }
        }


        $this->set(array(
            'promotion_message' => $MessageProduct,
            '_serialize' => array('promotion_message')
        ));
    }

    /* ------------------- End Target ---------------------- */


    /* ------------------- Get Territory ---------------------- */

    public function get_territory()
    {
        $this->loadModel('Territory');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $territory_id = $json_data['territory_id'];

        $territory = $this->Territory->find('first', array(
            'fields' => array('Territory.id', 'Territory.name', 'Territory.updated_at'),
            'conditions' => array('Territory.id' => $territory_id),
            'recursive' => 0
        ));

        foreach ($territory as $key => $val) {
            $territory[$key]['action'] = 1;
        }

        $this->set(array(
            'territory' => array($territory),
            '_serialize' => array('territory')
        ));
    }

    public function get_child_territories()
    {

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $group_id = $json_data['group_id'];


        $this->loadModel('Usermgmt.User');
        $user_info = $this->User->find('all', array(
            'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name', 'UserTerritoryList.territory_id'),
            'conditions' => array('SalesPerson.id' => $so_id, 'UserGroup.id' => $group_id, 'User.active' => 1),
            'joins' => array(
                array(
                    'alias' => 'UserTerritoryList',
                    'table' => 'user_territory_lists',
                    'type' => 'INNER',
                    'conditions' => 'User.id = UserTerritoryList.user_id'
                ),
                array(
                    'alias' => 'SalesPerson',
                    'table' => 'sales_people',
                    'type' => 'INNER',
                    'conditions' => 'User.sales_person_id = SalesPerson.id'
                )
            ),
            'recursive' => 0
        ));


        $territory_ids = array();
        foreach ($user_info as $result) {
            //echo $result['UserTerritoryList']['territory_id'].'<br>';
            array_push($territory_ids, $result['UserTerritoryList']['territory_id']);
        }




        $this->loadModel('Territory');
        $territory = $this->Territory->find('all', array(
            'fields' => array('Territory.id', 'Territory.name', 'Territory.updated_at'),
            'conditions' => array('Territory.id' => $territory_ids),
            'recursive' => 0
        ));

        //pr($territory);

        $data = array();

        foreach ($territory as $key => $val) {
            $territory[$key]['action'] = 1;
        }

        $this->set(array(
            'territory' => $territory,
            '_serialize' => array('territory')
        ));
    }

    /* ------------------- End Target ---------------------- */


    /* ------------------- Get Visit ---------------------- */

    public function get_visit_list()
    {
        $this->loadModel('VisitPlanList');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('VisitPlanList.so_id' => $so_id);
        } else {
            $conditions = array('VisitPlanList.so_id' => $so_id, 'VisitPlanList.updated_at >' => $last_update_date);
        }

        $visit_list = $this->VisitPlanList->find('all', array(
            'fields' => array('VisitPlanList.id', 'VisitPlanList.market_id', 'VisitPlanList.visit_plan_date', 'VisitPlanList.visit_status', 'VisitPlanList.updated_at', 'Market.name'),
            'conditions' => $conditions,
            'order' => array('VisitPlanList.updated_at' => 'asc'),
            'recursive' => 0
        ));

        foreach ($visit_list as $key => $val) {
            $visit_list[$key]['VisitPlanList']['action'] = 1;
        }

        $this->set(array(
            'visit_list' => $visit_list,
            '_serialize' => array('visit_list')
        ));
    }

    public function create_out_of_plan()
    {
        $this->loadModel('VisitPlanList');
        $this->loadModel('Market');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        if (!empty($json_data['visit_list'])) {
            // data array
            $plan_array = array();
            foreach ($json_data['visit_list'] as $val) {
                if (is_numeric($val['market_id'])) {
                    $market_id = $val['market_id'];
                } else {
                    $market_info = $this->Market->find('first', array(
                        'conditions' => array('Market.temp_id' => $val['market_id'])
                    ));
                    $market_id = $market_info['Market']['id'];
                }

                $data['aso_id'] = 0;
                $data['so_id'] = $val['so_id'];
                $data['market_id'] = $market_id;
                $data['visit_plan_date'] = $val['visit_plan_date'];
                $data['is_out_of_plan'] = 1;
                $data['visit_status'] = 'Pending';
                $data['created_at'] = $this->current_datetime();
                $data['created_by'] = $val['so_id'];
                $data['updated_at'] = $this->current_datetime();
                $plan_array[] = $data;
            }

            if ($this->VisitPlanList->saveAll($plan_array)) {
                $res['status'] = 1;
                $res['message'] = 'Out of plan created successfuly.';
            } else {
                $res['status'] = 0;
                $res['message'] = 'Out of plan not created.';
            }
        } else {
            $res['status'] = 1;
            $res['message'] = 'Out of plan created successfuly.';
        }

        $this->set(array(
            'out_of_plan' => $res,
            '_serialize' => array('out_of_plan')
        ));
    }

    /* ------------------- End Target ---------------------- */


    /* ------------------- Get Doctor ---------------------- */

    public function get_doctor_list()
    {
        $this->loadModel('Doctor');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $territory_id = $json_data['territory_id'];

        $last_update_date = $json_data['last_update_date'];

        if (!empty($json_data['child_territories'])) {
            $territory_id = array($territory_id);

            foreach ($json_data['child_territories'] as $key => $value) {
                array_push($territory_id, $value['id']);
                //echo $value['id'].'<br>';
            }
        }

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('Doctor.territory_id' => $territory_id);
        } else {
            //$conditions = array('Doctor.territory_id' => $territory_id, 'Doctor.action >' => 0, 'Doctor.updated_at >=' => $last_update_date);
            $conditions = array('Doctor.territory_id' => $territory_id, 'Doctor.updated_at >=' => $last_update_date);
        }




        $doctors = $this->Doctor->find('all', array(
            //'fields' => array('Doctor.id','Doctor.name','Doctor.updated_at','Doctor.action','DoctorType.title'),
            'fields' => array('Doctor.*', 'Market.thana_id'),
            'conditions' => $conditions,
            'order' => array('Doctor.updated_at' => 'asc'),
            'recursive' => 0
        ));

        //pr($doctors);

        $data_array = array();
        foreach ($doctors as $key => $val) {
            $doctors[$key]['Doctor']['thana_id'] = $val['Market']['thana_id'];
            $doctors[$key]['Doctor']['action'] = 1;
        }
        $this->set(array(
            'doctors' => $doctors,
            '_serialize' => array('doctors')
        ));
    }

    public function callback_doctor_list()
    {
        $this->loadModel('Doctor');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        if (!empty($json_data)) {
            $data_array = array();
            foreach ($json_data as $val) {
                $data['id'] = $val['id'];
                $data['action'] = 0;
                $data_array[] = $data;
            }
            $this->Doctor->saveAll($data_array);
        }
        $res['status'] = 1;
        $res['message'] = 'Success';

        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }

    // end callback_doctor_list()


    public function get_doctor_qualification()
    {
        $this->loadModel('DoctorQualification');
        $doctorQualifications = $this->DoctorQualification->find('all', array('recursive' => -1));
        $this->set(array(
            'doctor_qualification' => $doctorQualifications,
            '_serialize' => array('doctor_qualification')
        ));
    }

    public function get_doctor_type()
    {
        $this->loadModel('DoctorType');
        $doctorTypes = $this->DoctorType->find('all', array('recursive' => -1));
        $this->set(array(
            'doctor_type' => $doctorTypes,
            '_serialize' => array('doctor_type')
        ));
    }

    public function create_doctor()
    {
        $this->loadModel('Market');
        $this->loadModel('Outlet');
        $this->loadModel('Doctor');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $all_inserted = true;
        $relation_array = array();


        //exit;

        if (!empty($json_data['doctor_list'])) {
            // data array
            $doctor_array = array();

            //pr($json_data['doctor_list']);
            //exit;


            foreach ($json_data['doctor_list'] as $val) {
                if (is_numeric($val['market_id'])) {
                    $market_id = $val['market_id'];
                } else {
                    $market_info = $this->Market->find('first', array(
                        'conditions' => array('Market.temp_id' => $val['market_id'])
                    ));
                    $market_id = $market_info['Market']['id'];
                }

                if (is_numeric($val['outlet_id'])) {
                    $outlet_id = $val['outlet_id'];
                } else {
                    $outlet_info = $this->Outlet->find('first', array(
                        'conditions' => array('Outlet.temp_id' => $val['outlet_id'])
                    ));
                    $outlet_id = $outlet_info['Outlet']['id'];
                }

                /* if(is_int($val['doctor_id'])){
                  $data['id'] = $val['doctor_id'];
                  }else{
                  $data['temp_id'] = $val['doctor_id'];
                  } */

                $data['temp_id'] = $val['doctor_id'];

                $data['name'] = $val['doctor_name'];
                $data['doctor_qualification_id'] = $val['qualification_id'];
                $data['doctor_type_id'] = $val['doctor_type_id'];
                $data['gender'] = $val['sex'];
                $data['territory_id'] = $val['territory_id'];
                $data['market_id'] = $market_id;
                $data['outlet_id'] = $outlet_id;
                $data['clinic_name'] = $val['clinic_name'];
                $data['created_at'] = $this->current_datetime();
                $data['created_by'] = $val['so_id'];
                $data['updated_at'] = $this->current_datetime();
                $data['updated_by'] = $val['so_id'];



                if (is_numeric($val['doctor_id'])) {
                    $data['id'] = $val['doctor_id'];
                    if ($this->Doctor->save($data)) {
                        unset($data['id']);
                        $relation_array['new_id'] = $val['doctor_id'];
                        $relation_array['previous_id'] = $val['doctor_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
                } else {
                    $this->Doctor->create();
                    if ($this->Doctor->save($data)) {
                        $relation_array['new_id'] = $this->Doctor->getLastInsertID();
                        $relation_array['previous_id'] = $val['doctor_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
                }
            }

            $res['status'] = 1;
            if ($all_inserted) {
                $res['message'] = 'Doctor has been created successfuly completed.';
            } else {
                $res['message'] = 'One or More Doctor Failed to create.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }

        $this->set(array(
            'doctors' => $res,
            '_serialize' => array('doctors')
        ));
    }

    public function create_doctor_visit()
    {
        $this->loadModel('Doctor');
        $this->loadModel('DoctorVisit');
        $this->loadModel('DoctorVisitDetail');
        $this->loadModel('Store');

        $all_inserted = true;
        $relation_array = array();

        $json_data = $this->request->input('json_decode', true);
        $path = APP . 'logs/';
        $myfile = fopen($path . "create_doctor_visit.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        if (!empty($json_data['visit_list'])) {
            // data array

            foreach ($json_data['visit_list'] as $val) {
                /*$store_info = $this->Store->find('first', array(
                    'condition' => array('Store.office_id' => $val['office_id'], 'Store.store_type_id' => 2),
                    'fields' => array('id'),
                    'recursive' => -1
                ));*/

                $store_info = $this->Store->find('first', array(
                    'conditions' => array('Store.territory_id' => $val['territory_id'], 'Store.store_type_id' => 3),
                    'fields' => array('id'),
                    'recursive' => -1
                ));

                $stock_checking_array = array();
                foreach ($val['visit_details'] as $element) {
                    $stock_checking_array[$element['product_id']] = (isset($stock_checking_array[$element['product_id']]) ? $stock_checking_array[$element['product_id']] : 0) + $element['quantity'];
                }

                $stock_okay = $this->stock_check_for_validation_for_other($store_info['Store']['id'], $stock_checking_array);

                if (!$stock_okay) {
                    $relation_array['previous_id'] = $val['visit_id'];
                    $relation_array['messege'] = "Stock Not Available";
                    $res['replaced_relation'][] = $relation_array;
                    unset($relation_array);
                    continue;
                }

                if (is_numeric($val['doctor_temp_id'])) {
                    $market_id = $val['market_id'];
                    $doctor_id = $val['doctor_temp_id'];
                } else {
                    $doctor_info = $this->Doctor->find('first', array(
                        'conditions' => array('Doctor.temp_id' => $val['doctor_id'])
                    ));
                    $market_id = $doctor_info['Doctor']['market_id'];
                }
                if ($val['clinic_name']) {
                    $outlet_id = '';
                    $clinic_name = $val['clinic_name'];
                } else {
                    $outlet_id = $val['outlet_id'];
                    $clinic_name = '';
                }
                $val['visit_date_time'] = str_replace('p.m.', 'pm', $val['visit_date_time']);
                $data['temp_id'] = $val['visit_id'];
                $data['territory_id'] = $val['territory_id'];
                $data['market_id'] = $market_id;
                $data['doctor_id'] = $doctor_id;
                $data['outlet_id'] = $outlet_id;
                $data['visit_date'] = $val['visit_date'];
                $data['visit_date_time'] = $val['visit_date_time'];
                $data['latitude'] = $val['latitude'];
                $data['longitude'] = $val['longitude'];
                $data['created_at'] = $this->current_datetime();
                $data['created_by'] = $val['so_id'];
                $data['place_of_visit'] = $val['place_of_visit'];
                $data['night_halting'] = $val['night_halting'];
                $data['clinic_name'] = $clinic_name;

                if (!is_numeric($val['visit_id'])) {
                    $prev_doctor_visit = $this->DoctorVisit->find('first', array(
                        'conditions' => array('DoctorVisit.temp_id' => $val['visit_id']),
                        'recursive' => -1
                    ));
                    if ($prev_doctor_visit) {
                        $relation_array['new_id'] = $prev_doctor_visit['DoctorVisit']['id'];
                        $relation_array['previous_id'] = $val['visit_id'];
                        $res['replaced_relation'][] = $relation_array;
                        continue;
                    }
                }
                $this->DoctorVisit->create();
                if ($this->DoctorVisit->save($data)) {
                    $details_array = array();
                    foreach ($val['visit_details'] as $vd) {
                        $vdata['doctor_visit_id'] = $this->DoctorVisit->id;
                        $vdata['product_id'] = $vd['product_id'];
                        $vdata['product_type'] = $vd['product_type'];
                        $vdata['quantity'] = $vd['quantity'];
                        $details_array[] = $vdata;
                        // update stock
                        $this->update_stock_by_product_id($store_info['Store']['id'], $vd['product_id'], $vd['quantity'], 'deduct', 30, $val['visit_date']);
                    }

                    $this->DoctorVisitDetail->saveAll($details_array);
                    unset($details_array);

                    $relation_array['new_id'] = $this->DoctorVisit->getLastInsertID();
                    $relation_array['previous_id'] = $val['visit_id'];
                    $res['replaced_relation'][] = $relation_array;
                } else {
                    $all_inserted = false;
                }
            }

            $res['status'] = 1;
            if ($all_inserted) {
                $res['message'] = 'Doctor visit has been created successfuly completed.';
            } else {
                $res['message'] = 'One or More Doctor visit Failed to create.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }

        $this->set(array(
            'doctor_visit' => $res,
            '_serialize' => array('doctor_visit')
        ));
    }

    /*
     * Session
     * @return json 
     */

    function get_doctor_visit_list()
    {
        $this->loadModel('DoctorVisit');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        //$last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('DoctorVisit.sales_person_id' => $so_id);
        } else {
            $conditions = array('DoctorVisit.sales_person_id' => $so_id, 'DoctorVisit.action >' => 0);
        }
        $visit_list = $this->DoctorVisit->find('all', array(
            'fields' => array('DoctorVisit.*', 'DoctorVisitDetail.*'),
            'conditions' => $conditions,
            'order' => array('DoctorVisit.created_at' => 'asc'),
            'recursive' => 1
        ));

        foreach ($visit_list as $key => $val) {
            $visit_list[$key]['DoctorVisit']['action'] = 1;
        }

        $this->set(array(
            'visit_list' => $visit_list,
            '_serialize' => array('visit_list')
        ));
    }

    // end get_doctor_visit_list

    /* ------------------- End Doctor ---------------------- */


    /* ------------------- create deposit ------------------ */

    public function create_deposit_bcup()
    {
        $this->loadModel('Deposit');
        $this->loadModel('BankBranch');
        $this->loadModel('Memo');
        $this->loadModel('MemoDetail');
        $this->loadModel('Week');
        $this->loadModel('Collection');
        $this->loadModel('CollectionDepositLink');
        $so_id = 0;
        $json_data = $this->request->input('json_decode', true);
        // pr($json_data);die;
        $path = APP . 'logs/';
        $myfile = fopen($path . "create_deposit.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
        if (!empty($json_data['deposit_list'])) {
            foreach ($json_data['deposit_list'] as $val) {
                $memo_info = array();
                $memo_info = $this->Memo->find('first', array(
                    'conditions' => array('Memo.memo_no' => $val['memo_id']),
                    'fields' => array('id'),
                    'recursive' => -1
                ));
                $so_id = $val['so_id'];
                $data['memo_id'] = $memo_info ? $memo_info['Memo']['id'] : 0;
                $data['memo_no'] = $val['memo_id'];
                $data['sales_person_id'] = $val['so_id'];
                $data['deposit_amount'] = $val['deposit_amount'];
                $data['deposit_date'] = $val['deposit_date'];
                $data['slip_no'] = $val['slip_no'];
                $data['instrument_type'] = $val['instrument_type_id'];
                $data['bank_branch_id'] = $val['bank_branch_id'];
                $data['post_deposit_id'] = $val['post_deposit_id'];
                $data['cleared_at'] = $this->current_date();
                $data['week_id'] = $val['week_id'];
                $this->Deposit->create();
                $this->Deposit->save($data);
                $deposit_id = $this->Deposit->getInsertID();

                $deposit_amount = $val['deposit_amount'];
                $collection = $this->Collection->find('all', array(
                    'conditions' => array('Collection.so_id' => $val['so_id'], 'Collection.is_settled' => 0),
                    'order' => array('Collection.id'),
                    'recursive' => -1
                ));

                $collection_data_array = array();
                $collection_deposit_data_array = array();
                foreach ($collection as $collection_data) {

                    $collection_amount = $collection_data['Collection']['collectionAmount'] - $collection_data['Collection']['deposit_amount'];
                    $data_collection['id'] = $collection_data['Collection']['id'];
                    if ($deposit_amount == 0) {
                        break;
                    } else if ($collection_amount <= $deposit_amount) {
                        $data_collection['deposit_amount'] = $collection_data['Collection']['deposit_amount'] + $collection_amount;
                        $data_collection['is_settled'] = 1;
                        $deposit_amount -= $collection_amount;
                        $data_collection_deposit['deposit_amount'] = $collection_amount;
                    } else {
                        $data_collection['deposit_amount'] = $collection_data['Collection']['deposit_amount'] + $deposit_amount;
                        $data_collection_deposit['deposit_amount'] = $deposit_amount;
                        $deposit_amount = 0;
                        $data_collection['is_settled'] = 0;
                    }
                    $data_collection_deposit['collection_id'] = $collection_data['Collection']['id'];
                    $data_collection_deposit['deposit_id'] = $deposit_id;
                    $collection_data_array[] = $data_collection;
                    $collection_deposit_data_array[] = $data_collection_deposit;
                    unset($data_collection);
                    unset($data_collection_deposit);
                }
                if ($this->Collection->saveAll($collection_data_array) && $this->CollectionDepositLink->saveAll($collection_deposit_data_array)) {
                    $res['status'] = 1;
                    $res['message'] = 'Deposit has been created successfuly.';
                    unset($collection_data_array);
                    unset($collection_deposit_data_array);
                } else {
                    $this->Deposit->delete($deposit_id);
                    $res['status'] = 0;
                    $res['message'] = 'Deposit not created.';
                }
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'No Data Received.';
        }
        $this->update_territory_wise_collection_deposit_balance($so_id);
        $this->set(array(
            'deposits' => $res,
            '_serialize' => array('deposits')
        ));
    }

    public function create_deposit($list = null)
    {
        $this->loadModel('Deposit');
        $this->loadModel('BankBranch');
        $this->loadModel('Memo');
        $this->loadModel('MemoDetail');
        $this->loadModel('Week');
        $all_inserted = true;
        $relation_array = array();
        $from_json = 1;
        $so_id = 0;
        if ($list) {
            $json_data['deposit_list'] = $list;
            $from_json = 0;
        } else {
            $json_data = $this->request->input('json_decode', true);
            $path = APP . 'logs/';
            $myfile = fopen($path . "create_deposit.txt", "a") or die("Unable to open file!");
            fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
            fclose($myfile);
            /*---------------------------- Mac check --------------------------------*/
            $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
            if (!$mac_check) {

                $mac['status'] = 0;
                $mac['message'] = 'Mac Id Not Match';
                $res = $mac;
                $this->set(array(
                    'mac' => $res,
                    '_serialize' => array('mac')
                ));
                return 0;
            }
        }

        if (!empty($json_data['deposit_list'])) {
            // data array
            // $deposit_array = array();
            foreach ($json_data['deposit_list'] as $val) {
                $data = array();
                /* $this->Week->recursive = 2;
                  $week_info = $this->Week->find('first',array(
                  'conditions' => array('Week.id' => $val['week_id'])
                  ));
                 */
                $so_id = $val['so_id'];
                $memo_info = array();
                $memo_info = $this->Memo->find('first', array(
                    'conditions' => array('Memo.memo_no' => $val['memo_id']),
                    'fields' => array('id'),
                    'recursive' => -1
                ));
                $so_id = $val['so_id'];

                //add new
                $this->loadModel('SalesPerson');
                $terrtory_id = $this->SalesPerson->find('first', array(
                    'fields' => array('SalesPerson.territory_id'),
                    'conditions' => array('SalesPerson.id' => $so_id),
                    'recursive' => -1
                ));
                //pr($terrtory_id);exit;
                $territory_id = $terrtory_id['SalesPerson']['territory_id'];
                $data['territory_id'] = $territory_id;
                //end add new

                $data['memo_id'] = $memo_info ? $memo_info['Memo']['id'] : 0;
                $data['memo_no'] = $val['memo_id'];
                $data['sales_person_id'] = $val['so_id'];
                $data['deposit_amount'] = $val['deposit_amount'];
                $data['deposit_date'] = $val['deposit_date'];
                $data['slip_no'] = $val['slip_no'];
                $data['payment_id'] = $val['payment_id'];
                //$data['instrument_type'] = $val['instrument_type'];
                $data['instrument_type'] = $val['instrument_type'] == 'null' ? NUll : $val['instrument_type'];
                $data['type'] = $val['type'];
                $data['instrument_ref_no'] = $val['instrument_ref_no'];
                $data['bank_branch_id'] = $val['bank_branch_id'];
                $data['post_deposit_id'] = $val['post_deposit_id'];
                //$data['instrument_clearing_date'] = $val['instrument_date'];
                $data['created_at'] = $this->current_date();
                //$data['remarks'] = 'Need Clarification';		
                //$data['fiscal_year_id'] = $week_info['Month']['FiscalYear']['id'];
                //$data['month_id'] = $week_info['Month']['id'];	
                $data['week_id'] = $val['week_id'];
                // $deposit_array[] = $data;
                if (is_numeric($val['deposit_id'])) {
                    $data['id'] = $val['deposit_id'];
                    if ($this->Deposit->save($data)) {
                        unset($data['id']);
                        $relation_array['new_id'] = $val['deposit_id'];
                        $relation_array['previous_id'] = $val['deposit_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
                } else {
                    $prev_deposit = $this->Deposit->find('first', array(
                        'conditions' => array('Deposit.sales_person_id' => $so_id, 'Deposit.temp_id' => $val['deposit_id']),
                        'recursive' => -1
                    ));
                    if ($prev_deposit) {
                        $relation_array['new_id'] = $prev_deposit['Deposit']['id'];
                        $relation_array['previous_id'] = $val['deposit_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $data['temp_id'] = $val['deposit_id'];
                        $this->Deposit->create();
                        if ($this->Deposit->save($data)) {
                            $relation_array['new_id'] = $this->Deposit->getLastInsertID();
                            $relation_array['previous_id'] = $val['deposit_id'];
                            $res['replaced_relation'][] = $relation_array;
                        } else {
                            $all_inserted = false;
                        }
                    }
                }
            }
            $res['status'] = 1;
            if ($all_inserted) {

                $res['message'] = 'Deposit has been created successfuly completed.';
            } else {

                $res['message'] = 'One or More Deposit Failed to create.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'No Data Received.';
        }
        $this->update_territory_wise_collection_deposit_balance($so_id);
        $path = APP . 'logs/';
        $myfile = fopen($path . "create_deposit_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . json_encode($res));
        fclose($myfile);
        if ($from_json == 0) {
            return $res;
        } else {
            $this->set(array(
                'deposit' => $res,
                '_serialize' => array('deposit')
            ));
        }
    }

    /* ------------------- create Collection ------------------ */

    public function create_collection()
    {
        $this->loadModel('Collection');
        $json_data = $this->request->input('json_decode', true);



        if (!empty($json_data['collection_list'])) {
            // data array
            $collection_array = array();
            foreach ($json_data['collection_list'] as $val) {
                /*
                 * This condition added for data synchronization 
                 * Cteated by imrul in 09, April 2017
                 * Duplicate collection check
                 */
                $count = $this->Collection->find('count', array(
                    'conditions' => array(
                        'Collection.memo_id' => $val['memo_id']
                    )
                ));
                //---------------------------------------

                if ($count == 0) {
                    $data['memo_id'] = $val['memo_id'];
                    $data['is_credit_collection'] = $val['is_credit_collection'];
                    $data['instrument_type'] = $val['instrument_type'];
                    $data['bank_account_id'] = $val['bank_account_id'];
                    $data['instrumentRefNo'] = $val['instrumentRefNo'];
                    $data['instrument_date'] = $val['instrument_date'];
                    $data['collectionAmount'] = $val['collectionAmount'];
                    $data['collectionDate'] = $val['cllectionDate'];
                    $data['deposit_id'] = $val['deposit_id'];
                    $data['created_at'] = $this->current_datetime();
                    $collection_array[] = $data;
                }
            }

            if ($this->Collection->saveAll($collection_array)) {
                $res['status'] = 1;
                $res['message'] = 'Collection has been created successfuly.';
            } else {
                $res['status'] = 0;
                $res['message'] = 'Collection not created.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'No Data Received.';
        }

        $this->set(array(
            'collections' => $res,
            '_serialize' => array('collections')
        ));
    }

    /* ------------------- create payment ------------------ */
    //  change list variable as parameter 
    //  if collection pushed with memo that's why using this variable
    public function create_payment($list = null)
    {
        $this->loadModel('Collection');
        $this->loadModel('Memo');
        $this->loadModel('SalesPerson');
        $so_id = 0;
        $from_json = 1;
        $relation_array = array();
        $all_inserted = true;
        if ($list) {
            $json_data['payment_list'] = $list;
            $from_json = 0;
        } else {
            $path = APP . 'logs/';
            $myfile = fopen($path . "create_collection.txt", "a") or die("Unable to open file!");
            fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
            fclose($myfile);
            $json_data = $this->request->input('json_decode', true);
            /*---------------------------- Mac check --------------------------------*/
            $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
            if (!$mac_check) {

                $mac['status'] = 0;
                $mac['message'] = 'Mac Id Not Match';
                $res = $mac;
                $this->set(array(
                    'mac' => $res,
                    '_serialize' => array('mac')
                ));
                return 0;
            }
        }

        // pr($json_data);exit;
        if (!empty($json_data['payment_list'])) {
            // data array
            $collection_array = array();
            foreach ($json_data['payment_list'] as $val) {
                /*
                 * This condition added for data synchronization 
                 * Cteated by imrul in 09, April 2017
                 * Duplicate payment collection check
                 */
                /* $count = $this->Collection->find('count', array(
                  'conditions' => array(
                  'Collection.memo_id' => $val['memo_no']
                  )
                  )); */
                //------------------------
                $so_id = $val['so_id'];
                $territory_id = $this->SalesPerson->find('first', array(
                    'fields' => array('SalesPerson.territory_id'),
                    'conditions' => array('SalesPerson.id' => $so_id),
                    'recursive' => -1
                ));
                $territory_id = $territory_id['SalesPerson']['territory_id'];
                //if ($count == 0) {
                $data['so_id'] = $val['so_id'];
                $data['territory_id'] = $territory_id;
                $data['outlet_id'] = $val['outlet_id'];


                /*  memo_id and memo_no relation rebuilt start */

                $memo_info = array();
                $memo_info = $this->Memo->find('first', array(
                    'conditions' => array('Memo.memo_no' => $val['memo_no']),
                    'fields' => array('id', 'memo_date'),
                    'recursive' => -1
                ));
                if (empty($memo_info)) {
                    continue;
                }
                $data['memo_id'] = $memo_info['Memo']['id'];
                $data['memo_no'] = $val['memo_no'];

                /*  memo_id and memo_no relation rebuilt end */
                $data['memo_value'] = $val['memo_value'];
                $data['credit_or_due'] = $val['credit_or_due'];
                $data['memo_date'] = $memo_info['Memo']['memo_date'];
                $data['instrument_type'] = $val['inst_type_id'] == 0 ? $val['type'] : $val['inst_type_id'];
                $data['type'] = $val['type'];
                $data['bank_account_id'] = ($val['bank_branch_id'] && $val['bank_branch_id'] != "null") ? $val['bank_branch_id'] : '';
                $data['tax_ammount'] = $val['tax_amount'] ? $val['tax_amount'] : " ";
                $data['tax_no'] = $val['tax_no'] ? $val['tax_no'] : " ";
                $data['instrument_no'] = $val['instrument_no'] ? $val['instrument_no'] : " ";
                $data['payment_id'] = $val['payment_id'] ? $val['payment_id'] : " ";
                $data['instrumentRefNo'] = $val['inst_no'];
                $data['collectionAmount'] = $val['payment_amount'];
                $data['collectionDate'] = $val['collect_date'];
                $data['is_credit_collection'] = isset($val['is_credit_collection']) ? $val['is_credit_collection'] : '';
                $data['created_at'] = $this->current_datetime();
                /* $collection_array[] = $data; */
                if (is_numeric($val['payment_temp_id'])) {
                    $data['id'] = $val['payment_temp_id'];
                    if ($this->Collection->save($data)) {
                        unset($data['id']);
                        $relation_array['new_id'] = $val['payment_temp_id'];
                        $relation_array['previous_id'] = $val['payment_temp_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
                } else {
                    $data['temp_id'] = $val['payment_temp_id'];
                    $colection_exist = $this->Collection->find('first', array(
                        'conditions' => array(
                            'Collection.temp_id' => $val['payment_temp_id'],
                            'Collection.memo_id' => $memo_info['Memo']['id']
                        ),
                        'recursive' => -1
                    ));

                    if ($colection_exist) {
                        $relation_array['new_id'] = $colection_exist['Collection']['id'];
                        $relation_array['previous_id'] = $val['payment_temp_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $this->Collection->create();
                        if ($this->Collection->save($data)) {
                            $relation_array['new_id'] = $this->Collection->getLastInsertID();
                            $relation_array['previous_id'] = $val['payment_temp_id'];
                            $res['replaced_relation'][] = $relation_array;
                        } else {
                            $all_inserted = false;
                        }
                    }
                }
                // }
            }

            /* if ($this->Collection->saveAll($collection_array)) {
              $res['status'] = 1;
              $res['message'] = 'Payment has been created successfuly.';
              } else {
              $res['status'] = 0;
              $res['message'] = 'Payment not created.';
              } */

            if ($all_inserted) {

                $res['message'] = 'Collection has been created successfuly completed.';
            } else {

                $res['message'] = 'One or More Collection Failed to create.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'No Data Received.';
        }
        $this->update_territory_wise_collection_deposit_balance($so_id);
        $path = APP . 'logs/';
        $myfile = fopen($path . "create_collection_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . json_encode($res));
        fclose($myfile);
        if ($from_json == 0) {
            return $res;
        } else {
            $this->set(array(
                'payments' => $res,
                '_serialize' => array('payments')
            ));
        }
    }

    /*
     * Get return challan 
     * @return json 
     */

    public function get_inventory_status()
    {
        $this->loadModel('InventoryStatus');

        $inventory_status = $this->InventoryStatus->find('all', array(
            'fields' => array('InventoryStatus.id', 'InventoryStatus.name'),
            'order' => array('InventoryStatus.id' => 'asc'),
            'recursive' => -1
        ));
        $this->set(array(
            'inventory_status' => $inventory_status,
            '_serialize' => array('inventory_status')
        ));
    }

    // end function get_inventory_status
    // Return module 

    /*
     * Get return challan 
     * @return json 
     */

    public function get_return_challan()
    {
        $this->loadModel('Product');
        $this->loadModel('ReturnChallan');
        $this->loadModel('ReturnChallanDetail');

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $store_id = $json_data['store_id'];
        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'ReturnChallan.sender_store_id' => $store_id
            );
        } else {
            $conditions = array(
                'ReturnChallan.sender_store_id' => $store_id,
                'ReturnChallan.updated_at >' => $last_update_date
            );
        }

        $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'return_measurement_unit_id'), 'recursive' => -1));
        $product_list = Set::extract($products, '{n}.Product');

        $challans = $this->ReturnChallan->find('all', array(
            'conditions' => $conditions,
            'fields' => array('ReturnChallan.*'),
            'order' => array('ReturnChallan.updated_at' => 'asc'),
            'recursive' => 1
        ));


        $challan_array = array();
        if (!empty($challans)) {
            foreach ($challans as $val) {
                $data['id'] = $val['ReturnChallan']['id'];
                $data['challan_no'] = $val['ReturnChallan']['challan_no'];
                $data['challan_type'] = $val['ReturnChallan']['challan_type'];
                $data['challan_date'] = $val['ReturnChallan']['challan_date'];
                $data['remarks'] = $val['ReturnChallan']['remarks'];
                $data['sender_store_id'] = $val['ReturnChallan']['sender_store_id'];
                $data['transaction_type_id'] = $val['ReturnChallan']['transaction_type_id'];
                $data['status'] = $val['ReturnChallan']['status'];
                $data['receiver_store_id'] = $val['ReturnChallan']['receiver_store_id'];
                $data['received_date'] = $val['ReturnChallan']['received_date'];
                $data['created_at'] = $val['ReturnChallan']['created_at'];
                $data['created_by'] = $val['ReturnChallan']['created_by'];
                $data['updated_at'] = $val['ReturnChallan']['updated_at'];
                $data['updated_by'] = $val['ReturnChallan']['updated_by'];
                $data['inventory_status_id'] = $val['ReturnChallan']['inventory_status_id'];
                $data['action'] = 1;

                $challan_details_array = array();
                foreach ($val['ReturnChallanDetail'] as $cd) {

                    $units = $this->search_array($cd['product_id'], 'id', $product_list);
                    if ($units['sales_measurement_unit_id'] == $units['return_measurement_unit_id']) {
                        $quantity = $cd['challan_qty'];
                    } else {
                        $quantity = $this->convert_unit_to_unit($cd['product_id'], $units['return_measurement_unit_id'], $units['sales_measurement_unit_id'], $cd['challan_qty']);
                    }

                    $details_data['id'] = $cd['id'];
                    $details_data['challan_id'] = $cd['challan_id'];
                    $details_data['product_id'] = $cd['product_id'];
                    $details_data['measurement_unit_id'] = $units['sales_measurement_unit_id'];
                    $details_data['challan_qty'] = $quantity;
                    $details_data['batch_no'] = $cd['batch_no'];
                    $details_data['expire_date'] = $cd['expire_date'];
                    $details_data['inventory_status_id'] = $cd['inventory_status_id'];
                    $details_data['remarks'] = $cd['remarks'];
                    $challan_details_array[] = $details_data;
                }

                $data['ChallanDetail'] = $challan_details_array;
                $challan_array[] = $data;
            }
        }
        $this->set(array(
            'return_challans' => $challan_array,
            '_serialize' => array('return_challans')
        ));
    }

    // End function get_return_challan()


    /*
     * Create return challan 
     * @return json 
     */

    public function create_return_challan()
    {
        $this->loadModel('Product');
        $this->loadModel('ReturnChallan');
        $this->loadModel('ReturnChallanDetail');
        $this->loadModel('Store');

        $path = APP . 'logs/';
        $myfile = fopen($path . "create_return_challan.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        $json_data = $this->request->input('json_decode', true);
        $json_data = $json_data['challan_list'];
        $cdata = $json_data;

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }


        $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'return_measurement_unit_id'), 'recursive' => -1));
        $product_list = Set::extract($products, '{n}.Product');



        if (!empty($cdata)) {

            $prev_return_challan = $this->ReturnChallan->find(
                'first',
                array(
                    'conditions' => array('ReturnChallan.temp_id' => $cdata['temp_id']),
                    'recursive' => -1
                )
            );
            if ($prev_return_challan) {
                $res['status'] = 1;
                $res['message'] = 'Return challan Already Exist!!!';
            } else {
                $store_info = $this->Store->find('first', array(
                    'fields' => array('Store.id'),
                    'conditions' => array('Store.office_id' => $cdata['office_id']),
                    'recursive' => -1
                ));

                $challan_data['transaction_type_id'] = 9; // SO TO ASO (Return)
                $challan_data['inventory_status_id'] = $cdata['inventory_status_id'];
                $challan_data['temp_id'] = $cdata['temp_id'];
                $challan_data['challan_date'] = $cdata['challan_date'];
                $challan_data['sender_store_id'] = $cdata['store_id'];
                $challan_data['receiver_store_id'] = (!empty($store_info) ? $store_info['Store']['id'] : 0);
                $challan_data['status'] = 1;
                $challan_data['remarks'] = $cdata['challan_details'][0]['remarks'];
                $challan_data['created_at'] = $this->current_datetime();
                $challan_data['created_by'] = $cdata['so_id'];
                $challan_data['updated_at'] = $this->current_datetime();
                $challan_data['updated_by'] = 0;

                if ($this->ReturnChallan->save($challan_data)) {

                    $udata['id'] = $this->ReturnChallan->id;
                    $udata['challan_no'] = 'RCH' . (10000 + $this->ReturnChallan->id);
                    $this->ReturnChallan->save($udata);

                    $data_array = array();
                    foreach ($cdata['challan_details'] as $val) {

                        $punits = $this->search_array($val['product_id'], 'id', $product_list);
                        if ($punits['sales_measurement_unit_id'] == $punits['return_measurement_unit_id']) {
                            $quantity = $val['quantity'];
                        } else {
                            $quantity = $this->convert_unit_to_unit($val['product_id'], $punits['sales_measurement_unit_id'], $punits['return_measurement_unit_id'], $val['quantity']);
                        }

                        $data['challan_id'] = $this->ReturnChallan->id;
                        $data['product_id'] = $val['product_id'];
                        $data['measurement_unit_id'] = $val['measurement_unit'];
                        $data['challan_qty'] = $quantity;
                        $data['batch_no'] = $val['batch_no'];
                        $data['expire_date'] = $val['expire_date'];
                        $data['inventory_status_id'] = $cdata['inventory_status_id'];
                        //$data['remarks'] = $val['remarks'];
                        $data_array[] = $data;
                    }

                    // insert challan details data
                    $this->ReturnChallanDetail->saveAll($data_array);
                }

                $res['status'] = 1;
                $res['message'] = 'Return challan has been created successfuly.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'Challan not insert.';
        }

        $this->set(array(
            'return_challan' => $res,
            '_serialize' => array('return_challan')
        ));
    }

    // End function create_return_challan
    //------------------ Start NCP module --------------------

    /*
     * get so ncp challan list
     * @return json 
     */
    public function get_so_ncp_challan_list()
    {
        $this->loadModel('ReturnChallan');
        $this->loadModel('ReturnChallanDetail');

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $store_id = $json_data['store_id'];
        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'ReturnChallan.sender_store_id' => $store_id,
                'ReturnChallan.inventory_status_id' => 2
            );
        } else {
            $conditions = array(
                'ReturnChallan.sender_store_id' => $store_id,
                'ReturnChallan.inventory_status_id' => 2,
                'ReturnChallan.updated_at >' => $last_update_date
            );
        }

        $challans = $this->ReturnChallan->find('all', array(
            'conditions' => $conditions,
            'fields' => array('ReturnChallan.*'),
            'order' => array('ReturnChallan.updated_at' => 'asc'),
            'recursive' => 1
        ));


        $challan_array = array();

        if (!empty($challans)) {
            foreach ($challans as $val) {
                $data['id'] = $val['ReturnChallan']['id'];
                $data['challan_no'] = $val['ReturnChallan']['challan_no'];
                $data['challan_type'] = $val['ReturnChallan']['challan_type'];
                $data['challan_date'] = $val['ReturnChallan']['challan_date'];
                $data['remarks'] = $val['ReturnChallan']['remarks'];
                $data['sender_store_id'] = $val['ReturnChallan']['sender_store_id'];
                $data['transaction_type_id'] = $val['ReturnChallan']['transaction_type_id'];
                $data['status'] = $val['ReturnChallan']['status'];
                $data['receiver_store_id'] = $val['ReturnChallan']['receiver_store_id'];
                $data['received_date'] = $val['ReturnChallan']['received_date'];
                $data['created_at'] = $val['ReturnChallan']['created_at'];
                $data['created_by'] = $val['ReturnChallan']['created_by'];
                $data['updated_at'] = $val['ReturnChallan']['updated_at'];
                $data['updated_by'] = $val['ReturnChallan']['updated_by'];
                $data['inventory_status_id'] = $val['ReturnChallan']['inventory_status_id'];

                $challan_details_array = array();
                foreach ($val['ReturnChallanDetail'] as $cd) {
                    $details_data['id'] = $cd['id'];
                    $details_data['challan_id'] = $cd['challan_id'];
                    $details_data['product_id'] = $cd['product_id'];
                    $details_data['measurement_unit_id'] = $cd['measurement_unit_id'];
                    $details_data['challan_qty'] = $cd['challan_qty'];
                    $details_data['batch_no'] = $cd['batch_no'];
                    $details_data['expire_date'] = $cd['expire_date'];
                    $details_data['inventory_status_id'] = $cd['inventory_status_id'];
                    $details_data['remarks'] = $cd['remarks'];
                    $challan_details_array[] = $details_data;
                }

                $data['ChallanDetail'] = $challan_details_array;
                $challan_array[] = $data;
            }
        }

        $this->set(array(
            'ncp_challans' => $challan_array,
            '_serialize' => array('ncp_challans')
        ));
    }

    // end function get_ncp_challan_list


    /*
     * Create so ncp return challan 
     * @return json 
     */
    public function create_so_ncp_return_challan()
    {
        $this->loadModel('Product');
        $this->loadModel('ReturnChallan');
        $this->loadModel('ReturnChallanDetail');
        $this->loadModel('CurrentInventory');
        $this->loadModel('Store');

        $json_data_post = $this->request->input('json_decode', true);


        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data_post['mac'], $json_data_post['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        $json_data_challan_list = $json_data_post['challan_list'];

        $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
        $product_list = Set::extract($products, '{n}.Product');

        foreach ($json_data_challan_list as $json_data) {

            $prev_return_challan = $this->ReturnChallan->find(
                'first',
                array(
                    'conditions' => array('ReturnChallan.temp_id' => $json_data['temp_id']),
                    'recursive' => -1
                )
            );
            if ($prev_return_challan) {
                $res['temp_id'][] = $json_data['temp_id'];
                continue;
            }
            $store_info = $this->Store->find('first', array(
                'fields' => array('Store.id'),
                'conditions' => array('Store.office_id' => $json_data['office_id']),
                'recursive' => -1
            ));

            //$challan_data['transaction_type_id'] = 5;   // 5 = so to aso
            $challan_data['transaction_type_id'] = 10;   // 10 = so to aso(NCP Return)
            $challan_data['inventory_status_id'] = 2; //  2 = NCP Inventory		
            $challan_data['challan_date'] = $json_data['challan_date'];
            $challan_data['temp_id'] = $json_data['temp_id'];
            $challan_data['sender_store_id'] = $json_data['store_id'];
            $challan_data['receiver_store_id'] = (!empty($store_info) ? $store_info['Store']['id'] : 0);
            $challan_data['status'] = 1;  // 1 = pending
            // $challan_data['remarks'] = $json_data['remarks'];
            $challan_data['created_at'] = $this->current_datetime();
            $challan_data['created_by'] = $json_data_post['so_id'];
            $challan_data['updated_at'] = $this->current_datetime();
            $challan_data['updated_by'] = 0;

            if ($this->ReturnChallan->save($challan_data)) {

                $udata['id'] = $this->ReturnChallan->id;
                $udata['challan_no'] = 'NCPCH' . (10000 + $this->ReturnChallan->id);
                $this->ReturnChallan->save($udata);

                $data_array = array();
                $insert_data_array = array();
                $update_data_array = array();

                foreach ($json_data['challan_details'] as $val) {

                    $punits = $this->search_array($val['product_id'], 'id', $product_list);

                    $data['challan_id'] = $this->ReturnChallan->id;
                    $data['product_id'] = $val['product_id'];
                    $data['measurement_unit_id'] = $punits['base_measurement_unit_id'];
                    $data['challan_qty'] = $val['quantity'];
                    $data['batch_no'] = $val['batch_no'];
                    $data['expire_date'] = $this->to_expire_date($val['expire_date']);
                    $data['inventory_status_id'] = 0;
                    $data_array[] = $data;
                }

                // insert challan details data
                $this->ReturnChallanDetail->saveAll($data_array);
                $res['temp_id'][] = $json_data['temp_id'];
            }
        }

        $res['status'] = 1;
        $res['message'] = 'NCP return challan has been created successfuly.';

        $this->set(array(
            'return_challan' => $res,
            '_serialize' => array('return_challan')
        ));
    }

    // End function create_return_challan()



    /*
     * get ncp challan list
     * @return json 
     */
    public function get_ncp_challan_list()
    {
        $this->loadModel('Challan');
        $this->loadModel('ChallanDetail');

        $json_data = $this->request->input('json_decode', true);


        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $store_id = $json_data['store_id'];
        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'Challan.receiver_store_id' => $store_id,
                'OR' => array(
                    array('Challan.transaction_type_id' => 17), // 17= ASO to SO (NCP Return)	
                    array('Challan.transaction_type_id' => 18), //18= ASO to SO (Receive NCP Return)
                ),
                'Challan.inventory_status_id' => 2
            );
        } else {
            $conditions = array(
                'Challan.receiver_store_id' => $store_id,
                'Challan.inventory_status_id' => 2,
                'Challan.updated_at >' => $last_update_date,
                'OR' => array(
                    array('Challan.transaction_type_id' => 17), // 17= ASO to SO (NCP Return)	
                    array('Challan.transaction_type_id' => 18), //18= ASO to SO (Receive NCP Return)
                ),
            );
        }

        $challans = $this->Challan->find('all', array(
            'conditions' => $conditions,
            'fields' => array('Challan.*'),
            'order' => array('Challan.updated_at' => 'asc'),
            'recursive' => 1
        ));

        $challan_array = array();
        $challan_details_array = array();

        if (!empty($challans)) {
            foreach ($challans as $val) {
                $data['id'] = $val['Challan']['id'];
                $data['challan_no'] = $val['Challan']['challan_no'];
                $data['challan_type'] = $val['Challan']['challan_type'];
                $data['challan_date'] = $val['Challan']['challan_date'];
                $data['remarks'] = $val['Challan']['remarks'];
                $data['sender_store_id'] = $val['Challan']['sender_store_id'];
                $data['transaction_type_id'] = $val['Challan']['transaction_type_id'];
                $data['status'] = $val['Challan']['status'];
                $data['receiver_store_id'] = $val['Challan']['receiver_store_id'];
                $data['received_date'] = $val['Challan']['received_date'];
                $data['created_at'] = $val['Challan']['created_at'];
                $data['created_by'] = $val['Challan']['created_by'];
                $data['updated_at'] = $val['Challan']['updated_at'];
                $data['updated_by'] = $val['Challan']['updated_by'];
                $data['inventory_status_id'] = $val['Challan']['inventory_status_id'];
                $data['action'] = 1;


                foreach ($val['ChallanDetail'] as $cd) {

                    $details_data['id'] = $cd['id'];
                    $details_data['challan_id'] = $cd['challan_id'];
                    $details_data['product_id'] = $cd['product_id'];
                    $details_data['measurement_unit_id'] = $cd['measurement_unit_id'];
                    $details_data['challan_qty'] = $cd['challan_qty'];
                    $details_data['batch_no'] = $cd['batch_no'];
                    $details_data['expire_date'] = $cd['expire_date'];
                    $details_data['inventory_status_id'] = $cd['inventory_status_id'];
                    $details_data['remarks'] = $cd['remarks'];
                    $challan_details_array[] = $details_data;
                }

                $data['ChallanDetail'] = $challan_details_array;
                unset($challan_details_array);
                $challan_array[] = $data;
                unset($data);
            }
        }

        $this->set(array(
            'ncp_challans' => $challan_array,
            '_serialize' => array('ncp_challans')
        ));
    }

    // end function get_ncp_challan_list


    /*
     * get ncp return challan received
     * @return json 
     */
    public function ncp_return_challan_received()
    {
        $this->loadModel('Product');
        $this->loadModel('CurrentInventory');
        $this->loadModel('ReturnChallan');
        $this->loadModel('Challan');
        $this->loadModel('ReturnChallanDetail');
        $this->loadModel('ChallanDetail');

        $json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        $json_data = $json_data['challan_list'];


        if (!empty($json_data)) {
            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');


            foreach ($json_data['challan'] as $val) {
                $challan_id = $val['challan_id'];
                $challan_details = $this->ChallanDetail->find('all', array('conditions' => array('challan_id' => $challan_id), 'recursive' => -1));

                $chalan['id'] = $val['challan_id'];
                $chalan['status'] = 2;
                $chllan['transaction_type_id'] = 18; // 18=ASO to SO (Receive NCP Return)
                $chalan['received_date'] = date('Y-m-d', strtotime($val['received_date']));
                $chalan['updated_by'] = $json_data['so_id'];
                $this->Challan->save($chalan);  // update ReturnChallan

                $insert_data_array = array();
                $update_data_array = array();

                if (!empty($challan_details)) {
                    foreach ($challan_details as $dval) {

                        $units = $this->search_array($dval['ChallanDetail']['product_id'], 'id', $product_list);

                        if ($units['sales_measurement_unit_id'] == $units['base_measurement_unit_id']) {
                            $quantity = $dval['ChallanDetail']['challan_qty'];
                        } else {
                            $quantity = $this->unit_convert($dval['ChallanDetail']['product_id'], $units['sales_measurement_unit_id'], $dval['ChallanDetail']['challan_qty']);
                        }

                        // ------------ stock update --------------------		
                        $inventory_info = $this->CurrentInventory->find('first', array(
                            'conditions' => array(
                                'CurrentInventory.store_id' => $json_data['store_id'],
                                'CurrentInventory.inventory_status_id' => 2,
                                'CurrentInventory.product_id' => $dval['ChallanDetail']['product_id'],
                                'CurrentInventory.batch_number' => $dval['ChallanDetail']['batch_no'],
                                'CurrentInventory.expire_date' => $dval['ChallanDetail']['expire_date']
                            ),
                            'recursive' => -1
                        ));

                        if (!empty($inventory_info)) {
                            $update_data['id'] = $inventory_info['CurrentInventory']['id'];
                            $update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $quantity;
                            $update_data['transaction_type_id'] = 18; //18= ASO to SO (Receive NCP Return)
                            $update_data['transaction_date'] = date('Y-m-d', strtotime($val['received_date']));
                            $update_data_array[] = $update_data;
                        } else {
                            $insert_data['store_id'] = $json_data['store_id'];
                            $insert_data['inventory_status_id'] = 2;
                            $insert_data['product_id'] = $dval['ChallanDetail']['product_id'];
                            $insert_data['batch_number'] = $dval['ChallanDetail']['batch_no'];
                            $insert_data['expire_date'] = $dval['ChallanDetail']['expire_date'];
                            $insert_data['qty'] = $dval['ChallanDetail']['challan_qty'];
                            $insert_data['transaction_type_id'] = 18; // ASO to SO (Receive NCP Return)
                            $insert_data['transaction_date'] = date('Y-m-d', strtotime($val['received_date']));
                            $insert_data_array[] = $insert_data;
                        }
                        // -------------------- end stock update ----------------------
                    }
                    // insert inventory data
                    $this->CurrentInventory->saveAll($insert_data_array);

                    // Update inventory data
                    $this->CurrentInventory->saveAll($update_data_array);
                }
                unset($chalan);
                unset($insert_data_array);
                unset($update_data_array);
            }
        }

        $res['status'] = 1;
        $res['message'] = 'NCP Challan has been send successfuly.';

        $this->set(array(
            'ncp_challan_received' => $res,
            '_serialize' => array('ncp_challan_received')
        ));
    }

    // end function ncp_return_challan_received()
    //------------------ Start NCP module --------------------

    /*
     * get claim list 
     * @return json 
     */
    public function get_claim_list()
    {
        $this->loadModel('Claim');
        $this->loadModel('ClaimDetail');

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $store_id = $json_data['store_id'];
        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array(
                'Claim.sender_store_id' => $store_id,
                'Claim.transaction_type_id' => 24 //claim (so to aso )
            );
        } else {
            $conditions = array(
                'Claim.sender_store_id' => $store_id,
                'Claim.transaction_type_id' => 24,
                'Claim.updated_at >' => $last_update_date
            );
        }

        $claims = $this->Claim->find('all', array(
            'conditions' => $conditions,
            'fields' => array('Claim.*'),
            'order' => array('Claim.updated_at' => 'asc'),
            'recursive' => 1
        ));


        $claim_array = array();
        if (!empty($claims)) {
            foreach ($claims as $val) {
                $data['id'] = $val['Claim']['id'];
                $data['claim_id'] = $val['Claim']['claim_no'];
                $data['challan_no'] = $val['Claim']['challan_id'];
                //$data['challan_type'] = $val['Claim']['challan_type'];
                $data['challan_date'] = $val['Claim']['challan_date'];
                $data['receiver_store_id'] = $val['Claim']['receiver_store_id'];
                $data['received_date'] = $val['Claim']['received_date'];
                $data['created_at'] = $val['Claim']['created_at'];
                $data['created_by'] = $val['Claim']['created_by'];
                $data['updated_at'] = $val['Claim']['updated_at'];
                $data['updated_by'] = $val['Claim']['updated_by'];

                $claim_details_array = array();
                foreach ($val['ClaimDetail'] as $cd) {
                    $details_data['id'] = $cd['id'];
                    $details_data['claim_id'] = $cd['claim_id'];
                    $details_data['product_id'] = $cd['product_id'];
                    $details_data['quantity'] = $cd['claim_qty'];
                    $details_data['claim_type'] = $cd['claim_type'];
                    $claim_details_array[] = $details_data;
                }

                $data['claim_details'] = $claim_details_array;
                $claim_array[] = $data;
            }
        }

        $this->set(array(
            'claims' => $claim_array,
            '_serialize' => array('claims')
        ));
    }

    // end function get_claim_list()


    /*
     * Create claim 
     * @return json 
     */
    public function create_claim()
    {
        $this->loadModel('Product');
        $this->loadModel('Claim');
        $this->loadModel('ClaimDetail');
        $this->loadModel('Store');

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $json_data = $json_data['claims_list'];

        $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
        $product_list = Set::extract($products, '{n}.Product');

        if (!empty($json_data)) {
            $claim_id = array();
            foreach ($json_data as $cdata) {
                $store_info = $this->Store->find('first', array(
                    'fields' => array('Store.id'),
                    'conditions' => array('Store.office_id' => $cdata['office_id']),
                    'recursive' => -1
                ));

                $claim_data['transaction_type_id'] = 24;   // 24 = so to aso(clim create)
                $claim_data['claim_no'] = $cdata['claim_id'];
                $claim_data['challan_id'] = $cdata['challan_no'];
                $claim_data['challan_date'] = $cdata['claim_date'];
                $claim_data['sender_store_id'] = $cdata['store_id'];
                $claim_data['receiver_store_id'] = (!empty($store_info) ? $store_info['Store']['id'] : 0);
                $claim_data['status'] = 1;  // 1 = pending
                $claim_data['created_at'] = $this->current_datetime();
                $claim_data['created_by'] = $cdata['claim_by'];
                $claim_data['updated_at'] = $this->current_datetime();
                $claim_data['updated_by'] = 0;

                $this->Claim->create();
                if ($this->Claim->save($claim_data)) {

                    $data_array = array();
                    foreach ($cdata['details'] as $val) {

                        $punits = $this->search_array($val['product_id'], 'id', $product_list);

                        $data['claim_id'] = $this->Claim->id;
                        $data['product_id'] = $val['product_id'];
                        $data['measurement_unit_id'] = $punits['base_measurement_unit_id'];
                        $data['batch_no'] = $val['batch_no'];
                        $data['expire_date'] = $val['expire_date'];
                        $data['claim_qty'] = $val['quantity'];
                        $data['claim_type'] = $val['claim_type'];
                        $data_array[] = $data;
                    }
                    // insert claim details data
                    $this->ClaimDetail->saveAll($data_array);

                    $claim_ids['new_id'] = $this->Claim->id;
                } else {
                    $claim_ids['new_id'] = '';
                }
                $claim_ids['claim_id'] = $cdata['claim_id'];

                $claim_id[] = $claim_ids;
            }
            $res['status'] = 1;
            $res['claim_id'] = $claim_id;
            $res['message'] = 'Claim has been created successfuly.';
        } else {
            $res['status'] = 0;
            $res['claim_id'] = array();
            $res['message'] = 'Claim not created.';
        }

        $this->set(array(
            'claims' => $res,
            '_serialize' => array('claims')
        ));
    }

    // End function create_claim()


    /*
     * Gift item
     * @return json 
     */
    public function giftitem_received()
    {
        $this->loadModel('Product');
        $this->loadModel('GiftItem');
        $this->loadModel('GiftItemDetail');
        $this->loadModel('SalesPerson');

        $json_data = $this->request->input('json_decode', true);

        $path = APP . 'logs/';
        $myfile = fopen($path . "gift_item_received.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $json_data = isset($json_data['item_list']) ? $json_data['item_list'] : '';

        if (!empty($json_data)) {

            foreach ($json_data as $val) {
                if ($val['memo_no'] != '') {
                    $prev_gift_issue = $this->GiftItem->find('first', array(
                        'conditions' => array('GiftItem.memo_no' => $val['memo_no'])
                    ));
                    $gift_id = null;
                    if ($prev_gift_issue) {
                        $gift_id = $prev_gift_issue['GiftItem']['id'];
                        $gift_item['id'] = $gift_id;
                    }

                    if (!$val['territory_id']) {
                        $territory_id_retrive = $this->SalesPerson->find('first', array('conditions' => array('SalesPerson.id' => $val['so_id']), 'recursive' => -1));

                        $val['territory_id'] = $territory_id_retrive['SalesPerson']['territory_id'];        //re-asign $json_data['territory_id'] if null
                    }

                    $gift_item['territory_id'] = $val['territory_id'];
                    $gift_item['memo_no'] = $val['memo_no'];
                    $gift_item['so_id'] = $val['so_id'];
                    $gift_item['gift_for'] = $val['gift_for'];
                    $gift_item['outlet_id'] = $val['outlet_id'];
                    $gift_item['doctor_visit_id'] = $val['doctor_visit_id'];
                    $gift_item['session_id'] = $val['session_id'];
                    $gift_item['date'] = $val['date'];
                    $gift_item['created_at'] = $this->current_datetime();
                    $gift_item['created_by'] = $val['so_id'];
                    $gift_item['updated_at'] = $this->current_datetime();
                    $gift_item['updated_by'] = $val['so_id'];

                    if ($val['details']) {
                        //$this->GiftItem->create();
                        if ($gift_id) {
                            $this->GiftItemDetail->deleteAll(array('gift_item_id' => $gift_id));
                        }
                        if ($this->GiftItem->saveAll($gift_item)) {
                            unset($gift_item);
                            $data_array = array();
                            foreach ($val['details'] as $dval) {
                                $gift_item_details['gift_item_id'] = $this->GiftItem->id;
                                $gift_item_details['product_id'] = $dval['product_id'];
                                $gift_item_details['quantity'] = $dval['quantity'];
                                $data_array[] = $gift_item_details;
                            }
                            $this->GiftItemDetail->saveAll($data_array);
                        }
                    }
                    unset($gift_item);
                } else {
                    unset($gift_item);
                    if (!@$val['territory_id']) {
                        $territory_id_retrive = $this->SalesPerson->find('first', array('conditions' => array('SalesPerson.id' => $val['so_id']), 'recursive' => -1));

                        $val['territory_id'] = $territory_id_retrive['SalesPerson']['territory_id'];        //re-asign $json_data['territory_id'] if null
                    }

                    $gift_item['territory_id'] = $val['territory_id'];
                    // $gift_item['memo_no'] = $val['memo_no'];
                    $gift_item['so_id'] = $val['so_id'];
                    $gift_item['gift_for'] = $val['gift_for'];
                    $gift_item['outlet_id'] = $val['outlet_id'];
                    $gift_item['doctor_visit_id'] = $val['doctor_visit_id'];
                    $gift_item['session_id'] = $val['session_id'];
                    $gift_item['date'] = $val['date'];
                    $gift_item['created_at'] = $this->current_datetime();
                    $gift_item['created_by'] = $val['so_id'];
                    $gift_item['updated_at'] = $this->current_datetime();
                    $gift_item['updated_by'] = $val['so_id'];

                    if ($val['details']) {

                        $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                        $product_list = Set::extract($products, '{n}.Product');
                        if (!is_numeric($val['temp_id'])) {
                            $prev_gift_item = $this->GiftItem->find('first', array(
                                'conditions' => array(
                                    'GiftItem.temp_id' => $val['temp_id']
                                ),
                                'recursive' => -1
                            ));
                            if ($prev_gift_item) {
                                $relation_array['new_id'] = $prev_gift_item['GiftItem']['id'];
                                $relation_array['previous_id'] = $val['temp_id'];
                                $res['replaced_relation'][] = $relation_array;
                            } else {
                                $stock_checking_array = array();
                                foreach ($val['details'] as $element) {
                                    $stock_checking_array[$element['product_id']] = (isset($stock_checking_array[$element['product_id']]) ? $stock_checking_array[$element['product_id']] : 0) + $element['quantity'];
                                }

                                $stock_okay = $this->stock_check_for_validation_for_other($val['store_id'], $stock_checking_array);

                                if (!$stock_okay) {
                                    $relation_array['previous_id'] = $val['temp_id'];
                                    $relation_array['messege'] = "Stock Not Available";
                                    $res['replaced_relation'][] = $relation_array;
                                    unset($relation_array);
                                    continue;
                                }
                                $gift_item['temp_id'] = $val['temp_id'];
                                $this->GiftItem->create();
                                if ($this->GiftItem->save($gift_item)) {
                                    unset($gift_item);
                                    $data_array = array();
                                    foreach ($val['details'] as $dval) {
                                        $gift_item_details['gift_item_id'] = $this->GiftItem->id;
                                        $gift_item_details['product_id'] = $dval['product_id'];
                                        $gift_item_details['quantity'] = $dval['quantity'];
                                        $data_array[] = $gift_item_details;

                                        /*-------------- For Stock Update:Start --------------------------*/
                                        $punits = $this->search_array($dval['product_id'], 'id', $product_list);
                                        if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
                                            $sale_quantity = $dval['quantity'];
                                        } else {
                                            $sale_quantity = $this->unit_convert($dval['product_id'], $punits['sales_measurement_unit_id'], $dval['quantity']);
                                        }
                                        $this->update_current_inventory($sale_quantity, $dval['product_id'], $val['store_id'], 'deduct', 11, $val['date']);
                                        /*-------------- For Stock Update:End ----------------------------*/
                                    }
                                    $this->GiftItemDetail->saveAll($data_array);
                                    $relation_array['new_id'] = $this->GiftItem->getLastInsertID();
                                    $relation_array['previous_id'] = $val['temp_id'];
                                    $res['replaced_relation'][] = $relation_array;
                                }
                            }
                        }
                    }
                }
                unset($gift_item);
            }
            $res['status'] = 1;
            $res['message'] = 'Gift item has been received successfuly.';
        } else {
            $res['status'] = 0;
            $res['message'] = 'No data found.';
        }

        $this->set(array(
            'giftitem_received' => $res,
            '_serialize' => array('giftitem_received')
        ));
    }

    // end gift_item_received



    /*
     * Session
     * @return json 
     */
    function get_sessions()
    {
        $this->loadModel('ProgramSession');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array('ProgramSession.so_id' => $so_id);
        } else {
            $conditions = array('ProgramSession.so_id' => $so_id, 'ProgramSession.action >' => 0);
        }
        $session_list = $this->ProgramSession->find('all', array(
            'conditions' => $conditions,
            'order' => array('ProgramSession.updated_at' => 'asc'),
            'recursive' => 1
        ));
        $updated_data_array = array();
        foreach ($session_list as $key => $val) {
            $update_data['ProgramSession']['id'] = $val['ProgramSession']['id'];
            $update_data['ProgramSession']['action'] = 0;
            $updated_data_array[] = $update_data;
            unset($update_data);
            $session_list[$key]['ProgramSession']['action'] = 1;
        }
        $this->ProgramSession->saveAll($updated_data_array);
        unset($updated_data_array);
        $this->set(array(
            'sessions' => $session_list,
            '_serialize' => array('sessions')
        ));
    }

    // end get_session


    /*
     * Update Session
     * @return json 
     */
    function update_sessions()
    {
        $this->loadModel('ProgramSession');
        $this->loadModel('SessionDetail');
        $json_data = $this->request->input('json_decode', true);
        $path = APP . 'logs/';
        $myfile = fopen($path . "update_sessions.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        if (!empty($json_data['session_list'])) {
            // data array
            $res['success'] = '';
            $res['remove'] = '';
            foreach ($json_data['session_list'] as $val) {
                $user_id = $this->get_user_id_from_so_id($val['so_id']);
                $session_attend = array();
                $session_attend = $this->ProgramSession->find('first', array(
                    'conditions' => array(
                        'AND' => array(
                            'OR' => array(
                                'NOT' => array('ProgramSession.total_attend' => NULL),
                                array('ProgramSession.total_attend >' => 0),
                            ),
                            array(
                                'ProgramSession.id' => $val['session_id']
                            )
                        )
                    )
                ));
                if (empty($session_attend) || (!empty($session_attend) && $session_attend['ProgramSession']['updated_by'] == $user_id)) {
                    $data['id'] = $val['session_id'];
                    $data['total_attend'] = $val['total_male'] + $val['total_female'];
                    $data['total_male'] = $val['total_male'];
                    $data['total_female'] = $val['total_female'];
                    $data['session_arranged_date'] = $val['session_arranged_date'];
                    $data['longitude'] = $val['longitude'];
                    $data['latitude'] = $val['latitude'];
                    $data['updated_at'] = $this->current_datetime();
                    $data['updated_by'] = $user_id;

                    $session_product = $this->SessionDetail->find('all', array(
                        'conditions' => array('SessionDetail.session_id' => $val['session_id']),
                        'recursive' => -1
                    ));
                    $stock_checking_array = array();
                    $prev_stock_array = array();
                    foreach ($val['session_details'] as $element) {
                        $stock_checking_array[$element['product_id']] = (isset($stock_checking_array[$element['product_id']]) ? $stock_checking_array[$element['product_id']] : 0) + $element['quantity'];
                    }

                    if (!empty($session_product)) {
                        foreach ($session_product as $session_data) {
                            $prev_stock_array[$session_data['SessionDetail']['product_id']] = (isset($prev_stock_array[$session_data['SessionDetail']['product_id']]) ? $prev_stock_array[$session_data['SessionDetail']['product_id']] : 0) + $session_data['SessionDetail']['quantity'];
                        }
                    }

                    $stock_okay = $this->stock_check_for_validation_for_other($val['store_id'], $stock_checking_array, $prev_stock_array);

                    if (!$stock_okay) {
                        $res['status'] = 2;
                        $res['message'] = 'Some Session Data Has Not Been Updated.';
                        continue;
                    }

                    if ($this->ProgramSession->save($data)) {


                        if (!empty($session_product)) {
                            $this->SessionDetail->deleteAll(array('SessionDetail.session_id' => $val['session_id']));
                            foreach ($session_product as $data) {
                                $this->update_current_inventory($data['SessionDetail']['quantity'], $data['SessionDetail']['product_id'], $val['store_id'], 'add', 20, $val['session_arranged_date']);
                            }
                        }

                        $details_array = array();
                        foreach ($val['session_details'] as $vd) {
                            $vdata['session_id'] = $val['session_id'];
                            $vdata['product_id'] = $vd['product_id'];
                            $vdata['quantity'] = $vd['quantity'];
                            $details_array[] = $vdata;

                            $this->update_current_inventory($vd['quantity'], $vd['product_id'], $val['store_id'], 'deduct', 21, $val['session_arranged_date']);
                        }
                        $this->SessionDetail->saveAll($details_array);
                        unset($details_array);
                    }
                    $res['status'] = 1;
                    $res['success'] .= $val['session_id'] . ',';
                    $res['message'] = 'Session has been updated.';
                } else {
                    $res['status'] = 1;
                    $res['remove'] .= $val['session_id'] . ',';
                    $res['message'] = 'Session has been updated.';
                }
            }
            $res['remove'] = rtrim($res['remove'], ',');
            $res['success'] = rtrim($res['success'], ',');
        } else {
            $res['status'] = 0;
            $res['message'] = 'Session Data Not Received.';
        }

        $this->set(array(
            'sessions' => $res,
            '_serialize' => array('sessions')
        ));
    }

    // end get_session


    /*
     * Day Close
     * @return json 
     */
    function create_day_close()
    {
        $this->loadModel('DayClose');
        $this->loadModel('SalesPerson');
        $json_data = $this->request->input('json_decode', true);


        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }


        $path = APP . 'logs/';
        $myfile = fopen($path . "create_day_close.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        if (!$json_data['territory_id']) {
            $territory_id_retrive = $this->SalesPerson->find('first', array('conditions' => array('SalesPerson.id' => $json_data['so_id']), 'recursive' => -1));

            $json_data['territory_id'] = $territory_id_retrive['SalesPerson']['territory_id']; //re-asign $json_data['territory_id'] if null
        }

        $data['territory_id'] = $json_data['territory_id'];
        $data['sales_person_id'] = $json_data['so_id'];
        $data['closing_date'] = $json_data['closing_date'];
        $data['closed_at'] = $json_data['closing_date'];


        $this->DayClose->create();
        if ($this->DayClose->save($data)) {
            $data_array['status'] = 1;
        } else {
            $data_array['status'] = 0;
        }
        $this->set(array(
            'day_close' => $data_array,
            '_serialize' => array('day_close')
        ));
    }

    // end create_day_close
    /*
     * Day Close get
     * @return json 
     */
    function get_day_close()
    {
        $this->loadModel('DayClose');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $day_closes = $this->DayClose->find(
            'first',
            array(
                'conditions' => array('DayClose.sales_person_id' => $so_id),
                'order' => array('DayClose.id' => 'DESC'),
                'recursive' => -1
            )
        );
        $day_close_data = array(
            "DayClose" => date('Y-m-d H:i:s', strtotime($day_closes['DayClose']['closed_at'])),
            "DayCloseDate" => $day_closes['DayClose']['closing_date'],
            "DayCloseAMPM" => date('Y-m-d h:i a', strtotime($day_closes['DayClose']['closed_at'])),
        );
        $this->set(array(
            'day_close_info' => $day_close_data,
            '_serialize' => array('day_close_info')
        ));
    }

    // end get json 

    /*
     * update stock
     * @return json 
     */
    function update_stock_by_product_id($store_id = '', $product_id = '', $quantity = '', $update_type, $transaction_type_id = '', $transaction_date = '')
    {
        $this->loadModel('Product');
        $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
        $product_list = Set::extract($products, '{n}.Product');

        $punits = $this->search_array($product_id, 'id', $product_list);
        if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
            $update_quantity = $quantity;
        } else {
            $update_quantity = $this->unit_convert($product_id, $punits['sales_measurement_unit_id'], $quantity);
        }
        $this->update_current_inventory($update_quantity, $product_id, $store_id, $update_type, $transaction_type_id, $transaction_date);
        return true;
    }

    // end update_stock_by_product_id




    /* ------------------- Start Target ---------------------- */
    /*
     * create_total_memo
     * @return json 
     */
    public function get_target_list()
    {
        $this->loadModel('SaleTargetMonth');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $territory_id = $json_data['territory_id'];
        $target_type = $json_data['target_type'];

        if ($target_type == 1) {
            $conditions = array('SaleTargetMonth.territory_id' => $territory_id, 'SaleTargetMonth.product_id != ' => 0);
        } else {
            $conditions = array('SaleTargetMonth.territory_id' => $territory_id, 'SaleTargetMonth.product_id' => 0);
        }


        $this->SaleTargetMonth->bindModel(
            array(
                'belongsTo' => array(
                    'Month' => array(
                        'className' => 'Month',
                        'foreignKey' => 'month_id',
                        'conditions' => '',
                        'fields' => '',
                        'order' => ''
                    )
                )
            )
        );

        $target_list = $this->SaleTargetMonth->find('all', array(
            'conditions' => $conditions,
            'recursive' => 1
        ));

        $data_array = array();
        $sales_target = array();
        $so_target = array();

        //pr($target_list);

        foreach ($target_list as $each_tl) {


            $data['id'] = $each_tl['SaleTargetMonth']['id'];
            $data['sale_target_id'] = $each_tl['SaleTargetMonth']['sale_target_id'];
            $data['aso_id'] = $each_tl['SaleTargetMonth']['aso_id'];
            $data['product_id'] = $each_tl['SaleTargetMonth']['product_id'];
            $data['fiscal_year_id'] = $each_tl['SaleTargetMonth']['fiscal_year_id'];
            $data['month_id'] = $each_tl['Month']['month'];
            $data['updated_at'] = $each_tl['SaleTargetMonth']['updated_at'];


            //product sales target 
            if ($target_type == 1) {
                $sales_target = $data;
                $sales_target['target_quantity'] = $each_tl['SaleTargetMonth']['target_quantity'];
                $sales_target['target_amount'] = $each_tl['SaleTargetMonth']['target_amount'];
                $sales_target['target_quantity_achievement'] = $each_tl['SaleTargetMonth']['target_quantity_achievement'];
                $sales_target['target_amount_achievement'] = $each_tl['SaleTargetMonth']['target_amount_achievement'];
                $data_array[]['sales_target'] = $sales_target;
            } else if ($target_type == 2) {
                $so_target = $data;
                $so_target['outlet_coverage_pharma'] = $each_tl['SaleTargetMonth']['outlet_coverage_pharma'];
                $so_target['outlet_coverage_non_pharma'] = $each_tl['SaleTargetMonth']['outlet_coverage_non_pharma'];
                $so_target['outlet_coverage_pharma_achievement'] = $each_tl['SaleTargetMonth']['outlet_coverage_pharma_achievement'];
                $so_target['outlet_coverage_non_pharma_achievement'] = $each_tl['SaleTargetMonth']['outlet_coverage_non_pharma_achievement'];

                $so_target['effective_call_pharma'] = $each_tl['SaleTargetMonth']['effective_call_pharma'];
                $so_target['effective_call_non_pharma'] = $each_tl['SaleTargetMonth']['effective_call_non_pharma'];
                $so_target['effective_call_pharma_achievement'] = $each_tl['SaleTargetMonth']['effective_call_pharma_achievement'];
                $so_target['effective_call_non_pharma_achievement'] = $each_tl['SaleTargetMonth']['effective_call_non_pharma_achievement'];

                $so_target['session'] = $each_tl['SaleTargetMonth']['session'];
                $so_target['session_achievement'] = $each_tl['SaleTargetMonth']['session_achievement'];
                $so_target['session_participent'] = 0;
                $so_target['session_participent_achievement'] = 0;
                unset($so_target['product_id']);
                $data_array[]['so_target'] = $so_target;
            }
        }



        $this->set(array(
            'target_list' => $data_array,
            '_serialize' => array('target_list')
        ));
    }

    /* ------------------- end Target ---------------------- */



    /* ------------------- Start Stamp Target ---------------------- */

    public function get_stamp_target_info()
    {
        $this->loadModel('StoreBonusCard');
        $this->loadModel('BonusCard');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $territory_id = $json_data['territory_id'];
        $current_date = date('Y-m-d');
        //$conditions = array('StoreBonusCard.territory_id' => $territory_id);

        // AND '2018-07-01' BETWEEN fy.start_date AND fy.end_date
        // AND '$current_date' BETWEEN fy.start_date AND fy.end_date
        /*AND '$current_date' BETWEEN fy.start_date AND fy.end_date*/
        $target_list = $this->StoreBonusCard->query("
            SELECT 
                territory_id,
                market_id,
                outlet_id,
                product_id,
                bonus_card_type_id,
                bonus_card_id,
                fy.id as fiscal_year_id,
                fy.year_code,
                sum(quantity) as achieve_quantity,
                sum(no_of_stamp) as stamp     
    		FROM store_bonus_cards sbc 
            inner join fiscal_years fy on fy.id=sbc.fiscal_year_id
            where 
                sbc.territory_id=$territory_id
                AND fy.start_date >= 
                (
                    SELECT fy_v.start_date
                    FROM app_versions app_v
                    inner join fiscal_years fy_v on fy_v.id=app_v.fiscal_year_id_for_bonus_report
                    WHERE app_v.id = 2
                )
               
            group by 
                territory_id,
                market_id,
                outlet_id,
                product_id,
                bonus_card_type_id,
                bonus_card_id,
                fy.id,
                fy.year_code");


        $data_array = array();
        // echo $this->StoreBonusCard->getLastquery();exit; 

        foreach ($target_list as $each_tl) {
            $each_tl = $each_tl[0];
            $data['territory_id'] = $each_tl['territory_id'];
            $data['market_id'] = $each_tl['market_id'];
            $data['outlet_id'] = $each_tl['outlet_id'];
            $data['product_id'] = $each_tl['product_id'];
            $data['fiscal_year_id'] = $each_tl['fiscal_year_id'];
            $data['bonus_party_id'] = $each_tl['bonus_card_type_id'];
            $data['achieve_quantity'] = $each_tl['achieve_quantity'];
            $data['stamp'] = $each_tl['stamp'];

            /* getting target quantity */
            $bonus_card_info = $this->BonusCard->find('first', array(
                'conditions' => array('BonusCard.id' => $each_tl['bonus_card_id']),
                'recursive' => -1
            ));
            $this->LoadModel('FiscalYear');
            $start_date_fiscal_year = $this->FiscalYear->query("SELECT fy_v.start_date
                    FROM app_versions app_v
                    inner join fiscal_years fy_v on fy_v.id=app_v.fiscal_year_id_for_bonus_report
                    WHERE app_v.id = 2");
            $start_date_fiscal_year = $start_date_fiscal_year[0][0]['start_date'];
            // pr($start_date_fiscal_year);exit;
            $fiscal_years = $this->FiscalYear->find('list', array(
                'fields' => array('year_code'),
                'conditions' => array('FiscalYear.start_date >=' => $start_date_fiscal_year),
                'recursive' => -1
            ));
            // pr($fiscal_years);exit;
            $fiscal_year_array = array();
            foreach ($fiscal_years as $key => $val) {
                $fiscal_year_array[] = array(
                    'fiscal_year_id' => $key,
                    'fiscal_year_code' => $val
                );
            }
            $data['target_quantity'] = $bonus_card_info['BonusCard']['min_qty_per_year'];
            $data_array[]['product_bonus_target'] = $data;
        }

        $this->set(array(
            'bonus_target' => $data_array,
            'fiscal_years' => $fiscal_year_array,
            '_serialize' => array('bonus_target', 'fiscal_years')
        ));
    }

    /* ------------------- end Stamp Target ---------------------- */




    /* ---------------------------- Ncp collection List ------------------------------------- */

    public function get_ncp_collection_list()
    {
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $this->LoadModel('NcpCollection');
        $collection_list = $this->NcpCollection->find(
            'all',
            array(
                'fields' => array('NcpCollection.so_id', 'NcpCollection.market_id', 'NcpCollection.outlet_id', 'NcpCollection.challan_no', 'NcpCollection.challan_date', 'NcpCollection.product_id', 'NcpCollection.batch_no', 'NcpCollection.exp_date', 'NcpCollection.unit_id', 'NcpCollection.product_category_id', 'NcpCollection.collected_qty', 'NcpCollection.return_qty', 'NcpCollection.remarks', 'NcpCollection.isPushed', 'NcpCollection.updated_at'),
                'conditions' => array('NcpCollection.so_id' => $so_id),
                'order' => array('NcpCollection.id Desc'),
                'recursive' => -1
            )
        );
        //pr($collection_list);
        $data_array = array();
        foreach ($collection_list as $data) {
            $data_array[] = $data['NcpCollection'];
        }
        $this->set(array(
            'ncp_collection' => $data_array,
            '_serialize' => array('ncp_collection')
        ));
    }

    public function update_ncp_collection_list()
    {
        $this->LoadModel('NcpCollection');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $data_array = array();
        if (!empty($json_data)) {
            foreach ($json_data['ncp_collection'] as $data) {
                /* checking data if exist on db */
                $ncp_list = $this->NcpCollection->find('first', array('conditions' => array('NcpCollection.so_id' => $data['so_id'], 'NcpCollection.market_id' => $data['market_id'], 'NcpCollection.outlet_id' => $data['outlet_id'], 'NcpCollection.challan_no' => $data['challan_no'], 'NcpCollection.product_id' => $data['product_id'], 'NcpCollection.batch_no' => $data['batch_no'], 'NcpCollection.exp_date' => $data['exp_date']), 'recursive' => -1));
                if (empty($ncp_list)) {
                    //$this->NcpCollection->create();
                    $data_array[] = $data;
                } else {
                    $data['id'] = $ncp_list['NcpCollection']['id'];
                    $data_array[] = $data;
                }
            }
            if ($this->NcpCollection->saveAll($data_array)) {
                unset($data_array);
                $res['status'] = 1;
                $res['message'] = 'Ncp Collection updated.';
            } else {
                $res['status'] = 0;
                $res['message'] = 'Ncp Collection Not fully updated.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'Not send data.';
        }
        $this->set(array(
            'ncp_collection' => $res,
            '_serialize' => array('ncp_collection')
        ));
    }

    /* -------------- End Ncp collection list -------------------------------------- */


    /* ------------------------- NCP So Stock  --------------------------------------- */

    public function get_ncp_stock()
    {
        $this->LoadModel('NcpSoStock');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $ncp_stock_list = $this->NcpSoStock->find('all', array(
            'fields' => array('NcpSoStock.so_id', 'NcpSoStock.product_id', 'NcpSoStock.batch_number', 'NcpSoStock.expire_date', 'NcpSoStock.m_unit', 'NcpSoStock.qty', 'NcpSoStock.product_category_id', 'NcpSoStock.product_type_id', 'NcpSoStock.current_inventory_id', 'NcpSoStock.updated_at'),
            'conditions' => array('NcpSoStock.so_id' => $so_id),
            'order' => 'NcpSoStock.id ASC',
            'recursive' => -1
        ));
        $data_array = array();
        foreach ($ncp_stock_list as $data) {
            $data_array[] = $data['NcpSoStock'];
        }
        $this->set(array(
            'ncp_so_stock' => $data_array,
            '_serialize' => array('ncp_so_stock')
        ));
    }

    public function update_ncp_stock()
    {
        $this->LoadModel('NcpSoStock');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        if (!empty($json_data)) {
            $data_array = array();
            foreach ($json_data['ncp_so_stock'] as $data) {
                /* checking data if exist on db */
                $ncp_stock_list = $this->NcpSoStock->find('first', array(
                    'conditions' => array('NcpSoStock.so_id' => $data['so_id'], 'NcpSoStock.product_id' => $data['product_id'], 'NcpSoStock.batch_number' => $data['batch_number'], 'NcpSoStock.expire_date' => $data['expire_date']),
                    'recursive' => -1
                ));
                if (!empty($ncp_stock_list)) {
                    $data['id'] = $ncp_stock_list['NcpSoStock']['id'];
                    $data_array[] = $data;
                } else {

                    $data_array[] = $data;
                }
            }
            if ($this->NcpSoStock->saveAll($data_array)) {
                unset($data_array);
                $res['status'] = 1;
                $res['message'] = 'Ncp Stock updated.';
            } else {
                $res['status'] = 1;
                $res['message'] = 'Ncp Stock Not updated.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'Data not sent.';
        }
        $this->set(array(
            'ncp_so_stock' => $res,
            '_serialize' => array('ncp_so_stock')
        ));
    }

    /* ---------------------- END Ncp so Stock ------------------------------------ */


    /* -------------------------- visit plan ----------------------------- */

    public function get_doctor_visit_history()
    {
        $this->LoadModel('DoctorVisit');
        $this->LoadModel('SalesPerson');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];

        $territory_id = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.territory_id as t_id'),
            'conditions' => array('SalesPerson.id' => $so_id),
            'recursive' => -1
        ));

        //pr($territory_id);
        //exit;

        $time = date("Y-m-d");
        $final = date("Y-m-d", strtotime("-1 month", strtotime($time)));

        $this->DoctorVisit->unbindModel(array('belongsTo' => array('Market', 'Doctor', 'Territory')));

        $doctor_visit_list = $this->DoctorVisit->find('all', array(
            'fields' => array('DoctorVisit.territory_id', 'DoctorVisit.market_id', 'DoctorVisit.outlet_id', 'DoctorVisit.doctor_id', 'DoctorVisit.id as visit_id', 'DoctorVisit.visit_date', 'DoctorVisit.latitude', 'DoctorVisit.longitude', 'DoctorVisit.created_at', 'DoctorVisit.created_by', 'DoctorVisit.action', 'DoctorVisit.visit_date_time', 'DoctorVisit.place_of_visit', 'DoctorVisit.night_halting', 'DoctorVisit.clinic_name'),
            'conditions' => array('DoctorVisit.territory_id' => $territory_id[0]['t_id'], 'DoctorVisit.visit_date >=' => $final, 'DoctorVisit.visit_date <=' => $time),
            'order' => 'DoctorVisit.id ASC',
            'recursive' => 1
        ));

        //pr($doctor_visit_list);
        //exit;

        $data_array = array();
        $dv_array = array();
        foreach ($doctor_visit_list as $data) {
            $dv_array = $data['DoctorVisit'];
            $dv_array['visit_details'] = $data['DoctorVisitDetail'];
            $data_array[] = $dv_array;
        }
        $this->set(array(
            'doctor_visit_list' => $data_array,
            '_serialize' => array('doctor_visit_list')
        ));
    }

    public function get_user_doctor_visit_plan_list()
    {
        $this->loadModel('UserDoctorVisitPlanList');
        $this->loadModel('User');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $data_array = array();
        $user_id = $this->User->find('first', array(
            'fields' => array('User.id'),
            'conditions' => array('User.sales_person_id' => $json_data['so_id']),
            'recursive' => -1
        ));
        $visit_plan_list = $this->UserDoctorVisitPlanList->find('all', array(
            'fields' => array('UserDoctorVisitPlanList.territory_id', 'UserDoctorVisitPlanList.market_id', 'UserDoctorVisitPlanList.doctor_id', 'UserDoctorVisitPlanList.visit_plan_date'),
            'conditions' => array('UserDoctorVisitPlanList.user_id' => $user_id['User']['id']),
        ));
        foreach ($visit_plan_list as $data) {
            $data_array[] = $data['UserDoctorVisitPlanList'];
        }
        $this->set(array(
            'user_doctor_visit_plan_list' => $data_array,
            '_serialize' => array('user_doctor_visit_plan_list')
        ));
    }

    /* ----------------- GET GIFT ISSUE ------------------------------ */

    public function get_gift_issue()
    {
        $this->LoadModel('GiftItem');
        $this->LoadModel('GiftItemDetail');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $this->GiftItem->unbindModel(array('belongsTo' => array('SalesPerson', 'Outlet')));
        $gift_list = $this->GiftItem->find('all', array(
            'conditions' => array('GiftItem.so_id' => $so_id),
            'order' => 'GiftItem.id DESC',
            'recursive' => 1
        ));
        $data_array = array();
        foreach ($gift_list as $data) {
            $dv_array = array();
            $dv_array = $data['GiftItem'];
            $dv_array['memo_no'] = ($data['GiftItem']['memo_no'] == '') ? '' : $data['GiftItem']['memo_no'];
            $dv_array['gift_issue_detail'] = $data['GiftItemDetail'];
            $data_array[] = $dv_array;
        }
        $this->set(array(
            'gift_issue' => $data_array,
            '_serialize' => array('gift_issue')
        ));
    }

    /* ------------------ get deposit -------------------------- */

    public function get_deposit_list()
    {
        $this->LoadModel('Deposit');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $last_30_days = date('Y-m-d', strtotime('-1 month'));

        //add new
        $this->loadModel('SalesPerson');
        $terrtory_id = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.territory_id'),
            'conditions' => array('SalesPerson.id' => $so_id),
            'recursive' => -1
        ));
        //pr($terrtory_id);
        $territory_id = $terrtory_id['SalesPerson']['territory_id'];
        //end add new

        $deposit_list = $this->Deposit->find('all', array(
            'conditions' => array(
                //'Deposit.sales_person_id' => $so_id,
                'Deposit.territory_id' => $territory_id,
                'Deposit.deposit_date between ? AND ?' => array($last_30_days, date('Y-m-d')),
            ),
            'order' => 'Deposit.id DESC',
            'recursive' => -1
        ));

        $data_array = array();
        foreach ($deposit_list as $data) {
            $data['Deposit']['memo_id'] = $data['Deposit']['memo_no'];
            $data_array[] = $data['Deposit'];
        }
        $this->set(array(
            'deposit_list' => $data_array,
            '_serialize' => array('deposit_list')
        ));
    }

    /* -------------------- get collection ---------------------- */

    public function get_collection_list()
    {
        $this->LoadModel('Collection');
        $last_30_days = date('Y-m-d', strtotime('-40 days'));
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];


        $this->loadModel('SalesPerson');
        $terrtory_id = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.territory_id'),
            'conditions' => array('SalesPerson.id' => $so_id),
            'recursive' => -1
        ));
        //pr($terrtory_id);
        $territory_id = $terrtory_id['SalesPerson']['territory_id'];


        $collection_list = $this->Collection->find('all', array(
            'fields' => array('Collection.id', 'Collection.memo_no as memo_id', 'Collection.is_credit_collection', 'Collection.instrument_type', 'Collection.type', 'Collection.bank_account_id', 'Collection.instrumentRefNo', 'Collection.instrument_date', 'Collection.collectionAmount', 'Collection.collectionDate', 'Collection.deposit_id', 'Collection.created_at', 'Collection.outlet_id', 'Collection.memo_date', 'Collection.memo_value', 'Collection.credit_or_due', 'Collection.tax_ammount', 'Collection.tax_no', 'Collection.instrument_no', 'Collection.payment_id'),
            'conditions' => array(
                //'Collection.so_id' => $so_id,
                'Collection.territory_id' => $territory_id,
                'Collection.type' => 1,
                'Collection.is_credit_collection' => 1,
                'Collection.collectionDate between ? AND ?' => array($last_30_days, date('Y-m-d')),
            ),
            'order' => 'Collection.id DESC',
            'recursive' => -1
        ));

        $data_array = array();
        foreach ($collection_list as $data) {
            $data['Collection']['memo_id'] = $data[0]['memo_id'];
            if ($data['Collection']['type'] == $data['Collection']['instrument_type']) {
                $data['Collection']['instrument_type'] = 0;
            }
            $data_array[] = $data['Collection'];
        }
        $this->set(array(
            'collection_list' => $data_array,
            '_serialize' => array('collection_list')
        ));
    }

    /* -------------------  credit collection ------------------------ */

    public function update_credit_collection($list = null)
    {
        $this->LoadModel('SoCreditCollection');
        $from_json = 1;
        if ($list) {
            $json_data['credit_collection'] = $list;
            $from_json = 0;
        } else {

            $json_data = $this->request->input('json_decode', true);
            /*---------------------------- Mac check --------------------------------*/
            $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
            if (!$mac_check) {

                $mac['status'] = 0;
                $mac['message'] = 'Mac Id Not Match';
                $res = $mac;
                $this->set(array(
                    'mac' => $res,
                    '_serialize' => array('mac')
                ));
                return 0;
            }
            $path = APP . 'logs/';
            $myfile = fopen($path . "update_credit_collection.txt", "a") or die("Unable to open file!");
            fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
            fclose($myfile);
        }

        if (!empty($json_data)) {
            $collection_array = array();
            foreach ($json_data['credit_collection'] as $val) {

                //duplicate Credit collection check
                $memo_check = $this->SoCreditCollection->find('first', array('conditions' => array('SoCreditCollection.memo_no' => $val['memo_no']), 'recursive' => -1));
                //pr($memo_check);
                if ($memo_check) {
                    $data['id'] = $memo_check['SoCreditCollection']['id'];
                }

                //add new
                $so_id = $val['so_id'];
                $this->loadModel('SalesPerson');
                $terrtory_id = $this->SalesPerson->find('first', array(
                    'fields' => array('SalesPerson.territory_id'),
                    'conditions' => array('SalesPerson.id' => $so_id),
                    'recursive' => -1
                ));
                //pr($terrtory_id);exit;
                $territory_id = $terrtory_id['SalesPerson']['territory_id'];
                $data['territory_id'] = $territory_id;
                //end add new

                $data['so_id'] = $val['so_id'];
                $data['outlet_id'] = $val['outlet_id'];
                $data['date'] = $val['date'];
                $data['memo_no'] = $val['memo_no'];
                $data['memo_value'] = $val['memo_value'];
                $data['memo_value'] = $val['memo_value'];
                $data['collect_date'] = $val['collect_date'];
                $data['inst_type'] = $val['inst_type'];
                $data['due_ammount'] = $val['due_amount'];
                $data['paid_ammount'] = $val['paid_amount'];
                $data['updated_at'] = $this->current_datetime();
                $collection_array[] = $data;
                unset($data);
            }
            if ($this->SoCreditCollection->saveAll($collection_array)) {
                unset($collection_array);
                $res['status'] = 1;
                $res['message'] = 'Credit collection updated.';
            } else {
                $res['status'] = 0;
                $res['message'] = 'Credit collection not updated.';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'Data not received.';
        }
        if ($from_json == 0) {
            return $res;
        } else {
            $this->set(array(
                'credit_collection' => $res,
                '_serialize' => array('credit_collection')
            ));
        }
    }

    public function get_credit_collection_list()
    {
        $this->LoadModel('SoCreditCollection');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];

        //add new
        $memo_start_date = date('Y-m-d', strtotime('-6 months'));

        $this->loadModel('SalesPerson');
        $terrtory_id = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.territory_id'),
            'conditions' => array('SalesPerson.id' => $so_id),
            'recursive' => -1
        ));
        //pr($terrtory_id);
        $territory_id = $terrtory_id['SalesPerson']['territory_id'];
        //end new
        $credit_collection_list = $this->SoCreditCollection->find('all', array(
            //'conditions' => array('SoCreditCollection.so_id' => $so_id),
            'conditions' => array(
                'Market.territory_id' => $territory_id,
                'Memo.memo_date >=' => '2018-10-01',
                'SoCreditCollection.due_ammount >' => 0

            ),
            'joins' => array(
                array(
                    'alias' => 'Memo',
                    'table' => 'memos',
                    'type' => 'INNER',
                    'conditions' => 'SoCreditCollection.memo_no = Memo.memo_no'
                ),
                array(
                    'alias' => 'Market',
                    'table' => 'markets',
                    'type' => 'INNER',
                    'conditions' => 'Memo.market_id = Market.id'
                )
            ),

            'order' => 'SoCreditCollection.id DESC',
            'recursive' => -1
        ));
        // echo $this->SoCreditCollection->getLastquery();exit;

        $data_array = array();
        foreach ($credit_collection_list as $data) {
            $data_array[] = $data['SoCreditCollection'];
        }
        $this->set(array(
            'credit_collection_list' => $data_array,
            '_serialize' => array('credit_collection_list')
        ));
    }

    /* -------------------------------- End Credit collection ------------------------- */

    public function live_sales_tracking()
    {
        $this->LoadModel('LiveSalesTrack');
        $json_data = $this->request->input('json_decode', true);
        // $path = APP . 'logs/';
        // $myfile = fopen($path . "LiveSalesTrack.txt", "a") or die("Unable to open file!");
        // fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        // fclose($myfile);
        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        //$so_id=$json_data['so_id'];
        $tracking_list = $this->LiveSalesTrack->find('all', array(
            //'conditions'=>array('Deposit.sales_person_id'=>$so_id),
            //'order'=>'Deposit.id DESC',
            'limit' => 1,
            'order' => array('id' => 'desc'),
            'recursive' => -1
        ));

        $data_array = array();

        foreach ($tracking_list as $traking) {
            $data['id'] = $traking['LiveSalesTrack']['id'];
            $data['start_time'] = date('H:i:s', strtotime($traking['LiveSalesTrack']['start_time']));
            //$data['start_time'] = $traking['LiveSalesTrack']['start_time'];
            //$data['end_time'] = $traking['LiveSalesTrack']['end_time'];
            $data['end_time'] = date('H:i:s', strtotime($traking['LiveSalesTrack']['end_time']));
            $data['interval'] = $traking['LiveSalesTrack']['interval'];
            $data_array[]['LiveSalesTrack'] = $data;
        }

        //pr($data_array);

        $this->set(array(
            'tracking_list' => $data_array,
            '_serialize' => array('tracking_list')
        ));

        //exit;
    }

    public function map_sales_tracking()
    {
        $this->LoadModel('MapSalesTrack');

        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        //$so_id = $json_data['so_id'];

        $data_array = array();

        if (!empty($json_data['gps_tracking'])) {
            $market_array = array();
            foreach ($json_data['gps_tracking'] as $val) {
                $data['so_id'] = $val['so_id'];
                $data['latitude'] = $val['latitude'];
                $data['longitude'] = $val['longitude'];
                $data['created'] = $val['created'];
                $data_array[] = $data;
            }
            $this->MapSalesTrack->saveAll($data_array);
        }


        $res['status'] = 1;
        $res['message'] = 'Success';

        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));

        //exit;
    }

    public function create_installment_no($list = null)
    {

        $this->loadModel('InstallmentNo');
        $from_json = 1;
        if ($list) {
            $json_data = $list;
            $from_json = 0;
        } else {
            $json_data = $this->request->input('json_decode', true);
            /*---------------------------- Mac check --------------------------------*/
            $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
            if (!$mac_check) {

                $mac['status'] = 0;
                $mac['message'] = 'Mac Id Not Match';
                $res = $mac;
                $this->set(array(
                    'mac' => $res,
                    '_serialize' => array('mac')
                ));
                return 0;
            }
            $path = APP . 'logs/';
            $myfile = fopen($path . "create_instrument.txt", "a") or die("Unable to open file!");
            fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
            fclose($myfile);
            $json_data = $json_data['instrument_no'];
        }

        $insert_data_array = array();
        $update_data_array = array();
        foreach ($json_data as $data) {
            $chk_exist = $this->InstallmentNo->find('first', array(
                'conditions' => array(/*'installment_no_id' => $data['instrument_no_id'], */'payment_id' => $data['payment_id'], 'so_id' => $data['so_id'], 'memo_no' => $data['memo_no']),
                'recursive' => -1
            ));

            //pr($chk_exist);die;
            if ($chk_exist) {
                $data_insert['id'] = $chk_exist['InstallmentNo']['id'];
            } else {
                $data_insert['created_at'] = $this->current_datetime();
            }
            $data_insert['installment_no_id'] = $data['instrument_no_id'];
            $data_insert['installment_no_name'] = $data['instrument_no_name'];
            $data_insert['memo_no'] = $data['memo_no'];
            $data_insert['memo_value'] = $data['memo_value'];
            $data_insert['is_used'] = $data['isUsed'];
            $data_insert['is_pushed'] = 1;
            $data_insert['payment'] = $data['payment'];
            $data_insert['payment_id'] = $data['payment_id'];
            $data_insert['so_id'] = $data['so_id'];
            $data_insert['updated_at'] = $this->current_datetime();
            $data_insert['created_by'] = $data['so_id'];
            $data_insert['updated_by'] = $data['so_id'];

            $insert_data_array[] = $data_insert;
            unset($data_insert);
        }
        //$this->InstallmentNo->create();
        if ($this->InstallmentNo->saveAll($insert_data_array)) {
            $res['status'] = 1;
            $res['message'] = 'Success';
        } else {
            $res['status'] = 0;
            $res['message'] = 'Not inserted data';
        }
        if ($from_json == 0) {
            return $res;
        } else {
            $this->set(array(
                'response' => $res,
                '_serialize' => array('response')
            ));
        }
    }

    public function get_installment_no()
    {
        $this->loadModel('InstallmentNo');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $installment = $this->InstallmentNo->find('all', array(
            'conditions' => array(
                'so_id' => $so_id,
                /*'is_used'=> 0*/
            ),
            'recursive' => -1
        ));

        $data_array = array();
        foreach ($installment as $data) {
            $data_array[] = $data['InstallmentNo'];
        }

        $this->set(array(
            'installment_no' => $data_array,
            '_serialize' => array('installment_no')
        ));
    }

    public function delete_outlets()
    {
        $this->loadModel('Outlet');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "delete_outlet.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        if (!empty($json_data)) {

            foreach ($json_data['delete_outlets'] as $val) {

                /* Find there is any memo for that outlets existed */
                /*
                  $count = $this->Memo->find('count', array(
                  'conditions' => array(
                  'Memo.outlet_id' => $val['outlet_id']
                  )
                  ));
                 */

                $this->Outlet->id = $val['outlet_id'];
                $this->Outlet->delete();
            }


            $res['status'] = 1;
            $res['message'] = 'Outlet has been deleted successfuly.';
        } else {
            $res['status'] = 0;
            $res['message'] = 'Outlet not found.';
        }

        $this->set(array(
            'outlet' => $res,
            '_serialize' => array('outlet')
        ));
    }

    /** Password changing for so:Start */
    function change_password()
    {
        $this->loadModel('Usermgmt.User');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $username = $json_data['username'];
        $new_password = md5($json_data['new_password']);
        $old_password = md5($json_data['old_password']);
        $user_info = $this->User->find('first', array(
            'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name'),
            'conditions' => array('User.username' => $username, 'User.password' => $old_password, 'User.active' => 1),
            'recursive' => 0
        ));

        if (!$user_info) {
            $res['status'] = 0;
            $res['message'] = 'User Or Old Password Not match';
        } else {
            $data['User']['id'] = $user_info['User']['id'];
            $data['User']['password'] = $new_password;
            if ($this->User->save($data)) {
                $res['status'] = 1;
                $res['message'] = 'Password Change successfuly';
            } else {
                $res['status'] = 0;
                $res['message'] = 'Please Try Again Later';
            }
        }
        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }

    /** Password changing for so:END */
    /* ------------------- Start create_outlet_visit ---------------------- */

    public function create_outlet_visit()
    {
        $this->loadModel('VisitedOutlet');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $visit_data = isset($json_data['outlet_visit']) ? $json_data['outlet_visit'] : '';
        $outlet_visit_data_all = array();
        $visited_id = array();
        $deleted_id = array();
        if (!empty($visit_data)) {
            foreach ($visit_data as $each_data) {

                $prev_data = $this->VisitedOutlet->find('first', array(
                    'conditions' => array(
                        'outlet_id'                 => $each_data['outlet_id'],
                        'so_id'                     => $each_data['so_id'],
                        'CONVERT(date,visited_at)'  => date('Y-m-d', strtotime($each_data['updated_at']))
                    ),
                    'recursive'  => -1
                ));
                if ($prev_data) {
                    $visited_id[] = $each_data['id'];
                    continue;
                }
                $outlet_visit_data['created_at'] = $this->current_datetime();
                $outlet_visit_data['updated_at'] = $this->current_datetime();
                $outlet_visit_data['created_by'] = $this->get_user_id_from_so_id($each_data['so_id']);
                $outlet_visit_data['updated_by'] = $this->get_user_id_from_so_id($each_data['so_id']);

                $outlet_visit_data['outlet_id'] = $each_data['outlet_id'];
                $outlet_visit_data['so_id'] = $each_data['so_id'];
                $outlet_visit_data['latitude'] = $each_data['latitude'];
                $outlet_visit_data['longitude'] = $each_data['longitude'];
                $outlet_visit_data['visited_at'] = $each_data['updated_at'];
                if ($this->VisitedOutlet->saveAll($outlet_visit_data)) {
                    $visited_id[] = $each_data['id'];
                }
                unset($outlet_visit_data);
            }
        }

        $delete_data = isset($json_data['outlet_delete']) ? $json_data['outlet_delete'] : '';
        if (!empty($delete_data)) {
            foreach ($delete_data as $each_data) {

                $delete_visit_data['outlet_id'] = $each_data['outlet_id'];
                $delete_visit_data['so_id'] = $each_data['so_id'];
                $delete_visit_data['visited_at'] = $each_data['updated_at'];
                if ($this->VisitedOutlet->deleteAll($delete_visit_data))
                    $deleted_id[] = $each_data['id'];
                unset($delete_visit_data);
            }
        }
        /*if (count($outlet_visit_data_all ) > 0) {
            if ($this->VisitedOutlet->saveAll($outlet_visit_data_all)) {
                $res['status'] = 1;
                $res['message'] = 'Outlet Visit Data has been saved successfuly';
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'Outlet Visit Data has not been saved successfuly';
        }*/


        $res['visited_id'] = $visited_id;
        $res['deleted_id'] = $deleted_id;
        $res['status'] = 1;
        $res['message'] = 'Outlet Visit Data has been saved successfuly';

        $this->set(array(
            'outlet_visit' => $res,
            '_serialize' => array('outlet_visit')
        ));
    }
    public function get_outlet_visit()
    {
        $this->loadModel('VisitedOutlet');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        $visited_outlets = $this->VisitedOutlet->find('all', array(
            'conditions' => array(
                'CONVERT(DATE,VisitedOutlet.visited_at)' => date('Y-m-d'),
                'so_id' => $json_data['so_id']
            ),
            'recursive' => -1
        ));
        $data_array = array();
        foreach ($visited_outlets as $data) {
            $data_array[] = $data['VisitedOutlet'];
        }

        // pr($visited_outlets);exit;
        $this->set(array(
            'visited_outlets' => $data_array,
            '_serialize' => array('visited_outlets')
        ));
    }
    public function update_territory_wise_collection_deposit_balance($so_id)
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
        $territory_id = $terrtory_id['SalesPerson']['territory_id'];
        /* $instrument_type=array(
          '1'=>'Cash',
          '2'=>'Cheque'
          ); */
        $instrument_type = $this->InstrumentType->find('list', array(
            'conditions' => array('InstrumentType.id' => array('1', '2'))
        ));
        $updated_array = array();
        foreach ($instrument_type as $key => $ins_data) {
            $collection_data = $this->Collection->find('first', array(
                'fields' => array('SUM(Collection.collectionAmount) as total_collection'),
                'conditions' => array(
                    'Collection.territory_id' => $territory_id,
                    'Collection.type' => $key,
                    'Collection.so_id' => $so_id,
                    'Collection.memo_date >=' => '2018-10-01',
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
                'conditions' => array('Deposit.territory_id' => $territory_id, 'Deposit.type' => $key, 'Deposit.deposit_date >=' => '2018-10-01', 'Deposit.sales_person_id' => $so_id),
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

    public function get_product_price_id($product_id, $product_prices, $all_product_id, $memo_date, $is_distributor = 0)
    {
        // echo $product_id.'--'.$product_price.'<br>';
        if ($is_distributor == 1) {
            $this->LoadModel('DistProductCombination');
            $this->LoadModel('DistCombination');
            $data = array();
            $product_price = $this->DistProductCombination->find('first', array(
                'conditions' => array(
                    'DistProductCombination.product_id' => $product_id,
                    'DistProductCombination.price' => $product_prices,
                    'DistProductCombination.effective_date <=' => $memo_date,
                ),
                'order' => array('DistProductCombination.id' => 'DESC'),
                'recursive' => -1
            ));


            // echo $this->ProductCombination->getLastquery().'<br>';
            if ($product_price) {
                $is_combine = 0;
                if ($product_price['DistProductCombination']['combination_id'] != 0) {
                    $combination = $this->DistCombination->find('first', array(
                        'conditions' => array('DistCombination.id' => $product_price['DistProductCombination']['combination_id']),
                        'recursive' => -1
                    ));
                    $combination_product = explode(',', $combination['DistCombination']['all_products_in_combination']);
                    foreach ($combination_product as $combination_prod) {
                        if ($product_id != $combination_prod && in_array($combination_prod, $all_product_id)) {
                            $data['combination_id'] = $product_price['DistProductCombination']['combination_id'];
                            $data['product_price_id'] = $product_price['DistProductCombination']['id'];
                            $is_combine = 1;
                            break;
                        }
                    }
                }
                if ($is_combine == 0) {
                    $product_price = $this->DistProductCombination->find('first', array(
                        'conditions' => array(
                            'DistProductCombination.product_id' => $product_id,
                            'DistProductCombination.price' => $product_prices,
                            'DistProductCombination.effective_date <=' => $memo_date,
                            'DistProductCombination.parent_slab_id' => 0
                        ),
                        'order' => array('DistProductCombination.id DESC'),
                        'recursive' => -1
                    ));
                    $data['combination_id'] = '';
                    $data['product_price_id'] = $product_price['DistProductCombination']['id'];
                }
                return $data;
            } else {
                $data['combination_id'] = '';
                $data['product_price_id'] = '';
                return $data;
            }
        } else if ($is_distributor == 2) {
            $this->LoadModel('SpecialProductCombination');
            $this->LoadModel('SpecialCombination');
            $data = array();
            $product_price = $this->SpecialProductCombination->find('first', array(
                'conditions' => array(
                    'SpecialProductCombination.product_id' => $product_id,
                    'SpecialProductCombination.price' => $product_prices,
                    'SpecialProductCombination.effective_date <=' => $memo_date,
                ),
                'order' => array('SpecialProductCombination.id' => 'DESC'),
                'recursive' => -1
            ));


            // echo $this->ProductCombination->getLastquery().'<br>';
            if ($product_price) {
                $is_combine = 0;
                if ($product_price['SpecialProductCombination']['combination_id'] != 0) {
                    $combination = $this->SpecialCombination->find('first', array(
                        'conditions' => array('SpecialCombination.id' => $product_price['SpecialProductCombination']['combination_id']),
                        'recursive' => -1
                    ));
                    $combination_product = explode(',', $combination['SpecialCombination']['all_products_in_combination']);
                    foreach ($combination_product as $combination_prod) {
                        if ($product_id != $combination_prod && in_array($combination_prod, $all_product_id)) {
                            $data['combination_id'] = $product_price['SpecialProductCombination']['combination_id'];
                            $data['product_price_id'] = $product_price['SpecialProductCombination']['id'];
                            $is_combine = 1;
                            break;
                        }
                    }
                }
                if ($is_combine == 0) {
                    $product_price = $this->SpecialProductCombination->find('first', array(
                        'conditions' => array(
                            'SpecialProductCombination.product_id' => $product_id,
                            'SpecialProductCombination.price' => $product_prices,
                            'SpecialProductCombination.effective_date <=' => $memo_date,
                            'SpecialProductCombination.parent_slab_id' => 0
                        ),
                        'order' => array('SpecialProductCombination.id DESC'),
                        'recursive' => -1
                    ));
                    $data['combination_id'] = '';
                    $data['product_price_id'] = $product_price['SpecialProductCombination']['id'];
                }
                return $data;
            } else {
                $data['combination_id'] = '';
                $data['product_price_id'] = '';
                return $data;
            }
        } else {
            $this->LoadModel('ProductCombination');
            $this->LoadModel('Combination');
            $data = array();
            $product_price = $this->ProductCombination->find('first', array(
                'conditions' => array(
                    'ProductCombination.product_id' => $product_id,
                    'ProductCombination.price' => $product_prices,
                    'ProductCombination.effective_date <=' => $memo_date,
                ),
                'order' => array('ProductCombination.id' => 'DESC'),
                'recursive' => -1
            ));


            // echo $this->ProductCombination->getLastquery().'<br>';
            if ($product_price) {
                $is_combine = 0;
                if ($product_price['ProductCombination']['combination_id'] != 0) {
                    $combination = $this->Combination->find('first', array(
                        'conditions' => array('Combination.id' => $product_price['ProductCombination']['combination_id']),
                        'recursive' => -1
                    ));
                    $combination_product = explode(',', $combination['Combination']['all_products_in_combination']);
                    foreach ($combination_product as $combination_prod) {
                        if ($product_id != $combination_prod && in_array($combination_prod, $all_product_id)) {
                            $data['combination_id'] = $product_price['ProductCombination']['combination_id'];
                            $data['product_price_id'] = $product_price['ProductCombination']['id'];
                            $is_combine = 1;
                            break;
                        }
                    }
                }
                if ($is_combine == 0) {
                    $product_price = $this->ProductCombination->find('first', array(
                        'conditions' => array(
                            'ProductCombination.product_id' => $product_id,
                            'ProductCombination.price' => $product_prices,
                            'ProductCombination.effective_date <=' => $memo_date,
                            'ProductCombination.parent_slab_id' => 0
                        ),
                        'order' => array('ProductCombination.id DESC'),
                        'recursive' => -1
                    ));
                    $data['combination_id'] = '';
                    $data['product_price_id'] = $product_price['ProductCombination']['id'];
                }
                return $data;
            } else {
                $data['combination_id'] = '';
                $data['product_price_id'] = '';
                return $data;
            }
        }
    }

    public function get_deposit_balance()
    {
        $this->loadModel('SalesPerson');
        $this->loadModel('TerritoryWiseCollectionDepositBalance');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $terrtory_id = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.territory_id'),
            'conditions' => array('SalesPerson.id' => $so_id),
            'recursive' => -1
        ));
        $territory_id = $terrtory_id['SalesPerson']['territory_id'];
        $balance = $this->TerritoryWiseCollectionDepositBalance->find('all', array(
            'fields' => array('TerritoryWiseCollectionDepositBalance.hands_of_so as amount', 'TerritoryWiseCollectionDepositBalance.instrument_type_id as instrument_type'),
            'conditions' => array(
                'TerritoryWiseCollectionDepositBalance.territory_id' => $territory_id,
                'TerritoryWiseCollectionDepositBalance.so_id' => $so_id,
                'TerritoryWiseCollectionDepositBalance.instrument_type_id' => 1
            ),
            'recursive' => -1,
        ));
        $data_array = array();
        if ($balance) {
            foreach ($balance as $data) {
                $data_array = $data[0];
            }
        } else {
            $data_array = array('amount' => 0.00, 'instrument_type' => 1);
        }

        $this->set(array(
            'balance' => $data_array,
            '_serialize' => array('balance')
        ));
    }

    public function get_instrument_type()
    {
        $this->LoadModel('InstrumentType');
        $instrument_type = $this->InstrumentType->find('all', array(
            /* 'conditions'=>array(
              'NOT'=>array('id'=>array('1','2'))
              ), */
            'recursive' => -1,
            'fields' => array('id', 'name')
        ));
        $data_array = array();
        foreach ($instrument_type as $data) {
            $data_array[] = $data['InstrumentType'];
        }
        $this->set(array(
            'instrument' => $data_array,
            '_serialize' => array('instrument')
        ));
    }

    public function delete_deposits()
    {
        $this->loadModel('Deposit');
        $json_data = $this->request->input('json_decode', true);

        $path = APP . 'logs/';
        $myfile = fopen($path . "delete_deposit.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $deposit_id_list = $json_data['delete_deposits'];
        // $deleted_id = array_column($deposit_id_list, 'deposit_id');
        $deleted_id = array_map(function ($a) {
            return $a['deposit_id'];
        }, $deposit_id_list);
        if ($this->Deposit->deleteAll(array('Deposit.id' => $deleted_id))) {
            $res['status'] = 1;
            $res['messege'] = 'Deposit Deleted Successfully';
        } else {
            $res['status'] = 0;
            $res['messege'] = 'Deposit Not Deleted';
        }
        $this->update_territory_wise_collection_deposit_balance($json_data['so_id']);
        $this->set(array(
            'deposit' => $res,
            '_serialize' => array('deposit')
        ));
    }

    public function get_session_product_history()
    {
        $this->LoadModel('SessionDetail');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $so_id = $json_data['so_id'];
        $this->SessionDetail->unbindModel(
            array('belongsTo' => array('Product'))
        );
        $all_session_product = $this->SessionDetail->find('all', array(
            'fields' => array('SessionDetail.*'),
            'conditions' => array('Session.so_id' => $so_id),
        ));
        $session_product = array();
        foreach ($all_session_product as $data) {
            $session_product[] = $data['SessionDetail'];
        }
        $this->set(array(
            'session_product' => $session_product,
            '_serialize' => array('session_product')
        ));
    }

    public function get_outlet_visit_information_report()
    {
        $this->LoadModel('Outlet');
        $this->LoadModel('SalesPerson');
        // $this->LoadModel('VisitedOutlet');
        $json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        if (!$json_data['territory_id']) {
            $territory_id_retrive = $this->SalesPerson->find('first', array('conditions' => array('SalesPerson.id' => $json_data['so_id']), 'recursive' => -1));

            $json_data['territory_id'] = $territory_id_retrive['SalesPerson']['territory_id']; //re-asign $json_data['territory_id'] if null
        }
        $so_id = $json_data['so_id'];
        $territory_id = $json_data['territory_id'];
        $date_from = date("Y-m-d", strtotime(str_replace('/', '-', $json_data['start_date'])));
        $date_to = date("Y-m-d", strtotime(str_replace('/', '-', $json_data['end_date'])));

        $visited_sql = "select 
            o.is_pharma_type,count(distinct(o.id)) as outlet_coverage_from_visited,
            count(vo.id) as non_effective_outlet  
        from visited_outlets vo
        inner join outlets o on o.id=vo.outlet_id 
        inner join markets mk on mk.id=o.market_id
        left join memos m on m.outlet_id=o.id and m.memo_date=convert(date, vo.visited_at)
        where 
        convert(date, vo.visited_at)  between  '$date_from' and '$date_to'
        and mk.territory_id=$territory_id and m.id is null
        group by 
        o.is_pharma_type
        order by 
            o.is_pharma_type desc
        ";
        $visited_data = $this->Outlet->query($visited_sql);
        foreach ($visited_data as $data) {
            $v_data['outlet_coverage_from_visited'][$data[0]['is_pharma_type']] = $data[0]['outlet_coverage_from_visited'] ? $data[0]['outlet_coverage_from_visited'] : 0;
            $v_data['non_effective_outlet'][$data[0]['is_pharma_type']] = $data[0]['non_effective_outlet'] ? $data[0]['non_effective_outlet'] : 0;
        }
        $memo_sql = "select 
            is_pharma_type,count(distinct(o.id)) as outlet_coverage_from_memo,
            count(m.id) as ec 
        from 
            memos m 
        inner join 
            outlets o on o.id=m.outlet_id 
        inner join 
            markets mk on mk.id=o.market_id
        where m.memo_date  between '$date_from' and '$date_to'
        and mk.territory_id=$territory_id
        group by o.is_pharma_type
        order by 
            o.is_pharma_type desc
        ";
        $memo_data = $this->Outlet->query($memo_sql);
        // pr($memo_data);exit;
        $data_array = array();
        $total_outlet_coverage = 0;
        $total_effective_call = 0;
        $total_non_effective_visit = 0;
        foreach ($memo_data as $data) {
            $m_data['outlet_coverage'][$data['0']['is_pharma_type']] = $data['0']['outlet_coverage_from_memo'] ? $data['0']['outlet_coverage_from_memo'] : 0;
            $m_data['effective_call'][$data['0']['is_pharma_type']] = $data['0']['ec'] ? $data['0']['ec'] : 0;
        }
        for ($i = 0; $i <= 1; $i++) {
            $rpt_data['outlet_coverage'] = ($m_data['outlet_coverage'][$i] ? $m_data['outlet_coverage'][$i] : 0) + (@$v_data['outlet_coverage_from_visited'][$data['0']['is_pharma_type']] ? $v_data['outlet_coverage_from_visited'][$i] : 0);
            $rpt_data['effective_call'] = $m_data['effective_call'][$i] ? $m_data['effective_call'][$i] : 0;
            $rpt_data['non_effective_visit'] = @$v_data['non_effective_outlet'][$i] ? $v_data['non_effective_outlet'][$i] : 0;
            $rpt_data['outlet_type'] = ($i == 1 ? 'Prahma' : 'Non-Pharma');

            $total_outlet_coverage += $rpt_data['outlet_coverage'];
            $total_effective_call += $rpt_data['effective_call'];
            $total_non_effective_visit += $rpt_data['non_effective_visit'];
            $data_array[] = $rpt_data;
            unset($rpt_data);
        }
        if ($data_array) {
            $rpt_data['outlet_coverage'] = $total_outlet_coverage;
            $rpt_data['effective_call'] = $total_effective_call;
            $rpt_data['non_effective_visit'] = $total_non_effective_visit;
            $rpt_data['outlet_type'] = 'total';
            $data_array[] = $rpt_data;
            unset($rpt_data);
        }
        $this->set(array(
            'outlet_visit_report_data' => $data_array,
            '_serialize' => array('outlet_visit_report_data')
        ));
    }
    /*public function deleted_memo_log() {
        $this->loadModel('DeletedMemo');
        $this->loadModel('DeletedMemoDetail');

        $json_data = $this->request->input('json_decode', true);

        
        $mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
        if(!$mac_check)
        {

            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        
        $path = APP . 'logs/';
        $myfile = fopen($path . "deleted_memo_log.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        if (!empty($json_data)) {
        } else {
            $res['status'] = 0;
            $res['message'] = 'Memo not found.';
        }

        $this->set(array(
            'memo' => $res,
            '_serialize' => array('memo')
        ));
    }*/

    /*----------------------------- Distributor Product Price : Start ---------------------*/
    public function get_dist_product_price_list()
    {
        $this->loadModel('DistProductPrice');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('DistProductPrice.updated_at >' => $last_update_date);
        }

        $product_price = $this->DistProductPrice->find('all', array(
            'conditions' => $conditions,
            'order' => array('DistProductPrice.updated_at' => 'asc'),
            'recursive' => -1
        ));

        foreach ($product_price as $key => $val) {
            $product_price[$key]['DistProductPrice']['action'] = 1;
        }

        $this->set(array(
            'dist_product_price' => $product_price,
            '_serialize' => array('dist_product_price')
        ));
    }

    public function get_dist_product_combination_list()
    {
        $this->loadModel('DistProductCombination');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('DistProductCombination.updated_at >' => $last_update_date);
        }

        $product_combination = $this->DistProductCombination->find('all', array(
            'conditions' => $conditions,
            'order' => array('DistProductCombination.updated_at' => 'asc'),
            'recursive' => -1
        ));

        foreach ($product_combination as $key => $val) {
            $product_combination[$key]['DistProductCombination']['action'] = 1;
        }

        $this->set(array(
            'dist_product_combination' => $product_combination,
            '_serialize' => array('dist_product_combination')
        ));
    }
    /*----------------------------- Distributor Product Price : END -----------------------*/


    /*----------------------------- Hotel & Resturent Product Price : Start ---------------------*/
    public function get_hotel_resturent_product_price_list()
    {
        $this->loadModel('SpecialProductPrice');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('SpecialProductPrice.updated_at >' => $last_update_date);
        }

        $product_price = $this->SpecialProductPrice->find('all', array(
            'conditions' => $conditions,
            'order' => array('SpecialProductPrice.updated_at' => 'asc'),
            'recursive' => -1
        ));

        foreach ($product_price as $key => $val) {
            $product_price[$key]['SpecialProductPrice']['action'] = 1;
        }

        $this->set(array(
            'hotel_resturent_product_price' => $product_price,
            '_serialize' => array('hotel_resturent_product_price')
        ));
    }

    public function get_hotel_resturent_product_combination_list()
    {
        $this->loadModel('SpecialProductCombination');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('SpecialProductCombination.updated_at >' => $last_update_date);
        }

        $product_combination = $this->SpecialProductCombination->find('all', array(
            'conditions' => $conditions,
            'order' => array('SpecialProductCombination.updated_at' => 'asc'),
            'recursive' => -1
        ));

        foreach ($product_combination as $key => $val) {
            $product_combination[$key]['SpecialProductCombination']['action'] = 1;
        }

        $this->set(array(
            'hotel_resturent_product_combination' => $product_combination,
            '_serialize' => array('hotel_resturent_product_combination')
        ));
    }
    /*----------------------------- Hotel & Resturent Product Price : END -----------------------*/

    public function mac_check($mac_id, $so_id)
    {
        $this->LoadModel('User');
        $this->LoadModel('CommonMac');
        $users = $this->User->find('first', array(
            'conditions' => array('User.mac_id' => $mac_id, 'User.sales_person_id' => $so_id),
            'recursive' => -1
        ));
        if (empty($users)) {
            $common_mac_check = $this->CommonMac->find('first', array('conditions' => array('CommonMac.mac_id' => $mac_id)));

            if ($common_mac_check)
                return true;
            else
                return false;
        } else {
            return true;
        }
    }
    /*
        so stock comparison in apps. comparison so stock (apps) with web stock and push comparison report to server 
     */
    public function get_so_stock_check_data_from_apps()
    {
        $this->LoadModel('SoStockCheck');
        $this->LoadModel('SoStockCheckDetail');
        $json_data = $this->request->input('json_decode', true);
        // pr($json_data);exit;
        $mac_check = $this->mac_check($json_data['list']['mac'], $json_data['list']['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        if ($json_data) {
            $stock_data = $json_data['list'];
            $stock_data_details = $json_data['details'];
            $stock_check_data['so_id'] = $stock_data['so_id'];
            $stock_check_data['store_id'] = $stock_data['store_id'];
            $stock_check_data['reported_time'] = $stock_data['current_datetime'];
            $stock_check_data['created_at'] = $this->current_datetime();
            $stock_check_data['created_by'] = $stock_data['so_id'];
            $this->SoStockCheck->create();
            if ($this->SoStockCheck->save($stock_check_data)) {
                $so_stock_check_id = $this->SoStockCheck->id;
                $stock_checck_data_array = array();
                foreach ($stock_data_details as $data) {
                    $data['so_stock_check_id'] = $so_stock_check_id;
                    $stock_checck_data_array[] = $data;
                }
                $this->SoStockCheckDetail->saveAll($stock_checck_data_array);
            }
            $res['status'] = 1;
            $res['messege'] = 'Data Received';
        } else {
            $res['status'] = 0;
            $res['messege'] = 'Data Not Received';
        }

        $this->set(array(
            'status' => $res,
            '_serialize' => array('status')
        ));
    }
    public function distributor_json_update()
    {
        $this->loadModel('Memo');

        $json_data = $this->request->input('json_decode', true);
        $json_data = $json_data['memo_list'];
        // pr($json_data);exit;

        $memo_no = $json_data['memo_no'];
        $memo_data['is_distributor'] = $json_data['isDistibutor'] ? $json_data['isDistibutor'] : 0;
        $this->Memo->query('UPDATE memos set is_distributor=' . $memo_data['is_distributor'] . 'WHERE memo_no=\'' . $memo_no . '\'');
        $res['status'] = 1;
        $res['messege'] = 'Data  Received';
        $this->set(array(
            'status' => $res,
            '_serialize' => array('status')
        ));
    }

    /*
        Deleted memo logs . crete log if memo edit or delete in apps and push to server. For edit save previous memo data . 

     */
    public function deleted_memo_log()
    {

        //$this->user_status_check(149);

        $this->loadModel('Product');
        $this->loadModel('DeletedMemo');
        $this->loadModel('Memo');
        $this->loadModel('DeletedMemoDetail');
        $all_save = 1;
        $json_main = $this->request->input('json_decode', true);
        $json = $json_main['memo_log'];
        $json_user = $json_main['user_info'];
        $path = APP . 'logs/';
        $myfile = fopen($path . "create_deleted_memo_logs.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        if (!empty($json_main)) {

            /*---------------------------- Mac check --------------------------------*/
            $mac_check = $this->mac_check($json_user['mac'], $json_user['so_id']);
            if (!$mac_check) {

                $mac['status'] = 0;
                $mac['message'] = 'Mac Id Not Match';
                $res = $mac;
                $this->set(array(
                    'mac' => $res,
                    '_serialize' => array('mac')
                ));
                return 0;
            }
            $temp_id_array = array();
            foreach ($json as $json_m_data) {
                $json_data = $json_m_data['memo_list'];
                if (!$json_data['territory_id']) {
                    $this->loadModel('SalesPerson');
                    $territory_id_retrive = $this->SalesPerson->find('first', array('conditions' => array('SalesPerson.id' => $json_data['sales_person_id']), 'recursive' => -1));
                    $json_data['territory_id'] = $territory_id_retrive['SalesPerson']['territory_id']; //re-asign $json_data['territory_id'] if null
                }

                /* START ADD NEW */
                //get office id 
                $this->loadModel('Territory');
                $territory_info = $this->Territory->find(
                    'first',
                    array(
                        'conditions' => array('Territory.id' => $json_data['territory_id']),
                        'fields' => 'Territory.office_id',
                        'order' => array('Territory.id' => 'asc'),
                        'recursive' => -1,
                        //'limit' => 100
                    )
                );
                $office_id = $territory_info['Territory']['office_id'];

                //get thana id 
                $this->loadModel('Market');
                $market_info = $this->Market->find(
                    'first',
                    array(
                        'conditions' => array('Market.id' => $json_data['market_id']),
                        'fields' => 'Market.thana_id',
                        'order' => array('Market.id' => 'asc'),
                        'recursive' => -1,
                        //'limit' => 100
                    )
                );
                $thana_id = $market_info['Market']['thana_id'];

                $count = $this->DeletedMemo->find('count', array(
                    'conditions' => array(
                        'DeletedMemo.memo_no' => $json_data['memo_no'],
                        'DeletedMemo.temp_id' => $json_data['temp_id'],
                    )
                ));

                $this->loadModel('Outlet');
                $outlet_info = $this->Outlet->find('first', array(
                    'conditions' => array('Outlet.id' => $json_data['outlet_id']),
                    'recursive' => -1
                ));

                $is_csa_outlet = $outlet_info['Outlet']['is_csa'];
                //----------------------------------

                if (is_numeric($json_data['market_id']) || true) {

                    if ($count == 0) {
                        $memo_datas = $this->Memo->find('first', array(
                            'conditions' => array(
                                'Memo.memo_no' => $json_data['memo_no']
                            )
                        ));
                        $temp_id_array[] = $json_data['temp_id'];
                        $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                        $product_list = Set::extract($products, '{n}.Product');

                        $memo_data['memo_no'] = $json_data['memo_no'];
                        $memo_data['temp_id'] = $json_data['temp_id'];
                        $memo_data['memo_date'] = date('Y-m-d', strtotime($json_data['memo_date']));
                        $memo_data['memo_time'] = isset($memo_datas['Memo']['memo_time']) ? $memo_datas['Memo']['memo_time'] : $json_data['memo_date'];
                        $memo_data['sales_person_id'] = $json_data['sales_person_id'];
                        $memo_data['sales_to'] = 0;
                        $memo_data['outlet_id'] = (is_numeric($json_data['outlet_id']) ? $json_data['outlet_id'] : 0);
                        $memo_data['market_id'] = (is_numeric($json_data['market_id']) ? $json_data['market_id'] : 0);
                        $memo_data['territory_id'] = $json_data['territory_id'];
                        $memo_data['gross_value'] = $json_data['gross_value'];
                        $memo_data['cash_recieved'] = $json_data['cash_recieved'];
                        $memo_data['credit_amount'] = $credit_amount = $json_data['gross_value'] - $json_data['cash_recieved'];
                        $memo_data['latitude'] = $json_data['latitude'];
                        $memo_data['longitude'] = $json_data['longitude'];
                        $memo_data['status'] = ($credit_amount <= 0 ? 2 : 1);
                        $memo_data['is_delete'] = $json_data['isDeleted'];
                        $memo_data['from_app'] = 1;
                        $memo_data['action'] = 0;

                        $memo_data['deleted_at'] = $json_data['delete_time'];
                        $memo_data['created_at'] = $this->current_datetime();
                        $memo_data['updated_at'] = $this->current_datetime();
                        $so_id_created_by = $this->get_user_id_from_so_id($json_data['sales_person_id']);
                        $memo_data['created_by'] = $so_id_created_by;
                        $memo_data['updated_by'] = $so_id_created_by;

                        $memo_data['office_id'] = $office_id ? $office_id : 0;
                        $memo_data['thana_id'] = $thana_id ? $thana_id : 0;
                        $memo_data['is_distributor'] = isset($json_data['isDistibutor']) ? $json_data['isDistibutor'] : 0;
                        $this->DeletedMemo->create();
                        $this->DeletedMemo->save($memo_data);
                        $all_product_id = array_map(function ($element) {
                            return $element['product_id'];
                        }, $json_data['memo_details']);

                        // $memo_details_array = array();
                        foreach ($json_data['memo_details'] as $val) {

                            $memo_details_array = array();

                            $product_price_id = 0;
                            if ($val['price'] > 0) {
                                $product_price_id = $this->get_product_price_id($val['product_id'], $val['price'], $all_product_id, date('Y-m-d', strtotime($json_data['memo_date'])), isset($json_data['isDistibutor']) ? $json_data['isDistibutor'] : 0);
                            }

                            $memo_details['memo_id'] = $this->DeletedMemo->id;
                            $memo_details['product_id'] = $val['product_id'];
                            $memo_details['product_type'] = $val['product_type'];
                            $memo_details['measurement_unit_id'] = $val['measurement_unit_id'];
                            $memo_details['sales_qty'] = $val['sales_qty'];
                            $memo_details['price'] = $val['price'];
                            $memo_details['product_price_id'] = $product_price_id['product_price_id'];
                            $memo_details['product_combination_id'] = $product_price_id['combination_id'];
                            $memo_details['bonus_qty'] = $val['bonus_qty'];
                            $memo_details['bonus_product_id'] = $val['bonus_product_id'];
                            $memo_details['current_inventory_id'] = $val['current_inventory_id'];
                            $memo_details['bonus_inventory_id'] = $val['bonus_inventory_id'];
                            $memo_details['is_bonus'] = $val['is_bonus'];
                            $memo_date = date('Y-m-d', strtotime($json_data['memo_date']));

                            //START NEW FOR BOUNUS
                            if (!$val['price'] > 0) {
                                $b_product_id = $val['product_id'];
                                //$memo_date = date('Y-m-d', strtotime($json_data['memo_date']));
                                $memo_products = $json_data['memo_details'];

                                $bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date, $memo_products);

                                $memo_details['bonus_id'] = $bonus_result['bonus_id'];
                                $memo_details['bonus_scheme_id'] = $bonus_result['bonus_scheme_id'];
                            }

                            // insert data into MemoDetail
                            $memo_details_array[] = $memo_details;
                            $this->DeletedMemoDetail->saveAll($memo_details_array);
                        }
                    } else {
                        $temp_id_array[] = $json_data['temp_id'];
                    }
                } else {
                    $all_save = 0;
                }
            }
        } else {
            $res['status'] = 0;
            $res['message'] = 'NO Data Found';
        }
        if ($all_save) {
            $res['status'] = 1;
            $res['message'] = 'Data pushed successfully';
        } else {
            $res['status'] = 0;
            $res['message'] = 'Some Data Missed';
        }
        $res['temp_id'] = $temp_id_array;
        $this->set(array(
            'memo' => $res,
            '_serialize' => array('memo')
        ));
    }
    public function get_product_fraction_slab()
    {
        $this->loadModel('ProductFractionSlab');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductFractionSlab.updated_at >' => $last_update_date);
        }
        $product_fraciton_slab = $this->ProductFractionSlab->find('all', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));
        $product_fraciton_slab = array_map(function ($data) {
            return $data['ProductFractionSlab'];
        }, $product_fraciton_slab);
        // pr($product_fraciton_slab);exit;
        $this->set(array(
            'product_fraciton_slab' => $product_fraciton_slab,
            '_serialize' => array('product_fraciton_slab')
        ));
    }

    public function get_project_price()
    {
        $this->loadModel('ProductPrice');
        $this->loadModel('ProductCombination');
        $this->loadModel('OutletNgoPrice');
        $this->loadModel('SalesPerson');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $territory_id_retrive = $this->SalesPerson->find('first', array('conditions' => array('SalesPerson.id' => $json_data['so_id']), 'recursive' => -1));

        $territory_id = $territory_id_retrive['SalesPerson']['territory_id'];

        $last_update_date = $json_data['last_update_date'];

        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductPrice.updated_at >' => $last_update_date);
        }
        $conditions['AND'] = array('ProductPrice.project_id is not null', 'ProductPrice.project_id !=' => 0);
        $product_price = $this->ProductPrice->find('all', array(
            'conditions' => $conditions,
            'order' => array('ProductPrice.updated_at' => 'asc'),
            'recursive' => -1
        ));

        foreach ($product_price as $key => $val) {
            $product_price[$key]['ProductPrice']['action'] = 1;
        }

        /*---------------- product combination list for project --------------------*/

        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductCombination.updated_at >' => $last_update_date);
        }
        $conditions['AND'] = array('ProductPrice.project_id is not null', 'ProductPrice.project_id !=' => 0);
        $product_combination = $this->ProductCombination->find('all', array(
            'conditions' => $conditions,
            'order' => array('ProductCombination.updated_at' => 'asc'),
            'joins' => array(
                array(
                    'table' => 'product_prices',
                    'alias' => 'ProductPrice',
                    'conditions' => 'ProductPrice.id=ProductCombination.product_price_id'
                ),
            ),
            'recursive' => -1
        ));
        foreach ($product_combination as $key => $val) {
            $product_combination[$key]['ProductCombination']['action'] = 1;
        }

        /*------------------- Selected outlet for Project Pricing ---------------------*/
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('OutletNgoPrice.updated_at >' => $last_update_date);
        }
        $conditions['Market.territory_id'] = $territory_id;
        $project_outlet = $this->OutletNgoPrice->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'outlets',
                    'alias' => 'Outlet',
                    'conditions' => 'Outlet.id=OutletNgoPrice.outlet_id'
                ),
                array(
                    'table' => 'markets',
                    'alias' => 'Market',

                    'conditions' => 'Market.id=Outlet.market_id'
                ),
            ),
            'order' => array('OutletNgoPrice.updated_at' => 'asc'),
            'recursive' => 0
        ));
        // echo $this->OutletNgoPrice->getLastquery();exit;
        $project_oulet_array = array();
        foreach ($project_outlet as $data) {
            $data_p = $data['OutletNgoPrice'];
            $data_p['start_date'] = $data['ProductPrice']['effective_date'];
            $data_p['end_date'] = $data['ProductPrice']['end_date'];
            $data_p['project_id'] = $data['ProductPrice']['project_id'];
            $project_oulet_array[] = $data_p;
        }
        $this->set(array(
            'product_price' => $product_price,
            'product_combination' => $product_combination,
            'project_outlet_relation' => $project_oulet_array,
            '_serialize' => array('product_price', 'product_combination', 'project_outlet_relation')
        ));
    }


    //add new (25/07/2020)
    public function get_outlet_group()
    {
        $this->loadModel('OutletGroupToOutlet');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $conditions = array();
        $conditions = array('OutletGroupToOutlet.is_distributor' => 0);

        $outlet_group_list = $this->OutletGroupToOutlet->find('all', array(
            //'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
            'conditions' => $conditions,
            //'order' => array('OutletCategory.updated_at' => 'asc'),
            'recursive' => -1
        ));

        //pr($outlet_group_list);
        //exit;

        $data_array = array();
        /* foreach ($outlet_group_list as $key => $val) {
            $outlet_group_list[$key]['OutletGroupToOutlet']['action'] = 1;
        }*/

        $this->set(array(
            'outlet_group_list' => $outlet_group_list,
            '_serialize' => array('outlet_group_list')
        ));
    }


    public function get_policy_list()
    {
        $this->loadModel('GroupWiseDiscountBonusPolicy');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $territory_id = $json_data['Territory_Id'];  /// added by palash 26th May 2017
        $this->loadModel('Territory');
        $territory_info = $this->Territory->find(
            'first',
            array(
                'conditions' => array('Territory.id' => $territory_id),
                'fields' => array('Office.id'),
                'recursive' => 0
            )
        );
        $office_id = $territory_info['Office']['id'];


        $conditions = array(
            'GroupWiseDiscountBonusPolicy.is_distributor' => 0,

            'OR' => array(
                'Grp.office_id' => $office_id,
                'Market.territory_id' => $territory_id,
            )
        );

        $policy_list = $this->GroupWiseDiscountBonusPolicy->find('all', array(
            //'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'group_wise_discount_bonus_policy_to_offices',
                    'alias' => 'Grp',
                    'type' => 'Left',
                    'conditions' => 'Grp.group_wise_discount_bonus_policy_id=GroupWiseDiscountBonusPolicy.id'
                ),
                array(
                    'table' => 'group_wise_discount_bonus_policy_to_outlet_groups',
                    'alias' => 'GrpOg',
                    'type' => 'Left',
                    'conditions' => 'GrpOg.group_wise_discount_bonus_policy_id=GroupWiseDiscountBonusPolicy.id'
                ),
                array(
                    'table' => 'outlet_group_to_outlets',
                    'alias' => 'OutletGroup',
                    'type' => 'Left',
                    'conditions' => 'GrpOg.outlet_group_id=OutletGroup.outlet_group_id'
                ),
                array(
                    'table' => 'outlets',
                    'alias' => 'Outlet',
                    'type' => 'Left',
                    'conditions' => 'Outlet.id=OutletGroup.outlet_id'
                ),
                array(
                    'table' => 'markets',
                    'alias' => 'Market',
                    'type' => 'Left',
                    'conditions' => 'Market.id=Outlet.market_id'
                ),
            ),

            'recursive' => 2
        ));

        //pr($outlet_group_list);
        //exit;

        $data_array = array();
        /* foreach ($outlet_group_list as $key => $val) {
            $outlet_group_list[$key]['OutletGroupToOutlet']['action'] = 1;
        }*/

        $this->set(array(
            'policy_list' => $policy_list,
            '_serialize' => array('policy_list')
        ));
    }

    public function get_policy_list_v2()
    {
        $this->loadModel('DiscountBonusPolicy');
        $this->loadModel('SpecialGroup');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }
        $last_update_date = $json_data['last_update_date'];


        $territory_id = $json_data['territory_id'];  /// added by palash 26th May 2017
        $this->loadModel('Territory');
        $territory_info = $this->Territory->find(
            'first',
            array(
                'conditions' => array('Territory.id' => $territory_id),
                'fields' => array('Office.id'),
                'recursive' => 0
            )
        );
        $office_id = $territory_info['Office']['id'];


        $conditions = array(
            'DiscountBonusPolicy.is_so' => 1,
            'OR' => array(
                'Grp.reffrence_id' => $office_id,
                'Market.territory_id' => $territory_id,
            )
        );
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status != 1) {

            $conditions[] = array('DiscountBonusPolicy.updated_at >' => $last_update_date);
        }
        $this->DiscountBonusPolicy->unbindModel(array(
            'hasMany' => array(
                'DiscountBonusPolicyToSpecialGroupSr',
                'DiscountBonusPolicyToOfficeSr',
                'DiscountBonusPolicyToOfficeSo',
                'DiscountBonusPolicyToOutletGroupSr',
                'DiscountBonusPolicyToOutletCategorySr',
                'DiscountBonusPolicyOptionSr',
                'DiscountBonusPolicyOptionDB',
                'DiscountBonusPolicyOption',
            ),
        ));
        $policy_list = $this->DiscountBonusPolicy->find('all', array(
            //'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'discount_bonus_policy_settings',
                    'alias' => 'Grp',
                    'type' => 'Left',
                    'conditions' => 'Grp.discount_bonus_policy_id=DiscountBonusPolicy.id and Grp.create_for=2 and Grp.for_so_sr=1'
                ),
                array(
                    'table' => 'discount_bonus_policy_settings',
                    'alias' => 'GrpOg',
                    'type' => 'Left',
                    'conditions' => 'GrpOg.discount_bonus_policy_id=DiscountBonusPolicy.id and GrpOg.create_for=3 and GrpOg.for_so_sr=1'
                ),
                array(
                    'table' => 'outlet_group_to_outlets',
                    'alias' => 'OutletGroup',
                    'type' => 'Left',
                    'conditions' => 'GrpOg.reffrence_id=OutletGroup.outlet_group_id'
                ),
                array(
                    'table' => 'outlets',
                    'alias' => 'Outlet',
                    'type' => 'Left',
                    'conditions' => 'Outlet.id=OutletGroup.outlet_id'
                ),
                array(
                    'table' => 'markets',
                    'alias' => 'Market',
                    'type' => 'Left',
                    'conditions' => 'Market.id=Outlet.market_id'
                ),
            ),
            'group' => array(
                'DiscountBonusPolicy.id',
                'DiscountBonusPolicy.name',
                'DiscountBonusPolicy.start_date',
                'DiscountBonusPolicy.end_date',
                'DiscountBonusPolicy.updated_at',
            ),
            'fields' => array(
                'DiscountBonusPolicy.id',
                'DiscountBonusPolicy.name',
                'DiscountBonusPolicy.start_date',
                'DiscountBonusPolicy.end_date',
                'DiscountBonusPolicy.updated_at',
            ),

            'recursive' => 2
        ));

        $this->DiscountBonusPolicy->unbindModel(array(
            'hasMany' => array(
                'DiscountBonusPolicyToSpecialGroupSr',
                'DiscountBonusPolicyToOfficeSr',
                'DiscountBonusPolicyToOutletGroupSr',
                'DiscountBonusPolicyToOutletCategorySr',
                'DiscountBonusPolicyToOfficeSo',
                'DiscountBonusPolicyOptionSr',
                'DiscountBonusPolicyOptionDB',
                'DiscountBonusPolicyOption',
            ),
        ));
        $conditions = array(
            'DiscountBonusPolicy.is_so' => 1
        );
        if ($res_status != 1) {

            $conditions[] = array('DiscountBonusPolicy.updated_at >' => $last_update_date);
        }
        $special_policy_list = $this->DiscountBonusPolicy->find('all', array(
            //'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'discount_bonus_policy_settings',
                    'alias' => 'Grp',
                    'type' => 'inner',
                    'conditions' => 'Grp.discount_bonus_policy_id=DiscountBonusPolicy.id and Grp.create_for=1 and Grp.for_so_sr=1'
                ),

            ),
            'group' => array(
                'DiscountBonusPolicy.id',
                'DiscountBonusPolicy.name',
                'DiscountBonusPolicy.start_date',
                'DiscountBonusPolicy.end_date',
                'DiscountBonusPolicy.updated_at',
            ),
            'fields' => array(
                'DiscountBonusPolicy.id',
                'DiscountBonusPolicy.name',
                'DiscountBonusPolicy.start_date',
                'DiscountBonusPolicy.end_date',
                'DiscountBonusPolicy.updated_at',
            ),

            'recursive' => 2
        ));
        $assign_group = array();
        foreach ($special_policy_list as $data) {
            $group_id = $data['DiscountBonusPolicyToSpecialGroupSo'][0]['reffrence_id'];
            $special_group_details = $this->SpecialGroup->find('all', array(
                'conditions' => array(
                    'SpecialGroup.id' => $group_id,
                    'SPO.reffrence_id' => $office_id
                ),
                'joins' => array(
                    array(
                        'table' => 'special_group_other_settings',
                        'alias' => 'SPO',
                        'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=1'
                    ),
                    array(
                        'table' => '(
                                    select t.office_id,spg.reffrence_id as territory_id from special_groups sps
                                    inner join special_group_other_settings spg on sps.id=spg.special_group_id and create_for=2
                                    inner join territories t on t.id=spg.reffrence_id
                                    where 
                                        sps.id=' . $group_id . '
                                )',
                        'alias' => 'SPT',
                        'type' => 'left',
                        'conditions' => 'SPT.office_id=SPO.reffrence_id'
                    ),
                ),
                'group' => array('SPO.reffrence_id'),
                'fields' => array('SPO.reffrence_id', 'COUNT(SPT.territory_id) as total_territory'),
                'recursive' => -1
            ));
            if ($special_group_details) {
                if ($special_group_details[0][0]['total_territory'] > 0) {
                    $special_group_territory_details = $this->SpecialGroup->find('all', array(
                        'conditions' => array(
                            'SpecialGroup.id' => $group_id,
                            'SPO.reffrence_id' => $territory_id
                        ),
                        'joins' => array(
                            array(
                                'table' => 'special_group_other_settings',
                                'alias' => 'SPO',
                                'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=2'
                            )

                        ),
                        'recursive' => -1
                    ));
                    if ($special_group_territory_details) {
                        $assign_group[] = $data;
                    } else {
                        $outlet_group_data = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
                        if ($outlet_group_data) {
                            $assign_group[] = $data;
                        }
                    }
                } else {

                    $assign_group[] = $data;
                }
            } else {
                $outlet_group_data = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
                if ($outlet_group_data) {
                    $assign_group[] = $data;
                }
            }
        }
        $policy_list = array_merge($policy_list, $assign_group);

        $this->set(array(
            'policy_list' => $policy_list,
            '_serialize' => array('policy_list')
        ));
    }


    public function get_product_units()
    {
        $this->loadModel('ProductMeasurement');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
        if (!$mac_check) {

            $mac['status'] = 0;
            $mac['message'] = 'Mac Id Not Match';
            $res = $mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
            ));
            return 0;
        }

        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductMeasurement.updated_at >' => $last_update_date);
        }

        $unit_info = $this->ProductMeasurement->find('all', array(
            'fields' => array('ProductMeasurement.id', 'ProductMeasurement.measurement_unit_id', 'MeasurementUnit.name', 'ProductMeasurement.product_id', 'ProductMeasurement.qty_in_base', 'ProductMeasurement.updated_at'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'alias' => 'MeasurementUnit',
                    'table' => 'measurement_units',
                    'type' => 'INNER',
                    'conditions' => 'ProductMeasurement.measurement_unit_id = MeasurementUnit.id'
                ),
            ),
            //'order' => array('ProductMeasurement.updated_at' => 'asc'),
            'recursive' => -1
        ));

        //pr($unit_info);

        foreach ($unit_info as $key => $val) {
            $unit_info[$key]['ProductMeasurement']['action'] = 1;
        }
        $this->set(array(
            'unit_info' => $unit_info,
            '_serialize' => array('unit_info')
        ));
    }
}
