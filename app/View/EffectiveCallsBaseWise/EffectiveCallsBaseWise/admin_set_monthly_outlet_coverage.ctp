<style>
	.month-table {
		width: 1200px !important;
	}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sale Target Base Wise'); ?></h3>
					<div class="box-tools pull-right">
						<?php if($this->App->menu_permission('products','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>Base Wise Effective List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
					</div>
			</div>	
			<div class="box-body" style="overflow-x:auto;">
					<?php echo $this->Form->create('SaleTargetMonth', array('role' => 'form')); ?>
					<div class="form-group">
						<?php echo $this->Form->input('aso_id', array('id'=>'aso_id','class' => 'form-control','label'=>'Sales Area','empty'=>'---- Select ----','options'=>$saleOffice_list)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('fiscal_year_id', array('id'=>'fiscal_year_id','class' => 'form-control','empty'=>'---- Select ----','options'=>$fiscalYears)); ?>
					</div>
					<div class="form-group">
						<!--<label>Effective Calls : </label> <strong>0</strong>-->
					</div>
					<br/>
					<br/>
					<table style="overflow-x:auto;" class="table table-bordered table-striped month-table">
						<thead>	
							<tr>
								<th width="120" class="text-center"><?php echo 'Base Name' ?></th>
								<th width="120" class="text-center"><?php echo 'Active SO Name' ?></th>
								<th width="100" class="text-center"><?php echo 'Target Outlet Coverage'?></th>
								<?php foreach($month_list as $key=>$val):?>
								<th width="120" class="text-center">
								<?php echo substr($val, 0, 3)?>
								<input type="hidden" value="<?=$key?>" name=""/>
								</th>
								<?php endforeach;?>
							</tr>
						</thead>
						<tbody class="data_table">
							<!---- start Default month list ----->
							<?php
								if(!empty($saletargets_list)){
								foreach($saletargets_list as $val):
							?>
							<?php
							 
							 ?>
							<tr>
								<td width="100" class="text-left"><?php echo $val['Territory']['name']; ?></td>
								<td width="100" class="text-left"><?php echo $val['SalesPerson']['name']; ?></td>
								<td width="100" class="text-left"><?php echo $val['SaleTarget']['outlet_coverage']; ?></td>
								<?php 
								$counter = 0;
								foreach($month_list as $month_key => $month_val) { ?>
								<td width="100" class="text-left">
									<div class="form-group custom_group">
										<?php
										echo $this->Form->input('outlet_coverage', array('type'=>'number','label'=>false,'class' => 'width_100 monthly_qty','name'=>'data[SaleTargetMonth][outlet_coverage]['.$val['Territory']['id'].']['.$filter_array[$counter]['id'].']','value'=>(isset($val['SaleTargetMonth'][$filter_array[$counter]['id']]['outlet_coverage']))?$val['SaleTargetMonth'][$filter_array[$counter]['id']]['outlet_coverage']:0));
										echo $this->Form->input('hidden_target_id', array('type'=>'hidden','class' => 'form-control sales','name'=>'data[SaleTargetMonth][sale_target_id]['.$val['Territory']['id'].']['.$filter_array[$counter]['id'].']','label'=>'','value'=>(isset($val['SaleTarget']['id']))?$val['SaleTarget']['id']:0));
										?>
									</div>
								</td>
								<?php 
								$counter++;
								} ?>
								
							</tr>
							<?php
								
								endforeach;
							}
							?>
							<!---- end Default month list ----->
							
						</tbody>
					</table>
					<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary','style'=>'margin-top:10px;margin-left:250px;')); ?>
					<?php echo $this->Form->end(); ?>		
			</div>		
		</div>
	</div>
</div>

<script>
	/*-------- Start show territory with saleTarget -------*/
	$(document).ready(function() {
		$("#fiscal_year_id").change(function() { 
			var fiscal_year_id = $("#fiscal_year_id").val(); 
			var aso_id = $("#aso_id").val();
			$.ajax({
				type: "POST",
				url:  "<?php echo BASE_URL; ?>admin/EffectiveCallsBaseWise/month_outlet_coverage_view/",
				data: {fiscal_year_id:fiscal_year_id,aso_id:aso_id},
				success: function(response){
					console.log(response);
					//var obj = jQuery.parseJSON(response);
					$('.data_table').html(response);
				}
			});	
		});
		
	});
	/*-------- End show territory with saleTarget -------*/
</script>


