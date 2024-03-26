<?php
App::import('Controller', 'OutletCharacteristicReportsController');
$OutletCharacteristicController = new OutletCharacteristicReportsController;
?>


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
		width: 25%;
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

<style>
	#divLoading {
		display: none;
	}

	#divLoading.show {
		display: block;
		position: fixed;
		z-index: 100;
		background-image: url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
		background-color: #666;
		opacity: 0.4;
		background-repeat: no-repeat;
		background-position: center;
		left: 0;
		bottom: 0;
		right: 0;
		top: 0;
	}

	#loadinggif.show {
		left: 50%;
		top: 50%;
		position: absolute;
		z-index: 101;
		width: 32px;
		height: 32px;
		margin-left: -16px;
		margin-top: -16px;
	}
</style>

<div id="divLoading" class=""> </div>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">



			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title ?></h3>
				<?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New OutletCharacteristic Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
			</div>


			<div class="box-body">

				<div class="search-box">
					<?php echo $this->Form->create('OutletCharacteristicReports', array('role' => 'form', 'action' => 'index')); ?>
					<table class="search">

						<tr>
							<td class="required" width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?></td>

							<td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?></td>
						</tr>

						<tr>
							<td colspan="2">
								<?php echo $this->Form->input('report_type', array('legend' => 'Report Type :', 'class' => 'report_type', 'type' => 'radio', 'default' => 'visited', 'options' => $report_types, 'required' => true));  ?></td>
						</tr>

						<?php if ($office_parent_id == 0) { ?>
							<tr>
								<td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>
								<td></td>
							</tr>
						<?php } ?>


						<?php if ($office_parent_id == 14) { ?>
							<tr>
								<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>
								<td></td>
							</tr>
						<?php } ?>


						<?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
							<tr>
								<td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true, 'empty' => '---- All ----')); ?></td>
								<td></td>
							</tr>
						<?php } else { ?>
							<tr>
								<td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true)); ?></td>
								<td></td>
							</tr>
						<?php } ?>


						<tr>
							<td colspan="2">
								<?php echo $this->Form->input('type', array('legend' => 'Type :', 'class' => 'type', 'type' => 'radio', 'default' => 'territory', 'onClick' => 'typeChange(this.value)', 'options' => $types, 'required' => true));  ?>
							</td>
						</tr>

						<tr>
							<td>
								<div id="territory_html">
									<?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id office_t_so', 'required' => false, 'empty' => '---- All ----')); ?>
								</div>

								<div id="so_html">
									<?php echo $this->Form->input('so_id', array('label' => 'Sales Officers', 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'options' => $so_list, 'empty' => '---- All ----')); ?>
								</div>
							</td>
							<td></td>
						</tr>


						<tr>
							<td colspan="2">
								<?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
							</td>
						</tr>


						<tr>
							<td colspan="2">
								<label style="float:left; width:12.5%;">Outlet Category : </label>
								<div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
									<div style="margin:auto; width:90%; float:left; margin-left:-20px;">
										<input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
										<label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
									</div>
									<div class="selection">
										<?php echo $this->Form->input('outlet_category_id', array('id' => 'outlet_category_id', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $outlet_categories)); ?>
									</div>
								</div>
							</td>
						</tr>




						<tr>
							<td colspan="2">
								<label style="float:left; width:12.5%;">Districts : </label>
								<div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
									<div style="margin:auto; width:90%; float:left;">
										<input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall1" />
										<label for="checkall1" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
									</div>
									<div class="selection1 district_box box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?= ($districts) ? 'display:block' : '' ?>">
										<?php echo $this->Form->input('district_id', array('id' => 'district_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $districts)); ?>
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
									<div class="selection2 box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?= ($thanas) ? 'display:block' : '' ?>">
										<?php echo $this->Form->input('thana_id', array('id' => 'thana_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $thanas)); ?>
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
									<div class="selection3 box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?= ($markets) ? 'display:block' : '' ?>">
										<?php echo $this->Form->input('market_id', array('id' => 'market_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $markets)); ?>
									</div>
								</div>
							</td>
						</tr>


						<tr>
							<td colspan="2">
								<label style="float:left; width:12.5%;">Outlets : </label>
								<div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
									<div style="margin:auto; width:90%; float:left;">
										<input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall4" />
										<label for="checkall4" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
									</div>
									<div class="selection4 box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?= ($outlets) ? 'display:block' : '' ?>">
										<?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $outlets)); ?>
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


									<h3 style="margin:2px 0;"><?= $report_types[$report_type] ?></h3>

									<p>
										<b> Time Frame : <?= @date('d M, Y', strtotime($date_from)) ?> to <?= @date('d M, Y', strtotime($date_to)) ?></b>
									</p>

									<p>
										<?php if ($region_office_id) { ?>
											<span>Region Office: <?= $region_offices[$region_office_id] ?></span>
										<?php } else { ?>
											<span>Head Office</span>
										<?php } ?>
										<?php if ($office_id) { ?>
											<span>, Area Office: <?= $offices[$office_id] ?></span>
										<?php } ?>
										<?php if ($territory_id) { ?>
											<span>, Territory Name: <?= $territories[$territory_id] ?></span>
										<?php } ?>
									</p>

									<?php if ($report_type == 'detail' || $report_type == 'summary') { ?>
										<p><b>Measuring Unit: <?= $unit_type_text ?></b></p>
									<?php } ?>

								</div>

								<div style="float:left; width:100%; height:430px; overflow:scroll;">

									<?php if ($report_type == 'visited') { ?>
										<table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
											<tbody>
												<tr class="titlerow">
													<th style="text-align:left;">Outlet</th>
													<th style="text-align:left;">Outlet Type</th>
													<th>No of Visited Day</th>
													<th width="30%">Visit Date</th>
													<th style="text-align:right;">Memo Total</th>
													<th style="text-align:left;">Visited By</th>
												</tr>


												<?php if ($results) { ?>
													<?php /*?><?php foreach($results as $district_name => $result){ ?>
                                        <tr>
                                          <td style="text-align:left;" colspan="6"><b>District :- <?=$district_name?></b></td>
                                        </tr>
                                        
                                        <?php foreach($result as $thana_name => $market_data){ ?>
                                        <tr>
                                          <td style="text-align:left;" colspan="6"><b>Thana :- <?=$thana_name?> </b></td>
                                        </tr>
                                        
                                            <?php foreach($market_data as $market_name => $outlet_data){ ?>
                                            <tr>
                                              <td style="text-align:left;" colspan="6"><b>Market :- <?=$market_name?></b></td>
                                            </tr>
                                            <tr>
                                                </tr>
                                                <?php foreach($outlet_data as $outlet_name => $memo_data){ ?>
                                                <tr>
                                                  <td><?=$outlet_name?></td>
                                                  <td><?=$outlet_categories[$outlet_category_id]?></td>
                                                  <td><?=count($memo_data)?></td>
                                                  <td> 
                                                    <?php 
                                                    foreach($memo_data as $m_result){
                                                        $so_name =  $m_result['so_name'];
                                                        echo $m_result['memo_date'].', ';
                                                    }
                                                    ?>
                                                    </td>
                                                  <td style="text-align:right;">200.00</td>
                                                  <td><?=$so_name?></td>
                                                </tr>
                                                <?php } ?>
                                                
                                            <tr>
                                              <td style="text-align:right;" colspan="4"><b>Market Wise Summary :- Total Outlet: 120, Visited Outlets: 15, Memo Total:</b></td>
                                              <td style="text-align:right;"><b>350.00</b></td>
                                              <td colspan="3"></td>
                                            </tr>
                                            <?php } ?>
                                        
                                        <tr>
                                          <td style="text-align:right;" colspan="4"><b>Thana Wise Summary :- Total Outlet: 120, Visited Outlets: 15, Memo Total:</b></td>
                                          <td style="text-align:right;"><b>750.00</b></td>
                                          <td colspan="3"></td>
                                        </tr>
                                        <?php } ?>
                                    
                                    <?php } ?><?php */ ?>
													<?= $output ?>
												<?php } else { ?>
													<tr>
														<td colspan="6">No data found!</td>
													</tr>
												<?php } ?>




											</tbody>
										</table>
									<?php } ?>


									<?php if ($report_type == 'non_visited') { ?>
										<table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
											<tbody>
												<tr class="titlerow">
													<th style="text-align:left;">Market</th>
													<th style="text-align:left;">Outlet Name</th>
													<th style="text-align:left;">Outlet Type</th>
												</tr>

												<?php if ($results) { ?>
													<?= $output ?>
												<?php } else { ?>

													<tr>
														<td colspan="3">No Data Found!</td>
													</tr>

												<?php } ?>


											</tbody>
										</table>
									<?php } ?>


									<?php if ($report_type == 'detail') { ?>
										<table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
											<tbody>
												<tr class="titlerow">
													<th style="text-align:left;">Outlet</th>
													<th>Memo No</th>
													<th width="10%">Date</th>
													<th style="text-align:left;">Product</th>
													<th style="text-align:right;">Qty</th>
													<th style="text-align:right;">Total Value</th>
													<th style="text-align:left;">Sales Officer</th>
												</tr>


												<?php if ($results) { ?>

													<?= $output ?>

													<?php /*?><?php 
										$grand_total = 0;
										foreach($results as $market_name => $outlet_datas)
										{ 
										?>
                                            <tr>
                                              <td style="text-align:left; font-size:15px;" colspan="7"><b>Market :- <?=$market_name?></b></td>
                                            </tr>
                                        
                                        	<?php 
											$market_total = 0;
											foreach($outlet_datas as $outlet_name => $memo_datas)
											{ 
											?>
                                            
                                            	<?php 
												$outlet_total = 0;
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
                                                      <td><?=$i==1?$outlet_name:''?></td>
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
													$outlet_total+= $memo_total;
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
                                                  <td style="text-align:right;"><b><?=sprintf("%01.2f", $outlet_total)?></b></td>
                                                  <td colspan="3"></td>
                                                </tr>
                                            <?php 
											$market_total+= $outlet_total;
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
                                        </tr><?php */ ?>

												<?php } else { ?>

													<tr>
														<td colspan="7"><b>No Data Found!</b></td>
													</tr>

												<?php } ?>


											</tbody>
										</table>
									<?php } ?>


									<?php if ($report_type == 'summary') { ?>
										<table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
											<tbody>
												<tr class="titlerow">
													<th style="text-align:left;">Outlet</th>

													<?php foreach ($product_list as $pro_name) { ?>
														<th style="text-align:left;"><?= $pro_name ?></th>
													<?php } ?>
												</tr>

												<?php if ($results) { ?>

													<?php /*?><?php 
										$grand_total = array();
										foreach($results as $market_name => $outlet_datas){ 
										?>
                                        <tr>
                                          <td style="text-align:left; font-size:12px;" colspan="<?=count($product_list)?>"><b>Market :- <?=$market_name?></b></td>
                                        </tr>
                                        
											<?php 
											$sub_total = array(); 
											foreach($outlet_datas as $outlet_name => $pro_datas){ 
											?>
                                            <tr>
                                              <td><?=$outlet_name?></td>
                                              <?php
											  foreach($product_list as $product_id => $pro_name)
											  { 
											  ?>
                                              <td><?=@$pro_datas[$product_id]['sales_qty']?></td>
                                              <?php 
											  @$sub_total[$product_id]+= $pro_datas[$product_id]['sales_qty']?$pro_datas[$product_id]['sales_qty']:0;
											  } 
											  ?>
                                            </tr>
                                            <?php } ?>
                                        
                                         <tr style="font-weight:bold; background:#f2f2f2;">
                                          <td>Sub Total</td>
                                          <?php foreach($product_list as $product_id => $pro_name){ ?>
                                          <td><?=sprintf("%01.2f", $sub_total[$product_id])?></td>
                                          <?php 
										  	@$grand_total[$product_id]+=$sub_total[$product_id];
										  } 
										  ?>
                                        </tr>
                                        
                                        <?php 
										} 
										?>
                                        
                                        <tr style="font-weight:bold; background:#ccc;">
                                          <td>Grand Total</td>
                                          <?php foreach($product_list as $product_id => $pro_name){ ?>
                                          <td><?=sprintf("%01.2f", $grand_total[$product_id])?></td>
                                          <?php } ?>
                                        </tr><?php */ ?>

													<?= $output; ?>

												<?php } else { ?>

													<tr>
														<td colspan="<?= count($product_list) ?>"><b>No Data Found!</b></td>
													</tr>

												<?php } ?>


											</tbody>
										</table>
									<?php } ?>

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
		value: 'name',
		url: '<?= BASE_URL . 'market_characteristic_reports/get_office_list'; ?>',
		type: 'post',
		data: {
			'region_office_id': 'region_office_id'
		}
	});
	$('.region_office_id').change(function() {
		$('#territory_id').html('<option value="">---- All ----');
	});
	$('.office_id').selectChain({
		target: $('.territory_id'),
		value: 'name',
		url: '<?= BASE_URL . 'sales_people/get_territory_list_new' ?>',
		type: 'post',
		data: {
			'office_id': 'office_id'
		}
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
			outletBoxList();
		});

		$('#checkall4').click(function() {
			var checked = $(this).prop('checked');
			$('.selection4').find('input:checkbox').prop('checked', checked);
		});

	});

	$('#office_id').change(function() {
		//alert($(this).val());
		date_from = $('.date_from').val();
		date_to = $('.date_to').val();
		if (date_from && date_to) {
			$.ajax({
				type: "POST",
				url: '<?= BASE_URL ?>market_characteristic_reports/get_office_so_list',
				data: 'office_id=' + $(this).val() + '&date_from=' + date_from + '&date_to=' + date_to,
				cache: false,
				success: function(response) {
					//alert(response);						
					$('#so_id').html(response);
				}
			});
		} else {
			$('#office_id option:nth-child(1)').prop("selected", true);
			alert('Please select date range!');
		}
	});


	$(document).ready(function() {
		typeChange();
	});


	function typeChange() {
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
	<?php } ?><?php */ ?>

		if (type == 'so') {
			$('#so_html').show();
		} else {
			$('#territory_html').show();
		}

		if (type == 'so') {
			$('.office_t_so option:nth-child(1)').prop("selected", true).change();
		} else if (type == 'territory') {
			$('#so_id option:nth-child(1)').prop("selected", true).change();
		} else {
			<?php if (!@$request_data['OutletCharacteristicReports']['territory_id']) { ?>
				$('.office_t_so option:nth-child(1)').prop("selected", true).change();
			<?php } ?>

			<?php if (!@$request_data['OutletCharacteristicReports']['so_id']) { ?>
				$('#so_id option:nth-child(1)').prop("selected", true).change();
			<?php } ?>
		}


	}
