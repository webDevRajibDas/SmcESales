<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit User Territory'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> User Territory List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('UserTerritoryList', array('role' => 'form')); ?>
            	<div class="form-group">
					<?php //echo $this->Form->input('user_id', array('id'=>'user_id', 'class' => 'form-control','empty'=>'---- Select ----')); ?>
                    <label for="office_id">SPO User :</label>
                    <label style="text-align:left; text-transform:capitalize;" for="office_id"><?=$username?></label>
                    
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('id'=>'office_id', 'class' => 'form-control', 'onChange' => 'getTerritories(0);', 'value'=> $office_id, 'empty'=>'---- Select ----')); ?>
				</div>

                <div class="form-group">
					<label>Territories : </label>
					<?php //echo $this->Form->input('UserTerritoryList.territory_id', array('label'=>false,'div' => array('style'=>'margin-left:23%'), 'multiple' => 'checkbox', 'options' => '','required'=>true)); ?>
                    <div id="TerritoriseList" class="input select" style="margin-left:26%;"></div>
				</div>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>

<script>
$(document).ready(function(){
	
	
	getTerritories(<?php echo $office_id; ?>);
	
	
});

function getTerritories(office_id){
	
	if(office_id==0){
		var office_id = $("#office_id option:selected").val();
	}
	
	//alert(office_id);
	
	var dataString = 'office_id='+ office_id + '&user_id=' + <?php echo $user_id; ?>;
	
	$.ajax ({
			url: '<?= BASE_URL.'user_territory_lists/get_territory_list'?>',
			type: "POST",
			data: dataString,
			//beforeSend: function() {$("#message").html("<img id='checkmark' src='images/loading.gif' />")},
			//complete: function() {$("#confirmWait_"+pro_id).hide()},
			success: function(msg)
			{	
				//alert(msg);
				$('#TerritoriseList').html(msg);
				
			}
		});
}

</script>