<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Selective Outlet'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Selective Outlet List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('SelectiveOutlet', array('role' => 'form')); ?>
			<div class="form-group">
					<label>Outlet Category : </label>
					<div id="outletcatlist" class="input select" style="margin-left:26%;">
						<?php echo $output; ?>
					</div>
				</div>

			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
		</div>			
	</div>
</div>


<script>
    $(document).ready(function() {
		$("input[type='checkbox']").iCheck('destroy');
    	$("input[type='radio']").iCheck('destroy');
    });
</script>