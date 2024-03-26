<?php
App::import('Controller', 'BonusCampaignReportsController');
$BonusCampaignReports = new BonusCampaignReportsController;
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

	#market_list .radio label {
		padding-left: 0px;
		width: auto;
	}

	#market_list .radio {
		width: 100%;
		float: left;
		margin: 1px 0;
	}
</style>

<style>
	#loading {
		position: absolute;
		width: auto;
		height: auto;
		text-align: center;
		top: 45%;
		left: 50%;
		display: none;
		z-index: 999;
	}

	#loading img {
		display: inline-block;
		height: auto;
		width: auto;
	}
</style>

<div class="modal" id="myModal" data-backdrop="static" data-keyboard="false"></div>
<div id="loading">
	<?php echo $this->Html->image('load.gif'); ?>
</div>

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
					<?php echo $this->Form->create('BonusCampaignReports', array('role' => 'form', 'action' => 'index')); ?>
					<table class="search">

						<tr>
							<td class="required" width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?>
							</td>
							<td class="required" width="50%">
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?>
							</td>
						</tr>
						<tr>
							<?php if ($office_parent_id == 0) { ?>

								<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>

							<?php } else { ?>

								<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>

							<?php } ?>


							<?php if ($office_parent_id == 0) { ?>

								<td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office  :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- All ----')); ?></td>

							<?php } else { ?>

								<td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true)); ?></td>

							<?php } ?>

						</tr>
						<tr>
							<td class="required">
								<?php echo $this->Form->input('mother_product_id', array('label' => 'Mother Product :', 'class' => 'form-control product_id', 'required' => true, 'options' => $productList, 'empty' => '---- Select ---')); ?>
							</td>
							<td>
								<?php echo $this->Form->input('unit_types', array('legend' => 'Unit Type :', 'class' => 'rows', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $this->Form->input('rows', array('legend' => 'Rows :', 'class' => 'rows', 'type' => 'radio', 'default' => 'national', 'options' => $rows_list, 'required' => true));  ?>
							</td>
							<td class="discount_bonus_policy_list">

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
								table-layout: auto;
							}

							.table-responsive {
								min-height: 0.01%;
								overflow-x: auto;
							}


							/* .report_table th:nth-child(1),
							.report_table td:nth-child(1) {
								width: 50%;
							} */
						</style>

						<div class="table-responsive">

							<div class="pull-right csv_btn" style="padding-top:20px;">
								<?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
							</div>

							<div id="xls_body">
								<div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
									<h2 style="margin:2px 0;">SMC Enterprise Limited</h2>

									<p>

										<b> Time Frame (Policy Start and End date): <?= @date('Y-m-d', strtotime($date_from)) . ' To ' . @date('Y-m-d', strtotime($date_to)) ?> </b>
									</p>

									<p>
										<?php if ($region_office_id) { ?>
											<span>Region Office: <?= $region_offices[$region_office_id] ?></span>
										<?php } else { ?>
											<span>Head Office</span>
										<?php } ?>
										<?php if ($request_office_id) { ?>
											<span>, Area Office: <?= $offices[$request_office_id] ?></span>
										<?php } ?>

									</p>
									<p>
										<span> Unit: <?= $unit_types[$unit_types_req] ?></span>
										<span>, Report Print Time: <?= date("Y-m-d h:i a") ?></span>
									</p>

								</div>

								<div style="float:left; width:100%; height:430px; overflow:scroll;">
									<?php if ($bonus_data) { ?>
										<table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
											<thead>
												<tr>
													<?php if ($rows == 'national') {
														echo '<th rowspan="2">National</th>';
													}
													if ($rows == 'region' || $rows == 'area' || $rows == 'territory') {
														echo '<th rowspan="2">Region Office</th>';
													}
													if ($rows == 'area' || $rows == 'territory') {
														echo '<th rowspan="2">Area Office</th>';
													}
													if ($rows == 'territory') {
														echo '<th rowspan="2">Territory</th>';
													} ?>
													<th colspan="<?php echo count($policy_products) ?>">Mother Product QTY</th>
													<th rowspan="2">EC</th>
													<th rowspan="2">OC</th>
													<th rowspan="2">Value</th>
													<th colspan="<?php echo count($policy_bonus_products) ?>">Bonus Product QTY</th>
												</tr>
												<tr>
													<?php foreach ($policy_products as $pid => $pname) {
														echo '<th>' . $pname . '</th>';
													} ?>
													<?php foreach ($policy_bonus_products as $pid => $pname) {
														echo '<th>' . $pname . '</th>';
													} ?>
												</tr>
											</thead>
											<tbody>
												<?php
												$region_ec_total = 0;
												$region_oc_total = 0;
												$region_value_total = 0;
												$region_main_product_total = array();
												$region_bonus_product_total = array();

												$area_ec_total = 0;
												$area_oc_total = 0;
												$area_value_total = 0;
												$area_main_product_total = array();
												$area_bonus_product_total = array();

												$ec_total = 0;
												$oc_total = 0;
												$value_total = 0;
												$main_product_total = array();
												$bonus_product_total = array();
												$region_office_id = '';
												$area_office_id = '';
												?>
												<?php foreach ($bonus_data as $data) { ?>
													<?php
													if (($rows == 'area' || $rows == 'territory') && !$region_office_id) {
														$region_office_id =  $data['RegionOffice']['id'];
													}
													if ($rows == 'territory' && !$area_office_id) {
														$area_office_id =  $data['Office']['id'];
													}
													?>
													<!-- Area total : start -->
													<?php if (($rows == 'territory') &&  $area_office_id !=  $data['Office']['id']) { ?>
														<tr>
															<td colspan="3"> Area Total </td>
															<?php
															foreach ($policy_products as $pid => $pname) {
																echo '<td>' . $area_main_product_total[$pid] . '</td>';
															}
															?>
															<td><?= $area_ec_total ?></td>
															<td><?= $area_oc_total ?></td>
															<td><?= $area_value_total ?></td>
															<?php
															foreach ($policy_bonus_products as $pid => $pname) {
																echo '<td>' . $area_bonus_product_total[$pid] . '</td>';
															}
															?>
														</tr>
													<?php
														$area_office_id =  $data['Office']['id'];
														$area_ec_total = 0;
														$area_oc_total = 0;
														$area_value_total = 0;
														$area_main_product_total = array();
														$area_bonus_product_total = array();
													}
													?>
													<!-- Area total : end -->
													<!-- region total : start -->
													<?php if (($rows == 'area' || $rows == 'territory') && $region_office_id !=  $data['RegionOffice']['id']) { ?>
														<tr>
															<td colspan="<?php if ($rows == 'territory') echo 3;
																			else if ($rows == 'area') echo 2; ?>"> Region Total </td>
															<?php
															foreach ($policy_products as $pid => $pname) {
																echo '<td>' . $region_main_product_total[$pid] . '</td>';
															}
															?>
															<td><?= $region_ec_total ?></td>
															<td><?= $region_oc_total ?></td>
															<td><?= $region_value_total ?></td>
															<?php
															foreach ($policy_bonus_products as $pid => $pname) {
																echo '<td>' . $region_bonus_product_total[$pid] . '</td>';
															}
															?>
														</tr>
													<?php
														$region_office_id =  $data['RegionOffice']['id'];
														$region_ec_total = 0;
														$region_oc_total = 0;
														$region_value_total = 0;
														$region_main_product_total = array();
														$region_bonus_product_total = array();
													}
													?>
													<!-- region total : end -->
													<tr>
														<?php if ($rows == 'national') {
															echo '<td>National</td>';
														}
														if ($rows == 'region' || $rows == 'area' || $rows == 'territory') {
															echo '<td >' . $data['RegionOffice']['office_name'] . '</td>';
														}
														if ($rows == 'area' || $rows == 'territory') {
															echo '<td>' . $data['Office']['office_name'] . '</td>';
														}
														if ($rows == 'territory') {
															echo '<td>' . $data['Territory']['name'] . '</td>';
														} ?>
														<?php
														$value = 0;
														foreach ($policy_products as $pid => $pname) {
															$value += $data[0]['value_' . $pid];
															$sales_qty = ($unit_types_req == 2 ? $data[0]['sales_qty_' . $pid] : $BonusCampaignReports->unit_convertfrombase($pid, $policy_all_product_measurement_unit[$pid], $data[0]['sales_qty_' . $pid]));
															$region_main_product_total[$pid] = @$region_main_product_total[$pid] + $sales_qty;
															$area_main_product_total[$pid] = @$area_main_product_total[$pid] + $sales_qty;
															$main_product_total[$pid] = @$main_product_total[$pid] + $sales_qty;
															echo '<td>' . $sales_qty . '</td>';
														}
														?>
														<td>
															<?php
															$ec = 0;
															if ($rows == 'territory') {
																$ec = $ec_od_data[$data['Territory']['id']]['ec'];
															} else if ($rows == 'area') {
																$ec = $ec_od_data[$data['Office']['id']]['ec'];
															} else if ($rows == 'region') {
																$ec = $ec_od_data[$data['RegionOffice']['id']]['ec'];
															} else if ($rows == 'national') {
																$ec = $ec_od_data[0]['ec'];
															}
															echo $ec;
															?>
														</td>
														<td>
															<?php
															$oc = 0;
															if ($rows == 'territory') {
																$oc = $ec_od_data[$data['Territory']['id']]['oc'];
															} else if ($rows == 'area') {
																$oc = $ec_od_data[$data['Office']['id']]['oc'];
															} else if ($rows == 'region') {
																$oc = $ec_od_data[$data['RegionOffice']['id']]['oc'];
															} else if ($rows == 'national') {
																$oc = $ec_od_data[0]['oc'];
															}
															echo $oc;
															?>
														</td>
														<td><?= $value ?></td>
														<?php
														$region_ec_total += $ec;
														$region_oc_total += $oc;
														$region_value_total +=  $value;

														$area_ec_total += $ec;
														$area_oc_total += $oc;
														$area_value_total +=  $value;

														$ec_total += $ec;
														$oc_total += $oc;
														$value_total +=  $value;
														?>
														<?php foreach ($policy_bonus_products as $pid => $pname) {
															$b_sales_qty = ($unit_types_req == 2 ? $data[0]['b_sales_qty_' . $pid] : $BonusCampaignReports->unit_convertfrombase($pid, $policy_all_product_measurement_unit[$pid], $data[0]['b_sales_qty_' . $pid]));
															$region_bonus_product_total[$pid] = @$region_bonus_product_total[$pid] + $b_sales_qty;
															$area_bonus_product_total[$pid] = @$area_bonus_product_total[$pid] + $b_sales_qty;
															$bonus_product_total[$pid] = @$bonus_product_total[$pid] + $b_sales_qty;
															echo '<td>' . $b_sales_qty . '</td>';
														} ?>
													</tr>

												<?php } ?>
												<!-- Area total : start -->
												<?php if (($rows == 'territory')) { ?>
													<tr>
														<td colspan="3"> Area Total </td>
														<?php
														foreach ($policy_products as $pid => $pname) {
															echo '<td>' . $area_main_product_total[$pid] . '</td>';
														}
														?>
														<td><?= $area_ec_total ?></td>
														<td><?= $area_oc_total ?></td>
														<td><?= $area_value_total ?></td>
														<?php
														foreach ($policy_bonus_products as $pid => $pname) {
															echo '<td>' . $area_bonus_product_total[$pid] . '</td>';
														}
														?>
													</tr>
												<?php
												}
												?>
												<!-- Area total : end -->
												<!-- region total : start -->
												<?php if (($rows == 'area' || $rows == 'territory')) { ?>
													<tr>
														<td colspan="<?php if ($rows == 'territory') echo 3;
																		else if ($rows == 'area') echo 2; ?>"> Region Total </td>
														<?php
														foreach ($policy_products as $pid => $pname) {
															echo '<td>' . @$region_main_product_total[$pid] . '</td>';
														}
														?>
														<td><?= @$region_ec_total ?></td>
														<td><?= @$region_oc_total ?></td>
														<td><?= @$region_value_total ?></td>
														<?php
														foreach ($policy_bonus_products as $pid => $pname) {
															echo '<td>' . @$region_bonus_product_total[$pid] . '</td>';
														}
														?>
													</tr>
												<?php
												}
												?>
												<!-- region total : end -->
												<!-- Total :start -->
												<tr>
													<td colspan="<?php if ($rows == 'territory') echo 3;
																	else if ($rows == 'area') echo 2; ?>"> Total </td>
													<?php
													foreach ($policy_products as $pid => $pname) {
														echo '<td>' . @$main_product_total[$pid] . '</td>';
													}
													?>
													<td><?= $ec_total ?></td>
													<td><?= $oc_total ?></td>
													<td><?= $value_total ?></td>
													<?php
													foreach ($policy_bonus_products as $pid => $pname) {
														echo '<td>' . @$bonus_product_total[$pid] . '</td>';
													}
													?>
												</tr>
												<!-- Total :end -->
											</tbody>
										</table>
									<?php } else { ?>
										<p> No data found!!!</p>
									<?php } ?>
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
		value: 'name',
		url: '<?= BASE_URL . 'market_characteristic_reports/get_office_list'; ?>',
		type: 'post',
		data: {
			'region_office_id': 'region_office_id'
		},
		afterSuccess: function() {
			<?php if (@$request_office_id) { ?>
				$(".office_id").val(<?= @$request_office_id ?>);
			<?php } ?>
		}
	});
	$(document).ready(function() {
		$(".date_from,.date_to,.product_id").change(function(e) {
			var date_from = $(".date_from").val();
			var date_to = $(".date_to").val();
			var product_id = $(".product_id").val();
			if (date_from && date_to && product_id) {

				$.ajax({
					url: "<?php echo BASE_URL; ?>/BonusCampaignReports/get_policy_list",
					data: {
						'date_from': date_from,
						'date_to': date_to,
						'product_id': product_id
					},
					type: 'POST',
					async: false,
					beforeSend: function() {
						$('#myModal').modal('show');
						$('#loading').show();
						$('.discount_bonus_policy_list').html('');
					},
					success: function(res) {
						$('#myModal').modal('hide');
						$('#loading').hide();
						if ($('.discount_bonus_policy_list').html(res)) {
							<?php if (isset($policy_id) && $policy_id) { ?>
								$(".policy_id[value='<?= $policy_id ?>']").attr('checked', true);
							<?php } ?>
						}
					}
				});
			}
		});
		$(".product_id").trigger('change');
		$(".region_office_id").trigger('change');
		$('body').on('click', 'input[name="data[policy_id]"]', function(e) {
			$('input[name="data[policy_id]"]').prop('checked', false);
			$(this).prop('checked', true);
		});
	});
</script>




<script>
	$(document).ready(function() {
		$("#download_xl").click(function(e) {
			e.preventDefault();
			var html = $("#xls_body").html();
			var blob = new Blob([html], {
				type: 'data:application/vnd.ms-excel'
			});
			var downloadUrl = URL.createObjectURL(blob);
			var a = document.createElement("a");
			a.href = downloadUrl;
			a.download = "bonus_campaign_reports.xls";
			document.body.appendChild(a);
			a.click();
		});
	});
</script>