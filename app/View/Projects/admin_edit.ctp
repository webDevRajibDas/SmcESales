<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Project'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Project List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Project', array('role' => 'form')); ?>
					<div class="form-group">
						<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('start_date', array('type' => 'text','class' => 'form-control datepicker','value'=>date("d-m-Y",strtotime($this->request->data['Project']['start_date'])))); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('end_date', array('type' => 'text','class' => 'form-control datepicker','value'=>date("d-m-Y",strtotime($this->request->data['Project']['end_date'])))); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('is_active', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is Active :</b>')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('institute_id', array('class' => 'form-control','empty'=>'---- Select -----')); ?>
					</div>

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>