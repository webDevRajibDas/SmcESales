<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
//Configure::write('debug',2);
class FullcareReportsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('Memo', 'Market', 'Program', 'Division', 'District', 'Thana', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'MemoDetail', 'MeasurementUnit', 'ProductCategory', 'TerritoryAssignHistory', 'Office', 'Territory', 'ProductType');
    
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */


    public function admin_index()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0); //300 seconds = 5 minutes


        $this->set('page_title', 'Fullcare Report');

        $this->loadModel('Product');

        $conditions = array(
            'NOT' => array('Product.product_category_id' => 32),
            'is_active' => 1,
            'Product.product_type_id' => 1
        );

        $list = $this->Product->find('all', array(
            'fields' => array('id', 'name'),
            'conditions' => $conditions,
            'recursive' => -1
        ));


        $product_list = array();

        foreach ($list as $key => $value) {
            $product_list[$value['Product']['id']] = $value['Product']['name'];
        }

        //echo "<pre>";print_r($product_list);exit();

        $this->loadModel('Memo');
        $this->loadModel('Division');
        $this->loadModel('ProgramType');

        $divisions = $this->Division->find('list', array(
            'order' =>  array('name' => 'asc'),
        ));

        $programstype = $this->ProgramType->find('list', array(
            'order' =>  array('name' => 'asc'),
        ));

        $unit_types = array(
            '1' => 'Sales Unit',
            '2' => 'Base Unit',
        );
        $this->set(compact('unit_types'));


        if ($this->request->is('post')) {

            $request_data = $this->request->data;

            $unit_type = $request_data['Memo']['unit_type'];

            $productid = $request_data['Memo']['product_id'];

            //$productid = 465;

            $productInfo = $this->Product->find('all', array(
                //'fields' => array('id', 'name', 'source'),
                'conditions' => array('Product.id' => $productid),
                'recursive' => -1
            ));

            $productInfo = $productInfo[0]['Product'];

            $date_from = date('Y-m-d', strtotime($request_data['Memo']['date_from']));
            $date_to = date('Y-m-d', strtotime($request_data['Memo']['date_to']));

            $sql = "SELECT SUM(memo_details.sales_qty) as vol, SUM(memo_details.sales_qty*memo_details.price) as amount, markets.location_type_id as loc_id, districts.division_id as divisions FROM memos 
				INNER JOIN memo_details ON memos.id = memo_details.memo_id
				INNER JOIN markets ON memos.market_id = markets.id
				INNER JOIN location_types ON markets.location_type_id = location_types.id
				INNER JOIN thanas ON markets.thana_id = thanas.id
				INNER JOIN districts ON thanas.district_id = districts.id
				INNER JOIN divisions ON districts.division_id = divisions.id
                WHERE memo_date BETWEEN '$date_from' AND '$date_to' AND memo_details.product_id IN ($productid) GROUP BY markets.location_type_id, districts.division_id";

            $result = $this->Memo->query($sql);


            foreach ($result as $key => $val) {

                $divisionid = $val[0]['divisions'];

                $dataArray[$divisionid] = array_filter($result, function ($var) use ($divisionid) {
                    return ($var[0]['divisions'] == $divisionid);
                });
            }

            //pr($productInfo);exit();
            $pid = $productInfo['id'];
            $sales_measurement_unit_id = $productInfo['sales_measurement_unit_id'];

            $i = 1;
            $uamount = 0;
            $ramount = 0;
            foreach ($dataArray as $key => $value) {

                $resData[$i]['division_id'] = $key;
                $resData[$i]['rural'] = 0;
                $resData[$i]['urban'] = 0;


                foreach ($value as $key => $v) {

                    if ($v[0]['loc_id'] == 6) {
                        $resData[$i]['urban'] = sprintf("%01.2f", ($unit_type == 1) ? $v[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $v[0]['vol']));

                        $uamount = $v[0]['amount'];
                    } else {

                        $resData[$i]['rural'] = sprintf("%01.2f", ($unit_type == 1) ? $v[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $v[0]['vol']));
                        $ramount = $v[0]['amount'];
                    }
                }

                $resData[$i]['amount'] = $uamount + $ramount;

                $i++;
            }


            $geoSql = "SELECT count(DISTINCT thanas.id) as total_thanaid, count(DISTINCT thanas.district_id) as total_district_id FROM memos 
             	INNER JOIN memo_details ON memos.id = memo_details.memo_id
				INNER JOIN markets ON memos.market_id = markets.id
				INNER JOIN thanas ON markets.thana_id = thanas.id
                WHERE memo_date BETWEEN '$date_from' AND '$date_to' AND memo_details.product_id IN ($productid) ";



            $geoCoverage = $this->Memo->query($geoSql);

            $totalgeocovereged = $geoCoverage[0][0];


            $bspSqlnew = "
            SELECT  memos.outlet_id as outlet_id, 
            memos.id as memo_id, programs.program_type_id
            FROM memos 
            	INNER JOIN memo_details ON memos.id = memo_details.memo_id
             	LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (2) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
				WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND  memo_details.product_id = $productid AND programs.program_type_id !=0 GROUP BY memos.id, memos.outlet_id, programs.program_type_id ";



            $bspnew = " select COUNT(DISTINCT m.outlet_id) AS oc, COUNT(DISTINCT m.id) AS ec, gsp_memo.program_type_id, 
			SUM(md.sales_qty) AS vol
			from memos m inner join memo_details md on md.memo_id=m.id
            inner join ($bspSqlnew) as gsp_memo on gsp_memo.memo_id=m.id
            where md.product_id=$productid
            group by gsp_memo.program_type_id

			";


            $bspResult = $this->Memo->query($bspnew);


            // echo "<pre>";print_r($bspnew);exit();

            foreach ($bspResult as $key => $val) {
                $outResutl_BSP_PCHP[0]['outletid'] = $val[0]['oc'];
                $outResutl_BSP_PCHP[0]['memoid'] = $val[0]['ec'];
                $outResutl_BSP_PCHP[0]['vol'] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
                $outResutl_BSP_PCHP[0]['program_type_id'] = $val[0]['program_type_id'];
            }

            /**********Thana Wise BSP Start**********/
            // $thana_array= [10485,100,265,10562,224];

            //$thana_array = array("10485"=>"Lalpur", "100"=>"Monohardi", "265"=>"Nasirnagar", "10562"=>"Raj Nagar", "224"=>"Wazirpur");
            /***********Thana wise BSP Start**********/
            $thana_wise_bsp_store = array();


            $bspcode_thana = "
                SELECT  memos.outlet_id as outlet_id, 
                memos.id as memo_id, programs.program_type_id
                FROM memos 
                    INNER JOIN memo_details ON memos.id = memo_details.memo_id
                    LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (2) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                    WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND memos.thana_id IN(10485,100,265,10562,224) AND memo_details.product_id = $productid AND programs.program_type_id !=0 GROUP BY memos.id, memos.outlet_id, programs.program_type_id ";

            $bspfull_thana = " select m.thana_id as thana_id,COUNT(DISTINCT m.outlet_id) AS oc, COUNT(DISTINCT m.id) AS ec, gsp_memo.program_type_id, 
                SUM(md.sales_qty) AS vol
                from memos m inner join memo_details md on md.memo_id=m.id
                inner join ($bspcode_thana) as gsp_memo on gsp_memo.memo_id=m.id
                where  m.thana_id IN(10485,100,265,10562,224) AND md.product_id=$productid
                group by gsp_memo.program_type_id,m.thana_id

                ";

            $bspResult_thana = $this->Memo->query($bspfull_thana);
            // echo "<pre>";print_r($bspResult_thana);exit();

            foreach ($bspResult_thana as $key => $val) {

                $thana_wise_bsp_store[$val[0]['thana_id']] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
            }



            // echo "<pre>";print_r($thana_wise_bsp_store);exit();

            /**********Thana Wise BSP end**********/

            $bsp_code = " 
             Select DISTINCT memos.id as bsp_memo_id
            FROM memos 
            	INNER JOIN memo_details ON memos.id = memo_details.memo_id
             	LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (2) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND memo_details.product_id = $productid AND programs.program_type_id !=0 ";



            $gspSql = "
            SELECT  memos.outlet_id as outlet_id, 
            memos.id as memo_id, programs.program_type_id
            FROM memos 
            	INNER JOIN memo_details ON memos.id = memo_details.memo_id
             	LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (1) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
				LEFT JOIN ($bsp_code) as BSP on BSP.bsp_memo_id=memos.id
                WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND BSP.bsp_memo_id is null AND memo_details.product_id = $productid AND programs.program_type_id !=0 GROUP BY memos.id, memos.outlet_id, programs.program_type_id ";


            $gspnew = " select COUNT(DISTINCT m.outlet_id) AS oc, COUNT(DISTINCT m.id) AS ec, gsp_memo.program_type_id, 
			SUM(md.sales_qty) AS vol
			from memos m inner join memo_details md on md.memo_id=m.id
            inner join ($gspSql) as gsp_memo on gsp_memo.memo_id=m.id
            where md.product_id=$productid
            group by gsp_memo.program_type_id

			";




            $gspResult = $this->Memo->query($gspnew);



            foreach ($gspResult as $key => $val) {
                $outResutl_BSP_PCHP[1]['outletid'] = $val[0]['oc'];
                $outResutl_BSP_PCHP[1]['memoid'] = $val[0]['ec'];
                $outResutl_BSP_PCHP[1]['vol'] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
                $outResutl_BSP_PCHP[1]['program_type_id'] = $val[0]['program_type_id'];
            }






            /*********Thana wise GSP Start***********/

            $thana_wise_gsp_store = array();




            $gsp_code_thana = " 
                Select DISTINCT memos.id as bsp_memo_id
                FROM memos 
                    INNER JOIN memo_details ON memos.id = memo_details.memo_id
                    LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (2) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                    WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND memos.thana_id IN(10485,100,265,10562,224) AND memo_details.product_id = $productid AND programs.program_type_id !=0 ";



            $gspSql_thana = "
                SELECT  memos.outlet_id as outlet_id, 
                memos.id as memo_id, programs.program_type_id
                FROM memos 
                    INNER JOIN memo_details ON memos.id = memo_details.memo_id
                    LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (1) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                    LEFT JOIN ($gsp_code_thana) as BSP on BSP.bsp_memo_id=memos.id
                    WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND memos.thana_id IN(10485,100,265,10562,224) AND BSP.bsp_memo_id is null AND memo_details.product_id = $productid AND programs.program_type_id !=0 GROUP BY memos.id, memos.outlet_id, programs.program_type_id ";


            $gspfull_thana = " select m.thana_id as thana_id,COUNT(DISTINCT m.outlet_id) AS oc, COUNT(DISTINCT m.id) AS ec, gsp_memo.program_type_id, 
                SUM(md.sales_qty) AS vol
                from memos m inner join memo_details md on md.memo_id=m.id
                inner join ($gspSql_thana) as gsp_memo on gsp_memo.memo_id=m.id
                where m.thana_id IN(10485,100,265,10562,224) AND md.product_id=$productid
                group by gsp_memo.program_type_id,m.thana_id

                ";
            $gspResult_thana = $this->Memo->query($gspfull_thana);

            foreach ($gspResult_thana as $key => $val) {


                $thana_wise_gsp_store[$val[0]['thana_id']] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
            }



            // echo "<pre>";print_r($thana_wise_gsp_store);exit();



            /*********Thana wise GSP End **********/


            $where_nutundin = " ";

            /*$nutonSql = "SELECT count(DISTINCT memos.outlet_id) as outletid, count(DISTINCT memos.id) as memoid FROM memos 
            	INNER JOIN memo_details ON memos.id = memo_details.memo_id
             	INNER JOIN outlets ON memos.outlet_id = outlets.id
                
             	LEFT JOIN notundin_programs ON outlets.institute_id = notundin_programs.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from')
                WHERE memo_date BETWEEN '$date_from' AND '$date_to'  AND memo_details.product_id ='$productid' AND programs.program_type_id !=0 ";*/

            $bsp_gsp_memo_id = " 
             Select DISTINCT memos.id as bspgsp_memo_id
            FROM memos 
            	INNER JOIN memo_details ON memos.id = memo_details.memo_id
             	LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (1,2) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND memo_details.product_id = $productid AND programs.program_type_id !=0 ";

            $notonDinSql = "SELECT  memos.outlet_id as outlet_id,  memos.id as memo_id FROM memos 
                INNER JOIN memo_details ON memos.id = memo_details.memo_id
                INNER JOIN outlets ON memos.outlet_id = outlets.id
                INNER JOIN notundin_programs ON outlets.institute_id = notundin_programs.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from')
				LEFT JOIN ($bsp_gsp_memo_id) as BSPGSP on BSPGSP.bspgsp_memo_id=memos.id
                WHERE memo_date BETWEEN '$date_from' AND '$date_to' AND BSPGSP.bspgsp_memo_id is null  
				AND memo_details.product_id ='$productid' group by memos.id, memos.outlet_id";

            /*

            $notonDinSql = "SELECT  memos.outlet_id as outlet_id,  memos.id as memo_id FROM memos 
                INNER JOIN memo_details ON memos.id = memo_details.memo_id
                INNER JOIN outlets ON memos.outlet_id = outlets.id
                INNER JOIN notundin_programs ON outlets.institute_id = notundin_programs.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from')
                WHERE memo_date BETWEEN '$date_from' AND '$date_to'  AND memo_details.product_id ='$productid' group by memos.id, memos.outlet_id";
			
			*/

            $notondinsqlnew = " select COUNT(DISTINCT m.outlet_id) AS outletid, COUNT(DISTINCT m.id) AS memoid,
						SUM(md.sales_qty) AS vol
						from memos m inner join memo_details md on md.memo_id=m.id
					inner join ($notonDinSql) as gsp_memo on gsp_memo.memo_id=m.id
			where md.product_id=$productid
			

						";
            //echo "<pre>";print_r($new);

            $notondinResult = $this->Memo->query($notondinsqlnew);







            foreach ($notondinResult as $key => $val) {
                $outResutl_nutundin[$key]['outletid'] = $val[0]['outletid'];
                $outResutl_nutundin[$key]['memoid'] = $val[0]['memoid'];
                $outResutl_nutundin[$key]['vol'] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
            }


            /**************Thana wise notundin Start************/

            $thana_wise_notundin_store = array();




            $notun_din_code_thana = " 
                Select DISTINCT memos.id as bspgsp_memo_id
                FROM memos 
                    INNER JOIN memo_details ON memos.id = memo_details.memo_id
                    LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (1,2) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                    WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND memos.thana_id IN(10485,100,265,10562,224) AND memo_details.product_id = $productid AND programs.program_type_id !=0 ";

            $notonDinSql_thana = "SELECT  memos.outlet_id as outlet_id,  memos.id as memo_id FROM memos 
                    INNER JOIN memo_details ON memos.id = memo_details.memo_id
                    INNER JOIN outlets ON memos.outlet_id = outlets.id
                    INNER JOIN notundin_programs ON outlets.institute_id = notundin_programs.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from')
                    LEFT JOIN ($notun_din_code_thana) as BSPGSP on BSPGSP.bspgsp_memo_id=memos.id
                    WHERE memo_date BETWEEN '$date_from' AND '$date_to' AND memos.thana_id IN(10485,100,265,10562,224) AND BSPGSP.bspgsp_memo_id is null  
                    AND memo_details.product_id ='$productid' group by memos.id, memos.outlet_id";



            $notondinsqlnew_thana = "select m.thana_id as thana_id,COUNT(DISTINCT m.outlet_id) AS outletid, COUNT(DISTINCT m.id) AS memoid,
                            SUM(md.sales_qty) AS vol
                            from memos m inner join memo_details md on md.memo_id=m.id
                        inner join ($notonDinSql_thana) as gsp_memo on gsp_memo.memo_id=m.id
                where m.thana_id IN(10485,100,265,10562,224) And md.product_id=$productid group by m.thana_id ";
            //echo "<pre>";print_r($new);

            $notondinResult_thana = $this->Memo->query($notondinsqlnew_thana);

            foreach ($notondinResult_thana as $key => $val) {

                $thana_wise_notundin_store[$val[0]['thana_id']] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
            }



            /**************Thana wise notundin END************/



            $bsp_gsp_code = " 
             Select DISTINCT memos.id as bsp_memo_id
            FROM memos 
            	INNER JOIN memo_details ON memos.id = memo_details.memo_id
             	LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (1,2) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND memo_details.product_id = $productid AND programs.program_type_id !=0 ";


            $wherengo = "((programs.id is null and outlets.category_id =11) OR programs.program_type_id NOT IN (1,2)) and notundin_programs.id is null ";

            $outletngoSql = "SELECT  memos.outlet_id as outlet_id, memos.id as memo_id FROM memos 
                INNER JOIN outlets ON memos.outlet_id = outlets.id
                left join notundin_programs on notundin_programs.institute_id=outlets.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from') 
            	INNER JOIN memo_details ON memos.id = memo_details.memo_id
             	LEFT JOIN programs ON memos.outlet_id = programs.outlet_id AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
				LEFT JOIN ($bsp_gsp_code) as BSP_GSP on BSP_GSP.bsp_memo_id=memos.id
                WHERE memo_date BETWEEN '$date_from' AND '$date_to'  AND BSP_GSP.bsp_memo_id is null AND $wherengo  AND memo_details.product_id IN ($productid) AND memos.status!=0 and memos.gross_value>0 group by memos.id, memos.outlet_id";


            $ngosqlnew = " select COUNT(DISTINCT m.outlet_id) AS outletid, COUNT(DISTINCT m.id) AS memoid,
						SUM(md.sales_qty) AS vol
						from memos m inner join memo_details md on md.memo_id=m.id
					inner join ($outletngoSql) as gsp_memo on gsp_memo.memo_id=m.id
			where md.product_id=$productid
			

						";


            // echo $outletngoSql;

            $outletngoResult = $this->Memo->query($ngosqlnew);



            // pr($thana_wise_outlet_ngo_store);exit();

            foreach ($outletngoResult as $key => $val) {
                $outResutl_ngo[$key]['outletid'] = $val[0]['outletid'];
                $outResutl_ngo[$key]['memoid'] = $val[0]['memoid'];
                $outResutl_ngo[$key]['vol'] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
            }


            /************Thana wise outlet ngo Start***************/

            $thana_wise_outlet_ngo_store = array();



            $bsp_gsp_code_thana = " 
                Select DISTINCT memos.id as bsp_memo_id
               FROM memos 
                   INNER JOIN memo_details ON memos.id = memo_details.memo_id
                    LEFT JOIN programs ON memos.outlet_id = programs.outlet_id and programs.program_type_id IN (1,2) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                   WHERE  memo_date BETWEEN '$date_from' AND '$date_to' AND memos.thana_id IN(10485,100,265,10562,224) AND memo_details.product_id = $productid AND programs.program_type_id !=0 ";


            $wherengo_thana = "((programs.id is null and outlets.category_id =11) OR programs.program_type_id NOT IN (1,2)) and notundin_programs.id is null ";

            $outletngoSql_thana = "SELECT  memos.outlet_id as outlet_id, memos.id as memo_id FROM memos 
                   INNER JOIN outlets ON memos.outlet_id = outlets.id
                   left join notundin_programs on notundin_programs.institute_id=outlets.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from') 
                   INNER JOIN memo_details ON memos.id = memo_details.memo_id
                    LEFT JOIN programs ON memos.outlet_id = programs.outlet_id AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                   LEFT JOIN ($bsp_gsp_code_thana) as BSP_GSP on BSP_GSP.bsp_memo_id=memos.id
                   WHERE memo_date BETWEEN '$date_from' AND '$date_to' AND memos.thana_id IN(10485,100,265,10562,224) AND BSP_GSP.bsp_memo_id is null AND $wherengo_thana  AND memo_details.product_id IN ($productid) AND memos.status!=0 and memos.gross_value>0 group by memos.id, memos.outlet_id";


            $ngosqlnew_thana = " select m.thana_id as thana_id,COUNT(DISTINCT m.outlet_id) AS outletid, COUNT(DISTINCT m.id) AS memoid,
                           SUM(md.sales_qty) AS vol
                           from memos m inner join memo_details md on md.memo_id=m.id
                       inner join ($outletngoSql_thana) as gsp_memo on gsp_memo.memo_id=m.id
               where m.thana_id IN(10485,100,265,10562,224) AND md.product_id=$productid Group by m.thana_id
               
   
                           ";




            $outletngoResult_thana = $this->Memo->query($ngosqlnew_thana);

            foreach ($outletngoResult_thana as $key => $val) {

                $thana_wise_outlet_ngo_store[$val[0]['thana_id']] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
            }
            // echo "<pre>";print_r($thana_wise_outlet_ngo_store);exit();

            /************Thana wise outlet ngo End***************/




            $whereotherpharma = " (programs.id is null ) AND (notundin_programs.id is null)
            	and outlets.category_id!=11
                and outlets.is_pharma_type=1
            ";

            $outletpharmaSql = "SELECT count(DISTINCT memos.outlet_id) as outletid, count(DISTINCT memos.id) as memoid,SUM(memo_details.sales_qty) as vol FROM memos 
            	INNER JOIN memo_details ON memos.id = memo_details.memo_id
             	left JOIN programs ON memos.outlet_id = programs.outlet_id AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
             	INNER JOIN outlets ON memos.outlet_id = outlets.id
                left join notundin_programs on notundin_programs.institute_id=outlets.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from') 
                WHERE memo_details.product_id IN ($productid) AND  $whereotherpharma AND memo_date BETWEEN '$date_from' AND '$date_to' AND memos.status!=0 and memos.gross_value>0";

            $outletnpharmaResult = $this->Memo->query($outletpharmaSql);

            $outletnpharmaResult[0][0]['vol'] = sprintf("%01.2f", ($unit_type == 1) ? $outletnpharmaResult[0][0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $outletnpharmaResult[0][0]['vol']));
            $outletnpharmaResult = $outletnpharmaResult[0][0];

            /************Thana wise outlet Pharma = other pharma Start***************/
            $thana_wise_outlet_pharma_store = array();



            $whereotherpharma_thana = " (programs.id is null ) AND (notundin_programs.id is null)
                and outlets.category_id!=11
                and outlets.is_pharma_type=1
            ";

            $outletpharmaSql_thana = "SELECT memos.thana_id as thana_id,count(DISTINCT memos.outlet_id) as outletid, count(DISTINCT memos.id) as memoid,SUM(memo_details.sales_qty) as vol FROM memos 
                INNER JOIN memo_details ON memos.id = memo_details.memo_id
                left JOIN programs ON memos.outlet_id = programs.outlet_id AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                INNER JOIN outlets ON memos.outlet_id = outlets.id
                left join notundin_programs on notundin_programs.institute_id=outlets.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from') 
                WHERE memo_details.product_id IN ($productid) AND  $whereotherpharma_thana AND memo_date BETWEEN '$date_from' AND '$date_to' AND memos.thana_id IN(10485,100,265,10562,224) AND memos.status!=0 and memos.gross_value>0 Group by memos.thana_id";

            $outletnpharmaResult_thana = $this->Memo->query($outletpharmaSql_thana);

            // echo "<pre>";
            // print_r($outletnpharmaResult_thana);exit;
            foreach ($outletnpharmaResult_thana as $key => $val) {

                $thana_wise_outlet_pharma_store[$val[0]['thana_id']] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
            }





            /************Thana wise outlet Pharma = other pharma End***************/

            $wherotherpharma = " (programs.id is null) AND (notundin_programs.id is null)
            	
                and outlets.category_id!=11
                and outlets.is_pharma_type=0
            ";

            $ortherpharmaSql = "SELECT count(DISTINCT memos.outlet_id) as outletid, count(DISTINCT memos.id) as memoid,SUM(memo_details.sales_qty) as vol FROM memos 
            	INNER JOIN memo_details ON memos.id = memo_details.memo_id
             	LEFT JOIN programs ON memos.outlet_id = programs.outlet_id AND 
             	programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
             	INNER JOIN outlets ON memos.outlet_id = outlets.id
                left join notundin_programs on notundin_programs.institute_id=outlets.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from')
                WHERE  memo_details.product_id IN ($productid) AND  $wherotherpharma AND memo_date BETWEEN '$date_from' AND '$date_to' AND memos.status!=0 and memos.gross_value>0";

            $ortehrpharmaResult = $this->Memo->query($ortherpharmaSql);








            $ortehrpharmaResult[0][0]['vol'] = sprintf("%01.2f", ($unit_type == 1) ? $ortehrpharmaResult[0][0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $ortehrpharmaResult[0][0]['vol']));
            $ortehrpharmaResult = $ortehrpharmaResult[0][0];

            /*************Thana wise other Pharma = other  Start *********/
            $thana_wise_other_pharma_store = array();

            $wherotherpharma_thana = " (programs.id is null) AND (notundin_programs.id is null)
            	
                    and outlets.category_id!=11
                    and outlets.is_pharma_type=0
                ";

            $ortherpharmaSql_thana = "SELECT memos.thana_id as thana_id,count(DISTINCT memos.outlet_id) as outletid, count(DISTINCT memos.id) as memoid,SUM(memo_details.sales_qty) as vol FROM memos 
                    INNER JOIN memo_details ON memos.id = memo_details.memo_id
                    LEFT JOIN programs ON memos.outlet_id = programs.outlet_id AND 
                    programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')
                    INNER JOIN outlets ON memos.outlet_id = outlets.id
                    left join notundin_programs on notundin_programs.institute_id=outlets.institute_id and notundin_programs.assigned_date <= '$date_to' AND (notundin_programs.deassigned_date is null OR notundin_programs.deassigned_date >'$date_from')
                    WHERE  memo_details.product_id IN ($productid) AND  $wherotherpharma_thana AND memo_date BETWEEN '$date_from' AND '$date_to' AND memos.thana_id IN(10485,100,265,10562,224) AND memos.status!=0 and memos.gross_value>0 Group by memos.thana_id";

            $ortehrpharmaResult_thana = $this->Memo->query($ortherpharmaSql_thana);

            foreach ($ortehrpharmaResult_thana as $key => $val) {

                $thana_wise_other_pharma_store[$val[0]['thana_id']] = sprintf("%01.2f", ($unit_type == 1) ? $val[0]['vol'] : $this->unit_convert($pid, $sales_measurement_unit_id, $val[0]['vol']));
            }




            /*************Thana wise other Pharma = other  End *********/



            if (empty($resData) && empty($totalgeocovereged) && empty($programstype) && empty($outResutl_BSP_PCHP) && empty($outResutl_nutundin) && empty($outResutl_ngo) && empty($outletnpharmaResult) && empty($ortehrpharmaResult)) {
                $dataresult = 1;
            } else {
                $dataresult = 0;
            }
            // thana_wise_bsp_store, 
            // thana_wise_gsp_store , 
            // thana_wise_notundin_store , 
            // thana_wise_outlet_ngo_store, 
            // thana_wise_outlet_pharma_store, 
            // thana_wise_other_pharma_store 

            $this->set(compact('resData', 'divisions', 'request_data', 'totalgeocovereged', 'programstype', 'outResutl_BSP_PCHP', 'outResutl_nutundin', 'outResutl_ngo', 'outletnpharmaResult', 'ortehrpharmaResult', 'productInfo', 'dataresult', 'thana_wise_bsp_store', 'thana_wise_gsp_store', 'thana_wise_notundin_store', 'thana_wise_outlet_ngo_store', 'thana_wise_outlet_pharma_store', 'thana_wise_other_pharma_store'));

            $this->request->data['Memo']['product_id'] = $productid;
        } else {
            $this->request->data['Memo']['product_id'] = 465;
        }


        $this->set(compact('product_list'));
    }

    public function admin_fullcare_sales_volume_provider_report()
	{

		$this->set('page_title', 'FullCare Sales Volume Program Provider Report');

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 99999); //300 seconds = 5 minutes

        $conditions = array(
            'NOT' => array('Product.product_category_id' => 32),
            'is_active' => 1,
            'Product.product_type_id' => 1
        );

        $product_list = $this->Product->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
		
		//for divisions
		$divisions = $this->Division->find('list', array(
			'order' =>  array('name' => 'asc')
		));
		//end for divisions
        


		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('office_parent_id'));

		if ($office_parent_id == 0) {
			$office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
			$office_id = 0;
		} elseif ($office_parent_id == 14) {
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

			$office_id = array_keys($offices);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();
		}

        $this->set(compact('product_list', 'divisions', 'office_id'));


		$request_data = array();

		if ($this->request->is('post')) {
			

            $product_id = isset($this->request->data['Memo']['product_id']) != '' ? $this->request->data['Memo']['product_id'] : 0;
            $division_id = isset($this->request->data['Memo']['division_id']) != '' ? $this->request->data['Memo']['division_id'] : 0;
            $district_id = isset($this->request->data['Memo']['district_id']) != '' ? $this->request->data['Memo']['district_id'] : 0;
			$thana_id = isset($this->request->data['Memo']['thana_id']) != '' ? $this->request->data['Memo']['thana_id'] : 0;

          
			$date_from = date('Y-m-d', strtotime($this->request->data['Memo']['date_from']));
			$date_to = date('Y-m-d', strtotime($this->request->data['Memo']['date_to']));


            //district list 
			$districts = $this->District->find('list', array(
				'conditions' => array('District.division_id' => $division_id),
				'order' => array('District.name' => 'asc')
			));
			

            $thanas = $this->Thana->find('list', array(
                'conditions' => array('Thana.district_id' => $district_id),
                'order' => array('Thana.name' => 'asc')
            ));
			
			
			$request_data = $this->request->data;

            $this->set(compact('districts', 'thanas', 'date_from', 'date_to', 'request_data'));

            $conditions = array(
                'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
                'Memo.gross_value >' => 0,
                'Memo.status !=' => 0,
            );
            
            if($product_id)$conditions['MemoDetail.product_id'] = $product_id;
            if ($division_id) $conditions['Division.id'] = $division_id;
            if ($district_id) $conditions['District.id'] = $district_id;
			if ($thana_id) $conditions['Thana.id'] = $thana_id;
			if ($office_id) $conditions['Office.id'] = $office_id;

            
           $fields = array(
                'Office.office_name',
                'Territory.name',
                'SalesPeople.name',
                'Thana.name',
                'Market.name',
                'Outlet.name',
                'ProgramCode.code',
                'Memo.memo_date',
                'Memo.memo_no',
                'MemoDetail.sales_qty as qty'
             );
            $group = array(
                'Office.office_name',
                'Territory.name',
                'SalesPeople.name',
                'Thana.name',
                'Market.name',
                'Outlet.name',
                'ProgramCode.code',
                'Memo.memo_date',
                'Memo.memo_no',
                'MemoDetail.sales_qty'
            );

            $where = " where programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')";

            $results = $this->Memo->find('all', array(
                'conditions' => $conditions,
                'joins' => array(
                    array(
                        'alias' => 'MemoDetail',
                        'table' => 'memo_details',
                        'type' => 'INNER',
                        'conditions' => 'Memo.id = MemoDetail.memo_id'
                    ),
                    array(
                        'alias' => 'Office',
                        'table' => 'offices',
                        'type' => 'INNER',
                        'conditions' => 'Memo.office_id = Office.id'
                    ),
                    array(
                        'alias' => 'Product',
                        'table' => 'products',
                        'type' => 'INNER',
                        'conditions' => 'MemoDetail.product_id = Product.id'
                    ),
                    array(
                        'alias' => 'Territory',
                        'table' => 'territories',
                        'type' => 'LEFT',
                        'conditions' => 'Memo.territory_id = Territory.id'
                    ),
                    array(
                        'alias' => 'SalesPeople',
                        'table' => 'sales_people',
                        'type' => 'LEFT',
                        'conditions' => 'Territory.id = SalesPeople.territory_id'
                    ),
                    array(
                        'alias' => 'Outlet',
                        'table' => 'outlets',
                        'type' => 'INNER',
                        'conditions' => 'Memo.outlet_id = Outlet.id'
                    ),
                    array(
                        'alias' => 'ProgramOutlet',
                        'table' => "(select outlet_id from programs $where group by outlet_id )",
                        'type' => 'INNER',
                        'conditions' => 'ProgramOutlet.outlet_id = Memo.outlet_id'
                    ),
                    array(
                        'alias' => 'ProgramCode',
                        'table' => "programs",
                        'type' => 'INNER',
                        'conditions' => 'ProgramCode.outlet_id = ProgramOutlet.outlet_id'
                    ),
                    array(
                        'alias' => 'Market',
                        'table' => 'markets',
                        'type' => 'INNER',
                        'conditions' => 'Outlet.market_id = Market.id'
                    ),
                    array(
                        'alias' => 'Thana',
                        'table' => 'thanas',
                        'type' => 'INNER',
                        'conditions' => 'Market.thana_id = Thana.id'
                    ),
                    array(
                        'alias' => 'District',
                        'table' => 'districts',
                        'type' => 'INNER',
                        'conditions' => 'Thana.district_id = District.id'
                    ),
                    array(
                        'alias' => 'Division',
                        'table' => 'divisions',
                        'type' => 'INNER',
                        'conditions' => 'District.division_id = Division.id'
                    )
                ),
                'fields' => $fields,
                'group' => $group,
                'recursive' => -1
            ));
           //pr($results);exit;
            $this->set(compact('results'));

		}else {
            $this->request->data['Memo']['product_id'] = 465;
        }

	}
}
