<?php
//App::import('Controller', 'OutletCharacteristicReportsController');
//$OutletCharacteristicController = new OutletCharacteristicReportsController;

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
					<?php echo $this->Form->create('BonusCardPartyReport', array('role' => 'form', 'action' => 'index')); ?>
					<table class="search">

						<tr>
							<td class="required" width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?></td>

							<td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?></td>
						</tr>

						<tr>

							<?php if ($office_parent_id == 0) { ?>

								<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>

							<?php } ?>


							<?php if ($office_parent_id == 14) { ?>
								<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>
							<?php } ?>


							<?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
								<td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id',  'empty' => '---- All ----')); ?></td>
							<?php } else { ?>
								<td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id',)); ?></td>

							<?php } ?>
						</tr>

						<tr>
							<td>
								<?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'required' => false, 'empty' => '---- Select Territory ----')); ?>
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


				<?php if (!empty($result_set)) { ?>

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


									<h3 style="margin:2px 0;">ORS Bonus Party Report(ORSaline-N+ORSaline-N (25pcs))</h3>


									<p>
										<?php if ($region_office_id) { ?>
											<span>Region Office: <?= $region_offices[$region_office_id] ?></span>
										<?php } else { ?>
											<span>Head Office</span>
										<?php } ?>
										<?php if ($office_id) { ?>
											<span>, Area Office: <?= $offices[$office_id] ?></span>
										<?php } ?>
									</p>
									<p>
										<b> Time Frame : <?= @date('d M, Y', strtotime($date_from)) ?> to <?= @date('d M, Y', strtotime($date_to)) ?></b>
									</p>

								</div>

								<div style="float:left; width:100%; height:430px; overflow:scroll;">
									<table class="text-center report_table" cellspacing="0" border="1px solid black" align="center">
										<thead>
											<tr>
												<th>Office</th>
												<th>Territory</th>
												<th>Thana</th>
												<th>Market</th>
												<th>Outlet</th>
												<th>Bonus Type</th>
												<th>ORSaline-N <br>qty</th>
												<th>ORSaline-N (25pcs) <br>qty</th>
												<th>Total qty</th>
												<th>ORSaline-N <br>Eligible qty</th>
												<th>ORSaline-N (25pcs) <br>Eligible qty</th>
												<th>Total Eligible qty</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($result_set as $data) { ?>
												<tr>
													<td><?= $data['office'] ?></td>
													<td><?= $data['territory'] ?></td>
													<td><?= $data['thana'] ?></td>
													<td><?= $data['market'] ?></td>
													<td><?= $data['outlet'] ?></td>
													<td>
														<?php
														if ($data['bonus_type'] == 1) {
															echo h('Small Bonus');
														} elseif ($data['bonus_type'] == 2) {
															echo h('Big Bonus');
														} else {
															echo h('Not Applicable');
														}
														?>
													</td>
													<td><?= $data['total_qty_47'] ?></td>
													<td><?= $data['total_qty_644'] ?></td>
													<td><?= $data['total_qty'] ?></td>
													<td><?= $data['total_eligible_47'] ?></td>
													<td><?= $data['total_eligible_644'] ?></td>
													<td><?= $data['eligible_qty'] ?></td>
												</tr>
											<?php } ?>
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
		url: '<?= BASE_URL . 'bonus_card_party_reports/get_office_list'; ?>',
		type: 'post',
		data: {
			'region_office_id': 'region_office_id'
		}
	});
	$('.region_office_id').change(function() {
		$('.territory_id').html('<option value="">---- All ----');
	});
	$('.office_id').selectChain({
		target: $('.territory_id'),
		value: 'name',
		url: '<?= BASE_URL . 'bonus_card_party_reports/get_territory_list' ?>',
		type: 'post',
		data: {
			'office_id': 'office_id'
		}
		<?php if (isset($this->request->data['BonusCardPartyReport']['territory_id'])) { ?>,
			afterSuccess: function() {
				$('.territory_id').val(<?php echo $this->request->data['BonusCardPartyReport']['territory_id']; ?>);
			}
		<?php } ?>
	});
	if ($('.office_id').val() !== "") {
		$('.office_id').trigger('change');
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

			a.download = "bonus_card_party_report(ors+ors 25).xls";

			document.body.appendChild(a);

			a.click();

		});

	});
</script>