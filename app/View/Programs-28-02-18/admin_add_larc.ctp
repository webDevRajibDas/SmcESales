<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add/Edit LARC Program'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> LARC Program List'), array('action' => 'larc_program_list'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<?php echo $this->Form->create('Program', array('role' => 'form')); ?>			
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('id'=>'office_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>true)); ?>
				</div>	
				<div class="form-group">
					<?php echo $this->Form->input('market_id', array('id'=>'market_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>true)); ?>
				</div>								
				<?php echo $this->Form->submit('Search', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
				<br/><br/>
				<?php				
				if(!empty($doctors))
				{	
				?>
				<?php echo $this->Form->create('Program', array('role' => 'form','action'=>'add_larc_list')); ?>
				<input type="hidden" name="territory_id" value="<?=$this->request->data['Program']['territory_id'];?>" />
				<input type="hidden" name="market_id" value="<?=$this->request->data['Program']['market_id'];?>" />
				<table class="table table-bordered table-striped">
					<tr>
						<th class="text-center"></th>
						<th class="text-center">Outlet Name</th>
						<th class="text-center">Doctor Name</th>
						<th class="text-center">Doctor Type</th>
						<th class="text-center">Code</th>
						<th class="text-center">Assigned Date</th>
					</tr>	
				<?php			
					foreach($doctors as $val){
					$id = $val['Doctor']['id'];
				?>															
					<tr>
						<td class="text-center">							
							<input type="checkbox" class="doctor_id" name="doctor_id[<?=$id;?>]" value="<?=$id;?>" <?php if($val['Program']['doctor_id'] == $id AND $val['Program']['officer_id'] == $user_id){ echo 'checked'; } ?> required />
							<input type="hidden" name="program_id[<?=$id;?>]" value="<?=$val['Program']['id'];?>" />
							<input type="hidden" name="outlet_id[<?=$id;?>]" value="<?=$val['Outlet']['id'];?>" />
						</td>
						<td><?php echo $val['Outlet']['name']; ?></td>
						<td><?php echo $val['Doctor']['name']; ?></td>
						<td><?php echo $val['DoctorType']['title']; ?></td>						
						<td>
							<input type="text" class="form-control code_<?=$id;?>" name="code[<?=$id;?>]" value="<?php echo $val['Program']['code']; ?>"/>
						</td>
						<td>							
							<input type="text" class="form-control assigned_date_<?=$id;?> datepicker" name="assigned_date[<?=$id;?>]" value="<?php echo ($val['Program']['assigned_date'] !=NULL ? date('d-m-Y',strtotime($val['Program']['assigned_date'])) : ''); ?>"/>
						</td>						
					</tr>
				<?php
					}
				?>				
				</table>				
				</br>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>	
				<?php	
				}
				?>
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
	
	$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'markets/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':''}
	});	
	
	$('#office_id').change(function (){
		$('#market_id').html('<option value="">---- Select -----</option>');
	});	
	
	$('.doctor_id').on('ifChanged', function(event){
		var id = $(this).val();
		if ($(this).is(":checked")) {	        
	        $('.code_'+id).attr("required", true);
	        $('.assigned_date_'+id).attr("required", true);
	    }
	    else {
	        $('.code_'+id).removeAttr("required");
	        $('.assigned_date_'+id).removeAttr("required");
	    }
	});

});
</script>