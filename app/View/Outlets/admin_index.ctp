<?php
	// echo "<pre>";
	// print_r($outlets);die();
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Outlet List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('outlets','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Outlet'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('Outlet', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td>
								<?php echo $this->Form->input('name', array('class' => 'form-control','required'=>false)); ?>
							</td>
							<td>							
								<?php echo $this->Form->input('office_id', array('id'=>'office_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>false)); ?>
							</td>
						</tr>					
						<tr>
							<td>							
								<?php echo $this->Form->input('category_id', array('label' => 'Outlet Type','class' => 'form-control','options'=>$categories,'required'=>false,'empty'=>'---- Select ----')); ?>
							</td>
							<td>							
								<?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>false)); ?>
							</td>							
						</tr>					
						<tr>
							<td>
								<?php echo $this->Form->input('mobile', array('class' => 'form-control','required'=>false)); ?>
							</td>
                            <td>							
								<?php echo $this->Form->input('thana_id', array('id'=>'thana_id', 'class' => 'form-control thana_id','empty'=>'---- Select ----','required'=>false)); ?>
							</td>
						</tr>
                        <tr>
							<td>
								<?php echo $this->Form->input('bonus_type', array('label' => 'Bonus Type','class' => 'form-control','options'=>$bonus_types,'required'=>false,'empty'=>'---- Select ----')); ?>
							</td>
							<td>							
								<?php echo $this->Form->input('market_id', array('id'=>'market_id', 'class' => 'form-control market_id','empty'=>'---- Select ----','required'=>false)); ?>
							</td>							
						</tr>
						<tr>
							<td>
								<?php $status=array(1=>'Active',2=>'In-Active');echo $this->Form->input('is_active', array('label' => 'Status','class' => 'form-control','options'=>$status,'required'=>false,'empty'=>'---- Select ----')); ?>
							</td>
						</tr>					
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<a class="btn btn-success" id="download_xl">Download XL</a>
							</td>						
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>
                <table id="OutletCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<!-- <th class="text-center"><?php echo $this->Paginator->sort('address'); ?></th> -->
							<th class="text-center"><?php echo $this->Paginator->sort('thana_name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('mobile'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('market_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('category_id','Outlet Type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_pharma_type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_ngo'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('institute_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('bonus_type_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_active','Stauts'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($outlets as $outlet): ?>
					<tr>
						<td class="text-center"><?php echo h($outlet['Outlet']['id']); ?></td>
						<td><?php echo h($outlet['Outlet']['name']); ?></td>
						<!-- <td><?php echo h($outlet['Outlet']['address']); ?></td> -->
						<td><?php echo h($outlet['Thana']['name']); ?></td>
						<td><?php echo h($outlet['Outlet']['mobile']); ?></td>
						<td><?php echo h($outlet['Market']['name']); ?></td>
						<td><?php echo h($outlet['Territory']['name']); ?></td>
						<td><?php echo h($outlet['Office']['office_name']); ?></td>
						<td><?php echo h($outlet['OutletCategory']['category_name']); ?></td>
						<td>
							<?php
								if($outlet['Outlet']['is_pharma_type']==1){
									echo h('Yes');
								}elseif($outlet['Outlet']['is_pharma_type']==0){
									echo h('No');
								}
							?>
						</td>
						<td>
							<?php
								if($outlet['Outlet']['is_ngo']==1){
									echo h('Yes');
								}elseif($outlet['Outlet']['is_ngo']==0){
									echo h('No');
								}
							?>
						</td>
						<td><?php echo h($outlet['Institute']['name']); ?></td>
						<td>
							<?php
								if($outlet['Outlet']['bonus_type_id'] == 1){
									echo h('Small Bonus');
								}elseif ($outlet['Outlet']['bonus_type_id'] == 2) {
									echo h('Big Bonus');
								}else{
									echo h('Not Applicable');
								} 
							?>
						</td>
						<td>
							<?php
								if($outlet['Outlet']['is_active'] == 1){
									echo ('<i class="label label-success">Active</i>');
								}else{
									echo ('<i class="label label-danger">In-Active</i>');
								} 
							?>
						</td>
						<td class="text-center">
							<?php if($this->App->menu_permission('outlets','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $outlet['Outlet']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('outlets','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $outlet['Outlet']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php //if($this->App->menu_permission('outlets','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $outlet['Outlet']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $outlet['Outlet']['id'])); } ?>
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

	$('#download_xl').click(function(e) {
		e.preventDefault();
		var formData = $(this).closest('form').serialize();
		// var arrStr = encodeURIComponent(formData);
		// console.log(formData);return;
		// console.log(arrStr);
		window.open("<?= BASE_URL; ?>Outlets/download_xl?" + formData);
	});
	
	/*$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'markets/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':''}
	});*/	
	
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