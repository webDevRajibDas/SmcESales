<?php
App::import('Controller', 'ProgramsController');
$ProgramsController = new ProgramsController;					 
?>

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('LARC Program List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('programs','admin_add_larc')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add/Edit LARC Program'), array('action' => 'add_larc'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('Program', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%">	
                            	<?php echo $this->Form->input('program_type_id', array('type'=>'hidden','value'=>3)); ?>						
								<?php echo $this->Form->input('office_id', array('id'=>'office_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>false)); ?>
							</td>	
							<td width="50%"><?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>false)); ?></td>							
						</tr>					
						<tr>
							<td>							
								<?php echo $this->Form->input('status', array('id'=>'status', 'class' => 'form-control','empty'=>'---- Select ----', 'options' => $status, 'required'=>false)); ?>
							</td>
							<td><?php echo $this->Form->input('thana_id', array('id'=>'thana_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>false)); ?></td>														
						</tr>					
						<tr>
							<td></td>
							<td><?php echo $this->Form->input('market_id', array('id'=>'market_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>false)); ?></td>							
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
                <table id="BonusCards" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="20" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							
                            
                            
                            <th class="text-center">Office</th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Territory.territory_id','Territory'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Market.thana_id', 'Thana'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Market.market_id','Market'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Outlet.name','Outlet/Clinic Name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Doctor.name'); ?></th>
							
							<th class="text-center"><?php echo $this->Paginator->sort('assigned_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
							<th width="60" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($programs as $program): ?>
					<tr>
						<td class="text-center"><?php echo h($program['Program']['id']); ?></td>
                        
                        <td class="text-left"><?php echo h($program['Office']['office_name']); ?></td>
                        <td class="text-left"><?php echo h($program['Territory']['name']); ?></td>
                        <td class="text-left">
						<?=$ProgramsController->get_thana_info($program['Market']['thana_id'])['Thana']['name']?>
                        </td>
                        <td class="text-left"><?php echo h($program['Market']['name']); ?></td>
						<td class="text-left"><?php echo h($program['Outlet']['name']); ?></td>
                        
						<td class="text-left"><?php echo h($program['Doctor']['name']); ?></td>
						
						
						<td class="text-center"><?php echo $this->App->dateformat($program['Program']['assigned_date']); ?></td>
						<td class="text-center">
						<?php 
							if($program['Program']['status'] == 1)
							{
								echo '<span class="btn btn-success btn-xs">Assigned</span>';
							}else if($program['Program']['status'] == 2){
								echo '<span class="btn btn-danger btn-xs">De-Assigned</span>';
							}else{
								echo '<span class="btn btn-warning btn-xs">Not Assigned</span>';
							}
						 ?>
						</td>
						<td class="text-center">
							<?php if($this->App->menu_permission('programs','admin_edit_larc')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit_larc', $program['Program']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Deassigned')); } ?>
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
$(document).ready(function(){
	$('#office_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});
	
	$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'markets/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':''}
	});	
	
	$('#office_id').change(function (){
		$('#market_id').html('<option value="">---- Select -----</option>');
	});	
	
});
</script>