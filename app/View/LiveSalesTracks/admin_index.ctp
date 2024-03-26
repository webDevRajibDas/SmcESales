<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sales Tracking List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('LiveSalesTracks','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Sale Tracking'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
                
			</div>	
            
            
			<div class="box-body">
            
				
                
                <table id="LiveSalesTrack" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('start_time'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('end_time'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('interval'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
                    
					<tbody>
                    
					<?php foreach ($LiveSalesTracks as $visitPlanList): ?>
                    
					<tr>
						<td class="text-center"><?php echo h($visitPlanList['LiveSalesTrack']['id']); ?></td>
						<td class="text-center"><?php echo h(date('h:i a', strtotime($visitPlanList['LiveSalesTrack']['start_time']))); ?></td>
                        <td class="text-center"><?php echo h(date('h:i a', strtotime($visitPlanList['LiveSalesTrack']['end_time']))); ?></td>
						<td class="text-center"><?php echo h($visitPlanList['LiveSalesTrack']['interval']); ?> Min</td>
						
						<td class="text-center">
                        	<?php if($this->App->menu_permission('LiveSalesTracks','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $visitPlanList['LiveSalesTrack']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
                            
							<?php if($this->App->menu_permission('LiveSalesTracks','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $visitPlanList['LiveSalesTrack']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $visitPlanList['LiveSalesTrack']['id'])); } ?>
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
$(document).ready(function () {
	
	$('#user_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'LiveSalesTracks/get_spo_territory_list'?>',
		type: 'post',
		data:{'user_id': 'user_id'},
		/*before: function () {
		// do something
		alert(1111);
		},
		success:function(msg){
			alert(111);
		}*/
	});
	
	$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'LiveSalesTracks/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id'}
	});
	
});
</script>