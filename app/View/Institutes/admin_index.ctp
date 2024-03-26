<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Institutes'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('institutes','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Institute'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('Institute', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td>
								<?php echo $this->Form->input('short_name', array('class' => 'form-control','required'=>false)); ?>
							</td>
							<td>							
								<?php echo $this->Form->input('name', array('class' => 'form-control','required'=>false)); ?>
							</td>							
						</tr>					
						<tr>
							<td>
								<?php echo $this->Form->input('type', array('class' => 'form-control','options'=>$institute_list,'required'=>false,'empty'=>'---- Select ----')); ?>
							</td>
							<td>							
								<?php echo $this->Form->input('telephone', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?>
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
                <table id="Institutes" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('short_name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('address'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('email'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('telephone'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('contactname'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($institutes as $institute): ?>
					<tr>
						<td class="text-center"><?php echo h($institute['Institute']['id']); ?></td>
						<td><?php echo h($institute['Institute']['short_name']); ?></td>
						<td><?php echo h($institute['Institute']['name']); ?></td>
						<td>
							<?php
							if($institute['Institute']['type']==1){
								echo h('NGO');
							}elseif($institute['Institute']['type']==2){
								echo h('Institute');
							}
							?>
						</td>
						<td><?php echo h($institute['Institute']['address']); ?></td>
						<td><?php echo h($institute['Institute']['email']); ?></td>
						<td><?php echo h($institute['Institute']['telephone']); ?></td>
						<td><?php echo h($institute['Institute']['contactname']); ?></td>
						<td>
							<?php 
								if($institute['Institute']['is_active']==1){
									echo h("Yes");
								}elseif($institute['Institute']['is_active']==0){
									echo h("No");
								}
							?>
						</td>
						<td class="text-center">
							<?php //if($this->App->menu_permission('institutes','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $institute['Institute']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('institutes','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $institute['Institute']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('institutes','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $institute['Institute']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $institute['Institute']['id'])); } ?>
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