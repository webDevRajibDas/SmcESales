<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Sender/Receiver'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Sender/Receiver List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<?php echo $this->Form->create('PrimarySenderReceiver', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('store_type', array('label'=>'Type :','class' => 'form-control','options'=>array('1'=>'Sender','2'=>'Receiver'))); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('address', array('class' => 'form-control','type'=>'textarea')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('mobile', array('class' => 'form-control')); ?>
				</div>

				<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>