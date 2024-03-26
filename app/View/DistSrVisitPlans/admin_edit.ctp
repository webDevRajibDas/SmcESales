<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit DistAppUserGroup'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> DistAppUserGroup List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('DistAppUserGroup', array('role' => 'form')); ?>
			<!-- <div class="form-group">
				<?php //echo $this->Form->input('office_id', array('class' => 'form-control office_id', 'id' => 'office_id', 'empty'=> '--- select ----', 'options'=> $offices )); ?>
			</div>
			<div class="form-group">
				<?php //echo $this->Form->input('distributor_id', array('class' => 'form-control distributor_id', 'id'=>'distributor_id')); ?>
			</div> -->
			<div class="form-group">
				<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
			</div>
			<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
		</div>			
	</div>
</div>
<!-- <script>
	$('.office_id').selectChain({
	target: $('.distributor_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/dist_app_user_groups/get_distributer_id';?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});
</script> -->