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
        width: 12.5%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    .columns_box legend {
        width: 25% !important;
    }

    #market_list .checkbox label {
        padding-left: 0px;
        width: auto;
    }

    #market_list .checkbox {
        width: 30%;
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
        width: 21% !important;
    }

    .market_list3 .checkbox {
        width: 20% !important;
    }




    .outlet_category {
        float: right;
        width: 85%;
        padding-left: 10%;
        border: #c7c7c7 solid 1px;
        height: 100px;
        overflow: auto;
        margin-right: 5%;
        padding-top: 5px;
    }

    .outlet_category2 {
        padding-left: 3%;
        height: 118px;
    }

    .outlet_category3 {
        width: 92%;
        margin-right: 3%;
        height: 115px;
    }

    .outlet_category .checkbox {
        float: left;
        width: 25% !important;
    }

    .outlet_category3 .checkbox {
        float: left;
        width: 25% !important;
    }

    .label_title {
        float: right;
        width: 85%;
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
        width: 92%;
        margin-right: 3%;
    }

    .outlet_category label {
        width: auto;
    }


    .outlet_category .form-control {
        width: 55% !important;
    }

    .outlet_category2 .input {
        padding-bottom: 3px;
        float: left;
        width: 100%;
    }

    .outlet_category2 label {
        width: 32%;
    }

    .selection_box {
        display: none;
    }

    .search .form-control {
        width: 50%;
    }

    .radio,
    .checkbox {
        margin: 0px;
    }
</style>


