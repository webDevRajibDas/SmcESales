<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('User Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> User List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
				<table id="ProductCategories" class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo __('User Group'); ?></strong></td>
							<td><?php echo h($user['UserGroup']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Username'); ?></strong></td>
							<td><?php echo h($user['User']['username']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Code'); ?></strong></td>
							<td><?php echo h($user['SalesPerson']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Name'); ?></strong></td>
							<td><?php echo h($user['SalesPerson']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Designation'); ?></strong></td>
							<td><?php echo h($user['SalesPerson']['Designation']['designation_name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Office'); ?></strong></td>
							<td><?php echo h($user['SalesPerson']['Office']['office_name']); ?></td>
						</tr>						
						<tr>		
							<td><strong><?php echo __('Is Active'); ?></strong></td>
							<td><?php if($user['User']['active']==1){ echo 'Yes'; }else{ echo 'No'; }; ?></td>
						</tr>															
					</tbody>
				</table>
			</div>			
		</div>
	</div>
</div>
