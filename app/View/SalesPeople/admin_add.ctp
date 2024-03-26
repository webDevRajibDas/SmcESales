<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Sales Person'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Sales Person List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">				
			<?php echo $this->Form->create('SalesPerson', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('code', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('designation_id', array('class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('parent_id', array('class' => 'form-control','type'=>'select','options'=>$parentSalesPeople,'empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('class' => 'form-control','empty'=>'---- Select ----','id'=>'office_id')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('territory_id', array('class' => 'form-control','empty'=>'---- Select ----','id'=>'territory_id')); ?>
				</div>
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
$(document).ready(function() {
	$('#office_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});		
});
</script>