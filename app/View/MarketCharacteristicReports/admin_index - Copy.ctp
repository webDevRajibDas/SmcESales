<?php
App::import('Controller', 'MarketCharacteristicReportsController');
$OutletCharacteristicController = new MarketCharacteristicReportsController;
?>


<style>
.search .radio label {
    width: auto;
	float:none;
	padding:0px 5% 0px 5px;
	margin:0px;
}
.search .radio legend {
    float: left;
    margin: 5px 20px 0 0;
    text-align: right;
    width: 12.5%;
	display: inline-block;
    font-weight: 700;
	font-size:14px;
	border-bottom:none;
}
#market_list .checkbox label{
	padding-left:0px;
	width:auto;
}
#market_list .checkbox{
	width:25%;
	float:left;
	margin:1px 0;
}
body .td_rank_list .checkbox{
	width:auto !important;
	padding-left:20px !important;
}
.radio input[type="radio"], .radio-inline input[type="radio"]{
    margin-left: 0px;
    position: relative;
	margin-top:8px;
}
.search label {
    width: 25%;
}
#market_list{
	padding-top:5px;
}
.market_list2 .checkbox{
	width:15% !important;
}
.market_list3 .checkbox{
	width:20% !important;
}
.box_area{
	display:none;
}
</style>

<style>
#divLoading {
	display : none;
}
#divLoading.show {
	display : block;
	position : fixed;
	z-index: 100;
	background-image : url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
	background-color: #666;   
	opacity : 0.4;
	background-repeat : no-repeat;
	background-position : center;
	left : 0;
	bottom : 0;
	right : 0;
	top : 0;
}
#loadinggif.show {
	left : 50%;
	top : 50%;
	position : absolute;
	z-index : 101;
	width : 32px;
	height : 32px;
	margin-left : -16px;
	margin-top : -16px;
}
</style>

