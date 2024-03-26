<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Project'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Project List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Projects" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($project['Project']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($project['Project']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Start Date'); ?></strong></td>
		<td>
			<?php echo h($project['Project']['start_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('End Date'); ?></strong></td>
		<td>
			<?php echo h($project['Project']['end_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Active'); ?></strong></td>
		<td>
			<?php echo h($project['Project']['is_active']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Institute'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($project['Institute']['name'], array('controller' => 'institutes', 'action' => 'view', $project['Institute']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Project Ngo Outlets'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Project Ngo Outlet'), array('controller' => 'project_ngo_outlets', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($project['ProjectNgoOutlet'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Project Id'); ?></th>
		<th class="text-center"><?php echo __('Outlet Id'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($project['ProjectNgoOutlet'] as $projectNgoOutlet): ?>
		<tr>
			<td class="text-center"><?php echo $projectNgoOutlet['id']; ?></td>
			<td class="text-center"><?php echo $projectNgoOutlet['project_id']; ?></td>
			<td class="text-center"><?php echo $projectNgoOutlet['outlet_id']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'project_ngo_outlets', 'action' => 'view', $projectNgoOutlet['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'project_ngo_outlets', 'action' => 'edit', $projectNgoOutlet['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'project_ngo_outlets', 'action' => 'delete', $projectNgoOutlet['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $projectNgoOutlet['id'])); ?>
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

