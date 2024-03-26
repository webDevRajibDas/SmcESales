<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Current Inventories'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Current Inventory List'), array('action' => 'admin_index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>	
			<div class="box-body">
             <!--   <div class="search-box">
					<?php echo $this->Form->create('CurrentInventory', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('product_code', array('class' => 'form-control','required'=>false)); ?></td>							
							<td width="50%"><?php echo $this->Form->input('batch_number', array('class' => 'form-control','required'=>false)); ?></td>							
						</tr>					
						<tr>
							<td width="50%"><?php echo $this->Form->input('store_id', array('class' => 'form-control','empty'=>'---- Select Store ----','required'=>false)); ?></td>
							<td width="50%"><?php echo $this->Form->input('expire_date', array('type'=>'text','class' => 'form-control datepicker','required'=>false)); ?></td>							
						</tr>					
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>						
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>-->
				<table id="CurrentInventories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('store_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_code'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('inventory_status_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('batch_number'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('expire_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('qty','Quantity'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($currentInventories as $currentInventory): ?>
					<tr>
						<td class="text-center"><?php echo h($currentInventory['CurrentInventory']['id']); ?></td>
						<td><?php echo h($currentInventory['Store']['name']); ?></td>
						<td><?php echo h($currentInventory['Product']['name']); ?></td>
						<td class="text-center"><?php echo h($currentInventory['Product']['product_code']); ?></td>
						<td class="text-center"><?php echo h($currentInventory['InventoryStatuses']['name']); ?></td>
						<td class="text-center"><?php echo h($currentInventory['CurrentInventory']['batch_number']); ?></td>
						<td class="text-center"><?php if($currentInventory['CurrentInventory']['expire_date'] != '0000-00-00'){ echo $this->App->expire_dateformat($currentInventory['CurrentInventory']['expire_date']); } ?></td>
						<td class="text-center"><?php echo h($currentInventory['CurrentInventory']['qty']<=0?'0.00':$currentInventory['CurrentInventory']['qty']); ?></td>
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