</script>



<script>
	$(document).ready(function() {
		$('#region_office_id').change(function() {
			districtBoxList();
		});
		$('#office_id').change(function() {
			districtBoxList();
		});
		$('#territory_id').change(function() {
			districtBoxList();
		});
	});


	function districtBoxList() {
		$('#checkall1').removeAttr("checked");
		$('#checkall2').removeAttr("checked");
		$('#checkall3').removeAttr("checked");

		$('.box_area').hide();
		$('.box_area').html('');



		var region_office_id = $('#region_office_id').val() ? $('#region_office_id').val() : 0;
		var office_id = $('#office_id').val() ? $('#office_id').val() : 0;
		var territory_id = $('#territory_id').val() ? $('#territory_id').val() : 0;

		//alert(region_office_id);

		$.ajax({
			type: "POST",
			url: '<?php echo BASE_URL; ?>outlet_characteristic_reports/get_district_list',
			data: 'region_office_id=' + region_office_id + '&office_id=' + office_id + '&territory_id=' + territory_id,
			beforeSend: function() {
				$("div#divLoading").addClass('show');
			},
			cache: false,
			success: function(response) {
				//alert(response);
				if (response != '') {
					$('.selection1').show();
				}
				$('.selection1').html(response);
				$("div#divLoading").removeClass('show');
			}
		});
	}