<div id="divLoading" class=""> </div>

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
        
        
        
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?=$page_title?></h3>
				<?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New OutletCharacteristic Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */?>
			</div>
            
            	
			<div class="box-body">
				
                <div class="search-box">
					<?php echo $this->Form->create('MarketCharacteristicReports', array('role' => 'form', 'action'=>'index')); ?>
					<table class="search">
                    	
                         <tr>
							<td class="required" width="50%">
							<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true)); ?></td>
                            
                            <td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true)); ?></td>	
						</tr>
                        
                        <tr>
                        	<td colspan="2">
							<?php echo $this->Form->input('report_type', array('legend'=>'Report Type :', 'class' => 'report_type', 'type' => 'radio', 'default' => 'visit_info', 'options' => $report_types, 'required'=>true));  ?></td>		
						</tr>
                          
                         <?php if($office_parent_id==0){ ?>
                         <tr>
							<td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id','class' => 'form-control region_office_id', 'required'=>true, 'empty'=>'---- Head Office ----', 'options' => $region_offices)); ?></td>
							<td></td>							
						</tr>
                        <?php } ?>
                        
                        
                        <?php if($office_parent_id==14){ ?>
                         <tr>
							<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id','class' => 'form-control region_office_id', 'required'=>false, 'options' => $region_offices,)); ?></td>
							<td></td>							
						</tr>
                        <?php } ?>
                        
                        
                         <?php if($office_parent_id==0 || $office_parent_id==14){ ?>
                         <tr>
							<td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id','class' => 'form-control office_id','required'=>true,'empty'=>'---- All ----')); ?></td>
							<td></td>							
						</tr>
                        <?php }else{ ?>
                        <tr>
							<td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id','class' => 'form-control office_id', 'required'=>true)); ?></td>
							<td></td>							
						</tr>
                        <?php } ?>
                        
                        
                        <tr>
                        	<td colspan="2">
							<?php echo $this->Form->input('type', array('legend'=>'Type :', 'class' => 'type', 'type' => 'radio', 'default' => 'territory', 'onClick' => 'typeChange(this.value)', 'options' => $types, 'required'=>true));  ?>
                            </td>		
						</tr>
                        
						<tr>
							<td>
                            <div id="territory_html">
							<?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id office_t_so', 'required'=>false, 'empty'=>'---- All ----')); ?>
                            </div>
                            
                            <div id="so_html">
                            <?php echo $this->Form->input('so_id', array('label' => 'Sales Officers', 'id' => 'so_id','class' => 'form-control so_id', 'required'=>false, 'options' => $so_list, 'empty'=>'---- All ----')); ?>
                            </div>
                            </td>
							<td></td>							
						</tr>
                                                
                        
                        <tr>
                            <td colspan="2">
                            <label style="float:left; width:12.5%;">Districts : </label>
                            <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                <div style="margin:auto; width:90%; float:left;">
                                    <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall1" />
                                    <label for="checkall1" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                </div>
                                <div class="selection1 district_box box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?=($districts)?'display:block':''?>">
                                <?php echo $this->Form->input('district_id', array('id' => 'district_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'options'=> $districts)); ?>
                                </div>
                            </div>
                            </td>
                        </tr>
                        
                        
                        <tr>
                            <td colspan="2">
                            <label style="float:left; width:12.5%;">Thanas : </label>
                            <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                <div style="margin:auto; width:90%; float:left;">
                                    <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
                                    <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                </div>
                                <div class="selection2 box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?=($thanas)?'display:block':''?>">
                                <?php echo $this->Form->input('thana_id', array('id' => 'thana_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $thanas)); ?>
                                </div>
                            </div>
                            </td>
                        </tr>
                        
                        
                        <tr>
                            <td colspan="2">
                            <label style="float:left; width:12.5%;">Markets : </label>
                            <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                <div style="margin:auto; width:90%; float:left;">
                                    <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall3" />
                                    <label for="checkall3" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                </div>
                                <div class="selection3 box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?=($markets)?'display:block':''?>">
                                <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $markets)); ?>
                                </div>
                            </div>
                            </td>
                        </tr>
                        
                        
                        
                        
                        
						<tr align="center">
							<td colspan="2">
                                    
                                 <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div'=>false, 'name'=>'submit')); ?>
                                                         
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                                                
							</td>						
						</tr>
					</table>
                    	
                    
					<?php echo $this->Form->end(); ?>
				</div>
                
                
                
                
                
                
                <?php if(!empty($request_data)){ ?>
                                 
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
                    
                    	<div class="pull-right csv_btn" style="padding-top:20px;">
                            <?=$this->Html->link(__('Dwonload XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id'=>'download_xl', 'escape' => false)); ?>
                        </div>
                        
                        <div id="xls_body">
                            <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                                <h2 style="margin:2px 0;">Social Marketing Company</h2>
                                
                                
                                <h3 style="margin:2px 0;"><?=$report_types[$report_type]?></h3>
                                
                                <p>
                                   <b> Time Frame : <?=@date('d M, Y', strtotime($date_from))?> to <?=@date('d M, Y', strtotime($date_to))?></b>
                                </p>
                                
                                <p>
                                    <?php if($region_office_id){ ?>
                                        <span>Region Office: <?=$region_offices[$region_office_id]?></span>
                                    <?php }else{ ?>
                                        <span>Head Office</span>
                                    <?php } ?>
                                    <?php if($office_id){ ?>
                                        <span>, Area Office: <?=$offices[$office_id]?></span>
                                    <?php } ?>
                                    <?php if($territory_id){ ?>
                                        <span>, Territory Name: <?=$territories[$territory_id]?></span>
                                    <?php } ?>
                                </p>
                                
                            </div>	 
                                                       
                            <div style="float:left; width:100%; height:430px; overflow:scroll;">
                                
                                <?php if($report_type=='visited'){ ?>
                                <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                  <tbody>
                                    <tr class="titlerow">
                                      <th>Market</th>
                                      <th>No of<br> Visited Day</th>
                                      <th width="30%">Visit Date</th>
                                      <th>Visited Outlet</th>
                                      <th>No Of Memo</th>
                                      <th style="text-align:right;">Memo Total</th>
                                      <th>Visited By</th>
                                    </tr>
                                    
                                    
                                    <?php if($results){ ?>
                                    
                                    	<?php foreach($results as $district_name => $thana_datas){ ?>
                                            <tr>
                                              <td style="text-align:left;" colspan="7"><b>District :- <?=$district_name?></b></td>
                                            </tr>
                                            
                                            <?php foreach($thana_datas as $thana_name => $market_datas){ ?>
                                            <tr>
                                              <td style="text-align:left;" colspan="7"><b>Thana :- <?=$thana_name?> </b></td>
                                            </tr>
                                            
                                            	<?php 
												foreach($market_datas as $market_name => $memo_datas)
												{ 
													$memo_date = '';
													$memo_total = 0;
													$so_name = '';
													
													foreach($memo_datas as $m_results)
													{
														 $m_total = count($m_results);
														 foreach($m_results as $m_result)
														 {
															$memo_date.= $m_result['memo_date'].', ';
															@$so_name =  $m_result['so_name'];
															$memo_total+= $m_result['memo_total'];
														 }
														 //$memo_date.= $m_result[$i]['memo_date'].', ';
														//break;
													}
												?>
                                                <tr>
                                                  <td><?=$market_name?></td>
                                                  <td></td>
                                                  
                                                  <td>
												  
                                                  <?php
												  $memo_date_list = '';
												  $m_date_temp = '';
                                                  $memo_date = explode(', ', $memo_date);
												  $total_dates = count($memo_date);
												  foreach($memo_date as $date_val)
												  {
													 if($date_val){
														 if($date_val!=$m_date_temp){
															$memo_date_list.= $date_val.', ';
															$m_date_temp = $date_val;
														 }
													 }
												  }
												  //pr($memo_date);
												  ?>
                                                  <?=$memo_date_list?>
                                                  </td>
                                                  
                                                  <td><?=@count($memo_datas)?> of <?=@$total_outlets_market_wise[$market_name]?></td>
                                                  <td></td>
                                                  <td style="text-align:right;"><?=sprintf("%01.2f", $memo_total)?></td>
                                                  <td><?=$so_name?></td>
                                                </tr>
                                            	<?php } ?>
                                                <tr>
                                                  <td style="text-align:left;" colspan="6"><b>Total Market : 12, Visited Market : 15, Non Visited Market : 70</b></td>
                                                </tr>
                                               <?php } ?>
                                            
                                            
                                        <?php } ?>
                                        
                                    	<?php /*?><?=$output?><?php */?>
                                    
                                    <?php }else{ ?>
                                    <tr>
                                        <td colspan="6">No data found!</td>
                                    </tr>
                                    <?php } ?>
                                    
                                    
                                    
                                    
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                <?php if($report_type=='non_visited'){ ?>
                                <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                  <tbody>
                                    <tr class="titlerow">
                                      <th>Market</th>
                                      <th>Outlet Name</th>
                                      <th>Outlet Type</th>
                                    </tr>
                                    
                                    <?php if($results){ ?>
                                        <?=$output?>
                                    <?php }else{ ?>
                                        
                                        <tr>
                                          <td colspan="3">No Data Found!</td>
                                        </tr>
                                    
                                    <?php } ?>
                                    
                                    
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                <?php if($report_type=='visit_info'){ ?>
                                <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                  <tbody>
                                    <tr class="titlerow">
                                      <th>Outlet</th>
                                      <th>Memo No</th>
                                      <th width="10%">Date</th>
                                      <th style="text-align:left;">Product</th>
                                      <th style="text-align:right;">Qty</th>
                                      <th style="text-align:right;">Total Value</th>
                                      <th>Sales Officer</th>
                                    </tr>
                                    
                                    
                                    <?php if($results){ ?>  
                                        
                                        <?=$output?>
                                        
                                        <?php /*?><?php 
										$grand_total = 0;
										foreach($results as $market_name => $market_datas)
										{ 
										?>
                                            <tr>
                                              <td style="text-align:left; font-size:15px;" colspan="7"><b>Market :- <?=$market_name?></b></td>
                                            </tr>
                                        
                                        	<?php 
											$market_total = 0;
											foreach($market_datas as $market_name => $memo_datas)
											{ 
											?>
                                            
                                            	<?php 
												$market_total = 0;
												foreach($memo_datas as $memo_no => $memo_products){ 
												?>
                                                
													<?php 
													$memo_total = 0;
													$i=1;
													foreach($memo_products as $memo_product)
													{ 
													$memo_total+= $memo_product['product_price'];
													?>
                                                    <tr>
                                                      <td><?=$i==1?$market_name:''?></td>
                                                      <td><?=$i==1?$memo_no:''?></td>
                                                      <td><?=$i==1?date('d-m-Y', strtotime($memo_product['memo_date'])):''?></td>
                                                      <td style="text-align:left;"><?=@$memo_product['product_name']?></td>
                                                      <td style="text-align:right;"><?=@$memo_product['product_sales_qty']?></td>
                                                      <td style="text-align:right;"><?=@$memo_product['product_price']?></td>
                                                      <td><?=@$memo_product['so_name']?></td>
                                                    </tr>
                                                    <?php 
													$i++;
													} 
													$market_total+= $memo_total;
													?>
                                                    <tr>
                                                      <td style="text-align:right;" colspan="5"><b>Memo Total :</b></td>
                                                      <td style="text-align:right;"><b><?=sprintf("%01.2f", $memo_total)?></b></td>
                                                      <td colspan="3"></td>
                                                    </tr>
                                                <?php 
												} 
												?>
                                                
                                                <tr style="background:#f7f7f7">
                                                  <td style="text-align:right;" colspan="5"><b>Outlet Wise Memo Total :</b></td>
                                                  <td style="text-align:right;"><b><?=sprintf("%01.2f", $market_total)?></b></td>
                                                  <td colspan="3"></td>
                                                </tr>
                                            <?php 
											$market_total+= $market_total;
											} 
											?>
                                        
                                        		<tr style="background:#ccc">
                                                  <td style="text-align:right;" colspan="5"><b>Market Wise Memo Total :</b></td>
                                                  <td style="text-align:right;"><b><?=sprintf("%01.2f", $market_total)?></b></td>
                                                  <td colspan="3"></td>
                                                </tr>
                                        <?php 
										$grand_total+= $market_total;
										} 
										?>
                                        
                                        <tr style="background:#f7f7f7">
                                          <td style="text-align:right;" colspan="5"><b>Grand Total :</b></td>
                                          <td style="text-align:right;"><b><?=sprintf("%01.2f", $grand_total)?></b></td>
                                          <td colspan="3"></td>
                                        </tr><?php */?>
                                        
                                    <?php }else{ ?>
                                    	
                                        <tr>
                                          <td colspan="7"><b>No Data Found!</b></td>
                                        </tr>
                                    
                                    <?php } ?>
                                    
                                    
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                            </div>
                            
                            
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
	value:'name',
	url: '<?= BASE_URL.'OutletCharacteristic_reports/get_office_list';?>',
	type: 'post',
	data:{'region_office_id': 'region_office_id' }
});
$('.region_office_id').change(function () {
	$('#territory_id').html('<option value="">---- All ----');
});
$('.office_id').selectChain({
	target: $('.territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});
</script>


<script>
$(document).ready(function() {
	$("input[type='checkbox']").iCheck('destroy');
	$("input[type='radio']").iCheck('destroy');
	
	$('#checkall').click(function() {
		var checked = $(this).prop('checked');
		$('.selection').find('input:checkbox').prop('checked', checked);
		
	});
	
	$('#checkall1').click(function() {
		var checked = $(this).prop('checked');
		$('.selection1').find('input:checkbox').prop('checked', checked);
		thanaBoxList();
	});
	
	$('#checkall2').click(function() {
		var checked = $(this).prop('checked');
		$('.selection2').find('input:checkbox').prop('checked', checked);
		marketBoxList();
	});
	
	$('#checkall3').click(function() {
		var checked = $(this).prop('checked');
		$('.selection3').find('input:checkbox').prop('checked', checked);
	});
	

		
});

$('#office_id').change(function() 
{
	//alert($(this).val());
	date_from = $('.date_from').val();
	date_to = $('.date_to').val();
	if(date_from && date_to){
		$.ajax({
			type: "POST",
			url: '<?=BASE_URL?>market_characteristic_reports/get_office_so_list',
			data: 'office_id='+$(this).val() + '&date_from=' + date_from + '&date_to=' + date_to,
			cache: false, 
			success: function(response){
				//alert(response);						
				$('#so_html').html(response);
			}
		});
	}
	else
	{
		$('#office_id option:nth-child(1)').prop("selected", true);
		alert('Please select date range!');
	}
});


$(document).ready(function() {	
	typeChange();
});


function typeChange()
{
	var type = $('.type:checked').val();
	
	//for territory and so 
	$('#so_html').hide();
	$('#territory_html').hide();
	
	//alert(rows);
	
	<?php /*?><?php if(!@$request_data['SalesAnalysisReports']['territory_id']){ ?>
	$('.office_t_so option:nth-child(1)').prop("selected", true).change();
	<?php } ?>
	
	<?php if(!@$request_data['SalesAnalysisReports']['so_id']){ ?>
	$('#so_id option:nth-child(1)').prop("selected", true).change();
	<?php } ?><?php */?>
	
	if(type=='so'){
		$('#so_html').show();
	}else{
		$('#territory_html').show();
	}
	
	if(type=='so'){
		$('.office_t_so option:nth-child(1)').prop("selected", true).change();
	}else if(type=='territory'){
		$('#so_id option:nth-child(1)').prop("selected", true).change();
	}else{
		<?php if(!@$request_data['MarketCharacteristicReports']['territory_id']){ ?>
		$('.office_t_so option:nth-child(1)').prop("selected", true).change();
		<?php } ?>
		
		<?php if(!@$request_data['MarketCharacteristicReports']['so_id']){ ?>
		$('#so_id option:nth-child(1)').prop("selected", true).change();
		<?php } ?>
	}
	
	
}


</script>



<script>
$(document).ready(function () {
	$('#region_office_id').change(function () {
		districtBoxList();
	});
	$('#office_id').change(function () {
		districtBoxList();
	});
	$('#territory_id').change(function () {
		districtBoxList();
	});
});


function districtBoxList()
{
	$('#checkall1').removeAttr("checked");
	$('#checkall2').removeAttr("checked");
	$('#checkall3').removeAttr("checked");
	
	$('.box_area').hide();
	$('.box_area').html('');
		
	var region_office_id = $('#region_office_id').val()?$('#region_office_id').val():0;
	var office_id = $('#office_id').val()?$('#office_id').val():0;
	var territory_id = $('#territory_id').val()?$('#territory_id').val():0;
	
	//alert(region_office_id);
	
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL;?>market_characteristic_reports/get_district_list',
		data: 'region_office_id='+region_office_id+'&office_id='+office_id+'&territory_id='+territory_id,
		beforeSend: function() {$("div#divLoading").addClass('show');},
		cache: false, 
		success: function(response){
			//alert(response);
			if(response!=''){
				$('.selection1').show();
			}						
			$('.selection1').html(response);	
			$("div#divLoading").removeClass('show');						
		}
	});
}




</script>


<script>
$(document).ready(function () 
{
	$('[name="data[MarketCharacteristicReports][district_id][]"]').change(function () {
		//alert($(this).val()); // alert value
		//$('.selection').find('input:checkbox').prop('checked', checked);
		thanaBoxList();
	});
});

function thanaBoxList()
{
	var val = [];
	$('[name="data[MarketCharacteristicReports][district_id][]"]:checked').each(function(i){
	  val[i] = $(this).val();
	});
	
	//alert(val);
	$('.selection2').hide();
	
	$('.selection3').hide();
	$('.selection3').html('');

	
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL;?>market_characteristic_reports/get_thana_list',
		data: 'district_id='+val,
		beforeSend: function() {$("div#divLoading").addClass('show');},
		cache: false, 
		success: function(response){
			//alert(response);	
			if(response!=''){
				$('.selection2').show();
			}
			$('.selection2').html(response);
			$("div#divLoading").removeClass('show');				
		}
	});
}
</script>

<script>
$(document).ready(function () 
{
	$('[name="data[MarketCharacteristicReports][thana_id][]"]').change(function () {
		marketBoxList();
	});
});

function marketBoxList()
{
	var val = [];
	$('[name="data[MarketCharacteristicReports][thana_id][]"]:checked').each(function(i){
	  val[i] = $(this).val();
	});
	
	$('.selection3').hide();
	
	
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL;?>market_characteristic_reports/get_market_list',
		data: 'thana_id='+val,
		beforeSend: function() {$("div#divLoading").addClass('show');},
		cache: false, 
		success: function(response){
			//alert(response);	
			if(response!=''){
				$('.selection3').show();
			}
			$('.selection3').html(response);
			$("div#divLoading").removeClass('show');				
		}
	});
}
</script>





<script>
	function PrintElem(elem)
	{
		var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

		//mywindow.document.write('<html><head><title>' + document.title  + '</title>');
		mywindow.document.write('<html><head><title></title><style>.csv_btn{display:none;}</style>');
		mywindow.document.write('</head><body>');
		//mywindow.document.write('<h1>' + document.title  + '</h1>');
		mywindow.document.write(document.getElementById(elem).innerHTML);
		mywindow.document.write('</body></html>');

		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10*/

		mywindow.print();
		//mywindow.close();

		return true;
	}
	
	$(document).ready(function(){

            $("#download_xl").click(function(e){

              e.preventDefault();

              var html = $("#xls_body").html();

				// console.log(html);

				var blob = new Blob([html], { type: 'data:application/vnd.ms-excel' });

				var downloadUrl = URL.createObjectURL(blob);

				var a = document.createElement("a");

				a.href = downloadUrl;

				a.download = "market_characteristic_reports.xls";

				document.body.appendChild(a);

				a.click();

			  });

          });
</script>