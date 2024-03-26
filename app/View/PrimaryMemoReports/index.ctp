<?php
App::import('Controller', 'PrimaryMemoReportsController');
$PrimaryMemoReportsController = new PrimaryMemoReportsController;
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
                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('product_type', array('legend' => 'Product Type :', 'class' => 'product_type', 'type' => 'radio', 'default' => '1', 'options' => $product_type_list, 'required' => true));  ?>
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

                                <p>Measuring Unit : <?= $unit_type_text ?> Unit</p>
                            </div>

                            <?php if (@$product_quantity) { ?>
                                <!-- product quantity get-->
                                <?php
                                $product_qnty = array();
                                $product_price = array();
                                //pr($product_quantity);
                                foreach ($product_quantity as $data) {
                                    $product_qnty[$data['0']['sales_person_id']][$data['0']['product_id']] = $data['0']['pro_quantity'];
                                    $product_price[$data['0']['sales_person_id']][$data['0']['product_id']] = $data['0']['price'];
                                }
                                //pr($product_cyp);
                                //exit;
                                ?>


                                <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                    <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                        <tr class="titlerow">
                                            <th>
                                                <div class="qty_val" style="width:140px;">Sales Officer</div>
                                            </th>
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
                                        foreach ($so_sales_results as $so_id => $data) {
                                            //$so_id = $data_s['0']['sales_person_id'];
                                        ?>
                                            <tr class="rowDataSd">

                                                <td><?= $all_so_list[$so_id] ?></td>

                                                <?php
                                                $total_sales = 0;
                                                $total_cyp = 0;
                                                foreach ($categories_products as $c_value) {
                                                    $total_pro_qty = 0;
                                                    $total_pro_price = 0;
                                                    $total_pro_cyp = 0;
                                                    if ($c_value['Product']) {
                                                        foreach ($c_value['Product'] as $p_value) {
                                                            $pro_id = $p_value['id'];

                                                            $pro_qty = isset($product_qnty[$so_id][$pro_id]) ? $product_qnty[$so_id][$pro_id] : '0.00';
                                                            $base_qty = $SalesReportsController->unit_convert($pro_id, $product_measurement[$pro_id], $pro_qty);

                                                            $pro_qty = ($unit_type == 1) ? $pro_qty : $base_qty;

                                                            $total_pro_qty += $pro_qty;

                                                            $pro_price = isset($product_price[$so_id][$pro_id]) ? $product_price[$so_id][$pro_id] : '0.00';
                                                            $total_pro_price += $pro_price;


                                                            //FOR CYP
                                                            $pro_cyp_v = isset($product_cyp_v[$so_id][$pro_id]) ? $product_cyp_v[$so_id][$pro_id] : '0';

                                                            $pro_cyp = isset($product_cyp[$so_id][$pro_id]) ? $product_cyp[$so_id][$pro_id] : '';
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
                                                                <?php /*?><?=$pro_price?><br>
                                            <?=$pro_cyp_v?><br><?php */ ?>
                                                                <?php /*?><?=$pro_cyp?><br><?php */ ?>
                                                                <?php /*?><?=$pro_cyp_t?><?php */ ?>
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

                                                <td class="qty">
                                                    <?php
                                                    $data = $SalesReportsController->getECTotal($request_data, $so_id, $office_id);
                                                    if ($data)
                                                        echo $data;
                                                    else
                                                        echo 0;
                                                    ?>
                                                </td>
                                                <td class="qty">
                                                    <?php
                                                    $data = $SalesReportsController->getOCTotal($request_data, $so_id, $office_id);
                                                    if ($data)
                                                        echo $data;
                                                    else
                                                        echo 0;
                                                    ?>
                                                </td>

                                            </tr>
                                        <?php } ?>


                                        <tr class="totalColumn">
                                            <td><b>Total:</b></td>

                                            <?php foreach ($categories_products as $c_value) { ?>
                                                <?php if ($c_value['Product']) { ?>
                                                    <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                        <td class="totalQty"></td>
                                                    <?php } ?>
                                                    <td style="background:#CCF;" class="totalQty"></td>
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
                                    <?php foreach ($c_value['Product'] as $p_value) { ?>
                                        <?php $total_col .= ',0'; ?>
                                    <?php } ?>
                                    <?php $total_col .= ',0'; ?>
                                <?php } ?>
                                <?php
                                $total_col .= ',0';
                                $total_col .= ',0';
                                $total_col .= ',0';
                                $total_col .= ',0';
                                ?>

                                <script>
                                    var totals_qty = [<?= $total_col ?>];
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


                        <!--<div style="float:left; width:100%; padding-top:100px;">
                        <div style="width:33%;text-align:left;float:left">
                            Prepared by:______________ 
                        </div>
                        <div style="width:33%;text-align:center;float:left">
                            Checked by:______________ 
                        </div>
                        <div style="width:33%;text-align:right;float:left">
                            Signed by:______________
                        </div>        
                    </div>-->

                    </div>

                </div>
            <?php } ?>


        </div>
    </div>
</div>

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
            console.log(product_type);
            get_product_list(product_type);
        });
        var product_check = <?php echo @json_encode($this->request->data['Memo']['product_id']); ?>;
        console.log(product_check);

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