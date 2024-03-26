<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sale Target Base Wise'); ?></h3>
			</div>	
			<div class="box-body">
					<?php echo $this->Form->create('SaleTarget', array('role' => 'form')); ?>
					<div class="form-group">
						<?php echo $this->Form->input('aso_id', array('class' => 'form-control','label'=>'Sales Area','empty'=>'---- Select ----','options'=>$saleOffice_list)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$fiscalYears)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('product_id', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$product_options)); ?>
					</div>
					<table class="table table-bordered table-striped">
						<thead>	
							<tr>
								<th class="text-center"><?php echo 'Target QTY(B U)'?></th>
								<th class="text-center"><?php echo 'Target Amount' ?></th>
								<th class="text-center"><?php echo 'Assign QTY(B U)'?></th>
								<th class="text-center"><?php echo 'Assign Amount'?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-left">
									<div class="form-group">
										<?php if(!empty($saletarget['SaleTarget'])){
											echo $this->Form->input('quantity', array('class' => 'form-control sales_target','readonly' => 'readonly','value'=>$saletarget['SaleTarget']['quantity']));
										}else{
											echo $this->Form->input('quantity', array('class' => 'form-control sales_target','readonly' => 'readonly','value'=>'0'));	
										}	
										?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php if(!empty($saletarget['SaleTarget'])){ 
											echo $this->Form->input('amount', array('class' => 'form-control sales_target','readonly' => 'readonly','value'=>$saletarget['SaleTarget']['amount']));
										}else{
											echo $this->Form->input('amount', array('class' => 'form-control sales_target','readonly' => 'readonly','value'=>'0'));	
										}	
										?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('d', array('class' => 'form-control sales_target','label'=>'','value'=>'0'));	?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('e', array('class' => 'form-control sales_target','label'=>'','value'=>'0'));	?>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<br/>
					<br/>
					<table class="table table-bordered table-striped">
						<div class="box-header">
							<div class="box-tools pull-right">
								<?php if($this->App->menu_permission('products','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Set Monthly Target'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
							</div>
						</div>	
						<thead>	
							<tr>
								<th class="text-center"><?php echo 'Area Office' ?></th>
								<th class="text-center"><?php echo 'Base Name' ?></th>
								<th class="text-center"><?php echo 'Active SO Name' ?></th>
								<th class="text-center"><?php echo 'Quantity'?></th>
								<th class="text-center"><?php echo '%'?></th>
								<th class="text-center"><?php echo 'Amount'?></th>
								<th class="text-center"><?php echo '%'?></th>
							</tr>
						</thead>
						<tbody id="data_table">
						<?php 
						if(!empty($saletargets_list)){
						
						$total_amount   = $saletarget['SaleTarget']['amount'];
						$total_quantity = $saletarget['SaleTarget']['quantity'];	
							
						foreach ($saletargets_list as $saletarget): ?>
							<tr>
								<td class="text-left"><?php echo $saletarget['Office']['office_name'] ?></td>
								<td class="text-left"><?php echo $saletarget['Territory']['name']  ?></td>
								<td class="text-left"><?php   ?></td>
								<td class="text-left">
								<?php
									if(!empty($saletarget['SaleTarget'])){
										echo $this->Form->input('quantity', array('class' => 'form-control sales','id'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','name'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','label'=>'','value'=>$saletarget['SaleTarget']['quantity']));	
									}else{
										echo $this->Form->input('quantity', array('class' => 'form-control sales','id'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','name'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','label'=>'','value'=>''));	
									}
								?>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'data[quantity]','readonly' => 'readonly','value'=>($saletarget['SaleTarget']['quantity']*100)/$total_quantity));?>
									</div>
								</td>
								<td class="text-left">
								<?php
									if(!empty($saletarget['SaleTarget'])){
										echo $this->Form->input('amount', array('class' => 'form-control sales','name'=>'data[SaleTarget][amount]['.$saletarget['Territory']['id'].']','label'=>'','value'=>$saletarget['SaleTarget']['amount']));	
									}else{
										echo $this->Form->input('amount', array('class' => 'form-control sales','name'=>'data[SaleTarget][amount]['.$saletarget['Territory']['id'].']','label'=>'','value'=>''));	
									}
								?>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'data[amount]','readonly' => 'readonly','value'=>($saletarget['SaleTarget']['amount']*100)/$total_amount));?>
									</div>
								</td>
							</tr>
						<?php endforeach;
						}
						?>
						</tbody>
					</table>
					<?php echo $this->Form->submit('Save', array('name'=>'save_button','value'=>'save_button','class' => 'btn btn-large btn-primary save','style'=>'margin-top:10px;margin-left:250px;')); ?>
					<?php echo $this->Form->end(); ?>		
			</div>		
		</div>
	</div>
