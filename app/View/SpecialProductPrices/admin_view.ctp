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
                <table id="SpecialProductPrices" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($SpecialProductPrice['SpecialProductPrice']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Product'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($SpecialProductPrice['Product']['name'], array('controller' => 'products', 'action' => 'view', $SpecialProductPrice['Product']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Target Custommer'); ?></strong></td>
		<td>
			<?php echo h($SpecialProductPrice['SpecialProductPrice']['target_custommer']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Measurement Unit'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($SpecialProductPrice['MeasurementUnit']['name'], array('controller' => 'measurement_units', 'action' => 'view', $SpecialProductPrice['MeasurementUnit']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Institute'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($SpecialProductPrice['Institute']['name'], array('controller' => 'institutes', 'action' => 'view', $SpecialProductPrice['Institute']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Effective Date'); ?></strong></td>
		<td>
			<?php echo h($SpecialProductPrice['SpecialProductPrice']['effective_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Active'); ?></strong></td>
		<td>
			<?php echo h($SpecialProductPrice['SpecialProductPrice']['is_active']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('General Price'); ?></strong></td>
		<td>
			<?php echo h($SpecialProductPrice['SpecialProductPrice']['general_price']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Has Combination'); ?></strong></td>
		<td>
			<?php echo h($SpecialProductPrice['SpecialProductPrice']['has_combination']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

