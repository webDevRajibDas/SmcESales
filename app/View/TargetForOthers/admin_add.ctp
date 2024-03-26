<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Admin Add Target For Other'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Target For Other List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('TargetForOther', array('role' => 'form')); ?>
					<div class="form-group">
						<?php echo $this->Form->input('target_id', array('class' => 'form-control')); ?>
					</div>
<div class="form-group">
						<?php echo $this->Form->input('period_id', array('class' => 'form-control')); ?>
					</div>
<div class="form-group">
						<?php echo $this->Form->input('target_qty', array('class' => 'form-control')); ?>
					</div>

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>