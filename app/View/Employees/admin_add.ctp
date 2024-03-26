<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Employee'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Employee List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">	
			<?php 
			echo $this->Session->flash();
			echo $this->Form->create('Employee',array('controller' => 'employees','url'=>'add',));
			?>
			
				<div class="form-group">
					<?php echo $this->Form->input('first_name',array('class' => 'form-control', 'label'=>'First Name','placeholder'=>'Enter first name','required')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('last_name',array('class' => 'form-control', 'label'=>'Last Name','placeholder'=>'Enter last name','required')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('username',array('class' => 'form-control', 'label'=>'Username','placeholder'=>'Enter username','required')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('email',array('class' => 'form-control', 'label'=>'Email','placeholder'=>'Enter email','required')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('phone',array('class' => 'form-control', 'label'=>'Phone','placeholder'=>'Enter phone','required')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('department',array('class' => 'form-control', 'label'=>'Department','placeholder'=>'Enter Department','required')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('salary',array('type'=>'number','class' => 'form-control', 'label'=>'Salary','placeholder'=>'Enter Salary','required')); ?>
				</div>

			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>

			<?php echo $this->Form->end(); ?>


		</div>			
	</div>
</div>