<?php
App::uses('AppController', 'Controller');

/**
 * Controller
 *
 * @property ApiDataRetrives $ApiDataRetrives
 * @property PaginatorComponent $Paginator
 */
class ApiDataSap11Controller extends AppController
{
    public $components = array('RequestHandler', 'Usermgmt.UserAuth');
    public function get_data()
    {
        $this->loadModel('Memo');

        // $json_data = $this->request->input('json_decode', true);
        if (!$this->auth()) {
            $this->set(array(
                'result' => array('status' => 0, 'msg' => 'Wrong auth token'),
                '_serialize' => array('result')
            ));
            return 0;
        }
        /* $fields = array(
            'Memo.memo_no as MemoNo',
            'MemoDetail.id as Line',
            'CASE WHEN MemoDetail.product_id=47 then 1400000000 WHEN MemoDetail.product_id=149 then 1400000001 END as ProductCode',
            '\'EE00030009\' as CustomerCode',
            '\'\' as CustomerPONo',
            '\'1001\' as SalesOrg',
            '\'1\' as DistChnl',
            '\'0\' as Division',
            '\'1003\' as Plant',
            '\'TO01\' as StorageLoc',
            '\'ZDHE\' as Region',
            '\'001\' as Territory',
            'MemoDetail.sales_qty as Quantity',
            '\'PAC\' as UoM',
            'MemoDetail.actual_price as UnitPrice',
            'Memo.memo_date as CustomerRefDt',
            'Memo.memo_date as SalesDocDate',
            '\'001\' as PaymentTerms',
            '\'CFR\' as Inco1',
            '\'BD\' as Inco2',
            '\'001\' as OrderReason',
            'Memo.memo_date as DeliveryDate',
            'MemoDetail.discount_amount as DiscountAmount',
            'Memo.outlet_id as Outlet',
            'CASE WHEN Outlet.is_pharma_type=1 THEN \'PHAM\' ELSE \'NPHA\' END as OutletType',
            'CASE WHEN MemoDetail.price=0 THEN \'BONUS\' END as FOCFlag',
            '\'ZORS\' as OrderType',
        );
        $memo_data = $this->Memo->find('all', array(
            'conditions' => array(
                'Memo.memo_date' => date('Y-m-d', strtotime($this->request->query['date'])),
                'Memo.office_id' => 15,
                //'Memo.territory_id' => 20209,
                'MemoDetail.product_id' => array(47, 149)
            ),
            'joins' => array(
                array(
                    'table' => 'outlets',
                    'alias' => 'Outlet',
                    'conditions' => 'Outlet.id=Memo.outlet_id'
                ),
                array(
                    'table' => 'memo_details',
                    'alias' => 'MemoDetail',
                    'conditions' => 'MemoDetail.memo_id=Memo.id'
                ),
                array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'conditions' => 'MemoDetail.product_id=Product.id'
                )
            ),
            /* 'limit' => 20, * /
            'order' => array('Memo.updated_at'),
            'fields' => $fields,
            'recursive' => -1
        ));
        $memo_data_array = array();
        foreach ($memo_data as $data_m) {
            $memo_data_array[] = $data_m[0];
        } */

        $memo_data = $this->Memo->find('all', array(
            'conditions' => array(
                'Memo.memo_date' => date('Y-m-d', strtotime($this->request->query['date'])),

            ),

            'limit' => 3,
            'order' => array('Memo.updated_at'),
            /* 'fields' => $fields, */
            'recursive' => -1
        ));
        /*  pr($memo_data);
        exit; */
        $json_data = json_decode('[
            {
                "InvoiceNo": "' . $memo_data['0']['Memo']['memo_no'] . '",
                "OrderNo": "' . $memo_data['0']['Memo']['memo_no'] . '",
                "CustomerCode": "EE00030009",
                "SalesOrg": "1001",
                "CustomerPONo": " ",
                "DistChnl": "01",
                "Division": "01",
                "Territory": "001",
                "CustomerRefDt": "' . date('d.m.Y', strtotime($memo_data['0']['Memo']['memo_date'])) . '",
                "SalesDocDate": "' . date('d.m.Y', strtotime($memo_data['0']['Memo']['memo_date'])) . '",
                "DeliveryDate": "' . date('d.m.Y', strtotime($memo_data['0']['Memo']['memo_date'])) . '",
                "PaymentTerms": "0001",
                "OrderType": "ZORS",
                "item": [
                    {
                        "Line": "10001",
                        "Plant": "1003",
                        "ProductCode": "1400000000",
                        "Quantity": "4",
                        "UoM": "PAK",
                        "StorageLoc": "TO01",
                        "UnitPrice": "7.00",
                        "Inco1": "SAP",
                        "Inco2": "SAP",
                        "OrderReason": "SAP",
                        "DiscountAmount": "5.00",
                        "FOCFlag": " ",
                        "Outlet": "R101",
                        "Action": " "
                    }
                ]
            },
            {
                "InvoiceNo": "' . $memo_data['1']['Memo']['memo_no'] . '",
                "OrderNo": "' . $memo_data['1']['Memo']['memo_no'] . '",
                "CustomerCode": "EE00030009",
                "SalesOrg": "1001",
                "CustomerPONo": " ",
                "DistChnl": "01",
                "Division": "01",
                "Territory": "001",
                "CustomerRefDt": "' . date('d.m.Y', strtotime($memo_data['1']['Memo']['memo_date'])) . '",
                "SalesDocDate": "' . date('d.m.Y', strtotime($memo_data['1']['Memo']['memo_date'])) . '",
                "DeliveryDate": "' . date('d.m.Y', strtotime($memo_data['1']['Memo']['memo_date'])) . '",
                "PaymentTerms": "0001",
                "OrderType": "ZORS",
                "item": [
                    {
                        "Line": "10001",
                        "Plant": "1003",
                        "ProductCode": "1400000000",
                        "Quantity": "10",
                        "UoM": "PAK",
                        "StorageLoc": "TO01",
                        "UnitPrice": "7.00",
                        "Inco1": "SAP",
                        "Inco2": "SAP",
                        "OrderReason": "SAP",
                        "DiscountAmount": "5.00",
                        "FOCFlag": " ",
                        "Outlet": "R101",
                        "Action": " "
                    },
                    {
                        "Line": "10002",
                        "Plant": "1003",
                        "ProductCode": "1400000000",
                        "Quantity": "2",
                        "UoM": "PAK",
                        "StorageLoc": "TO01",
                        "UnitPrice": "0.00",
                        "Inco1": "SAP",
                        "Inco2": "SAP",
                        "OrderReason": "SAP",
                        "DiscountAmount": "0.00",
                        "FOCFlag": "M",
                        "Outlet": "R101",
                        "Action": " "
                    }
                ]
            },
            {
                "InvoiceNo": "' . $memo_data['2']['Memo']['memo_no'] . '",
                "OrderNo": "' . $memo_data['2']['Memo']['memo_no'] . '",
                "CustomerCode": "EE00030009",
                "SalesOrg": "1001",
                "CustomerPONo": " ",
                "DistChnl": "01",
                "Division": "01",
                "Territory": "001",
                "CustomerRefDt": "' . date('d.m.Y', strtotime($memo_data['2']['Memo']['memo_date'])) . '",
                "SalesDocDate": "' . date('d.m.Y', strtotime($memo_data['2']['Memo']['memo_date'])) . '",
                "DeliveryDate": "' . date('d.m.Y', strtotime($memo_data['2']['Memo']['memo_date'])) . '",
                "PaymentTerms": "0001",
                "OrderType": "ZORS",
                "item": [
                    {
                        "Line": "10001",
                        "Plant": "1003",
                        "ProductCode": "1400000000",
                        "Quantity": "25",
                        "UoM": "PAK",
                        "StorageLoc": "TO01",
                        "UnitPrice": "7.00",
                        "Inco1": "SAP",
                        "Inco2": "SAP",
                        "OrderReason": "SAP",
                        "DiscountAmount": "5.00",
                        "FOCFlag": " ",
                        "Outlet": "R101",
                        "Action": " "
                    },
                    {
                        "Line": "10002",
                        "Plant": "1003",
                        "ProductCode": "1400000000",
                        "Quantity": "3",
                        "UoM": "PAK",
                        "StorageLoc": "TO01",
                        "UnitPrice": "0.00",
                        "Inco1": "SAP",
                        "Inco2": "SAP",
                        "OrderReason": "SAP",
                        "DiscountAmount": "0.00",
                        "FOCFlag": "M",
                        "Outlet": "R101",
                        "Action": " "
                    },
                    {
                        "Line": "10003",
                        "Plant": "1003",
                        "ProductCode": "1400000023",
                        "Quantity": "5",
                        "UoM": "EA",
                        "StorageLoc": "TO01",
                        "UnitPrice": "0.00",
                        "Inco1": "SAP",
                        "Inco2": "SAP",
                        "OrderReason": "SAP",
                        "DiscountAmount": "0.00",
                        "FOCFlag": "G",
                        "Outlet": "R101",
                        "Action": " "
                    }
                ]
            }
        ]', true);

        $this->set(array(
            'Orders' => array('order' => $json_data),
            '_serialize' => array('Orders')
        ));
    }
    public function get_data_json()
    {
        $this->loadModel('Memo');
        $json_data = $this->request->input('json_decode', true);
        if (!$this->auth()) {
            $this->set(array(
                'result' => array('status' => 0, 'msg' => 'Wrong auth token'),
                '_serialize' => array('result')
            ));
        }
        $memo_data = $this->Memo->find('all', array(
            'conditions' => array('Memo.memo_date' => date('Y-m-d', strtotime($json_data['date']))),
            'limit' => 20,
            'order' => array('Memo.id'),
            'recursive' => -1
        ));
        $this->set(array(
            'result' => array('status' => 1, 'msg' => 'Sending 20 data for -' . $json_data['date'], 'memo_data' => $memo_data),
            '_serialize' => array('result')
        ));
    }
    public function post_data_json()
    {
        $this->loadModel('Memo');
        // $json_data = $this->request->input('json_decode', true);

        if (!$this->auth()) {
            $this->set(array(
                'result' => array('status' => 0, 'msg' => 'Wrong auth token'),
                '_serialize' => array('result')
            ));
        }
        $path = APP . 'logs/';
        $myfile = fopen($path . "sap_post.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        $this->set(array(
            'result' => array('status' => 1, 'msg' => 'Data received successfully'),
            '_serialize' => array('result')
        ));
    }
    private function auth()
    {
        $user_pass = $this->hash_user_password();
        if ($user_pass == $_SERVER['HTTP_AUTH_TOKEN']) {
            return true;
        } else {
            return false;
        }
    }
    private function hash_user_password()
    {
        $sha1 = Security::hash('SAP_Naser:SAP_pass', 'sha1', true);
        return $sha1;
    }
}
