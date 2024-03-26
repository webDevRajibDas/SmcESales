<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Edit Office'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Office List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<?php echo $this->Form->create('Office', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('office_name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('short_name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('name_bangla', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('office_type_id', array('class' => 'form-control', 'options' => $officeTypes, 'empty' => '---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('parent_office_id', array('class' => 'form-control', 'options' => $parentOffices, 'empty' => '---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('order', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('phone', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('email', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('address_bangla', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('longitude', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('latitude', array('class' => 'form-control')); ?>
				</div>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>