<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">



            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo $page_title; ?></h3>
                <?php /*?><div class="box-tools pull-right">
          <?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New ProjectionAchievement Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
        </div><?php */ ?>
            </div>


            <div class="box-body">

                <div class="search-box">
                    <?php echo $this->Form->create('AreaBatchLotByStocks', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">
                        <tr style="display:none;">
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => false)); ?>
                            </td>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control date_to datepicker', 'required' => false)); ?>
                            </td>
                        </tr>
                        <?php if ($office_parent_id == 0) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id',  'empty' => '---- Head Office ----', 'options' => $region_offices,)); ?></td>
                                <td class="text-left">

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($office_parent_id == 14) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id',  'options' => $region_offices,)); ?></td>
                                <td class="text-left">
                                </td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- All ----')); ?></td>
                                <td>
                                    <div class="operator_exp_value"><?php echo $this->Form->input('expire_date', array('class' => 'form-control expireDatepicker')); ?></div>
                                    <div class="between_value"><?php echo $this->Form->input('expire_date_from', array('class' => 'form-control operator_between_exp_value expireDatepicker')); ?></div>
                                </td>

                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id')); ?></td>
                                <td>
                                    <div class="operator_exp_value"><?php echo $this->Form->input('expire_date', array('class' => 'form-control expireDatepicker')); ?></div>
                                    <div class="between_value"><?php echo $this->Form->input('expire_date_from', array('class' => 'form-control operator_between_exp_value expireDatepicker')); ?></div>
                                </td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td width="50%">
                                <?php echo $this->Form->input('operator', array('label' => 'Operator :', 'id' => 'operator', 'class' => 'form-control operator', 'empty' => '---- All ----', 'options' => array('1' => 'Less than (<)', '2' => 'Gretter than (>)', '3' => 'Between'))); ?>
                            </td>
                            <td class="text-left">
                                <div class="between_value"><?php echo $this->Form->input('expire_date_to', array('class' => 'form-control operator_between_exp_value expireDatepicker1')); ?></div>
                            </td>
                        </tr>


                        <tr style="display:none;">
                            <td colspan="2">
                                <?php echo $this->Form->input('type', array('legend' => 'Type :', 'class' => 'type', 'type' => 'radio', 'default' => 'territory', 'onClick' => 'typeChange(this.value)', 'options' => $types, 'required' => true));  ?>
                            </td>
                        </tr>

                        <tr style="display:none;">
                            <td>
                                <div id="territory_html">
                                    <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id office_t_so', 'required' => false, 'empty' => '---- All ----')); ?>
                                </div>

                                <div id="so_html">
                                    <?php echo $this->Form->input('so_id', array('label' => 'Sales Officers', 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'options' => $so_list, 'empty' => '---- All ----')); ?>
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
                                <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
                            </td>
                        </tr>


                        <tr>
                            <td class="columns_box">
                                <?php echo $this->Form->input('columns', array('legend' => 'Selection :', 'class' => 'columns', 'type' => 'radio', 'default' => 'product', 'options' => $columns, 'required' => true));  ?></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <div class="input select" style="float:left; width:100%; padding-bottom:20px;">
                                    <div id="product" class="selection_box" style="float:left; width:100%; display:block;">

                                        <div style="margin:auto; width:90%;">
                                            <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall1" />
                                            <label for="checkall1" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label>
                                        </div>

                                        <p class="label_title pro_label_title">Product Selection</p>
                                        <div id="market_list" class="product_selection outlet_category outlet_category3">
                                            <?php echo $this->Form->input('product_id', array('label' => false, 'class' => 'checkbox product_id', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $product_list)); ?>
                                        </div>
                                    </div>

                                    <div id="brand" class="selection_box" style="float:left; width:100%;">
                                        <div style="margin:auto; width:90%;">
                                            <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
                                            <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label>
                                        </div>
                                        <p class="label_title pro_label_title">Brand Selection</p>
                                        <div id="market_list" class="brand_selection outlet_category outlet_category3">
                                            <?php echo $this->Form->input('brand_id', array('label' => false, 'class' => 'checkbox brand_id', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $brands)); ?>
                                        </div>
                                    </div>

                                    <div id="category" class="selection_box" style="float:left; width:100%;">
                                        <div style="margin:auto; width:90%;">
                                            <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall3" />
                                            <label for="checkall3" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label>
                                        </div>
                                        <p class="label_title pro_label_title">Category Selection</p>
                                        <div id="market_list" class="category_selection outlet_category outlet_category3">
                                            <?php echo $this->Form->input('product_category_id', array('label' => false, 'class' => 'checkbox product_category_id', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $categories)); ?>
                                        </div>
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






                <?php if (!empty($request_data) || $next_month_expire == 1) { ?>

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

                                <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                                    <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>


                                    <h3 style="margin:2px 0;"><?= $page_title ?></h3>

                                    <p>
                                        <b> Reporting Date : <?= @date('d M, Y') ?></b>
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

                                    <p><b>Measuring Unit: <?= $unit_type_text ?></b></p>

                                </div>


                                <div style="float:left; width:100%; height:430px; overflow:scroll;">

                                    <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                        <thead>
                                            <tr class="titlerow">
                                                <th style="text-align:left;">Product</th>
                                                <th>Store (Office Name)</th>
                                                <th style="text-align:left;">Lot/Batch</th>
                                                <th>Expiry Date</th>
                                                <th>Fund Source</th>
                                                <th>Sound Stock</th>
                                                <th>Damage Stock</th>
                                                <th>Reusable Stock</th>
                                                <th>Expired Stock</th>
                                                <th>Transit Stock</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php if ($results) { ?>
                                                <?php foreach ($results as $product_id => $product_datas) {
                                                    $product_count = 0; ?>
                                                    <?php foreach ($product_datas as $store_id => $product_datas_store) {
                                                        $store_count = 0;
                                                    ?>
                                                        <?php foreach ($product_datas_store as $batch_no => $expire_date_datas) { ?>
                                                            <?php foreach ($expire_date_datas as $expire_date => $datas) { ?>
                                                                <tr>
                                                                    <!-- <td style="text-align:left;"><?= $datas['product_name'] ?></td> -->
                                                                    <?php if ($product_count == 0) { ?><td style="text-align:left;" rowspan="<?= $results_span[$product_id]['row_span'] ?>"><?= $datas['product_name']; ?></td><?php  }
                                                                                                                                                                                                                            $product_count++; ?>
                                                                    <?php if ($store_count == 0) { ?><td style="text-align:left;" rowspan="<?= $results_span[$product_id][$store_id]['row_span'] ?>"><?= $datas['store_name'] . ' (<b>' . $offices[$datas['office_id']] . '</b>)' ?></td><?php  }
                                                                                                                                                                                                                                                                                        $store_count++; ?>
                                                                    <td style="text-align:left;"><?= $datas['batch_number'] ?></td>
                                                                    <td><?= $datas['expire_date'] ?></td>
                                                                    <td><?= $datas['product_source'] ?></td>
                                                                    <td><?= ($datas['inventory_status_id'] == 1) ? $datas['qty'] : '' ?></td>
                                                                    <td><?= ($datas['inventory_status_id'] == 7) ? $datas['qty'] : '' ?></td>
                                                                    <td><?= ($datas['inventory_status_id'] == 9) ? $datas['qty'] : '' ?></td>
                                                                    <td><?= ($datas['inventory_status_id'] == 8) ? $datas['qty'] : '' ?></td>
                                                                    <td>
                                                                        <?php
                                                                        if (isset($challan_results[$product_id][$store_id][$batch_no][$expire_date]['challan_qty'])) {
                                                                            $results_span[$product_id]['transit_stock']['grand_total'] = isset($results_span[$product_id]['transit_stock']['grand_total']) ? ($results_span[$product_id]['transit_stock']['grand_total'] += $challan_results[$product_id][$store_id][$batch_no][$expire_date]['challan_qty']) : $challan_results[$product_id][$store_id][$batch_no][$expire_date]['challan_qty'];
                                                                            $results_span['transit_stock']['total'] = isset($results_span['transit_stock']['total']) ? ($results_span['transit_stock']['total'] += $challan_results[$product_id][$store_id][$batch_no][$expire_date]['challan_qty']) : $challan_results[$product_id][$store_id][$batch_no][$expire_date]['challan_qty'];
                                                                            echo $challan_results[$product_id][$store_id][$batch_no][$expire_date]['challan_qty'];
                                                                        } else {
                                                                            echo '';
                                                                        } ?>

                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <tr style="background: #2828b957;">
                                                        <td colspan="5"><?= '<b>' . $product_list[$product_id] . ' Total </b>' ?></td>
                                                        <td><?= isset($results_span[$product_id][1]['grand_total']) ? $results_span[$product_id][1]['grand_total'] : ''; ?></td>
                                                        <td><?= isset($results_span[$product_id][7]['grand_total']) ? $results_span[$product_id][7]['grand_total'] : ''; ?></td>
                                                        <td><?= isset($results_span[$product_id][9]['grand_total']) ? $results_span[$product_id][9]['grand_total'] : ''; ?></td>
                                                        <td><?= isset($results_span[$product_id][8]['grand_total']) ? $results_span[$product_id][8]['grand_total'] : ''; ?></td>
                                                        <td><?= isset($results_span[$product_id]['transit_stock']['grand_total']) ? $results_span[$product_id]['transit_stock']['grand_total'] : ''; ?></td>
                                                    </tr>
                                                <?php } ?>
                                                <tr style="background: #b9284747;">
                                                    <td colspan="5"><b>Grand Total </b></td>
                                                    <td><?= isset($results_span[1]['total']) ? $results_span[1]['total'] : ''; ?></td>
                                                    <td><?= isset($results_span[7]['total']) ? $results_span[7]['total'] : ''; ?></td>
                                                    <td><?= isset($results_span[9]['total']) ? $results_span[9]['total'] : ''; ?></td>
                                                    <td><?= isset($results_span[8]['total']) ? $results_span[8]['total'] : ''; ?></td>
                                                    <td><?= isset($results_span['transit_stock']['total']) ? $results_span['transit_stock']['total'] : ''; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    </table>

                                </div>


                                <!--<div style="float:left; width:100%; padding:100px 0 50px;">
                                <div class="bottom_box">
                                    Prepared by:______________ 
                                </div>
                                <div class="bottom_box">
                                    Checked by:______________ 
                                </div>
                                <div class="bottom_box">
                                    Signed by:______________
                                </div>		  
                            </div>-->


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
        url: '<?= BASE_URL . 'ProjectionAchievement_reports/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
    $('.region_office_id').change(function() {
        $('#territory_id').html('<option value="">---- All ----');
    });
    /* $('.office_id').selectChain({
        target: $('.territory_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
        type: 'post',
        data: {
            'office_id': 'office_id'
        }
    }); */
</script>



<script>
    $(document).ready(function() {
        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');

        $('.checkall').click(function(e) {
            var checked = $(this).prop('checked');
            $(this).closest('.select').find('.selection').find('input:checkbox').prop('checked', checked);
        });

        $('#checkall1').click(function() {
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

    $('#office_id').change(function() {
        /*
                //alert($(this).val());
                date_from = $('.date_from').val();
                date_to = $('.date_to').val();
                if(date_from && date_to){
                    $.ajax({
                        type: "POST",
                        url: '<?= BASE_URL ?>market_characteristic_reports/get_office_so_list',
                        data: 'office_id='+$(this).val() + '&date_from=' + date_from + '&date_to=' + date_to,
                        cache: false,
                        success: function(response){
                            //alert(response);
                            $('#so_id').html(response);
                        }
                    });
                }
                else
                {
                    $('#office_id option:nth-child(1)').prop("selected", true);
                    alert('Please select date range!');
                }
            */
    });

    $(document).ready(function() {
        typeChange();
    });


    function typeChange() {
        var type = $('.type:checked').val();

        //for territory and so
        $('#so_html').hide();
        $('#territory_html').hide();

        //alert(rows);



        if (type == 'so') {
            $('#so_html').show();
        } else {
            $('#territory_html').show();
        }




    }


    $(document).ready(function() {


        /*-------------- Operator Part : Start ---------------*/
        $(".operator").change(function() {
            operator_value_set();
        });
        operator_value_set();

        function operator_value_set() {
            var operator_value = $(".operator").val();
            if (operator_value == 3) {
                $('.between_value').show();
                $('.between_value input').attr('required', true);
                $('.operator_exp_value').hide();
                $('.operator_exp_value input').attr('required', false);
            } else if (operator_value == 1 || operator_value == 2) {
                $('.operator_exp_value').show();
                $('.operator_exp_value input').attr('required', true);
                $('.between_value').hide();
                $('.between_value input').attr('required', false);
            } else {
                $('.operator_exp_value').hide();
                $('.operator_exp_value input').attr('required', false);
                $('.between_value').hide();
                $('.between_value input').attr('required', false);
            }
        }
        /*-------------- Operator Part : END ---------------*/


        var columns = $('.columns:checked').val();
        $('.selection_box').hide();
        if (columns == 'brand') {
            $('#brand').show();
        } else if (columns == 'category') {
            $('#category').show();
        } else {
            $('#product').show();
        }

        $('.columns').change(function() {
            //alert(this.value);

            if (this.value == 'product' || this.value == 'category' || this.value == 'brand') {
                //$('.selection').prop("checked", false);
                //$('.selection').prop('checked', false);
                //alert(111);
                $('.product_selection').find('input:checkbox').prop('checked', false);
                $('.brand_selection').find('input:checkbox').prop('checked', false);
                $('.category_selection').find('input:checkbox').prop('checked', false);

                $('#checkall').prop('checked', false);
                $('#checkall2').prop('checked', false);
                $('#checkall3').prop('checked', false);
            }

            $('.selection_box').hide();
            if (this.value == 'brand') {
                $('#brand').show();
            } else if (this.value == 'category') {
                $('#category').show();
            } else {
                $('#product').show();
            }
        });



    });


    var dates = $('.expireDatepicker').datepicker({
        'format': "M-yy",
        'startView': "year",
        'minViewMode': "months",
        'autoclose': true
    });
    $('.expireDatepicker').click(function() {
        dates.datepicker('setDate', null);
    });

    var dates1 = $('.expireDatepicker1').datepicker({
        'format': "M-yy",
        'startView': "year",
        'minViewMode': "months",
        'autoclose': true
    });
    $('.expireDatepicker1').click(function() {
        dates1.datepicker('setDate', null);
    });

    $('.datepicker2').datepicker({
        maxDate: 'now',
        format: 'dd-mm-yyyy',
    });
</script>




<script>
    $("#download_xl").click(function(e) {
        e.preventDefault();
        var html = $("#xls_body").html();

        var blob = new Blob([html], {
            type: 'data:application/vnd.ms-excel'
        });
        var downloadUrl = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = downloadUrl;
        a.download = "transaction_list_on_stocks.xls";
        document.body.appendChild(a);
        a.click();
    });
</script>