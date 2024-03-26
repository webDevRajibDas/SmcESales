<?php


?>
<style>
	.draft {
		padding: 0px 15px;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Credit Memo Transfer'); ?></h3>

			</div>
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('CreditMemoTransfer', array('role' => 'form', 'action' => 'filter')); ?>
					<table class="search">

						<tr>
							<td><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true)); ?></td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true)); ?>
							</td>
						</tr>
						<tr>
							<td><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----')); ?></td>
							<td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'required' => false, 'empty' => '---- Select Territory ----')); ?></td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
				<div class="table-responsive">
					<table id="SoCreditCollection" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>


								<th class="text-center"><?php echo $this->Paginator->sort('Office.name', 'Area Office'); ?></th>

								<th class="text-center"><?php echo $this->Paginator->sort('Territory.name', 'Territory'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('SoCreditCollection.memo_no', 'Memo No'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('SoCreditCollection.date', 'Memo Date'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('SalesPerson.name', 'Memo Created By'); ?></th>
								<!-- <th class="text-center"><?php echo $this->Paginator->sort('CollectionPeople.name', 'Collection By'); ?></th> -->

								<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php $serial = 1;
							if (isset($SoCreditCollections)) {
								foreach ($SoCreditCollections as $memo) : ?>
									<tr>
										<td align="center"><?php echo h($serial++); ?></td>
										<td align="center"><?php echo h($memo['Office']['office_name']); ?></td>
										<td class="text-left"><?php echo h($memo['Territory']['name']); ?></td>
										<td class="text-left"><?php echo h($memo['SoCreditCollection']['memo_no']); ?></td>
										<td class="text-left"><?php echo h($memo['SoCreditCollection']['date']); ?></td>
										<td class="text-left"><?php echo h($memo['SalesPerson']['name']); ?></td>




										<td class="text-center">
											<?php if ($this->App->menu_permission('CreditMemoTransfers', 'admin_confirm')) {
												echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'confirm', $memo['SoCreditCollection']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Confirm'));
											} ?>

										</td>
									</tr>
							<?php endforeach;
							} ?>
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

<script>
	$(document).ready(function() {

		$('.office_id').selectChain({
			target: $('.territory_id'),
			value: 'name',
			url: '<?= BASE_URL . 'credit_memo_transfers/get_territory_list' ?>',
			type: 'post',
			data: {
				'office_id': 'office_id'
			},
			afterSuccess: function() {
				<?php if ($this->request->data['CreditMemoTransfer']['territory_id']) { ?>
					$('.territory_id').val(<?php echo $this->request->data['CreditMemoTransfer']['territory_id']; ?>);
				<?php } ?>
			}
		});



		if ($('.office_id').val() != '') {
			$('.office_id').trigger('change');
		}

	});
</script>