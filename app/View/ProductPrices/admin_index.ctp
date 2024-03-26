<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Price Setup'); ?></h3>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('Product', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('product_category_id', array('id' => 'category_id', 'class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?></td>							
							<td>
								<?php echo $this->Form->input('product_type_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----','default'=>1)); ?>
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
				<table id="ProductCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('base_measurement_unit_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_category_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('brand_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('variant_id'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($products as $product): ?>
					<tr>
						<td class="text-center"><?php echo h($product['Product']['id']); ?></td>
						<td class="text-left"><?php echo h($product['Product']['name']); ?></td>
						<td class="text-left"><?php echo h($product['BaseMeasurementUnit']['name']); ?></td>
						<td class="text-left"><?php echo h($product['ProductCategory']['name']); ?></td>
						<td class="text-left"><?php echo h($product['Brand']['name']); ?></td>
						<td class="text-left"><?php echo h($product['Variant']['name']); ?></td>
						<td class="text-center">
							<?php echo $this->Html->link('Set Price', array('action' => 'admin_price_list', $product['Product']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Price'));  ?>
                            <?php echo $this->Html->link('Set Combined Price', array('controller' => 'product_combinations','action'=>'index', $product['Product']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Combined Price'));  ?>
							<?php echo $this->Html->link('NGO Price List', array('controller' => 'product_prices','action'=>'ngo_price_list', $product['Product']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'NGO Price List'));  ?>
							<?php echo $this->Html->link('Commission Setup', array('controller' => 'product_prices','action'=>'distributor_commission_list', $product['Product']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Commission'));  ?>
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