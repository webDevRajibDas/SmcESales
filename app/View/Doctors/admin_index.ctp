<?php //pr(compact('doctors'));?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Doctors'); ?></h3>
				<!-- 
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('doctors','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Doctor'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
				--> 
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('Doctor', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----','options'=>$offices)); ?></td>

							<td width="50%"><?php echo $this->Form->input('doctor_qualification_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select Qualification ----','options'=>$doctorQualifications ));  ?></td>
						</tr>					
						<tr>
							<td width="50%"><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>

							<td><?php echo $this->Form->input('doctor_type_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select Type ----','options'=>$doctorTypes)); ?></td>							
						</tr>
						<tr>
							<td><?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets)); ?></td>
							<td><?php echo $this->Form->input('district_id', array('id' => 'district_id','class' => 'form-control district_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$districts)); ?></td>

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
				<table id="Doctors" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="60" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('doctor_qualification_id','Qualification'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('doctor_type_id','Type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('gender'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Thana.name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('market_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('outlet_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('clinic_name'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($doctors as $doctor): ?>
					<tr>
						<td class="text-center"><?php echo h($doctor['Doctor']['id']); ?></td>
						<td class="text-left"><?php echo h($doctor['Doctor']['name']); ?></td>
						<td class="text-left"><?php echo h($doctor['DoctorQualification']['title']); ?></td>
						<td class="text-left"><?php echo h($doctor['DoctorType']['title']); ?></td>
						<td class="text-left"><?php echo h($doctor['Doctor']['gender']); ?></td>
						<td class="text-left"><?php echo h($doctor['Territory']['name']); ?></td>
						<td class="text-left"><?php echo h($doctor['Thana']['name']); ?></td>
						<td class="text-left"><?php echo h($doctor['Market']['name']); ?></td>
						<td class="text-left"><?php echo h($doctor['Outlet']['name']); ?></td>
						<td class="text-left"><?php echo h($doctor['Doctor']['clinic_name']); ?></td>
						<td class="text-center">
							<?php // if($this->App->menu_permission('doctors','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $doctor['Doctor']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('doctors','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $doctor['Doctor']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('doctors','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $doctor['Doctor']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $doctor['Doctor']['id'])); } ?>
							
							<?php // if($this->App->menu_permission('doctors','admin_change_status')){ echo $this->Form->postLink(__($doctor['Doctor']['is_active']==1?'<i class="glyphicon glyphicon-eye-open"></i>':'<i class="glyphicon glyphicon-eye-close"></i>'), array('action' => 'change_status', $doctor['Doctor']['id']), array('class' => 'btn btn-info btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Change Status'), __('Are you sure you want to Change Status # %s?', $doctor['Doctor']['name'])); } ?>
							
							<?php
                            
								if($doctor['Doctor']['is_active']==1)
								{
									$tolpit_text="Change Status to Inactive";
									$change_status_text=" to Inactive";
								}
								else 
								{
									$tolpit_text="Change Status to Active";
									$change_status_text="to Active";
								}
								
								if($this->App->menu_permission('doctors','admin_change_status')){ echo $this->Form->postLink(__($doctor['Doctor']['is_active']==1?'<i class="glyphicon glyphicon-eye-open"></i>':'<i class="glyphicon glyphicon-eye-close"></i>'), array('action' => 'change_status', $doctor['Doctor']['id']), array('class' => 'btn btn-info btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => $tolpit_text), __('Are you sure you want to Change Status %s # %s?', $change_status_text,$doctor['Doctor']['name'])); } 
							?>
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
<script>
$('#office_id').selectChain({
	target: $('#territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_territory_list';?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});
$('.territory_id').selectChain({
	target: $('.market_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_market';?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});
</script>