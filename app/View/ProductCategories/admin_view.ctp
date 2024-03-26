<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Product Category'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Category List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="ProductCategories" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($productCategory['ProductCategory']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($productCategory['ProductCategory']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Parent Product Category'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($productCategory['ParentProductCategory']['name'], array('controller' => 'product_categories', 'action' => 'view', $productCategory['ParentProductCategory']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Product Categories'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Child Product Category'), array('controller' => 'product_categories', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($productCategory['ChildProductCategory'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Name'); ?></th>
		<th class="text-center"><?php echo __('Parent Id'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($productCategory['ChildProductCategory'] as $childProductCategory): ?>
		<tr>
			<td class="text-center"><?php echo $childProductCategory['id']; ?></td>
			<td class="text-center"><?php echo $childProductCategory['name']; ?></td>
			<td class="text-center"><?php echo $childProductCategory['parent_id']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'product_categories', 'action' => 'view', $childProductCategory['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'product_categories', 'action' => 'edit', $childProductCategory['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'product_categories', 'action' => 'delete', $childProductCategory['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $childProductCategory['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				
			</div><!-- /.related -->

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Products'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Product'), array('controller' => 'products', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($productCategory['Product'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Product Code'); ?></th>
		<th class="text-center"><?php echo __('Name'); ?></th>
		<th class="text-center"><?php echo __('Product Category Id'); ?></th>
		<th class="text-center"><?php echo __('Sub Category Id'); ?></th>
		<th class="text-center"><?php echo __('Brand Id'); ?></th>
		<th class="text-center"><?php echo __('Variant Id'); ?></th>
		<th class="text-center"><?php echo __('Base Measurement Unit Id'); ?></th>
		<th class="text-center"><?php echo __('Sales Measurement Unit'); ?></th>
		<th class="text-center"><?php echo __('Challan Measurement Unit'); ?></th>
		<th class="text-center"><?php echo __('Is Active'); ?></th>
		<th class="text-center"><?php echo __('Is Saleable'); ?></th>
		<th class="text-center"><?php echo __('Maintain Batch'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($productCategory['Product'] as $product): ?>
		<tr>
			<td class="text-center"><?php echo $product['id']; ?></td>
			<td class="text-center"><?php echo $product['product_code']; ?></td>
			<td class="text-center"><?php echo $product['name']; ?></td>
			<td class="text-center"><?php echo $product['product_category_id']; ?></td>
			<td class="text-center"><?php echo $product['sub_category_id']; ?></td>
			<td class="text-center"><?php echo $product['brand_id']; ?></td>
			<td class="text-center"><?php echo $product['variant_id']; ?></td>
			<td class="text-center"><?php echo $product['base_measurement_unit_id']; ?></td>
			<td class="text-center"><?php echo $product['sales_measurement_unit']; ?></td>
			<td class="text-center"><?php echo $product['challan_measurement_unit']; ?></td>
			<td class="text-center"><?php echo $product['is_active']; ?></td>
			<td class="text-center"><?php echo $product['is_saleable']; ?></td>
			<td class="text-center"><?php echo $product['maintain_batch']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'products', 'action' => 'view', $product['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'products', 'action' => 'edit', $product['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'products', 'action' => 'delete', $product['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $product['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				
			</div><!-- /.related -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

