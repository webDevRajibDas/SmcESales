<style>
	.sales{
		width:60%;
	}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('National Sale Targets'); ?></h3>
			</div>	
			<div class="box-body">
					<?php echo $this->Form->create('SaleTarget', array('role' => 'form')); ?>
					<div class="form-group">
						<?php 
						echo $this->Form->input('is_submit', array('name'=>'is_submit','value'=>'YES','type'=>'hidden'));
						echo $this->Form->input('fiscal_year_id', array('class' => 'form-control','name'=>'data[SaleTarget][fiscal_year_id]','empty'=>'---- Select ----','options'=>$fiscalYears,'default'=>$current_year_code)); ?>
					</div>
				<div id="tbodys">
					<table class="table table-bordered table-striped">
						<thead>	
							<tr>
								<th class="text-center"><?php echo 'Product Code'?></th>
								<th class="text-center"><?php echo 'product Name' ?></th>
								<th class="text-center"><?php echo 'Unit' ?></th>
								<th class="text-center"><?php echo 'Qty'?></th>
								<th class="text-center"><?php echo 'Amount'?></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($products as $product): 
						
						//echo '<pre>';
						//print_r($product['SaleTarget']);
						?>
							<tr>
								<td class="text-left"><?php echo h($product['Product']['product_code']); ?></td>
								<td class="text-left"><?php echo h($product['Product']['name']); ?></td>
								<td class="text-left"><?php echo h($product['BaseMeasurementUnit']['name']); ?></td>
								<td class="text-left">
									<div class="form-group">
										<?php if(!empty($product['SaleTarget'])) 
										{	
											echo $this->Form->input('quantity', array('class' => 'form-control sales','name'=>'data[SaleTarget][quantity]['.$product['Product']['id'].']','label'=>'','value'=>$product['SaleTarget']['quantity']));
										}
										else
										{
											echo $this->Form->input('quantity', array('class' => 'form-control sales','name'=>'data[SaleTarget][quantity]['.$product['Product']['id'].']','label'=>'','value'=>0));	
										}
										?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php
										if(!empty($product['SaleTarget']))
											{ 
												echo $this->Form->input('amount', array('class' => 'form-control sales','name'=>'data[SaleTarget][amount]['.$product['Product']['id'].']','label'=>'','value'=>$product['SaleTarget']['amount']));
											}else
											{
												echo $this->Form->input('amount', array('class' => 'form-control sales','name'=>'data[SaleTarget][amount]['.$product['Product']['id'].']','label'=>'','value'=>0));	
											}	
										
										?>
									</div>
								</td>
								<?php echo  $this->Form->input('sale_target_id', array('class' => 'form-control','type' => 'hidden','name'=>'data[SaleTarget][id]['.$product['Product']['id'].']','value'=>(isset($product['SaleTarget']['id']))?$product['SaleTarget']['id']:''));
										//echo  $this->Form->input('', array('class' => 'form-control','type' => 'text','name'=>'data[SaleTarget1][id1]['.$product['Product']['id'].']','value'=>'data[SaleTarget][id1]'));
											?>
								
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary','style'=>'margin-top:10px;margin-left:250px;')); ?>
					<?php echo $this->Form->end(); ?>
				</div>		
			</div>		
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
		$("#SaleTargetFiscalYearId").change(function() {
			var FiscalYearId = $(this).val(); 
			$.ajax({
				type: "POST",
				url:  "<?php echo BASE_URL; ?>admin/SaleTargets/get_national_sales_data",
				data: "FiscalYearId="+FiscalYearId,
				success: function(response){
					if(response =='[]')
					{
						$(".sales").each(function() {
							$(this).val('0');
						});
					}
					else
					{
						response = jQuery.parseJSON(response);
						if(response.length != 'undefined')
						for (var i = 0; i < response.length; i++){
							$("input[name='data[SaleTarget][id]["+response[i].SaleTarget.product_id+"]']").val(response[i].SaleTarget.id);
							$("input[name='data[SaleTarget][quantity]["+response[i].SaleTarget.product_id+"]']").val(response[i].SaleTarget.quantity);
							$("input[name='data[SaleTarget][amount]["+response[i].SaleTarget.product_id+"]']").val(response[i].SaleTarget.amount);
						}
					}
				}
			});	
		});
	});
</script>

