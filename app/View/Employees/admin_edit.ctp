
<?php //pr($edit);die;?>

<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Employee'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Employee List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">	
			<?php 
			echo $this->Session->flash();
			echo $this->Form->create('Employee',array('controller' => 'employees','url'=>'add',));
			echo $this->Form->input('Employee.id',array('type'=>'hidden','value'=>$edit['Employee']['id']));
			?>
			
				<div class="form-group">
					<?php echo $this->Form->input('Employee.first_name',array('class'=>'form-control','type'=>'text','placeholder'=>'Edit Your name','value'=>$edit['Employee']['first_name'])); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('last_name',array('class' => 'form-control', 'label'=>'Last Name','placeholder'=>'Enter last name','required','value'=>$edit['Employee']['last_name'])); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('username',array('class' => 'form-control', 'label'=>'Username','placeholder'=>'Enter username','required','value'=>$edit['Employee']['username'])); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('email',array('class' => 'form-control', 'label'=>'Email','placeholder'=>'Enter email','required','value'=>$edit['Employee']['email'])); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('phone',array('class' => 'form-control', 'label'=>'Phone','placeholder'=>'Enter phone','required','value'=>$edit['Employee']['phone'])); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('department',array('class' => 'form-control', 'label'=>'Department','placeholder'=>'Enter Department','required','value'=>$edit['Employee']['department'])); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('salary',array('type'=>'number','class' => 'form-control', 'label'=>'Salary','placeholder'=>'Enter Salary','required','value'=>round($edit['Employee']['salary'],2))); ?>
				</div>

			<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-info')); ?>

			<?php echo $this->Form->end(); ?>


		</div>			
	</div>
</div>