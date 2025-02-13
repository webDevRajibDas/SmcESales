<?php
App::import('Controller', 'DistCombineQueryOnSalesReportsController');
$controller = new DistCombineQueryOnSalesReportsController;

?>


<style>
    @media only screen and (max-width: 768px) {

        #market_list .checkbox {
            width: 100% !important;
            float: left;
            margin: 1px 0;
        }

        .search label {
            width: auto !important;
        }

        .search td table thead {
            display: none;
        }

        .mobile_td {
            display: block;
            background-color: #00000080;
        }

        .search td table tr {
            border-bottom: 3px solid #ddd;
            display: block;
        }

        .search td table td {
            border-bottom: 1px solid #ddd;
            display: block;
            text-align: left;
            height: 36px;
            width: 100% !important;
        }

        .search td table td a.filter_mobile {
            width: 70%;
        }

        .search td table td::before {
            content: attr(data-label);
            float: left;
        }
    }

    .mobile_td {
        display: none;
    }

    .search .radio label {
        width: auto;
        float: none;
        padding: 0px 5% 0px 5px;
        margin: 0px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 12.5%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    #market_list .checkbox label {
        padding-left: 0px;
        width: auto;
    }

    #market_list .checkbox {
        width: 33%;
        float: left;
        margin: 1px 0;
    }

    body .td_rank_list .checkbox {
        width: auto !important;
        padding-left: 20px !important;
    }

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
    }

    .search label {
        width: 25%;
    }

    #market_list {
        padding-top: 5px;
    }

    .market_list2 .checkbox {
        width: 15% !important;
    }

    .market_list3 .checkbox {
        width: 20% !important;
    }

    .box_area {
        display: none;
    }
</style>

<style>
    #divLoading {
        display: none;
    }

    #divLoading.show {
        display: block;
        position: fixed;
        z-index: 100;
        background-image: url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
        background-color: #666;
        opacity: 0.4;
        background-repeat: no-repeat;
        background-position: center;
        left: 0;
        bottom: 0;
        right: 0;
        top: 0;
    }

    #loadinggif.show {
        left: 50%;
        top: 50%;
        position: absolute;
        z-index: 101;
        width: 32px;
        height: 32px;
        margin-left: -16px;
        margin-top: -16px;
    }
</style>

