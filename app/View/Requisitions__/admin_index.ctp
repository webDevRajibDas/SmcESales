<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('DO List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('requisitions','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Create New DO'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('Requisition', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('do_no', array('class' => 'form-control','required'=>false)); ?></td>
							<td width="50%"><?php echo $this->Form->input('status', array('class' => 'form-control','type'=>'select','required'=>false,'empty'=>'---- Select ----','options'=>array(1=>'Unused',2=>'Used'))); ?></td>							
						</tr>					
						<tr>
							<td><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>false)); ?></td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>false)); ?>
							</td>
						</tr>					
						<tr>							
							<?php if(CakeSession::read('Office.parent_office_id') == 0){ ?>
							<td>
								<?php echo $this->Form->input('sender_store_id', array('class' => 'form-control','empty'=>'---- Select ----','required'=>false)); ?>
							</td>
							<?php }	?>
							</td>
							<td>
								<?php echo $this->Form->input('receiver_store_id', array('class' => 'form-control','empty'=>'---- Select ----','required'=>false)); ?>
							</td>
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
				<table id="Requisitions" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('do_no','DO No.'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('title'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sender_store_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('receiver_store_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('created_at','DO Date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('remarks'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($requisitions as $requisition): ?>
					<tr>
						<td class="text-center"><?php echo h($requisition['Requisition']['id']); ?></td>
						<td class="text-center"><?php echo h($requisition['Requisition']['do_no']); ?></td>
						<td class="text-center"><?php echo h($requisition['Requisition']['title']); ?></td>
						<td class="text-center"><?php echo h($requisition['SenderStore']['name']); ?></td>
						<td class="text-center"><?php echo h($requisition['ReceiverStore']['name']); ?></td>
						<td class="text-center"><?php echo h($requisition['Requisition']['created_at']); ?></td>
						<td class="text-center">
							<?php 
								if($requisition['Requisition']['status'] == 1)
								{ 
									echo '<span class="btn btn-warning btn-xs">Open</span>';
								}else{
									echo '<span class="btn btn-danger btn-xs">Close</span>'; 
								}
							?>
						</td>
						<td class="text-center"><?php echo h($requisition['Requisition']['remarks']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('requisitions','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $requisition['Requisition']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php 
							if($store_id == $requisition['SenderStore']['id']){							
								if($this->App->menu_permission('requisitions','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $requisition['Requisition']['id']), array('class' => 'btn btn-warning  btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Edit')); } 
							}
							?>
							<?php 
							if($requisition['Requisition']['status'] == 1 AND $store_id == $requisition['SenderStore']['id']){
								if($this->App->menu_permission('requisitions','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $requisition['Requisition']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $requisition['Requisition']['id'])); } 
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