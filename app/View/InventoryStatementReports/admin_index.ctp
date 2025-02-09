<style>
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


<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title ?></h3>
            </div>

            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('InventoryStatementReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?>
                            </td>

                            <td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control date_to', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?></td>
                        </tr>

                        <?php if ($office_parent_id == 0) { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id',  'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 14) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- All ----')); ?></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('type', array('legend' => 'Type :', 'class' => 'type', 'type' => 'radio', 'default' => 'territory', 'onClick' => 'typeChange(this.value)', 'options' => $types, 'required' => true));  ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div id="territory_html">
                                    <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id office_t_so', 'required' => false, 'empty' => '---- All ----')); ?>
                                </div>

                                <div id="so_html">
                                    <?php echo $this->Form->input('so_id', array('label' => 'Sales Officers :', 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'options' => $so_list, 'empty' => '---- All ----')); ?>
                                </div>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Product Type :</label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="product_type_id" class="checkall" />
                                        <label for="product_type_id" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection">
                                        <?php echo $this->Form->input('product_type_id', array('id' => 'product_type_id', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $product_types)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <label for="source" style="float:left; width:15%;">Product Source :</label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="source" class="checkall2" />
                                        <label for="source" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection2">
                                        <?php echo $this->Form->input('source', array('id' => 'source', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $product_sources)); ?>
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
                                <label for="source" style="float:left; width:15%;"> Day Wise Closing : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">

                                    <div class="selection">
                                        <?php echo $this->Form->input('day_wise_closing', array('id' => 'day_wise_closing', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => array('day_wise_closing' => ''))); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('report_type', array('legend' => ' ', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $report_type, 'required' => true));  ?>
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
                            <?php if ($reports_type == 1) { ?>
                                <div id="xls_body">

                                    <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                                        <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>


                                        <h3 style="margin:2px 0;"><?= $page_title ?></h3>

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
                                            <?php if ($territory_id) { ?>
                                                <span>, Territory Name: <?= $territories[$territory_id] ?></span>
                                            <?php } ?>
                                        </p>
                                        <p>Measuring  Unit : <?= $unit_type_text ?> Unit</p>
                                    </div>


                                    <div style="float:left; width:100%; height:430px; overflow:scroll;">

                                        <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                            <thead>
                                                <tr class="titlerow">
                                                    <th style="min-width:200px;"></th>

                                                    <th colspan="3">Opening Stock</th>
                                                    <th colspan="6">Product Received</th>
                                                    <th colspan="6">Product Issued</th>

                                                    <th style="border-bottom:none;"></th>
                                                    <th style="border-bottom:none;"></th>
                                                    <!-- <?php //if(!$territory_id){ 
                                                            ?><th style="border-bottom:none;"></th><?php //} 
                                                                                                    ?> -->

                                                    <th colspan="5">Closing Stock</th>
                                                    <th colspan="3">Stock Monitoring</th>
                                                    <?php if ($is_day_wise_closing == 1) { ?>
                                                        <th colspan="<?php echo $total_day ?>">Day Wise Closing(Area)</th>
                                                        <th colspan="<?php echo $total_day ?>">Day Wise Closing(Territory)</th>
                                                    <?php } ?>
                                                </tr>



                                                <tr class="titlerow">
                                                    <th style="border-top:#F1F1F1 solid 1px; text-align:left;" valign="top">Product</th>

                                                    <th>Area Stock</th>
                                                    <th>Territory Stock</th>
                                                    <th>Total</th>

                                                    <th>From CWH</th>
                                                    <th>Return from<br>Territory</th>
                                                    <th>From ASO</th>
                                                    <th>From<br>Others</th>
                                                    <th>Return From<br>DB</th>
                                                    <th>Total<br>Received</th>

                                                    <th>Return to<br>CWH</th>
                                                    <th>To Terriotry</th>
                                                    <th>To ASO</th>
                                                    <th>To Others</th>
                                                    <th>DB Issue</th>
                                                    <th>Total Issue</th>

                                                    <th style="border-top:#F1F1F1 solid 1px;" valign="top">Sales Qty</th>
                                                    <th style="border-top:#F1F1F1 solid 1px;" valign="top">Bonus/Gift<br>Issue</th>


                                                    <?php if (!$territory_id) { ?><th valign="top">So Transit <br> Stock</th><?php } ?>
                                                    <?php if (!$territory_id) { ?><th valign="top">Prev So Transit <br> Stock</th><?php } ?>
                                                    <th>Area Office<br>Stock</th>
                                                    <th>Territory<br>Stock</th>
                                                    <th>Total Stock</th>

                                                    <th>Area Office<br>Stock (Days)</th>
                                                    <th>Territory<br>Stock<br>(Days)</th>
                                                    <th>Total Stock<br>(Days)</th>
                                                    <?php if ($is_day_wise_closing == 1) {
                                                        for ($date = $date_from; $date < $date_to; $date = date('Y-m-d', strtotime($date . '+1 Day'))) {
                                                    ?>
                                                            <th>Day <?php echo date('d', strtotime($date)); ?></th>
                                                    <?php
                                                        }
                                                    } ?>
                                                    <?php if ($is_day_wise_closing == 1) {
                                                        for ($date = $date_from; $date < $date_to; $date = date('Y-m-d', strtotime($date . '+1 Day'))) {
                                                    ?>
                                                            <th>Day <?php echo date('d', strtotime($date)); ?></th>
                                                    <?php
                                                        }
                                                    } ?>
                                                </tr>
                                            </thead>

                                            <?php foreach ($product_list as $source_name => $source_products) { ?>

                                                <tr style="background:#f7f7f7; font-size:13px;">
                                                    <td colspan="<?php if (!$territory_id) {
                                                                        $span = 25;
                                                                        if ($is_day_wise_closing == 1) {
                                                                            $span += ($total_day * 2);
                                                                        }
                                                                        echo $span;
                                                                    } else {
                                                                        $span = 24;
                                                                        if ($is_day_wise_closing == 1) {
                                                                            $span += ($total_day * 2);
                                                                        }
                                                                        echo $span;
                                                                    } ?>" style="text-align:left;"><b><?= $source_name ?></b></td>
                                                </tr>

                                                <?php foreach ($source_products as $type_name => $products) { ?>

                                                    <tr style="background:#f2f2f2;">
                                                        <td colspan="<?php if (!$territory_id) {
                                                                            $span = 25;
                                                                            if ($is_day_wise_closing == 1) {
                                                                                $span += ($total_day * 2);
                                                                            }
                                                                            echo $span;
                                                                        } else {
                                                                            $span = 24;
                                                                            if ($is_day_wise_closing == 1) {
                                                                                $span += ($total_day * 2);
                                                                            }
                                                                            echo $span;
                                                                        } ?>" style="text-align:left;"><b><?= $type_name ?></b></td>
                                                    </tr>

                                                    <?php
                                                    foreach ($products as $product_id => $product_data) {
                                                        $product_name = $product_data['product_name'];
                                                    ?>
                                                        <tr>
                                                            <td style="text-align:left;"><?= $product_name ?></td>

                                                            <td style="text-align:right;"><?= @$aso_opening = ($territory_id || $so_id) ? '' : $aso_final_opening_closing[$product_id]['opening_balance'] != 0 ? $aso_final_opening_closing[$product_id]['opening_balance'] : '' ?></td>
                                                            <td style="text-align:right;"><?= @$so_opening = $so_final_opening_closing[$product_id]['opening_balance'] != 0 ? $so_final_opening_closing[$product_id]['opening_balance'] : '' ?></td>
                                                            <td style="text-align:right;"><?= ($aso_opening + $so_opening != 0) ? @sprintf("%01.2f", $aso_opening + $so_opening) : '' ?></td>

                                                            <?php
                                                            @$from_cwh_to_aso = $received_results[$product_id]['from_cwh_to_aso'] ? $received_results[$product_id]['from_cwh_to_aso'] : '';
                                                            @$from_territory_to_aso = $received_results[$product_id]['from_territory_to_aso'] ? $received_results[$product_id]['from_territory_to_aso'] : '';
                                                            @$from_aso = $received_results[$product_id]['from_aso'] ? $received_results[$product_id]['from_aso'] : '';
                                                            @$from_orthers = $received_results[$product_id]['from_orthers'] ? $received_results[$product_id]['from_orthers'] : '';
                                                            @$db_return = $received_results[$product_id]['db_return'] ? $received_results[$product_id]['db_return'] : '';
                                                            ?>
                                                            <td style="text-align:right;"><?= $from_cwh_to_aso ?></td>
                                                            <td style="text-align:right;"><?= $from_territory_to_aso ?></td>
                                                            <td style="text-align:right;"><?= $from_aso ?></td>
                                                            <td style="text-align:right;"><?= $from_orthers ?></td>
                                                            <td style="text-align:right;"><?= $db_return ?></td>
                                                            <?php
                                                            $total_r = $from_cwh_to_aso + $from_territory_to_aso + $from_aso + $from_orthers + $db_return;
                                                            ?>
                                                            <td style="text-align:right;"><?= ($total_r > 0) ? sprintf("%01.2f", $total_r) : '' ?></td>



                                                            <?php
                                                            @$to_cwh = $issue_results[$product_id]['to_cwh'] ? $issue_results[$product_id]['to_cwh'] : '';
                                                            @$to_territory = $issue_results[$product_id]['to_territory'] ? $issue_results[$product_id]['to_territory'] : '';
                                                            @$to_aso = $issue_results[$product_id]['to_aso'] ? $issue_results[$product_id]['to_aso'] : '';
                                                            @$to_orthers = $issue_results[$product_id]['to_orthers'] ? $issue_results[$product_id]['to_orthers'] : '';
                                                            @$db_issue = $issue_results[$product_id]['db_issue'] ? $issue_results[$product_id]['db_issue'] : '';
                                                            ?>
                                                            <td style="text-align:right;"><?= $to_cwh ?></td>
                                                            <td style="text-align:right;"><?= $to_territory ?></td>
                                                            <td style="text-align:right;"><?= $to_aso ?></td>
                                                            <td style="text-align:right;"><?= $to_orthers ?></td>
                                                            <td style="text-align:right;"><?=$db_issue ?></td>
                                                            <?php
                                                            $total_issue = $to_cwh + $to_territory + $to_aso + $to_orthers + $db_issue;
                                                            ?>
                                                            <td style="text-align:right;"><?= ($total_issue > 0) ? sprintf("%01.2f", ($total_issue)) : '' ?></td>



                                                            <td style="text-align:right;"><?= @$sbg_results[$product_id]['sales_qty'] ? $sbg_results[$product_id]['sales_qty'] : '' ?></td>
                                                            <td>
                                                                <?= @$sbg_results[$product_id]['bonus_qty'] + @$sbg_results[$product_id]['gift_qty'] ?>
                                                                <!-- <?php if (@$sbg_results[$product_id]['gift_qty']) { ?>
                                            <?= @$sbg_results[$product_id]['gift_qty'] > 0 ? sprintf("%01.2f", $sbg_results[$product_id]['gift_qty']) : '' ?>
                                        <?php } else { ?>
                                            <?= @$sbg_results[$product_id]['bonus_qty'] > 0 ? sprintf("%01.2f", $sbg_results[$product_id]['bonus_qty']) : '' ?>
                                        <?php } ?> -->
                                                                <?php /*?><?=@$sbg_results[$product_id]['gift_qty']?$sbg_results[$product_id]['gift_qty']:@$sbg_results[$product_id]['bonus_qty']?@$sbg_results[$product_id]['bonus_qty']:0?><?php */ ?>
                                                            </td>


                                                            <?php if (!$territory_id) { ?><td style="text-align:right;"><?= (@$issue_results[$product_id]['transit_qty'] ? $issue_results[$product_id]['transit_qty'] : '')  ?></td><?php } ?>
                                                            <?php if (!$territory_id) { ?><td style="text-align:right;"><?= (@$issue_results[$product_id]['transit_qty_rcv'] ? $issue_results[$product_id]['transit_qty_rcv'] : '');  ?></td><?php } ?>
                                                            <td style="text-align:right;">
                                                                <?= @$aso_closing = ($territory_id || $so_id) ? '' : $aso_final_opening_closing[$product_id]['closing_balance'] != 0 ? $aso_final_opening_closing[$product_id]['closing_balance'] : '' ?>
                                                            </td>

                                                            <td style="text-align:right;"><?= @$so_closing = $so_final_opening_closing[$product_id]['closing_balance'] != 0 ? $so_final_opening_closing[$product_id]['closing_balance'] : '' ?></td>

                                                            <td style="text-align:right;"><?= ($aso_closing + $so_closing + @$issue_results[$product_id]['transit_qty'] != 0) ? sprintf("%01.2f", $aso_closing + $so_closing + @$issue_results[$product_id]['transit_qty']) : '' ?></td>


                                                            <?php
                                                            $datediff = strtotime($date_to) - strtotime($date_from);
                                                            $days = round($datediff / (60 * 60 * 24));
                                                            @$per_day_sales = $sbg_results[$product_id]['sales_qty'] / $days;
                                                            ?>
                                                            <td>
                                                                <?php @$aso_remaing_days = $aso_closing / $per_day_sales; ?>
                                                                <?= ($aso_remaing_days > 0) ? sprintf("%01.2f", $aso_remaing_days) : '' ?>
                                                            </td>

                                                            <td>
                                                                <?php @$so_remaing_days = $so_closing / $per_day_sales; ?>
                                                                <?= ($so_remaing_days > 0) ? sprintf("%01.2f", $so_remaing_days) : '' ?>
                                                            </td>

                                                            <td><?= ($aso_remaing_days + $so_remaing_days > 0) ? sprintf("%01.2f", ($aso_remaing_days + $so_remaing_days)) : '' ?></td>
                                                            <?php if ($is_day_wise_closing == 1) {
                                                                for ($date = $date_from; $date < $date_to; $date = date('Y-m-d', strtotime($date . '+1 Day'))) {
                                                            ?>
                                                                    <td><?php echo ($territory_id || $so_id) || !isset($aso_day_wise_closing[$product_id]) || !isset($aso_day_wise_closing[$product_id][$date]) ? '' : $aso_day_wise_closing[$product_id][$date] ?></td>
                                                            <?php
                                                                }
                                                            } ?>

                                                            <?php if ($is_day_wise_closing == 1) {
                                                                for ($date = $date_from; $date < $date_to; $date = date('Y-m-d', strtotime($date . '+1 Day'))) {
                                                            ?>
                                                                    <td><?php echo ((isset($so_day_wise_closing[$product_id]) && isset($so_day_wise_closing[$product_id][$date])) ? $so_day_wise_closing[$product_id][$date] : '');  ?></td>
                                                            <?php
                                                                }
                                                            } ?>
                                                        </tr>
                                            <?php
                                                    }
                                                }
                                                //break; 
                                            }
                                            ?>

                                        </table>

                                    </div>
                                    

                                </div>
                            <?php } elseif ($reports_type == 2) { ?>
                                <?php if ($adjustment_result) { ?>
                                    <div id="xls_body">
                                        <div style="float:left; width:100%; height:430px; overflow:scroll;">
                                            <?php if ($adjustment_result[2]) { ?>
                                                <div>
                                                    <p> Adjustment In</p>
                                                </div>
                                                <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table_in">
                                                    <tr class="titlerow">
                                                        <th>Product</th>
                                                        <?php foreach ($adjustment_in as $key => $name) { ?>
                                                            <th><?= $name ?></th>
                                                        <?php } ?>
                                                        <th>Total</th>
                                                    </tr>
                                                    <?php foreach ($adjustment_result_product[2] as $product_id => $in_data) { ?>
                                                        <tr>
                                                            <td><?= $in_data['product'] ?></td>
                                                            <?php $total = 0;
                                                            foreach ($adjustment_in as $key => $name) { ?>
                                                                <td>
                                                                    <?php if ($adjustment_result[2][$product_id][$key]) {
                                                                        echo $adjustment_result[2][$product_id][$key];
                                                                        $total += $adjustment_result[2][$product_id][$key];
                                                                    }
                                                                    ?>
                                                                </td>
                                                            <?php } ?>
                                                            <td><?= $total ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            <?php } ?>
                                            <?php if ($adjustment_result[1]) { ?>
                                                <div>
                                                    <p> Adjustment Out</p>
                                                </div>
                                                <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table_out">
                                                    <tr class="titlerow">
                                                        <th>Product</th>
                                                        <?php foreach ($adjustment_out as $key => $name) { ?>
                                                            <th><?= $name ?></th>
                                                        <?php } ?>
                                                        <th>Total</th>
                                                    </tr>
                                                    <?php foreach ($adjustment_result_product[1] as $product_id => $in_data) { ?>
                                                        <tr>
                                                            <td><?= $in_data['product'] ?></td>
                                                            <?php $total = 0;
                                                            foreach ($adjustment_out as $key => $name) { ?>
                                                                <td>
                                                                    <?php if ($adjustment_result[1][$product_id][$key]) {
                                                                        echo $adjustment_result[1][$product_id][$key];
                                                                        $total += $adjustment_result[1][$product_id][$key];
                                                                    }
                                                                    ?>
                                                                </td>
                                                            <?php } ?>
                                                            <td><?= $total ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php  } else { ?>
                                    <div style="clear:both;"></div>
                                    <div class="alert alert-warning">No Report Found!</div>
                            <?php }
                            } ?>

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
    $('.office_id').selectChain({
        target: $('.territory_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
        type: 'post',
        data: {
            'office_id': 'office_id'
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
        });

        $('.checkall2').click(function(e) {
            var checked = $(this).prop('checked');
            $(this).closest('.select').find('.selection2').find('input:checkbox').prop('checked', checked);
        })

    });

    $('#office_id').change(function() {
        //alert($(this).val());
        date_from = $('.date_from').val();
        date_to = $('.date_to').val();
        if (date_from && date_to) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>market_characteristic_reports/get_office_so_list',
                data: 'office_id=' + $(this).val() + '&date_from=' + date_from + '&date_to=' + date_to,
                cache: false,
                success: function(response) {
                    //alert(response);						
                    $('#so_id').html(response);
                }
            });
        } else {
            $('#office_id option:nth-child(1)').prop("selected", true);
            alert('Please select date range!');
        }
    });


    $(document).ready(function() {
        typeChange();
    });


    function typeChange() {
        var type = $('.type:checked').val();

        //for territory and so 
        $('#so_html').hide();
        $('#territory_html').hide();


        if (type == 'so') {
            $('#so_html').show();
        } else {
            $('#territory_html').show();
        }

        if (type == 'so') {
            $('.office_t_so option:nth-child(1)').prop("selected", true).change();
        } else if (type == 'territory') {
            $('#so_id option:nth-child(1)').prop("selected", true).change();
        } else {
            <?php if (!@$request_data['DcrReports']['territory_id']) { ?>
                $('.office_t_so option:nth-child(1)').prop("selected", true).change();
            <?php } ?>

            <?php if (!@$request_data['DcrReports']['so_id']) { ?>
                $('#so_id option:nth-child(1)').prop("selected", true).change();
            <?php } ?>
        }
    }
