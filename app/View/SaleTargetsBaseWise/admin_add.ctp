<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sale Target Base Wise'); ?></h3>
					<div class="box-tools pull-right">
						<?php if($this->App->menu_permission('products','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>Base Wise Target List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
					</div>
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
					<div class="form-group">
						
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
										<?php echo $this->Form->input('quantity', array('class' => 'form-control sales_target','label'=>'','value'=>'0'));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('amount', array('class' => 'form-control sales_target','label'=>'','value'=>'0'));	?>
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
						<thead>	
							<tr>
								<th class="text-center"><?php echo 'Base Name' ?></th>
								<th class="text-center"><?php echo 'Active SO Name' ?></th>
								<th class="text-center"><?php echo 'Target Qty'?></th>
								<th class="text-center"><?php echo 'Target Value'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
								<th class="text-center"><?php echo 'Jul QTY'?></th>
							</tr>
						</thead>
						<tbody>
						
							<tr>
								<td class="text-left"><?php  ?></td>
								<td class="text-left"><?php  ?></td>
								<td class="text-left"><?php  ?></td>
								<td class="text-left"><?php  ?></td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
									</div>
								</td>
							</tr>
					
						</tbody>
					</table>
					<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary','style'=>'margin-top:10px;margin-left:250px;')); ?>
					<?php echo $this->Form->end(); ?>		
			</div>		
		</div>
	</div>
</div>

<script>
    $(document).ready(function() {
		$("#SaleTargetProductId").change(function() {
			var FiscalYearId = $("#SaleTargetFiscalYearId").val(); 
			var ProductId = $("#SaleTargetProductId").val(); 
			$.ajax({
				type: "POST",
				url:  "<?php echo BASE_URL; ?>admin/NatioanlSaleTargetsAreaWise/get_national_target_area_wise_data",
				data: {FiscalYearId:FiscalYearId, ProductId:ProductId,},
				success: function(response){
					if(response[0]=='['){
						$(".sales_target").each(function() {
							$(this).val('');
						});
						$(".sales").each(function() {
							$(this).val('');
						});
					}
					response = jQuery.parseJSON(response);
					$("input[name='data[SaleTarget][amount]'").val(response[0].SaleTarget.amount);
					$("input[name='data[SaleTarget][quantity]'").val(response[0].SaleTarget.quantity);
					//alert(response[1][0].SaleTarget.product_id);
					//alert(JSON.stringify(response[1]));
					for (var i = 0; i < response[1].length; i++){
						$("input[name='data[Office][SaleTarget][quantity]["+response[1][i].SaleTarget.aso_id+"]']").val(response[1][i].SaleTarget.quantity);
						$("input[name='data[Office][SaleTarget][amount]["+response[1][i].SaleTarget.aso_id+"]']").val(response[1][i].SaleTarget.amount);
					}
					
				}
			});	
		});
	});
	
</script>


