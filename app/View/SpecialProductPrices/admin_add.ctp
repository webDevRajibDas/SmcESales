<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Product Price'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Price List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('SpecialProductPrice', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('product_id', array('class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('target_custommer', array('class' => 'form-control','options'=>$target_customer,'empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('measurement_unit_id', array('class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('institute_id', array('class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('class' => 'form-control datepicker','type'=>'text')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_active', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is Active :</b>')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('general_price', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('has_price_slot', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Has Price Slot :</b>')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('has_combination', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Has Combination :</b>')); ?>
				</div>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>