<?php
App::import('Controller', 'NationalSalesReportsController');
$NationalSalesController = new NationalSalesReportsController;
?>
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
		<div class="box box-primary">
        
        
        
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('National Sales Report'); ?></h3>
				<?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Product Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */?>
			</div>
            
            	
			<div class="box-body">
				
                <div class="search-box">
					<?php echo $this->Form->create('NationalSalesReports', array('role' => 'form', 'action'=>'index')); ?>
					<table class="search">
                    
                    	 <tr>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required' => true)); ?></td>
                            
                            <td width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required' => true)); ?></td>	
						</tr>
                        
                        
                        <tr>
                        	<td><?php echo $this->Form->input('outlet_type', array('legend'=>'Outlet Category :', 'class' => 'outlet_type', 'type' => 'radio', 'value' => 1,  'options' => $outlet_type_list, 'required'=>true));  ?></td>		
                            <td></td>
                            			
                            <?php /*?><td id="td_product_categories"><?php echo $this->Form->input('product_categories_id', array('id' => 'product_category_id', 'label'=>false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'required' => true)); ?></td><?php */?>
						</tr>
                        
                        <tr>
							<td colspan="2">
                            <label style="float:left; width:15%;">Product Categories : </label>
                            <div id="market_list" class="td_product_categories input select" style="float:left; width:80%; padding-left:20px;">
							<?php echo $this->Form->input('product_categories_id', array('id' => 'product_category_id', 'label'=>false, 'class' => 'checkbox simple', 'multiple' => 'checkbox', 'required' => true)); ?>
                            </div>
                            </td>
						</tr>
                        
                        
						<tr>
							<td colspan="2">
                            <label style="float:left; width:15%;">Area Office : </label>
                            <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
							<?php echo $this->Form->input('office_id', array('id' => 'office_id', 'label'=>false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'required'=> true, 'options'=> $offices)); ?>
                            </div>
                            </td>
						</tr>
                       				
                        
						
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Submit', array('type' => 'submit','class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                
                                <?php if(!empty($requested_data)){ ?>
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
				});
				</script>
                
                
                 <?php if(!empty($requested_data)){ ?>
                 
                 <div id="content" style="width:90%; margin:0 5%;">
                 
                    <style type="text/css">
					.table-responsive { color: #333; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; line-height: 1.42857; }
					.report_table{ font-size:12px; }
					.qty_val{ width:125px; margin:0; float:left; }
					.qty, .val{ width:49%; float:left; border-right:#333 solid 1px; text-align:center; padding:5px 0;}
					.val{ border-right:none; }
					p{ margin:2px 0px; }
					.bottom_box{ float:left; width:33.3%; text-align:center; }
					td, th {padding: 0;}
					table { border-collapse: collapse; border-spacing: 0; }
					.titlerow, .totalColumn{ background:#f1f1f1; }
					.rowDataSd, .totalCol {font-size:85%;}
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
                            <h3 style="margin:2px 0;">National Sales Volume and Value by Brand</h3>
                            <p>
                                Time Frame : <b><?=date('d M, Y', strtotime($date_from))?></b> to <b><?=date('d M, Y', strtotime($date_to))?></b> <?php /*?><br>Reporting Date: <b><?php echo date('d F, Y')?></b><?php */?>
                            </p>
                            <p>Print Unit : Base Unit</p>
                        </div>	 
                         
					
                        <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
                              
                              <tr class="titlerow">
                                  <th>Sales Area</th>
                                  
								  <?php 
								  $temp_cateogry_id = 0;
								  $i=1;
								  $p_total = count($product_list);
								  foreach ($product_list as $p_value){ 
								  if($i==1)$temp_cateogry_id = $p_value['Product']['product_category_id'];
								  ?>
                                  
                                  	<?php if($p_value['Product']['product_category_id']!=$temp_cateogry_id){ ?>
                                    <th>
                                        <div class="qty_val" style="padding-top:2px; height:35px; border-bottom:#333 solid 1px;">Total <?=$temp_cateogry_id?> Sales</div>
                                        <div class="qty_val">
                                            <div class="qty"><small>Qty</small></div>
                                            <div class="val"><small>Val</small></div>
                                        </div>
                                    </th>
                                    <?php
									$temp_cateogry_id = $p_value['Product']['product_category_id'];
									} 
									?>
                                  
                                    <th>
                                        <div class="qty_val" style="padding-top:2px; height:35px; border-bottom:#333 solid 1px;"><?=$p_value['Product']['name']?></div>
                                        <div class="qty_val">
                                            <div class="qty"><small>Qty</small></div>
                                            <div class="val"><small>Val</small></div>
                                        </div>
                                    </th>
                                                                        
                                  <?php $i++; } ?>
                                  
                                  
                                  <th>
                                    <div class="qty_val" style="padding-top:2px; height:35px; border-bottom:#333 solid 1px;">Total <?=$category_name?> Sales</div>
                                    <div class="qty_val">
                                        <div class="qty"><small>Qty</small></div>
                                        <div class="val"><small>Val</small></div>
                                    </div>
                                  </th>
                              </tr>
                              
                              
                              <?php 
                              foreach ($offices as $key => $value){ 
                              if(in_array($key, $requested_data['NationalSalesReports']['office_id'])) {
                              ?>
                              <tr>
                                 <td style="width:50px; font-size:85%;"><?=str_replace('Sales Office', '', $value)?></td>
                                
                                 <?php
                                 $t_qty = 0; 
                                 $t_val = 0;
                                 foreach($product_list as $p_value){ 
                                 $product_id = $p_value['Product']['id'];
                                 $office_id = $key;
                                 $sales_data = $NationalSalesController->getProductSales($office_id, $date_from, $date_to, $product_id);
                                 $t_qty+=$sales_data['qty']?$sales_data['qty']:'0.00';
                                 $t_val+=$sales_data['val']?$sales_data['val']:'0.00';
                                 ?>
                                 <td class="rowDataSd">
                                     <div class="qty_val">
                                        <div class="qty"><?=$sales_data['qty']?$sales_data['qty']:'0.00'?></div>
                                        <div class="val"><?=$sales_data['val']?$sales_data['val']:'0.00'?></div>
                                     </div>
                                 </td>
                                 <?php } ?>
                                 
                                 
                                 <td class="rowDataSd">
                                    <div class="qty_val">
                                        <div class="qty"><?=$t_qty?></div>
                                        <div class="val"><?=$t_val?></div>
                                    </div>
                                  </td>
                              </tr>
                              <?php 
                                } 
                              }
                              ?>
                              
                              <tr class="totalColumn">
                                  <td><b>Total:</b></td>
                                  <?php foreach ($product_list as $p_value){ ?>
                                    <td class="totalCol">
                                        <div class="qty_val">
                                            <div class="qty"></div>
                                            <div class="val"></div>
                                        </div>
                                    </td>
                                  <?php } ?>
                                  <td class="totalCol">
                                    <div class="qty_val">
                                        <div class="qty"></div>
                                        <div class="val"></div>
                                    </div>
                                  </td>
                              </tr>
                              
                              
                             
                      </table>
                  	
                  	
						<script>
                           <?php
                           $product_total = count($product_list);
                           $total_v = '0';
                           for($i=0; $i<$product_total; $i++){
                                $total_v.= ',0';
                           }
                           ?>
                           //alert('<?=$total_v?>');
                            var totals_qty = [<?=$total_v?>];
                            var totals_val = [<?=$total_v?>];
                            $(document).ready(function(){
                    
                                var $dataRows = $("#sum_table tr:not('.totalColumn, .titlerow')");
                    
                                $dataRows.each(function() {
                                    $(this).find('.rowDataSd .qty').each(function(i){        
                                        totals_qty[i]+=parseFloat( $(this).html());
                                    });
                                });
                                $("#sum_table td.totalCol .qty").each(function(i){  
                                    $(this).html(totals_qty[i]);
                                });
                                
                                
                                $dataRows.each(function() {
                                    $(this).find('.rowDataSd .val').each(function(i){        
                                        totals_val[i]+=parseFloat( $(this).html());
                                    });
                                });
                                $("#sum_table td.totalCol .val").each(function(i){  
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
$('.outlet_type').on('ifChecked', function(event){
	//alert($(this).val()); // alert value
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL;?>NationalSalesReports/get_category_list',
		data: 'outlet_type='+$(this).val(),
		cache: false, 
		success: function(response){
			//alert(response);						
			$('.td_product_categories').html(response);				
		}
	});
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