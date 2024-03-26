<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('All Users'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('sr_users','admin_add')){ ?> <a href="<?php echo Router::url('/admin/sr_users/add'); ?>"><button class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> New SR User</button></a> <?php } ?>			 		
				</div>
			</div>	
			<div class="box-body">               
			
				<?php /*?><form action="/smc_sales/admin/allUsers" role="form" id="UserFilterForm" method="post" accept-charset="utf-8"><?php */?>
                
				<?php //echo $this->Form->create('allUsers', array('role' => 'form', 'id' => 'UserFilterForm')); ?>
                
                <form id="UserFilterForm" action="" role="form" method="post" accept-charset="utf-8">
                
				<div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>
				<div class="search-box">
					<table class="search">
						<tr>
							<td>
								<?php echo $this->Form->input('username', array('class' => 'form-control','required'=>false)); ?>
							</td>						
						</tr>					
						<tr>
							<td>
								<?php echo $this->Form->input('user_group_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?>
							</td>
							<td>							
								<?php echo $this->Form->input('office_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?>
							</td>							
						</tr>					
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>						
						</tr>
					</table>						
				</div>	
				<?php echo $this->Form->end(); ?>	
                
                <div class="table-responsive">	
				<table class="table table-bordered table-striped">
					<thead>
						<tr>												
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('username'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sales_person_id','Sales Person ID'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sales_person','Full Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('group'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
							<th class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($users as $row): ?>
						<tr>
							<td class="text-center"><?php echo h($row['User']['id']); ?></td>
							<td><?php echo h($row['User']['username']); ?></td>
							<td class="text-center"><?php echo h($row['User']['sales_person_id']); ?></td>
							<td><?php echo h($row['SalesPerson']['name']); ?></td>
							<td class="text-center"><?php echo (isset($row['SalesPerson']['Office']['office_name'])!='' ? $row['SalesPerson']['Office']['office_name'] : ''); ?></td>
							<td class="text-center"><?php echo (isset($row['SalesPerson']['Territory']['name'])!='' ? $row['SalesPerson']['Territory']['name'] : ''); ?></td>
							<td class="text-center"><?php echo h($row['UserGroup']['name']); ?></td>
							<td class="text-center">
							<?php if ($row['User']['active']==1) {
									echo "Active";
								} else {
									echo "Inactive";
								} ?>
							</td>
							<td class="text-center">
								
								
								<?php if($this->App->menu_permission('sr_users','admin_edit')){ 
									echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $row['User']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); 
							} ?>
								<?php if($this->App->menu_permission('sr_users','admin_delete')){ ?> <?php echo $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', array('action' => '/deleteUser', $row['User']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Delete','escape' => false, 'confirm' => __('Are you sure you want to delete this user? Delete it your own risk'))); ?> <?php } ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
                </div>
				
                <div class='row'>
					<div class='col-xs-6'>
						<?php //echo $this->Form->input('page_limit', array('label'=>false,'class' => 'form-control page_limit','required'=>false,'empty'=>'---- Select ----','options'=> array(5=>5,10=>10,20=>20,50=>50))); ?>
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
$(document).ready(function (){
	$(".page_limit").change(function() {
		this.form.submit();
	});
	
	$(document).on("click",".pagination a",function() {		
		var url=$(this).attr('href');		
		$('#UserFilterForm').attr('action',url).submit();
		return false;
	});
	
});
</script>