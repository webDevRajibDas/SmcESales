<?php
//pr($this->request->data);exit;

?>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Dist Gift Items List'); ?></h3>
			</div>
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('DistGiftItem', array('role' => 'form', 'action' => 'filter')); ?>
					<!-- <table class="search">
						<tr>
							<td width="50%"><?php //echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----','options'=>$offices)); 
											?></td>
							<td width="50%"><?php //echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>false)); 
											?></td>
						</tr>					
						<tr>
							<td><?php //echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); 
								?></td>
							<td><?php //echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>false)); 
								?></td>							
						</tr>
						<tr>
							<td>
								<?php //cho $this->Form->input('so_id', array('label'=>'SO :','id' => 'so_id','class' => 'form-control so_id','required'=>false,'empty'=>'---- Select SO ----','options'=>$salesPersons)); 
								?>
							</td>							
							<td></td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php //echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); 
								?>
								<?php //echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); 
								?>
							</td>						
						</tr>
					</table>	 -->
					<table class="search">

						<tr>
							<td class="required" width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'autocomplete' => 'off', 'placeholder' => 'Selected Date', 'id' => 'date_from', 'required' => true)); ?>
							</td>
							<td class="required">
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'autocomplete' => 'off', 'placeholder' => 'Selected Date', 'id' => 'date_to', 'required' => true)); ?>
							</td>
						</tr>
						<tr>
							<?php if (isset($region_offices)) { ?>
								<td class="required" width="50%">
									<?php
									if (count($region_offices) > 1) {
										echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'empty' => '---- Head Office ----', 'options' => $region_offices,));
									} else {
										echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'options' => $region_offices));
									}
									?>

								</td>
							<?php } ?>
							<td class="required" width="50%">
								<?php
								if (count($offices) > 1) {
									echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id',  'empty' => '---- Select Office ----'));
								} else {
									echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id'));
								}
								?>

							</td>
						</tr>
						<tr>

							<td class="distributor_list">
								<?php
								if (isset($distributor_list))
									echo $this->Form->input('distributor_id', array('class' => 'form-control distributor_id', 'empty' => '--- Select---', 'options' => $distributor_list, 'label' => 'Distributor'));
								?>

							</td>
							<td class="route_list">
								<?php
								if (isset($route_list))
									echo $this->Form->input('dist_route_id', array('class' => 'form-control dist_route_id', 'empty' => '--- Select---', 'options' => $route_list, 'label' => 'Route'));
								?>

							</td>
						</tr>

						<tr>
							<td class="market_list">
								<?php
								if (isset($market_list))
									echo $this->Form->input('dist_market_id', array('class' => 'form-control market_id', 'empty' => '--- Select---', 'options' => $market_list, 'label' => 'Distributor Market'));
								?>

							</td>

						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'id' => 'search_button', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<?php if ($dist_gift_items) { ?>
									<a class="btn btn-success" id="download_xl">Download XL</a>
								<?php } ?>

							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>

				<table id="DistGiftItem" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="60" class="text-center"><?php //echo $this->Paginator->sort('id'); 
																?>Serial</th>
							<th class="text-center"><?php echo $this->Paginator->sort('distributor_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('DistSalesRepresentative.name', 'SR'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('route'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('market'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Outlet.name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('memo_no'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort(''); 
													?>Remarks</th>
							<th class="text-center"><?php echo $this->Paginator->sort('date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('quantity'); ?></th>
							<!-- <th width="120" class="text-center"><?php echo __('Actions'); ?></th> -->
						</tr>
					</thead>

					<tbody>
						<?php $serial = 1;
						foreach ($dist_gift_items as $item) : ?>
							<tr>
								<td class="text-center"><?php /*echo h($item['GiftItem']['id']);*/ echo $serial++; ?></td>
								<td class="text-center"><?php echo h($item['DistGiftItem']['distributor']); ?></td>
								<td class="text-center"><?php echo h($item['DistSalesRepresentative']['name']); ?></td>
								<td class="text-center"><?php echo h($item['DistGiftItem']['route']); ?></td>
								<td class="text-center"><?php echo h($item['DistGiftItem']['market']); ?></td>
								<td class="text-center"><?php echo h($item['DistOutlet']['name']); ?></td>
								<td class="text-center"><?php echo h($item['DistGiftItem']['memo_no']); ?></td>
								<td class="text-center"><?php echo h($item['DistGiftItem']['remarks']); ?></td>
								<td class="text-center"><?php echo $this->App->dateformat($item['DistGiftItem']['date']); ?></td>
								<td class="text-center"><?php echo h($item['DistGiftItem']['product']); ?></td>
								<td class="text-center"><?php echo h($item['DistGiftItem']['quantity']); ?></td>
								<!-- <td class="text-center">
							<?php //if($this->App->menu_permission('dist_gift_items','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $item['GiftItem']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } 
							?>
						</td> -->
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
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
<!-- <script>
$('#office_id').selectChain({
	target: $('#territory_id'),
	value:'name',
	url: '<?= BASE_URL . 'sales_people/get_distributor_list'; ?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});

$('.territory_id').selectChain({
	target: $('.so_id'),
	value:'name',
	url: '<?= BASE_URL . 'sales_people/get_so_list'; ?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});

$('.office_id').change(function(){
	$('.so_id').html('<option value="">---- Select SO ----');
});
</script> -->

<script type="text/javascript">
	$(document).ready(function() {
		if ($('#office_id').val() != '') {
			get_distributor_list($('#office_id').val());
		}
		$('#office_id').change(function() {

			get_distributor_list($(this).val());
		});

		function get_distributor_list(office_id) {
			$.ajax({
				type: "POST",
				url: '<?= BASE_URL ?>dist_gift_items/get_distributor_list',
				data: 'office_id=' + office_id,
				cache: false,
				success: function(response) {
					$('.distributor_list').html(response);
					<?php if (isset($this->request->data['DistGiftItem']['distributor_id'])) { ?>
						if ($('.distributor_id option[value="<?= $this->request->data['DistGiftItem']['distributor_id'] ?>"]').attr("selected", true)) {
							get_route_list($('.distributor_id').val());
						}
					<?php } ?>
				}
			});
		}
		$('.region_office_id').selectChain({
			target: $('.office_id'),
			value: 'name',
			url: '<?= BASE_URL . 'dist_gift_items/get_office_list'; ?>',
			type: 'post',
			data: {
				'region_office_id': 'region_office_id'
			}
		});

		function get_route_list(distributor_id) {
			$.ajax({
				type: "POST",
				url: '<?= BASE_URL ?>dist_gift_items/get_route_list',
				data: 'distributor_id=' + distributor_id,
				cache: false,
				success: function(response) {
					$('.route_list').html(response);
					<?php if (isset($this->request->data['DistGiftItem']['dist_route_id'])) { ?>
						if ($('.dist_route_id option[value="<?= $this->request->data['DistGiftItem']['dist_route_id'] ?>"]').attr("selected", true)) {
							get_market_list($('.dist_route_id').val());
						}
					<?php } ?>
				}
			});
		}

		if ($('.distributor_id').val() != '') {
			get_route_list($('.distributor_id').val());
		}
		$('body').on('change', '.distributor_id', function() {

			get_route_list($(this).val());
		});

		/*-----------------------------------------*/
		function get_market_list(dist_route_id) {
			$.ajax({
				type: "POST",
				url: '<?= BASE_URL ?>dist_gift_items/get_market_list',
				data: 'dist_route_id=' + dist_route_id,
				cache: false,
				success: function(response) {
					$('.market_list').html(response);
					<?php if (isset($this->request->data['DistGiftItem']['dist_market_id'])) { ?>
						$('.dist_market_id option[value="<?= $this->request->data['DistGiftItem']['dist_market_id'] ?>"]').attr("selected", true);
					<?php } ?>
				}
			});
		}
		if ($('.dist_route_id').val() != '') {
			get_market_list($('.dist_route_id').val());
		}
		$('body').on('change', '.dist_route_id', function() {

			get_market_list($(this).val());
		});

		/*-----------------------------------------*/


		$("#download_xl").click(function(e) {
			e.preventDefault();
			var loginForm = $(this).closest('form').serializeArray();
			var loginFormObject = {};
			$.each(loginForm,
				function(i, v) {
					loginFormObject[v.name] = v.value;
				});
			console.log(loginFormObject);
			var html = '';
			$.ajax({
				url: '<?= BASE_URL . 'dist_gift_items/download_xl'; ?>',
				type: 'POST',
				data: loginFormObject,
				cache: false,
				success: function(response) {
					// console.log(response);
					html += response;
					var blob = new Blob([html], {
						type: 'data:application/vnd.ms-excel'
					});
					var downloadUrl = URL.createObjectURL(blob);
					var a = document.createElement("a");
					a.href = downloadUrl;
					a.download = "DistGiftItemList.xls";
					document.body.appendChild(a);
					a.click();
				}

			});

		});
	})
</script>