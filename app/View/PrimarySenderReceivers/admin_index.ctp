<?php 
 	$store_types=array('1'=>'Sender','2'=>'Receiver')
?>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sender/Receiver'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> New Sender/Receiver'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>	
			<div class="box-body">
				<table id="clients" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('store_type','Type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('address'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('mobile'); ?></th>
							<th width="120"  class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($sender_receivers as $sender_receiver): ?>
							<tr>
								<td class="text-center"><?php echo h($sender_receiver['PrimarySenderReceiver']['id']); ?></td>
								<td class="text-left"><?php echo h($store_types[$sender_receiver['PrimarySenderReceiver']['store_type']]); ?></td>
								<td class="text-left"><?php echo h($sender_receiver['PrimarySenderReceiver']['name']); ?></td>
								<td class="text-center"><?php echo h($sender_receiver['PrimarySenderReceiver']['address']); ?></td>
								<td class="text-center"><?php echo h($sender_receiver['PrimarySenderReceiver']['mobile']); ?></td>

								<td class="text-center">
									<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $sender_receiver['PrimarySenderReceiver']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
									<?php  echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $sender_receiver['PrimarySenderReceiver']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $sender_receiver['PrimarySenderReceiver']['id']));  ?>
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