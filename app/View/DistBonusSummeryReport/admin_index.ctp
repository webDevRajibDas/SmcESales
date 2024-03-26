<?php
ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
// pr($result);die;
?>
<style type="text/css">
	.border,
	.border td {
		border: 1px solid black;
		white-space: nowrap;
	}

	.search .radio label {
		width: auto;
		float: none;
		padding-left: 5px;
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
		width: 30%;
		float: left;
		margin: 1px 0;
	}

	.so_list {
		float: right;
		width: 97%;
		padding-left: 10%;
		border: #c7c7c7 solid 1px;
		height: 150px;
		overflow: auto;
		margin-right: 5%;
		padding-top: 5px;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Bonus Summery Report'); ?></h3>
			</div>
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('search', array('role' => 'form')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('class' => 'form-control', 'empty' => '---- Select Office ----', 'required' => true)); ?></td>
							<td width="50%"><?php //echo $this->Form->input('bonus_card_id', array('class' => 'form-control','empty'=>'---- Select Bonus Card ----','required'=>true)); 
											?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('fiscal_year_id', array('id' => 'fiscal_year_id', 'class' => 'form-control', 'empty' => '---- Select Fiscal Year ----', 'required' => true)); ?></td>
							<td width="50%"><?php echo $this->Form->input('bonus_card_id', array('id' => 'bonus_card_id', 'class' => 'form-control', 'empty' => '---- Select Bonus Card ----', 'required' => true)); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true)); ?></td>
							<td width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true)); ?></td>
						</tr>
						<tr>
							<td colspan="2">
								<label style="float:left; width:15%;">Route : </label>
								<div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px; ">
									<div style="margin:auto; width:90%; float:left; margin-left:-20px;">
										<input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
										<label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
									</div>
									<div class="selection2 so_list">
										<?php echo $this->Form->input('route_id', array('label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $route_list)); ?>
									</div>
								</div>
							</td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<?php if (!empty($result)) { ?>
									<a class="btn btn-success" id="download_xl">Download XL</a>
									<!--  <button type="button" onclick="PrintElem('content')" class="btn btn-primary">
                                <i class="glyphicon glyphicon-print"></i> Print
                            </button> -->
								<?php } ?>
							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
					<?php if (!empty($result)) { ?>
						<div class="row">

							<div id="content" style="height:400px;overflow: scroll;width:90%;margin-left:5%;margin-right:5%;">
								<div style="width:100%;">
									<div style="width:25%;text-align:left;float:left">
										&nbsp;&nbsp;&nbsp;&nbsp;
									</div>
									<div style="width:50%;text-align:center;float:left">
										<font id="heading_name"><b>SMC Enterprise Limited</b></font><br>
										<span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
										<font><b>Bonus Summery Report (<?php echo h($bonusCards[$this->request->data['search']['bonus_card_id']]); ?>)</b></font><br>
										<font><b>Area Office : <?php echo h($offices[$this->request->data['search']['office_id']]); ?></b></font><br>
										<font><?php if (!empty($this->request->data)) { ?><strong>Between:</strong>&nbsp;&nbsp;<u><?php echo date('d-F-Y', strtotime($this->request->data['search']['date_from'])); ?>&nbsp;&nbsp;to&nbsp;&nbsp;<?php echo date('d-F-Y', strtotime($this->request->data['search']['date_to']));
																																																											} ?></u>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Reporting Date:</strong>&nbsp;&nbsp;<?php echo date('d-F-Y'); ?></font>
									</div>
									<div style="width:25%;text-align:right;float:left">
										&nbsp;&nbsp;&nbsp;&nbsp;
									</div>
								</div>
								<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
								<table class="table  table-striped border" style="font-size:12px;" border="1">
									<thead>
										<tr>
											<td rowspan="2" class="text-center">TSO</td>
											<td rowspan="2" class="text-center">DB</td>
											<td rowspan="2" class="text-center">SR</td>
											<td rowspan="2" class="text-center">Route</td>
											<td rowspan="2" class="text-center">Outlet</td>
											<td rowspan="2" class="text-center">Market</td>
											<?php for ($i = date('Y-m', strtotime($this->request->data['search']['date_from'])); $i <= date('Y-m', strtotime($this->request->data['search']['date_to'])); $i = date('Y-m', strtotime('+1 months', strtotime($i)))) { ?>
												<td colspan="2" class="text-center"><?= date('F', strtotime($i)); ?></td>
											<?php } ?>
											<td colspan="2" class="text-center">Total</td>
										</tr>
										<tr>

											<?php for ($i = date('Y-m', strtotime($this->request->data['search']['date_from'])); $i <= date('Y-m', strtotime($this->request->data['search']['date_to'])); $i = date('Y-m', strtotime('+1 months', strtotime($i)))) {
												$territory_total_month['sales_qty_' . $i] = 0;
												$territory_total_month['stamp_' . $i] = 0;
											?>
												<td class="text-center">Sales<br>Qty</td>
												<td class="text-center">Bonus<br>Qty</td>
											<?php
											}
											$territory_total['sales_qty'] = 0;
											$territory_total['bonus_qty'] = 0;
											$last_territory_id = '';
											?>
											<td class="text-center">Sales<br>Qty</td>
											<td class="text-center">Bonus<br>Qty</td>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($result as $data) { ?>
											<?php
											if (!$last_territory_id) {
												$last_territory_id = $data['route_id'];
											}
											if ($last_territory_id && $last_territory_id != $data['route_id']) { ?>
												<tr>
													<td class="text-right" colspan="6">
														<b>Route Total : </b>
													</td>

													<?php for ($j = date('Y-m', strtotime($this->request->data['search']['date_from'])); $j <= date('Y-m', strtotime($this->request->data['search']['date_to'])); $j = date('Y-m', strtotime('+1 months', strtotime($j)))) {

													?>

														<td class="text-center sales_qty"><?= isset($territory_total_month['sales_qty_' . $j]) ? $territory_total_month['sales_qty_' . $j] : '' ?></td>
														<td class="text-center stamp_qty"><?= isset($territory_total_month['stamp_' . $j]) ? $territory_total_month['stamp_' . $j] : '' ?></td>
													<?php
														$territory_total_month['sales_qty_' . $j] = 0;
														$territory_total_month['stamp_' . $j] = 0;
													}
													?>
													<td class="text-center"><?= $territory_total['sales_qty'] ?></td>
													<td class="text-center"><?= $territory_total['bonus_qty'] ?></td>
												</tr>
											<?php
												$last_territory_id = $data['route_id'];
												$territory_total['sales_qty'] = 0;
												$territory_total['bonus_qty'] = 0;
											}
											?>
											<tr>
												<td class="text-left">
													<?= $data['tso_name'] ?>
												</td>
												<td class="text-left">
													<?= $data['db_name'] ?>
												</td>
												<td class="text-left">
													<?= $data['sr_name'] ?>
												</td>
												<td class="text-left">
													<?= $data['route_name'] ?>
												</td>
												<td class="text-left">
													<?= $data['outlet'] ?>
												</td>
												<td class="text-left">
													<?= $data['market'] ?>
												</td>

												<?php for ($i = date('Y-m', strtotime($this->request->data['search']['date_from'])); $i <= date('Y-m', strtotime($this->request->data['search']['date_to'])); $i = date('Y-m', strtotime('+1 months', strtotime($i)))) { ?>

													<td class="text-center sales_qty"><?= isset($data['sales_qty_' . $i]) ? $data['sales_qty_' . $i] : '' ?></td>
													<td class="text-center stamp_qty"><?= isset($data['stamp_' . $i]) ? $data['stamp_' . $i] : '' ?></td>
												<?php
													if (isset($data['sales_qty_' . $i])) {
														$territory_total_month['sales_qty_' . $i] += $data['sales_qty_' . $i];
													}
													if (isset($data['stamp_' . $i])) {
														$territory_total_month['stamp_' . $i] += $data['stamp_' . $i];
													}
												}

												?>
												<td class="text-center"><?= $data['total_qty'] ?></td>
												<td class="text-center"><?= $data['total_stamp'] ?></td>
											</tr>

										<?php
											$territory_total['sales_qty'] += $data['total_qty'];
											$territory_total['bonus_qty'] += $data['total_stamp'];
										} ?>
										<tr>
											<td class="text-right" colspan="6">
												<b>Route Total : </b>
											</td>

											<?php for ($i = date('Y-m', strtotime($this->request->data['search']['date_from'])); $i <= date('Y-m', strtotime($this->request->data['search']['date_to'])); $i = date('Y-m', strtotime('+1 months', strtotime($i)))) {

											?>

												<td class="text-center sales_qty"><?= isset($territory_total_month['sales_qty_' . $i]) ? $territory_total_month['sales_qty_' . $i] : '' ?></td>
												<td class="text-center stamp_qty"><?= isset($territory_total_month['stamp_' . $i]) ? $territory_total_month['stamp_' . $i] : '' ?></td>
											<?php
												$territory_total_month['sales_qty_' . $i] = 0;
												$territory_total_month['stamp_' . $i] = 0;
											}
											?>
											<td class="text-center"><?= $territory_total['sales_qty'] ?></td>
											<td class="text-center"><?= $territory_total['bonus_qty'] ?></td>
										</tr>
									</tbody>
								</table>
								<div style="width:100%;padding-top:100px;">
									<footer style="width:100%;text-align:center;">
										"This Report has been generated from SMC Automated Sales System at <?php echo h($offices[$this->request->data['search']['office_id']]); ?> Area. This information is confidential and for internal use only."
									</footer>
								</div>
							</div>
						</div>

					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	function PrintElem(elem) {

		$("#table_content").html($("#report_content").html());
		var mywindow = window.open('', 'PRINT', 'height=600,width=960');

		mywindow.document.write('<html><head><title></title><?php echo $this->Html->css('bootstrap.min.css');
															echo $this->fetch('css'); ?>');
		mywindow.document.write('</head><body >');
		mywindow.document.write(document.getElementById(elem).innerHTML);
		mywindow.document.write('</body></html>');

		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10*/

		mywindow.print();
		mywindow.close();

		return true;
	}
	$(document).ready(function() {
		$('input').iCheck('destroy');
		$("#fiscal_year_id").change(function() {
			$.post('<?= BASE_URL . 'bonus_summery_report/get_bonus_card' ?>', {
				'fiscal_year_id': $(this).val()
			}, function(data, status) {
				$("#bonus_card_id").html(data);
			});
		});
		$('#searchOfficeId').change(function() {
			$.ajax({
				type: "POST",
				url: '<?= BASE_URL ?>dist_bonus_summery_report/get_route_list',
				data: 'office_id=' + $(this).val(),
				cache: false,
				success: function(response) {
					$('#checkall2').prop('checked', false);
					$('.so_list').html(response);
				}
			});
		});
		$('#checkall2').click(function() {
			var checked = $(this).prop('checked');
			$('.selection2').find('input:checkbox').prop('checked', checked);
		});
		$("#download_xl").click(function(e) {
			e.preventDefault();
			var html = $("#content").html();
			// console.log(html);
			var blob = new Blob([html], {
				type: 'data:application/vnd.ms-excel'
			});
			var downloadUrl = URL.createObjectURL(blob);
			var a = document.createElement("a");
			a.href = downloadUrl;
			a.download = "downloadFile.xls";
			document.body.appendChild(a);
			a.click();
		});
	});
</script>