<div id="divLoading" class=""> </div>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">



            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title ?></h3>
                <?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New OutletCharacteristic Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
            </div>


            <div class="box-body">

                <div class="search-box">

                    <?php echo $this->Form->create('DistCombineQueryOnSalesReports', array('role' => 'form', 'action' => 'index')); ?>

                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?>
                            </td>

                            <td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?></td>
                        </tr>

                        <tr>
                            <?php if ($office_parent_id == 0) { ?>
                                <td class="" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'empty' => '---- Head Office ----', 'options' => $region_offices,)); ?></td>
                            <?php } ?>
                            <?php if ($office_parent_id == 14) { ?>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>
                            <?php } ?>

                            <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- All ----')); ?></td>
                            <?php } else { ?>
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- All ----')); ?></td>
                            <?php } ?>
                        </tr>


                        <tr>
                            <td>
                                <?php echo $this->Form->input('tso_id', array('id' => 'tso_id', 'class' => 'form-control tso_id office_t_so', 'required' => false, 'empty' => '---- All ----')); ?>

                            </td>
                            <td>
                                <?php echo $this->Form->input('dist_id', array('id' => 'dist_id', 'class' => 'form-control dist_id office_t_so', 'required' => false, 'empty' => '---- All ----', 'Label' => 'Distributor')); ?>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="12">
                                <?php echo $this->Form->input('rows', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => 'db', 'options' => $rows_array, 'required' => true)); ?>
                            </td>

                        </tr>

                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Outlet Category : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="out_let_category" class="checkall" />
                                        <label for="out_let_category" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection">
                                        <?php echo $this->Form->input('outlet_category_id', array('id' => 'outlet_category_id', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $outlet_categories)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td colspan="2">
                                <?php //echo $this->Form->input('unit_type', array('legend'=>'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required'=>true));  
                                ?>
                            </td>
                        </tr> -->
                        <tr>
                            <td colspan="2" style="padding:2%;">
                                <div class="table-responsive">
                                    <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table table" id="product_list_table">
                                        <thead>
                                            <tr>
                                                <th style="text-align:left;">Product</th>
                                                <th style="text-align:left;">Comparator</th>
                                                <th style="text-align:left;">Volume/Value</th>
                                                <th>From</th>
                                                <th> To </th>
                                                <th style="text-align:left;">Memo/Total</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($product_rows)) {
                                                $i = 1;
                                                foreach ($product_rows as $i => $row) {
                                            ?>
                                                    <tr id="<?= $i ?>">
                                                        <td class="mobile_td"><b>Product</b></td>
                                                        <td style="width:30%">
                                                            <?php foreach ($row['product_id'] as $key_p => $pid) { ?>
                                                                <div class="product">
                                                                    <?php echo $this->Form->input('product_id', array('label' => false, 'id' => 'product_id', 'class' => 'form-control product_id', 'required' => true, 'empty' => '---- Select Product ----', 'options' => $product_list, 'style' => 'width:90%', 'name' => 'data[DistCombineQueryOnSalesReports][product_id][' . $i . '][]', 'value' => $pid, 'required' => true)); ?>
                                                                    <?php if ($key_p == 0) { ?>
                                                                        <a href="#" class="btn btn-xs btn-success add_more_product" style="margin-top:0%"><i class="fa fa-plus"></i></a>
                                                                    <?php } else { ?>
                                                                        <a href="#" class="btn btn-xs remove_product btn-danger " style="margin-top:1.5%"><i class="glyphicon glyphicon-trash"></i></a>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="mobile_td"><b>Comparator</b></td>
                                                        <td style="width:20%">
                                                            <?php echo $this->Form->input('compotator', array('label' => false, 'id' => 'compotator', 'class' => 'form-control compotator', 'required' => true, 'empty' => '---- Select Compotator ----', 'options' => $compotators, 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][compotator][' . $i . ']', 'value' => $row['compotator'], 'required' => true)); ?>
                                                        </td>

                                                        <td class="mobile_td"><b>Volume/Value</b></td>
                                                        <td style="width:20%">
                                                            <?php echo $this->Form->input('volume_or_value', array('label' => false, 'id' => 'volume_or_value', 'class' => 'form-control volume_or_value', 'required' => true, 'options' => $volumes_values, 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][volume_or_value][' . $i . ']', 'required' => true, 'value' => $row['volume_or_value'])); ?>
                                                        </td>

                                                        <td class="mobile_td"><b>From</b></td>
                                                        <td style="width:10%">
                                                            <?php echo $this->Form->input('qty', array('class' => 'form-control qty', 'label' => false, 'required' => true, 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][qty][' . $i . ']', 'value' => $row['qty'])); ?>
                                                        </td>
                                                        <td class="mobile_td"><b>To</b></td>
                                                        <td style="width:10%">


                                                            <?php echo $this->Form->input('qty2', array('class' => 'form-control qty2', 'label' => false, 'id' => 'qty2', 'disabled' => 'disabled', 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][qty2][' . $i . ']', 'value' => $row['qty2'], 'disabled' => $row['qty2'] ? false : 'disabled',)); ?>

                                                        </td>

                                                        <td class="mobile_td"><b>Memo/Total</b></td>

                                                        <td style="width:20%">
                                                            <?php echo $this->Form->input('memo_total', array('label' => false, 'id' => 'memo_total', 'class' => 'form-control memo_total', 'required' => true, 'empty' => '---- Select Compotator ----', 'options' => $memo_totals, 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][memo_total][' . $i . ']', 'value' => $row['memo_total'])); ?>
                                                        </td>


                                                        <td>
                                                            <?php
                                                            if (($i) == count($product_rows)) {
                                                            ?>
                                                                <a href="#" class="btn btn-xs btn-success add_more filter_mobile" style="margin-top:0%"><i class="fa fa-plus"></i></a>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <a href="#" class="btn btn-xs remove_row btn-danger filter_mobile" style="margin-top:0%"><i class="glyphicon glyphicon-trash"></i></a>
                                                            <?php
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    $i++;
                                                }
                                            } else {
                                                ?>
                                                <tr id="1">
                                                    <td class="mobile_td"><b>Product</b></td>
                                                    <td style="width:30%">
                                                        <div class="product">
                                                            <?php echo $this->Form->input('product_id', array('label' => false, 'id' => 'product_id', 'class' => 'form-control product_id', 'required' => true, 'empty' => '---- Select Product ----', 'options' => $product_list, 'style' => 'width:90%', 'name' => 'data[DistCombineQueryOnSalesReports][product_id][1][]', 'required' => true,)); ?>
                                                            <a href="#" class="btn btn-xs btn-success add_more_product" style="margin-top:0%"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                    </td>
                                                    <td class="mobile_td"><b>Comparator</b></td>
                                                    <td style="width:20%">
                                                        <?php echo $this->Form->input('compotator', array('label' => false, 'id' => 'compotator', 'class' => 'form-control compotator', 'required' => true, 'empty' => '---- Select Compotator ----', 'options' => $compotators, 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][compotator][1]', 'required' => true,)); ?>
                                                    </td>
                                                    <td class="mobile_td"><b>Volume/Value</b></td>
                                                    <td style="width:20%">
                                                        <?php echo $this->Form->input('volume_or_value', array('label' => false, 'id' => 'compotator', 'class' => 'form-control volume_or_value', 'required' => true, 'options' => $volumes_values, 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][volume_or_value][1]', 'required' => true,)); ?>
                                                    </td>
                                                    <td class="mobile_td"><b>Qty</b></td>
                                                    <td style="width:10%">
                                                        <?php echo $this->Form->input('qty', array('class' => 'form-control qty', 'label' => false, 'required' => true, 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][qty][1]')); ?>
                                                    </td>
                                                    <td class="mobile_td"><b>...</b></td>
                                                    <td style="width:10%">
                                                        <?php echo $this->Form->input('qty2', array('class' => 'form-control qty2', 'label' => false, 'id' => 'qty2', 'disabled' => 'disabled', 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][qty2][1]')); ?>
                                                    </td>
                                                    <td class="mobile_td"><b>Memo/Total</b></td>
                                                    <td style="width:20%">
                                                        <?php echo $this->Form->input('memo_total', array('class' => 'form-control memo_total', 'label' => false, 'id' => 'memo_total', 'options' => $memo_totals, 'required' => true, 'style' => 'width:100%', 'name' => 'data[DistCombineQueryOnSalesReports][memo_total][1]')); ?>
                                                    </td>

                                                    <td>
                                                        <a href="#" class="btn btn-xs btn-success add_more filter_mobile" style="margin-top:0%"><i class="fa fa-plus"></i></a>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                            </td>
                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                            </td>
                        </tr>

                    </table>

                    <?php echo $this->Form->end(); ?>
                </div>






                <?php if (!empty($request_data)) { ?>

                    <div id="content" style="width:90%; margin:0 5%;">

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
                                width: 125px;
                                margin: 0;
                                float: left;
                                text-transform: capitalize;
                            }

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
                                padding: 5px;
                            }

                            table {
                                border-collapse: collapse;
                                border-spacing: 0;
                            }

                            .titlerow,
                            .totalColumn {
                                background: #f1f1f1;
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
                        </style>

                        <div class="table-responsive">

                            <div class="pull-right csv_btn" style="padding-top:20px;">
                                <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                            </div>

                            <div id="xls_body">

                                <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">

                                <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                                    <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>


                                    <h3 style="margin:2px 0;">Query On Sales Information</h3>

                                    <p>
                                        <b> Time Frame : <?= @date('d M, Y', strtotime($date_from)) ?> to <?= @date('d M, Y', strtotime($date_to)) ?></b>
                                    </p>

                                    <p>
                                        <?php if ($region_office_id) { ?>
                                            <span>Region Office: <?= $region_offices[$region_office_id] ?></span>
                                        <?php } else { ?>
                                            <span>Head Office</span>
                                        <?php } ?>
                                        <?php if ($office_id) { ?>
                                            <span>, Area Office: <?= $offices[$office_id] ?></span>
                                        <?php } ?>

                                    </p>
                                </div>


                                <div style="float:left; width:100%; height:430px; overflow:scroll;">

                                    <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                        <thead>
                                            <tr class="titlerow">
                                                <th style="text-align:left;">ASO</th>
                                                <th style="text-align:left;">TSO</th>
                                                <th style="text-align:left;">DB</th>
                                                <?php if ($by_rows == 'sr') { ?>
                                                    <th style="text-align:left;">SR</th>
                                                <?php } ?>
                                                <th style="text-align:left;">Route</th>
                                                <th style="text-align:left;">Market</th>
                                                <th style="text-align:left;">Outlet Name</th>
                                                <th style="text-align:left;">Outlet Type</th>
                                                <th style="text-align:right;">Sales QTY</th>
                                                <th style="text-align:right;">Revenue</th>
                                                <th style="text-align:right;">Eff.Call</th>
                                            </tr>
                                        </thead>


                                        <tbody>
                                            <?php
                                            if ($results) {
                                                $g_total_outlet = 0;
                                                $g_total_qty = 0;
                                                $g_total_val = 0;
                                                $g_total_ec = 0;
                                            ?>
                                                <?php foreach ($results as $data) { ?>
                                                    <tr>
                                                        <td colspan="11" style="text-align:left;"><?= $data['heading_td'] ?></td>
                                                    </tr>
                                                    <?php
                                                    $total_outlet = 0;
                                                    $total_qty = 0;
                                                    $total_val = 0;
                                                    $total_ec = 0;
                                                    ?>
                                                    <?php foreach ($data['details'] as $rpt_data) { ?>


                                                        <tr class="">
                                                            <td style="text-align:left;"><?= $rpt_data['aso'] ?></td>
                                                            <td style="text-align:left;"><?= $rpt_data['tso'] ?></td>
                                                            <td style="text-align:left;"><?= $rpt_data['db'] ?></td>
                                                            <?php if ($by_rows == 'sr') { ?>
                                                                <td style="text-align:left;"><?= $rpt_data['sr'] ?></td>
                                                            <?php } ?>
                                                            <td style="text-align:left;"><?= $rpt_data['route'] ?></td>
                                                            <td style="text-align:left;"><?= $rpt_data['market'] ?></td>
                                                            <td><?= $rpt_data['outlet'] ?></td>
                                                            <td><?= $rpt_data['outlet_category'] ?></td>
                                                            <td style="text-align:right;"><?= $rpt_data['sales_qty'] ?></td>
                                                            <td style="text-align:right;"><?= $rpt_data['value'] ?></td>
                                                            <td style="text-align:right;"><?= $rpt_data['ec'] ?></td>
                                                        </tr>
                                                        <?php
                                                        $total_outlet++;
                                                        @$total_qty += $rpt_data['sales_qty'];
                                                        @$total_val += $rpt_data['value'];
                                                        @$total_ec += $rpt_data['ec'];
                                                        ?>
                                                    <?php } ?>
                                                    <tr style="background:#f7f7f7; font-weight:bold;">
                                                        <td colspan="7" style="text-align:right;">Total :</td>
                                                        <td>Outlet : <?= $total_outlet ?></td>
                                                        <td style="text-align:right;"><?= sprintf("%01.2f", $total_qty) ?></td>
                                                        <td style="text-align:right;"><?= sprintf("%01.2f", $total_val) ?></td>
                                                        <td style="text-align:right;"><?= $total_ec ?></td>
                                                    </tr>
                                                <?php
                                                    $g_total_outlet += $total_outlet;
                                                    $g_total_qty += $total_qty;
                                                    $g_total_val += $total_val;
                                                    $g_total_ec += $total_ec;
                                                }
                                                ?>
                                                <tr style="background:#f7f7f7; font-weight:bold;">
                                                    <td colspan="7" style="text-align:right;">Grand Total :</td>
                                                    <td>Outlet : <?= $g_total_outlet ?></td>
                                                    <td style="text-align:right;"><?= sprintf("%01.2f", $g_total_qty) ?></td>
                                                    <td style="text-align:right;"><?= sprintf("%01.2f", $g_total_val) ?></td>
                                                    <td style="text-align:right;"><?= $g_total_ec ?></td>
                                                </tr>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="10">No Result Found!</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>

                    </div>

                <?php } ?>




            </div>
        </div>
    </div>
</div>

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
    $('.region_office_id').change(function() {
        $('#territory_id').html('<option value="">---- All ----');
    });
    /*$('.office_id').selectChain({
        target: $('.tso_id'),
        value:'name',
        url: '<?= BASE_URL . 'DistCombineQueryOnSalesReports/get_tso_list' ?>',
        type: 'post',
        data:{'office_id': 'office_id' }
    });*/
    $('.tso_id').selectChain({
        target: $('.dist_id'),
        value: 'name',
        url: '<?= BASE_URL . 'DistCombineQueryOnSalesReports/get_distributor_list' ?>',
        type: 'post',
        data: {
            'office_id': 'office_id',
            'tso_id': 'tso_id'
        }
    });
</script>

<script>
    $(document).ready(function() {
        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');

        $('.checkall').click(function(e) {
            var checked = $(this).prop('checked');
            $(this).closest('.select').find('.selection').find('input:checkbox').prop('checked', checked);
        })

    });
</script>


<script type="text/javascript">
    $(document).ready(function() {
        var row = $('#product_list_table tbody tr').first();
        var product = row.find('td').eq(1).html();
        var comparator = row.find('td').eq(3).html();
        var volume_value = row.find('td').eq(5).html();
        var qty = row.find('td').eq(7).html();
        var qty2 = row.find('td').eq(9).html();
        var memo_total = row.find('td').eq(11).html();
        $(document).on({
            click: function(e) {
                e.preventDefault();

                var current_row_no = $(this).parent().parent().attr('id');

                var sl = (parseInt(current_row_no) + 1);

                var valid_row = $('#' + current_row_no + ' td .qty').val();

                //alert(valid_row);

                if (valid_row != '') {
                    var tr = '<tr id=' + sl + '>\
                            <td class="mobile_td"><b>Product</b></td>\
                            <td style="width:30%">\
                                ' + product + '\
                            </td>\
                            <td class="mobile_td"><b>Comparator</b></td>\
                            <td style="width:20%">\
                            ' + comparator + '</td>\
                            <td class="mobile_td"><b>Volume/Value</b></td>\
                            <td style="width:20%">\
                            ' + volume_value + '</td>\
                            <td class="mobile_td"><b>From</b></td>\
                            <td style="width:10%">\
                            ' + qty + '\
                            </td>\
                            <td class="mobile_td"><b>To</b></td>\
                            <td style="width:10%">\
                            ' + qty2 + '\
                            </td>\
                            <td class="mobile_td"><b>Memo/Total</b></td>\
                            <td style="width:20%">\
                            ' + memo_total + '\
                            </td>\
                            <td>\
                            <a href="#" class="btn btn-xs btn-success add_more filter_mobile" style="margin-top:0%"><i class="fa fa-plus"></i></a>\
                            </td>\
                          </tr>';
                    $(this).closest('table tbody').append(tr);
                    $(this).removeClass('add_more btn-success').addClass('remove_row btn-danger').html('<i class="glyphicon glyphicon-trash"></i>');

                    $('tr#' + sl + ' td .product:not(:first)').remove();

                    $('tr#' + sl + ' td .product_id option:selected').removeAttr('selected');

                    $('tr#' + sl + ' td .product_id option:first').prop('selected', true);
                    $('tr#' + sl + ' td .compotator option:first').prop('selected', true);
                    $('tr#' + sl + ' td .volume_or_value option:first').prop('selected', true);

                    $('tr#' + sl + ' td .qty').val('');

                    $('tr#' + sl + ' td .qty2').val(0);
                    $('tr#' + sl + ' td .qty2').prop('required', false);
                    $('tr#' + sl + ' td .qty2').prop('disabled', true);

                    $('tr#' + sl + ' td .product_id').attr('name', 'data[DistCombineQueryOnSalesReports][product_id][' + sl + '][]');
                    $('tr#' + sl + ' td .qty2').attr('name', 'data[DistCombineQueryOnSalesReports][qty2][' + sl + ']');
                    $('tr#' + sl + ' td .compotator').attr('name', 'data[DistCombineQueryOnSalesReports][compotator][' + sl + ']');
                    $('tr#' + sl + ' td .qty').attr('name', 'data[DistCombineQueryOnSalesReports][qty][' + sl + ']');
                    $('tr#' + sl + ' td .memo_total').attr('name', 'data[DistCombineQueryOnSalesReports][memo_total][' + sl + ']');


                } else {
                    alert('Please fill up this row!');
                }
            }
        }, '.add_more');

        $(document).on({
            click: function(e) {
                e.preventDefault();
                if (!$(this).parent().parent().find(".product:last").find('select').val()) {
                    alert("please select product");
                    return;
                }
                var product_var = $(this).prev().html();
                var html =
                    '<div class="product">\
                    ' + product_var + '\
                    <a href="#" class="btn btn-xs remove_product btn-danger " style="margin-top:1.5%"><i class="glyphicon glyphicon-trash"></i></a>\
                </div>';

                $(this).parent().append(html);
            }
        }, '.add_more_product');


        $(document).on({
            click: function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
            }
        }, '.remove_row');

        $(document).on({
            click: function(e) {
                e.preventDefault();
                $(this).parent().remove();
            }
        }, '.remove_product');



        $("body").on("change", ".compotator", function() {
            var compotator = $(this).val();

            var current_row_no = $(this).parent().parent().parent().attr('id');

            if (compotator == 'between') {
                $('tr#' + current_row_no + ' td .qty2').prop('required', true);
                $('tr#' + current_row_no + ' td .qty2').prop('disabled', false);
            } else {
                $('tr#' + current_row_no + ' td .qty2').prop('required', false);
                $('tr#' + current_row_no + ' td .qty2').prop('disabled', true);
            }
        });

        $("body").on("change", ".office_id", function() {
            var office_id = $('.office_id').val();
            get_tso_list(office_id);
        });

        function get_tso_list(office_id) {
            var date_from = $(".date_from").val();
            var date_to = $(".date_to").val();
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>DistCombineQueryOnSalesReports/get_tso_list',
                data: {
                    office_id: office_id,
                    date_from: date_from,
                    date_to: date_to
                },
                cache: false,
                success: function(response) {
                    $('.tso_id').html(response);
                }
            });
        }
    })
</script>


<script>
    $(document).ready(function() {

        $("#download_xl").click(function(e) {

            e.preventDefault();

            var html = $("#xls_body").html();

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "query_on_sales_reports.xls";

            document.body.appendChild(a);

            a.click();

        });

    });
</script>