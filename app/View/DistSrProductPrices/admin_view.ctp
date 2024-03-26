<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Product Price'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Price List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="DistSrProductPrices" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($DistSrProductPrice['DistSrProductPrice']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Product'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($DistSrProductPrice['Product']['name'], array('controller' => 'products', 'action' => 'view', $DistSrProductPrice['Product']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Target Custommer'); ?></strong></td>
		<td>
			<?php echo h($DistSrProductPrice['DistSrProductPrice']['target_custommer']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Measurement Unit'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($DistSrProductPrice['MeasurementUnit']['name'], array('controller' => 'measurement_units', 'action' => 'view', $DistSrProductPrice['MeasurementUnit']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Institute'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($DistSrProductPrice['Institute']['name'], array('controller' => 'institutes', 'action' => 'view', $DistSrProductPrice['Institute']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Effective Date'); ?></strong></td>
		<td>
			<?php echo h($DistSrProductPrice['DistSrProductPrice']['effective_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Active'); ?></strong></td>
		<td>
			<?php echo h($DistSrProductPrice['DistSrProductPrice']['is_active']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('General Price'); ?></strong></td>
		<td>
			<?php echo h($DistSrProductPrice['DistSrProductPrice']['general_price']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Has Combination'); ?></strong></td>
		<td>
			<?php echo h($DistSrProductPrice['DistSrProductPrice']['has_combination']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