</div>

<script>
    $(document).ready(function() {
		$('.save').hide();
		$("#SaleTargetProductId").change(function() {
			var FiscalYearId = $("#SaleTargetFiscalYearId").val(); 
			var ProductId = $("#SaleTargetProductId").val(); 
			var SaleTargetAsoId = $("#SaleTargetAsoId").val(); 
			$.ajax({
				type: "POST",
				url:  "<?php echo BASE_URL; ?>admin/SaleTargetsBaseWise/sales_base_wise_data",
				data: {FiscalYearId:FiscalYearId, ProductId:ProductId,SaleTargetAsoId:SaleTargetAsoId},
				success: function(response){
					if(response=='[]'){
						$(".sales_target").each(function() {
							$(this).val('0');
						});
					} 
					response = jQuery.parseJSON(response);
					$("input[name='data[SaleTarget][amount]'").val(response.SaleTarget.amount);
					$("input[name='data[SaleTarget][quantity]'").val(response.SaleTarget.quantity);
				}
			});	
		});
	});
	
	$(document).ready(function() {
		$("#SaleTargetProductId").change(function() { 
			var FiscalYearId = $("#SaleTargetFiscalYearId").val(); 
			var ProductId = $("#SaleTargetProductId").val(); 
			var SaleTargetAsoId = $("#SaleTargetAsoId").val();
			$.ajax({
				type: "POST",
				url:  "<?php echo BASE_URL; ?>admin/SaleTargetsBaseWise/get_sales_target_base_wise_data/",
				data: {SaleTargetAsoId:SaleTargetAsoId,ProductId:ProductId,FiscalYearId:FiscalYearId},
				success: function(response){
					
					$('#data_table').html(response)
					$('.save').show();
					
				}
			});	
		});
		
	});
	
</script>

<script>
	$(document).ready(function() {
		
	
		$("body").on("input",".quantity",function(){
			var val = $(this).val();
			var qunatity_id = $(this).attr('id');
			var target_quantity_value = $("#SaleTargetQuantity").val();
			var result_quantity = (100*val)/target_quantity_value;
			$("#quantity_"+qunatity_id).val(result_quantity);
			
		});
		
		/* $("body").on("input",".quantity_parcent",function(){
			var val = $(this).val();
			var qunatity_id = $(this).attr('id');
			var qunatity_parcent_id = qunatity_id.slice(9);
			var target_quantity_value = $("#SaleTargetQuantity").val();
			var result_quantity = (val*target_quantity_value)/100;
			$("#"+qunatity_parcent_id).val(result_quantity);
		}); */
		
		$("body").on("input",".amount",function(){
			var val = $(this).val();
			var amount_id = $(this).attr('id');
			var target_amount_value = $("#SaleTargetAmount").val();
			var result_amount = (val*100)/target_amount_value;
			$("#amount_"+amount_id).val(result_amount);
		});
		
		/* $("body").on("input",".amount_parcent",function(){
			
			var val = $(this).val();
			var amount_id = $(this).attr('id');
			var amount_parcent_id = amount_id.slice(7);
			var target_amount_value = $("#SaleTargetAmount").val();
			var result_amount = (val*target_amount_value)/100;
			$("#"+amount_parcent_id).val(result_amount);
		}); */
		
		
	}); 

</script>
