<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Product Convert Histories'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('productConvertHistories','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Product Convert History'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('ProductConvertHistory', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('from_product_id', array('class' => 'form-control','required'=>false,'type'=>'text')); ?></td>
							<td width="50%"><?php echo $this->Form->input('batch_number', array('class' => 'form-control','type'=>'select','required'=>false,'type'=>'text')); ?></td>
						</tr>
						<tr>
							<td><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>false)); ?></td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>false)); ?>
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
                <table id="ProductConvertHistories" class="table table-bordered table-striped">
					<thead>
						<tr>

							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('store_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('from_product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('batch_number'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('expire_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('from_status_id'); ?></th>

							<th class="text-center"><?php echo $this->Paginator->sort('to_product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('batch_number'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('expire_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('to_status_id'); ?></th>

							<th class="text-center"><?php echo $this->Paginator->sort('quantity'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('created_at'); ?></th>

						</tr>
					</thead>
					<tbody>
					<?php foreach ($productConvertHistories as $productConvertHistory): ?>
					<tr>
						<td class="text-center"><?php echo h($productConvertHistory['ProductConvertHistory']['id']); ?></td>
						<td class="text-center"><?php echo h($productConvertHistory['Store']['Name']); ?></td>
						<td class="text-center"><?php echo h($productConvertHistory['FromProduct']['name']); ?></td>
						<td class="text-center"><?php echo h($productConvertHistory['FromCurrentInventory']['batch_number']); ?></td>
						<td class="text-center"><?php echo h($productConvertHistory['FromCurrentInventory']['expire_date']); ?></td>
						<td class="text-center"><?php echo h($productConvertHistory['FromProductType']['name']); ?></td>

						<td class="text-center"><?php echo h($productConvertHistory['ToProduct']['name']); ?></td>
						<td class="text-center"><?php echo h($productConvertHistory['ToCurrentInventory']['batch_number']); ?></td>
						<td class="text-center"><?php echo h($productConvertHistory['ToCurrentInventory']['expire_date']); ?></td>
						<td class="text-center"><?php echo h($productConvertHistory['ToProductType']['name']); ?></td>


						<td class="text-center"><?php echo h($productConvertHistory['ProductConvertHistory']['quantity']); ?></td>
						<td class="text-center"><?php echo h($productConvertHistory['ProductConvertHistory']['created_at']); ?></td>

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