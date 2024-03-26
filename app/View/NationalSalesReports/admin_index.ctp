<?php
App::import('Controller', 'NationalSalesReportsController');
$NationalSalesController = new NationalSalesReportsController;
?>
<style>
    .search .radio label {
        width: auto;
        float: none;
        padding-left: 5px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 30%;
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
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">



            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('National Sales Volume and Value Report'); ?></h3>
                <?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Product Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
            </div>


            <div class="box-body">

                <div class="search-box">
                    <?php echo $this->Form->create('NationalSalesReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <tr>
                            <td width="50%" class="required"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true)); ?></td>

                            <td width="50%" class="required"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true)); ?></td>
                        </tr>


                        <tr>
                            <td><?php echo $this->Form->input('outlet_type', array('legend' => 'Outlet Type :', 'class' => 'outlet_type', 'type' => 'radio', 'default' => 1,  'options' => $outlet_type_list, 'required' => true));  ?></td>
                            <td></td>

                            <?php /*?><td id="td_product_categories"><?php echo $this->Form->input('product_categories_id', array('id' => 'product_category_id', 'label'=>false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'required' => true)); ?></td><?php */ ?>
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
                                        <?php echo $this->Form->input('product_categories_id', array('id' => 'product_category_id', 'label' => false, 'class' => 'checkbox simple', 'multiple' => 'checkbox', 'required' => true)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>


                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Area Office : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall1" />
                                        <label for="checkall1" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection1">
                                        <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'required' => true, 'options' => $offices)); ?>
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
                </div>

                <script>
                    //$(input[type='checkbox']).iCheck(false); 
                    $(document).ready(function() {
                        $("input[type='checkbox']").iCheck('destroy');
                        $('#checkall').click(function() {
                            var checked = $(this).prop('checked');
                            $('.selection').find('input:checkbox').prop('checked', checked);
                        });
                        $('#checkall1').click(function() {
                            var checked = $(this).prop('checked');
                            $('.selection1').find('input:checkbox').prop('checked', checked);
                        });
                    });
                </script>


                <?php if (!empty($request_data)) { ?>
                    <div id="content" style="width:90%; margin:0 5%;">

                        


                        <div class="table-responsive">

                            <div class="pull-right csv_btn" style="padding-top:20px;">
                                <?php //$this->Html->link(__('Download XLS'), array('action' => 'dwonload_xls?data=' . serialize($request_data)), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                                <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                            </div>
                            <div id='xls_body'> <!--  download xl div start -->
                            <style type="text/css">
                            .table-responsive {
                                color: #333;
                                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                                line-height: 1.42857;
                            }

                            .report_table {
                                font-size: 12px;
                            }

                            /* .qty_val {
                                width: 125px;
                                margin: 0;
                                float: left;
                                text-transform: capitalize;
                            } */

                            /* .qty,
                            .val {
                                width: 49%;
                                float: left;
                                border-right: #333 solid 1px;
                                text-align: center;
                                padding: 5px 0;
                            }

                            .val {
                                border-right: none;
                            } */

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
                            <div style="width:100%; text-align:center; padding:20px 0;">
                                <h2 style="margin:2px 0;">Social Marketing Company</h2>
                                <h3 style="margin:2px 0;">National Sales Volume and Value by Brand</h3>
                                <p>
                                    Time Frame : <b><?= date('d M, Y', strtotime($date_from)) ?></b> to <b><?= date('d M, Y', strtotime($date_to)) ?></b> <?php /*?><br>Reporting Date: <b><?php echo date('d F, Y')?></b><?php */ ?>
                                </p>
                                <p>Print Unit : Sale Unit</p>
                            </div>

                            <div style="float:left; width:100%; height:430px; overflow:scroll;">
                                <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                    <tr class="titlerow">
                                        <th rowspan="2">Sales Area</th>

                                        <?php
                                        $total_products = 0;
                                        foreach ($categories_products as $c_value) {
                                            
                                            $total_products += count($c_value['Product']);
                                        ?>

                                            <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                <th colspan="2">
                                                    <div class="qty_val" ><?= $p_value['name'] ?></div>
                                                   
                                                </th>
                                            <?php } ?>

                                            <th style="background:#CCF;" colspan="2">
                                                <div class="qty_val" >Total <?= $c_value['ProductCategory']['name'] ?></div>
                                                
                                            </th>

                                        <?php } ?>

                                        <th colspan="2">
                                            <div class="qty_val" >Total Sales</div>
                                            
                                        </th>

                                    </tr>
                                    
                                    <tr class="titlerow">

                                        <?php
                                        foreach ($categories_products as $c_value) {
                                        ?>

                                            <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                <th class="qty_val" style="padding:5px; ">
                                                        <div class="qty"><small>Qty</small></div>
                                                </th>
                                                <th class="qty_val" style="padding:5px; ">
                                                        <div class="val"><small>Val</small></div>
                                                </th>
                                            <?php } ?>

                                            <th style="background:#CCF; padding:5px; " class="qty_val">
                                                    <div class="qty"><small>Qty</small></div>
                                            </th>
                                            <th style="background:#CCF; padding:5px; " class="qty_val">
                                                    <div class="val"><small>Val</small></div>
                                            </th>

                                        <?php } ?>

                                        <th class="qty_val" style="padding:5px; ">
                                                <div class="qty"><small>Qty</small></div>
                                        </th>
                                        <th class="qty_val" style="padding:5px; ">
                                                <div class="val"><small>Val</small></div>
                                        </th>

                                    </tr>

                                    <?php
                                    foreach ($offices as $key => $value) {
                                        if (in_array($key, $request_data['NationalSalesReports']['office_id'])) {
                                    ?>
                                            <tr>
                                                <td style="width:50px; font-size:85%;"><?= str_replace('Sales Office', '', $value) ?></td>

                                                <?php
                                                $f_t_qty = 0;
                                                $f_t_val = 0;
                                                foreach ($categories_products as $c_value) {
                                                    $t_qty = 0;
                                                    $t_val = 0;
                                                    $office_id = $key;
                                                    foreach ($c_value['Product'] as $p_value) {
                                                        $product_id = $p_value['id'];

                                                        /*$sales_data = $NationalSalesController->getProductSales($office_id, $date_from, $date_to, $product_id);
                                             //pr($sales_data);
                                             // exit;
                                             $t_qty+=$sales_data[0][0]['qty']?$sales_data[0][0]['qty']:'0.00';
                                             $t_val+=$sales_data[0][0]['val']?$sales_data[0][0]['val']:'0.00';*/
                                                        $qty = @$q_data[$office_id][$product_id]['qty'] ? $q_data[$office_id][$product_id]['qty'] : '0.00';
                                                        $val = @$q_data[$office_id][$product_id]['val'] ? $q_data[$office_id][$product_id]['val'] : '0.00';
                                                        $t_qty += $qty;
                                                        $t_val += $val;
                                                ?>
                                                        <td class="rowDataSd qty_val" style="padding:5px; ">
                                                                <div class="qty"><?= $qty ?></div>
                                                            
                                                        </td>
                                                        <td class="rowDataSd qty_val" style="padding:5px; ">
                                                                <div class="val"><?= $val ?></div>
                                                            
                                                        </td>
                                                    <?php } ?>

                                                    <td class="rowDataSd qty_val" style="background:#CCF;padding:5px; ">
                                                        
                                                            <div class="qty"><?= $t_qty ?></div>
                                                        
                                                    </td>
                                                    <td class="rowDataSd qty_val" style="background:#CCF;padding:5px; ">
                                                        
                                                            
                                                            <div class="val"><?= $t_val ?></div>
                                                        
                                                    </td>

                                                <?php
                                                    $f_t_qty += $t_qty;
                                                    $f_t_val += $t_val;
                                                }
                                                ?>

                                                <td class="rowDataSd qty_val" style="padding:5px; ">
                                                        <div class="qty"><?= $f_t_qty ?></div>
                                                    
                                                </td>
                                                <td class="rowDataSd qty_val" style="padding:5px; ">
                                                        <div class="val"><?= $f_t_val ?></div>
                                                    
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>

                                    <tr class="totalColumn">
                                        <td style="padding:5px; "><b>Total:</b></td>

                                        <?php foreach ($categories_products as $c_value) { ?>

                                            <?php foreach ($c_value['Product'] as $p_value) { ?>
                                                <td class="totalCol qty_val" style="padding:5px; ">
                                                        <div class="qty"></div>
                                                    
                                                </td>
                                                <td class="totalCol qty_val" style="padding:5px; ">
                                                        <div class="val"></div>
                                                    
                                                </td>
                                            <?php } ?>

                                            <td class="totalCol qty_val" style="background:#CCF;padding:5px; ">
                                                
                                                    <div class="qty"></div>
                                                
                                            </td>
                                            
                                            <td class="totalCol qty_val" style="background:#CCF;padding:5px; ">
                                                
                                                    <div class="val"></div>
                                                
                                            </td>

                                        <?php } ?>

                                        <td class="totalCol qty_val" style="padding:5px; ">
                                            <div class="qty"></div>
                                        </td>
                                        <td class="totalCol qty_val" style="padding:5px; ">
                                            <div class="val"></div>
                                        </td>
                                    </tr>



                                </table>
                            </div>
                            



                            <script>
                                <?php
                                
                                $total_c_p = count($categories_products) + $total_products;
                                // echo $total_c_p;exit;
                                $total_v = '0';
                                for ($i = 0; $i < $total_c_p; $i++) {
                                    $total_v .= ',0';
                                }
                                ?>
                                //alert('<?= $total_v ?>');
                                var totals_qty = [<?= $total_v ?>];
                                var totals_val = [<?= $total_v ?>];
                                $(document).ready(function() {

                                    var $dataRows = $("#sum_table tr:not('.totalColumn, .titlerow')");


                                    $dataRows.each(function() {
                                        $(this).find('.rowDataSd .qty').each(function(i) {
                                            totals_qty[i] += parseFloat($(this).html());
                                        });
                                    });
                                    $("#sum_table td.totalCol .qty").each(function(i) {
                                        $(this).html(totals_qty[i].toFixed(2));
                                    });


                                    $dataRows.each(function() {
                                        $(this).find('.rowDataSd .val').each(function(i) {
                                            totals_val[i] += parseFloat($(this).html());
                                        });
                                    });
                                    $("#sum_table td.totalCol .val").each(function(i) {
                                        $(this).html(totals_val[i].toFixed(2));
                                    });


                                });
                            </script>


                            <div style="float:left; width:100%; padding:100px 0 50px;">
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
                            </div> <!--  download xl div end -->
                        </div>

                    </div>
                <?php } ?>







            </div>
        </div>
    </div>
</div>



<script>
    /*$('.outlet_type').on('ifChecked', function(event){
	//alert($(this).val()); // alert value
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL; ?>NationalSalesReports/get_category_list',
		data: 'outlet_type='+$(this).val(),
		cache: false, 
		success: function(response){
			//alert(response);						
			$('.td_product_categories').html(response);				
		}
	});
});*/
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
    $(document).ready(function() {
        $("#download_xl").click(function(e) {

            e.preventDefault();

            var html = $("#xls_body").html();

            console.log(html);

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "National_Sales_Volume_and_Value_Report.xls";

            document.body.appendChild(a);

            a.click();

        });
    });
</script>