<?php 
$sr_id = $this->request->data['SalesPerson']['dist_sales_representative_id'];
?>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit User'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> User List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('DmsUser'); ?>
				<?php echo $this->Form->input("id" ,array('type' => 'hidden', 'label' => false,'div' => false))?>
				<?php echo $this->Form->input('sales_person_id', array('type'=>'hidden')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('SalesPerson.office_id', array('class' => 'form-control','empty'=>'---- Select ----','id'=>'office_id')); ?>
				</div>
				<div class="form-group" id="db_list">
					<?php echo $this->Form->input('dist_distributor_id', array('class' => 'form-control dist_distributor_id','empty'=>'---- Select ----','id'=>'dist_distributor_id','options'=>$distributor_list, 'selected'=>$distributor_id)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input("user_group_id", array('class' => 'form-control','empty'=>'---- Select Group ----','id'=>'user_group_id')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('username', array('label' => 'Username (Use employee ID here) :', 'class' => 'form-control username')); ?>
					<div style="margin-top:-15px;padding-left:10px;" id="mgs"></div>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('password', array('class' => 'form-control','type' => 'password')); ?>
				</div>
				<div class="form-group">
					<label for="UserPassword">Confirm Password :</label>
					<?php echo $this->Form->input('cpassword', array('class' => 'form-control','type'=>'password','label'=>false)); ?>
				</div>
				<?php if($sr_data){?>
				 <div class="form-group" id="sr_list">
					<?php echo $this->Form->input('SalesPerson.dist_sales_representative_id', array('class' => 'form-control sr_id', 'id'=>'sr_id', 'options'=>$sr_data, 'selected'=>$sr_id)); ?>
				</div> 
				<?php }?>
				<div class="form-group">
					<?php echo $this->Form->input('SalesPerson.name', array('class' => 'form-control', 'id'=>"name")); ?>
				</div>
                <div class="form-group">
					<?php echo $this->Form->input('SalesPerson.contact', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('SalesPerson.designation_id', array('class' => 'form-control','empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('User.dashboard', array('class' => 'form-control','id'=>'office_id','options'=>array('1'=>'Graphical Dashboard','2'=>'Normal Dashboard'))); ?>
				</div>
				<div class="form-group">
					<label for="UserActive">Is Active :</label>
					<?php echo $this->Form->input('active', array('class' => 'form-control','type'=>'checkbox','label'=>false)); ?>
				</div>
				<?php echo $this->Form->submit('Update User', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
		</div>			
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){    
	$(".username").keyup(function(){  
		var user_id = '<?php echo $this->request->data['User']['id'];?>';
		var username = $(this).val();   
		if(username.length > 3)
		{  
			$("#mgs").html(' Checking...');   
			$.ajax({    
			type : 'POST',
			url  : '<?php echo BASE_URL;?>admin/duplicate_user_check',
			data : 'user_id='+user_id+'&username='+username,
			success : function(data){
					$("#mgs").html(data);
				}
			});
			return false;   
		}
		else
		{
			$("#mgs").html('');
		}
	});
		
	$(".code").keyup(function(){  
		var id = '<?php echo $this->request->data['User']['sales_person_id'];?>';		
		var code = $(this).val();   
		if(code.length > 2)
		{  
			$("#codemgs").html(' Checking...');   
			$.ajax({    
			type : 'POST',
			url  : '<?php echo BASE_URL;?>admin/duplicate_usercode_check',
			data : 'id='+id+'&code='+code,
			success : function(data){
					$("#codemgs").html(data);
				}
			});
			return false;   
		}
		else
		{
			$("#codemgs").html('');
		}
	});
	//$('#sr_list').hide();   
	$('#user_group_id').change(function(){
		$('#sr_list').show(); 
		var office_id = $("#office_id").val(); 
		var user_group = $("#user_group_id").val();
		if(user_group == 1032){
			//alert(user_group);
			$.ajax({    
			type : 'POST',
			url: '<?= BASE_URL.'admin/territories/getOfficeSrData'?>',
			data : {office_id: office_id,user_group:user_group},
			success : function(data){
					var json = $.parseJSON(data);
				
					console.log(json);
					var data = '<option value="">-- Select---</option>';
	                for (var i=0;i<json.length;++i)
	                {
	                    data = data+'<option value="'+json[i].id+'">'+json[i].name+'</option>';
	                }
	                $('.sr_id').html(data);
				}
			});
		}
		else{
			$('#sr_list').hide();
			$('#name').val('');
			$('#name').prop('readonly',false); 
		}
		if(user_group == 1034){
			$('#db_list').show();
			$.ajax({    
			type : 'POST',
			url: '<?= BASE_URL.'admin/territories/get_distributor_list_for_db_user'?>',
			data : {office_id: office_id},
			success : function(data){
				console.log(data);
				var json = $.parseJSON(data);
				
	                for (var i=0;i<json.length;++i)
	                {
	                    data = data+'<option value="'+json[i].id+'">'+json[i].name+'</option>';
	                }
	                
	                $('#dist_distributor_id').html(data);
				}
			});
		}else{
			$('#db_list').hide();
		}
		
		
	});
	$('#sr_id').change(function(){
		var name = $('#sr_id option:selected').text();
		var res = name.split("(");
		$('#name').val(res[0]);
		$('#name').prop('readonly',true);
	}); 	
});
</script>
