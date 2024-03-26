<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Inventory Stores'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('inventoryStores','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Inventory Store'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="InventoryStatuses" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('store_type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Reference Id'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($inventoryStores as $inventoryStore): ?>
					<tr>
						<td class="text-center"><?php echo h($inventoryStore['InventoryStore']['id']); ?></td>
						<td class="text-left"><?php echo h($inventoryStore['InventoryStore']['name']); ?></td>
						<td class="text-left">
						<?php
						if($inventoryStore['InventoryStore']['store_type']==1){
							$type_name = 'ASO';
						}elseif($inventoryStore['InventoryStore']['store_type']==2){
							$type_name = 'CWH';
						}elseif($inventoryStore['InventoryStore']['store_type']==3){
							$type_name = 'HO';
						}elseif($inventoryStore['InventoryStore']['store_type']==4){
							$type_name = 'HR';
						}else{
							$type_name = '';
						}
						echo h($type_name);
						?>
						</td>
						<td class="text-left"><?php echo h($inventoryStore['InventoryStore']['reference_id']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('inventoryStores','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $inventoryStore['InventoryStore']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('inventoryStores','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $inventoryStore['InventoryStore']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $inventoryStore['InventoryStore']['id'])); } ?>
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