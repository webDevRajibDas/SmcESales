<?php
App::import('Controller', 'ProductSalesReportsController');
$ProductSalesController = new ProductSalesReportsController;
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
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">



            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Product Sales Report'); ?></h3>
                <?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Product Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
            </div>


            <div class="box-body">

                <div class="search-box">
                    <?php echo $this->Form->create('ProductSalesReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <tr>
                            <td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true)); ?></td>

                            <td width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true)); ?></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
                            </td>
                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                                <?php if (!empty($requested_data)) { ?>
                                    <button onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                                <?php } ?>

                            </td>
                        </tr>
                    </table>

                    <?php echo $this->Form->end(); ?>
                </div>



                <?php if (!empty($requested_data)) { ?>

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
                            <div style="width:100%; text-align:center; padding:20px 0;">
                                <h2 style="margin:2px 0;">Social Marketing Company</h2>
                                <h3 style="margin:2px 0;">Product Sales Volume and Value by Brand</h3>
                                <p>
                                    Time Frame : <b><?= date('d M, Y', strtotime($date_from)) ?></b> to <b><?= date('d M, Y', strtotime($date_to)) ?></b> <?php /*?><br>Reporting Date: <b><?php echo date('d F, Y')?></b><?php */ ?>
                                </p>
                                <p>Print Unit : <?= $unit_types[$this->data['ProductSalesReports']['unit_type']] ?></p>
                            </div>


                            <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                <tr class="titlerow">
                                    <th>Product Category</th>
                                    <th>Product Brand</th>
                                    <th>Product Name</th>
                                    <th>Product Code</th>
                                    <th style="text-align:right;">Product Qty</th>
                                    <th style="text-align:right;">Product Value</th>
                                </tr>

                                <?php
                                $unit_type = $this->data['ProductSalesReports']['unit_type'];
                                foreach ($product_list as $value) {
                                    $sales_data = $ProductSalesController->getProductSales($date_from, $date_to, $value['Product']['id']);
                                    if ($sales_data['qty'] && $sales_data['val']) {
                                ?>

                                        <tr class="rowDataSd">
                                            <td><?= $value['ProductCategory']['name'] ?></td>
                                            <td><?= $value['Brand']['name'] ?></td>
                                            <td><?= $value['Product']['name'] ?></td>
                                            <td><?= $value['Product']['product_code'] ?></td>
                                            <td class="qty" style="text-align:right;">
                                                <?php
                                                echo $sales_data['qty'] ? sprintf("%01.2f", $unit_type == 2 ? ($ProductSalesController->unit_convert_from_global($value['Product']['id'], $value['Product']['sales_measurement_unit_id'], $sales_data['qty'])) : $sales_data['qty']) : '0.00'
                                                ?>
                                            </td>
                                            <td class="val" style="text-align:right;"><?= $sales_data['val'] ? sprintf("%01.2f", $sales_data['val']) : '0.00' ?></td>
                                        </tr>

                                <?php }
                                } ?>


                                <tr class="totalColumn">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><b>Total:</b></td>
                                    <td class="totalQty" style="text-align:right;"></td>
                                    <td class="totalVal" style="text-align:right;"></td>
                                </tr>


                            </table>




                            <script>
                                <?php $total_v = '0,0'; ?>
                                var totals_qty = [<?= $total_v ?>];
                                var totals_val = [<?= $total_v ?>];
                                $(document).ready(function() {

                                    var $dataRows = $("#sum_table tr:not('.totalColumn, .titlerow')");


                                    $dataRows.each(function() {
                                        $(this).find('.qty').each(function(i) {
                                            totals_qty[i] += parseFloat($(this).html());
                                        });
                                    });

                                    $("#sum_table .totalQty").each(function(i) {
                                        $(this).html(totals_qty[i]);
                                    });


                                    $dataRows.each(function() {
                                        $(this).find('.val').each(function(i) {
                                            totals_val[i] += parseFloat($(this).html());
                                        });
                                    });
                                    $("#sum_table .totalVal").each(function(i) {
                                        $(this).html(totals_val[i]);
                                    });


                                });
                            </script>


                            <div style="width:100%; padding:100px 0 50px;">
                                <div class="bottom_box">
                                    Prepared by:______________
                                </div>
                                <div class="bottom_box">
                                    Checked by:______________
                                </div>
                                <div class="bottom_box">
                                    Signed by:______________
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
    $('.outlet_type').on('ifChecked', function(event) {
        //alert($(this).val()); // alert value
        $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL; ?>ProductSalesReports/get_category_list',
            data: 'outlet_type=' + $(this).val(),
            cache: false,
            success: function(response) {
                //alert(response);						
                $('.td_product_categories').html(response);
            }
        });
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
        //mywindow.close();

        return true;
    }
</script>