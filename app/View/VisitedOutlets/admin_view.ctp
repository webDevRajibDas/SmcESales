<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Visited Outlet'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Visited Outlet List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="VisitedOutlets" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($visitedOutlet['VisitedOutlet']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Outlet'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($visitedOutlet['Outlet']['name'], array('controller' => 'outlets', 'action' => 'view', $visitedOutlet['Outlet']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Sales Person'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($visitedOutlet['SalesPerson']['name'], array('controller' => 'sales_people', 'action' => 'view', $visitedOutlet['SalesPerson']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Longitude'); ?></strong></td>
		<td>
			<?php echo h($visitedOutlet['VisitedOutlet']['longitude']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Latitude'); ?></strong></td>
		<td>
			<?php echo h($visitedOutlet['VisitedOutlet']['latitude']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Visited At'); ?></strong></td>
		<td>
			<?php echo h($visitedOutlet['VisitedOutlet']['visited_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created At'); ?></strong></td>
		<td>
			<?php echo h($visitedOutlet['VisitedOutlet']['created_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created By'); ?></strong></td>
		<td>
			<?php echo h($visitedOutlet['VisitedOutlet']['created_by']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated At'); ?></strong></td>
		<td>
			<?php echo h($visitedOutlet['VisitedOutlet']['updated_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated By'); ?></strong></td>
		<td>
			<?php echo h($visitedOutlet['VisitedOutlet']['updated_by']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

