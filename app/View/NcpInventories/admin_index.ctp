<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('NCP Inventories'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('currentInventories','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Inventory'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('CurrentInventory', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('product_code', array('class' => 'form-control','required'=>false)); ?></td>
							<td width="50%"><?php echo $this->Form->input('product_id', array('class' => 'form-control','empty'=>'---- Select Product ----','required'=>false)); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('store_id', array('class' => 'form-control','empty'=>'---- Select Store ----','required'=>false)); ?></td>
							<td width="50%"><?php echo $this->Form->input('product_categories_id', array('class' => 'form-control','empty'=>'---- Select Status ----','required'=>false)); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('inventory_status_id', array('type'=>'hidden','class' => 'form-control','required'=>false,'value'=>2)); ?></td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
				<table id="CurrentInventories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('store_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_unit'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_code'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('inventory_status_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('ProductCategory_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('qty','Quantity'); ?></th>
							<!-- <th class="text-center"><?php //echo $this->Paginator->sort('transaction_type_id'); ?></th> -->
							<th class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($currentInventories as $currentInventory): ?>
					<tr>
						<td><?php echo h($currentInventory['Store']['name']); ?></td>
						<td><?php echo h($currentInventory['Product']['name']); ?></td>
						<td><?php echo h($measurement_unit_list[$currentInventory['Product']['base_measurement_unit_id']]); ?></td>
						<td class="text-center"><?php echo h($currentInventory['Product']['product_code']); ?></td>
						<td class="text-center"><?php echo h($currentInventory['InventoryStatuses']['name']); ?></td>
						<td class="text-center"><?php echo h($currentInventory['ProductCategory']['name']); ?></td>
						<td class="text-center"><?php echo h($currentInventory[0]['total']<=0?'0.00':$currentInventory[0]['total']); ?></td>
						<!-- <td class="text-center"><?php //echo h($currentInventory['TransactionType']['name']); ?></td> -->
						<td class="text-center">
							<?php if($this->App->menu_permission('CurrentInventories','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'viewDetails', $currentInventory['CurrentInventory']['product_id'],$currentInventory['CurrentInventory']['store_id'],$currentInventory['CurrentInventory']['transaction_type_id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'View details')); } ?>
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
						<?php	echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>	
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>
								<?php
									echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
									echo $this->Paginator->numbers(array("separator" => "","currentTag" => "a", "currentClass" => "active","tag" => "li","first" => 1));
									echo $this->Paginator->next(__("next"), array("tag" => "li","currentClass" => "disabled"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
