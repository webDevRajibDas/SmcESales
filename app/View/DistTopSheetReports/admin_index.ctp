<?php
App::import('Controller', 'DistTopSheetReportsController');
$db_wise_topsheet_report = new DistTopSheetReportsController;

//pr($this->request->data);exit;
?>

<style>
	#loading {
		position: absolute;
		width: auto;
		height: auto;
		text-align: center;
		top: 45%;
		left: 50%;
		display: none;
		z-index: 999;
	}

	#loading img {
		display: inline-block;
		height: 100px;
		width: auto;
	}
</style>

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

<style>
    

    #checkbox_list .checkbox label {
        padding-left: 10px;
        width: auto;
        float: none;
        text-align: left;
        margin: 0px;
    }

    #checkbox_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }

    .custom_design {
        width: 200px;
    }

    .office_search_table tr td {
        padding: 0px 10px;
    }

    #checkbox_list .checkbox label {
        padding-left: 0px;
        width: auto;
    }

    #checkbox_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }

    .outlet_category {
        float: right;
        width: 98%;
        padding-left: 5%;
        border: #c7c7c7 solid 1px;
        height: 100px;
        overflow: auto;
        margin-right: 3%;
        padding-top: 5px;
    }

    .outlet_category3 {
        width: 97%;
        margin-right: 3%;
        height: 115px;
    }

    .outlet_category .checkbox {
        float: left;
        width: 100% !important;
    }

    .outlet_category3 .checkbox {
        float: left;
        width: 50% !important;
    }

    .label_title {
        float: right;
        width: 95%;
        background: #c7c7c7;
        margin: 0px;
        padding: 1px 0px;
        text-align: center;
        color: #000;
        margin-right: 5%;
        font-weight: bold;
        font-size: 90%;
    }

    .pro_label_title {
        width: 97%;
        margin-right: 3%;
    }

    .outlet_category label {
        width: auto;
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
                                <td  width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'empty' => '---- Head Office ----', 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                        <?php if ($office_parent_id == 14) { ?>
                            <tr>
                                <td  width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>
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
                                <?php echo $this->Form->input('type', array('legend' => 'Columns Selection :', 'class' => 'type', 'type' => 'radio', 'default' => 'db', 'options' => $types, 'required' => true));  ?>
                            </td>
                        </tr>

                        <!--tr>
                            <td>
                                <div id="db_html">
                                    <?php echo $this->Form->input('db_id', array('label' => 'Distributor', 'id' => 'db_id', 'class' => 'form-control db_id office_t_db', 'required' => false, 'empty' => '---- All ----')); ?>
                                </div>

                                <div id="sr_html">
                                    <?php echo $this->Form->input('sr_id', array('label' => 'Sales Representative', 'id' => 'sr_id', 'class' => 'form-control sr_id', 'required' => false, 'options' => array(), 'empty' => '---- All ----')); ?>
                                </div>
                            </td>
                            <td></td>
                        </tr-->

                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            <?php echo $this->Form->input('product_type', array('legend' => 'Product Types :','class' => 'product_type', 'type' => 'radio', 'default' => '1', 'options' => $product_type_list));  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php //echo $this->Form->input('product_type', array('legend' => 'Product Type :', 'class' => 'product_type', 'type' => 'radio', 'default' => '1', 'options' => $product_type_list, 'required' => true));  ?>
                                <div id="product_type_categories_list" class="input select" style="padding-left: 20px; width:100%; display:block;">
                                        <div style="margin:auto; width:90%; float:left;">
                                                <input type="checkbox" id="product_type_categories_checkall" class="check_all" />
                                                <label for="product_type_categories_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                        </div>    
                                        <p class="label_title pro_label_title">Product Category Selection</p>
                                        <div id="checkbox_list" class="product_type_selection selection outlet_category outlet_category3">
                                            <?php echo $this->Form->input('product_type_categories_id', array('id' => 'product_type_categories_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $product_category_list)); ?>
                                        </div>
                                    
                                </div>
                               
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <!-- <label style="float:left; width:15%;">Products : </label> -->
                                <!-- <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                    <div style="margin:auto; width:90%; float:left;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                        <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="product selection" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto">
                                        <?php //echo $this->Form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list)); ?>
                                    </div>
                                </div> -->
                                

                                <div id="product_list" class="input select" style="padding-left: 20px; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">Products :</label> -->
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="product_checkall" class="check_all" />
                                                <label for="product_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <p class="label_title pro_label_title">Product Selection</p>
                                            <div id="checkbox_list" class="product_selection selection  outlet_category outlet_category3" style="height:180px;">
                                                <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox')); ?>
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
                });
            </script>
            <script>
                //$(input[type='checkbox']).iCheck(false); 
                $(document).ready(function() {
                    $("input[type='checkbox']").iCheck('destroy');
                    $("input[type='radio']").iCheck('destroy');
                    $('.check_all').click(function() {
                        var checked = $(this).prop('checked');
                        $(this).parent().parent().find('.selection input:checkbox').prop('checked', checked);
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
                                    $product_qnty[$data['0']['ref_id']][$data['0']['product_id']] = $data['0']['pro_quantity'];
                                    $product_price[$data['0']['ref_id']][$data['0']['product_id']] = $data['0']['price'];
                                    $product_cyp_v[$data['0']['ref_id']][$data['0']['product_id']] = $data['0']['cyp_v'];
                                    $product_cyp[$data['0']['ref_id']][$data['0']['product_id']] = $data['0']['cyp'];
                                }
                                //pr($product_qnty);
                                //exit;
                                ?>


                                <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                    <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                        <tr class="titlerow">

                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    Office
                                                </div>
                                            </th>

                                            <?php if($type == 'ae'){ ?>
                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    Area Executive
                                                </div>
                                            </th>
                                            <?php } ?>

                                            <?php if($type == 'tso'){ ?>
                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    Area Executive
                                                </div>
                                            </th>
                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    TSO
                                                </div>
                                            </th>
                                            <?php } ?>

                                            <?php if($type == 'db'){ ?>
                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    Area Executive
                                                </div>
                                            </th>
                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    TSO
                                                </div>
                                            </th>
                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    Distributor
                                                </div>
                                            </th>
                                            <?php } ?>

                                            <?php if($type == 'sr'){ ?>
                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    Area Executive
                                                </div>
                                            </th>
                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    TSO
                                                </div>
                                            </th>
                                            <th>
                                                <div class="qty_val" style="width:120px;">
                                                    Distributor
                                                </div>
                                            </th>
                                            <th>
                                                <div class="qty_val" style="width:140px;">
                                                    Sales Representative
                                                    
                                                </div>
                                            </th>
                                            <?php } ?>
                                            
                                            <?php foreach ($categories_products as $c_value) { ?>

                                                <?php if ($c_value['Product']) { ?>
                                                    <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                        <th>
                                                            <div class="qty_val"><?= $p_value['name'] ?></div>
                                                        </th>
                                                    <?php } ?>
                                                    <th style="background:#CCF;">
                                                        <div class="qty_val">Total <?= $c_value['ProductCategory']['name'] ?></div>
                                                    </th>
                                                <?php } ?>

                                            <?php } ?>

                                            <th>
                                                <div class="qty_val">Total Sales<br>(Tk.)</div>
                                            </th>


                                            <th>
                                                <div class="qty_val">CYP</div>
                                            </th>
                                            <th>
                                                <div class="qty_val">EC Call</div>
                                            </th>
                                            <th>
                                                <div class="qty_val">Outlet Cover</div>
                                            </th>
                                        </tr>



                                        <?php
                                        //pr($sales_people);
                                        foreach ($db_sales_results as $db_id => $data) {
                                            //$so_id = $data_s['0']['sales_person_id'];
                                        ?>
                                            <tr class="rowDataSd">

                                                <td><?= $data['office_name'] ?></td>
                                                <?php if($type == 'ae'){ ?>
                                                <td><?= $data['ae_name'] ?></td>
                                                <?php } ?>
                                                <?php if($type == 'tso'){ ?>
                                                <td><?= $data['ae_name'] ?></td>
                                                <td><?= $data['tso_name'] ?></td>
                                                <?php } ?>
                                                <?php if($type == 'db'){ ?>
                                                <td><?= $data['ae_name'] ?></td>
                                                <td><?= $data['tso_name'] ?></td>
                                                <td><?= $data['dist_name'] ?></td>
                                                <?php } ?>
                                                <?php if($type == 'sr'){ ?>
                                                <td><?= $data['ae_name'] ?></td>
                                                <td><?= $data['tso_name'] ?></td>
                                                <td><?= $data['dist_name'] ?></td>
                                                <td><?= $data['ref_name'] ?></td>
                                                <?php } ?>

                                                <?php
                                                $total_sales = 0;
                                                $total_cyp = 0;
                                                foreach ($categories_products as $c_value) {
                                                    $total_pro_qty = 0;
                                                    $total_pro_return_qty = 0;
                                                    $total_pro_price = 0;
                                                    $total_pro_cyp = 0;
                                                    if ($c_value['Product']) {
                                                        foreach ($c_value['Product'] as $p_value) {
                                                            $pro_id = $p_value['id'];
                                                            $pro_qty = isset($product_qnty[$db_id][$pro_id]) ? $product_qnty[$db_id][$pro_id] : '0.00';
                                                            $base_qty = $db_wise_topsheet_report->unit_convert($pro_id, $product_measurement[$pro_id], $pro_qty);

                                                            $pro_qty = ($unit_type == 1) ? $pro_qty : $base_qty;
                                                            $total_pro_qty += $pro_qty;
                                                            $pro_price = isset($product_price[$db_id][$pro_id]) ? $product_price[$db_id][$pro_id] : '0.00';
                                                            $total_pro_price += $pro_price;

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

                                                            <td class="qty">
                                                                <?= $pro_qty ?>
                                                            </td>

                                                        <?php
                                                        }
                                                        $total_sales += $total_pro_price;
                                                        $total_cyp += $total_pro_cyp;
                                                        ?>

                                                        <td class="qty" style="background:#CCF;"><?= sprintf("%01.2f", $total_pro_qty) ?></td>

                                                <?php
                                                    }
                                                }
                                                ?>

                                                <td class="qty"><?= sprintf("%01.2f", $total_sales) ?></td>

                                                <td class="qty"><?= sprintf("%01.2f", $total_cyp) ?></td>

                                                <td class="qty"><?= $db_wise_topsheet_report->getECTotal($request_data, $db_id, $office_id); ?></td>
                                                <td class="qty"><?= $db_wise_topsheet_report->getOCTotal($request_data, $db_id, $office_id); ?></td>


                                            </tr>
                                        <?php } ?>


                                        <tr class="totalColumn">
                                            <?php if($type == 'ae'){ ?>
                                                <td colspan="2"><b>Total:</b></td>
                                            <?php }elseif($type == 'tso'){ ?>
                                                <td colspan="3"><b>Total:</b></td>
                                            <?php }elseif($type == 'db'){ ?>
                                                <td colspan="4"><b>Total:</b></td>
                                            <?php }else{ ?>
                                                <td colspan="5"><b>Total:</b></td>
                                            <?php } ?>

                                            <?php foreach ($categories_products as $c_value) { ?>
                                                <?php if ($c_value['Product']) { ?>
                                                    <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                        <td class="totalQty sales"></td>
                                                    <?php } ?>
                                                    <td style="background:#CCF;" class="totalQty sales"></td>
                                                <?php } ?>

                                            <?php } ?>

                                            <td class="totalQty"></td>
                                            <td class="totalQty"></td>

                                            <td class="totalQty"></td>
                                            <td class="totalQty"></td>
                                           

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
                                        <?php } ?>
                                        <?php $total_col .= ',0'; ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php
                                $total_col .= ',0';
                                $total_col .= ',0';
                                $total_col .= ',0';
                                $total_col .= ',0';
                                $total_col .= ',0';
                                ?>

                                <script>
                                    var totals_qty = [<?= $total_col ?>];
                                    console.log(totals_qty);
                                    $(document).ready(function() {

                                        var $dataRows = $("#sum_table tr:not('.totalColumn, .titlerow')");

                                        $dataRows.each(function() {
                                            $(this).find('.qty').each(function(i) {
                                                totals_qty[i] += parseFloat($(this).html());
                                            });
                                        });

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
<div class="modal" id="myModal"></div>
<div id="loading">
	<?php echo $this->Html->image('load.gif'); ?>
</div>
<script>
   /*  $(document).ready(function() {
        $('.office_id').selectChain({
            target: $('.db_id'),
            value: 'name',
            url: '<?= BASE_URL . 'dist_db_wise_detail_sales/get_db_list' ?>',
            type: 'post',
            data: {
                'office_id': 'office_id',
                'date_from': 'MemoDateFrom',
                'date_to': 'MemoDateTo'
            },
            <?php if (isset($db_id_post) && $db_id_post) { ?>
                afterSuccess: function() {
                    console.log(<?= $db_id_post ?>);
                    $(".db_id").val(<?= $db_id_post ?>);
                }
            <?php } ?>
        });
    }); */
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
    $(document).ready(function() {
        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');

        $('#checkall').click(function() {
            var checked = $(this).prop('checked');
            $('.product_selection').find('input:checkbox').prop('checked', checked);
        });

        $('#checkall2').click(function() {
            var checked = $(this).prop('checked');
            $('.brand_selection').find('input:checkbox').prop('checked', checked);
        });

        $('#checkall3').click(function() {
            var checked = $(this).prop('checked');
            $('.category_selection').find('input:checkbox').prop('checked', checked);
        });

    });

    /* $('#office_id').change(function() {
        //alert($(this).val());
        date_from = $('#MemoDateFrom').val();
        date_to = $('#MemoDateTo').val();
        if (date_from && date_to) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>dist_db_wise_detail_sales/get_sr_list',
                data: 'office_id=' + $(this).val() + '&date_from=' + date_from + '&date_to=' + date_to,
                cache: false,
                success: function(response) {
                    //alert(response);
                    if ($('#sr_id').html(response)) {
                        <?php if (isset($sr_id_post) && $sr_id_post) { ?>
                            $("#sr_id").val(<?= $sr_id_post ?>);
                        <?php } ?>
                    }
                }
            });
        } else {
            $('#office_id option:nth-child(1)').prop("selected", true);
            alert('Please select date range!');
        }
    }); */
    $(document).ready(function() {
        typeChange();
        if ($('.office_id').val()) {
            $('.office_id').trigger('change');
            $('.office_id').trigger('selectChain');
        }
    });


    function typeChange() {
        var type = $('.type:checked').val();
        //for territory and so
        $('#sr_html').hide();
        $('#db_html').hide();

        //alert(rows);



        if (type == 'sr') {
            $('#sr_html').show();
        } else {
            $('#db_html').show();
        }
    }
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
        

    });
</script>

<script>
$(document).ready(function() {

        get_product_list($(".product_type:checked").serializeArray()); //for first time

  
        $(".product_type").change(function() {
            product_type = $(".product_type:checked").serializeArray();
            
            get_product_list(product_type);
            // console.log("here5");
        });

        function get_product_list(product_type) {
            // console.log(product_type);
            var product_type_categories_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.product_type_selection').find('input:checkbox').prop('checked', checked);
            
                $('[name="data[Memo][product_type_categories_id][]"]:checked').each(function(i) {
                    product_type_categories_id[j] = $(this).val();
                    j++;
                    //data = data + dist_distributor_id +",";
                });
                // console.log("here1");
                
           
                // console.log("false");
            
            var product_check={};
            <?php if(isset($this->request->data['Memo']['product_id'])) {?>
            product_check = <?php echo json_encode($this->request->data['Memo']['product_id']);?>;
            <?php }?>
            
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>DistTopSheetReports/get_product_list',
                data: {
                    product_type: product_type,
                    product_type_categories_id: product_type_categories_id
                },
                cache: false,
                beforeSend: function() {
					$('#loading').show();
					$('#myModal').modal({
						backdrop: 'static',
						keyboard: false
					});
				},
                success: function(response) {
                    $(".product_selection").html(response);
                    // console.log(response);
                    if(response)
                    {
                        $.each(product_check, function(i, val){

                            $(".product_id>input[value='" + val + "']").prop('checked', true);

                     });
                    }

                    $('#loading').hide();
					$('#myModal').modal('hide');
                }
            });
        }
        /****** Outlet List By Product Category Start *************/
        $('#product_type_categories_checkall').click(function() {
            // alert('hello');return;
            // e.preventDefault();
            // console.log("here2");
            // $(".product_selection").html('');
            // var product_type_categories_id = new Array();
            // var j = 0;
            // var checked = $(this).prop('checked');
            // $('.product_type_selection').find('input:checkbox').prop('checked', checked);
            // if ($('.product_type_selection').find('input:checkbox').prop('checked') == true) {
            //     $('[name="data[Memo][product_type_categories_id][]"]:checked').each(function(i) {
            //         product_type_categories_id[j] = $(this).val();
            //         j++;
            //         //data = data + dist_distributor_id +",";
            //     });
                // console.log(product_type_categories_id);
                // get_product_list_by_category(product_type_categories_id);
                product_type = $(".product_type:checked").serializeArray();
            
                get_product_list(product_type);
            // } else {
            //     console.log("false");
            // }
        });
        $('.product_type_selection').click(function(e) {
            $(".product_selection").html('');
            // console.log('here3');
            //console.log(e.target.tagName);
            if(e.target.tagName=='INPUT'){
                product_type = $(".product_type:checked").serializeArray();
            
                get_product_list(product_type);

            }
            

        });
        /****** Outlet List By Product Category End *************/
    

    function get_sr_list(dist_distributor_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>DistTopSheetReports/get_sr_list',
            data: {
                dist_distributor_id: dist_distributor_id
            },
            cache: false,
            success: function(data) {
                $(".sr_selection").html(data);
            }
        });
    }
    /* get Sr List By Distributor End*/

    /* get Rout Beat List By SR Start */
    function get_rout_beat_list(sr_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>DistTopSheetReports/get_rout_beat_list',
            data: {
                sr_id: sr_id
            },
            cache: false,
            success: function(data) {
                $(".route_beat_selection").html(data);
            }
        });
    }
    /* get Rout Beat List By SR End */

    /* get Market List By SR Start */
    function get_market_list(route_beat_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>DistTopSheetReports/get_market_list',
            data: {
                route_beat_id: route_beat_id
            },
            cache: false,
            success: function(data) {
                $(".market_selection").html(data);
            }
        });
    }
    /* get Market List By SR End */
    /* get Outlet Categories List By Market Start */
    function get_outlet_categories_list(market_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>DistTopSheetReports/get_outlet_categories_list',
            data: {
                market_id: market_id
            },
            cache: false,
            success: function(data) {
                $(".outlet_categories_selection").html(data);
            }
        });
    }
    /* get Outlet Categories List By Market End */
    /* get Outlet List By Category Start */
    function get_outlet_list_by_category(outlet_categories_id) {
        var market_id = new Array();
        var j = 0;
        var checked = $(this).prop('checked');
        $('.market_selection').find('input:checkbox').prop('checked', checked);
        if ($('.market_selection').find('input:checkbox').prop('checked') == true) {
            $('[name="data[Memo][market_id][]"]:checked').each(function(i) {
                market_id[j] = $(this).val();
                j++;
            });
        } else {
            console.log("false");
        }
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>DistTopSheetReports/get_outlet_list_by_category',
            data: {
                outlet_categories_id: outlet_categories_id,
                market_id: market_id
            },
            cache: false,
            success: function(data) {
                $(".outlet_selection").html(data);
            }
        });
    }
    /* get Outlet List By Category End */
    /* get Outlet List By Market Start */
    function get_outlet_list(market_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>DistTopSheetReports/get_outlet_list',
            data: {
                market_id: market_id
            },
            cache: false,
            success: function(data) {
                $(".outlet_selection").html(data);
            }
        });
    }
    /* get Outlet List By Market End */
    /* get Product List By Category Start */
    function get_product_list_by_category(product_type_categories_id) {
        product_type = $(".product_type:checked").serializeArray();
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>DistTopSheetReports/get_product_list_by_category',
            data: {
                product_type_categories_id: product_type_categories_id,
                product_type: product_type
            },
            cache: false,
            beforeSend: function() {
					$('#loading').show();
					$('#myModal').modal({
						backdrop: 'static',
						keyboard: false
					});
				},
            success: function(data) {
                
                
                $(".product_selection").html(data);
                $('#loading').hide();
				$('#myModal').modal('hide');
                
            }
        });
    }
});
    /* get Product List By Category End */
</script>