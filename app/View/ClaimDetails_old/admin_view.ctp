<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Claim Detail'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Claim Detail List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="ClaimDetails" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($claimDetail['ClaimDetail']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Claim'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($claimDetail['Claim']['claim_no'], array('controller' => 'claims', 'action' => 'view', $claimDetail['Claim']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Product'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($claimDetail['Product']['name'], array('controller' => 'products', 'action' => 'view', $claimDetail['Product']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Measurement Unit'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($claimDetail['MeasurementUnit']['name'], array('controller' => 'measurement_units', 'action' => 'view', $claimDetail['MeasurementUnit']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Challan Qty'); ?></strong></td>
		<td>
			<?php echo h($claimDetail['ClaimDetail']['challan_qty']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Claim Qty'); ?></strong></td>
		<td>
			<?php echo h($claimDetail['ClaimDetail']['claim_qty']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Batch No'); ?></strong></td>
		<td>
			<?php echo h($claimDetail['ClaimDetail']['batch_no']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Expire Date'); ?></strong></td>
		<td>
			<?php echo h($claimDetail['ClaimDetail']['expire_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Inventory Status'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($claimDetail['InventoryStatus']['name'], array('controller' => 'inventory_statuses', 'action' => 'view', $claimDetail['InventoryStatus']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Remarks'); ?></strong></td>
		<td>
			<?php echo h($claimDetail['ClaimDetail']['remarks']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

