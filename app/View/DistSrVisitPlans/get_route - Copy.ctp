<?php 
/*if(array_key_exists(8, $previous_visit_plan)){
	pr($previous_visit_plan[8]);die();
}*/
//$group_id = $data;

?>

			<div class="box-body">
				<?php echo $this->Form->create('DistSrVisitPlans', array('role' => 'form','action'=>'sr_visit_plan') ); ?>

				<?php  echo $this->Form->input('office_id', array('type' => 'hidden', 'class' => 'form-control office_id', 'value' => $office_id));

					echo $this->Form->input('distributor_id', array('type' => 'hidden', 'class' => 'form-control distributor_id', 'value' => $distributor_id));

					echo $this->Form->input('sr_id', array('type' => 'hidden', 'class' => 'form-control sr_id', 'value' => $sr_id));
				?>
				<?php if(!empty($routes)){?>
					<table id="Controller" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th style="width:30%" rowspan="2" class="text-center">Route Name</th>
								<th style="width:70%" colspan="7" class="text-center">Week Days</th>
							</tr>
							<tr>								
								<th class="text-center">Saturday</th>
								<th class="text-center">Sunday</th>
								<th class="text-center">Monday</th>
								<th class="text-center">Tuesday</th>
								<th class="text-center">Wednessday</th>
								<th class="text-center">Thursday</th>
								<th class="text-center">Friday</th>
								
							</tr>
						</thead>
						<tbody>
						
						<?php 
						foreach ($routes as $key => $val):
						
						?>
							<tr>
								<td style="padding-left:30px;" class="text-left"><?php echo $val;?></td>
								 
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitePlan']['sat'] == 1){
								?>
								<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Sat]"  value=1 checked></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Sat]"  ></label>
								<?php } }else{?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Sat]"  ></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitePlan']['sun'] == 1){
								?>
								<label style="width:45%"><input type="checkbox"  name="route[<?=$key;?>][Sun]" value=1 checked></label>
								<?php }else {?>
									<label style="width:45%"><input type="checkbox"  name="route[<?=$key;?>][Sun]" ></label>
								<?php }}else{?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Sun]"  ></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitePlan']['mon'] == 1){
								?>
								<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Mon]"  value=1 checked ></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Mon]"   ></label>
								<?php }}else{?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Mon]"  ></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitePlan']['tue'] == 1){
								?>
								<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Tue]" value=1 checked  ></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Tue]"   ></label>
								<?php } }else{?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Tue]"  ></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitePlan']['wed'] == 1){
								?>
								<label style="width:45%"><input type="checkbox"  name="route[<?=$key;?>][Wed]" value=1 checked ></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox"  name="route[<?=$key;?>][Wed]"  ></label>
								<?php } }else{?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Wed]"  ></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitePlan']['thu'] == 1){
								?>
								<label style="width:45%"><input type="checkbox"  name="route[<?=$key;?>][Thu]" value=1 checked ></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox"  name="route[<?=$key;?>][Thu]"  ></label>
								<?php } }else{?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Thu]"  ></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitePlan']['fri'] == 1){
								?>
									<label style="width:45%"><input type="checkbox"  name="route[<?=$key;?>][Fri]" value=1 checked ></label>
							<?php }else { ?>
									<label style="width:45%"><input type="checkbox"  name="route[<?=$key;?>][Fri]"  ></label>
								<?php } }else{?>
									<label style="width:45%"><input type="checkbox" name="route[<?=$key;?>][Fri]"  ></label>
								<?php }?>
								</td>					 
							</tr>

						<?php endforeach; ?>
						<tr align="center">
							<td colspan="8">

								<input type="submit" value="Save" name="submit" class='btn btn-large btn-primary'>
							</td>
						</tr>
						</tbody>
					</table>
				<?php } else{?>
					<div class="alert alert-danger" role="alert">
					  <h4 class="text-center"><b><?php echo $msg;?></b></h4>
					</div>
					<!-- <div class="box box-primary">
						<div class="box-body">
							<h4 class="text-center" style="color: red"><b><?php //echo $msg;?></b></h4>
						</div>
					</div> -->
				<?php }?>
				<?php echo $this->Form->end(); ?>
			</div>			
		