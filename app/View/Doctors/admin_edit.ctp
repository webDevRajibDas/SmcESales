<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Doctor'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Doctor List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Doctor', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('doctor_qualification_id', array('class' => 'form-control','empty'=>'---- Select Qualification ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('doctor_type_id', array('class' => 'form-control','empty'=>'---- Select Type ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('gender', array('class' => 'form-control','empty'=>'---- Select Gender ----','options'=>array('Male'=>'Male','Female'=>'Female'))); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','empty'=>'---- Select Territory ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','empty'=>'---- Select Market ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','empty'=>'---- Select Outlet ----')); ?>
				</div>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
$('.territory_id').selectChain({
	target: $('.market_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_market';?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});
$('.market_id').selectChain({
	target: $('.outlet_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_outlet';?>',
	type: 'post',
	data:{'market_id': 'market_id' }
});	
$('.territory_id').change(function(){	
	$('#outlet_id').html('<option value="">---- Select Outlet ----</option>');
});
</script>