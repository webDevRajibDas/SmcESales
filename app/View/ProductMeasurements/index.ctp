<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Product Measurements'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('productMeasurements','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Product Measurement'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="ProductMeasurements" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('measurement_unit_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_base'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('qty_in_base'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
							<th class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($productMeasurements as $productMeasurement): ?>
					<tr>
						<td class="text-center"><?php echo h($productMeasurement['ProductMeasurement']['id']); ?></td>
						<td class="text-center">
			<?php echo $this->Html->link($productMeasurement['Product']['name'], array('controller' => 'products', 'action' => 'view', $productMeasurement['Product']['id'])); ?>
		</td>
						<td class="text-center">
			<?php echo $this->Html->link($productMeasurement['MeasurementUnit']['name'], array('controller' => 'measurement_units', 'action' => 'view', $productMeasurement['MeasurementUnit']['id'])); ?>
		</td>
						<td class="text-center"><?php echo h($productMeasurement['ProductMeasurement']['is_base']); ?></td>
						<td class="text-center"><?php echo h($productMeasurement['ProductMeasurement']['qty_in_base']); ?></td>
						<td class="text-center"><?php echo h($productMeasurement['ProductMeasurement']['is_active']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('productMeasurements','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $productMeasurement['ProductMeasurement']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('productMeasurements','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $productMeasurement['ProductMeasurement']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('productMeasurements','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $productMeasurement['ProductMeasurement']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $productMeasurement['ProductMeasurement']['id'])); } ?>
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