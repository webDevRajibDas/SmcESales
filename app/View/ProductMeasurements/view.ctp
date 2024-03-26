<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Product Measurement'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Measurement List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="ProductMeasurements" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($productMeasurement['ProductMeasurement']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Product'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($productMeasurement['Product']['name'], array('controller' => 'products', 'action' => 'view', $productMeasurement['Product']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Measurement Unit'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($productMeasurement['MeasurementUnit']['name'], array('controller' => 'measurement_units', 'action' => 'view', $productMeasurement['MeasurementUnit']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Base'); ?></strong></td>
		<td>
			<?php echo h($productMeasurement['ProductMeasurement']['is_base']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Qty In Base'); ?></strong></td>
		<td>
			<?php echo h($productMeasurement['ProductMeasurement']['qty_in_base']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Active'); ?></strong></td>
		<td>
			<?php echo h($productMeasurement['ProductMeasurement']['is_active']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

