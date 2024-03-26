<style>
	.draft{
		padding: 0px 15px;
	}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Return Challan List (ASO to CWH)'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('return_challans','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Return Challan'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('ReturnChallan', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%">
							<?php echo $this->Form->input('inventory_status_id', array('type'=>'hidden','class' => 'form-control','required'=>false,'default'=>1)); ?>
							<?php echo $this->Form->input('transaction_type_id_1', array('type'=>'hidden','class' => 'form-control','required'=>false,'default'=>7)); ?>
							<?php echo $this->Form->input('transaction_type_id_2', array('type'=>'hidden','class' => 'form-control','required'=>false,'default'=>22)); ?>
							<?php echo $this->Form->input('challan_no', array('class' => 'form-control','required'=>false)); ?>
							</td>
							<td width="50%">
								<?php //echo $this->Form->input('transaction_type_id', array('type'=>'hidden','class' => 'form-control','required'=>false,'default'=>4)); ?>
								<?php echo $this->Form->input('status', array('class' => 'form-control','type'=>'select','required'=>false,'empty'=>'---- Select ----','options'=>array(1=>'Pending',2=>'Received'))); ?>
							</td>
						</tr>					
						<tr>
							<td><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>false)); ?></td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>false)); ?>
							</td>
						</tr>	
						<tr>
							<td><?php echo $this->Form->input('inventory_status_id', array('id'=>'inventory_status_id','class' => 'form-control inventory_status_id','empty'=>'---- Select Invetory Status ----','options'=>$inventoryStatus,'required'=>false)); ?></td>
							<td>
								<?php echo $this->Form->input('sender_store_id', array('class' => 'form-control', 'empty'=>'---- Select Store ----', 'required'=>false));?>
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
				<table id="ReturnChallan" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('challan_no','Issue No.'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('transaction_type_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('inventory_status_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sender_store_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('challan_date','Issue Date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('receiver_store_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('received_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('remarks'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($challans as $challan): ?>
					<tr>
						<td align="center"><?php echo h($challan['ReturnChallan']['id']); ?></td>
						<td align="center"><?php echo h($challan['ReturnChallan']['challan_no']); ?></td>
						<td class="text-left"><?php echo h($challan['TransactionType']['name']); ?></td>
						<td class="text-left"><?php echo h($challan['InventoryStatus']['name']); ?></td>						
						<td class="text-left"><?php echo h($challan['SenderStore']['name']); ?></td>
						<td align="center"><?php echo $this->App->dateformat($challan['ReturnChallan']['challan_date']); ?></td>
						<td class="text-left"><?php echo h($challan['ReceiverStore']['name']); ?></td>
						<td align="center"><?php echo $this->App->dateformat($challan['ReturnChallan']['received_date']); ?></td>
						<td align="center">
							<?php
									if ($challan['ReturnChallan']['status'] == 1) {
										echo '<span class="btn btn-warning btn-xs">Pending</span>';
									}elseif($challan['ReturnChallan']['status'] == 2) {
										echo '<span class="btn btn-success btn-xs">Received</span>';
									}else{
										echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
									}
								?>
					   </td>
						<td class="text-left"><?php echo h($challan['ReturnChallan']['remarks']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('return_challans','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $challan['ReturnChallan']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php
								if ($challan['ReturnChallan']['status'] == 0) {
									if($this->App->menu_permission('memos','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $challan['ReturnChallan']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } 
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