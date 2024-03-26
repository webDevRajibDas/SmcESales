<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Fiscal Year'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Fiscal Year List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="FiscalYears" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($fiscalYear['FiscalYear']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Year Code'); ?></strong></td>
		<td>
			<?php echo h($fiscalYear['FiscalYear']['year_code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Start Date'); ?></strong></td>
		<td>
			<?php echo h($fiscalYear['FiscalYear']['start_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('End Date'); ?></strong></td>
		<td>
			<?php echo h($fiscalYear['FiscalYear']['end_date']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Months'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Month'), array('controller' => 'months', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($fiscalYear['Month'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Name'); ?></th>
		<th class="text-center"><?php echo __('Fiscal Year Id'); ?></th>
		<th class="text-center"><?php echo __('Month'); ?></th>
		<th class="text-center"><?php echo __('Year'); ?></th>
		<th class="text-center"><?php echo __('YearID'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($fiscalYear['Month'] as $month): ?>
		<tr>
			<td class="text-center"><?php echo $month['id']; ?></td>
			<td class="text-center"><?php echo $month['name']; ?></td>
			<td class="text-center"><?php echo $month['fiscal_year_id']; ?></td>
			<td class="text-center"><?php echo $month['month']; ?></td>
			<td class="text-center"><?php echo $month['year']; ?></td>
			<td class="text-center"><?php echo $month['YearID']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'months', 'action' => 'view', $month['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'months', 'action' => 'edit', $month['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'months', 'action' => 'delete', $month['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $month['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				
			</div><!-- /.related -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

