<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Thanas'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('thanas','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Thana'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
            
            
            
			<div class="box-body">
                        
            	<div class="search-box">
					<?php echo $this->Form->create('Thana', array('role' => 'form', 'action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td>							
								<?php echo $this->Form->input('name', array('class' => 'form-control', 'required'=>false)); ?>
							</td>
						
							<td>							
								<?php echo $this->Form->input('district_id', array('class' => 'form-control','empty'=>'---- Select ----', 'required'=>false)); ?>
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
                
                
                
                
                <table id="Thanas" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('district_id'); ?></th>
							<th width="100" class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($thanas as $thana): ?>
					<tr>
						<td class="text-center"><?php echo h($thana['Thana']['id']); ?></td>
						<td class="text-left"><?php echo h($thana['Thana']['name']); ?></td>
						<td class="text-left"><?php echo h($thana['District']['name']); ?></td>
						<td class="text-left">
							<?php
								if($thana['Thana']['is_active']==1){
									echo h('Yes');
								}else{
									echo h('No');
								}
							?>
						</td>
						<td class="text-center">
							<?php if($this->App->menu_permission('thanas','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $thana['Thana']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('thanas','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $thana['Thana']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $thana['Thana']['id'])); } ?>
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