<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Session Type'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Session Type List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="SessionTypes" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($sessionType['SessionType']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($sessionType['SessionType']['name']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Sessions'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Session'), array('controller' => 'sessions', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($sessionType['Session'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Territory Id'); ?></th>
		<th class="text-center"><?php echo __('So Id'); ?></th>
		<th class="text-center"><?php echo __('Name'); ?></th>
		<th class="text-center"><?php echo __('Session Type Id'); ?></th>
		<th class="text-center"><?php echo __('Total Participant'); ?></th>
		<th class="text-center"><?php echo __('Total Attend'); ?></th>
		<th class="text-center"><?php echo __('Total Male'); ?></th>
		<th class="text-center"><?php echo __('Total Female'); ?></th>
		<th class="text-center"><?php echo __('Session Date'); ?></th>
		<th class="text-center"><?php echo __('Session Arranged Date'); ?></th>
		<th class="text-center"><?php echo __('Created At'); ?></th>
		<th class="text-center"><?php echo __('Created By'); ?></th>
		<th class="text-center"><?php echo __('Updated At'); ?></th>
		<th class="text-center"><?php echo __('Updated By'); ?></th>
		<th class="text-center"><?php echo __('Action'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($sessionType['Session'] as $session): ?>
		<tr>
			<td class="text-center"><?php echo $session['id']; ?></td>
			<td class="text-center"><?php echo $session['territory_id']; ?></td>
			<td class="text-center"><?php echo $session['so_id']; ?></td>
			<td class="text-center"><?php echo $session['name']; ?></td>
			<td class="text-center"><?php echo $session['session_type_id']; ?></td>
			<td class="text-center"><?php echo $session['total_participant']; ?></td>
			<td class="text-center"><?php echo $session['total_attend']; ?></td>
			<td class="text-center"><?php echo $session['total_male']; ?></td>
			<td class="text-center"><?php echo $session['total_female']; ?></td>
			<td class="text-center"><?php echo $session['session_date']; ?></td>
			<td class="text-center"><?php echo $session['session_arranged_date']; ?></td>
			<td class="text-center"><?php echo $session['created_at']; ?></td>
			<td class="text-center"><?php echo $session['created_by']; ?></td>
			<td class="text-center"><?php echo $session['updated_at']; ?></td>
			<td class="text-center"><?php echo $session['updated_by']; ?></td>
			<td class="text-center"><?php echo $session['action']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'sessions', 'action' => 'view', $session['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'sessions', 'action' => 'edit', $session['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'sessions', 'action' => 'delete', $session['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $session['id'])); ?>
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

