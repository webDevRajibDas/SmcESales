<?php
/* echo "<pre>";
print_r($inventoryAdjustment);
exit; */
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<?php echo $this->Form->create('InventoryAdjustment', array('role' => 'form')); ?>	
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Inventory Adjustment'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Inventory Adjustment List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
			<table id="InventoryAdjustments" class="table table-bordered table-striped">
				<tbody>
					<tr>		
						<td style="width:15%"><strong><?php echo __('Status'); ?></strong></td>
						<td>
						<?php
						if($inventoryAdjustment['InventoryAdjustment']['status'] == 2){
							echo 'In';
						}elseif($inventoryAdjustment['InventoryAdjustment']['status'] == 1){
							echo "Out";
						}
						?>
						&nbsp;
						</td>
					</tr>
					<tr>		
						<td style="width:15%"><strong><?php echo __('Remarks'); ?></strong></td>
						<td>
						<?php echo h($inventoryAdjustment['InventoryAdjustment']['remarks']); ?>
						&nbsp;
						</td>
					</tr>
					<tr>		
						<td style="width:15%"><strong><?php echo __('Date'); ?></strong></td>
						<td>
						<?php echo $this->App->dateformat($inventoryAdjustment['InventoryAdjustment']['created_at']); ?>
						&nbsp;
						</td>
					</tr>			
					<tr>		
						<td style="width:15%"><strong><?php echo __('Aproval  Status'); ?></strong></td>
						<td>
						<?php echo $inventoryAdjustment['InventoryAdjustment']['acknowledge_status'] == 0 ? '<span class="btn btn-warning btn-xs">Pending</span>' : '<span class="btn btn-success btn-xs">Approved</span>'; ?>
						&nbsp;
						</td>
					</tr>
				</tbody>
			</table>
			</div>	
			<!-- details -->
			<?php if (!empty($inventoryAdjustment['InventoryAdjustmentDetail'])): ?>
				<div class="box-body table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center"><?php echo __('Id'); ?></th>
								<th class="text-center"><?php echo __('Product Name'); ?></th>
								<th class="text-center"><?php echo __('Product Code'); ?></th>
								<th class="text-center"><?php echo __('Batch Number'); ?></th>
								<th class="text-center"><?php echo __('expire_date'); ?></th>
								<th class="text-center"><?php echo __('Quantity'); ?></th>
							</tr>
						</thead>
						<tbody>
								<?php
									$i = 0;
									foreach ($inventoryAdjustment['InventoryAdjustmentDetail'] as $inventoryAdjustmentDetail): ?>
									<tr>
										<td class="text-center"><?php echo ++$i; ?></td>
										<td class="text-center"><?php echo $inventoryAdjustmentDetail['product_info']['Product']['name']; ?></td>
										<td class="text-center"><?php echo $inventoryAdjustmentDetail['product_info']['Product']['product_code']; ?></td>
										<td class="text-center"><?php echo $inventoryAdjustmentDetail['product_info']['CurrentInventory']['batch_number']; ?></td>
										<td class="text-center"><?php echo $inventoryAdjustmentDetail['product_info']['CurrentInventory']['expire_date']; ?></td>
										<!--
										<td class="text-center"><?php echo $inventoryAdjustmentDetail['inventory_adjustment_id']; ?></td>
										<td class="text-center"><?php echo $inventoryAdjustmentDetail['current_inventory_id']; ?></td>
										-->
										<td class="text-center"><?php echo $inventoryAdjustmentDetail['quantity']; ?></td>
									</tr>
									<input type="hidden"name="data[CurrentInventory][current_inventory_id][]"value="<?=$inventoryAdjustmentDetail['current_inventory_id']?>"/>
									<input type="hidden"name="data[CurrentInventory][inventory_adjustment_id]"value="<?=$inventoryAdjustmentDetail['inventory_adjustment_id']?>"/>
									<input type="hidden"name="data[CurrentInventory][quantity][]"value="<?=$inventoryAdjustmentDetail['quantity']?>"/>
								<?php endforeach; ?>
								<input type="hidden"name="data[CurrentInventory][status]"value="<?=$inventoryAdjustment['InventoryAdjustment']['status']?>"/>
								<input type="hidden"name="data[InventoryAdjustment][transaction_type_id]"value="<?=$inventoryAdjustment['InventoryAdjustment']['transaction_type_id']?>"/>
						</tbody>
					</table><!-- /.table table-striped table-bordered -->
				</div><!-- /.table-responsive -->
			<?php endif; ?>
			<?php
			if($inventoryAdjustment['InventoryAdjustment']['acknowledge_status'] == 0 AND $office_paren_id == 0){
				echo $this->Form->submit('Approved', array('class' => 'btn btn-large btn-primary'));
			}
			?>
			<?php echo $this->Form->end(); ?>
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

