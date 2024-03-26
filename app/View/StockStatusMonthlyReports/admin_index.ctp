
<?php //pr($so_info); ?>


<style>
    .search .radio label {
        width: auto;
        float:none;
        padding-left:5px;
    }
    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 30%;
        display: inline-block;
        font-weight: 700;
        font-size:14px;
        border-bottom:none;
    }
    #market_list .checkbox label{
       padding-left:10px;
       width:auto;
   }
   #market_list .checkbox{
       width:30%;
       float:left;
       margin:1px 0;
   }
</style>



<div class="row">

    <div class="col-xs-12">
        <div class="box box-primary" style="float:left;">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Stock status by SO Reports'); ?></h3>
            </div>

            <div class="box-body" style="float:left; width:100%;">

                <div class="search-box" style="padding-bottom:20px; margin-bottom:30px;">
                    <?php echo $this->Form->create('search', array('role' => 'form')); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('class' => 'form-control','empty'=>'---- Select Office ----','required'=>true)); ?></td>
                            <td width="50%"><?php echo $this->Form->input('unit_type', array('legend'=>'Unit Type :', 'type' => 'radio', 'options' => $unit_type, 'required'=>true)); ?></td>
                        </tr>
                        <tr>
                            <td class="required">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker22','required'=>true)); ?>
                            </td>
                            <td class="required">
                                <?php //echo $this->Form->input('date_to', array('class' => 'form-control datepicker22','required'=>true)); ?>
                            </td>
                        </tr>
                        <tr>
                         <td colspan="2">
                            <label style="float:left; width:15%;">Sales Officers : </label>
                            <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                    <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
                                    <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                </div>
                                <div class="selection2 so_list">
                                    <?php echo $this->Form->input('so_id', array('label'=>false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $so_list)); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Products : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                   <div style="margin:auto; width:90%; float:left;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                        <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                <div class="selection" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto">
                                   <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $product_list)); ?>
                               </div>
                           </div>
                       </td>
                   </tr>

                   <tr align="center">
                    <td colspan="2">
                        <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
                        <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                        <?php if(!empty($Store) && !empty($so_info)){?>
                        <button type="button" onclick="PrintElem('content')" class="btn btn-primary">
                            <i class="glyphicon glyphicon-print"></i> Print
                        </button>
                        <?php }?>
                             <?php if($request_data){ ?>
                             <a class="btn btn-success" id="download_xl">Download XL</a>
                             <?php }?>
                    </td>						
                </tr>
            </table>	
            <?php echo $this->Form->end(); ?>
        </div>     
        <script>
                //$(input[type='checkbox']).iCheck(false); 
                $(document).ready(function() {
                    $("input[type='checkbox']").iCheck('destroy');
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

            <?php if($request_data){ ?>
            <style type="text/css">
                    @media print
                    {
                        #non-printable { display: none; }
                        #content { 
                            display: block;
                        }
                        table{
                            width:100%;
                            font-size: 11px;
                            height: inherit;
                            page-break-after: always;
                        }

                        table, th, td {
                            border: 1px solid black;
                            border-collapse: collapse;
                        }
                        footer{
                            position: fixed;
                            bottom: 0;
                            font-size: 10px;
                        }
                        .footer1{
                            width:100%;
                            height: 100px;
                            position: absolute;
                            font-size: 10px;
                            overflow-y: inherit;
                        }

                        .font_size{
                            font-size: 11px;
                        }
                        .page-break{
                            page-break-after: always;
                        }
                        #heading_name{
                            font-size: 24px;
                        }
                        #heading_add{
                            font-size: 18px;
                        }
                        .page_header{
                            width:100%;
                            font-weight: normal;
                            font-size: 8px;
                            float: right;
                            text-align: right;
                            margin-right: 3%;
        /* position: relative;
        top:0px;normal
        right:0px;
        width:30%;
        font-size: 8px;
        margin-bottom: 10px;*/
    }
    @page {size: auto;  margin: 30px; }
}

