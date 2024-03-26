<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('So Stock Checks'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('soStockChecks','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New So Stock Check'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('SoStockCheck', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<!-- <td width="50%"><?php //echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----','options'=>$offices)); ?></td>
							<td> -->
							<td>	
								<?php 
								if($StoreId==13){
									echo $this->Form->input('store_id', array('class' => 'form-control', 'empty'=>'---- Select Store ----', 'required'=>false)); 
								}else{
									echo $this->Form->input('store_id', array('class' => 'form-control', 'selected' => $StoreId, 'empty'=>'---- Select Store ----', 'required'=>false)); 
								}
								?>
                            </td>
						</tr>
						<tr>
							
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['SoStockCheck']['date_from'])=='' ? $current_date : $this->request->data['SoStockCheck']['date_from']),'required'=>false)); ?></td>

							<td><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['SoStockCheck']['date_to'])=='' ? $current_date : $this->request->data['SoStockCheck']['date_to']),'required'=>false)); ?></td>

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


                <table id="SoStockChecks" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('so_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('store_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('reported_time'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('created_at'); ?></th>
							<!-- <th class="text-center"><?php //echo $this->Paginator->sort('created_by'); ?></th> -->
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($soStockChecks as $soStockCheck): ?>
					<tr>
						<td class="text-center"><?php echo h($soStockCheck['SoStockCheck']['id']); ?></td>
						<td class="text-center"><?php echo h($soStockCheck['SalesPerson']['name']); ?></td>
						<td class="text-center">
			<?php echo $this->Html->link($soStockCheck['Store']['name'], array('controller' => 'stores', 'action' => 'view', $soStockCheck['Store']['id'])); ?>
		</td>
						<td class="text-center"><?php echo h($soStockCheck['SoStockCheck']['reported_time']); ?></td>
						<td class="text-center"><?php echo h($soStockCheck['SoStockCheck']['created_at']); ?></td>
						<!-- <td class="text-center"><?php //echo h($soStockCheck['SoStockCheck']['created_by']); ?></td> -->
						<td class="text-center">
							<?php if($this->App->menu_permission('soStockChecks','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $soStockCheck['SoStockCheck']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('soStockChecks','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $soStockCheck['SoStockCheck']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('soStockChecks','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $soStockCheck['SoStockCheck']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $soStockCheck['SoStockCheck']['id'])); } ?>
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