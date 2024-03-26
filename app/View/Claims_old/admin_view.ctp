<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Claim'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Claim List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Claims" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($claim['Claim']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Claim No'); ?></strong></td>
		<td>
			<?php echo h($claim['Claim']['claim_no']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Claim Status'); ?></strong></td>
		<td>
			<?php echo h($claim['Claim']['claim_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Claim Type'); ?></strong></td>
		<td>
			<?php echo h($claim['Claim']['claim_type']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created At'); ?></strong></td>
		<td>
			<?php echo h($claim['Claim']['created_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Challan'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($claim['Challan']['id'], array('controller' => 'challans', 'action' => 'view', $claim['Challan']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

