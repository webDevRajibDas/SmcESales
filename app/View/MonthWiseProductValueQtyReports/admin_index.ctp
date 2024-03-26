<?php
//App::import('Controller', 'OutletCharacteristicReportsController');
//$OutletCharacteristicController = new OutletCharacteristicReportsController;

//echo '<pre>';print_r($totalDay);exit;
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
					<?php echo $this->Form->create('MonthWiseProductValueQtyReports', array('role' => 'form', 'action' => 'index')); ?>
					<table class="search">


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
							<td>

								<?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id office_t_so', 'required' => false, 'empty' => '---- All ----')); ?>

							</td>
							<td width="50%"><?php echo $this->Form->input('date_from', array('label' => 'Month :', 'id' => 'datepicker', 'class' => 'form-control', 'required' => true)); ?></td>
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
						<tr>
							<td colspan="2">
								<?php echo $this->Form->input('rows', array('legend' => 'Unit :', 'class' => 'rows', 'type' => 'radio', 'default' => 'product', 'options' => $rows, 'required' => true));  ?>
							</td>
						</tr>
						<tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
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

										<b> Time Frame : <?= @date('M, Y', strtotime('01' . '-' . $date_from)) ?> </b>
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

										<thead>
											<tr class="titlerow">
												<th class="adress_col"></th>
												<?php
												for ($x = 1; $x <= $totalDay; $x++) {
												?>
													<th colspan="2">Day <?= $x; ?></th>
												<?php } ?>
												<th colspan="2"></th>
											</tr>
											<tr class="titlerow">
												<?php if ($rows_by == 'area') { ?>
													<th>Area Office</th>
												<?php } else { ?>
													<th>Product Name</th>
												<?php } ?>
												<?php
												for ($x = 1; $x <= $totalDay; $x++) {
												?>
													<td>Quantity</td>
													<td>Value</td>
												<?php } ?>
												<td>Total Qty</td>
												<td>Total Value</td>
											</tr>
										</thead>
										<tbody>
											<?php echo $output; ?>
										</tbody>
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


	$(document).ready(function() {
		typeChange();
	});


	function typeChange() {
		$('#territory_html').show();
		<?php if (!@$request_data['MonthWiseProductValueQtyReports']['territory_id']) { ?>
			$('.office_t_so option:nth-child(1)').prop("selected", true).change();
		<?php } ?>


	}
</script>


<script>
	$(document).ready(function() {
		//terrotory_thanaBoxList();

		$("#territory_id").change(function() {
			var tid = $(this).val();
			terrotory_thanaBoxList(tid);
		});
	});


	function terrotory_thanaBoxList() {

		var terrotoryval = $('#territory_id').val();

		if (terrotoryval) {

			//alert(val);
			$('.selection2').hide();

			$('.selection3').hide();
			$('.selection3').html('');
			$('.selection4').hide();
			$('.selection4').html('');

			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>month_wise_product_value_qty_reports/get_thana_list',
				data: 'territory_id=' + $('#territory_id').val(),
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

		} else {
			$('.selection2').hide();

			$('.selection3').hide();
			$('.selection3').html('');
			$('.selection4').hide();
			$('.selection4').html('');
		}
	}
</script>

<script>
	$(document).ready(function() {
		$('[name="data[MonthWiseProductValueQtyReports][thana_id][]"]').change(function() {
			//alert($(this).val()); // alert value
			//$('.selection').find('input:checkbox').prop('checked', checked);
			marketBoxList();
		});
	});

	function marketBoxList() {
		var val = [];
		$('[name="data[MonthWiseProductValueQtyReports][thana_id][]"]:checked').each(function(i) {
			val[i] = $(this).val();
		});

		$('.selection3').hide();

		$('.selection4').hide();
		$('.selection4').html('');

		$.ajax({
			type: "POST",
			url: '<?php echo BASE_URL; ?>month_wise_product_value_qty_reports/get_market_list',
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
		$('[name="data[MonthWiseProductValueQtyReports][market_id][]"]').change(function() {
			//alert($(this).val()); // alert value
			//$('.selection').find('input:checkbox').prop('checked', checked);
			outletBoxList();
		});

	});

	function outletBoxList() {
		var val = [];
		$('[name="data[MonthWiseProductValueQtyReports][market_id][]"]:checked').each(function(i) {
			val[i] = $(this).val();
		});

		$('.selection4').hide();

		$.ajax({
			type: "POST",
			url: '<?php echo BASE_URL; ?>month_wise_product_value_qty_reports/get_outlet_list',
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

			a.download = "day_wise_product_qty_value_report.xls";

			document.body.appendChild(a);

			a.click();

		});

	});

	$("#datepicker").datepicker({
		format: "mm-yyyy",
		viewMode: "years",
		minViewMode: "months"
	});
</script>