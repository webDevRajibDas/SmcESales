<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Outlet'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Outlet List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Outlet', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('ownar_name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('in_charge', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('telephone', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('mobile', array('class' => 'form-control')); ?>
				</div>
                <div class="form-group">
					<?php echo $this->Form->input('office_id', array('id'=>'office_id', 'class' => 'form-control','selected' => $office_id, 'empty'=>'---- Select ----')); ?>
				</div>
                <div class="form-group">
					<?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'class' => 'form-control', 'selected' => $territory_id, 'empty'=>'---- Select ----')); ?>
				</div>	
				<div class="form-group">
					<?php echo $this->Form->input('thana_id', array('id'=>'thana_id', 'class' => 'form-control','empty'=>'---- Select ----','selected'=>$thana_id)); ?>
				</div>	
				<div class="form-group">
					<?php echo $this->Form->input('location_type_id', array('id'=>'location_type_id','class' => 'form-control', 'selected' => $location_type_id, 'empty'=>'---- Select ----')); ?>
				</div>	
				<div class="form-group">
					<?php echo $this->Form->input('market_id', array('id'=>'market_id', 'class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('category_id', array('label' => 'Outlet Type','class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_pharma_type', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is Pharma Type :</b>')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('bonus_type_id', array('class' => 'form-control bonus_type','type'=>'select','options'=>array('0'=>'Not Applicable','1'=>'Small Bonus','2'=>'Big Bonus'), 'selected'=>$outlets['Outlet']['bonus_type_id'])); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_ngo', array('class' => 'form-control is_ngo','type'=>'select','label'=>'<b>Is NGO Type :</b>','options'=>array('0'=>'No','1'=>'Yes'))); ?>
				</div>
				<div class="form-group institute_id">
					<?php echo $this->Form->input('Institute.type', array('id'=>'type','class' => 'form-control','empty'=>'---- Select ----','options'=>$instituteTypes,'required'=>false)); ?>
				</div>				
				<div class="form-group institute_id">
					<?php echo $this->Form->input('institute_id', array('id'=>'institute_id','div'=>array('class'=>'required'),'class' => 'form-control','empty'=>'---- Select NGO ----','required'=>false)); ?>
				</div>
                
                <div class="form-group">
					<?php echo $this->Form->input('is_csa', array('class' => 'form-control is_csa','type'=>'select','label'=>'<b>Is CSA Type :</b>','options'=>array('0'=>'No','1'=>'Yes'))); ?>
				</div>
                
                <div class="form-group">
					<?php echo $this->Form->input('is_active', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is Active :</b>')); ?>
                </div>
                
			<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
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
		target: $('#thana_id'),
		value:'name',
		url: '<?= BASE_URL.'outlets/get_thana_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id'}
	});
	$('#thana_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'outlets/get_market_list'?>',
		type: 'post',
		data:{'thana_id': 'thana_id','location_type_id':'location_type_id','territory_id': 'territory_id'}
	});
	
	$('#location_type_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'markets/get_market_list'?>',
		type: 'post',
		data:{'thana_id': 'thana_id','territory_id': 'territory_id','location_type_id':'location_type_id'}
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

