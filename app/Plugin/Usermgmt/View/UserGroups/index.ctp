<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('All Groups'); ?></h3>
				<div class="box-tools pull-right">
					<a href="<?php echo Router::url('/admin/addGroup'); ?>"><button class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> New Group</button></a>					
				</div>
			</div>	
            
            
			<div class="box-body">
            	
                <div class="table-responsive">	
                <table class="table table-bordered table-striped">
					<thead>
						<tr>												
							<th class="text-center" width="5%"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('alias_name'); ?></th>							
							<th class="text-center"><?php echo $this->Paginator->sort('created'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('modified'); ?></th>
							<th class="text-center" width="10%"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($userGroups as $row): ?>
						<tr>
							<td class="text-center"><?php echo h($row['UserGroup']['id']); ?></td>
							<td><?php echo h($row['UserGroup']['name']); ?></td>
							<td><?php echo h($row['UserGroup']['alias_name']); ?></td>							
							<td class="text-center">
							<?php echo date('d-M-Y',strtotime($row['UserGroup']['created'])); ?>
							</td>
							<td class="text-center">
							<?php echo date('d-M-Y',strtotime($row['UserGroup']['modified'])); ?>
							</td>
							<td class="text-center">
								<a class='btn btn-warning btn-xs' href="<?php echo $this->Html->url('/admin/editGroup/'.$row['UserGroup']['id']) ?>"><i class='glyphicon glyphicon-pencil'></i></a>
								<a class='btn btn-default btn-xs' href="<?php echo $this->Html->url('/admin/groupPermission/'.$row['UserGroup']['id']) ?>"><i class='glyphicon glyphicon-check'></i></a>
								<?php //echo $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', array('action' => 'deleteGroup', $row['UserGroup']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete','escape' => false, 'confirm' => __('Are you sure you want to delete this group? Delete it your own risk'))); ?>
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
								?>							</ul>
						</div>
					</div>
				</div>				
			</div>			
		</div>
	</div>
</div>