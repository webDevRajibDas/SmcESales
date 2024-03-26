<style type="text/css">
label{
	margin-top: 0px;
}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			
            
            <div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Memo Setting'); ?></h3>
				<?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('productSettings','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New ProductSetting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */?>
			</div>	
            
            
			<div class="box-body">
				
                <div class="box-body">		
				<?php echo $this->Form->create('MemoSetting', array('role' => 'form')); ?>
					
                    <?php foreach($MemoSettings as $result) { ?>
                    
                    <div class="form-group">
                        
                        <?php if($result['MemoSetting']['value']==1){ ?>
                        
							<?php echo $this->Form->input($result['MemoSetting']['name'], array('class' => 'form-control', 'type' => 'checkbox', 'checked' => 'checked', 'required' => false)); ?>
                            
                        <?php }else{ ?>
                        
                        	<?php echo $this->Form->input($result['MemoSetting']['name'], array('class' => 'form-control', 'type' => 'checkbox', 'required' => false)); ?>
                            
                        <?php } ?>
                        
                    </div>
                    
                    <?php } ?>
                    
                    
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
                </div>
                			
			</div>	
            
            		
		</div>
	</div>
</div>
