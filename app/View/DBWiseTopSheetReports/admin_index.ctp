<?php
App::import('Controller', 'DBWiseTopSheetReportsController');
$db_wise_topsheet_report = new DBWiseTopSheetReportsController;

?>



<style>
    .search .radio label {
        width: auto;
        float: none;
        padding: 0px 15px 0px 5px;
        margin: 0px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 15%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
    }

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 33%;
        float: left;
        margin: 1px 0;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title; ?></h3>
            </div>

            <div class="box-body">
                <div class="search-box">

                    <?php echo $this->Form->create('Memo', array('role' => 'form')); ?>

                    <table class="search">
                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                            <td class="required">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                        </tr>
                        <?php if ($office_parent_id == 0) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'empty' => '---- Head Office ----', 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                        <?php if ($office_parent_id == 14) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                            <tr>
                                <td class="" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- All ----')); ?></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td class="" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">DB : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
                                        <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection2 so_list">

                                        <?php echo $this->Form->input('db_id', array('label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $dbs)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">

                                <?php echo $this->Form->input('product_type', array('legend' => 'Product Type :', 'class' => 'product_type', 'type' => 'radio', 'default' => '1', 'options' => $product_type_list, 'required' => true));  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            
                                <?php echo $this->Form->input('col_id', array('class' => 'col_id', 'type' => 'radio', 'default' => 'db', 'options' => $by_colums, 'label' => true, 'legend' => 'Columns Selection :'));  ?>
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                               
                                <label style="float:left; width:15%;">Product Column :</label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                    <div class="selection">
                                        <?php echo $this->Form->input('return_column', array('class'=>'return_column', 'id' => 'return_column', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $columnlist)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Products : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                    <div style="margin:auto; width:90%; float:left;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                        <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="product selection" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto">
                                        <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>



                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>

            <script>
                //$(input[type='checkbox']).iCheck(false); 
                $(document).ready(function() {
                    
                    $("input[type='checkbox']").iCheck('destroy');
                    $("input[type='radio']").iCheck('destroy');
                    $('#checkall2').click(function() {
                        var checked = $(this).prop('checked');
                        $('.selection2').find('input:checkbox').prop('checked', checked);
                    });
                    $('#checkall').click(function() {
                        var checked = $(this).prop('checked');
                        $('.selection').find('input:checkbox').prop('checked', checked);
                    });

                    $(".btn-primary").click(function(){

                        var j = 1;

                        for (i = 0; i < 3; i++) {
                            
                           if($('#return_column'+i).is(":checked")){
                                j = 2;
                            } 
                          

                        }

                        if(j == 1){
                            alert('Please select product column.');
                            $("#divLoading_default").removeClass("show");
                            return false;
                        }

                    });

                    
                });
            </script>


            <?php if ($request_data) { ?>
                <!-- Report Print -->
                <div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%; float:left;">


                    <style type="text/css">
                        .table-responsive {
                            color: #333;
                            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                            line-height: 1.42857;
                        }

                        .report_table {
                            font-size: 12px;
                        }

                        .qty_val {
                            width: 90px;
                            margin: 0;
                            float: left;
                            text-transform: capitalize;
                        }

                        /*.qty, .val{ width:49%; float:left; border-right:#333 solid 1px; text-align:center; padding:5px 0;}*/
                        .val {
                            border-right: none;
                        }

                        p {
                            margin: 2px 0px;
                        }

                        .bottom_box {
                            float: left;
                            width: 33.3%;
                            text-align: center;
                        }

                        td,
                        th {
                            padding: 0;
                        }

                        table {
                            border-collapse: collapse;
                            border-spacing: 0;
                        }

                        .titlerow,
                        .totalColumn {
                            background: #f1f1f1;
                        }

                        #sum_table {
                            font-size: 75%;
                        }

                        .report_table {
                            margin-bottom: 18px;
                            max-width: 100%;
                            width: 100%;
                        }

                        .table-responsive {
                            min-height: 0.01%;
                            overflow-x: auto;
                        }

                        #sum_table td {
                            padding: 5px 0;
                            text-align: center;
                        }
                    </style>

                    <div class="table-responsive">
                        <div class="pull-right csv_btn" style="padding-top:20px;">

                            <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                        </div>

                        <div id="xls_body">
                            <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                                <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
                                <h3 style="margin:2px 0;"><?= $page_title; ?></h3>
                                <p>
                                    <b> Time Frame : <?= @date('d M, Y', strtotime($date_from)) ?> to <?= @date('d M, Y', strtotime($date_to)) ?></b>
                                </p>
                                <p>
                                    <?php if ($region_office_id) { ?>
                                        <span>Region Office: <?= $region_offices[$region_office_id] ?></span>
                                    <?php } ?>


                                    <?php if ($office_id) { ?>
                                        <span><?= ($region_office_id) ? ', ' : '' ?>Area Office: <?= $offices[$office_id] ?></span>
                                    <?php } ?>
                                </p>
                                <p>Measuring Unit : <?= $unit_type_text ?> Unit</p>
                            </div>



                            <?php if (@$product_quantity) { ?>
                                <!-- product quantity get-->
                                <?php
                                $product_qnty = array();
                                $product_price = array();
                                $product_cyp_v = array();
                                $product_cyp = array();
                                //pr($product_quantity);
                                foreach ($product_quantity as $data) {

                                    if($this->request->data['Memo']['col_id'] == 'db'){
                                        $nameid = 'db_id';
                                    }
                                    if($this->request->data['Memo']['col_id'] == 'tso'){
                                        $nameid = 'tso_id';
                                    }

                                    if($this->request->data['Memo']['col_id'] == 'ae'){
                                        $nameid = 'ae_id';
                                    }
                                   
                                    $product_qnty[$data['0'][$nameid]][$data['0']['product_id']] = $data['0']['pro_quantity'];
                                    $product_price[$data['0'][$nameid]][$data['0']['product_id']] = $data['0']['price'];
                                    $product_cyp_v[$data['0'][$nameid]][$data['0']['product_id']] = $data['0']['cyp_v'];
                                    $product_cyp[$data['0'][$nameid]][$data['0']['product_id']] = $data['0']['cyp'];


                                }
                                //pr($product_qnty);
                                //exit;
                                ?>


                                <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                    <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                        <tr class="titlerow">
                                            <th rowspan="2">
                                                <div class="qty_val" style="width:140px;">Area Office</div>
                                            </th>

                                            <?php if($nameid == 'db_id'){ ?>
                                            <th rowspan="2">
                                                <div class="qty_val" style="width:140px;">Area Executive</div>
                                            </th>
                                            <th rowspan="2">
                                                <div class="qty_val" style="width:140px;">TSO</div>
                                            </th>
                                            <th rowspan="2">
                                                <div class="qty_val" style="width:140px;">Distributor</div>
                                            </th>
                                            <?php } ?>

                                            <?php if($nameid == 'tso_id'){ ?>
                                                <th rowspan="2">
                                                <div class="qty_val" style="width:140px;">Area Executive</div>
                                            </th>
                                            <th rowspan="2">
                                                <div class="qty_val" style="width:140px;">TSO</div>
                                            </th>
                                            <?php } ?>

                                            <?php if($nameid == 'ae_id'){ ?>
                                                <th rowspan="2">
                                                <div class="qty_val" style="width:140px;">Area Executive</div>
                                            </th>
                                            <?php } ?>

                                            <?php foreach ($categories_products as $c_value) { ?>

                                                <?php if ($c_value['Product']) { ?>
                                                    <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                        <th colspan="<?=$totalcolum;?>">
                                                            <div class="qty_val"><?= $p_value['name'] ?></div>
                                                        </th>
                                                    <?php } ?>
                                                    <th style="background:#CCF;" colspan="<?=$totalcolum;?>">
                                                        <div class="qty_val">Total <?= $c_value['ProductCategory']['name'] ?></div>
                                                    </th>
                                                <?php } ?>

                                            <?php } ?>

                                            <th rowspan="2">
                                                <div class="qty_val">Total Sales<br>(Tk.)</div>
                                            </th>
                                            <?php if($num_column == 2){ ?>
                                            <th rowspan="2">
                                                <div class="qty_val">Total Return<br>(Tk.)</div>
                                            </th>
                                            <?php } ?>

                                            <th rowspan="2">
                                                <div class="qty_val">CYP</div>
                                            </th>
                                            <th rowspan="2">
                                                <div class="qty_val">EC Call</div>
                                            </th>
                                        </tr>

                                        <tr class="titlerow">
                                            <?php foreach ($categories_products as $c_value) { ?>

                                                <?php if ($c_value['Product']) { ?>
                                                    <?php foreach ($c_value['Product'] as $p_value) { ?>

                                                        <?php if($sales == 'YES'){ ?>
                                                            <th>Sales</th>
                                                        <?php } ?>

                                                        <?php if($value == 'YES'){ ?>
                                                            <th>Value</th>
                                                        <?php } ?>

                                                        <?php if($retrun == 'YES'){ ?>
                                                            <th>Return</th>
                                                        <?php } ?>

                                                    <?php } ?>

                                                    <?php if($sales == 'YES'){ ?>
                                                        <th style="background:#CCF;">Sales</th>
                                                    <?php } ?>

                                                    <?php if($value == "YES"){ ?>
                                                        <th style="background:#CCF;">Value</th>
                                                    <?php } ?>

                                                    <?php if($retrun == "YES"){ ?>
                                                        <th style="background:#CCF;">Return</th>
                                                    <?php } ?>


                                                <?php } ?>

                                            <?php } ?>

                                        </tr>


                                        <?php
                                        //pr($sales_people);
                                        $name_for_tso = '';
                                        $name_for_ae = '';
                                        $p=1;
                                        $q=1;

                                        $tso_p_qty =0;
                                        $tso_p_price =0;
                                        $tso_p_return =0;
                                        $tsototalsales =0;
                                        $tsototalcyp =0;
                                        $tsoefectivecall =0;

                                        $ae_p_qty =0;
                                        $ae_p_price =0;
                                        $ae_p_return =0;
                                        $aetotalsales =0;
                                        $aetotalcyp =0;
                                        $aeefectivecall =0;

                                        foreach ($db_list as $db_id => $db_other) {
                                            //$so_id = $data_s['0']['sales_person_id'];
                                            if($nameid == 'db_id'){ 
                                                $db_id_explode = explode('_',$db_id);
                                                $db_id = str_replace("'", "", $db_id_explode[0]);
                                            }
                                            if (!isset($db_sales_results[$db_id])) {
                                                continue;
                                            }
                                           
                                            $data = $db_sales_results[$db_id];


                                        ?>
                                            <?php 

                                                if($nameid == 'db_id'){

                                                    if(empty($db_other['tso_id'])){
                                                        $db_other['tso_id'] = 0;
                                                    }
        
                                                   $tsoture = 1;

                                                    $aename[$p] = $db_other['ae'];

                                                    if($p == 1){

                                                        $name_for_tso = $db_other['tso_id'];

                                                        if(empty($name_for_tso)){
                                                            $name_for_tso = '0';
                                                            $tsoture = 0;
                                                        }

                                                    }

                                                    $p++;
                                                    
                                                    
                                                    if( $name_for_tso != $db_other['tso_id'] AND $tsoture > 0){
                                                       
                                            ?>
                                            <tr>
                                                <td><?= $db_other['office'] ?></td>
                                                <td><?= $aename[$p-2] ?></td>
                                                <td colspan="2" align="right" style="text-align:right;">
                                                 <b> Total &nbsp; </b> </td>
                                                
                                                <?php foreach ($categories_products as $c_value) { ?>
                                                    <?php if ($c_value['Product']) { ?>
                                                        <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                            
                                                           
                                                            <?php if($sales == 'YES'){ ?>
                                                                <td> <?php echo $tso_pro_qty[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                            <?php if($value == 'YES'){ ?>
                                                                <td> <?php echo $tso_pro_price[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                            <?php if($retrun == "YES"){ ?>
                                                                <td> <?php echo $tso_pro_return[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                        <?php } ?>

                                                        <?php if($sales == 'YES'){ ?>
                                                        <td > <?= $tso_p_qty  ?> </td>
                                                        <?php } ?>

                                                        <?php if($value == 'YES'){ ?>
                                                        <td> <?= $tso_p_price  ?> </td>
                                                        <?php } ?>

                                                        <?php if($retrun == "YES"){ ?>
                                                        <td > <?= $tso_p_return  ?> </td>
                                                        <?php } ?>

                                                    <?php } ?>

                                            <?php } ?>
                                                    <td><?= $tsototalsales  ?></td>
                                                    <td><?= $tsototalcyp  ?></td>
                                                    <td><?= $tsoefectivecall  ?></td>
                                                


                                            </tr>

                                            <?php 
                                                $p=1;
                                                $name_for_tso = $db_other['tso_id'];

                                                $tso_pro_qty = array();
                                                $tso_pro_price = array();
                                                $tso_pro_return = array();

                                                $tso_p_qty =0;
                                                $tso_p_price =0;
                                                $tso_p_return =0;
                                                $tsototalsales =0;
                                                $tsototalcyp =0;
                                                $tsoefectivecall =0;

                                                $ture = 1;


                                        } ?>
                                        <?php 

                                            if(empty($db_other['ae_id'])){
                                                $db_other['ae_id'] = 0;
                                            }

                                           $ture = 1;

                                            if($q == 1){

                                                $name_for_ae = $db_other['ae_id'];
                                               
                                                if(empty($name_for_ae)){
                                                   $name_for_ae = '0';
                                                   $ture = 0;
                                                }

                                            }

                                            $q++;

                                            if( $name_for_ae != $db_other['ae_id'] and $ture > 0 ){
                                        
                                        
                                        ?>

                                        <tr>
                                                <td><?= $db_other['office'] ?></td>
                                               
                                                <td colspan="3" align="right" style="text-align:right;">
                                                <b> Total &nbsp; </b> </td>
                                                
                                                <?php foreach ($categories_products as $c_value) { ?>
                                                    <?php if ($c_value['Product']) { ?>
                                                        <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                            
                                                           
                                                            <?php if($sales == 'YES'){ ?>
                                                                <td> <?php echo $ae_pro_qty[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                            <?php if($value == 'YES'){ ?>
                                                                <td> <?php echo $ae_pro_price[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                            <?php if($retrun == "YES"){ ?>
                                                                <td> <?php echo $ae_pro_return[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                        <?php } ?>

                                                        <?php if($sales == 'YES'){ ?>
                                                        <td > <?= $ae_p_qty  ?> </td>
                                                        <?php } ?>

                                                        <?php if($value == 'YES'){ ?>
                                                        <td> <?= $ae_p_price  ?> </td>
                                                        <?php } ?>

                                                        <?php if($retrun == "YES"){ ?>
                                                        <td > <?= $ae_p_return  ?> </td>
                                                        <?php } ?>

                                                    <?php } ?>

                                            <?php } ?>
                                                    <td><?= $aetotalsales  ?></td>
                                                    <td><?= $aetotalcyp  ?></td>
                                                    <td><?= $aeefectivecall  ?></td>

                                            </tr>

                                            <?php 
                                                $q=1;
                                                $name_for_ae = $db_other['ae_id'];

                                                $ae_pro_qty = array();
                                                $ae_pro_price = array();
                                                $ae_pro_return = array();

                                                $ae_p_qty =0;
                                                $ae_p_price =0;
                                                $ae_p_return =0;
                                                $aetotalsales =0;
                                                $aetotalcyp =0;
                                                $aeefectivecall =0;


                                        ?>


                                        <?php } ?>

                                        <?php } ?>


                                            <tr class="rowDataSd">
                                                <td><?= $db_other['office'] ?></td>
                                                <?php if($nameid == 'db_id'){ ?>
                                                <td><?= $db_other['ae'] ?></td>
                                                <td><?= $db_other['tso'] ?></td>
                                                <td><?= $db_other['db'] ?></td>
                                                <?php } ?>

                                                <?php if($nameid == 'tso_id'){ ?>
                                                <td><?= $db_other['ae'] ?></td>
                                                <td><?= $db_other['tso'] ?></td>
                                                <?php } ?>

                                                <?php if($nameid == 'ae_id'){ ?>
                                                <td><?= $db_other['ae'] ?></td>
                                                <?php } ?>

                                                <?php
                                                $total_sales = 0;
                                                $total_return = 0;
                                                $total_cyp = 0;
                                                foreach ($categories_products as $c_value) {
                                                    $total_pro_qty = 0;
                                                    $total_pro_return_qty = 0;
                                                    $total_pro_price = 0;
                                                    $total_pro_return_price = 0;
                                                    $total_pro_cyp = 0;
                                                    if ($c_value['Product']) {
                                                        foreach ($c_value['Product'] as $p_value) {
                                                            $pro_id = $p_value['id'];
                                                            $pro_qty = isset($product_qnty[$db_id][$pro_id]) ? $product_qnty[$db_id][$pro_id] : '0.00';
                                                            $base_qty = $db_wise_topsheet_report->unit_convert($pro_id, $product_measurement[$pro_id], $pro_qty);

                                                            $pro_qty = ($unit_type == 1) ? $pro_qty : $base_qty;

                                                            $pro_qty_return = isset($db_return_results[$db_id][$pro_id]['pro_quantity']) ? $db_return_results[$db_id][$pro_id]['pro_quantity'] : '0.00';
                                                            $base_qty_return = $db_wise_topsheet_report->unit_convert($pro_id, $product_measurement[$pro_id], $pro_qty_return);

                                                            $pro_qty_return = ($unit_type == 1) ? $pro_qty_return : $base_qty_return;

                                                            $total_pro_qty += $pro_qty;
                                                            $total_pro_return_qty += $pro_qty_return;

                                                            $pro_price = isset($product_price[$db_id][$pro_id]) ? $product_price[$db_id][$pro_id] : '0.00';
                                                            $total_pro_price += $pro_price;

                                                            $return_pro_price =  isset($db_return_results[$db_id][$pro_id]['price']) ? $db_return_results[$db_id][$pro_id]['price'] : '0.00';
                                                            $total_pro_return_price += $return_pro_price;

                                                            //FOR CYP
                                                            $pro_cyp_v = isset($product_cyp_v[$db_id][$pro_id]) ? $product_cyp_v[$db_id][$pro_id] : '0';

                                                            $pro_cyp = isset($product_cyp[$db_id][$pro_id]) ? $product_cyp[$db_id][$pro_id] : '';
                                                            $pro_cyp_t = 0;
                                                            if ($pro_cyp_v && $pro_cyp) {
                                                                if ($pro_cyp == '*') {
                                                                    $pro_cyp_t = ($base_qty) * ($pro_cyp_v);
                                                                } elseif ($pro_cyp == '/') {
                                                                    $pro_cyp_t = ($base_qty) / ($pro_cyp_v);
                                                                } elseif ($pro_cyp == '+') {
                                                                    $pro_cyp_t = ($base_qty) + ($pro_cyp_v);
                                                                } elseif ($pro_cyp == '-') {
                                                                    $pro_cyp_t = ($base_qty) - ($pro_cyp_v);
                                                                }
                                                            }
                                                            $total_pro_cyp += $pro_cyp_t;
                                                ?>

                                                            <?php if($sales == 'YES'){ ?>
                                                                <td class="qty">
                                                                    <?= $pro_qty ?> 
                                                                </td>
                                                            <?php } ?>

                                                            <?php if($value == 'YES'){ ?>
                                                                <td class="qty">
                                                                    <?= sprintf("%01.2f", $pro_price)  ?> 
                                                                </td>
                                                            <?php } ?>

                                                            <?php if($retrun == "YES"){ ?>
                                                            <td class="qty">
                                                                <?= $pro_qty_return ?> 
                                                            </td>
                                                            <?php } ?>

                                                            <?php 

                                                            if($nameid == 'db_id'){

                                                                $tso_pro_qty[$p_value['id']] += $pro_qty;
                                                                $tso_pro_price[$p_value['id']] += $pro_price;
                                                                $tso_pro_return[$p_value['id']] += $pro_qty_return;

                                                                $tso_p_qty += $pro_qty;
                                                                $tso_p_price += $pro_price;
                                                                $tso_p_return += $pro_qty_return;

                                                                $ae_pro_qty[$p_value['id']] += $pro_qty;
                                                                $ae_pro_price[$p_value['id']] += $pro_price;
                                                                $ae_pro_return[$p_value['id']] += $pro_qty_return;

                                                                $ae_p_qty += $pro_qty;
                                                                $ae_p_price += $pro_price;
                                                                $ae_p_return += $pro_qty_return;

                                                            }
                                                                
                                                            ?>


                                                        <?php
                                                        }
                                                        $total_sales += $total_pro_price;
                                                        $total_return += $total_pro_return_price;
                                                        $total_cyp += $total_pro_cyp;
                                                        ?>
                                                        <?php if($sales == 'YES'){ ?>
                                                            <td class="qty" style="background:#CCF;"><?= sprintf("%01.2f", $total_pro_qty) ?></td>
                                                        <?php } ?>

                                                        <?php if($value == 'YES'){ ?>
                                                            <td class="qty" style="background:#CCF;"><?= sprintf("%01.2f", $total_pro_price) ?></td>
                                                        <?php } ?>

                                                        <?php if($retrun == "YES"){ ?>
                                                        <td class="qty" style="background:#CCF;"><?= sprintf("%01.2f", $total_pro_return_qty) ?></td>
                                                        <?php } ?>

                                                <?php
                                                    }
                                                }
                                                ?>


                                                <td class="qty"><?= sprintf("%01.2f", $total_sales) ?></td>
                                               <!--  <?php if($num_column == 2){ ?>
                                                <td class="qty"><?= sprintf("%01.2f", $total_return) ?></td>
                                                <?php } ?> -->

                                                <td class="qty"><?= sprintf("%01.2f", $total_cyp) ?></td>

                                                <td class="qty">
                                                    <?php
                                                           $efective_call =  $db_wise_topsheet_report->getECTotal($request_data, $db_id, $office_id); 
                                                           echo $efective_call;
                                                    ?>
                                                </td>


                                                <?php 

                                                if($nameid == 'db_id'){

                                                    $tsototalsales += $total_sales;
                                                    $tsototalcyp += $total_cyp;
                                                    $tsoefectivecall += $efective_call;

                                                    $aetotalsales += $total_sales;
                                                    $aetotalcyp += $total_cyp;
                                                    $aeefectivecall += $efective_call;

                                                }

                                                $officename = $db_other['office'];
                                                $nameae = $db_other['ae'];

                                                ?>

                                            </tr>



                                        <?php } ?>

                                        <?php  if($nameid == 'db_id'){ ?>

                                            <tr>
                                                <td><?= $officename ?></td>
                                                <td><?= $nameae ?></td>
                                                <td colspan="2" align="right" style="text-align:right;">
                                                 <b> Total &nbsp; </b> </td>
                                                
                                                <?php foreach ($categories_products as $c_value) { ?>
                                                    <?php if ($c_value['Product']) { ?>
                                                        <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                            
                                                           
                                                            <?php if($sales == 'YES'){ ?>
                                                                <td> <?php echo $tso_pro_qty[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                            <?php if($value == 'YES'){ ?>
                                                                <td> <?php echo $tso_pro_price[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                            <?php if($retrun == "YES"){ ?>
                                                                <td> <?php echo $tso_pro_return[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                        <?php } ?>

                                                        <?php if($sales == 'YES'){ ?>
                                                        <td > <?= $tso_p_qty  ?> </td>
                                                        <?php } ?>

                                                        <?php if($value == 'YES'){ ?>
                                                        <td> <?= $tso_p_price  ?> </td>
                                                        <?php } ?>

                                                        <?php if($retrun == "YES"){ ?>
                                                        <td > <?= $tso_p_return  ?> </td>
                                                        <?php } ?>

                                                    <?php } ?>

                                                <?php } ?>
                                                    <td><?= $tsototalsales  ?></td>
                                                    <td><?= $tsototalcyp  ?></td>
                                                    <td><?= $tsoefectivecall  ?></td>
                                                
                                            </tr>

                                            <tr>
                                                <td><?= $officename ?></td>
                                               
                                                <td colspan="3" align="right" style="text-align:right;">
                                                <b> Total &nbsp; </b> </td>
                                                
                                                <?php foreach ($categories_products as $c_value) { ?>
                                                    <?php if ($c_value['Product']) { ?>
                                                        <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                            
                                                           
                                                            <?php if($sales == 'YES'){ ?>
                                                                <td> <?php echo $ae_pro_qty[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                            <?php if($value == 'YES'){ ?>
                                                                <td> <?php echo $ae_pro_price[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                            <?php if($retrun == "YES"){ ?>
                                                                <td> <?php echo $ae_pro_return[$p_value['id']]; ?> </td>
                                                            <?php } ?>

                                                        <?php } ?>

                                                        <?php if($sales == 'YES'){ ?>
                                                        <td > <?= $ae_p_qty  ?> </td>
                                                        <?php } ?>

                                                        <?php if($value == 'YES'){ ?>
                                                        <td> <?= $ae_p_price  ?> </td>
                                                        <?php } ?>

                                                        <?php if($retrun == "YES"){ ?>
                                                        <td > <?= $ae_p_return  ?> </td>
                                                        <?php } ?>

                                                    <?php } ?>

                                            <?php } ?>
                                                    <td><?= $aetotalsales  ?></td>
                                                    <td><?= $aetotalcyp  ?></td>
                                                    <td><?= $aeefectivecall  ?></td>

                                            </tr>

                                        <?php } ?>

                                        <tr class="totalColumn">
                                            <td colspan="<?php if($nameid == 'db_id'){echo '4';}elseif($nameid == 'tso_id'){echo '3'; }elseif($nameid == 'ae_id'){echo '2';} ?>"><b>Total:</b></td>

                                            <?php foreach ($categories_products as $c_value) { ?>
                                                <?php if ($c_value['Product']) { ?>
                                                    <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                        
                                                        <?php if($sales == 'YES'){ ?>
                                                            <td class="totalQty sales"></td>
                                                        <?php } ?>

                                                        <?php if($value == 'YES'){ ?>
                                                            <td class="totalQty value"></td>
                                                        <?php } ?>

                                                        <?php if($retrun == "YES"){ ?>
                                                            <td class="totalQty return"></td>
                                                        <?php } ?>

                                                    <?php } ?>

                                                    <?php if($sales == 'YES'){ ?>
                                                    <td style="background:#CCF;" class="totalQty sales"></td>
                                                    <?php } ?>

                                                    <?php if($value == 'YES'){ ?>
                                                    <td style="background:#CCF;" class="totalQty value"></td>
                                                    <?php } ?>

                                                    <?php if($retrun == "YES"){ ?>
                                                    <td style="background:#CCF;" class="totalQty return"></td>
                                                    <?php } ?>

                                                <?php } ?>

                                            <?php } ?>

                                            <td class="totalQty"></td>
                                            <td class="totalQty"></td>
                                            <td class="totalQty"></td>
                                            <?php if($totalcolum == 3){ ?>
                                                <td class="totalQty"></td>
                                            <?php } ?>
                                        </tr>

                                    </table>
                                </div>


                                <?php
                                $total_col = '0';
                                ?>
                                <?php foreach ($categories_products as $c_value) { ?>
                                    <?php if ($c_value['Product']) { ?>
                                        <?php foreach ($c_value['Product'] as $p_value) { ?>
                                            <?php $total_col .= ',0'; ?>
                                            <?php $total_col .= ',0'; ?>
                                        <?php } ?>
                                        <?php $total_col .= ',0'; ?>
                                        <?php $total_col .= ',0'; ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php
                                $total_col .= ',0';
                                $total_col .= ',0';
                                $total_col .= ',0';
                                $total_col .= ',0';
                                ?>

                                <script>
                                    var totals_qty = [<?= $total_col ?>];
                                    //console.log(totals_qty);
                                    $(document).ready(function() {

                                        var $dataRows = $("#sum_table tr:not('.totalColumn, .titlerow')");

                                        $dataRows.each(function() {
                                            $(this).find('.qty').each(function(i) {
                                                totals_qty[i] += parseFloat($(this).html());
                                            });
                                        });
                                        console.log(totals_qty);
                                        $("#sum_table .totalQty").each(function(i) {
                                            $(this).html(totals_qty[i].toFixed(2));
                                        });

                                    });
                                </script>

                            <?php } else { ?>

                                <div style="clear:both;"></div>
                                <div class="alert alert-warning">No Report Found!</div>

                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>


        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        if ($('#office_id').val() && $('#MemoDateFrom').val() && $('#MemoDateTo').val()) {
            get_db_list();
        }
        $('#MemoDateFrom,#MemoDateTo').change(function() {
            if ($('#MemoDateFrom').val() && $('#MemoDateTo').val() && $('#office_id').val()) {
                get_db_list();
            }
        });
        $('#office_id').change(function() {
            get_db_list();
        });
        var db_check = <?php echo @json_encode($this->request->data['Memo']['db_id']); ?>;

        function get_db_list() {
            if (!$('#office_id').val()) {
                alert('Please select Office!');
                return false;
            }
            //alert($(this).val());
            date_from = $('#MemoDateFrom').val();
            date_to = $('#MemoDateTo').val();
            if (date_from && date_to) {
                $.ajax({
                    type: "POST",
                    //url: '<?= BASE_URL ?>sales_analysis_reports/get_office_so_list',
                    url: '<?= BASE_URL ?>db_wise_top_sheet_reports/get_db_list',
                    data: 'office_id=' + $('#office_id').val() + '&date_from=' + date_from + '&date_to=' + date_to,
                    cache: false,
                    success: function(response) {
                        //alert(response);                      
                        $('.so_list').html(response);
                        if (db_check) {
                            $.each(db_check, function(i, val) {

                                $(".db_id>input[value='" + val + "']").prop('checked', true);

                            });
                        }
                    }
                });
            } else {
                $('#office_id option:nth-child(1)').prop("selected", true);
                alert('Please select date range!');
            }
        }
    })
</script>

<script>
    $('.region_office_id').selectChain({
        target: $('.office_id'),
        value: 'name',
        url: '<?= BASE_URL . 'market_characteristic_reports/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
</script>

<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

        //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('<html><head><title></title>');
        mywindow.document.write('</head><body >');
        //mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>

<script>
    $(document).ready(function() {

        $("#download_xl").click(function(e) {

            e.preventDefault();

            var html = $("#xls_body").html();

            // console.log(html);

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "top_sheet_report.xls";

            document.body.appendChild(a);

            a.click();

        });
        get_product_list($(".product_type:checked").serializeArray());
        $(".product_type").change(function() {
            product_type = $(".product_type:checked").serializeArray();

            get_product_list(product_type);
        });
        var product_check = <?php echo @json_encode($this->request->data['Memo']['product_id']); ?>;

        function get_product_list(product_type) {
            $.ajax({
                type: "POST",
                //url: '<?= BASE_URL ?>sales_analysis_reports/get_office_so_list',
                url: '<?= BASE_URL ?>sales_reports/get_product_list',
                data: product_type,
                cache: false,
                success: function(response) {
                    $(".product").html(response);
                    if (product_check) {
                        $.each(product_check, function(i, val) {

                            $(".product_id>input[value='" + val + "']").prop('checked', true);

                        });
                    }
                }
            });
        }

    });
</script>