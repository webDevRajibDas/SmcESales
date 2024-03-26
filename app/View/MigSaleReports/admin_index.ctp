<?php
//App::import('Controller', 'MigSaleReportsController');
//$SalesAnalysisController = new MigSaleReportsController;
?>


<style>
	.search label {
		width: 20%;
	}

	.row_list div.list,
	.input.radio {
		float: left;
		width: 50%;
		margin: 0px;
	}

	.row_list label {
		width: auto;
	}

	.row_list input.form-control {
		width: auto;
		margin: 0 !important;
	}

	.list label,
	.input.radio label {
		cursor: pointer;
		font-weight: 400;
		margin-bottom: 0;
		min-height: 20px;
		padding-left: 16px;
	}

	.input.radio label {
		padding-left: 28px;
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
		width: 20%;
		float: left;
		margin: 1px 0;
	}

	body .td_rank_list .checkbox {
		width: auto !important;
		padding-left: 20px !important;
	}

	/*.td_rank_list #rank_list label{
	clear:right;
	width:50% !important;
}*/
	.so_list .checkbox {
		width: 50% !important;
		float: left;
		margin: 1px 0;
	}

	.search .date_field .form-control {
		width: 40%;
	}

	.date_field label {
		width: 40%;
	}

	td {
		padding: 5px;
	}

	td.left {
		border-right: #c7c7c7 solid 1px;
	}

	.outlet_category {
		float: right;
		width: 85%;
		padding-left: 10%;
		border: #c7c7c7 solid 1px;
		height: 100px;
		overflow: auto;
		margin-right: 5%;
		padding-top: 5px;
	}

	.outlet_category2 {
		padding-left: 3%;
		height: 118px;
	}

	.outlet_category3 {
		width: 92%;
		margin-right: 3%;
		height: 115px;
	}

	.outlet_category .checkbox {
		float: left;
		width: 100% !important;
	}

	.outlet_category3 .checkbox {
		float: left;
		width: 50% !important;
	}

	.label_title {
		float: right;
		width: 85%;
		background: #c7c7c7;
		margin: 0px;
		padding: 1px 0px;
		text-align: center;
		color: #000;
		margin-right: 5%;
		font-weight: bold;
		font-size: 90%;
	}

	.pro_label_title {
		width: 92%;
		margin-right: 3%;
	}

	.outlet_category label {
		width: auto;
	}


	.outlet_category .form-control {
		width: 55% !important;
	}

	.outlet_category2 .input {
		padding-bottom: 3px;
		float: left;
		width: 100%;
	}

	.outlet_category2 label {
		width: 32%;
	}

	.selection_box {
		display: none;
	}

	/*.condition{
	border-right:#ccc solid 1px;
}
.c_first{
	border-top:#ccc solid 1px;
}
.c_last{
	border-bottom:#ccc solid 1px;
}
h4.border_title{
	padding-left:5%;
	font-size: 16px;
    margin-top: -20px;
}
h4.border_title span{
	padding:0 1%;
	background:#fff;
	float:none;
}

.left .left_div{
	float:left;
	width:98%;
	border:#ccc solid 1px;
	padding-top:43px;
	height:143px;
	margin-top:-5px;
}
.left .left_div h4.border_title{
	margin-top:-54px;
}
*/
	.left_div {
		float: left;
		width: 98%;
		border: #ccc solid 1px;
		padding-top: 43px;
		height: 143px;
		margin-top: -5px;
	}

	.search-box label {
		width: 35%;
	}

	.left_div label {
		width: 40%;
	}

	.search-box .form-control {
		width: 40%;
	}

	.filter_left_div {
		padding-left: 0px;
	}

	.data_box {
		float: left;
		width: 100%;
		position: relative;
		border: #ccc solid 1px;
		padding: 10px 10px;
		margin-bottom: 20px;
	}

	.c_first {
		padding: 20px 10px 0px;
		border: #ccc solid 1px;
		float: left;
	}

	.box_title {
		width: auto;
		margin: -22px 0 0 0;
	}

	.box_title span {
		background: #fff;
		padding: 0 5px;
	}

	.data_box .row_list div.list,
	.data_box .input.radio {
		float: left;
		margin: 0;
		width: 48%;
	}

	.data_box .list label,
	.data_box .input.radio label {
		cursor: pointer;
		font-weight: 400;
		margin-bottom: 0;
		min-height: 20px;
		padding-left: 16px;
		width: 75%;
		text-align: left;
	}

	.data_box .input.radio label {
		margin-left: 12px;
	}

	#so_html,
	.office_t_so {
		display: none;
	}
