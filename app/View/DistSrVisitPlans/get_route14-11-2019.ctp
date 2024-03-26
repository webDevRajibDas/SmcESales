			<table class="search">
			<tr>
				<td width="50%" class="required">
					<?php echo $this->Form->input('effective_date', array('type'=>'text', 'class' => 'form-control datepicker1 effective_date', 'value'=>$effective_date, 'id'=>'effective_date', 'required' => TRUE, 'disabled'=> TRUE)); ?>                        
				</td>
			</tr>
			</table>
			
			<div class="box-body">
				
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
									if($previous_visit_plan[$key]['DistSrVisitPlanDetail']['sat'] == 1){
								?>
								<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Sat]"  value=1 checked></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox" class="checkdata" value=1 name="route[<?=$key;?>][Sat]"  ></label>
								<?php 
									} 
								}
								else
								{
								?>
									<label style="width:45%"><input type="checkbox" class="checkdata" value=1 name="route[<?=$key;?>][Sat]"  ></label>
								<?php
								}
								?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitPlanDetail']['sun'] == 1){
								?>
								<label style="width:45%"><input type="checkbox" class="checkdata"  name="route[<?=$key;?>][Sun]" value=1 checked></label>
								<?php }else {?>
									<label style="width:45%"><input type="checkbox" class="checkdata" value=1 name="route[<?=$key;?>][Sun]" ></label>
								<?php }}else{?>
									<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Sun]"  value=1></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitPlanDetail']['mon'] == 1){
								?>
								<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Mon]"  value=1 checked ></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Mon]"  value=1 ></label>
								<?php }}else{?>
									<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Mon]"  value=1></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitPlanDetail']['tue'] == 1){
								?>
								<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Tue]" value=1 checked  ></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Tue]"  value=1 ></label>
								<?php } }else{?>
									<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Tue]"  value=1></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitPlanDetail']['wed'] == 1){
								?>
								<label style="width:45%"><input type="checkbox" class="checkdata"  name="route[<?=$key;?>][Wed]" value=1 checked ></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox" class="checkdata"  name="route[<?=$key;?>][Wed]"  value=1></label>
								<?php } }else{?>
									<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Wed]"  value=1></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitPlanDetail']['thu'] == 1){
								?>
								<label style="width:45%"><input type="checkbox" class="checkdata"  name="route[<?=$key;?>][Thu]" value=1 checked ></label>
								<?php }else { ?>
									<label style="width:45%"><input type="checkbox" class="checkdata"  name="route[<?=$key;?>][Thu]" value=1 ></label>
								<?php } }else{?>
									<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Thu]"  value=1></label>
								<?php }?>
								</td>
								<td>
								<?php 
								if(array_key_exists($key, $previous_visit_plan)){
									if($previous_visit_plan[$key]['DistSrVisitPlanDetail']['fri'] == 1){
								?>
									<label style="width:45%"><input type="checkbox" class="checkdata"  name="route[<?=$key;?>][Fri]" value=1 checked ></label>
							<?php }else { ?>
									<label style="width:45%"><input type="checkbox" class="checkdata"  name="route[<?=$key;?>][Fri]"  value=1></label>
								<?php } }else{?>
									<label style="width:45%"><input type="checkbox" class="checkdata" name="route[<?=$key;?>][Fri]"  value=1></label>
								<?php }?>
								</td>					 
							</tr>

						<?php endforeach; ?>
						<tr align="center">
							<td colspan="8">

								<input type="submit" value="Save" name="submit" class='btn btn-large btn-primary save'>
							</td>
						</tr>
						</tbody>
					</table>
				<?php } else{?>
					<div class="alert alert-danger" role="alert">
					  <h4 class="text-center"><b><?php echo $msg;?></b></h4>
					</div>
				<?php }?>
				
			</div>			

<script>
    $(document).ready(function () {
    	$('.save').hide();
        $('.datepicker1').datepicker({
            startDate: new Date(),
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
<script>
$(document).ready(function () {
	$("body").on("change", ".checkdata", function () {
    	$('.effective_date').attr('disabled',false);
		$('.save').show();
	});
});
</script>