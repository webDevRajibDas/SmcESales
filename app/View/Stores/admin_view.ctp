<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Store'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Store List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Stores" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($store['Store']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($store['Store']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Store Type'); ?></strong></td>
		<td>
			<?php echo h($store['Store']['store_type']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Office'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($store['Office']['office_name'], array('controller' => 'offices', 'action' => 'view', $store['Office']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Territory'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($store['Territory']['name'], array('controller' => 'territories', 'action' => 'view', $store['Territory']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created At'); ?></strong></td>
		<td>
			<?php echo h($store['Store']['created_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created By'); ?></strong></td>
		<td>
			<?php echo h($store['Store']['created_by']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated At'); ?></strong></td>
		<td>
			<?php echo h($store['Store']['updated_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated By'); ?></strong></td>
		<td>
			<?php echo h($store['Store']['updated_by']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Current Inventories'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Current Inventory'), array('controller' => 'current_inventories', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($store['CurrentInventory'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Store Id'); ?></th>
		<th class="text-center"><?php echo __('Inventory Status Id'); ?></th>
		<th class="text-center"><?php echo __('Product Id'); ?></th>
		<th class="text-center"><?php echo __('Batch Number'); ?></th>
		<th class="text-center"><?php echo __('M Unit'); ?></th>
		<th class="text-center"><?php echo __('Qty'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($store['CurrentInventory'] as $currentInventory): ?>
		<tr>
			<td class="text-center"><?php echo $currentInventory['id']; ?></td>
			<td class="text-center"><?php echo $currentInventory['store_id']; ?></td>
			<td class="text-center"><?php echo $currentInventory['inventory_status_id']; ?></td>
			<td class="text-center"><?php echo $currentInventory['product_id']; ?></td>
			<td class="text-center"><?php echo $currentInventory['batch_number']; ?></td>
			<td class="text-center"><?php echo $currentInventory['m_unit']; ?></td>
			<td class="text-center"><?php echo $currentInventory['qty']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'current_inventories', 'action' => 'view', $currentInventory['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'current_inventories', 'action' => 'edit', $currentInventory['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'current_inventories', 'action' => 'delete', $currentInventory['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $currentInventory['id'])); ?>
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

