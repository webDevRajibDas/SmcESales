<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Report Product Setting'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Outlet List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('ReportProductSetting', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('product_id', array('id'=>'product_id', 'class' => 'form-control chosen','empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('sort', array('class' => 'form-control', 'type' => 'number')); ?>
				</div>
			<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>

<script>
$(document).ready(function(){
	$(".chosen").chosen();
	$('.institute_id').hide();
	var is_ngo_val = '<?php echo $this->data['Outlet']['is_ngo']; ?>';	
	if(is_ngo_val == '1')
	{
		$('.institute_id').show();
	}
	
	$('.is_ngo').change(function(){
		var is_ngo = $(this).val();
		if(is_ngo == 1)
		{
			$('.institute_id').show();
		}else{
			$('.institute_id').hide();
		}
	});
	
	$('#office_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});
	
	$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'markets/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':'location_type_id'}
	});
	
	$('#location_type_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'markets/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':'location_type_id'}
	});
	
	$('#office_id').change(function (){
		$('#market_id').html('<option value="">---- Select -----</option>');
	});
	
	$('#type').selectChain({
		target: $('#institute_id'),
		value:'name',
		url: '<?= BASE_URL.'institutes/get_institute_list'?>',
		type: 'post',
		data:{'institute_type_id': 'type'}
	});
	
});
</script>

