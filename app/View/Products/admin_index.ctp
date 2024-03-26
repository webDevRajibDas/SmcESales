<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Product List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if ($this->App->menu_permission('products', 'admin_add')) {
						echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Product'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
					} ?>
					<?php if ($this->App->menu_permission('products', 'admin_product_rearrange')) {
						echo $this->Html->link(__('<i class="glyphicon glyphicon-refresh"></i> Product Rearrange'), array('action' => 'product_rearrange'), array('class' => 'btn btn-success', 'escape' => false));
					} ?>
				</div>
			</div>
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('Product', array('role' => 'form', 'action' => 'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('name', array('label' => 'Product Name :', 'class' => 'form-control', 'required' => false)); ?></td>
							<td width="50%"><?php echo $this->Form->input('product_category_id', array('id' => 'category_id', 'class' => 'form-control', 'required' => false, 'empty' => '---- Select ----')); ?></td>
						</tr>
						<tr>
							<td><?php echo $this->Form->input('brand_id', array('class' => 'form-control', 'required' => false, 'empty' => '---- Select ----')); ?></td>
							<td><?php echo $this->Form->input('variant_id', array('class' => 'form-control', 'required' => false, 'empty' => '---- Select ----')); ?></td>
						</tr>
						<tr>
							<td><?php echo $this->Form->input('base_measurement_unit_id', array('class' => 'form-control', 'required' => false, 'empty' => '---- Select ----', 'options' => $base_measurement_unit)); ?></td>
							<td><?php echo $this->Form->input('product_type_id', array('class' => 'form-control', 'required' => false, 'empty' => '---- Select ----')); ?></td>
						</tr>
						<tr>
							<td>
								<?php echo $this->Form->input('group_id', array('class' => 'form-control', 'required' => false, 'empty' => '---- Select ----')); ?>
							</td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
				<div class="table-responsive">
					<table id="ProductCategories" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('sap_product_code'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('parent_id'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('product_category_id'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('product_type_id'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('base_measurement_unit_id'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('brand_id'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('variant_id'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('group_id', 'Group'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('source'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
								<th class="text-center"><?php echo $this->Paginator->sort('order'); ?></th>
								<th width="150" class="text-center"><?php echo __('Actions'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($products as $product) : ?>
								<tr>
									<td class="text-center"><?php echo h($product['Product']['id']); ?></td>
									<td class="text-center"><?php echo h($product['Product']['name']); ?></td>
									<td class="text-center"><?php echo h($product['Product']['sap_product_code']); ?></td>
									<td class="text-center"><?php echo h($product['Parent']['name']); ?></td>
									<td class="text-center"><?php echo h($product['ProductCategory']['name']); ?></td>
									<td class="text-center"><?php echo h($product['ProductType']['name']); ?></td>
									<td class="text-center"><?php echo h($product['BaseMeasurementUnit']['name']); ?></td>
									<td class="text-center"><?php echo h($product['Brand']['name']); ?></td>
									<td class="text-center"><?php echo h($product['Variant']['name']); ?></td>
									<td class="text-center"><?php echo h($product['Group']['name']); ?></td>
									<td class="text-center"><?php echo h($product['Product']['source']); ?></td>
									<td class="text-center">
										<?php if ($product['Product']['is_active'] == 1) {
											echo 'Yes';
										} else {
											echo 'No';
										}; ?>
									</td>
									<td class="text-center"><?php echo h($product['Product']['order']); ?></td>
									<td class="text-center">
										<?php if ($this->App->menu_permission('products', 'admin_view')) {
											echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $product['Product']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
										} ?>
										<?php if ($this->App->menu_permission('products', 'admin_edit')) {
											echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $product['Product']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
										} ?>
										<?php if ($this->App->menu_permission('products', 'admin_delete')) {
											echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $product['Product']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $product['Product']['id']));
										} ?>

										<?php if ($this->App->menu_permission('open_combinations', 'admin_index')) {
											echo $this->Html->link(__('<i class="glyphicon glyphicon-open"></i>'), array('controller' => 'open_combinations', 'action' => 'index', $product['Product']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'price open'));
										} ?>
										<?php if ($this->App->menu_permission('bonus_combinations', 'admin_index')) {
											echo $this->Html->link(__('<i class="glyphicon glyphicon-open"></i>'), array('controller' => 'bonus_combinations', 'action' => 'index', $product['Product']['id']), array('class' => 'btn btn-success btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'bonus open'));
										} ?>

									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
							<?php echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>
								<?php
								echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
								echo $this->Paginator->numbers(array("separator" => "", "currentTag" => "a", "currentClass" => "active", "tag" => "li", "first" => 1));
								echo $this->Paginator->next(__("next"), array("tag" => "li", "currentClass" => "disabled"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>