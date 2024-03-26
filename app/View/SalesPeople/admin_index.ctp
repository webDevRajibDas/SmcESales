<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sales People'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('salesPeople','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Sales Person'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('SalesPerson', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td>
								<?php echo $this->Form->input('name', array('class' => 'form-control','required'=>false)); ?>
							</td>
							<td>							
								<?php echo $this->Form->input('code', array('class' => 'form-control','required'=>false)); ?>
							</td>							
						</tr>					
						<tr>
							<td>
								<?php echo $this->Form->input('designation_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?>
							</td>
							<td>							
								<?php echo $this->Form->input('office_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?>
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
				<table id="SalesPeople" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('code'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('designation_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('parent_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory_name'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($salesPeople as $salesPerson): ?>
					<tr>
						<td class="text-center"><?php echo h($salesPerson['SalesPerson']['id']); ?></td>
						<td class="text-left"><?php echo h($salesPerson['SalesPerson']['code']); ?></td>
						<td class="text-left"><?php echo h($salesPerson['SalesPerson']['name']); ?></td>
						<td class="text-left"><?php echo h($salesPerson['Designation']['designation_name']); ?></td>
						<td class="text-left"><?php echo h($salesPerson['ParentSalesPerson']['name']); ?></td>
						<td class="text-left"><?php echo h($salesPerson['Office']['office_name']); ?></td>
						<td class="text-left"><?php echo h($salesPerson['Territory']['name']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('salesPeople','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $salesPerson['SalesPerson']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('salesPeople','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $salesPerson['SalesPerson']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $salesPerson['SalesPerson']['id'])); } ?>
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