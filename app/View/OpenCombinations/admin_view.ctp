<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Product Combination'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Combination List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="ProductCombinations" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($productCombination['ProductCombination']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Product'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($productCombination['Product']['name'], array('controller' => 'products', 'action' => 'view', $productCombination['Product']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Indivisual Or Total'); ?></strong></td>
		<td>
			<?php echo h($productCombination['ProductCombination']['indivisual_or_total']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Min Qty'); ?></strong></td>
		<td>
			<?php echo h($productCombination['ProductCombination']['min_qty']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Min Total Qty'); ?></strong></td>
		<td>
			<?php echo h($productCombination['ProductCombination']['min_total_qty']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

