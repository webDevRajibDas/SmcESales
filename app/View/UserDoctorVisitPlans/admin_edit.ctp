<?php
	//pr($this->request->data);exit;
	// pr($list_doctor);exit;
?>

<style>

	#market_list .checkbox label{
		padding-left:10px;
		width:auto;
	}
	#market_list .checkbox{
		width:33%;
		float:left;
		margin:1px 0;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Admin Add User Doctor Visit Plan'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> User Doctor Visit Plan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<?php echo $this->Form->create('UserDoctorVisitPlan', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('user_id', array('label'=>'SPO User', 'id'=>'user_id', 'class' => 'form-control user_id', 'empty' => '---- Select SPO ----', 'options' => $users)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'class' => 'form-control territory_id', 'empty' => '---- Select ----', 'options' => $territories)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('market_id', array('id'=>'market_id', 'class' => 'form-control market_id','empty' => '---- Select ----','options' => '')); ?>
				</div>
				<div class="form-group">
					<label>Doctors : </label>
					<label class="pull-right">Total Selected Doctor : <span class="total_selected_doctor"></span></label><br>
					<div id="market_list" style="margin-left:23%">
						<?php $selected_doctor=array_keys($list_doctor);
						echo $this->form->input('doctor_id', array('label'=>false,'class'=>'checkbox doctor_id_click', 'multiple' => 'checkbox', 'options' => $list_doctor, 'selected' => $selected_doctor, 'required'=>true));
						?>
						<?php //echo $this->Form->input('market_id', array('label'=>false,'multiple' => 'checkbox', 'options' => $markets,'required'=>true)); ?>
					</div>
				</div>	
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary submit')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>

<script>
	$(document).ready(function () {
		var user_id=<?=$this->request->data['UserDoctorVisitPlan']['user_id'];?>;
		user_id_fixed();
		var checked_doctor=new Array();
		var i=0;
		<?php foreach($list_doctor as $key=>$value){?>
			checked_doctor[i++]="<?=$key?>";
			<?php }?>
			var count = checked_doctor.filter(function() { return true; }).length;
			$('.total_selected_doctor').text(count);
			$('body').on('click','.doctor_id_click',function(){
				var checkbox=$(this).children();
				var doctor_id=checkbox.val();
				if(checkbox.is(':checked'))
				{
					if(($.inArray(doctor_id,checked_doctor))==-1){
						checked_doctor[i++]=doctor_id;
					}
				}
				else
				{
					var index=$.inArray(doctor_id,checked_doctor);
					if(index > -1)
					{
						checked_doctor.splice(index,1);
					}

				}


		// console.log(checked_doctor);
		var count = checked_doctor.filter(function() { return true; }).length;
		$('.total_selected_doctor').text(count);
		
	});
			$('input').on('ifChecked', function(event){
				var doctor_id=$(this).val();
				if(($.inArray(doctor_id,checked_doctor))==-1){
					checked_doctor[i++]=doctor_id;
				}
				var count = checked_doctor.filter(function() { return true; }).length;
				$('.total_selected_doctor').text(count);
			});

			$('input').on('ifUnchecked', function(event){
				var doctor_id=$(this).val();
				var index=$.inArray(doctor_id,checked_doctor);
				if(index > -1)
				{
					checked_doctor.splice(index,1);
				}
				var count = checked_doctor.filter(function() { return true; }).length;
				$('.total_selected_doctor').text(count);
			});


			$('#user_id').selectChain({
				target: $('#territory_id'),
				value:'name',
				url: '<?= BASE_URL.'UserDoctorVisitPlans/get_spo_territory_list'?>',
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
				url: '<?= BASE_URL.'UserDoctorVisitPlans/get_market_list'?>',
				type: 'post',
				data:{'territory_id': 'territory_id'}
			});
			$('body').on('change','#territory_id,#market_id',function(){
				/*console.log('hie');*/
				getDoctorList();
			});


			function getDoctorList(){
	// console.log('hi');
	var territory_id = $('#territory_id').val();	
	var market_id = $('#market_id').val();
	// console.log(market_id);
	var selected_doctor = JSON.stringify(checked_doctor);
	console.log(selected_doctor);
	//alert(territory_id);
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL;?>admin/user_doctor_visit_plans/get_doctor_list',
		data: 'territory_id='+territory_id + '&market_id=' + market_id+'&selected_doctor='+selected_doctor,
		cache: false, 
		success: function(response){
			//alert(response);						
			$('#market_list').html(response);				
		}
	});		
}
function user_id_fixed()
{
	$('#user_id').val(user_id);
	$('#user_id').attr('disabled','true');
}
$(".submit").click(function(e){
	e.preventDefault();
	$('.selected_doctor_remove').remove();
	console.log(checked_doctor);
	var selectd_checked_doctor=checked_doctor.filter(function() { return true; });
	console.log(selectd_checked_doctor);
	$.each(selectd_checked_doctor,function(key,value){
		var input="<input name='selected_doctor[]' class='selected_doctor_remove' type='hidden' value='"+value+"'>";
		$('#market_list').append(input);
	});
	$("#UserDoctorVisitPlanAdminEditForm").submit();
});
});
</script>