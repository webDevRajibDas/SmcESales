<?php 

?><div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Delivery Schedules'); ?></h3>
				
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('DistOrderDeliverySchedule', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td class="required" width="50%">
							    <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?>
                            </td>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?>
                            </td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----','options'=>$offices)); ?></td>

							<td width="50%"><?php echo $this->Form->input('sr_id', array('id' => 'sr_id','class' => 'form-control sr_id','required'=>false,'empty'=>'---- Select SR ----','options'=>$srs)); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('distributor_id', array('id' => 'distributor_id','class' => 'form-control distributor_id','required'=>false,'empty'=>'---- Select Distributor ----','options'=>$distDistributors)); ?></td>
						
							<td width="50%"><?php echo $this->Form->input('status', array('id' => 'status','class' => 'form-control status','required'=>false,'empty'=>'---- Select Status ----','options'=>$status_list)); ?></td>

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
                <table id="Territories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sr_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('distributor_id'); ?></th>
							<!--<th class="text-center"><?php echo $this->Paginator->sort('process_status'); ?></th>-->
							<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($results as $result): ?>
					<tr>
						<td class="text-center"><?php echo h($result['DistOrderDeliverySchedule']['id']); ?></td>
						<td class="text-center"><?php echo h($result['DistSalesRepresentative']['name']); ?></td>
						<td class="text-center"><?php echo h($result['Office']['office_name']); ?></td>
						<td class="text-center"><?php echo h($result['DistDistributor']['name']); ?></td>
						<?php /*?><td class="text-center">
							<?php
								if($territory['DistOrderDeliverySchedule']['process_status']==0){
									echo h('Pending');
								}else{
									echo h('Complete');
								}
							?>
						</td><?php */?>
						<td class="text-center">
							<?php
								if($result['DistOrderDeliverySchedule']['status']==1){
									echo h('Success');
								}elseif($result['DistOrderDeliverySchedule']['status']==2){
									echo h('Cancel');
								}else{
									echo h('Fail');
								}
							?>
						</td>
						<td class="text-center">
							<?php if($this->App->menu_permission('DistOrderDeliverySchedules','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'view', $result['DistOrderDeliverySchedule']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							
							<?php if($this->App->menu_permission('DistOrderDeliverySchedules','admin_cancel')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'cancel', $result['DistOrderDeliverySchedule']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'cancel'), __('Are you sure you want to cancel # %s?', $result['DistOrderDeliverySchedule']['id'])); } ?>
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
$(document).ready(function () {
	$('#office_id').selectChain({
		target: $('#distributor_id'),
		value:'name',
		url: '<?= BASE_URL.'DistDistributors/get_dist_distributor_list'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});
	$('#distributor_id').selectChain({
		target: $('#sr_id'),
		value:'name',
		url: '<?= BASE_URL.'DistOrderDeliverySchedules/get_dist_distributor_sr_list'?>',
		type: 'post',
		data:{'distributor_id': 'distributor_id' }
	});
});
</script>