<?php
App::import('Controller', 'DistDistribuReportsController');
$DistDistribuReportsController = new DistDistribuReportsController;
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

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 30%;
        float: left;
        margin: 1px 0;
    }

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
    }

    table td,
    table th {
        padding: 2px 3px;
    }
</style>


<div class="row">

    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distric & Division Wise Distribution Report'); ?></h3>
            </div>

            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('search', array('role' => 'form')); ?>
                    <table class="search">
                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?>
                            </td>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?>
                            </td>
                        </tr>
                        <?php /*?><tr>
                            <td width="50%"><?php echo $this->Form->input('division_id', array('class' => 'form-control','empty'=>'---- All ----', 'required'=>false)); ?></td>
    						<td width="50%"><?php //echo $this->Form->input('unit_type', array('legend'=>'Unit Type :', 'type' => 'radio', 'options' => $unit_type, 'required'=>true)); ?></td>
                        </tr><?php */ ?>

                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Division : </label>
                                <div id="market_list" class="td_product_categories input select" style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
                                        <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection2">
                                        <?php echo $this->Form->input('division_id', array('id' => 'division_id', 'label' => false, 'class' => 'checkbox simple', 'multiple' => 'checkbox', 'required' => true)); ?>
                                    </div>
                                </div>
                            </td>
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
                            <td colspan="2">
                                <label style="float:left; width:15%;">Product Categories : </label>
                                <div id="market_list" class="td_product_categories input select" style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                        <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection">
                                        <?php echo $this->Form->input('product_categories_id', array('id' => 'product_category_id', 'label' => false, 'class' => 'checkbox category_box', 'multiple' => 'checkbox', 'required' => true)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Products : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                    <div style="margin:auto; width:90%; float:left;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall1" />
                                        <label for="checkall1" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection1" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto">
                                        <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                                <?php if (!empty($request_data)) { ?>
                                    <button onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                                <?php } ?>

                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>


                    <script>
                        //$(input[type='checkbox']).iCheck(false); 
                        $(document).ready(function() {
                            $("input[type='checkbox']").iCheck('destroy');
                            $("input[type='radio']").iCheck('destroy');
                            $('#checkall').click(function() {
                                var checked = $(this).prop('checked');
                                $('.selection').find('input:checkbox').prop('checked', checked);
                                productBoxList();
                            });
                            $('#checkall1').click(function() {
                                var checked = $(this).prop('checked');
                                $('.selection1').find('input:checkbox').prop('checked', checked);
                            });
                            $('#checkall2').click(function() {
                                var checked = $(this).prop('checked');
                                $('.selection2').find('input:checkbox').prop('checked', checked);
                            });
                        });
                    </script>

                    <?php if (!empty($request_data)) { ?>
                        <div class="row">

                            <div id="content" style="width:90%; margin:0 5%;">
                                <style type="text/css">
                                    .table-responsive {
                                        color: #333;
                                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                                        line-height: 1.42857;
                                    }

                                    .report_table {
                                        font-size: 13px;
                                    }

                                    .qty_val {
                                        width: 100px;
                                        margin: 0;
                                        float: left;
                                        text-transform: capitalize;
                                    }

                                    .qty,
                                    .val {
                                        width: 49%;
                                        float: left;
                                        border-right: #333 solid 1px;
                                        text-align: center;
                                        padding: 5px 0;
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

                                    .rowDataSd,
                                    .totalCol {
                                        font-size: 85%;
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

                                    <?php /*?><div class="pull-right csv_btn" style="padding-top:20px;">
									<?=$this->Html->link(__('Download XLS'), array('action' => 'Download_xls?data='.serialize($request_data)), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                                </div><?php */ ?>

                                    <div class="pull-right csv_btn" style="padding-top:20px;">
                                        <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                                    </div>

                                    <div id="xls_body">
                                        <div style="width:100%; text-align:center; padding:20px 0;">
                                            <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
                                            <h3 style="margin:10px 0; font-size:18px;">Distric & Division Wise Distribution Report</h3>
                                            <p>Time Frame : <b><?= date('d M, Y', strtotime($date_from)) ?></b> to <b><?= date('d M, Y', strtotime($date_to)) ?></b></p>
                                            <p><b>Measuring Unit: <?= $unit_type_text ?></b></p>
                                        </div>


                                        <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                            <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center" style="margin-bottom:-1px;">

                                                <tr class="titlerow">
                                                    <th>
                                                        <div class="qty_val">District</div>
                                                    </th>

                                                    <?php
                                                    $total_products = 0;
                                                    foreach ($categories_products as $c_value) {
                                                        $total_products += count($c_value['Product']);

                                                    ?>

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
                                                        <div class="qty_val">Total Sales</div>
                                                    </th>

                                                </tr>
                                            </table>


                                            <?php
                                            foreach ($divisions_2 as $value) {
                                                $division_id = $value['Division']['id'];
                                            ?>
                                                <table id="division_table<?= $division_id ?>" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center" style="margin-bottom:-1px;">
                                                    <tr class="titlerow">
                                                        <td colspan="15" style="text-align:left; padding:2px 5px; border-right:none;">
                                                            <b>Division : <span style="text-transform:uppercase;">
                                                                    <?= $value['Division']['name'] ?>
                                                                </span></b>
                                                        </td>
                                                    </tr>

                                                    <?php
                                                    foreach ($value['District'] as $dis_val) {
                                                        $district_id = $dis_val['id'];
                                                    ?>
                                                        <tr class="data_row">
                                                            <td style="text-align:left;">
                                                                <div style="width:100px;"><?= $dis_val['name'] ?></div>
                                                            </td>

                                                            <?php
                                                            $f_t_qty = 0;
                                                            $f_t_val = 0;
                                                            foreach ($categories_products as $c_value) {
                                                                $t_qty = 0;
                                                                $t_val = 0;
                                                                if ($c_value['Product']) {
                                                                    foreach ($c_value['Product'] as $p_value) {
                                                                        $product_id = $p_value['id'];

                                                                        //$sales_data = $DistDistribuReportsController->getProductSales($request_data, $district_id, $product_id, 0);
                                                                        //$sales_qty = $sales_data[0][0]['volume']?$sales_data[0][0]['volume']:'0.00';

                                                                        $sales_qty = @$datas[$district_id][$product_id]['volume'] ? @$datas[$district_id][$product_id]['volume'] : '0.00';

                                                                        $t_qty += $sales_qty;
                                                                        //$t_val+=$sales_data['val']?$sales_data['val']:'0.00';
                                                            ?>
                                                                        <td class="rowDataSd">
                                                                            <div class="qty_val qty_val_<?= $division_id ?>"><?= $sales_qty ?></div>
                                                                        </td>
                                                                    <?php } ?>

                                                                    <td class="rowDataSd" style="background:#CCF;">
                                                                        <div class="qty_val qty_val_<?= $division_id ?>"><?= sprintf("%01.2f", $t_qty) ?></div>
                                                                    </td>

                                                            <?php
                                                                    $f_t_qty += $t_qty;
                                                                }
                                                                //$f_t_val+=$t_val;
                                                            }
                                                            ?>

                                                            <td class="rowDataSd">
                                                                <div class="qty_val qty_val_<?= $division_id ?>"><?= sprintf("%01.2f", $f_t_qty) ?></div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>

                                                    <tr class="totalColumn" style="font-weight:bold;">
                                                        <td style="text-align:right;">Total:</td>

                                                        <?php foreach ($categories_products as $c_value) { ?>

                                                            <?php if ($c_value['Product']) { ?>

                                                                <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                                    <td class="totalCol">
                                                                        <div class="qty_val"></div>
                                                                    </td>
                                                                <?php } ?>

                                                                <td class="totalCol" style="background:#CCF;">
                                                                    <div class="qty_val"></div>
                                                                </td>

                                                            <?php } ?>

                                                        <?php } ?>

                                                        <td class="totalCol">
                                                            <div class="qty_val"></div>
                                                        </td>
                                                    </tr>

                                                </table>

                                                <script>
                                                    <?php
                                                    $total_c_p = count($categories_products) + $total_products;
                                                    $total_v = '0';
                                                    for ($i = 0; $i < $total_c_p; $i++) {
                                                        $total_v .= ',0';
                                                    }
                                                    ?>
                                                    //alert('<?= $total_v ?>');
                                                    var totals_qty<?= $division_id ?> = [<?= $total_v ?>];
                                                    //var totals_val = [<?= $total_v ?>];

                                                    $(document).ready(function() {

                                                        var $dataRows<?= $division_id ?> = $("#division_table<?= $division_id ?> tr:not('.totalColumn, .titlerow')");
                                                        //var val = 0;

                                                        $dataRows<?= $division_id ?>.each(function() {
                                                            $(this).find(".qty_val").each(function(i) {
                                                                val = $(this).html() ? $(this).html() : 0;

                                                                totals_qty<?= $division_id ?>[i] += parseFloat(val);
                                                            });
                                                        });

                                                        $("#division_table<?= $division_id ?> .totalCol .qty_val").each(function(i) {
                                                            $(this).html(totals_qty<?= $division_id ?>[i].toFixed(2));
                                                        });

                                                    });
                                                </script>

                                            <?php } ?>



                                            <h3 style="margin-bottom:5px; font-size:18px;">National</h3>
                                            <table id="sum_table2" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                                <tr class="titlerow">
                                                    <th>
                                                        <div class="qty_val" style="text-align:left;">Division</div>
                                                    </th>

                                                    <?php
                                                    $total_products = 0;
                                                    foreach ($categories_products as $c_value) {
                                                        $total_products += count($c_value['Product']);
                                                    ?>

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
                                                        <div class="qty_val">Total Sales</div>

                                                    </th>

                                                </tr>


                                                <?php foreach ($divisions_3 as $key => $value) { ?>
                                                    <tr>
                                                        <td style="width:50px; font-size:85%; text-align:left;"><?= str_replace('Sales Office', '', $value) ?></td>

                                                        <?php
                                                        $f_t_qty = 0;
                                                        $f_t_val = 0;
                                                        foreach ($categories_products as $c_value) {
                                                            $t_qty = 0;
                                                            $t_val = 0;
                                                            if ($c_value['Product']) {
                                                                foreach ($c_value['Product'] as $p_value) {
                                                                    $product_id = $p_value['id'];
                                                                    $division_id = $key;
                                                                    //$sales_data = $DistDistribuReportsController->getProductSales($request_data, 0, $product_id, $division_id);
                                                                    $sales_qty = @$datas_2[$division_id][$product_id]['volume'] ? @$datas_2[$division_id][$product_id]['volume'] : '0.00';
                                                                    $t_qty += @$sales_qty;
                                                                    //$t_val+=$sales_data['val']?$sales_data['val']:'0.00';
                                                        ?>
                                                                    <td class="rowDataSd">
                                                                        <div class="qty_val"><?= @$sales_qty ?></div>
                                                                    </td>
                                                                <?php } ?>

                                                                <td class="rowDataSd" style="background:#CCF;">
                                                                    <div class="qty_val"><?= sprintf("%01.2f", $t_qty) ?></div>
                                                                </td>

                                                        <?php
                                                                $f_t_qty += $t_qty;
                                                                //$f_t_val+=$t_val;
                                                            }
                                                        }
                                                        ?>

                                                        <td class="rowDataSd">
                                                            <div class="qty_val"><?= sprintf("%01.2f", $f_t_qty) ?></div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>

                                                <tr class="totalColumn" style="font-weight:bold;">
                                                    <td style="text-align:right;">Total:</td>

                                                    <?php foreach ($categories_products as $c_value) { ?>

                                                        <?php if ($c_value['Product']) { ?>

                                                            <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                                <td class="totalCol">
                                                                    <div class="qty_val">
                                                                    </div>
                                                                </td>
                                                            <?php } ?>

                                                            <td class="totalCol" style="background:#CCF;">
                                                                <div class="qty_val">
                                                                </div>
                                                            </td>

                                                        <?php } ?>

                                                    <?php } ?>

                                                    <td class="totalCol">
                                                        <div class="qty_val">
                                                        </div>
                                                    </td>
                                                </tr>

                                            </table>

                                            <script>
                                                <?php
                                                $total_c_p = count($categories_products) + $total_products;
                                                $total_v = '0';
                                                for ($i = 0; $i < $total_c_p; $i++) {
                                                    $total_v .= ',0';
                                                }
                                                ?>
                                                //alert('<?= $total_v ?>');
                                                var totals_qtyN = [<?= $total_v ?>];
                                                //var totals_val = [<?= $total_v ?>];

                                                $(document).ready(function() {

                                                    var $dataRowsN = $("#sum_table2 tr:not('.totalColumn, .titlerow')");

                                                    $dataRowsN.each(function() {
                                                        $(this).find('.qty_val').each(function(i) {
                                                            valN = $(this).html() ? $(this).html() : 0;
                                                            totals_qtyN[i] += parseFloat(valN);
                                                        });
                                                    });

                                                    $("#sum_table2 .totalCol .qty_val").each(function(i) {
                                                        $(this).html(totals_qtyN[i].toFixed(2));
                                                    });

                                                });
                                            </script>
                                        </div>

                                    </div>



                                    <?php /*?><div style="float:left; width:100%; padding:100px 0 50px;">
                                    <div class="bottom_box">
                                        Prepared by:______________ 
                                    </div>
                                    <div class="bottom_box">
                                        Checked by:______________ 
                                    </div>
                                    <div class="bottom_box">
                                        Signed by:______________
                                    </div>		  
                                </div><?php */ ?>



                                </div>


                            </div>

                        </div>
                    <?php } ?>

                </div>
            </div>

        </div>
    </div>

</div>

<?php /*?><script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css">
    <script type="text/javascript">
    $(function() {
        $('.date-picker').datepicker( {
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'MM yy',
            onClose: function(dateText, inst) { 
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            },
            beforeShow : function(input, inst) {
                var datestr;
                if ((datestr = $(this).val()).length > 0) {
                    year = datestr.substring(datestr.length-4, datestr.length);
                    month = jQuery.inArray(datestr.substring(0, datestr.length-5), $(this).datepicker('option', 'monthNamesShort'));
                    $(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
                    $(this).datepicker('setDate', new Date(year, month, 1));
                }
            }
        });
    });
    </script>
    <style>
    .ui-datepicker-calendar {
        display: none;
        }
    </style><?php */ ?>

<script>
    $(document).ready(function() {
        $('[name="data[search][product_categories_id][]"]').change(function() {
            //alert($(this).val()); // alert value
            //$('.selection').find('input:checkbox').prop('checked', checked);
            //alert(111);
            productBoxList();
        });
    });

    function productBoxList() {
        var val = [];
        $('[name="data[search][product_categories_id][]"]:checked').each(function(i) {
            val[i] = $(this).val();
        });

        //alert(val);

        $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL; ?>dist_distribu_reports/get_product_list',
            data: 'product_categories_id=' + val,
            cache: false,
            success: function(response) {
                //alert(response);						
                $('.selection1').html(response);
            }
        });
    }
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

<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=600,width=960');

        mywindow.document.write('<html><head><title></title><?php echo $this->Html->css('bootstrap.min.css');
                                                            echo $this->fetch('css'); ?>');
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