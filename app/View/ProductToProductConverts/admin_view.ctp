<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Product Convert History'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Convert History List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="ProductConvertHistories" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($productConvertHistory['ProductConvertHistory']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Store'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($productConvertHistory['Store']['name'], array('controller' => 'stores', 'action' => 'view', $productConvertHistory['Store']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('From Product Id'); ?></strong></td>
		<td>
			<?php echo h($productConvertHistory['ProductConvertHistory']['from_product_id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('To Product Id'); ?></strong></td>
		<td>
			<?php echo h($productConvertHistory['ProductConvertHistory']['to_product_id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('From Status Id'); ?></strong></td>
		<td>
			<?php echo h($productConvertHistory['ProductConvertHistory']['from_status_id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('To Status Id'); ?></strong></td>
		<td>
			<?php echo h($productConvertHistory['ProductConvertHistory']['to_status_id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Quantity'); ?></strong></td>
		<td>
			<?php echo h($productConvertHistory['ProductConvertHistory']['quantity']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Type'); ?></strong></td>
		<td>
			<?php echo h($productConvertHistory['ProductConvertHistory']['type']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created At'); ?></strong></td>
		<td>
			<?php echo h($productConvertHistory['ProductConvertHistory']['created_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created By'); ?></strong></td>
		<td>
			<?php echo h($productConvertHistory['ProductConvertHistory']['created_by']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

