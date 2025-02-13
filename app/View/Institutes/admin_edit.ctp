<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Institute'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Institute List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Institute', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('short_name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('type', array('class' => 'form-control','options'=>$institute_list,'empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('email', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('telephone', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('contactname', array('label'=>'Contact Name :','class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_active',array('label'=>'<b>Is Active :</b>','type'=>'checkbox'));	?>
				</div>
				<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>