

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			
            <div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Notundin Program List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('notundin_programs', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add/Edit Notundin Program'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>
            	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('NotundinProgram', array('role' => 'form', 'action'=>'filter')); ?>
					<table class="search">
										
						<tr>
							<td width="50%">							
								<?php echo $this->Form->input('institute_name', array('id'=>'institute_name', 'class' => 'form-control','required'=>false)); ?>
							</td>
							<td width="50%">
							<?php echo $this->Form->input('status', array('id'=>'status', 'class' => 'form-control','empty'=>'---- Select ----', 'options' => $status, 'required'=>false)); ?>
                            </td>														
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
                            <th class="text-left">Institute Name</th>
							<th class="text-center"><?php echo $this->Paginator->sort('assigned_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
							<th width="60" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($programs as $program): ?>
					<tr>
						<td class="text-center"><?php echo h($program['NotundinProgram']['id']); ?></td>
						<td class="text-left"><?php echo h($program['Institute']['name']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($program['NotundinProgram']['assigned_date']); ?></td>
						<td class="text-center">
						<?php 
						if($program['NotundinProgram']['status'] == 1)
						{
							echo '<span class="btn btn-success btn-xs">Assigned</span>';
						}else if($program['NotundinProgram']['status'] == 2){
							echo '<span class="btn btn-danger btn-xs">De-Assigned</span>';
						}
						?>
						</td>
						<td class="text-center">
							<?php if($this->App->menu_permission('notundin_programs', 'admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $program['NotundinProgram']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Deassigned')); } ?>
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
	
	/*$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'markets/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':''}
	});	*/
	
	
	
	
	
	$('#territory_id').selectChain({
		target: $('#thana_id'),
		value:'name',
		url: '<?= BASE_URL.'programs/get_thana_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':''}
	});
	
	$('#thana_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'programs/get_market_list'?>',
		type: 'post',
		data:{'thana_id': 'thana_id', 'location_type_id':''}
	});	
	
	$('#office_id').change(function (){
		$('#market_id').html('<option value="">---- Select -----</option>');
	});	
	$('#office_id').change(function (){
		$('#thana_id').html('<option value="">---- Select -----</option>');
	});	
	
	$('#territory_id').change(function (){
		$('#market_id').html('<option value="">---- Select -----</option>');
	});	
	$('#territory_id').change(function (){
		$('#thana_id').html('<option value="">---- Select -----</option>');
	});	
	
});
</script>