<?php
App::import('Controller', 'ProgramsController');
$ProgramsController = new ProgramsController;					 
?>
<style>
.outlet_list .form-control{
	width:100%;
}
</style>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add/Edit Pink Star Program'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Pink Star Program List'), array('action' => 'larc_program_list'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<?php echo $this->Form->create('Program', array('role' => 'form')); ?>			
				<div class="form-group required">
					<?php echo $this->Form->input('office_id', array('id'=>'office_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>true)); ?>
				</div>
				<div class="form-group required">
					<?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>true)); ?>
				</div>	
                <div class="form-group required">
					<?php echo $this->Form->input('thana_id', array('id'=>'thana_id', 'class' => 'form-control','empty'=>'---- Select ----','required'=>true)); ?>
				</div>	
				<div class="form-group required">
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
				<table class="table table-bordered table-striped outlet_list">
					<tr>
						<th class="text-center"></th>
						<th class="text-center">Outlet Name</th>
						<th class="text-center">Doctor Name</th>
						<th class="text-center">Doctor Type</th>
						<th class="text-center">Code</th>
						<th class="text-center">Assigned Date</th>
					</tr>	
                    
                    
                    
                    
					<?php /*?><?php			
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
					?>	<?php */?>	
                    
                    
                    <?php 
					//pr($request_data);
					$program_info = array();
					foreach($doctors as $val)
					{ 
					
					//$id = $val[0]['id'];
					$id = $val[0]['outlet_id'];
					
					$program_info = $ProgramsController->get_program_info($doctor_id=0, $request_data, $outlet_id=$val[0]['outlet_id'], $program_type_id=3);
					//pr($program_info);
					?>
                    <tr>
						<td class="text-center">							
							<input type="checkbox" class="outlet_id" name="outlet_id[<?=$id;?>]" value="<?=$id;?>" <?=($program_info)?'checked':'';?> />
							
                            
                            <input type="hidden" name="program_id[<?=$id;?>]" value="<?=($program_info)?$program_info['Program']['id']:'';?>" />
                         
                            <input type="hidden" name="doctor_id[<?=$id;?>]" value="<?=$val[0]['id'];?>" /> 
							<?php /*?><input type="hidden" name="outlet_id[<?=$id;?>]" value="<?=$val[0]['outlet_id'];?>" /><?php */?>
						</td>
						<td><?php echo $val[0]['outlet_name']; ?></td>
						<td><?php echo $val[0]['name']; ?></td>
						<td><?php echo $val[0]['title']; ?></td>						
						<td>
							<input type="text" class="form-control code_<?=$id;?>" name="code[<?=$id;?>]" value="<?=($program_info)?$program_info['Program']['code']:'';?>"/>
						</td>
						<td>							
							<input type="text" class="form-control assigned_date_<?=$id;?> datepicker" name="assigned_date[<?=$id;?>]" value="<?=($program_info)?date('d-m-Y',strtotime($program_info['Program']['assigned_date'])):'';?>"/>
						</td>						
					</tr>
                    <?php } ?>
                    
                    		
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
	
	/*$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'markets/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':''}
	});*/	
	
	$('#territory_id').selectChain({
		target: $('#thana_id'),
		value:'name',
		url: '<?= BASE_URL.'programs/get_thana_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':''}
	});
	
	$('#thana_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'programs/get_market_list'?>',
		type: 'post',
		data:{'thana_id': 'thana_id', 'location_type_id':''}
	});	
	
	$('#office_id').change(function (){
		$('#market_id').html('<option value="">---- Select -----</option>');
	});	
	$('#office_id').change(function (){
		$('#thana_id').html('<option value="">---- Select -----</option>');
	});	
	
	$('#territory_id').change(function (){
		$('#market_id').html('<option value="">---- Select -----</option>');
	});	
	$('#territory_id').change(function (){
		$('#thana_id').html('<option value="">---- Select -----</option>');
	});		
	
	/*$('.doctor_id').on('ifChanged', function(event){
		var id = $(this).val();
		if ($(this).is(":checked")) {	        
	        //$('.code_'+id).attr("required", true);
	        $('.assigned_date_'+id).attr("required", true);
	    }
	    else {
	        //$('.code_'+id).removeAttr("required");
	        $('.assigned_date_'+id).removeAttr("required");
	    }
	});*/
	$('.outlet_id').on('ifChanged', function(event){
		var id = $(this).val();
		//alert(id);
		if ($(this).is(":checked")) {	        
	        //$('.member_type_'+id).attr("required", true);
	        //$('.code_'+id).attr("required", true);
	        $('.assigned_date_'+id).prop("required", true);
	    }
	    else {
	        //$('.member_type_'+id).removeAttr("required");
	        //$('.code_'+id).removeAttr("required");
	        $('.assigned_date_'+id).prop("required", false);
	    }
	});

});
</script>