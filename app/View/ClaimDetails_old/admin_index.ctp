<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Claim Details'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('claimDetails','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Claim Detail'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="ClaimDetails" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('claim_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('measurement_unit_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('challan_qty'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('claim_qty'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('batch_no'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('expire_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('inventory_status_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('remarks'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($claimDetails as $claimDetail): ?>
					<tr>
						<td class="text-center"><?php echo h($claimDetail['ClaimDetail']['id']); ?></td>
						<td class="text-center">
			<?php echo $this->Html->link($claimDetail['Claim']['claim_no'], array('controller' => 'claims', 'action' => 'view', $claimDetail['Claim']['id'])); ?>
		</td>
						<td class="text-center">
			<?php echo $this->Html->link($claimDetail['Product']['name'], array('controller' => 'products', 'action' => 'view', $claimDetail['Product']['id'])); ?>
		</td>
						<td class="text-center">
			<?php echo $this->Html->link($claimDetail['MeasurementUnit']['name'], array('controller' => 'measurement_units', 'action' => 'view', $claimDetail['MeasurementUnit']['id'])); ?>
		</td>
						<td class="text-center"><?php echo h($claimDetail['ClaimDetail']['challan_qty']); ?></td>
						<td class="text-center"><?php echo h($claimDetail['ClaimDetail']['claim_qty']); ?></td>
						<td class="text-center"><?php echo h($claimDetail['ClaimDetail']['batch_no']); ?></td>
						<td class="text-center"><?php echo h($claimDetail['ClaimDetail']['expire_date']); ?></td>
						<td class="text-center">
			<?php echo $this->Html->link($claimDetail['InventoryStatus']['name'], array('controller' => 'inventory_statuses', 'action' => 'view', $claimDetail['InventoryStatus']['id'])); ?>
		</td>
						<td class="text-center"><?php echo h($claimDetail['ClaimDetail']['remarks']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('claimDetails','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $claimDetail['ClaimDetail']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('claimDetails','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $claimDetail['ClaimDetail']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('claimDetails','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $claimDetail['ClaimDetail']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $claimDetail['ClaimDetail']['id'])); } ?>
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