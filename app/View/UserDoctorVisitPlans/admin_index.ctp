<?php
	// pr($userDoctorVisitPlans);exit;
?>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('User Doctor Visit Plans'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('userDoctorVisitPlans','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New User Doctor Visit Plan'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<table id="UserDoctorVisitPlans" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('fiscal_year_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('user_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('total','Total Doctor'); ?></th>
							<!-- <th class="text-center"><?php //echo $this->Paginator->sort('created_at'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('created_by'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('updated_at'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('updated_by'); ?></th> -->
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($userDoctorVisitPlans as $userDoctorVisitPlan): ?>
							<tr>
								<td class="text-center"><?php echo h($userDoctorVisitPlan['UserDoctorVisitPlan']['id']); ?></td>
								<td class="text-center">
									<?php echo $this->Html->link($userDoctorVisitPlan['FiscalYear']['year_code'], array('controller' => 'fiscal_years', 'action' => 'view', $userDoctorVisitPlan['FiscalYear']['id'])); ?>
								</td>
								<td class="text-center">
									<?php echo $this->Html->link($userDoctorVisitPlan['SalesPerson']['name'], array('controller' => 'users', 'action' => 'view', $userDoctorVisitPlan['User']['id'])); ?>
								</td>
								<td class="text-center">
									<?php echo h($userDoctorVisitPlan[0]['total']); ?>
								</td>
						<!-- <td class="text-center"><?php //echo h($userDoctorVisitPlan['UserDoctorVisitPlan']['created_at']); ?></td>
						<td class="text-center"><?php //echo h($userDoctorVisitPlan['UserDoctorVisitPlan']['created_by']); ?></td>
						<td class="text-center"><?php //echo h($userDoctorVisitPlan['UserDoctorVisitPlan']['updated_at']); ?></td>
						<td class="text-center"><?php //echo h($userDoctorVisitPlan['UserDoctorVisitPlan']['updated_by']); ?></td> -->
						<td class="text-center">
							<?php if($this->App->menu_permission('userDoctorVisitPlans','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $userDoctorVisitPlan['UserDoctorVisitPlan']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('userDoctorVisitPlans','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $userDoctorVisitPlan['UserDoctorVisitPlan']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('userDoctorVisitPlans','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $userDoctorVisitPlan['UserDoctorVisitPlan']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $userDoctorVisitPlan['UserDoctorVisitPlan']['id'])); } ?>
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