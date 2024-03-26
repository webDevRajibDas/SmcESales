<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<?php echo $this->Form->create('DistInventoryAdjustment', array('role' => 'form')); ?>
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Distributor Inventory Adjustment'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Inventory Adjustment List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<table id="InventoryAdjustments" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td style="width:15%"><strong><?php echo __('Status'); ?></strong></td>
							<td>
								<?php
								if ($inventoryAdjustment['DistInventoryAdjustment']['status'] == 2) {
									echo 'In';
								} elseif ($inventoryAdjustment['DistInventoryAdjustment']['status'] == 1) {
									echo "Out";
								}
								?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td style="width:15%"><strong><?php echo __('Remarks'); ?></strong></td>
							<td>
								<?php echo h($inventoryAdjustment['DistInventoryAdjustment']['remarks']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td style="width:15%"><strong><?php echo __('Date'); ?></strong></td>
							<td>
								<?php echo $this->App->dateformat($inventoryAdjustment['DistInventoryAdjustment']['created_at']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td style="width:15%"><strong><?php echo __('Aproval  Status'); ?></strong></td>
							<td>
								<?php echo $inventoryAdjustment['DistInventoryAdjustment']['approval_status'] == 0 ? '<span class="btn btn-warning btn-xs">Pending</span>' : '<span class="btn btn-success btn-xs">Approved</span>'; ?>
								&nbsp;
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- details -->
			<?php if (!empty($inventoryAdjustment['DistInventoryAdjustmentDetail'])) : ?>
				<div class="box-body table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center"><?php echo __('Id'); ?></th>
								<th class="text-center"><?php echo __('Product Name'); ?></th>
								<th class="text-center"><?php echo __('Product Code'); ?></th>
								<th class="text-center"><?php echo __('Quantity'); ?></th>
								<th class="text-center"><?php echo __('Bonus Quantity'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							foreach ($inventoryAdjustment['InventoryAdjustmentDetail'] as $inventoryAdjustmentDetail) :
							?>
								<tr>
									<td class="text-center"><?php echo ++$i; ?></td>
									<td class="text-center"><?php echo $inventoryAdjustmentDetail['product_info']['Product']['name']; ?></td>
									<td class="text-center"><?php echo $inventoryAdjustmentDetail['product_info']['Product']['product_code']; ?></td>
									<td class="text-center"><?php echo $inventoryAdjustmentDetail['quantity']; ?></td>
									<td class="text-center"><?php echo $inventoryAdjustmentDetail['bonus_quantity']; ?></td>
								</tr>
								<input type="hidden" name="data[DistCurrentInventory][dist_current_inventory_id][]" value="<?= $inventoryAdjustmentDetail['dist_current_inventory_id'] ?>" />
								<input type="hidden" name="data[DistCurrentInventory][dist_inventory_adjustment_id]" value="<?= $inventoryAdjustmentDetail['dist_inventory_adjustment_id'] ?>" />
								<input type="hidden" name="data[DistCurrentInventory][quantity][]" value="<?= $inventoryAdjustmentDetail['quantity'] ?>" />
								<input type="hidden" name="data[DistCurrentInventory][bonus_quantity][]" value="<?= $inventoryAdjustmentDetail['bonus_quantity'] ?>" />
							<?php endforeach; ?>
							<input type="hidden" name="data[DistCurrentInventory][status]" value="<?= $inventoryAdjustment['DistInventoryAdjustment']['status'] ?>" />
							<input type="hidden" name="data[DistInventoryAdjustment][transaction_type_id]" value="<?= $inventoryAdjustment['DistInventoryAdjustment']['transaction_type_id'] ?>" />
						</tbody>
					</table><!-- /.table table-striped table-bordered -->
				</div><!-- /.table-responsive -->
			<?php endif; ?>
			<?php
			if ($inventoryAdjustment['DistInventoryAdjustment']['approval_status'] == 0 and $office_paren_id == 0) {
				echo $this->Form->submit('Approved', array('class' => 'btn btn-large btn-primary'));
			}
			?>
			<?php echo $this->Form->end(); ?>
		</div>


	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->