<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Employees'); ?></h3>
				<div class="box-tools pull-right">
					<?php if ($this->App->menu_permission('employees', 'add')) { echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add New Employee'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="employees" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center">ID</th>
							<th class="text-center">Full Name</th>
							<th class="text-center">Email</th>
							<th class="text-center">Username</th>
							<th class="text-center"><?php echo $this->Paginator->sort('salary'); ?></th>
							<th class="text-center">Department</th>
							<th class="text-center">Phone</th>
							<th class="text-center">Created At</th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody style="text-align:center">


					<?php foreach($employees as $data): ?>
						<tr>
							<td><?php echo $data['Employee']['id'];?></td>
							<td><?php echo $data['Employee']['first_name'];?><?php echo $data['Employee']['last_name'];?></td>
							<td><?php echo $data['Employee']['email'];?></td>
							<td><?php echo $data['Employee']['username'];?></td>
							<td><?php echo number_format((float)$data['Employee']['salary'], 2, '.', '');?></td>
							<td><?php echo $data['Employee']['department'];?></td>
							<td><?php echo $data['Employee']['phone'];?></td>
							<td><?php echo date('d.m.Y', strtotime($data['Employee']['created_at'])); ?></td>

							<td>
								<?php echo $this->Html->link('Edit',array('action'=>'edit/'.$data['Employee']['id']),array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));?>
								<?php echo $this->Html->link('Delete', array('action' => 'delete', $data['Employee']['id']),array('class'=>'btn btn-danger btn-xs'), null, 'Are you sure?' )?>
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
								?>							</ul>
						</div>
					</div>
				</div>				
			</div>			
		</div>
	</div>
</div>