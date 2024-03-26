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
                    <?php echo $this->Form->create('StockOpeningReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('day', array('id' => 'day','class' => 'form-control datepicker date_from', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?>
                            </td>

                            <td  width="50%"><?php echo $this->Form->input('product_type_id', array('label' => 'Product Type :', 'id' => 'product_type_id', 'class' => 'form-control product_type_id',  'empty' => '---- All ----', 'options' => $product_types)); ?></td>
                        </tr>

                        <tr>
                               
                                
                                <td  width="50%">
                                    <?php echo $this->Form->input('product_category_id', array('label' => 'Product Category :', 'id' => 'product_category_id', 'class' => 'form-control product_category_id',  'empty' => '---- All ----', 'options' => $product_categories)); ?>
                                </td>
                                <td  width="50%">
                                     <?php echo $this->Form->input('product_id', array('label' => 'Product Name :', 'id' => 'product_id', 'class' => 'form-control product_id',  'empty' => '---- All ----', 'options' => $products)); ?> 
                                    
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

                                    <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                                        <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>


                                        <h3 style="margin:2px 0;"><?= $page_title ?></h3>

                                        

                                        <p>
                                            <?php if (!empty($request_data['StockOpeningReports']['day']) && $request_data['StockOpeningReports']['day']) { ?>
                                                <span> <b>Opening stock:</b> <?= $request_data['StockOpeningReports']['day'] ?></span>
                                            <?php } ?>
                                            <?php if (!empty($request_data['StockOpeningReports']['product_type_id']) && $request_data['StockOpeningReports']['product_type_id']) { ?>
                                                <span>, <b>Product Type:</b> <?= $product_types[$request_data['StockOpeningReports']['product_type_id']] ?></span>
                                            <?php } ?>
                                            <?php if (!empty($request_data['StockOpeningReports']['product_category_id']) && $request_data['StockOpeningReports']['product_category_id']) { ?>
                                                <span>, <b>Product Category:</b> <?= $product_categories[$request_data['StockOpeningReports']['product_category_id']] ?></span>
                                            <?php } ?>
                                            <?php if (!empty($request_data['StockOpeningReports']['product_id']) && $request_data['StockOpeningReports']['product_id']) { ?>
                                                <span>, <b>Product Name:</b> <?= $products[$request_data['StockOpeningReports']['product_id']] ?></span>
                                            <?php } ?>
                                        </p>
                                        
                                    </div>


                                    <div style="float:left; width:100%; height:430px; overflow:scroll;">

                                        <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                            <thead>
                                                <tr class="titlerow">
                                                    

                                                    <th >Name of Area</th>
                                                    <th >Stock at Area Office</th>
                                                    <th >Stock at SO base</th>
                                                    <th >Total</th>

                                                    
                                                </tr>

                                            </thead>
                                            <?php 
                                            $total_tso_stock=0;
                                            $total_so_stock=0;

                                             ?>
                                            
                                            <?php foreach ($office_array as $key => $office) { ?>
                                                <tr>
                                                    <td><?= $office['name']; ?></td>
                                                    <td><?=$result_array[$office['id']]['tso_opening'];?></td>
                                                    <td><?=$result_array[$office['id']]['so_opening'];?></td>
                                                    <td><?=$result_array[$office['id']]['tso_opening']+$result_array[$office['id']]['so_opening']?></td>
                                                    
                                                </tr>

                                            <?php 
                                                $total_tso_stock += $result_array[$office['id']]['tso_opening'];
                                                $total_so_stock += $result_array[$office['id']]['so_opening'];
                                             } ?>

                                            <tr class="titlerow">
                                                <td>Total</td>
                                                <td><?= $total_tso_stock;?></td>
                                                <td><?= $total_so_stock;?></td>
                                                <td><?= $total_tso_stock+$total_so_stock ?></td>
                                            </tr>
                                                    



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

                             
                                <?php  } else { ?>
                                    
                            <?php } ?>
                            

                        </div>

                    </div>

               

            </div>
        </div>
    </div>
</div>

<script>
    
    // $('.product_type_id').selectChain({
        
    //     target: $('.product_category_id'),
    //     value: 'name',
    //     url: '<?= BASE_URL . 'StockOpeningReports/get_product_category' ?>',
    //     type: 'post',
    //     data: {
    //         'product_type_id': 'product_type_id',
    //         'day':'day'
    //     }
    // });

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
</script>
<script>
    $('#product_type_id').change(function() {
        //alert($(this).val());
        day = $('#day').val();
        $('#product_id').html('<option value="">---- All -----</option>');
        
        if (day) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>StockOpeningReports/get_product_category',
                data: 'product_type_id=' + $(this).val() + '&day=' + day,
                cache: false,
                success: function(response) {
                    //alert(response);						
                    $('#product_category_id').html(response);
                }
            });
        } else {
            $('#day option:nth-child(1)').prop("selected", true);
            alert('Please select date!');
        }
    });

    $('#product_category_id').change(function() {
        //alert($(this).val());
            
        
        
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>StockOpeningReports/get_product_list',
                data: 'product_category_id=' + $(this).val(),
                cache: false,
                success: function(response) {
                    //alert(response);						
                    $('#product_id').html(response);
                }
            });
        
    });
</script>

