<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Message'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Message List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('MessageList', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('message_category_id', array('class' => 'form-control','empty' => '---- Select Category -----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('message_type', array('class' => 'form-control','empty' => '---- Select Type -----','options'=>array('2'=>'Inbox'))); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('message', array('type' => 'textarea','class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('MessageReceiver.receiver_id', array('multiple' => 'checkbox', 'options' => $receiver_list,'required'=>true)); ?>
					
				</div>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>