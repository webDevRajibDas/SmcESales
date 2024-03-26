<?php
App::import('Controller', 'PrimaryMemoReportsController');
$PrimaryMemoReportsController = new PrimaryMemoReportsController;
?>
<style>
    .search .radio label {
        width: auto;
        float:none;
        padding:0px 15px 0px 5px;
        margin:0px;
    }
    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 15%;
        display: inline-block;
        font-weight: 700;
        font-size:14px;
        border-bottom:none;
    }
    .radio input[type="radio"], .radio-inline input[type="radio"]{
        margin-left: 0px;
        position: relative;
        margin-top:8px;
    }

    #market_list .checkbox label{
        padding-left:10px;
        width:auto;
    }
    #market_list .checkbox{
        width:33%;
        float:left;
        margin:1px 0;
    }

</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?=$page_title; ?></h3>
            </div>  
            <div class="box-body">
                <div class="search-box">
                <?php echo $this->Form->create('PrimaryMemoReports', array('role' => 'form')); ?>
                    <table class="search">
                        <tr>
                            <td class="required">
                            <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','readonly' => true,'required'=>true)); ?></td>
                            <td class="required">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','readonly' => true,'required'=>true)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('product_type', array('legend'=>'Product Type :', 'class' => 'product_type', 'type' => 'radio', 'default' => '1', 'options' => $product_type_list, 'required'=>true));  ?>
                            </td>
                        </tr>
                        <!--tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Products : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                    <div style="margin:auto; width:90%; float:left;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                        <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="product selection" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto">
                                        <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $product_list)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr-->
                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div'=>false, 'name'=>'submit')); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                <?php if(!empty($requested_data)){ ?>
                                <button onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                                <?php } ?>   
                            </td>                       
                        </tr>
                    </table>    
                    <?php echo $this->Form->end(); ?>
                </div>
            <!--*create report table-*-->
            <?php if(!empty($requested_data)){ ?>
                <div id="content" style="width:90%; margin:0 5%;">
                    <style type="text/css">
                       .table-responsive { color: #333; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; line-height: 1.42857; }
                       .report_table{ font-size:12px; }
                       .qty_val{ width:125px; margin:0; float:left; text-transform:capitalize;}
                       .val{ border-right:none; }
                       p{ margin:2px 0px; }
                       .bottom_box{ float:left; width:33.3%; text-align:center; }
                       td, th {padding: 5px;}
                       table { border-collapse: collapse; border-spacing: 0; }
                       .titlerow, .totalColumn{ background:#f1f1f1; }
                       .report_table {margin-bottom: 18px; max-width: 100%; width: 100%;}
                       .table-responsive {min-height: 0.01%;overflow-x: auto;}
                   </style>
                   <div class="table-responsive">
                        <div style="width:100%; text-align:center; padding:20px 0;">
                            <h2 style="margin:2px 0;">SMC Enterprise Ltd. </h2>
                            <h3 style="margin:2px 0;">Primary Memo Reports</h3>
                            <p>
                                Time Frame : <b><?=date('d M, Y', strtotime($date_from))?></b> to <b><?=date('d M, Y', strtotime($date_to))?></b>
                            </p>
                            <p>
                               Challan No : <b><?=$startchallan['PrimaryMemo']['challan_no'];?></b> to <b><?=$endchallasn['PrimaryMemo']['challan_no'];?></b>
                            </p>
                        </div>   
                        <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
                            <tr class="titlerow">
                                <th>Product Name</th>
                                <th style="text-align:center;">Product Qty</th>
                                <th width="100" style="text-align:center;">Number of Challans</th>
                                <th style="text-align:center;">Base Value</th>
                                <th style="text-align:center;">Vat</th>
                                <th style="text-align:center;">Total Value</th>
                            </tr>
                            <tbody>
                                <?php
                                    $serial=1;
                                    $total_value = 0;
                                    $total_vat = 0;
                                    $total_basevalue = 0;
                                    $pinfo = '';
                                error_reporting(0);
                                foreach ($products as $key => $value): 
                                    $pinfo = $p_data[$key];                
                                    ?>
                                <tr>
                                    <td style="text-align: left;">
                                        <?php echo h($value);  ?>
                                    </td> 
                                    <td align="center">
                                        <?php 
                                           if(!empty($pinfo)){
                                            echo $pinfo['challan_qty'] + 0;
                                          }else{
                                            echo "0";
                                          }  
                                        ?>                                        
                                    </td>
                                    <td align="center">
                                        <?php 
                                          if(!empty($pinfo)){
                                            echo $pinfo['total_challan_no'];
                                          }else{
                                            echo "0";
                                          }
                                        ?>                                       
                                    </td>
                                    <td align="right" style="text-align: right;">
                                    <?php 
                                          if(!empty($pinfo)){
                                            //echo $pinfo['basevalue'];
                                            echo sprintf('%0.2f', $pinfo['basevalue']);
                                          }else{
                                            echo "0";
                                          }
                                        ?>  
                                    </td>
                                    <td align="right" style="text-align: right;">
                                    <?php 
                                          if(!empty($pinfo)){
                                            //echo $pinfo['vat'];
                                            echo sprintf('%0.2f', $pinfo['vat']);
                                          }else{
                                            echo "0";
                                          }
                                        ?>  
                                    </td>
                                    <td align="right" style="text-align: right;">
                                        <?php 
                                            if(!empty($pinfo)){
                                            //echo $pinfo['value'];
                                            echo sprintf('%0.2f', $pinfo['value']);
                                          }else{
                                            echo "0";
                                          }      
                                        ?>    
                                    </td>   
                                </tr>
                                  <?php
                                    $total=$pinfo['value'];
                                    $challan_qty=$pinfo['challan_qty'];
                                    $total_value =  $total_value + $total;
                                    $total_basevalue +=  $pinfo['basevalue'] ;
                                    $total_vat +=  $pinfo['vat'] ;
                                    $total_product =  $total_product + $challan_qty;
                                    $serial++; 
                                 endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="totalColumn">
                                    <td><b>Total:</b></td>
                                    <td class="totalQty" style="text-align:center;"><?php echo sprintf('%.2f',$total_product); ?> </td>
                                    <td></td>
                                    
                                    <td align="right" style="text-align:right;"><?php echo sprintf('%.2f',$total_basevalue); ?></td>
                                    <td align="right" style="text-align:right;"><?php echo sprintf('%.2f',$total_vat); ?></td>
                                    <td class="totalVal" style="text-align:right;"><?php echo sprintf('%.2f',$total_value); ?></td>
                                </tr>
                            </tfoot>
                        </table>
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
            
            <script>
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
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        get_product_list($(".product_type:checked").serializeArray());
        $(".product_type").change(function(){
            product_type=$(".product_type:checked").serializeArray();
            console.log(product_type);
            get_product_list(product_type);
        });
        var product_check=<?php echo @json_encode($this->request->data['PrimaryMemoReports']['product_id']);?>;
        console.log(product_check);

        function get_product_list(product_type)
        {
            $.ajax({
                type: "POST",
                url: '<?=BASE_URL?>primarymemoreports/get_product_list',
                data: product_type,
                cache: false, 
                success: function(response){
                    $(".product").html(response);
                    if(product_check)
                    {
                        $.each(product_check, function(i, val){
                            $(".product_id>input[value='" + val + "']").prop('checked', true);
                        });
                    }
                }
            });
        }
    });
</script>
<script>
    function PrintElem(elem)
    {
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