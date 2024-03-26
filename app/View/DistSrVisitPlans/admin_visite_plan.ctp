<?php //pr($data);die();
$group_id = $data;
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> App Menu Permission </h3>

			</div>	
			<div class="box-body">
				<?php echo $this->Form->create('DistAppUserGroups', array('role' => 'form','action'=>'groupPermission') ); ?>

				<div class="form-group">
				<?php echo $this->Form->input('office_id', array('class' => 'form-control office_id', 'id' => 'office_id', 'empty'=> '--- select ----', 'options'=> $offices )); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('distributor_id', array('class' => 'form-control distributor_id', 'id'=>'distributor_id')); ?>
				</div>
				<!-- <div class="form-group">
					<?php //echo $this->Form->input('user_group_id', array('class' => 'form-control user_group_id', 'id'=>'user_group_id','type'=>'hidden', 'value'=>$group_id)); ?>
				</div> -->
					<table id="Controller" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th style="width:30%" rowspan="2" class="text-center">Menu Name</th>
								<th style="width:70%" colspan="5" class="text-center">Action</th>
							</tr>
							<tr>								
								<th class="text-center">Is Permited</th>
								
							</tr>
						</thead>
						<tbody>
						<input type="hidden" name="app_user_group_id"  value=<?=$group_id?>>
						<?php 
						foreach ($allControllers as $key => $val):
						
						?>

							<tr>

								<td style="padding-left:30px;" class="text-left"><?php
								$cName=$val;

									echo $cName;
								 ?>
								 	
								 </td>
								 
								 <td>
								 <?php 
								 						
									$checkboxVal=$val;
									if(array_key_exists($val,$previous_menus))
									{
									?>
									<label style="width:45%"><input type="checkbox" name="menus[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> checked ></label>
									<?php
									}
									else
									{
									?>
									<label style="width:45%"><input type="checkbox" name="menus[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> ></label>
									<?php }
													 
								 ?>
								 </td>
								 
															 
							</tr>

						<?php endforeach; ?>
						<tr align="center">
							<td colspan="6">

								<input type="submit" value="Set Permission" name="submit" class='btn btn-large btn-primary'>
							</td>
						</tr>
						</tbody>
					</table>
				<?php echo $this->Form->end(); ?>
			</div>			
		</div>
	</div>
</div>
<script>
	$('.office_id').selectChain({
	target: $('.distributor_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/dist_app_user_groups/get_distributer_id';?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});
</script>