</style>


<div class="row">
	<div class="col-xs-12">

		<div class="box box-primary" style="float:left; width:100%;">

			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Mig Sales Report'); ?></h3>
			</div>


			<div class="box-body">

				<div class="search-box" style="border:none; float:left; width:100%;">

					<?php echo $this->Form->create('MigSaleReports', array('role' => 'form', 'action' => 'index')); ?>

					<div style="float:left; width:100%;">

						<div class="col-md-5 col-sm-12 filter_left_div">

							<div class="data_box">
								<h4 class="box_title"><span>Date Selection</span></h4>
								<div class="required col-md-6 col-sm-12" style="padding:10px 0;">
									<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker1', 'required' => true, 'readonly' => true)); ?>
								</div>

								<div class="required  col-md-6 col-sm-12" style="padding:10px 0;">
									<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker1', 'required' => true, 'readonly' => true)); ?>
								</div>
							</div>

							<div class="data_box">
								<h4 class="box_title"><span>Rows</span></h4>
								<div id="market_list" class="input row_list" style="float:left; width:100%; padding-left:0px;">
									<?php echo $this->Form->input('rows', array('legend' => false, 'onclick' => 'indicatorDisable()', 'type' => 'radio', 'id' => 'rows', 'default' => 'so', 'separator' => '</div><div class="list">', 'class' => 'form-control rows')); ?>
								</div>
							</div>

							<div class="data_box">
								<h4 class="box_title"><span>Columns</span></h4>
								<div id="market_list" class="input row_list" style="float:left; width:100%; padding-left:0px;">
									<?php echo $this->Form->input('columns', array('legend' => false, 'onclick' => 'indicatorDisable()', 'type' => 'radio', 'id' => 'columns', 'default' => 'product', 'separator' => '</div><div class="list">', 'class' => 'form-control columns')); ?>
								</div>
							</div>

							<div class="data_box">
								<h4 class="box_title"><span>Indicators</span></h4>
								<div id="market_list" class="input select so_list" style="float:left; width:100%; padding-left:20px;">
									<?php echo $this->Form->input('indicators', array('label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $indicators)); ?>
								</div>
							</div>


							<div class="data_box">
								<h4 class="box_title"><span>Unit Type</span></h4>
								<div id="market_list" class="input row_list" style="float:left; width:100%; padding-left:0px;">
									<?php echo $this->Form->input('unit_type', array('legend' => false, 'type' => 'radio', 'id' => 'unit_type', 'default' => '2', 'separator' => '</div><div class="list">', 'class' => 'form-control unit_type', 'options' => $unit_types)); ?>
								</div>
							</div>

						</div>

						<div class="col-md-7 col-sm-12 condition c_first">
							<h4 class="box_title" style="margin-top:-32px;"><span>Filtering Condition</span></h4>
							<div style="float:left; width:100%; padding-bottom:20px;">
								<div class="col-md-6 col-sm-12" style="padding:0px">
									<div style="padding-bottom:10px; float:left; width:100%;">
										<?php echo $this->Form->input('location_type_id', array('id' => 'location_type_id', 'class' => 'form-control', 'empty' => '---- All ----')); ?>
									</div>
								</div>
								<div class="col-md-6 col-sm-12" style="padding:0px">
									<div style="margin:auto; width:80%;">
										<input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall_outlet" />
										<label for="checkall_outlet" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label>
									</div>
									<p class="label_title">Outlet Category</p>
									<div id="market_list" class="outlet_selection outlet_category">
										<?php echo $this->Form->input('outlet_category_id', array('id' => 'outlet_category_id', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $outlet_categories)); ?>
									</div>
								</div>
							</div>

							<div style="float:left; width:100%; padding-bottom:20px;">
								<div class="col-md-6 col-sm-12" style="padding:0px">
									<div style="padding-bottom:10px; float:left; width:100%;">
										<?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- All ----')); ?>
									</div>
									<div style="padding-bottom:10px; float:left; width:100%;">
										<label>SO :</label>
										<div id="so_html">
											<?php echo $this->Form->input('so_id', array('label' => false, 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'empty' => '---- All ----')); ?>
										</div>
									</div>
								</div>

								<div class="col-md-6 col-sm-12" style="padding:0px">
									<p class="label_title"><b>GEO Location</b></p>
									<div class="outlet_category outlet_category2">

										<?php echo $this->Form->input('division_id', array('id' => 'division_id', 'class' => 'form-control', 'empty' => '---- All ----', 'required' => false)); ?>

										<style>
											body .list_div {
												float: left;
												width: 55%;
											}

											#dist_html select,
											#thana_html select {
												width: 100% !important;
											}
										</style>
										<div class="input select">
											<label for="district_id">District :</label>
											<span id="dist_html" class="list_div">
												<?php echo $this->Form->input('district_id', array('label' => false, 'id' => 'district_id', 'class' => 'form-control district_id', 'required' => false, 'empty' => '---- All ----')); ?>
											</span>
										</div>


										<div class="input select">
											<label for="division_id">Thana :</label>
											<span id="thana_html" class="list_div">
												<?php echo $this->Form->input('thana_id', array('label' => false, 'id' => 'thana_id', 'class' => 'form-control thana_id', 'required' => false, 'empty' => '---- All ----')); ?>
											</span>
										</div>

									</div>
								</div>
							</div>

							<div style="float:left; width:100%; padding-bottom:20px;">
								<div id="product" class="selection_box" style="float:left; width:100%; display:block;">

									<div style="margin:auto; width:90%;">
										<input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
										<label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label>
									</div>

									<p class="label_title pro_label_title">Product Selection</p>
									<div id="market_list" class="product_selection outlet_category outlet_category3">
										<?php echo $this->Form->input('product_id', array('label' => false, 'class' => 'checkbox product_id', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $product_list)); ?>
									</div>
								</div>

								<div id="brand" class="selection_box" style="float:left; width:100%;">
									<div style="margin:auto; width:90%;">
										<input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
										<label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label>
									</div>
									<p class="label_title pro_label_title">Brand Selection</p>
									<div id="market_list" class="brand_selection outlet_category outlet_category3">
										<?php echo $this->Form->input('brand_id', array('label' => false, 'class' => 'checkbox brand_id', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $brands)); ?>
									</div>
								</div>

								<div id="category" class="selection_box" style="float:left; width:100%;">
									<div style="margin:auto; width:90%;">
										<input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall3" />
										<label for="checkall3" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label>
									</div>
									<p class="label_title pro_label_title">Category Selection</p>
									<div id="market_list" class="category_selection outlet_category outlet_category3">
										<?php echo $this->Form->input('product_category_id', array('label' => false, 'class' => 'checkbox product_category_id', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $categories)); ?>
									</div>
								</div>
							</div>



						</div>


						<div class="data_box" style="float:left; width:100%;">
							<h4 class="box_title"><span>Product Target</span></h4>
							<div id="market_list" class="input select so_list" style="float:left; width:100%; padding-left:20px;">
								<?php //echo $this->Form->input('product_targets', array('label'=>false, 'onChange' => 'indicatorDisable()', 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $product_targets)); 
								?>

								<?php //echo $this->Form->input('MigSaleReportsProductTargets', array('legend' => false, 'type' => 'checkbox', 'id' => 'MigSaleReportsProductTargets', 'separator'=> '</div><div class="list">', 'class' => 'form-control unit_type', 'options' => $product_targets)); 
								?>

								<div class="checkbox">
									<input type="checkbox" <?= @$request_data['MigSaleReports']['product_targets'] ? 'checked' : '' ?> onChange="indicatorDisable()" name="data[MigSaleReports][product_targets]" value="1" id="MigSaleReportsProductTargets" style=""><label for="MigSaleReportsProductTargets">Target By Area By Product</label>
								</div>

							</div>
						</div>

						<div style="float:left; width:100%; padding-top:20px; text-align:center;">
							<?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>


							<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

							<?php if (!empty($request_data)) { ?>
								<a onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
							<?php } ?>
						</div>

					</div>

					<?php echo $this->Form->end(); ?>
				</div>


				<?php
				//pr($request_data);

				if (!empty($request_data)) { ?>



					<div id="content" style="width:96%; margin:0 2%; float:left;">

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

							.titlerow th div {
								text-transform: capitalize;
								min-width: 100px;
								float: left;
								position: relative;
							}

							.titlerow th {
								text-align: center;
							}
						</style>


						<div class="table-responsive">

							<div class="pull-right csv_btn" style="padding-top:20px;">
								<?php /*?><?=$this->Html->link(__('Download XLS'), array('action' => 'Download_xls?data='.serialize($request_data)), array('class' => 'btn btn-primary', 'escape' => false)); ?><?php */ ?>
								<?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
							</div>


							<div id="xls_body">

								<div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both;">
									<h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
									<h3 style="margin:2px 0;">Mig Sales Report</h3>
									<p>
										Time Frame : <b><?= date('d M, Y', strtotime($date_from)) ?></b> to <b><?= date('d M, Y', strtotime($date_to)) ?></b>
									</p>
									<?php if (in_array('volume', $request_data['MigSaleReports']['indicators'])) { ?>
										<p><b><?php if (@!$request_data['MigSaleReports']['product_targets']) { ?>Measuring Unit: <?= $unit_type_text ?><?php } else {
																																						echo 'Target By Area By Product';
																																					} ?></b></p>
									<?php } ?>
								</div>


								<div style="float:left; width:100%; height:450px; overflow:scroll;">
									<table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

										<?php
										$indicators_array = array();
										if (empty($request_data['MigSaleReports']['indicators'])) {
											foreach ($indicators as $key => $val) {
												array_push($indicators_array, $key);
											}
										} else {
											$indicators_array = $request_data['MigSaleReports']['indicators'];
										}
										//pr($columns_list);
										?>



										<?php if ($columns_list) { ?>

											<tr class="titlerow">
												<th style="text-align:left;">
													<div>By <?= $request_data['MigSaleReports']['rows'] ?></div>
												</th>
												<?php
												$color = '#f1f1f1';
												foreach ($columns_list as $col_key => $col_val) {
													$color = ($color == '#f1f1f1') ? '#e2e2e2' : '#f1f1f1';
													foreach ($indicators2 as $in_key => $in_val) {
														if (in_array($in_key, $indicators_array)) {
												?>
															<th style="background:<?= $color ?>;">
																<div><?= $col_val . ' ' . $in_val ?></div>
															</th>

													<?php
														}
													}
													?>
												<?php } ?>

												<?php if (in_array('oc', $this->request->data['MigSaleReports']['indicators'])) { ?>
													<td><b>OC</b></td>
												<?php } ?>
												<?php if (in_array('ec', $this->request->data['MigSaleReports']['indicators'])) { ?>
													<td><b>EC</b></td>
												<?php } ?>

												<?php /*?><td><b>Total</b></td><?php */ ?>
											</tr>

											<?php echo html_entity_decode($output); ?>

										<?php } else { ?>
											<div style="clear:both;"></div>
											<p style="background:#F5F5F5; padding:20px 10px; border:#ccc solid 1px; border-radius:4px; text-align:center;">No Report Found!</p>
										<?php } ?>





									</table>
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
	$(document).ready(function() {

		$('.datepicker1').datepicker({
			'format': "M-yy",
			'startView': "year",
			'minViewMode': "months",
			'autoclose': true
		});

		$('input').iCheck('destroy');
		//$("input[type='checkbox']").iCheck('destroy');
		$('#MigSaleReportsIndexForm').trigger("reset");

		$('#checkall_outlet').click(function() {
			var checked = $(this).prop('checked');
			$('.outlet_selection').find('input:checkbox').prop('checked', checked);
		});

		$('#checkall').click(function() {
			var checked = $(this).prop('checked');
			$('.product_selection').find('input:checkbox').prop('checked', checked);
		});

		$('#checkall2').click(function() {
			var checked = $(this).prop('checked');
			$('.brand_selection').find('input:checkbox').prop('checked', checked);
		});

		$('#checkall3').click(function() {
			var checked = $(this).prop('checked');
			$('.category_selection').find('input:checkbox').prop('checked', checked);
		});



	});




	$('#division_id').change(function() {
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL ?>mig_sale_reports/get_district_list',
			data: 'division_id=' + $(this).val(),
			cache: false,
			success: function(response) {
				//alert(response);						
				$('#district_id').html(response);
			}
		});

	});

	$('#district_id').change(function() {
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL ?>mig_sale_reports/get_thana_list',
			data: 'district_id=' + $(this).val(),
			cache: false,
			success: function(response) {
				//alert(response);						
				$('#thana_id').html(response);
			}
		});

	});



	$('#office_id').change(function() {
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL ?>mig_sale_reports/get_office_so_list',
			data: 'office_id=' + $(this).val(),
			cache: false,
			success: function(response) {
				//alert(response);	
				if (response) {
					$('#so_html').html(response);
				}
			}
		});

	});
