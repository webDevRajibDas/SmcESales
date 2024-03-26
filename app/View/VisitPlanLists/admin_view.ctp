<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Visit Plan List'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Visit Plan List List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="VisitPlanLists" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($visitPlanList['VisitPlanList']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Aso Id'); ?></strong></td>
		<td>
			<?php echo h($visitPlanList['VisitPlanList']['aso_id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('So Id'); ?></strong></td>
		<td>
			<?php echo h($visitPlanList['VisitPlanList']['so_id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Market'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($visitPlanList['Market']['name'], array('controller' => 'markets', 'action' => 'view', $visitPlanList['Market']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Visit Plan Date'); ?></strong></td>
		<td>
			<?php echo h($visitPlanList['VisitPlanList']['visit_plan_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Visited Date'); ?></strong></td>
		<td>
			<?php echo h($visitPlanList['VisitPlanList']['visited_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Out Of Plan'); ?></strong></td>
		<td>
			<?php echo h($visitPlanList['VisitPlanList']['is_out_of_plan']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Visit Status'); ?></strong></td>
		<td>
			<?php echo h($visitPlanList['VisitPlanList']['visit_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created At'); ?></strong></td>
		<td>
			<?php echo h($visitPlanList['VisitPlanList']['created_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created By'); ?></strong></td>
		<td>
			<?php echo h($visitPlanList['VisitPlanList']['created_by']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

