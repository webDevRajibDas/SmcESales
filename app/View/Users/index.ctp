<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Users'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New User'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="Users" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('user_group_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('username'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('password'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('email'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('active'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('created'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('modified'); ?></th>
							<th class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($users as $user): ?>
					<tr>
						<td class="text-center"><?php echo h($user['User']['id']); ?></td>
						<td class="text-center">
							<?php echo $this->Html->link($user['UserGroup']['name'], array('controller' => 'user_groups', 'action' => 'view', $user['UserGroup']['id'])); ?>
						</td>
						<td class="text-center"><?php echo h($user['User']['username']); ?></td>
						<td class="text-center"><?php echo h($user['User']['password']); ?></td>
						<td class="text-center"><?php echo h($user['User']['email']); ?></td>
						<td class="text-center"><?php echo h($user['User']['name']); ?></td>
						<td class="text-center"><?php echo h($user['User']['active']); ?></td>
						<td class="text-center"><?php echo h($user['User']['created']); ?></td>
						<td class="text-center"><?php echo h($user['User']['modified']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('return_challans','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $user['User']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('return_challans','admin_territory_tag')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-tag"></i>'), array('action' => 'territory_tag', $user['User']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('return_challans','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $user['User']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('return_challans','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $user['User']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $user['User']['id'])); } ?>
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