
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('All Users'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('users','addUser')){ ?> <a href="<?php echo Router::url('/admin/addUser'); ?>"><button class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> New User</button></a> <?php } ?>			 		
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
								<?php echo $this->Form->input('name', array('class' => 'form-control', 'required'=>false)); ?>
							</td>
							<td>
								
							</td>	
						</tr>
						<tr>
							
							<td>
								<?php echo $this->Form->input('username', array('class' => 'form-control','required'=>false)); ?>
							</td>
							<td>
								<?php echo $this->Form->input('user_type_id', array('label'=>'System','class' => 'form-control','required'=>false,'options'=>$user_types)); ?>
							</td>	
						</tr>
						<tr>
							<td>
								<?php echo $this->Form->input('user_group_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?>
							</td>
							<td>
								<?php echo $this->Form->input('mac_id', array('class' => 'form-control','required'=>false,'label'=>'IMEI','type'=>'text')); ?>
							</td>
													
						</tr>					
						<tr>
							
							<td>							
								<?php echo $this->Form->input('office_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?>
							</td>
							<td>							
								<?php echo $this->Form->input('version', array('type'=>'select','class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?>
							</td>
													
						</tr>

						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<?php if($users)echo $this->Form->button('<i class="fa fa-info"></i> Excel', array('type'=>'button','name'=>'downloadexcel','id'=>'downloadexcel','class' => 'btn btn-large btn-info','escape' => false)); ?>
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
							<th class="text-center"><?php echo $this->Paginator->sort('sap_customer_code','Customer Code'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sales_person','Full Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
							<th class="text-center"><?php echo h('Contact No'); ?></th>
							<th class="text-center"><?php echo h('IMEI'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('version'); ?></th>
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
							<td class="text-center"><?php echo h($row['SalesPerson']['sap_customer_code']); ?></td>
							<td><?php echo h($row['SalesPerson']['name']); ?></td>
							<td class="text-center"><?php echo (isset($row['SalesPerson']['Office']['office_name'])!='' ? $row['SalesPerson']['Office']['office_name'] : ''); ?></td>
							<td class="text-center"><?php echo (isset($row['SalesPerson']['Territory']['name'])!='' ? $row['SalesPerson']['Territory']['name'] : ''); ?></td>
							<td class="text-center"><?php echo h($row['SalesPerson']['contact']); ?></td>
							<td class="text-center">
							
								<?php echo h($row['User']['mac_id']); ?>
								<input type="hidden" id="mac_<?php echo h($row['User']['id']); ?>" value="<?php echo h($row['User']['mac_id']); ?>">
							
							</td>
							<td class="text-center"><?php echo h($row['User']['version']); ?></td>
							<td class="text-center"><?php echo h($row['UserGroup']['name']); ?></td>
							<td class="text-center">
							<?php if ($row['User']['active']==1) {
									echo "Active";
								} else {
									echo "Inactive";
								} ?>
							</td>
							<td class="text-center">
								<?php if($this->App->menu_permission('users','territory_tag')){ ?> <a class='btn btn-success btn-xs' data-toggle="tooltip" title="Territory Assigned" href="<?php echo $this->Html->url('/admin/territoryTag/'.$row['User']['sales_person_id']); ?>"><i class='glyphicon glyphicon-tag'></i></a> <?php } ?>
								<?php if($this->App->menu_permission('users','territory_deassigned')){ ?> <a class='btn btn-primary btn-xs' data-toggle="tooltip" title="Territory De-Assigned" href="<?php echo $this->Html->url('/admin/territory_deassigned/'.$row['User']['sales_person_id']); ?>"><i class='glyphicon glyphicon-flash'></i></a> <?php } ?>
								<?php if($this->App->menu_permission('users','editUser')){ ?> <a class='btn btn-warning btn-xs' data-toggle="tooltip" title="Edit" href="<?php echo $this->Html->url('/admin/editUser/'.$row['User']['id']) ?>"><i class='glyphicon glyphicon-pencil'></i></a> <?php } ?>
								<?php //if($this->App->menu_permission('users','deleteUser')){ ?> <?php //echo $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', array('action' => '/deleteUser', $row['User']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Delete','escape' => false, 'confirm' => __('Are you sure you want to delete this user? Delete it your own risk'))); ?> <?php //} ?>

								<?php if($row['UserGroup']['id'] == "4"){ ?>
									
									<?php 
										if( empty($row['SalesPerson']['ae_id']) || $row['SalesPerson']['ae_id'] == 0 ){
											if($this->App->menu_permission('users','ae_assing_to_so')){
									 ?> 
												<a class='btn btn-success btn-xs' data-toggle="tooltip" title="Area Executive Assigned" href="<?php echo $this->Html->url('/admin/ae_assing_to_so/'.$row['User']['sales_person_id']. '/'.$row['SalesPerson']['office_id']); ?>"><i class='glyphicon glyphicon-floppy-saved'></i></a> 
									<?php 
											} 
										}else{
									?>

										<?php if($this->App->menu_permission('users','ae_deassing_to_so')){ ?> 
											<a class='btn btn-primary btn-xs' data-toggle="tooltip" title="Area Executive De-Assigned" href="<?php echo $this->Html->url('/admin/ae_deassing_to_so/'.$row['User']['sales_person_id']. '/'.$row['SalesPerson']['office_id']); ?>"><i class='glyphicon glyphicon-floppy-remove'></i></a> 
									<?php 
											} 
										}
									?>

								<?php } ?>
								

								<?php if($this->App->menu_permission('users','mac_free') && $row['User']['mac_id']){ ?> <a class='btn btn-primary btn-xs' title="Mac Free" href="#" onclick="mac_free_function(<?=$row['User']['id'];?>)" data-toggle='modal' data-target='#mac_free'><i class='glyphicon glyphicon-book'></i></a> <?php } ?>
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


<div id="mac_free" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			   <button type="button" class="close" data-dismiss="modal">&times;</button>
			   <h4 class="modal-title">Change Information</h4>
		   </div>
		   <div class="modal-body">
		   		<?php echo $this->Form->create('User', array('role' => 'form', 'action'=>'mac_free')); ?>

		   		<?php $name = $this->UserAuth->getUser(); ?>

		   		<div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control', 'readonly'=>true,  'style'=>'width: 60%;')); ?>
				</div>

		   		<div class="form-group">
					<?php echo $this->Form->input('change_by', array('class' => 'form-control', 'value'=>$name['User']['name'], 'readonly'=>true,  'style'=>'width: 60%;')); ?>
				</div>
				
				<div class="form-group">
					<?php echo $this->Form->input('current_mac', array('class' => 'form-control', 'type'=>'hidden', 'readonly'=>true,  'style'=>'width: 60%;')); ?>
				</div>

		    	<div class="form-group">
					<?php echo $this->Form->input('mac_node', array('class' => 'form-control', 'label'=>'Mac Free Reason', 'required'=>true, 'type' => 'textarea', 'style'=>'width: 60%;')); ?>
				</div>

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary', 'id'=>"valueAdd", 'type'=>"submit")); ?>
				
			</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
		</div>
	</div>
</div>        
</div>



<script>
$("#UserId").val('');
$("#UserCurrentMac").val('');
function mac_free_function(id) {
  $("#UserId").val(id);
  var currentmac = $("#mac_"+id).val();
  $("#UserCurrentMac").val(currentmac);
}


$(document).ready(function (){
	$(".page_limit").change(function() {
		this.form.submit();
	});
	
	$(document).on("click",".pagination a",function() {		
		var url=$(this).attr('href');		
		$('#UserFilterForm').attr('action',url).submit();
		return false;
	});
	$("#user_type_id").change(function(){
		getVersion();
	});
	if($("#user_type_id").val())
	{
		getVersion();
	}
	function getVersion()
	{
		var target_apk=parseInt($("#user_type_id").val())+1;
		$.ajax({
			url:"<?php echo BASE_URL ?>get_version",
			data: {"target_apk":target_apk},
			type:'POST',
			dataType:'json',
			success:function(response)
			{
				option='<option value="">--- Select ---</option>';
				$.each(response, function(i, item) {
					option+='<option value="'+i+'">'+item+'</option>';
				});
				<?php if(@$this->request->data['version']!= '') {?>
					if($("#version").html(option))
					{
						$("#version").val('<?=$this->request->data['version']?>');
					}
				<?php }else{ ?>
					$("#version").html(option);
				<?php } ?>
			}

		});
	}
	$('#downloadexcel').click(function(){
            var formData = $(this).closest('form').serialize();
            // var arrStr = encodeURIComponent(formData);
            /*console.log(formData);
            console.log(arrStr);*/
            window.open("<?=BASE_URL;?>users/download_xl?"+formData);
    });
});
</script>