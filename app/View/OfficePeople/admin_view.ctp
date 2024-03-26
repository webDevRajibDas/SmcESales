<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Office Person'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Office Person List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="OfficePeople" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($officePerson['OfficePerson']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Office'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($officePerson['Office']['id'], array('controller' => 'offices', 'action' => 'view', $officePerson['Office']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Sales Person'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($officePerson['SalesPerson']['name'], array('controller' => 'sales_people', 'action' => 'view', $officePerson['SalesPerson']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Effective Date'); ?></strong></td>
		<td>
			<?php echo h($officePerson['OfficePerson']['effective_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Active'); ?></strong></td>
		<td>
			<?php echo h($officePerson['OfficePerson']['is_active']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

