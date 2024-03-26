<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Receiver Office Person'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Receiver Office Person List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">				
			<?php echo $this->Form->create('RecieverOfficePerson', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('receive_type', array('class' => 'form-control','empty'=>'---- Select ----','options' => array( 1 => 'Challan', 2 => 'Requsition'))); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('class' => 'form-control','empty'=>'---- Select ----','id'=>'office_id')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('sales_person_id', array('class' => 'form-control','empty'=>'---- Select ----','id'=>'sales_person_id','options'=>$salesPeople)); ?>
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
		target: $('#sales_person_id'),
		value:'name',
		url: '<?= BASE_URL.'admin/reciever_office_people/get_sales_person'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});		
});
</script>