</script>

<style>
    .datepicker table tr td.disabled,
    .datepicker table tr td.disabled:hover {
        color: #c7c7c7;
    }
</style>
<script>
    $(document).ready(function() {
        //var date = new Date();
        var yesterday = new Date(new Date().setDate(new Date().getDate() - 1));

        $('.date_to').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true,
            endDate: yesterday
        });

    });
</script>

<script>
    $(document).ready(function() {

        remove_zero_value_column();

        function remove_zero_value_column() {
            var column_sum_in = [];
            $("#sum_table_in tr:not(:first) td").each(function(i, value) {
                var value_txt = parseFloat($(this).text());
                if (isNaN(value_txt)) {
                    value_txt = 0
                }
                var column_index = $(this).index();
                column_sum_in[column_index] = column_sum_in[column_index] ? column_sum_in[column_index] + value_txt : 0 + value_txt
            });
            $.each(column_sum_in, function(index, value) {
                if (value == 0) {
                    $("#sum_table_in th:not(:first-child,:last-child):nth-child(" + (index + 1) + ")").addClass('remove_column');
                    $("#sum_table_in td:not(:first-child,:last-child):nth-child(" + (index + 1) + ")").addClass('remove_column');
                }
            });


            var column_sum_out = [];
            $("#sum_table_out tr:not(:first) td").each(function(i, value) {
                var value_txt = parseFloat($(this).text());
                if (isNaN(value_txt)) {
                    value_txt = 0
                }
                var column_index = $(this).index();
                column_sum_out[column_index] = column_sum_out[column_index] ? column_sum_out[column_index] + value_txt : 0 + value_txt
            });
            $.each(column_sum_out, function(index, value) {
                if (value == 0) {
                    $("#sum_table_out th:not(:first-child,:last-child):nth-child(" + (index + 1) + ")").addClass('remove_column');
                    $("#sum_table_out td:not(:first-child,:last-child):nth-child(" + (index + 1) + ")").addClass('remove_column');
                }
            });

            $(".remove_column").remove();

        }

        $("#download_xl").click(function(e) {

            e.preventDefault();

            var html = $("#xls_body").html();

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "inventory_statemanet_report.xls";

            document.body.appendChild(a);

            a.click();

        });

    });
</script>