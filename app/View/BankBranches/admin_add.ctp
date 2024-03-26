<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Bank Branches'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>Bank Branches'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('BankBranch', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control','label'=>'Branch Name')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('bank_id', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$bank)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('address', array('type'=>'textarea','class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('id'=>'office_id', 'class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>
                <div class="form-group">
					<?php echo $this->Form->input('is_common', array('id' => 'is_common', 'class' => 'form-control is_common','type'=>'select','label'=>'<b>Is Common :</b>','options'=>array('0'=>'No','1'=>'Yes'))); ?>
				</div>	
				<div class="form-group territory">
					<?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>	
                		
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>

<script>
$(document).ready(function(){
		
	$('#office_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});	
	
	var is_common = $("#is_common option:selected").val(); 
	if(is_common==1){
		$('.territory div.input').removeClass('required');
		$('#territory_id').prop('required', false);
		
		$("#territory_id").prop("selectedIndex", 0);
		$('#territory_id').prop('disabled', true);
	}else{
		$('.territory div.input').addClass('required');
		$('#territory_id').prop('required', true);
		$('#territory_id').prop('disabled', false);
	}
		
	$('#is_common').on('change', function (e) {
		//alert($("option:selected", this).val());
		if($("option:selected", this).val()==1){
			$('.territory div.input').removeClass('required');
			$('#territory_id').prop('required', false);
			
			$("#territory_id").prop("selectedIndex", 0);
			$('#territory_id').prop('disabled', true);
		}else{
			$('.territory div.input').addClass('required');
			$('#territory_id').prop('required', true);
			$('#territory_id').prop('disabled', false);
		}
	});
	
});
</script>