<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Visit Plan List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('visitPlanLists','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Plan'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                    <?php if($this->App->menu_permission('visitPlanLists','admin_set_visit_plan')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Set Visit Plan'), array('action' => 'set_visit_plan'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('VisitPlanList', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----','options'=>$offices)); ?></td>

							<td><?php echo $this->Form->input('date', array('class' => 'form-control datepicker','required'=>false)); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>

							<td><?php echo $this->Form->input('visit_status', array('class' => 'form-control','required'=>false)); ?></td>
						</tr>
						<tr>
							<td><?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets)); ?></td>

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
                <table id="VisitPlanLists" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('so_id','SO Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('market_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('visit_plan_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('visited_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_out_of_plan'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('visit_status'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($visitPlanLists as $visitPlanList): ?>
					<tr>
						<td class="text-center"><?php echo h($visitPlanList['VisitPlanList']['id']); ?></td>
						<td class="text-center"><?php echo h($visitPlanList['So']['name']); ?></td>
						<td class="text-center"><?php echo h($visitPlanList['Market']['name']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($visitPlanList['VisitPlanList']['visit_plan_date']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($visitPlanList['VisitPlanList']['visited_date']); ?></td>
						<td class="text-center"><?php echo h($visitPlanList['VisitPlanList']['is_out_of_plan']==1?'YES':'NO'); ?></td>
						<td class="text-center">
							<?php 
							if($visitPlanList['VisitPlanList']['visit_status'] == 'Pending')
							{
								echo '<span class="btn btn-warning btn-xs">Pending</span>';
							}elseif($visitPlanList['VisitPlanList']['visit_status'] == 'Visited'){
								echo '<span class="btn btn-success btn-xs">Visited</span>';
							}							
							?>
						</td>
						<td class="text-center">
							<?php // if($this->App->menu_permission('visitPlanLists','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $visitPlanList['VisitPlanList']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php // if($this->App->menu_permission('visitPlanLists','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $visitPlanList['VisitPlanList']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('visitPlanLists','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $visitPlanList['VisitPlanList']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $visitPlanList['VisitPlanList']['id'])); } ?>
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