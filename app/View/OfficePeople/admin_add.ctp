<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Office Person'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Office Person List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('OfficePerson', array('role' => 'form')); ?>
					<div class="form-group">
						<?php echo $this->Form->input('office_id', array('class' => 'form-control','empty'=>'---- Select ----')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('sales_person_id', array('class' => 'form-control','empty'=>'---- Select ----')); ?>
					</div>
					<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('type' => 'text','class' => 'form-control datepicker')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('is_active', array('class' => 'form-control','type'=>'checkbox','label'=>'<strong>Is Active :</strong>')); ?>
					</div>

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>