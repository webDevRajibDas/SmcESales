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
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Add/Edit NGO For Injectable Outlet');?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> NGO For Injectable Outlet List'), array('action' => 'ngo_injective_program_list'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
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
				<div class="form-group">
					<?php echo $this->Form->input('market_id', array('id'=>'market_id', 'class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>								
				<?php echo $this->Form->submit('Search', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
				<br/>
                <br/>
                                
				<?php if(!empty($outlets)){ ?>
                
					<?php echo $this->Form->create('Program', array('role' => 'form','action'=>'add_ngo_injective_list')); ?>
                    <input type="hidden" name="territory_id" value="<?=$this->request->data['Program']['territory_id'];?>" />
                    <input type="hidden" name="market_id" value="<?=$this->request->data['Program']['market_id'];?>" />
                    <table class="table table-bordered table-striped outlet_list">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Outlet name</th>
                            <th style="display:none;" class="text-center">Incharge name</th>
                            <th style="display:none;" class="text-center">Owner Name</th>
                            <th class="text-center">Code</th>
                            <th class="text-center">Assigned Date</th>
                        </tr>	
                        <?php		
                        foreach($outlets as $val)
                        {
                            $id = $val['Outlet']['id'];
                            $program_info = $ProgramsController->get_program_info($doctor_id=0, $request_data, $outlet_id=$id, $program_type_id=5);
                        ?>															
                            <tr>
                                <td class="text-center">
                                                                    
                                    <input type="checkbox" class="outlet_id" name="outlet_id[<?=$id;?>]" value="<?=$id;?>" <?=($program_info)?'checked':'';?> />
                                    
                                    <input type="hidden" name="program_id[<?=$id;?>]" value="<?=($program_info)?$program_info['Program']['id']:'';?>" />
                                </td>
                                
                                <td><?php echo $val['Outlet']['name']; ?></td>
                                
                                
                                <td style="display:none;">
                                    <input type="radio" class="member_type_<?=$id;?>" name="member_type[<?=$id;?>]" value="1" <?php if($program_info['Program']['member_type'] == 1){ echo 'checked'; } ?> /> &nbsp;
                                    <?php echo $val['Outlet']['in_charge']; ?>
                                </td>	
                                <td style="display:none;">
                                    <input type="radio" class="member_type_<?=$id;?>" name="member_type[<?=$id;?>]" value="2" <?php if($program_info['Program']['member_type'] == 2){ echo 'checked'; } ?> /> &nbsp;
                                    <?php echo $val['Outlet']['ownar_name']; ?>
                                </td>
                                
                                
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
				
				<?php }else{ ?>
                
                	<table class="table table-bordered table-striped outlet_list">
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">Outlet name</th>
						<th style="display:none;" class="text-center">Incharge name</th>
						<th style="display:none;" class="text-center">Owner Name</th>
						<th class="text-center">Code</th>
						<th class="text-center">Assigned Date</th>
					</tr>	
                    <tr>
                    	<td colspan="5" style="text-align:center;">No Result Found!</td>
                    </tr>
                    </table>
                
                <?php } ?>
                
                
                
                
                
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
	
	
	
	$('.outlet_id').on('ifChanged', function(event){
		var id = $(this).val();
		//alert(id);
		if ($(this).is(":checked")) {	        
	        //$('.member_type_'+id).attr("required", true);
	        //$('.code_'+id).attr("required", true);
	        //$('.assigned_date_'+id).prop("required", true);
	    }
	    else {
	        //$('.member_type_'+id).removeAttr("required");
	        //$('.code_'+id).removeAttr("required");
	        //$('.assigned_date_'+id).prop("required", false);
	    }
	});

});
</script>