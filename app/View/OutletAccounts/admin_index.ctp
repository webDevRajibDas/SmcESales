<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Outlet Accounts'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('outletAccounts','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Outlet Account'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="OutletAccounts" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('phone_number'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('password'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('user_name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('outlet_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('created_at'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('updated_at'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('updated_by'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($outletAccounts as $outletAccount): ?>
					<tr>
						<td class="text-center"><?php echo h($outletAccount['OutletAccount']['id']); ?></td>
						<td class="text-center"><?php echo h($outletAccount['OutletAccount']['phone_number']); ?></td>
						<td class="text-center"><?php echo h($outletAccount['OutletAccount']['password']); ?></td>
						<td class="text-center"><?php echo h($outletAccount['OutletAccount']['user_name']); ?></td>
						<td class="text-center">
			<?php echo $this->Html->link($outletAccount['Outlet']['name'], array('controller' => 'outlets', 'action' => 'view', $outletAccount['Outlet']['id'])); ?>
		</td>
						<td class="text-center"><?php echo h($outletAccount['OutletAccount']['is_active']); ?></td>
						<td class="text-center"><?php echo h($outletAccount['OutletAccount']['status']); ?></td>
						<td class="text-center"><?php echo h($outletAccount['OutletAccount']['created_at']); ?></td>
						<td class="text-center"><?php echo h($outletAccount['OutletAccount']['updated_at']); ?></td>
						<td class="text-center"><?php echo h($outletAccount['OutletAccount']['updated_by']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('outletAccounts','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $outletAccount['OutletAccount']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('outletAccounts','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $outletAccount['OutletAccount']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('outletAccounts','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $outletAccount['OutletAccount']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $outletAccount['OutletAccount']['id'])); } ?>
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