</script>

<script>
	function indicatorDisable() {
		var rows = $('.rows:checked').val();

		//for territory and so 
		$('#so_html').hide();
		$('.office_t_so').hide();

		//alert(rows);
		$('#rowsSo').prop('disabled', false);
		$('#rowsDivision').prop('disabled', false);
		$('#rowsDistrict').prop('disabled', false);
		$('#rowsThana').prop('disabled', false);
		$('#rowsNgo').prop('disabled', false);

		$('#columnsCategory').prop('disabled', false);
		$('#columnsBrand').prop('disabled', false);
		$('#columnsOutletType').prop('disabled', false);
		$('#so_id').prop('disabled', false);
		$('.outlet_selection input').prop('disabled', false);
		$('#location_type_id').prop('disabled', false);

		$('#MigSaleReportsIndicatorsEc').prop('disabled', true);
		$('#MigSaleReportsIndicatorsOc').prop('disabled', true);
		$('#MigSaleReportsIndicatorsCyp').prop('disabled', false);
		$('#MigSaleReportsIndicatorsBonus').prop('disabled', false);

		$('#division_id').prop('disabled', false);
		$('#district_id').prop('disabled', false);
		$('#thana_id').prop('disabled', false);


		$('#so_html').show();

		/*if(rows=='so'){
			$('#so_html').show();
		}else{
			$('#territory_id').show();
		}*/

		if (rows == 'so') {
			$('.office_t_so option:nth-child(1)').prop("selected", true).change();
		} else if (rows == 'territory') {
			$('#so_id option:nth-child(1)').prop("selected", true).change();
		} else {
			<?php if (!@$request_data['MigSaleReports']['territory_id']) { ?>
				$('.office_t_so option:nth-child(1)').prop("selected", true).change();
			<?php } ?>

			<?php if (!@$request_data['MigSaleReports']['so_id']) { ?>
				$('#so_id option:nth-child(1)').prop("selected", true).change();
			<?php } ?>
		}

		//end for territory and so 

		var columns = $('.columns:checked').val();


		if (columns == 'outlet_type') {
			$('#rowsSo').prop('disabled', true);
			$('#MigSaleReportsIndicatorsVolume').prop('disabled', true);
			$('#MigSaleReportsIndicatorsBonus').prop('disabled', true);
		} else {
			$('#MigSaleReportsIndicatorsVolume').prop('disabled', false);
			$('#MigSaleReportsIndicatorsBonus').prop('disabled', false);
			$('#rowsSo').prop('disabled', false);
		}




		//alert(rows);

		if (rows == 'area' || rows == 'month' || rows == 'year' || rows == 'national') {
			$('#MigSaleReportsIndicatorsEc').prop('disabled', false);
			$('#MigSaleReportsIndicatorsOc').prop('disabled', false);
		} else {
			$('#MigSaleReportsIndicatorsEc').prop('disabled', true);
			$('#MigSaleReportsIndicatorsOc').prop('disabled', true);
		}

		if (columns == 'national') {
			$('#MigSaleReportsIndicatorsVolume').prop('disabled', true);
			$('#MigSaleReportsIndicatorsEc').prop('disabled', false);
			$('#MigSaleReportsIndicatorsOc').prop('disabled', false);
			$('#MigSaleReportsIndicatorsBonus').prop('disabled', true);
		} else {
			$('#MigSaleReportsIndicatorsVolume').prop('disabled', false);
			$('#MigSaleReportsIndicatorsEc').prop('disabled', true);
			$('#MigSaleReportsIndicatorsOc').prop('disabled', true);
			$('#MigSaleReportsIndicatorsBonus').prop('disabled', false);
		}

		/*if(columns=='product' || columns=='category' || columns=='brand'){
			
		}*/

		if (rows == 'ngo') {
			$('#columnsOutletType').prop('disabled', true);
			$('#so_id').prop('disabled', true);
			$('.outlet_selection input').prop('disabled', true);
			$('#location_type_id').prop('disabled', true);
		} else {
			$('#columnsOutletType').prop('disabled', false);
			$('#so_id').prop('disabled', false);
			$('.outlet_selection input').prop('disabled', false);
			$('#location_type_id').prop('disabled', false);
		}

		if (rows == 'so') {
			$('#columnsOutletType').prop('disabled', true);
		} else {
			$('#columnsOutletType').prop('disabled', false);
		}

		if (rows == 'so' || rows == 'division' || rows == 'district' || rows == 'thana' || rows == 'ngo') {
			$('#MigSaleReportsIndicatorsEc').prop('disabled', true);
			$('#MigSaleReportsIndicatorsOc').prop('disabled', true);
		}

		var target = $('#MigSaleReportsProductTargets:checked').val();

		if (target == 1) {
			//$('#rowsArea').prop('checked', true);
			$('#columnsProduct').prop('checked', true);

			$('#rowsSo').prop('disabled', true);
			$('#rowsDivision').prop('disabled', true);
			$('#rowsDistrict').prop('disabled', true);
			$('#rowsThana').prop('disabled', true);
			$('#rowsNgo').prop('disabled', true);

			$('#columnsCategory').prop('disabled', true);
			$('#columnsBrand').prop('disabled', true);
			$('#columnsOutletType').prop('disabled', true);

			$('#so_id').prop('disabled', true);
			$('.outlet_selection input').prop('disabled', true);
			$('#location_type_id').prop('disabled', true);

			$('#MigSaleReportsIndicatorsEc').prop('disabled', true);
			$('#MigSaleReportsIndicatorsOc').prop('disabled', true);
			$('#MigSaleReportsIndicatorsCyp').prop('disabled', true);
			$('#MigSaleReportsIndicatorsBonus').prop('disabled', true);

			$('#division_id').prop('disabled', true);
			$('#district_id').prop('disabled', true);
			$('#thana_id').prop('disabled', true);
		}

	}

	$(document).ready(function() {

		indicatorDisable();

		var columns = $('.columns:checked').val();
		$('.selection_box').hide();
		if (columns == 'brand') {
			$('#brand').show();
		} else if (columns == 'category') {
			$('#category').show();
		} else {
			$('#product').show();
		}

		$('.columns').change(function() {
			//alert(this.value);

			if (this.value == 'product' || this.value == 'category' || this.value == 'brand') {
				//$('.selection').prop("checked", false);
				//$('.selection').prop('checked', false);
				//alert(111);
				$('.product_selection').find('input:checkbox').prop('checked', false);
				$('.brand_selection').find('input:checkbox').prop('checked', false);
				$('.category_selection').find('input:checkbox').prop('checked', false);

				$('#checkall').prop('checked', false);
				$('#checkall2').prop('checked', false);
				$('#checkall3').prop('checked', false);
			}

			$('.selection_box').hide();
			if (this.value == 'brand') {
				$('#brand').show();
			} else if (this.value == 'category') {
				$('#category').show();
			} else {
				$('#product').show();
			}
		});

	});



	$('#product_type').change(function() {
		//alert($(this).val());
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL ?>mig_sale_reports/get_product_list',
			data: 'product_type=' + $(this).val(),
			cache: false,
			success: function(response) {
				//alert(response);						
				$('#product .outlet_category3').html(response);
			}
		});
	});
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
</script>

<script>
	$("#download_xl").click(function(e) {
		e.preventDefault();
		var html = $("#xls_body").html();

		var blob = new Blob([html], {
			type: 'data:application/vnd.ms-excel'
		});
		var downloadUrl = URL.createObjectURL(blob);
		var a = document.createElement("a");
		a.href = downloadUrl;
		a.download = "sales_analysis_report.xls";
		document.body.appendChild(a);
		a.click();
	});
</script>