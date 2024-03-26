<?php
//print_r($product_name);die();
?>
<style>
	table,
	th,
	td {
		/*border: 1px solid black;*/
		border-collapse: collapse;
	}

	#content {
		display: none;
	}

	@media print {
		#non-printable {
			display: none;
		}

		#content {
			display: block;
		}

		table,
		th,
		td {
			border: 1px solid black;
			border-collapse: collapse;
		}
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Memo List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if ($this->App->menu_permission('memos', 'admin_create_memo')) {
						echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Memo'), array('action' => 'create_memo'), array('class' => 'btn btn-primary', 'escape' => false));
					} ?>
					<?php if ($this->App->menu_permission('memos', 'admin_memo_map')) {
						echo $this->Html->link(__('<i class="glyphicon glyphicon-map-marker"></i> Memo on Map'), array('action' => 'memo_map'), array('class' => 'btn btn-success', 'escape' => false));
					} ?>
					<?php if ($this->App->menu_permission('memos', 'admin_memo_map_by_outlet')) {
						echo $this->Html->link(__('<i class="glyphicon glyphicon-map-marker"></i> Memo on Map By Outlet'), array('action' => 'memo_map_by_outlet'), array('class' => 'btn btn-info', 'escape' => false));
					} ?>
				</div>
			</div>
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('Memo', array('role' => 'form', 'action' => 'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----')); ?></td>
							<td width="50%"><?php echo $this->Form->input('memo_no', array('class' => 'form-control', 'required' => false)); ?></td>
						</tr>
						<tr>
							<td><?php echo $this->Form->input('program_officer_id', array('id' => 'program_officer_id', 'class' => 'form-control program_officer_id', 'required' => false, 'empty' => '---- Select Program Officer ----', 'options' => $program_office_lsit)); ?></td>
							
							<td><?php echo $this->Form->input('memo_reference_no', array('class' => 'form-control', 'required' => false)); ?></td>
						</tr>
						<tr>
							<td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'required' => false, 'empty' => '---- Select Territory ----', 'options' => $territories)); ?></td>
							
							<td>
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'label'=>'Memo Date From', 'value' => (isset($this->request->data['Memo']['date_from']) == '' ? $current_date : $this->request->data['Memo']['date_from']), 'required' => false)); ?>
							</td>
						</tr>
						<tr>
							<td class="thana_list">
								<?php echo $this->Form->input('thana_id', array('id' => 'thana_id', 'class' => 'form-control thana_id', 'empty' => '--- Select---', 'options' => '', 'label' => 'Thana'));
								?>
							</td>
							
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'label'=>'Memo Date To', 'value' => (isset($this->request->data['Memo']['date_to']) == '' ? $current_date : $this->request->data['Memo']['date_to']), 'required' => false)); ?>
							</td>

						</tr>
						<tr>
							<td>
								<?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'required' => false, 'empty' => '---- Select Market ----', 'options' => $markets)); ?>
							</td>
							
							<td class="operator_memo_value">
								<div><?php echo $this->Form->input('mamo_value', array('class' => 'form-control')); ?></div>
							</td>
							<td class="between_value text-left">
								<?php echo $this->Form->input('memo_value_from', array('class' => 'form-control operator_between_memo_value')); ?>
							</td>
						</tr>
						<tr>
							<td><?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'required' => false, 'empty' => '---- Select Outlet ----', 'options' => $outlets)); ?></td>
							
							<td class="between_value text-left">
								<?php echo $this->Form->input('memo_value_to', array('class' => 'form-control operator_between_memo_value')); ?>
							</td>
						</tr>
						<tr>
							<td class="text-left">
								<?php echo $this->Form->input('operator', array('class' => 'form-control operator', 'empty' => '---Select---', 'options' => array('1' => 'Less than (<)', '2' => 'Gretter than (>)', '3' => 'Between'))); ?>
							</td>
							

							<td class="operator_memo_product_count">
								<div><?php echo $this->Form->input('memo_product_count', array('class' => 'form-control', 'label' => 'No. Of Product')); ?></div>
							</td>
							<td class="between_p_count text-left">
								<?php echo $this->Form->input('memo_product_count_from', array('class' => 'form-control operator_between_memo_product', 'label' => 'No. Of Product(From)')); ?>
							</td>
						</tr>
						<tr>
							<td class="text-left">
								<?php echo $this->Form->input('operator_product_count', array('class' => 'form-control operator_p_count', 'empty' => '---Select---', 'options' => array('1' => 'Less than (<)', '2' => 'Gretter than (>)', '3' => 'Between'))); ?>
							</td>

							
							<td class="between_p_count text-left">
								<?php echo $this->Form->input('memo_product_count_to', array('class' => 'form-control operator_between_memo_product', 'label' => 'No. Of Product(To)')); ?>
							</td>
						</tr>
						<tr>
							<td class="text-left">
								<?php echo $this->Form->input('payment_status', array('class' => 'form-control', 'empty' => '---Select---', 'options' => array('1' => 'Due', '2' => 'Paid'))); ?>
							</td>
							<td>
								<?php echo $this->Form->input('from_app', array('class' => 'form-control', 'label'=>'Show Web Memo', 'empty' => '---Select---', 'options' => array('2' => 'Yes', '1' => 'No'))); ?>
							</td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'id' => 'search_button', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>
						</tr>

					</table>
					<?php echo $this->Form->end(); ?>
				</div>
				<div class="table-responsive">
					<table id="Memo" class="table table-bordered">
						<thead>
							<tr>
								<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('memo_no'); ?></th>
								<?php if(!empty($this->request->data['Memo']['program_officer_id']) and $this->request->data['Memo']['program_officer_id'] >0){ ?>
									<th class="text-center"><?php echo __('POF Name'); ?></th>
								<?php }else{ ?>
									<th class="text-center"><?php echo $this->Paginator->sort('memo_reference_no'); ?></th>
								<?php } ?>
								<th class="text-center"><?php echo $this->Paginator->sort('Outlet.name', 'Outlet'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('Market.name', 'Market'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('Territory.name', 'Territory'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('gross_value', 'Memo Total'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('memo_date'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('memo_time', 'Entry Time'); ?></th>

								<th class="text-center">Memo Type</th>

								<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
								<th width="80" class="text-center"><?php echo __('Actions'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$total_amount = 0;
							//echo '<pre>';print_r($memos);exit;
							foreach ($memos as $memo) :
								
								$memodate_con = date('Y-m-d', strtotime($memo['Memo']['memo_time']));
								
								$date1 = date_create(date('Y-m-d', strtotime($memo['Memo']['memo_time'])));
								$date2 = date_create(date('Y-m-d'));
								$diff = date_diff($date1, $date2);
								$dare_diff = $diff->format("%a");
							?>
								<tr style="background-color:<?php echo $memo['Memo']['from_app'] == 0 ? '#f5f5f5' : 'white' ?>">
									<td align="center"><?php echo h($memo['Memo']['id']); ?></td>
									<td align="center"><?php echo h($memo['Memo']['memo_no']); ?></td>
									<?php if(!empty($this->request->data['Memo']['program_officer_id']) and $this->request->data['Memo']['program_officer_id'] >0){ ?>
										<td align="center">
											<?php 
												echo $program_office_lsit[$this->request->data['Memo']['program_officer_id']];	
											?>
										</td>
									<?php }else{ ?>
										<td align="center"><?php echo h($memo['Memo']['memo_reference_no']); ?></td>
									<?php } ?>
									<td align="center"><?php echo h($memo['Outlet']['name']); ?></td>
									<td align="center"><?php echo h($memo['Market']['name']); ?></td>
									<td align="center"><?php echo h($memo['Territory']['name']); ?></td>
									<td align="center"><?php echo sprintf('%.2f', $memo['Memo']['gross_value']); ?></td>
									<td align="center"><?php echo $memo['Memo']['memo_date']; ?></td>
									<td align="center"><?php echo $this->App->datetimeformat($memo['Memo']['memo_time']); ?></td>

									<td align="center"><?= ($memo['Memo']['credit_amount'] > 0) ? 'Credit' : 'Cash'; ?></td>

									<td align="center">
										<?php //echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; 
										?>
										<?php
										if ($memo['Memo']['status'] == 0) { {
												echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
											}
										} else {
											if (!isset($memo['0']['payment_status']) || $memo['0']['payment_status'] < $memo['Memo']['gross_value']) {
												if ($memo['Memo']['from_app'] == 0 && !isset($memo['0']['payment_status'])) {
													echo '<span class="btn btn-danger btn-xs">WEB</span>';
												} else {
													echo '<span class="btn btn-danger btn-xs">Due</span>';
												}
											} elseif ($memo['0']['payment_status'] == $memo['Memo']['gross_value']) {
												echo '<span class="btn btn-success btn-xs">Paid</span>';
											}
										}

										?>
									</td>



									<td class="text-center">

										<?php if ($this->App->menu_permission('memos', 'admin_view')) {
											echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $memo['Memo']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
										} ?>


										<?php if ($memo['Memo']['action'] != 0) {
											if ($memo['Memo']['is_distributor'] != 1) {
										?>
												<?php if ($memo['Memo']['status'] == 0) { ?>
													<?php
													if ($this->App->menu_permission('memos', 'admin_edit')) {
														echo $this->Html->link(
															__('<i class="glyphicon glyphicon-pencil"></i>'),
															array('action' => 'edit', $memo['Memo']['id']),
															array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit')
														);
													}
													?>
												<?php } ?>
												
												<?php if ( $usergroupid == 1016 AND $memo['Memo']['status'] > 0 AND $memo['Memo']['created_by'] == $this->UserAuth->getUserId() ) { ?>
													<?php
													if ($this->App->menu_permission('memos', 'admin_edit')) {
														echo $this->Html->link(
															__('<i class="glyphicon glyphicon-pencil"></i>'),
															array('action' => 'edit', $memo['Memo']['id']),
															array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit')
														);
													}
													?>
												<?php } ?>
												
												
												<?php if ($this->App->menu_permission('memos', 'admin_delete')) {
													echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $memo['Memo']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $memo['Memo']['id']));
												} ?>

										<?php }
										} ?>

										<?php
										
										
										if( $memodate_con >= '2023-07-01'){
											
												//if ($office_parent_id && $dare_diff <= 3) {
												if ( ($office_parent_id && $dare_diff <= 3) || ( $memo['Memo']['from_app'] == 0 && $memo['0']['payment_status'] != $memo['Memo']['gross_value']) ) {
													if ($this->App->menu_permission('memos', 'admin_memo_editable') && $memo['Memo']['memo_editable'] != 1) {
														echo $this->Form->postLink(__('<i class="glyphicon glyphicon-edit"></i>'), array('action' => 'memo_editable', $memo['Memo']['id']), array('class' => 'btn btn-warning btn-xs disable', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'make editable'), __('Allow edit from apps? This memo will be pull to mobile apps for editing # %s?', $memo['Memo']['memo_no']));
													}
												}

												if (!$office_parent_id) {
													if ($this->App->menu_permission('memos', 'admin_memo_editable') && $memo['Memo']['memo_editable'] != 1) {
														echo $this->Form->postLink(__('<i class="glyphicon glyphicon-edit"></i>'), array('action' => 'memo_editable', $memo['Memo']['id']), array('class' => 'btn btn-warning btn-xs disable', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'make editable'), __('Allow edit from apps? This memo will be pull to mobile apps for editing # %s?', $memo['Memo']['memo_no']));
													}
												}
											
											}
											
										?>

									</td>


								</tr>
							<?php
								$total_amount = $total_amount + $memo['Memo']['gross_value'];
							endforeach;
							?>
							<tr>
								<td align="right" colspan="5"><b>Total Amount :</b></td>
								<td align="center"><b><?php echo sprintf('%.2f', $total_amount); ?></b></td>
								<td class="text-center" colspan="3"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
							<?php echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>
								<?php
								echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
								echo $this->Paginator->numbers(array("separator" => "", "currentTag" => "a", "currentClass" => "active", "tag" => "li", "first" => 1));
								echo $this->Paginator->next(__("next"), array("tag" => "li", "currentClass" => "disabled"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Report Print -->

<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;">
	<!-- <div style="text-align:right;width:100%;">Page No :1 of 1</div>
		<div style="text-align:right;width:100%;">Print Date :19 Jul 2017</div> -->


	<div style="width:100%;text-align:center;float:left">
		<h2>SMC Enterprise Limited</h2>
		<h3>Top Sheet</h3>
		<h2><u>Sales Report</u></h2>
		<h5><?php if (!empty($requested_data)) { ?><strong>Between:</strong>&nbsp;&nbsp;<u><?php echo $requested_data['Memo']['date_from']; ?>&nbsp;&nbsp;to&nbsp;&nbsp;<?php echo $requested_data['Memo']['date_to'];
																																									} ?></u>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Reporting Date:</strong>&nbsp;&nbsp;<?php echo date('d-F-Y'); ?></h5>
		<h4>Area : <?php echo $offices[$requested_data['Memo']['office_id']]; ?></h4>
	</div>

	<!-- product quantity get-->
	<?php
	$product_qnty = array();

	foreach ($product_quantity as $data) {


		$product_qnty[$data['0']['sales_person_id']][$data['0']['product_id']] = $data['0']['pro_quantity'];
	}
	?>
	<table style="width:100%" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
		<tr>
			<th>Sales Officer</th>
			<?php
			foreach ($product_name as $value) {
			?>
				<th <?php if ($value['Product']['product_category_id'] == 20) {
						echo 'class="condom"';
					} else if ($value['Product']['product_category_id'] == 21) {
						echo 'class="pill"';
					} ?>><?php echo $value['Product']['name'] . '<br>[' . $value['0']['mes_name'] . ']'; ?></th>
			<?php
			}
			?>
			<script>
				$('.condom:last').after("<th>Total Condom</th>");
				$('.pill:last').after("<th>Total Pill</th>")
			</script>
		</tr>
		<?php
		foreach ($sales_people as $data_s) {
		?>
			<tr>
				<td><?= $data_s['SalesPerson']['name'] ?></td>
				<?php

				foreach ($product_name as $data_q) {
				?>
					<td <?php if ($data_q['Product']['product_category_id'] == 20) {
							echo 'class="condom_' . $data_s['0']['sales_person_id'] . '"';
						} else if ($data_q['Product']['product_category_id'] == 21) {
							echo 'class="pill_' . $data_s['0']['sales_person_id'] . '"';
						} ?>>
						<?php
						if (array_key_exists($data_q['Product']['id'], $product_qnty[$data_s['0']['sales_person_id']])) {
							echo $product_qnty[$data_s['0']['sales_person_id']][$data_q['Product']['id']];
						} else echo '0.00';
						?>

					</td>
				<?php
				}
				?>
				<script>
					/**
					 * [total_condom description]
					 * @type {Number}
					 */
					var total_condom = 0.0;
					$('.condom_<?php echo $data_s['0']['sales_person_id'] ?>').each(function() {
						total_condom += parseFloat($(this).text());
					});
					$('.condom_<?php echo $data_s['0']['sales_person_id'] ?>:last').after('<td>' + total_condom + '</td>')
					/**
					 * [total_pill description]
					 * @type {Number}
					 */
					var total_pill = 0.0;
					$('.pill_<?php echo $data_s['0']['sales_person_id'] ?>').each(function() {
						total_pill += parseFloat($(this).text());
					});
					$('.pill_<?php echo $data_s['0']['sales_person_id'] ?>:last').after('<td>' + total_pill + '</td>')
				</script>
			</tr>
		<?php } ?>
	</table>

	<div style="width:100%;padding-top:100px;">
		<div style="width:33%;text-align:left;float:left">
			Prepared by:______________
		</div>
		<div style="width:33%;text-align:center;float:left">
			Checked by:______________
		</div>
		<div style="width:33%;text-align:right;float:left">
			Signed by:______________
		</div>
	</div>

</div>
<script>
	$('.office_id').selectChain({
		target: $('.territory_id'),
		value: 'name',
		url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
		type: 'post',
		data: {
			'office_id': 'office_id'
		}
	});

	$('.territory_id').selectChain({
		target: $('.market_id'),
		value: 'name',
		url: '<?= BASE_URL . 'admin/doctors/get_market'; ?>',
		type: 'post',
		data: {
			'territory_id': 'territory_id'
		}
	});

	function get_thana_list(territory_id) {
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL ?>memos/get_thana_by_territory_id',
			data: 'territory_id=' + territory_id,
			cache: false,
			success: function(response) {
				$('.thana_id').html(response);
				<?php if (isset($this->request->data['Memo']['thana_id'])) { ?>
					$('.thana_id option[value="<?= $this->request->data['Memo']['thana_id'] ?>"]').attr("selected", true);
				<?php } ?>
			}
		});
	}
	if ($('.territory_id').val() != '') {
		get_thana_list($('.territory_id').val());
	}
	$('body').on('change', '.territory_id', function() {

		get_thana_list($(this).val());
	});
	$('.thana_id').selectChain({
		target: $('.market_id'),
		value: 'name',
		url: '<?= BASE_URL . 'memos/market_list'; ?>',
		type: 'post',
		data: {
			'thana_id': 'thana_id'
		}
	});
	$('.market_id').selectChain({
		target: $('.outlet_id'),
		value: 'name',
		url: '<?= BASE_URL . 'admin/doctors/get_outlet'; ?>',
		type: 'post',
		data: {
			'market_id': 'market_id'
		}
	});

	$('.office_id').change(function() {
		$('.market_id').html('<option value="">---- Select Market ----');
		$('.outlet_id').html('<option value="">---- Select Outlet ----');
	});

	$('.territory_id').change(function() {
		$('.outlet_id').html('<option value="">---- Select Outlet ----');
	});
	$(".operator").change(function() {
		operator_value_set();
	});
	operator_value_set();

	function operator_value_set() {
		var operator_value = $(".operator").val();
		if (operator_value == 3) {
			$('.between_value').show();
			$('.operator_memo_value').hide();
		} else if (operator_value == 1 || operator_value == 2) {
			$('.operator_memo_value').show();
			$('.between_value').hide();
		} else {
			$('.operator_memo_value').hide();
			$('.between_value').hide();
		}
	}


	$(".operator_p_count").change(function() {
		operator_p_count_set();
	});
	operator_p_count_set();

	function operator_p_count_set() {
		var operator_value = $(".operator_p_count").val();
		if (operator_value == 3) {
			$('.between_p_count').show();
			$('.operator_memo_product_count').hide();
		} else if (operator_value == 1 || operator_value == 2) {
			$('.operator_memo_product_count').show();
			$('.between_p_count').hide();
		} else {
			$('.operator_memo_product_count').hide();
			$('.between_p_count').hide();
		}
	}
	
	function get_program_officer_list(office_id) {
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL ?>memos/get_program_officer_list',
			data: 'office_id=' + office_id,
			cache: false,
			success: function(response) {
				$('.program_officer_id').html(response);
				<?php if (isset($this->request->data['Memo']['program_officer_id'])) { ?>
					$('.program_officer_id option[value="<?= $this->request->data['Memo']['program_officer_id'] ?>"]').attr("selected", true);
				<?php } ?>
			}
		});
	}


	$(document).ready(function(){
		$(".office_id").change(function(){

			var office_id = $('.office_id').val();
			
			var user_group_id = '<?=$usergroupid;?>';
			
			if( user_group_id != 1016){
				get_program_officer_list(office_id);
			}
			


		});
	});
	
</script>
<script>
	function PrintElem(elem) {
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
		mywindow.close();

		return true;
	}
</script>