</style>
            <div id="content" style="width:96%;height:100%;margin-left:2%;margin-right:2%;">
                
                    <!-- <div style="text-align:right;width:100%;">Page No :1 of 1</div>
                    <div style="text-align:right;width:100%;">Print Date :19 Jul 2017</div> -->
                    
                    <?php if(!empty($Store) && !empty($so_info)){ ?>
                  

                  <div style="float:left; width:100%; padding-bottom:20px;">
                    <div style="width:25%;text-align:left;float:left">
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </div>
                    <div style="width:50%;text-align:center;float:left">
                        <font id="heading_name"><b>SMC Enterprise Limited</b></font><br>
                        <span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
                        <font><b>Stock Status By SO</b></font><br>
                        <font><b>Issueing Office : <?php echo h($offices[$this->request->data['search']['office_id']]); ?></b></font><br>
                        <font><?php if(!empty($this->request->data)){ ?><strong>Between:</strong>&nbsp;&nbsp;<u><?php  echo date('d-F-Y',strtotime($this->request->data['search']['date_from'])); ?>&nbsp;&nbsp;to&nbsp;&nbsp;<?php echo date('t-F-Y',strtotime($this->request->data['search']['date_from'])); }?></u>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Reporting Date:</strong>&nbsp;&nbsp;<?php echo date('d-F-Y');?></font>
                    </div>
                    <div style="width:25%;text-align:right;float:left">
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </div>        
                </div>  

                <div style="float:left; width:100%; height:450px; overflow:scroll;">  
                    <table id="sum_table" style="width:100%;text-align:center; margin-bottom: 50px;" border="1px solid black" cellpadding="0px" cellspacing="0" align="center" class="table table-bordered table-responsive">
                        <thead>
                            <tr class="titlerow">
                                <th>Sales Officer</th>
                                <th></th>
                                <?php
                                foreach ($products as $value) {
                                    ?>
                                    <th class="text-center"><?php echo $value['Product']['name'].'<br>['.$unit_type_text.']';?></th>
                                    <?php 
                                } 
                                ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php 
                            $i=1; 
                            //pr($Store);die;
                            foreach ($Store as $data)
                            { 
                                ?>
                                <tr class="rowDataSd">
                                    <td rowspan="7" ><?=$data['sp']['name'].'<br>('.$data['Territory']['name'].')'?></td>
                                    <td style="text-align:left">Open</td>
                                    <?php
                                    foreach ($products as $value) {
                                        ?>
                                        <td class="qty" id="<?php echo $data['Store']['id'].'_'.$value['Product']['id'].'o';?>">
                                            <?php 
                                            if(!empty($so_info[$data['Store']['id']]['OB']))
                                            {
                                                if(array_key_exists($value['Product']['id'], $so_info[$data['Store']['id']]['OB']))
                                                {
                                                    echo $so_info[$data['Store']['id']]['OB'][$value['Product']['id']];
                                                }
                                                else 
                                                {
                                                    echo '0.00';
                                                }
                                            }
                                            else 
                                            {
                                                echo '0.00';
                                            }
                                            ?>
                                            
                                        </td>
                                        <?php 
                                    } 
                                    ?>
                                </tr>
                                <tr>
                                    <td style="text-align:left">Received</td>
                                    <?php
                                    foreach ($products as $value) {
                                        ?>
                                        <td class="qty" id="<?php echo $data['Store']['id'].'_'.$value['Product']['id'].'r';?>">
                                            <?php 
                                            if(!empty($so_info[$data['Store']['id']]['RCV']))
                                            {
                                                if(array_key_exists($value['Product']['id'], $so_info[$data['Store']['id']]['RCV']))
                                                {
                                                    echo $so_info[$data['Store']['id']]['RCV'][$value['Product']['id']];
                                                }
                                                else
                                                { 
                                                    echo '0.00';
                                                }
                                            }
                                            else 
                                            {
                                                echo '0.00';
                                            }
                                            ?>
                                        </td>
                                        <?php 
                                    } 
                                    ?>
                                </tr>
                                <tr>
                                    <td style="text-align:left">Total</td>
                                    <?php foreach ($products as $value) { ?>
                                    <td class="t_<?=$data["Store"]["id"]?>_<?=$value["Product"]["id"]?>" class="qty"></td>
                                    <script>
                                        // var open=$('#<?php echo $data["Store"]["id"]."_".$value["Product"]["id"]."o";?>').text();
                                        // console.log(open);
                                        var total=parseFloat($('#<?php echo $data["Store"]["id"]."_".$value["Product"]["id"]."o";?>').text())+parseFloat($('#<?php echo $data["Store"]["id"]."_".$value["Product"]["id"]."r";?>').text());
                                            //document.write(parseFloat(total).toFixed(2));
                                            $('.t_<?=$data["Store"]["id"]?>_<?=$value["Product"]["id"]?>').text(parseFloat(total).toFixed(2));
                                        </script>
                                        
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left">Sales</td>
                                        <?php foreach ($products as $value) { ?>
                                        <td class="qty">
                                            <?php 
                                            if(!empty($so_info[$data['Store']['id']]['SQ']))
                                            {
                                                if(array_key_exists($value['Product']['id'], $so_info[$data['Store']['id']]['SQ']))
                                                {
                                                    echo $so_info[$data['Store']['id']]['SQ'][$value['Product']['id']];
                                                }
                                                else 
                                                {
                                                    echo '0.00';
                                                }
                                            }
                                            else 
                                            {
                                                echo '0.00';
                                            }
                                            ?>
                                        </td>
                                        <?php 
                                    } 
                                    ?>
                                </tr>
                                <tr>
                                    <td style="text-align:left">Bonus</td>
                                    <?php
                                    foreach ($products as $value) {
                                        ?>
                                        <td class="qty">
                                            <?php 
                                            if(!empty($so_info[$data['Store']['id']]['BQ']))
                                            {
                                                if(array_key_exists($value['Product']['id'], $so_info[$data['Store']['id']]['BQ']))
                                                {
                                                    echo $so_info[$data['Store']['id']]['BQ'][$value['Product']['id']];
                                                }
                                                else 
                                                {
                                                    echo '0.00';
                                                }
                                            }
                                            else 
                                            {
                                                echo '0.00';
                                            }
                                            ?>
                                        </td>
                                        <?php 
                                    } 
                                    ?>
                                </tr>
                                <tr>
                                    <td style="text-align:left">Returned</td>
                                    <?php
                                    foreach ($products as $value) {
                                        ?>
                                        <td class="qty">
                                            <?php 
                                            if(!empty($so_info[$data['Store']['id']]['RQ']))
                                            {
                                                if(array_key_exists($value['Product']['id'], $so_info[$data['Store']['id']]['RQ'])){
                                                    echo $so_info[$data['Store']['id']]['RQ'][$value['Product']['id']];
                                                }
                                                else 
                                                {
                                                    echo '0.00';
                                                }
                                            }
                                            else 
                                            {
                                                echo '0.00';
                                            }
                                            ?>
                                        </td>
                                        <?php 
                                    } 
                                    ?>
                                </tr>
                                <tr style="border-bottom:#ccc solid 4px;">
                                    <td style="text-align:left">Closing</td>
                                    <?php
                                    foreach ($products as $value) {
                                        ?>
                                        <td class="qty">
                                            <?php 
                                            if(!empty($so_info[$data['Store']['id']]['CB']))
                                            {
                                                if(array_key_exists($value['Product']['id'], $so_info[$data['Store']['id']]['CB']))
                                                {
                                                    echo $so_info[$data['Store']['id']]['CB'][$value['Product']['id']];
                                                }
                                                else
                                                {
                                                    echo '0.00';
                                                }


                                            /*if(array_key_exists($value['Product']['id'], $so_info[$data['Store']['id']]['OB']))
                                            {
                                                echo $so_info[$data['Store']['id']]['OB'][$value['Product']['id']];
                                            }*/
                                            
                                        }
                                        else
                                        {
                                            echo '0.00';
                                        }
                                        
                                        
                                        ?>
                                    </td>
                                    <?php 
                                } 
                                ?>
                            </tr>

                            <?php  $i++;} //break; } ?>

                            <tr class="totalColumn" style="font-weight:bold;">
                                <td></td>
                                <td>Total :</td>
                                <?php foreach ($products as $value) { ?>
                                <td class="text-center totalQty">0.00</td>
                                <?php } ?>
                            </tr>
                            
                        </tbody>

                    </table>

                    <?php 
                    $total_col = '0,0';
                    ?>

                    <?php foreach ($products as $value){ ?>
                    <?php $total_col .= ',0'; ?>
                    <?php } ?>


                    <script>
                        var totals_qty = [<?=$total_col?>];
                        $(document).ready(function(){

                           setTimeout(
                               function() 
                               {
                                var $dataRows = $("#sum_table tr:not('.totalColumn, .titlerow')");

                                $dataRows.each(function() {
                                   $(this).find('.qty').each(function(i){ 
										//alert($(this).html());   
										totals_qty[i]+=parseFloat($(this).html());
									});
                               });

                                $("#sum_table .totalQty").each(function(i){  
                                   $(this).html(totals_qty[i].toFixed(2));
                               });
                            }, 2000);
                       });
                   </script>

               </div>

                    <?php /*?><div style="width:100%;padding-top:100px;">
                        <footer style="width:100%;text-align:center;">
                    "This Report has been generated from SMC Automated Sales System at <?php echo h($offices[$this->request->data['search']['office_id']]); ?> Area. This information is confidential and for internal use only."
                </footer>	  
            </div><?php */?>



            <?php }else{ ?>

            <div style="clear:both;"></div>
            <div class="alert alert-warning">No Report Found!</div>

            <?php } ?>


            <div style="float:left; width:100%; padding-top:100px;">
                <div style="width:33%;text-align:left;float:left">
                    Prepared by:______________ 
                </div>
                <div style="width:33%;text-align:center;float:left">
                    Checked by:______________ 
                </div>
                <div style="width:33%;text-align:right;float:left">
                    Signed by:______________
                </div>		  
            </div>

        </div>
        <?php } ?>

    </div>	

</div>
</div>

</div>


<script>
   
    $(document).ready(function(){
        $('#searchOfficeId').change(function() {
            $.ajax({
                type: "POST",
                url: '<?=BASE_URL?>stock_status_monthly_reports/get_territory_so_list',
                data: 'office_id='+$(this).val(),
                cache: false, 
                success: function(response)
                {   
                    $('#checkall2').prop('checked', false);             
                    $('.so_list').html(response);               
                }
            });
        });
        $("#download_xl").click(function(e){
            e.preventDefault();
            var html = $("#content").html();
                            // console.log(html);
                            var blob = new Blob([html], { type: 'data:application/vnd.ms-excel' }); 
                            var downloadUrl = URL.createObjectURL(blob);
                            var a = document.createElement("a");
                            a.href = downloadUrl;
                            a.download = "stock_status_monthly_reports.xls";
                            document.body.appendChild(a);
                            a.click();
                        });
        $('.datepicker22').datepicker({
            startView: "year", 
            minViewMode: "months",
            format: "MM-yyyy",
            autoclose: true,
            todayHighlight: true
        });
    });
</script>