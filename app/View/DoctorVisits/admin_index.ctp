<?php 
	// pr(compact('visits'));exit;
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Doctor Visit List'); ?></h3>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('DoctorVisit', array('role' => 'form','action'=>'filter')); ?>
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
							<td></td>
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
				<table id="Visits" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="60" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Doctor.name','Doctor Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Territory.name','Territory'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('thana_name','Thana'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Market.name','Market'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('outlet_name','Outlet/Clinic Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('visit_date'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($visits as $visit): ?>
					<tr>
						<td class="text-center"><?php echo h($visit['DoctorVisit']['id']); ?></td>
						<td class="text-center"><?php echo h($visit['Doctor']['name']); ?></td>
						<td class="text-center"><?php echo h($visit['Territory']['name']); ?></td>
						<td class="text-center"><?php echo h($visit['Thana']['name']); ?></td>
						<td class="text-center"><?php echo h($visit['Market']['name']); ?></td>
						<td class="text-center"><?php if(!empty($visit['Outlet']['name']))echo h($visit['Outlet']['name']);else echo h($visit['DoctorVisit']['clinic_name']) ?></td>
						<td class="text-center"><?php echo $this->App->datetimeformat($visit['DoctorVisit']['visit_date_time']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('doctor_visits','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $visit['DoctorVisit']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
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