</script>


<script>
	$(document).ready(function() {
		$('[name="data[OutletCharacteristicReports][district_id][]"]').change(function() {
			//alert($(this).val()); // alert value
			//$('.selection').find('input:checkbox').prop('checked', checked);
			thanaBoxList();
		});
	});

	function thanaBoxList() {
		var val = [];
		$('[name="data[OutletCharacteristicReports][district_id][]"]:checked').each(function(i) {
			val[i] = $(this).val();
		});

		//alert(val);
		$('.selection2').hide();

		$('.selection3').hide();
		$('.selection3').html('');
		$('.selection4').hide();
		$('.selection4').html('');

		$.ajax({
			type: "POST",
			url: '<?php echo BASE_URL; ?>outlet_characteristic_reports/get_thana_list',
			data: 'district_id=' + val + '&territory_id=' + $('#territory_id').val(),
			beforeSend: function() {
				$("div#divLoading").addClass('show');
			},
			cache: false,
			success: function(response) {
				//alert(response);	
				if (response != '') {
					$('.selection2').show();
				}
				$('.selection2').html(response);
				$("div#divLoading").removeClass('show');
			}
		});
	}
</script>

<script>
	$(document).ready(function() {
		$('[name="data[OutletCharacteristicReports][thana_id][]"]').change(function() {
			//alert($(this).val()); // alert value
			//$('.selection').find('input:checkbox').prop('checked', checked);
			marketBoxList();
		});
	});

	function marketBoxList() {
		var val = [];
		$('[name="data[OutletCharacteristicReports][thana_id][]"]:checked').each(function(i) {
			val[i] = $(this).val();
		});

		$('.selection3').hide();

		$('.selection4').hide();
		$('.selection4').html('');

		$.ajax({
			type: "POST",
			url: '<?php echo BASE_URL; ?>outlet_characteristic_reports/get_market_list',
			data: 'thana_id=' + val + '&territory_id=' + $('#territory_id').val(),
			beforeSend: function() {
				$("div#divLoading").addClass('show');
			},
			cache: false,
			success: function(response) {
				//alert(response);	
				if (response != '') {
					$('.selection3').show();
				}
				$('.selection3').html(response);
				$("div#divLoading").removeClass('show');
			}
		});
	}
</script>


<script>
	$(document).ready(function() {
		$('[name="data[OutletCharacteristicReports][market_id][]"]').change(function() {
			//alert($(this).val()); // alert value
			//$('.selection').find('input:checkbox').prop('checked', checked);
			outletBoxList();
		});
	});

	function outletBoxList() {
		var val = [];
		$('[name="data[OutletCharacteristicReports][market_id][]"]:checked').each(function(i) {
			val[i] = $(this).val();
		});

		$('.selection4').hide();

		$.ajax({
			type: "POST",
			url: '<?php echo BASE_URL; ?>outlet_characteristic_reports/get_outlet_list',
			data: 'market_id=' + val,
			beforeSend: function() {
				$("div#divLoading").addClass('show');
			},
			cache: false,
			success: function(response) {
				//alert(response);	
				if (response != '') {
					$('.selection4').show();
				}
				$('.selection4').html(response);
				$("div#divLoading").removeClass('show');
			}
		});
	}
</script>


<script>
	function PrintElem(elem) {
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

			a.download = "outlet_characteristic_reports.xls";

			document.body.appendChild(a);

			a.click();

		});

	});
</script>