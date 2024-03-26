<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Edit Product Month'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Month List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<?php echo $this->Form->create('ProductMonth', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('product_id', array('class' => 'form-control chosen', 'options' => $product_list, 'empty' => '---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('day_month', array('type' => 'number', 'label' => 'Month', 'class' => 'form-control')); ?>
				</div>
				<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('.chosen').chosen();
	});
</script>