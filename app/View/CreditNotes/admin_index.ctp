<?php
	
	//pr($creditnotes);exit;

?>
<style>
	.draft{
		padding: 0px 15px;
	}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Credit Note List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('credit_notes','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Credit Note'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('CreditNote', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
											
						<tr>
							<td><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>false)); ?></td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>false)); ?>
							</td>
						</tr>	
						<tr>
							<td ><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----')); ?></td>
							<td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id', 'required'=>false, 'empty'=>'---- Select Territory ----')); ?></td>
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
                <div class="table-responsive">	
				<table id="SoCreditCollection" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							
							
							<th class="text-center"><?php echo $this->Paginator->sort('Office.office_name', 'Office'); ?></th>
					
							<th class="text-center"><?php echo $this->Paginator->sort('Territory.name', 'Territory'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Market.name', 'Market'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Outlet.name', 'Outlet'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('CreditNote.credit_number', 'Credit Number'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('CreditNote.created_at', 'Date'); ?></th>

							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php $serial=1;
					if(isset($creditnotes)){ 
					foreach ($creditnotes as $val): ?>					
					<tr>
						<td align="center"><?php echo h($serial++); ?></td>
						<td align="center"><?php echo h($val['Office']['office_name']); ?></td>
						<td class="text-left"><?php echo h($val['Territory']['name']); ?></td>
						<td class="text-left"><?php echo h($val['Market']['name']); ?></td>
						<td class="text-left"><?php echo h($val['Outlet']['name']); ?></td>
						<td class="text-left"><?php echo h($val['CreditNote']['credit_number']); ?></td>
						<td class="text-left"><?php echo h($val['CreditNote']['created_at']); ?></td>
						
						<td class="text-center">
							<?php if($this->App->menu_permission('credit_notes','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $val['CreditNote']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('credit_notes','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $val['CreditNote']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'View')); } ?>
							
						</td>
					</tr>
					<?php endforeach;
					} ?>
					</tbody>
				</table>
                </div>
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
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'credit_notes/get_territory_list'?>',
			type: 'post',
			data:{'office_id': 'office_id' }
	});
        
});
</script>