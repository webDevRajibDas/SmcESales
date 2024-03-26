<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Week'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Week List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Weeks" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($week['Week']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Week Name'); ?></strong></td>
		<td>
			<?php echo h($week['Week']['week_name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Start Date'); ?></strong></td>
		<td>
			<?php echo h($week['Week']['start_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('End Date'); ?></strong></td>
		<td>
			<?php echo h($week['Week']['end_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Month'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($week['Month']['name'], array('controller' => 'months', 'action' => 'view', $week['Month']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

