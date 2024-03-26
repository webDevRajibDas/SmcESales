<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Doctor'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Doctor List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Doctors" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($doctor['Doctor']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($doctor['Doctor']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Doctor Qualification'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($doctor['DoctorQualification']['title'], array('controller' => 'doctor_qualifications', 'action' => 'view', $doctor['DoctorQualification']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Doctor Type'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($doctor['DoctorType']['title'], array('controller' => 'doctor_types', 'action' => 'view', $doctor['DoctorType']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Gender'); ?></strong></td>
		<td>
			<?php echo h($doctor['Doctor']['gender']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Territory'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($doctor['Territory']['name'], array('controller' => 'territories', 'action' => 'view', $doctor['Territory']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Market'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($doctor['Market']['name'], array('controller' => 'markets', 'action' => 'view', $doctor['Market']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Outlet'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($doctor['Outlet']['name'], array('controller' => 'outlets', 'action' => 'view', $doctor['Outlet']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created At'); ?></strong></td>
		<td>
			<?php echo h($doctor['Doctor']['created_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created By'); ?></strong></td>
		<td>
			<?php echo h($doctor['Doctor']['created_by']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated At'); ?></strong></td>
		<td>
			<?php echo h($doctor['Doctor']['updated_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated By'); ?></strong></td>
		<td>
			<?php echo h($doctor['Doctor']['updated_by']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

