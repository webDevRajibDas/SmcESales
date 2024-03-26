
<?php
App::import('Controller', 'UserTerritoryListsController');
$ListsController = new UserTerritoryListsController;

?>

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('User to Territory List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('UserTerritoryLists', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Assign New'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
            
            	<div class="search-box">
					<?php echo $this->Form->create('UserTerritoryList', array('role' => 'form', 'action'=>'filter')); ?>
					<table class="search">
                    	
						<tr>
                        	<td width="33%"><?php echo $this->Form->input('user_id', array('id' => 'user_id','class' => 'form-control user_id','required'=>false,'empty'=>'---- Select SPO User ----', 'options'=>$users)); ?></td>
							<td width="33%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----','options'=>$offices)); ?></td>
							<td width="33%"><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----')); ?></td>
						</tr>
						
						
						<tr align="center">
							<td colspan="3">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
            
                <table id="UserTerritoryLists" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center">User Name</th>
							<th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($UserTerritoryLists as $result): 
					//pr($result);
					?>
					<tr>
						<td style="text-transform:capitalize;">
                        <?=$result['SalesPeople']['name'];?>
						<?php //echo h($result['UserTerritoryList']['user_id']); ?>
                        </td>
						
						<td class="text-center"><?php echo $ListsController -> getOfficeName($result['SalesPeople']['office_id']);?></td>
						
                        
                        <td class="text-center"><?php echo  $ListsController -> getUserTerritory($result['UserTerritoryList']['user_id'], 1); ?></td>
						
						
                        <td class="text-center">
							<?php if($this->App->menu_permission('UserTerritoryLists','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $result['UserTerritoryList']['user_id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							
							<?php if($this->App->menu_permission('UserTerritoryLists','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $result['UserTerritoryList']['user_id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $result['UserTerritoryList']['user_id'])); } ?>
                            
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

});
</script>