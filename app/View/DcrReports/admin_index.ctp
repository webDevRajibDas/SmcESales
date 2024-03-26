<?php
App::import('Controller', 'DcrReportsController');
$DcrController = new DcrReportsController;
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
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Dcr Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
			</div>


			<div class="box-body">

				<div class="search-box">
					<?php echo $this->Form->create('DcrReports', array('role' => 'form', 'action' => 'index')); ?>
					<table class="search">

						<tr>
							<td class="required" width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?></td>

							<td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?></td>
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


									<h3 style="margin:2px 0;">Sales Officer's Daily Call Report</h3>

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

								</div>


								<div style="float:left; width:100%; height:430px; overflow:scroll;">
									<table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
										<tbody>
											<tr class="titlerow">
												<th>Outlet</th>
												<th>Type</th>
												<?php foreach ($product_list as $pro_name) { ?>
													<th><?= $pro_name ?></th>
												<?php } ?>

												<th>Cash Sales</th>
												<th>Creadit<br>Sales</th>
												<th>Creadit<br>Collection</th>
												<th>LPC/Memo</th>
												<th>Average<br>LPC/DCR</th>
											</tr>


											<?php if ($results) { ?>

												<?php
												$total_cash_sales = 0;
												$total_creadit_sales = 0;
												$total_creadit_collection = 0;
												$total_discount = 0;
												$total_outlet = 0;
												$total_report_row = 0;
												$total_lpc = 0;
												foreach ($results as $market_name => $outlet_datas) {
													$total_report_row++;
												?>
													<tr style="background:#f9f9f9;">
														<td style="text-align:left; font-size:12px;" colspan="<?= count($product_list) + 6 ?>"><b>Market :- <?= $market_name ?></b></td>
														<?php if ($total_report_row == 1) { ?>
															<td class="average_lpc_row" rowspan="0">0</td>
														<?php } ?>
													</tr>

													<?php
													$sub_total = array();
													foreach ($outlet_datas as $outlet_name => $memo_datas) {
														$total_report_row++;
														$total_outlet++;
													?>
														<tr>
															<td><?= $outlet_name ?></td>

															<?php
															$outlet_category_name = '';
															$cash_sales = 0;

															$creadit_sales = 0;
															$creadit_collection = 0;
															$total_memo = 0;
															$total_product = 0;
															foreach ($memo_datas as $memo_no => $memo_info) {
																$outlet_category_name = $memo_info['memo']['outlet_category_name'];
																$total_memo++;
																$total_product += count($memo_info['memo_detial']);
																$cash_sales += $memo_info['memo']['cash_recieved'];
																$total_discount += $memo_info['memo']['total_discount'];
																$creadit_sales += $memo_info['memo']['credit_amount'];
																$creadit_collection += $memo_info['memo']['creadit_collection'];
															}
															$total_lpc += $total_product / $total_memo;
															?>

															<td><?= $outlet_category_name ?></td>

															<?php 
															$discount_value = 0;
															foreach ($product_list as $product_id => $pro_name) { ?>
																<td>
																	<?php
																	$sales_qty = 0;
																	foreach ($memo_datas as $memo_no => $memo_info) {
																		$sales_qty += @$memo_info['memo_detial'][$product_id]['sales_qty'];
																		$discount_value += @$memo_info['memo_detial'][$product_id]['discount_value'];
																	}
																	?>
																	<?= $sales_qty ? $sales_qty : '' ?>
																</td>
															<?php } 
															
																$cash_sales = $cash_sales - $discount_value;
															
															?>
															
															<td><?= sprintf("%01.2f", $cash_sales) ?></td>
															<td><?= sprintf("%01.2f", $creadit_sales) ?></td>
															<td><?= sprintf("%01.2f", $creadit_collection) ?></td>
															<td><?= sprintf("%01.2f", $total_product / $total_memo); // . '<br>' . $total_product . '---' . $total_memo 
																?></td>

														</tr>
													<?php
														$total_cash_sales += $cash_sales;
														$total_creadit_sales += $creadit_sales;
														$total_creadit_collection += $creadit_collection;
													}
													?>

												<?php } ?>



												<tr style="font-weight:bold; background:#f2f2f2;">
													<td colspan="<?= count($product_list) + 2; ?>" style="text-align:right;">Total :</td>
													<td><?= sprintf("%01.2f", $total_cash_sales) ?></td>
													<td><?= sprintf("%01.2f", $total_creadit_sales) ?></td>
													<td><?= sprintf("%01.2f", $total_creadit_collection) ?></td>
													<td><?= sprintf("%01.2f", $total_lpc) ?></td>
													<td></td>
												</tr>
												<tr style="font-weight:bold; background:#f2f2f2;">
													<td colspan="<?= count($product_list) + 2; ?>" style="text-align:right;">Total Discount:</td>
													<td><?= sprintf("%01.2f", $total_discount) ?></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>

												</tr>

											<?php } else { ?>

												<tr>
													<td colspan="<?= count($product_list) + 5 ?>"><b>No Data Found!</b></td>
												</tr>

											<?php } ?>


										</tbody>
									</table>

									<?php if ($results) { ?>
										<h2>Summary</h2>
										<table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
											<tbody>
												<tr class="titlerow">
													<th colspan="2"></th>
													<?php foreach ($product_list as $pro_name) { ?>
														<th><?= $pro_name ?></th>
													<?php } ?>

													<th colspan="3">Total</th>

												</tr>


												<tr>
													<td colspan="2">Stockist Sales</td>
													<?php
													$total_stockis = 0;
													
													//echo '<pre>';print_r($stockist_results);exit;
													
													foreach ($product_list as $product_id => $pro_name) {
													?>
														<td><?= @$stockist_results[$product_id]['sales_qty'] ?></td>
													<?php
														@$total_stockis += $stockist_results[$product_id]['sales_qty'];
													}
													?>
													<td colspan="3"><?= $total_stockis ?></td>
												</tr>

												<tr>
													<td colspan="2">Retailer Sales</td>
													<?php
													$total_retailer = 0;
													foreach ($product_list as $product_id => $pro_name) {
													?>
														<td><?= @$retailer_results[$product_id]['sales_qty'] ?></td>
													<?php
														@$total_retailer += $retailer_results[$product_id]['sales_qty'];
													}
													?>
													<td colspan="3"><?= $total_retailer ?></td>
												</tr>

												<tr>
													<td colspan="2">Total Sales</td>
													<?php 
														$total = 0;
														foreach ($product_list as $product_id => $pro_name) {  
														$total += $stockist_results[$product_id]['sales_qty'] + $retailer_results[$product_id]['sales_qty'];
													?>
														<td><?= @sprintf("%01.2f", ($stockist_results[$product_id]['sales_qty'] + $retailer_results[$product_id]['sales_qty'])) ?></td>
													<?php } ?>
													<td colspan="3"><?= @sprintf("%01.2f", $total);?></td>
												</tr>

												<tr>
													<td colspan="2">Bonus</td>
													<?php
													$total_bonus = 0;
													foreach ($product_list as $product_id => $pro_name) {
													?>
														<td><?= @$bonus_results[$product_id]['sales_qty'] ?></td>
													<?php
														@$total_bonus += $bonus_results[$product_id]['sales_qty'];
													}
													?>
													<td colspan="3"><?= $total_bonus ?></td>
												</tr>


												<tr style="font-weight:bold; background:#f9f9f9;">
													<td colspan="2"></td>
													<?php foreach ($outlet_categories as $outlet_categories_name) { ?>
														<td><?= $outlet_categories_name ?></td>
													<?php } ?>
													<td>Total</td>
												</tr>

												<tr>
													<td colspan="2">OC</td>
													<?php
													$total_oc = 0;
													foreach ($outlet_categories as $category_id => $categories_name) {
													?>
														<td><?= @$oc_results[$category_id]['oc'] ?></td>
													<?php
														@$total_oc += $oc_results[$category_id]['oc'];
													}
													?>
													<td><?= $total_oc ?></td>
												</tr>

												<tr>
													<td colspan="2">EC</td>
													<?php
													$total_ec = 0;
													foreach ($outlet_categories as $category_id => $categories_name) {
													?>
														<td><?= @$ec_results[$category_id]['ec'] ?></td>
													<?php
														@$total_ec += $ec_results[$category_id]['ec'];
													}
													?>
													<td><?= $total_ec ?></td>
												</tr>

											</tbody>
										</table>
									<?php } ?>

								</div>


								<?php /*?><div style="float:left; width:100%; padding:100px 0 50px;">
                                <div class="bottom_box">
                                    Prepared by:______________ 
                                </div>
                                <div class="bottom_box">
                                    Checked by:______________ 
                                </div>
                                <div class="bottom_box">
                                    Signed by:______________
                                </div>		  
                            </div><?php */ ?>


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
		url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
		type: 'post',
		data: {
			'office_id': 'office_id'
		}
	});
</script>

<script>
	$(document).ready(function() {
		<?php if ($results) { ?>
			var total_outlet = <?= $total_outlet ?>;
			var total_lpc = <?= $total_lpc ?>;
			var total_report_row = <?= $total_report_row ?>;
			$(".average_lpc_row").attr('rowspan', total_report_row);
			$(".average_lpc_row").text((total_lpc / total_outlet).toFixed(2));
		<?php } ?>
		$("input[type='checkbox']").iCheck('destroy');
		$("input[type='radio']").iCheck('destroy');
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
			<?php if (!@$request_data['DcrReports']['territory_id']) { ?>
				$('.office_t_so option:nth-child(1)').prop("selected", true).change();
			<?php } ?>

			<?php if (!@$request_data['DcrReports']['so_id']) { ?>
				$('#so_id option:nth-child(1)').prop("selected", true).change();
			<?php } ?>
		}


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

			a.download = "dcr_reports.xls";

			document.body.appendChild(a);

			a.click();

		});

	});
</script>