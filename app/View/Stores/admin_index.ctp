<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Stores'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('stores','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Store'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('Store', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('store_type_id', array('class' => 'form-control','empty'=>'---- Select Type ----','options'=>$store_types,'required'=>false)); ?></td>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id'=>'office_id','class' => 'form-control','empty'=>'---- Select Office ----','required'=>false)); ?></td>							
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
                <div class="table-responsive">	
				<table id="Stores" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sap_store_code'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('store_type_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($stores as $store): ?>
					<tr>
						<td class="text-center"><?php echo h($store['Store']['id']); ?></td>
						<td><?php echo h($store['Store']['name']); ?></td>
						<td><?php echo h($store['Store']['sap_store_code']); ?></td>
						<td><?php echo h($store['StoreType']['name']); ?></td>						
						</td>
						<td class="text-center"><?php echo h($store['Office']['office_name']); ?></td>
						<td class="text-center"><?php echo h($store['Territory']['name']); ?></td>
                                                <td class="text-center"><?php echo h($store['Store']['address']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('stores','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $store['Store']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('stores','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $store['Store']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $store['Store']['id'])); } ?>
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
                </div>
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