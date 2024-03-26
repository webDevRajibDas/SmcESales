<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Bonus'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Bonus List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Bonus', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('mother_product_id', array('class' => 'form-control','options'=>$mother_products,'empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('mother_product_quantity', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('bonus_product_id', array('class' => 'form-control','options'=>$bonus_products,'empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('bonus_product_quantity', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('label'=>'Start Date','type'=>'text','class' => 'form-control datepicker')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('end_date', array('type'=>'text','class' => 'form-control datepicker')); ?>
				</div>	
				<div class="form-group">
					<label for="UserActive">Is Active :</label>
					<?php echo $this->Form->input('active', array('class' => 'form-control','type'=>'checkbox','label'=>false)); ?>
				</div>			
			<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>