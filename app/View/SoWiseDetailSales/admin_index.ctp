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
                    <?php echo $this->Form->create('SoWiseDetailSales', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">
                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?>
                            </td>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?>
                            </td>
                        </tr>
                        <?php if ($office_parent_id == 0) { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id',  'empty' => '---- Head Office ----', 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                        <?php if ($office_parent_id == 14) { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- All ----')); ?></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true,)); ?></td>
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
                                    <?php echo $this->Form->input('so_id', array('label' => 'Sales Officers', 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'options' => $so_list, 'empty' => '---- All ----')); ?>
                                </div>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="columns_box">
                                <?php echo $this->Form->input('columns', array('legend' => 'Product Type :', 'class' => 'columns', 'type' => 'radio', 'default' => 'product', 'options' => $columns, 'required' => true));  ?></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <div class="input select" style="float:left; width:100%; padding-bottom:20px;">
                                    <div id="product" class="selection_box" style="float:left; width:100%; display:block;">

                                        <div style="margin:auto; width:90%;">
                                            <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                            <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label>
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
                                    <p>Messuging Unit : <?= $unit_type_text ?> Unit</p>
                                </div>


                                <div style="float:left; width:100%; height:430px; overflow:scroll;">

                                    <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                        <thead>
                                            <tr class="titlerow">
                                                <th style="text-align:left;">Name</th>
                                                <th style="text-align:left;">Customer</th>
                                                <th>Memo Date</th>
                                                <th>Memo No.</th>
                                                <th style="text-align:right;">Quantity</th>
                                                <th style="text-align:right;">Value</th>
                                                <th style="text-align:right;">Memo Bonus</th>
                                            </tr>
                                        </thead>


                                        <tbody>
                                            <?php if ($results) { ?>

                                                <?php /*?><?php 
											$g_sales_qty = 0;
											$g_price = 0;
											$g_bonus = 0;
											foreach($results as $so_name => $product_datas){ 
											?>
                                            <tr><td style="text-align:left; font-weight:bold;" colspan="7">Sales Officer : <?=$so_name?></td></tr>
                                            
                                            <?php foreach($product_datas as $product_name => $memo_datas){ ?>
                                            
												<?php 
												$i=1;
												$total_sales_qty = 0;
												$total_price = 0;
												$total_bonus = 0;
												foreach($memo_datas as $memo_data)
												{ 
												$total_sales_qty+=$memo_data['sales_qty'];
												$total_price+=$memo_data['price'];
												$total_bonus+= @$b_results[$memo_data['product_id']][$memo_data['memo_no']]['sales_qty'];
												?>
                                                <tr>
                                                    <td><?=$i==1?$product_name:''?></td>
                                                    <td><?=$memo_data['outlet_name'].'-'.$memo_data['market_name'].'-'.$memo_data['thana_name'].'-'.$memo_data['district_name']?></td>
                                                    <td><?=$memo_data['memo_date']?></td>
                                                    <td><?=$memo_data['memo_no']?></td>
                                                    <td style="text-align:right;"><?=$memo_data['sales_qty']?></td>
                                                    <td style="text-align:right;"><?=$memo_data['price']?></td>
                                                    <td style="text-align:right;"><?=@$b_results[$memo_data['product_id']][$memo_data['memo_no']]['sales_qty']?$b_results[$memo_data['product_id']][$memo_data['memo_no']]['sales_qty']:0?></td>
                                                </tr>
                                                <?php $i++; } ?>
                                                
                                                <tr style="font-weight:bold;">
                                                    <td colspan="4" style="text-align:right;">Sub Total :</td>
                                                    <td style="text-align:right;"><?=sprintf("%01.2f", $total_sales_qty)?></td>
                                                    <td style="text-align:right;"><?=sprintf("%01.2f", $total_price)?></td>
                                                    <td style="text-align:right;"><?=$total_bonus?></td>
                                                </tr>
                                                <?php 
												$g_sales_qty+=$total_sales_qty;
												$g_price+=$total_price;
												$g_bonus+=$total_bonus;
												} 
												?>
                                            
                                            <?php 
											} 
											?>
                                            
                                            
                                            <tr style="font-weight:bold;">
                                                <td colspan="4" style="text-align:right;">Grand Total :</td>
                                                <td style="text-align:right;"><?=sprintf("%01.2f", $g_sales_qty)?></td>
                                                <td style="text-align:right;"><?=sprintf("%01.2f", $g_price)?></td>
                                                <td style="text-align:right;"><?=$g_bonus?></td>
                                            </tr><?php */ ?>

                                                <?= $output ?>

                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="7"><b>No Result Found!</b></td>
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

        //alert(rows);



        if (type == 'so') {
            $('#so_html').show();
        } else {
            $('#territory_html').show();
        }




    }


    $(document).ready(function() {


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
</script>




<script>
    $("#download_xl").click(function(e) {
        e.preventDefault();
        var html = $("#content").html();

        var blob = new Blob([html], {
            type: 'data:application/vnd.ms-excel'
        });
        var downloadUrl = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = downloadUrl;
        a.download = "so_wise_detail_sales.xls";
        document.body.appendChild(a);
        a.click();
    });
</script>