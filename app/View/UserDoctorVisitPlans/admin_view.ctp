<?php
	// pr($UserDoctorVisitPlanLists);exit;
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('User to Doctor List'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Doctor Visit Plan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
                
			</div>	
            
            
			<div class="box-body">
            
			<!-- 	<div class="search-box">
					<?php echo $this->Form->create('UserDoctorVisitPlanList', array('role' => 'form', 'action'=>'filter')); ?>
					<table class="search">
						<tr>

							<td><?php echo $this->Form->input('date', array('label'=>'Visited Date','class' => 'form-control datepicker','required'=>false)); ?></td>
							<td><?php echo $this->Form->input('visit_status', array('class' => 'form-control','required'=>false)); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----')); ?></td>
							<td><?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----')); ?></td>
							
						</tr>
						
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div> -->
                
                <table id="VisitPlanLists" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('market_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('doctor_id'); ?></th>
                            <th class="text-center"><?php echo h('Outlet/Clinic Name'); ?></th>
							<!-- <th class="text-center"><?php //echo $this->Paginator->sort('visited_date'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('is_out_of_plan'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('visit_status'); ?></th> -->
						</tr>
					</thead>
					<tbody>
					<?php foreach ($UserDoctorVisitPlanLists as $visitPlanList): ?>
					<tr>
						<td class="text-center"><?php echo h($visitPlanList['UserDoctorVisitPlanList']['id']); ?></td>
					
                        <td class="text-center"><?php echo h($visitPlanList['Territory']['name']); ?></td>
						<td class="text-center"><?php echo h($visitPlanList['Market']['name']); ?></td>
                        <td class="text-center"><?php echo h($visitPlanList['Doctor']['name']); ?></td>
                        <td class="text-center"><?php if($visitPlanList['Outlet']['name']){echo h($visitPlanList['Outlet']['name']);}else{echo $visitPlanList['Doctor']['clinic_name'];} ?></td>
						<!-- <td class="text-center"><?php //echo $this->App->dateformat($visitPlanList['UserDoctorVisitPlanList']['visited_date']); ?></td>
						<td class="text-center"><?php //echo h($visitPlanList['UserDoctorVisitPlanList']['is_out_of_plan']==0?'NO':'YES'); ?></td>
						<td class="text-center">
							<?php 
							/*if($visitPlanList['UserDoctorVisitPlanList']['visit_status'] == 'Pending')
							{
								echo '<span class="btn btn-warning btn-xs">Pending</span>';
							}elseif($visitPlanList['UserDoctorVisitPlanList']['visit_status'] == 'Visited'){
								echo '<span class="btn btn-success btn-xs">Visited</span>';
							}*/							
							?>
						</td> -->
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
	
	$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'UserDoctorVisitPlanLists/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id'}
	});
	
});
</script>