<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Measurement Unit'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Measurement Unit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="MeasurementUnits" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($measurementUnit['MeasurementUnit']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($measurementUnit['MeasurementUnit']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Active'); ?></strong></td>
		<td>
			<?php echo h($measurementUnit['MeasurementUnit']['is_active']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Challan Details'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Challan Detail'), array('controller' => 'challan_details', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($measurementUnit['ChallanDetail'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Challan Id'); ?></th>
		<th class="text-center"><?php echo __('Product Id'); ?></th>
		<th class="text-center"><?php echo __('Measurement Unit Id'); ?></th>
		<th class="text-center"><?php echo __('Challan Qty'); ?></th>
		<th class="text-center"><?php echo __('Received Qty'); ?></th>
		<th class="text-center"><?php echo __('Batch Id'); ?></th>
		<th class="text-center"><?php echo __('Remarks'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($measurementUnit['ChallanDetail'] as $challanDetail): ?>
		<tr>
			<td class="text-center"><?php echo $challanDetail['id']; ?></td>
			<td class="text-center"><?php echo $challanDetail['challan_id']; ?></td>
			<td class="text-center"><?php echo $challanDetail['product_id']; ?></td>
			<td class="text-center"><?php echo $challanDetail['measurement_unit_id']; ?></td>
			<td class="text-center"><?php echo $challanDetail['challan_qty']; ?></td>
			<td class="text-center"><?php echo $challanDetail['received_qty']; ?></td>
			<td class="text-center"><?php echo $challanDetail['batch_id']; ?></td>
			<td class="text-center"><?php echo $challanDetail['remarks']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'challan_details', 'action' => 'view', $challanDetail['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'challan_details', 'action' => 'edit', $challanDetail['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'challan_details', 'action' => 'delete', $challanDetail['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $challanDetail['id'])); ?>
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
					<h3 class="box-title"><?php echo __('Related Product Measurements'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Product Measurement'), array('controller' => 'product_measurements', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($measurementUnit['ProductMeasurement'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Product Id'); ?></th>
		<th class="text-center"><?php echo __('Measurement Unit Id'); ?></th>
		<th class="text-center"><?php echo __('Is Base'); ?></th>
		<th class="text-center"><?php echo __('Qty In Base'); ?></th>
		<th class="text-center"><?php echo __('Is Active'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($measurementUnit['ProductMeasurement'] as $productMeasurement): ?>
		<tr>
			<td class="text-center"><?php echo $productMeasurement['id']; ?></td>
			<td class="text-center"><?php echo $productMeasurement['product_id']; ?></td>
			<td class="text-center"><?php echo $productMeasurement['measurement_unit_id']; ?></td>
			<td class="text-center"><?php echo $productMeasurement['is_base']; ?></td>
			<td class="text-center"><?php echo $productMeasurement['qty_in_base']; ?></td>
			<td class="text-center"><?php echo $productMeasurement['is_active']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'product_measurements', 'action' => 'view', $productMeasurement['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'product_measurements', 'action' => 'edit', $productMeasurement['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'product_measurements', 'action' => 'delete', $productMeasurement['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $productMeasurement['id'])); ?>
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
					<h3 class="box-title"><?php echo __('Related Product Prices'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Product Price'), array('controller' => 'product_prices', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($measurementUnit['ProductPrice'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Product Id'); ?></th>
		<th class="text-center"><?php echo __('Target Custommer'); ?></th>
		<th class="text-center"><?php echo __('Measurement Unit Id'); ?></th>
		<th class="text-center"><?php echo __('Institute Id'); ?></th>
		<th class="text-center"><?php echo __('Effective Date'); ?></th>
		<th class="text-center"><?php echo __('Is Active'); ?></th>
		<th class="text-center"><?php echo __('General Price'); ?></th>
		<th class="text-center"><?php echo __('Has Combination'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($measurementUnit['ProductPrice'] as $productPrice): ?>
		<tr>
			<td class="text-center"><?php echo $productPrice['id']; ?></td>
			<td class="text-center"><?php echo $productPrice['product_id']; ?></td>
			<td class="text-center"><?php echo $productPrice['target_custommer']; ?></td>
			<td class="text-center"><?php echo $productPrice['measurement_unit_id']; ?></td>
			<td class="text-center"><?php echo $productPrice['institute_id']; ?></td>
			<td class="text-center"><?php echo $productPrice['effective_date']; ?></td>
			<td class="text-center"><?php echo $productPrice['is_active']; ?></td>
			<td class="text-center"><?php echo $productPrice['general_price']; ?></td>
			<td class="text-center"><?php echo $productPrice['has_combination']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'product_prices', 'action' => 'view', $productPrice['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'product_prices', 'action' => 'edit', $productPrice['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'product_prices', 'action' => 'delete', $productPrice['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $productPrice['id'])); ?>
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
					<h3 class="box-title"><?php echo __('Related Target For Product Sales'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Target For Product Sale'), array('controller' => 'target_for_product_sales', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($measurementUnit['TargetForProductSale'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Target Id'); ?></th>
		<th class="text-center"><?php echo __('Period Id'); ?></th>
		<th class="text-center"><?php echo __('Product Id'); ?></th>
		<th class="text-center"><?php echo __('Target Qty'); ?></th>
		<th class="text-center"><?php echo __('Measurement Unit Id'); ?></th>
		<th class="text-center"><?php echo __('Target Amount'); ?></th>
		<th class="text-center"><?php echo __('Assigned Qty'); ?></th>
		<th class="text-center"><?php echo __('Assigned Amount'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($measurementUnit['TargetForProductSale'] as $targetForProductSale): ?>
		<tr>
			<td class="text-center"><?php echo $targetForProductSale['id']; ?></td>
			<td class="text-center"><?php echo $targetForProductSale['target_id']; ?></td>
			<td class="text-center"><?php echo $targetForProductSale['period_id']; ?></td>
			<td class="text-center"><?php echo $targetForProductSale['product_id']; ?></td>
			<td class="text-center"><?php echo $targetForProductSale['target_qty']; ?></td>
			<td class="text-center"><?php echo $targetForProductSale['measurement_unit_id']; ?></td>
			<td class="text-center"><?php echo $targetForProductSale['target_amount']; ?></td>
			<td class="text-center"><?php echo $targetForProductSale['assigned_qty']; ?></td>
			<td class="text-center"><?php echo $targetForProductSale['assigned_amount']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'target_for_product_sales', 'action' => 'view', $targetForProductSale['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'target_for_product_sales', 'action' => 'edit', $targetForProductSale['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'target_for_product_sales', 'action' => 'delete', $targetForProductSale['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $targetForProductSale['id'])); ?>
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

