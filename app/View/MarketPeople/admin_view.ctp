<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Market Person'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Market Person List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="MarketPeople" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($marketPerson['MarketPerson']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Market'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($marketPerson['Market']['name'], array('controller' => 'markets', 'action' => 'view', $marketPerson['Market']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Sales Person'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($marketPerson['SalesPerson']['name'], array('controller' => 'sales_people', 'action' => 'view', $marketPerson['SalesPerson']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

