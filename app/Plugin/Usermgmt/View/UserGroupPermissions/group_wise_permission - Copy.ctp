<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> Group Wise Permission </h3>

			</div>	
			<div class="box-body">
				<?php echo $this->Form->create('UserGroupPermissions', array('role' => 'form','action'=>'groupWisePermission','url' =>array($groupId)) ); ?>
					<table id="Controller" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th style="width:30%" rowspan="2" class="text-center">Menu Name</th>
								<th style="width:70%" colspan="5" class="text-center">Action</th>
							</tr>
							<tr>								
								<th class="text-center">View All</th>
								<th class="text-center">View</th>
								<th class="text-center">Add</th>
								<th class="text-center">Edit</th>
								<th class="text-center">Delete</th>
							</tr>
						</thead>
						<tbody>
					<!--	<input type="hidden" name="user_group_id"  value=<?=$groupId?>>-->
						<?php 
                       
						foreach ($allControllers as $key => $val):?>

							<tr>

								<td class="text-left"><?php
								$cName=$val['controller'];

								if (array_key_exists($cName,$mappingConName))
									{
						            echo $mappingConName[$cName];
									}
									else 
									{
									echo $cName;
									}								
								 ?></td>
								 
								 <td>
								 <?php 
								 if(in_array("admin_index",$val['action']))
								 {
									 		$aval="admin_index";								
											$checkboxVal=$val['controller'].':'.$aval;
											if(isset($allowedPermissions[$checkboxVal]))
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> checked ></label>
											<?php
											}
											else
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> ></label>
											<?php }
								 }								 
								 ?>
								 </td>
								 
								 <td>
								 <?php 
								 if(in_array("admin_view",$val['action']))
								 {
									 		$aval="admin_view";								
											$checkboxVal=$val['controller'].':'.$aval;
											if(isset($allowedPermissions[$checkboxVal]))
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> checked ></label>
											<?php
											}
											else
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> ></label>
											<?php }
								 }								 
								 ?>
								 </td>
								 
								 <td>
								 <?php 
								 if(in_array("admin_add",$val['action']))
								 {
									 		$aval="admin_add";								
											$checkboxVal=$val['controller'].':'.$aval;
											if(isset($allowedPermissions[$checkboxVal]))
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> checked ></label>
											<?php
											}
											else
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> ></label>
											<?php }
								 }								 
								 ?>
								 </td>
								 
								 <td>
								 <?php 
								 if(in_array("admin_edit",$val['action']))
								 {
									 		$aval="admin_edit";								
											$checkboxVal=$val['controller'].':'.$aval;
											if(isset($allowedPermissions[$checkboxVal]))
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> checked ></label>
											<?php
											}
											else
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> ></label>
											<?php }
								 }								 
								 ?>
								 </td>
								 
								 <td>
								 <?php 
								 if(in_array("admin_delete",$val['action']))
								 {
									 		$aval="admin_delete";								
											$checkboxVal=$val['controller'].':'.$aval;
											if(isset($allowedPermissions[$checkboxVal]))
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> checked ></label>
											<?php
											}
											else
											{
											?>
											<label style="width:45%"><input type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> ></label>
											<?php }
								 }	


                                /* passing the others function permission start */	
								  								  
								  $array2=array('admin_index','admin_view','admin_add','admin_edit','admin_delete');								  
								  $other_actions=array_diff($val['action'], $array2);
								  echo "<div style='display:none;'>";
								  foreach ($other_actions as $other_key => $other_val)
								  {
									  
									        $aval=$other_val;								
											$checkboxVal=$val['controller'].':'.$aval;
											if(isset($allowedPermissions[$checkboxVal]))
											{
											?>
											<input style="display: none;" type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> checked >
											<?php
											}
											else
											{
											?>
											<input style="display: none;" type="checkbox" name="check[<?=$checkboxVal;?>]"  value= <?=$checkboxVal;?> >
											<?php }
									  
								  }	

                                   echo "</div>";
								   
                                  /* passing the others function permission end */


								 
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
