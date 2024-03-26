<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Territory Person'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Territory Person List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="TerritoryPeople" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($territoryPerson['TerritoryPerson']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Territory'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($territoryPerson['Territory']['name'], array('controller' => 'territories', 'action' => 'view', $territoryPerson['Territory']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Sales Person'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($territoryPerson['SalesPerson']['name'], array('controller' => 'sales_people', 'action' => 'view', $territoryPerson['SalesPerson']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

