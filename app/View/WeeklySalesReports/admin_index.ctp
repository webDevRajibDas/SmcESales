<?php
App::import('Controller', 'WeeklySalesReportsController');
$WeeklySalesReportsController = new WeeklySalesReportsController;
//pr($product_sales_data);die();
?>

<div class="row">
    <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Weekly Sales Report'); ?></h3>
        <div class="box-tools pull-right">
          <?php
          if ($this->App->menu_permission('ProductCategoryOrders', 'admin_create_order')) {
              echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Create Order for Report'), array('controller'=>'ProductCategoryOrders','action' => 'create_order'), array('class' => 'btn btn-primary', 'escape' => false));
          }
          ?>
        </div>
      </div>
      <div class="box-body">
            <div class="search-box">
				<?php echo $this->Form->create('WeeklySalesReports', array('role' => 'form', 'action'=>'index')); ?>
				<table class="search">
						
					<tr>
						<td width="50%"><?php echo $this->Form->input('date_from', array('label'=>'Date :','id'=>'datepicker','class' => 'form-control','required' => true)); ?></td>
						<td width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control date_to','required' => true)); ?></td> 
					</tr>
					<tr>
						<td width="50%"><?php echo $this->Form->input('total_working_days', array('class' => 'form-control total_working_days','type'=>'number','required' => true)); ?></td>
						<td width="50%"><?php echo $this->Form->input('monthly_working_days', array('class' => 'form-control monthly_working_days','type'=>'number','required' => true)); ?></td> 
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
                            <h2 style="margin:2px 0;">Social Marketing Company</h2>
                            <h3 style="margin:2px 0;">Weekly Sales Report</h3>
                            <p>
                                Time Frame : <b><?=date('d M, Y', strtotime($date_from))?></b> to <b><?=date('d M, Y', strtotime($date_to))?></b> <?php /*?><br>Reporting Date: <b><?php echo date('d F, Y')?></b><?php */?>
                            </p>
                            <p>Print Unit : Sale Unit</p>
                        </div> 
                        <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
                              
							<tr class="titlerow">
								<th>Product Name</th>
								<th>Dhaka East Sales Office</th>
								<th>Dhaka West Sales Office</th>
								<th>Mymensingh Sales Office</th>
								<th>Chattogram Sales Office</th>
								<th>Cumilla Sales Office</th>
								<th>Sylhet Sales Office</th>
								<th>Khulna Sales Office</th>
								<th>Kushtia Sales Office</th>
								<th>Barisal Sales Office</th>
								<th>Bogura Sales Office</th>
								<th>Rajshahi Sales Office</th>
								<th>Rangpur Sales Office</th>
								<th style="text-align:center;">Sales (<?=$total_working_days?> Working days) </th>
								<th style="text-align:center;">Monthly Projection </th>
								<th style="text-align:center;">% Achieved</th>
								<th style="text-align:center;">Sales trend per working day</th>
								<th style="text-align:center;">Balance for remaining working days</th>
								<th style="text-align:center;">Required Sale per working day</th>
							</tr>
                          
							<?php 
								$total_dhaka_east = 0;
								$total_dhaka_west = 0;
								$total_mymensingh = 0;
								$total_chittagong = 0;
								$total_cumilla = 0;
								$total_sylhet = 0;
								$total_bogra = 0;
								$total_rangpur = 0;
								$total_rajshahi = 0;
								$total_kushtia = 0;
								$total_barishal = 0;
								$total_khulna = 0;
								
								$total_dhaka_east_cyp = 0;
								$total_dhaka_west_cyp = 0;
								$total_mymensingh_cyp = 0;
								$total_chittagong_cyp = 0;
								$total_cumilla_cyp = 0;
								$total_sylhet_cyp = 0;
								$total_bogra_cyp = 0;
								$total_rangpur_cyp = 0;
								$total_rajshahi_cyp = 0;
								$total_kushtia_cyp = 0;
								$total_barishal_cyp = 0;
								$total_khulna_cyp = 0;
								
								$total_dhaka_east_eff = 0;
								$total_dhaka_west_eff = 0;
								$total_mymensingh_eff = 0;
								$total_chittagong_eff = 0;
								$total_cumilla_eff = 0;
								$total_sylhet_eff = 0;
								$total_bogra_eff = 0;
								$total_rangpur_eff = 0;
								$total_rajshahi_eff = 0;
								$total_kushtia_eff = 0;
								$total_barishal_eff = 0;
								$total_khulna_eff = 0;
								
								$total_sales_qty = 0;
								$total_projection = 0;
								$total_achive = 0;
								$total_sales_per_day = 0;
								$total_sales_remaining_per_day = 0;
								$total_sales_required_per_day = 0;
								
								$dhaka_east_sales = 0;
								$dhaka_west_sales = 0;
								$mymensingh_sales = 0;
								$chittagong_sales = 0;
								$cumilla_sales = 0;
								$sylhet_sales = 0;
								$bogra_sales = 0;
								$rangpur_sales = 0;
								$rajshahi_sales = 0;
								$kushtia_sales = 0;
								$barishal_sales = 0;
								$khulna_sales = 0;
								$product_sales_qty = 0;
								$product_projection = 0;
								$product_achive = 0;
								$product_sales_per_day = 0;
								$product_sales_remaining_per_day = 0;
								$product_sales_required_per_day = 0;
								
								$last_setting_info = array();
							foreach ($product_sales_data as $key => $value)
							{
								$item = $value['ProductSettingsForReport']['item'];
								if(array_key_exists(15, $value)){
									$total_dhaka_east_cyp = $total_dhaka_east_cyp + $value[15]['cyp'];
									$total_dhaka_east_eff = $total_dhaka_east_eff + $value[15]['effective_call'];
								}
								if(array_key_exists(19, $value)){
									$total_dhaka_west_cyp = $total_dhaka_west_cyp + $value[19]['cyp'];
									$total_dhaka_west_eff = $total_dhaka_west_eff + $value[19]['effective_call'];
								}
								if(array_key_exists(26, $value)){
									$total_mymensingh_cyp = $total_mymensingh_cyp + $value[26]['cyp'];
									$total_mymensingh_eff = $total_mymensingh_eff + $value[26]['effective_call'];
								}
								if(array_key_exists(18, $value)){
									$total_chittagong_cyp = $total_chittagong_cyp + $value[18]['cyp'];
									$total_chittagong_eff = $total_chittagong_eff + $value[18]['effective_call'];
								}
								if(array_key_exists(16, $value)){
									$total_cumilla_cyp = $total_cumilla_cyp + $value[16]['cyp'];
									$total_cumilla_eff = $total_cumilla_eff + $value[16]['effective_call'];
								}
								if(array_key_exists(25, $value)){
									$total_sylhet_cyp = $total_sylhet_cyp + $value[25]['cyp'];
									$total_sylhet_eff = $total_sylhet_eff + $value[25]['effective_call'];
								}
								if(array_key_exists(22, $value)){
									$total_bogra_cyp = $total_bogra_cyp + $value[22]['cyp'];
									$total_bogra_eff = $total_bogra_eff + $value[22]['effective_call'];
								}
								if(array_key_exists(23, $value)){
									$total_rangpur_cyp = $total_rangpur_cyp + $value[23]['cyp'];
									$total_rangpur_eff = $total_rangpur_eff + $value[23]['effective_call'];
								}
								if(array_key_exists(24, $value)){
									$total_rajshahi_cyp = $total_rajshahi_cyp + $value[24]['cyp'];
									$total_rajshahi_eff = $total_rajshahi_eff + $value[24]['effective_call'];
								}
								if(array_key_exists(28, $value)){
									$total_kushtia_cyp = $total_kushtia_cyp + $value[28]['cyp'];
									$total_kushtia_eff = $total_kushtia_eff + $value[28]['effective_call'];
								}
								if(array_key_exists(29, $value)){
									$total_barishal_cyp = $total_barishal_cyp + $value[29]['cyp'];
									$total_barishal_eff = $total_barishal_eff + $value[29]['effective_call'];
								}
								if(array_key_exists(27, $value)){
									$total_khulna_cyp = $total_khulna_cyp + $value[27]['cyp'];
									$total_khulna_eff = $total_khulna_eff + $value[27]['effective_call'];
								}
								
								if($first_item == $item)
								{?>
								<tr class="rowDataSd">
									<td><?=$value['Product']['product_name']?></td>
									<td><?php if(array_key_exists(15, $value)){?>
										<?php echo $value[15]['pro_quantity'];
										$dhaka_east_sales = $dhaka_east_sales + $value[15]['pro_quantity'];
										$total_dhaka_east = $total_dhaka_east + $value[15]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(19, $value)){?>
										<?php echo $value[19]['pro_quantity'];
										$dhaka_west_sales = $dhaka_west_sales + $value[19]['pro_quantity'];
										$total_dhaka_west = $total_dhaka_west + $value[19]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(26, $value)){?>
										<?php echo $value[26]['pro_quantity'];
										$mymensingh_sales = $mymensingh_sales + $value[26]['pro_quantity'];
										$total_mymensingh = $total_mymensingh + $value[26]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(18, $value)){?>
										<?php echo $value[18]['pro_quantity'];
										$chittagong_sales =  $chittagong_sales + $value[18]['pro_quantity'];
										$total_chittagong =  $total_chittagong + $value[18]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(16, $value)){?>
										<?php echo $value[16]['pro_quantity'];
										$cumilla_sales = $cumilla_sales + $value[16]['pro_quantity'];
										$total_cumilla = $total_cumilla + $value[16]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									
									<td><?php if(array_key_exists(25, $value)){?>
										<?php echo $value[25]['pro_quantity'];
										$sylhet_sales = $sylhet_sales + $value[25]['pro_quantity'];
										$total_sylhet = $total_sylhet + $value[25]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(27, $value)){?>
										<?php echo $value[27]['pro_quantity'];
										$khulna_sales = $khulna_sales + $value[27]['pro_quantity'];
										$total_khulna = $total_khulna + $value[27]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(28, $value)){?>
										<?php echo $value[28]['pro_quantity'];
										$kushtia_sales = $kushtia_sales + $value[28]['pro_quantity'];
										$total_kushtia = $total_kushtia + $value[28]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(29, $value)){?>
										<?php echo $value[29]['pro_quantity'];
										$barishal_sales = $barishal_sales + $value[29]['pro_quantity'];
										$total_barishal = $total_barishal + $value[29]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(22, $value)){?>
										<?php echo $value[22]['pro_quantity'];
										$bogra_sales = $bogra_sales + $value[22]['pro_quantity'];
										$total_bogra = $total_bogra + $value[22]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(24, $value)){?>
										<?php echo $value[24]['pro_quantity'];
										$rajshahi_sales = $rajshahi_sales + $value[24]['pro_quantity'];
										$total_rajshahi = $total_rajshahi + $value[24]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(23, $value)){?>
										<?php echo $value[23]['pro_quantity'];
										$rangpur_sales = $rangpur_sales + $value[23]['pro_quantity'];
										$total_rangpur = $total_rangpur + $value[23]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									
									<td style="text-align:right;">
										<?php if(!empty($value)){
										$total_sales = $value['total_qty'];
										$product_sales_qty = $product_sales_qty + $value['total_qty'];
										$total_sales_qty = $total_sales_qty + $value['total_qty'];
										echo sprintf("%01.2f", $value['total_qty']);
										}else{ echo '0.00';}?>
									</td>
									<!--other info-->
									<td style="text-align:right;">
										<?php 
											$target_quantity = $value['Product']['target_quantity'];
											$product_projection = $product_projection + $target_quantity;
											$total_projection = $total_projection + $target_quantity;
											echo $value['Product']['target_quantity'];
										?>
									</td>
									
								
									<td style="text-align:right;">
										<?php 
											$product_achive = $product_achive + $value['achive'];
											$total_achive = $total_achive + $value['achive'];
											echo sprintf("%01.2f",$value['achive']);
										?>
									</td>
								
									<td style="text-align:right;">
										<?php 
											$per_day_sales = $target_quantity/$total_working_days;
											$product_sales_per_day = $product_sales_per_day + $per_day_sales;
											$total_sales_per_day = $total_sales_per_day + $per_day_sales;
											echo floor($per_day_sales);?>
									</td>
									<td style="text-align:right;">
										<?php 
											$diff = $target_quantity - $total_sales;
											if($diff < 0){ $diff = $diff*(-1);}
											$product_sales_remaining_per_day = $product_sales_remaining_per_day + $diff;
											$total_sales_remaining_per_day = $total_sales_remaining_per_day + $diff;
											echo sprintf("%01.2f",$diff);
										?>
									</td>
									<td style="text-align:right;">
										<?php
											$sales_required = $diff/$remaining_days;
											$product_sales_required_per_day = $product_sales_required_per_day + $sales_required;
											$total_sales_required_per_day = $total_sales_required_per_day + $sales_required;
											echo sprintf("%01.2f",$sales_required);?>
									</td>
								</tr> 
						<?php 	$last_setting_info = $value['ProductSettingsForReport'];
								}else{
									$info = $WeeklySalesReportsController->getSettingsInfo($last_setting_info);
									$data_name = '';
									if($info != 'SMC'){
										$data_name = $info;
									}
									$first_item = $value['ProductSettingsForReport']['item'];
							?>
									<tr class="c_totalColumn">
										<td><b>Total <?=$data_name?>:</b></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$dhaka_east_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$dhaka_west_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$mymensingh_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$chittagong_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$cumilla_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$sylhet_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$khulna_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$kushtia_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$barishal_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$bogra_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$rajshahi_sales);?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$rangpur_sales);?></td>
										<td style="text-align:right;"><?php echo $product_sales_qty;?></td>
										<td style="text-align:right;"><?php echo $product_projection;?></td>
										<td style="text-align:right;"><?php echo floor($product_achive);?></td>
										<td style="text-align:right;"><?php echo floor($product_sales_per_day);?></td>
										<td style="text-align:right;"><?php echo $product_sales_remaining_per_day;?></td>
										<td style="text-align:right;"><?php echo sprintf("%01.2f",$product_sales_required_per_day);?></td>
									</tr>
									<?php 
										$dhaka_east_sales = 0;
										$dhaka_west_sales = 0;
										$mymensingh_sales = 0;
										$chittagong_sales = 0;
										$cumilla_sales = 0;
										$sylhet_sales = 0;
										$bogra_sales = 0;
										$rangpur_sales = 0;
										$rajshahi_sales = 0;
										$kushtia_sales = 0;
										$barishal_sales = 0;
										$khulna_sales = 0;
										$product_sales_qty = 0;
										$product_projection = 0;
										$product_achive = 0;
										$product_sales_per_day = 0;
										$product_sales_remaining_per_day = 0;
										$product_sales_required_per_day = 0;
									?>
									<tr class="rowDataSd">
									<td><?=$value['Product']['product_name']?></td>
									<td><?php if(array_key_exists(15, $value)){?>
										<?php echo $value[15]['pro_quantity'];
										$dhaka_east_sales = $dhaka_east_sales + $value[15]['pro_quantity'];
										$total_dhaka_east = $total_dhaka_east + $value[15]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(19, $value)){?>
										<?php echo $value[19]['pro_quantity'];
										$dhaka_west_sales = $dhaka_west_sales + $value[19]['pro_quantity'];
										$total_dhaka_west = $total_dhaka_west + $value[19]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(26, $value)){?>
										<?php echo $value[26]['pro_quantity'];
										$mymensingh_sales = $mymensingh_sales + $value[26]['pro_quantity'];
										$total_mymensingh = $total_mymensingh + $value[26]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(18, $value)){?>
										<?php echo $value[18]['pro_quantity'];
										$chittagong_sales =  $chittagong_sales + $value[18]['pro_quantity'];
										$total_chittagong =  $total_chittagong + $value[18]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(16, $value)){?>
										<?php echo $value[16]['pro_quantity'];
										$cumilla_sales = $cumilla_sales + $value[16]['pro_quantity'];
										$total_cumilla = $total_cumilla + $value[16]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									
									<td><?php if(array_key_exists(25, $value)){?>
										<?php echo $value[25]['pro_quantity'];
										$sylhet_sales = $sylhet_sales + $value[25]['pro_quantity'];
										$total_sylhet = $total_sylhet + $value[25]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(27, $value)){?>
										<?php echo $value[27]['pro_quantity'];
										$khulna_sales = $khulna_sales + $value[27]['pro_quantity'];
										$total_khulna = $total_khulna + $value[27]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(28, $value)){?>
										<?php echo $value[28]['pro_quantity'];
										$kushtia_sales = $kushtia_sales + $value[28]['pro_quantity'];
										$total_kushtia = $total_kushtia + $value[28]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(29, $value)){?>
										<?php echo $value[29]['pro_quantity'];
										$barishal_sales = $barishal_sales + $value[29]['pro_quantity'];
										$total_barishal = $total_barishal + $value[29]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(22, $value)){?>
										<?php echo $value[22]['pro_quantity'];
										$bogra_sales = $bogra_sales + $value[22]['pro_quantity'];
										$total_bogra = $total_bogra + $value[22]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(24, $value)){?>
										<?php echo $value[24]['pro_quantity'];
										$rajshahi_sales = $rajshahi_sales + $value[24]['pro_quantity'];
										$total_rajshahi = $total_rajshahi + $value[24]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td><?php if(array_key_exists(23, $value)){?>
										<?php echo $value[23]['pro_quantity'];
										$rangpur_sales = $rangpur_sales + $value[23]['pro_quantity'];
										$total_rangpur = $total_rangpur + $value[23]['pro_quantity'];
										?>
										<?php }else{ echo "-";}?>
									</td>
									<td style="text-align:right;">
										<?php if(!empty($value)){
										$total_sales = $value['total_qty'];
										$product_sales_qty = $product_sales_qty + $value['total_qty'];
										$total_sales_qty = $total_sales_qty + $value['total_qty'];
										echo sprintf("%01.2f", $value['total_qty']);
										}else{ echo '0.00';}?>
									</td>
									<!--other info-->
									<td style="text-align:right;">
										<?php 
											$target_quantity = $value['Product']['target_quantity'];
											$product_projection = $product_projection + $target_quantity;
											$total_projection = $total_projection + $target_quantity;
											echo $value['Product']['target_quantity'];
										?>
									</td>
									
								
									<td style="text-align:right;">
										<?php 
											$product_achive = $product_achive + $value['achive'];
											$total_achive = $total_achive + $value['achive'];
											echo sprintf("%01.2f",$value['achive']);
										?>
									</td>
								
									<td style="text-align:right;">
										<?php 
											$per_day_sales = $target_quantity/$total_working_days;
											$product_sales_per_day = $product_sales_per_day + $per_day_sales;
											$total_sales_per_day = $total_sales_per_day + $per_day_sales;
											echo floor($per_day_sales);?>
									</td>
									<td style="text-align:right;">
										<?php 
											$diff = $target_quantity - $total_sales;
											if($diff < 0){ $diff = $diff*(-1);}
											$product_sales_remaining_per_day = $product_sales_remaining_per_day + $diff;
											$total_sales_remaining_per_day = $total_sales_remaining_per_day + $diff;
											echo sprintf("%01.2f",$diff);
										?>
									</td>
									<td style="text-align:right;">
										<?php
											$sales_required = $diff/$remaining_days;
											$product_sales_required_per_day = $product_sales_required_per_day + $sales_required;
											$total_sales_required_per_day = $total_sales_required_per_day + $sales_required;
											echo sprintf("%01.2f",$sales_required);?>
									</td>
								</tr>
						<?php	
								$last_setting_info = $value['ProductSettingsForReport'];
								} 
							}
							$info = $WeeklySalesReportsController->getSettingsInfo($last_setting_info);
							$data_name = 'SMC';
							if($info != 'SMC'){
								$data_name = $info;
							}
						?>  
							<tr class="c_totalColumn">
								<td><b>Total <?=$data_name?>:</b></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$dhaka_east_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$dhaka_west_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$mymensingh_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$chittagong_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$cumilla_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$sylhet_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$khulna_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$kushtia_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$barishal_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$bogra_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$rajshahi_sales);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$rangpur_sales);?></td>
								<td style="text-align:right;"><?php echo $product_sales_qty;?></td>
								<td style="text-align:right;"><?php echo $product_projection;?></td>
								<td style="text-align:right;"><?php echo floor($product_achive);?></td>
								<td style="text-align:right;"><?php echo floor($product_sales_per_day);?></td>
								<td style="text-align:right;"><?php echo $product_sales_remaining_per_day;?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$product_sales_required_per_day);?></td>
							</tr>
							<tr class="c_totalColumn">
								<td><b>Total CYP:</b></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_dhaka_east_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_dhaka_west_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_mymensingh_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_chittagong_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_cumilla_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_sylhet_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_khulna_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_kushtia_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_barishal_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_bogra_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_rajshahi_cyp);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_rangpur_cyp);?></td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
							</tr>
							<tr class="c_totalColumn">
								<td><b>Total Revenue:</b></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_dhaka_east);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_dhaka_west);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_mymensingh);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_chittagong);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_cumilla);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_sylhet);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_khulna);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_kushtia);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_barishal);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_bogra);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_rajshahi);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_rangpur);?></td>
								<td style="text-align:right;"><?php echo $total_sales_qty;?></td>
								<td style="text-align:right;"><?php echo $total_projection;?></td>
								<td style="text-align:right;"><?php echo floor($total_achive);?></td>
								<td style="text-align:right;"><?php echo floor($total_sales_per_day);?></td>
								<td style="text-align:right;"><?php echo $total_sales_remaining_per_day;?></td>
								<td style="text-align:right;"><?php echo $total_sales_required_per_day;?></td>
							</tr>
							<tr class="c_totalColumn">
								<td><b>Total Effective Call:</b></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_dhaka_east_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_dhaka_west_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_mymensingh_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_chittagong_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_cumilla_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_sylhet_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_khulna_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_kushtia_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_barishal_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_bogra_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_rajshahi_eff);?></td>
								<td style="text-align:right;"><?php echo sprintf("%01.2f",$total_rangpur_eff);?></td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
								<td style="text-align:right;">-</td>
							</tr>
						</table>
						<script>
						<?php $total_v = '0,0'; ?>
						var totals_qty = [<?=$total_v?>];
						var totals_val = [<?=$total_v?>];
						$(document).ready(function(){

							var $dataRows = $("#sum_table tr:not('.c_totalColumn, .titlerow')");


							$dataRows.each(function() {
								$(this).find('.c_qty').each(function(i){    
									totals_qty[i]+=parseFloat( $(this).html());
								});
							});

							$("#sum_table .c_totalQty").each(function(i){  
								$(this).html(totals_qty[i]);
							});
							
							
							$dataRows.each(function() {
								$(this).find('.c_val').each(function(i){        
									totals_val[i]+=parseFloat( $(this).html());
								});
							});
							$("#sum_table .c_totalVal").each(function(i){  
								$(this).html(totals_val[i]);
							});
							

						});
						</script>
						<script>
						<?php $total_v = '0,0'; ?>
						var totals_qty = [<?=$total_v?>];
						var totals_val = [<?=$total_v?>];
						$(document).ready(function(){

							var $dataRows = $("#sum_table tr:not('.b_totalColumn, .titlerow')");


							$dataRows.each(function() {
								$(this).find('.b_qty').each(function(i){    
									totals_qty[i]+=parseFloat( $(this).html());
								});
							});

							$("#sum_table .b_totalQty").each(function(i){  
								$(this).html(totals_qty[i]);
							});
							
							
							$dataRows.each(function() {
								$(this).find('.b_val').each(function(i){        
									totals_val[i]+=parseFloat( $(this).html());
								});
							});
							$("#sum_table .b_totalVal").each(function(i){  
								$(this).html(totals_val[i]);
							});
							

						});
						</script>
						<script>
						<?php $total_v = '0,0'; ?>
						var totals_qty = [<?=$total_v?>];
						var totals_val = [<?=$total_v?>];
						$(document).ready(function(){

							var $dataRows = $("#sum_table tr:not('.s_totalColumn, .titlerow')");


							$dataRows.each(function() {
								$(this).find('.s_qty').each(function(i){    
									totals_qty[i]+=parseFloat( $(this).html());
								});
							});

							$("#sum_table .s_totalQty").each(function(i){  
								$(this).html(totals_qty[i]);
							});
							
							
							$dataRows.each(function() {
								$(this).find('.s_val').each(function(i){        
									totals_val[i]+=parseFloat( $(this).html());
								});
							});
							$("#sum_table .s_totalVal").each(function(i){  
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


<script type="text/javascript">
var fromdate = '';
$("#datepicker").datepicker( {
    format: "mm-yyyy",
    viewMode: "years", 
    minViewMode: "months"
});

$(document).ready(function(){
	var yesterday = new Date(new Date().setDate(new Date().getDate()-1));
	$('.date_to').datepicker({
	format: "dd-mm-yyyy",
	autoclose: true,
	todayHighlight: true,
	//startdate: fromdate,
	enddate: yesterday
});	
});
</script>
<script>
$('.outlet_type').on('ifChecked', function(event){
  //alert($(this).val()); // alert value
  $.ajax({
    type: "POST",
    url: '<?php echo BASE_URL;?>ProductSalesReports/get_category_list',
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