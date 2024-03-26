<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
        
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Visit Plan'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Visit Plan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
            
			<div class="box-body">		
				<?php echo $this->Form->create('UserDoctorVisitPlanList', array('role' => 'form')); ?>
                
                    <div class="form-group">
                        <?php echo $this->Form->input('user_id', array('label'=>'SPO User', 'id'=>'user_id', 'class' => 'form-control user_id', 'empty' => '---- Select SPO ----', 'options' => $users)); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('visit_plan_date', array('type'=>'text', 'class' => 'form-control datepicker')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'onChange'=>'getDoctorList();', 'class' => 'form-control territory_id', 'empty' => '---- Select ----', 'options' => '')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('market_id', array('id'=>'market_id', 'onChange'=>'getDoctorList();', 'class' => 'form-control market_id','empty' => '---- Select ----','options' => '')); ?>
                    </div>	
                    
                    <div class="form-group">
                        <label>Doctors : </label>
                        <div id="market_list" style="margin-left:23%">
                        <?php //echo $this->Form->input('market_id', array('label'=>false,'multiple' => 'checkbox', 'options' => $markets,'required'=>true)); ?>
                        </div>
                    </div>	
                                
                    <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                
				<?php echo $this->Form->end(); ?>
			</div>
            
		</div>			
	</div>
</div>

<script>
$(document).ready(function () {
	
	$('#user_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'UserDoctorVisitPlanLists/get_spo_territory_list'?>',
		type: 'post',
		data:{'user_id': 'user_id'},
		/*before: function () {
		// do something
		alert(1111);
		},
		success:function(msg){
			alert(111);
		}*/
	});
	
	$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'UserDoctorVisitPlanLists/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id'}
	});
	
});
</script>

<script>
function getDoctorList(){
	var territory_id = $('#territory_id').val();	
	var market_id = $('#market_id').val();	
	//alert(territory_id);
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL;?>admin/user_doctor_visit_plan_lists/get_doctor_list',
		data: 'territory_id='+territory_id + '&market_id=' + market_id,
		cache: false, 
		success: function(response){
			//alert(response);						
			$('#market_list').html(response);				
		}
	});		
};
</script>