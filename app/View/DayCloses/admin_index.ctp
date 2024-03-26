<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Day Close History'); ?></h3>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('DayClose', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?></td>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>false)); ?></td>							
						</tr>					
						<tr>
							<td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>false)); ?>
							</td>						
						</tr>					
						<!-- <tr>
							<td>
								<?php //echo $this->Form->input('sales_person_id', array('id' => 'sales_person_id','class' => 'form-control sales_person_id','required'=>false,'empty'=>'---- Select SO ----','options'=>$salesPersons)); ?>
							</td>
							<td></td>							
						</tr> -->						
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>						
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>
				<table id="DayClose" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('SalesPerson.name','SO Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Territory.name','Territory'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('closing_date'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach($list as $val): ?>
					<tr>
						<td align="center"><?php echo h($val['DayClose']['id']); ?></td>
						<td align="center"><?php echo h($val['SalesPerson']['name']); ?></td>
						<td align="center"><?php echo h($val['Territory']['name']); ?></td>
						<td align="center"><?php echo $this->App->datetimeformat($val['DayClose']['closed_at']); ?></td>						
					</tr>
					<?php 
					endforeach; 					
					?>					
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
$('.office_id').selectChain({
	target: $('.territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});

$('.territory_id').selectChain({
	target: $('.sales_person_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_so_list';?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});

$('.office_id').change(function(){
	$('.sales_person_id').html('<option value="">---- Select SO ----');
});
</script>