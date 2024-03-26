<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Doctor Type'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Doctor Type List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="DoctorTypes" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($doctorType['DoctorType']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Title'); ?></strong></td>
		<td>
			<?php echo h($doctorType['DoctorType']['title']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Doctors'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Doctor'), array('controller' => 'doctors', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($doctorType['Doctor'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Name'); ?></th>
		<th class="text-center"><?php echo __('Doctor Qualification Id'); ?></th>
		<th class="text-center"><?php echo __('Doctor Type Id'); ?></th>
		<th class="text-center"><?php echo __('Gender'); ?></th>
		<th class="text-center"><?php echo __('Territory Id'); ?></th>
		<th class="text-center"><?php echo __('Market Id'); ?></th>
		<th class="text-center"><?php echo __('Outlet Id'); ?></th>
		<th class="text-center"><?php echo __('Created At'); ?></th>
		<th class="text-center"><?php echo __('Created By'); ?></th>
		<th class="text-center"><?php echo __('Updated At'); ?></th>
		<th class="text-center"><?php echo __('Updated By'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($doctorType['Doctor'] as $doctor): ?>
		<tr>
			<td class="text-center"><?php echo $doctor['id']; ?></td>
			<td class="text-center"><?php echo $doctor['name']; ?></td>
			<td class="text-center"><?php echo $doctor['doctor_qualification_id']; ?></td>
			<td class="text-center"><?php echo $doctor['doctor_type_id']; ?></td>
			<td class="text-center"><?php echo $doctor['gender']; ?></td>
			<td class="text-center"><?php echo $doctor['territory_id']; ?></td>
			<td class="text-center"><?php echo $doctor['market_id']; ?></td>
			<td class="text-center"><?php echo $doctor['outlet_id']; ?></td>
			<td class="text-center"><?php echo $doctor['created_at']; ?></td>
			<td class="text-center"><?php echo $doctor['created_by']; ?></td>
			<td class="text-center"><?php echo $doctor['updated_at']; ?></td>
			<td class="text-center"><?php echo $doctor['updated_by']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'doctors', 'action' => 'view', $doctor['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'doctors', 'action' => 'edit', $doctor['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'doctors', 'action' => 'delete', $doctor['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $doctor['id'])); ?>
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

