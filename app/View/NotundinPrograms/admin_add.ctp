<?php
App::import('Controller', 'NotundinProgramsController');
$NotundinProgramsController = new NotundinProgramsController;					 
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
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add/Edit Notundin Program'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Notundin Program List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                    
				</div>
			</div>
            
            
			<div class="box-body">		
				                
				<?php				
				if(!empty($institutes))
				{	
				?>
				<?php echo $this->Form->create('NotundinProgram', array('role' => 'form','action'=>'add_list')); ?>
                
                <div class="submit pull-right" style="width:auto; padding-bottom:10px;">
                <?php echo $this->Form->button('Save', array('class' => 'btn btn-large btn-primary')); ?>
                </div>
                
				<input type="hidden" name="user_id" value="<?=$user_id;?>" />
				<table class="table table-bordered table-striped outlet_list">
					<tr>
						<th class="text-center"></th>
						<th class="text-left">Institute name</th>
						<th class="text-center">Assigned Date</th>
					</tr>	
					<?php		
					//pr($outlets);	
					foreach($institutes as $val)
					{
						
						$id = $val['Institute']['id'];
						$program_info = $NotundinProgramsController->get_program_info($institute_id=$id);
					?>															
                        <tr>
                            <td class="text-center">
                                                                
                                <input type="checkbox" class="institute_id" name="institute_id[<?=$id;?>]" value="<?=$id;?>" <?=($program_info)?'checked':'';?> />
                                
                                <input type="hidden" name="program_id[<?=$id;?>]" value="<?=($program_info)?$program_info['NotundinProgram']['id']:'';?>" />
                            </td>
                            
                            <td><?php echo $val['Institute']['name']; ?></td>
                                                        
                            
                            <td>
                                <input type="text" class="form-control assigned_date_<?=$id;?> datepicker" name="assigned_date[<?=$id;?>]" value="<?=($program_info)?date('d-m-Y',strtotime($program_info['NotundinProgram']['assigned_date'])):'';?>"/>

                            </td>						
                        </tr>
					<?php
						
					}
					?>				
				</table>				
				</br>
                <div class="submit" style="margin-left:40%;">
				<?php echo $this->Form->button('Save', array('class' => 'btn btn-large btn-primary')); ?>
                </div>
				<?php echo $this->Form->end(); ?>	
				<?php	
				}
				?>                
                
			</div>
            
		</div>			
	</div>
</div>



