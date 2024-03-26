<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Project Ngo Outlets'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('projectNgoOutlets','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Project Ngo Outlet'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="ProjectNgoOutlets" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('project_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('outlet_id'); ?></th>
							<th class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($projectNgoOutlets as $projectNgoOutlet): ?>
					<tr>
						<td class="text-center"><?php echo h($projectNgoOutlet['ProjectNgoOutlet']['id']); ?></td>
						<td class="text-center">
			<?php echo $this->Html->link($projectNgoOutlet['Project']['name'], array('controller' => 'projects', 'action' => 'view', $projectNgoOutlet['Project']['id'])); ?>
		</td>
						<td class="text-center">
			<?php echo $this->Html->link($projectNgoOutlet['Outlet']['name'], array('controller' => 'outlets', 'action' => 'view', $projectNgoOutlet['Outlet']['id'])); ?>
		</td>
						<td class="text-center">
							<?php if($this->App->menu_permission('projectNgoOutlets','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $projectNgoOutlet['ProjectNgoOutlet']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('projectNgoOutlets','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $projectNgoOutlet['ProjectNgoOutlet']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('projectNgoOutlets','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $projectNgoOutlet['ProjectNgoOutlet']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $projectNgoOutlet['ProjectNgoOutlet']['id'])); } ?>
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