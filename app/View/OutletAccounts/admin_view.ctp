<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Outlet Account'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Outlet Account List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="OutletAccounts" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($outletAccount['OutletAccount']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Phone Number'); ?></strong></td>
		<td>
			<?php echo h($outletAccount['OutletAccount']['phone_number']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Password'); ?></strong></td>
		<td>
			<?php echo h($outletAccount['OutletAccount']['password']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('User Name'); ?></strong></td>
		<td>
			<?php echo h($outletAccount['OutletAccount']['user_name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Outlet'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($outletAccount['Outlet']['name'], array('controller' => 'outlets', 'action' => 'view', $outletAccount['Outlet']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Active'); ?></strong></td>
		<td>
			<?php echo h($outletAccount['OutletAccount']['is_active']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Status'); ?></strong></td>
		<td>
			<?php echo h($outletAccount['OutletAccount']['status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created At'); ?></strong></td>
		<td>
			<?php echo h($outletAccount['OutletAccount']['created_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated At'); ?></strong></td>
		<td>
			<?php echo h($outletAccount['OutletAccount']['updated_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated By'); ?></strong></td>
		<td>
			<?php echo h($outletAccount['OutletAccount']['updated_by']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

