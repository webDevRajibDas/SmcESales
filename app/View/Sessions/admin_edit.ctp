<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Session'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Session List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('ProgramSession', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				</div>				
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','empty'=>'---- Select Office ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','empty'=>'---- Select Territory ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('so_id', array('id' => 'sales_person_id','class' => 'form-control sales_person_id','empty'=>'---- Select SO ----','options'=>$salesPersons)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('session_type_id', array('id' => 'session_type_id','class' => 'form-control')); ?><span style="color:red">*</span>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('label'=>'Session Name :','class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('session_date', array('type'=>'text','class' => 'form-control datepicker','value'=> date('d-m-Y',strtotime($this->request->data['ProgramSession']['session_date'])))); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('total_participant', array('class' => 'form-control')); ?>
				</div>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
$('#office_id').selectChain({
	target: $('#territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sessions/get_territory_list';?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});

$('.territory_id').selectChain({
	target: $('.sales_person_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_so_list';?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});

$('.office_id').change(function(){
	$('.sales_person_id').html('<option value="">---- Select SO ----');
});
</script>