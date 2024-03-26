<?php
//pr($return_results);
?>
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
                    <?php echo $this->Form->create('DistInventoryStatementReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?>
                            </td>

                            <td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control date_to', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?></td>
                        </tr>

                        <?php if ($office_parent_id == 0) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>
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
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- All ----')); ?></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id')); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <!-- <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('type', array('legend' => 'Type :', 'class' => 'type', 'type' => 'radio', 'default' => 'dist', 'onClick' => 'typeChange(this.value)', 'options' => $types, 'required' => true)); ?>
                            </td>		
                        </tr> -->

                        <tr>
                            <td>
                                <div class="" id="dist_html">
                                    <?php echo $this->Form->input('dist_id', array('label' => 'Distributor:', 'id' => 'dist_id', 'class' => 'form-control dist_id', 'empty' => '---- All ----', 'options' => $dist_list)); ?>
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
                                <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true)); ?>
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
                                <?= $this->Html->link(__('Dwonload XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                            </div>

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
                                        <?php if ($dist_distributor_id) { ?>
                                            <span>, Distributor Name: <?= $dist_list[$dist_distributor_id] ?></span>
                                        <?php } ?>
                                    </p>
                                    <p>Messuging Unit : <?= $unit_type_text ?> Unit</p>
                                </div>


                                <div style="float:left; width:100%; height:430px; overflow:scroll;">

                                    <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                        <thead>
                                            <tr class="titlerow">
                                                <th style="min-width:200px;"></th>
                                                <th></th>
                                                <th colspan="3">Received</th>
                                                <th></th>
                                                <th colspan="5">Issued</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>



                                            <tr class="titlerow">
                                                <th style="border-top:#F1F1F1 solid 1px; text-align:left;" valign="top">Product</th>
                                                <th style="border-top:#F1F1F1 solid 1px; text-align:left;" valign="top">Opening Stock</th>

                                                <th>From Challan</th>
                                                <th>Adjustment</th>
                                                <th>Total</th>

                                                <th style="border-top:#F1F1F1 solid 1px; text-align:left;" valign="top">Total Stock</th>
                                                <th>Adjustment</th>
                                                <th>Return Qty</th>
                                                <th>Sales Qty</th>
                                                <th>Bonus Qty</th>
                                                <th>Total</th>

                                                <th style="border-top:#F1F1F1 solid 1px; text-align:left;" valign="top">Booking Qty</th>
                                                <th style="border-top:#F1F1F1 solid 1px; text-align:left;" valign="top">Bonus Booking Qty</th>
                                                <th style="border-top:#F1F1F1 solid 1px; text-align:left;" valign="top">Closing Stock</th>
                                            </tr>
                                        </thead>

                                        <?php foreach ($product_list as $source_name => $source_products) { ?>

                                            <tr style="background:#f7f7f7; font-size:13px;">
                                                <td colspan="22" style="text-align:left;"><b><?= $source_name ?></b></td>
                                            </tr>

                                            <?php foreach ($source_products as $type_name => $products) { ?>

                                                <tr style="background:#f2f2f2;">
                                                    <td colspan="22" style="text-align:left;"><b><?= $type_name ?></b></td>
                                                </tr>

                                                <?php
                                                foreach ($products as $product_id => $product_data) {
                                                    $product_name = $product_data['product_name'];
                                                ?>
                                                    <tr>
                                                        <td style="text-align:left;"><?= $product_name ?></td>

                                                        <td style="text-align:right;"><?= @$dist_opening = $dist_final_opening_closing[$product_id]['opening_balance'] + @$prev_invoice_results[$product_id]['sales_qty'] != 0 ? $dist_final_opening_closing[$product_id]['opening_balance'] + @$prev_invoice_results[$product_id]['sales_qty'] : '' ?></td>


                                                        <td style="text-align:right;"><?= @$receive_from_challan = $received_results[$product_id]['receive_from_challan'] ? $received_results[$product_id]['receive_from_challan'] : ''; ?></td>

                                                        <td style="text-align:right;"><?= @$receive_adjustment = $receive_adjustment_results[$product_id]['receive_adjustment'] ? $receive_adjustment_results[$product_id]['receive_adjustment'] : ''; ?></td>

                                                        <td style="text-align:right;"><?= $total_receive = $receive_from_challan + $receive_adjustment; ?></td>
                                                        <td style="text-align:right;"><?= $dist_opening + $total_receive ?></td>



                                                        <td style="text-align:right;"><?= @$issue_adjustment = $issue_adjustment_results[$product_id]['issue_adjustment'] ? $issue_adjustment_results[$product_id]['issue_adjustment'] : ''; ?></td>
                                                        <td style="text-align:right;"><?= $return_qty = @$return_results[$product_id]['return_qty'] > 0 ? sprintf("%01.2f", $return_results[$product_id]['return_qty']) : '' ?></td>
                                                        <td style="text-align:right;"><?= $sales_qty = @$sales_results[$product_id]['sales_qty'] > 0 ? sprintf("%01.2f", $sales_results[$product_id]['sales_qty']) : '' ?></td>
                                                        <td style="text-align:right;"><?= @$bounus_qty = @$bounus_results[$product_id]['bonus_qty'] > 0 ? sprintf("%01.2f", $bounus_results[$product_id]['bonus_qty']) : '' ?></td>
                                                        <td style="text-align:right;"><?= $issue_adjustment + $sales_qty + $bounus_qty + $return_qty ?></td>

                                                        <td style="text-align:right;"><?= @$invoice_qty = @$invoice_results[$product_id]['sales_qty'] > 0 ? sprintf("%01.2f", $invoice_results[$product_id]['sales_qty']) : '' ?></td>
                                                        <td style="text-align:right;"><?= @$bounus_invoice_qty = @$bounus_invoice_results[$product_id]['bonus_qty'] > 0 ? sprintf("%01.2f", $bounus_invoice_results[$product_id]['bonus_qty']) : '' ?></td>
                                                        <td style="text-align:right;"><?= @$dist_closing = $dist_final_opening_closing[$product_id]['closing_balance'] != 0 ? ($dist_final_opening_closing[$product_id]['closing_balance'] + $invoice_qty + $bounus_invoice_qty) : (($invoice_qty + $bounus_invoice_qty) > 0 ? ($invoice_qty + $bounus_invoice_qty) : '') ?></td>

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

    /*
    $('.office_id').selectChain({
        target: $('.territory_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
        type: 'post',
        data: {'office_id': 'office_id'}
    });
    */
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
                url: '<?= BASE_URL ?>DistInventoryStatementReports/get_office_dist_list',
                data: 'office_id=' + $(this).val() + '&date_from=' + date_from + '&date_to=' + date_to,
                cache: false,
                success: function(response) {
                    $('#dist_id').html(response);
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
        // $('#dist_html').hide();
        // $('#territory_html').hide();
        $('#dist_html').show();
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

        $("#download_xl").click(function(e) {

            e.preventDefault();

            var html = $("#xls_body").html();

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "Distributor_inventory_statemanet_report.xls";

            document.body.appendChild(a);

            a.click();

        });

    });
</script>