<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Inventory Adjustment List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('inventoryAdjustments','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Inventory Adjustment'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('InventoryAdjustment', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%">
							<?php echo $this->Form->input('status', array('type' => 'select', 'class' => 'form-control', 'empty' => '---- Select Status ----', 'options' => array(2=>'In',1=>'Out'))); ?>
							<?php //echo $this->Form->input('name', array('label'=>'Product Name :','class' => 'form-control','required'=>false)); ?>
							
							</td>
							<td width="50%">
							
							<?php echo $this->Form->input('created_at', array('type'=>'text','class' => 'form-control datepicker','required'=>false)); ?>
							
							</td>							
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
                <table id="InventoryAdjustments" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('store_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('remarks'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('created_at','Date'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('acknowledge_status'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($inventoryAdjustments as $inventoryAdjustment): ?>
					<tr>
						<td class="text-center"><?php echo h($inventoryAdjustment['InventoryAdjustment']['id']); ?></td>
						<td class="text-center"><?php echo h($inventoryAdjustment['Store']['name']); ?></td>
						<td class="text-center"><?php if($inventoryAdjustment['InventoryAdjustment']['status'] == 2){ echo 'In'; }else{ echo 'Out';}  ?></td>
						<td class="text-center"><?php echo h($inventoryAdjustment['InventoryAdjustment']['remarks']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($inventoryAdjustment['InventoryAdjustment']['created_at']); ?></td>
                        
                        <td align="center">
                            <?php echo $inventoryAdjustment['InventoryAdjustment']['acknowledge_status'] == 0 ? '<span class="btn btn-warning btn-xs">Pending</span>' : '<span class="btn btn-success btn-xs">Approved</span>'; ?>
                        </td>
                            
                        <td class="text-center">
                            <?php if($this->App->menu_permission('inventoryAdjustments','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $inventoryAdjustment['InventoryAdjustment']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
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