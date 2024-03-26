<?php
App::uses('AppController', 'Controller');

/**
 * Controller
 *
 * @property ApiDataRetrives $ApiDataRetrives
 * @property PaginatorComponent $Paginator
 */
class ApiDataBanksController extends AppController
{
    public $components = array('RequestHandler', 'Usermgmt.UserAuth');
    function beforeFilter()
    {
        /* $remote_address = $_SERVER['REMOTE_ADDR'];
        if ($remote_address != '172.16.8.130') {
            $res['status'] = false;
            $res['msg'] = 'IP Address not matching';
            echo json_encode(array("results" => $res));
            exit;
        } */
    }
    public function push_transaction()
    {
        $json_data = $this->request->input('json_decode', true);
        $json = $this->request->input();
        /* removing unicode characters */
        $raw_json_data = str_replace("蹢", '"', $json);
        $json_data = json_decode($raw_json_data, TRUE);
        $all_inserted = true;
        $relation_array = array();
        $path = APP . 'logs/';
        $myfile = fopen($path . "bank_push.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
        foreach ($json_data['transaction_list'] as $trn_data) {
            $relation_array[] = $trn_data['transaction_ref_no'];
        }
        if ($all_inserted) {
            $res['status'] = true;
            $res['successful_transaction'] = $relation_array;
        } else {
            $res['status'] = false;
            $res['successful_transaction'] = $relation_array;
        }
        $this->set(array(
            'results' => $res,
            '_serialize' => array('results')
        ));
    }
}
