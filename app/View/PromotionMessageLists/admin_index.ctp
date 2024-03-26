<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Promotional Message List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('promotionMessageLists','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Message'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="MessageCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('message'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('message_category_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('message_type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('created_at'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($messageLists as $message): ?>
					<tr>
						<td class="text-center"><?php echo h($message['MessageList']['id']); ?></td>
						<td><?php echo h($message['MessageList']['message']); ?></td>
						<td class="text-center"><?php echo h($message['MessageCategory']['title']); ?></td>
						<td class="text-center">
						<?php 
							if($message['MessageList']['message_type'] == 0)
							{
								echo 'Inbox and Ticker Both';
							}elseif($message['MessageList']['message_type'] == 1)
							{
								echo 'Ticker';
							}elseif($message['MessageList']['message_type'] == 2)
							{
								echo 'Inbox';
							}							
						?>
						</td>
						<td class="text-center"><?php echo $this->App->dateformat($message['MessageList']['created_at']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('messageLists','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $message['MessageList']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('messageLists','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $message['MessageList']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $message['MessageList']['id'])); } ?>
							<?php
								$active_msg= $message['MessageList']['status']==1?'Inactive':'Active';
								if($this->App->menu_permission('messageLists','admin_change_status')){ 
									echo $this->Form->postLink(__($message['MessageList']['status']==1?'<i class="glyphicon glyphicon-ok-circle"></i>':'<i class="glyphicon glyphicon-ban-circle"></i>'), array('action' => 'change_status', $message['MessageList']['id']), array('class' => 'btn btn-info btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Change Status to '.$active_msg), __('Are you sure you want to Change Status # %s?',$message['MessageList']['id'])); 
								} 
							?>
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