<?php
//App::import('Controller', 'SalesReportsController');
//$SalesReportsController = new SalesReportsController;
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

    .pro_label_title {
        width: 92%;
        margin-right: 3%;
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

    td {
        padding: 5px;
    }

    td.left {
        border-right: #c7c7c7 solid 1px;
    }

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 20%;
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
                            <td>
                                <?php echo $this->Form->input('company', array('label' => 'Company', 'class' => 'form-control company_id', 'options' => $companies, 'empty' => '--- Select ----'));
                                ?>
                            </td>
                            <td>
                                <div style="margin-left: 95px;">
                                    <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));
                                    ?>
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

            <?php if ($dataresult == 1) { ?>
                <h2 style="text-align: center;">No Data Found.</h2>
            <?php } else { ?>

                <?php
                if (!empty($request_data)) {
                ?>
                    <div id="content" style="width:90%; margin: 0 5%;">
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

                            .titlerow th div {
                                text-transform: capitalize;
                                min-width: 100px;
                                float: left;
                                position: relative;
                            }

                            .titlerow th {
                                text-align: center;
                            }
                        </style>

                        <div class="table-responsive">
                            <div class="pull-right csv_btn" style="padding-top:20px;">

                                <?php echo $this->Html->link(__('Download XLS'), array('action' => '#'), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                            </div>

                            <div id="xls_body">
                                <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                                    <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
                                    <h3 style="margin:2px 0;"><?= $page_title; ?></h3>
                                    <p>
                                        <b> Time Frame : <?= @date('d M, Y', strtotime($date_from)) ?> to <?= @date('d M, Y', strtotime($date_to)) ?></b>
                                    </p>

                                    <p>Measuring Unit : <?= $unit_types[$unit_type] ?></p>
                                </div>
                                <table class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">SL</th>
                                            <th rowspan="2">Product Code</th>
                                            <th rowspan="2">Product Category</th>
                                            <th rowspan="2">Brand</th>
                                            <th rowspan="2">Name Of Product</th>
                                            <th colspan="3">Issued/Sales Qty</th>
                                            <th colspan="3">Value</th>
                                            <th colspan="3">Bonus Value</th>
                                        </tr>
                                        <tr>
                                            <th>Sales</th>
                                            <th>Bonus/Sample/Gift/CP</th>
                                            <th>Total</th>
                                            <th>Excluding VAT</th>
                                            <th>VAT</th>
                                            <th>Total</th>
                                            <th>Excluding VAT</th>
                                            <th>VAT</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_sales = 0;
                                        $total_bonus = 0;
                                        $total_ex_vat = 0;
                                        $total_vat = 0;
										
										$bonus_total_bonus = 0;
                                        $bonus_total_ex_vat = 0;
                                        $bonus_total_vat = 0;
										
                                        $sl = 1;
                                        ?>
                                        <?php foreach ($dataresult as $data) { ?>
                                            <?php
                                            $sales = 0;
                                            $bonus = 0;
                                            ?>
                                            <tr>
                                                <td><?= $sl++; ?></td>
                                                <td><?= $data['Product']['product_code'] ?></td>
                                                <td><?= $data['Category']['name'] ?></td>
                                                <td><?= $data['Brand']['name'] ?></td>
                                                <td><?= $data['Product']['name'] ?></td>
                                                <td><?= $sales = sprintf('%01.2f', $unit_type == 1 ? $data['ProductData']['sales_qty_sale_unit'] : $data['ProductData']['sales_qty_base_unit']); ?></td>
                                                <td><?= $bonus = sprintf('%01.2f', $unit_type == 1 ? $data['0']['bonus_qty_sale_unit'] : $data['0']['bonus_qty_base_unit']); ?></td>
                                                <td><?= sprintf('%01.2f', $sales + $bonus) ?></td>

                                                <td><?= sprintf('%01.2f', $data['ProductData']['excluding_vat']) ?></td>
                                                <td><?= sprintf('%01.2f', $data['ProductData']['total_vat']) ?></td>
                                                <td><?= sprintf('%01.2f', $data['ProductData']['excluding_vat'] + $data['ProductData']['total_vat'])  ?></td>

                                                <td><?= sprintf('%01.2f', $data['0']['bonus_excluding_vat']) ?></td>
                                                <td><?= sprintf('%01.2f', $data['0']['total_bonus_value'] - $data['0']['bonus_excluding_vat']) ?></td>
                                                <td><?= sprintf('%01.2f', $data['0']['total_bonus_value'])  ?></td>



                                                <?php
                                                $total_sales += $sales;
                                                $total_bonus += $bonus;
                                                $total_ex_vat += sprintf('%01.2f', $data['ProductData']['excluding_vat']);
                                                $total_vat += sprintf('%01.2f', $data['ProductData']['total_vat']);
													
												
												$bonus_total_ex_vat += $data['0']['bonus_excluding_vat'];
												$bonus_total_bonus += $data['0']['total_bonus_value'] - $data['0']['bonus_excluding_vat'];
												$bonus_total_vat += $data['0']['total_bonus_value'];
												
                                                ?>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td colspan="4"> Print Date and Time : <?= date('Y-m-d h:i a') ?></td>
                                            <td>Total : </td>
                                            <td><?= sprintf('%01.2f', $total_sales) ?></td>
                                            <td><?= sprintf('%01.2f', $total_bonus) ?></td>
                                            <td><?= sprintf('%01.2f', $total_sales + $total_bonus) ?></td>
                                            <td><?= sprintf('%01.2f', $total_ex_vat) ?></td>
                                            <td><?= sprintf('%01.2f', $total_vat) ?></td>
                                            <td><?= sprintf('%01.2f', $total_ex_vat + $total_vat) ?></td>
											
                                            <td><?= sprintf('%01.2f', $bonus_total_ex_vat) ?></td>
                                            <td><?= sprintf('%01.2f', $bonus_total_bonus) ?></td>
                                            <td><?= sprintf('%01.2f', $bonus_total_vat) ?></td>
                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            <?php }
            } ?>
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
        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');

        $('.checkall').click(function(e) {
            var checked = $(this).prop('checked');
            $(this).closest('.select').find('.selection').find('input:checkbox').prop('checked', checked);
        });

        $("#download_xl").click(function(e) {

            e.preventDefault();

            $(".downloadborder").attr("border", "1");

            var html = $("#xls_body").html();

            // console.log(html);

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });


            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            console.log(a);

            a.href = downloadUrl;

            a.download = "Vat_reports.xls";

            document.body.appendChild(a);

            a.click();

            $(".downloadborder").removeAttr("border");

        });

    });
</script>