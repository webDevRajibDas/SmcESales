<style>
	.draft{
		padding: 0px 15px;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Return Challan Details'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Return Challan  List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>

			<?php echo $this->Form->create('DistReturnChallan', array('role' => 'form')); ?>
			<div class="box-body">
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td width="25%"><strong><?php echo 'DistReturnChallan No.'; ?></strong></td>
							<td><?php echo h($challan['DistReturnChallan']['challan_no']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Distributor (Sender Store)'; ?></strong></td>
							<td><?php echo h($challan['DistStore']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'DistReturnChallan Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat($challan['DistReturnChallan']['challan_date']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Receiver Store'; ?></strong></td>
							<td><?php echo @$challan['ReceiverStore']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Received Date'; ?></strong></td>
							<td>
								<?php 
								if($challan['DistReturnChallan']['status'] == 1 AND $office_parent_id !=0){
									echo $this->Form->input('received_date', array('type'=>'text','label'=>false, 'class' => 'form-control datepicker_range','required'=>true)); 
								}else{
									echo $this->App->dateformat(($challan['DistReturnChallan']['received_date'])); 
								}
								?>
							</td>
						</tr>
						<tr>
							<td><strong><?php echo 'Status'; ?></strong></td>
							<td>
								<?php
									if ($challan['DistReturnChallan']['status'] == 1) {
										echo '<span class="btn btn-warning btn-xs">Pending</span>';
									}elseif($challan['DistReturnChallan']['status'] == 2) {
										echo '<span class="btn btn-success btn-xs">Received</span>';
									}else{
										echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
									}
								?>
							</td>
						</tr>
						<?php /*?><tr>
							<td><strong><?php echo 'Remarks'; ?></strong></td>
							<td><?php echo h($challan['DistReturnChallan']['remarks']); ?></td>
						</tr><?php */?>
				</table>
			</div>
			<div class="box-body">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th class="text-center">SL.</th>
							<th class="text-center">Product Name</th>
							<th class="text-center">Batch No.</th>
							<th class="text-center">Expire Date</th>
							<th class="text-center">Unit</th>
							<th class="text-center">Quantity</th>
							<th class="text-right">Value</th>
							<th class="text-center">Remarks</th>
						</tr>
						
						
						<tr><td colspan="8"><b>Sellable Products</b></td></tr>
						<?php
						if(!empty($challandetail)){
							$sl = 1;
							$total_quantity = 0;
							$total_received_quantity = 0;
							foreach($challandetail as $val){

								if( $val['DistReturnChallanDetail']['virtual_product_name_show'] > 0){
									$val['Product']['name'] = $val['VirtualProduct']['name'];
								}
								if( $val['DistReturnChallanDetail']['virtual_product_id'] > 0 ){
									$val['DistReturnChallanDetail']['product_id'] = $val['DistReturnChallanDetail']['virtual_product_id'];
								}

								if($val['DistReturnChallanDetail']['unit_price']>0 && $val['DistReturnChallanDetail']['is_ncp']==0){
								?>
									<tr>
										<td align="center"><?php echo $sl; ?></td>
										<td><?php echo $val['Product']['name']; ?></td>
										<td align="center"><?php echo $val['DistReturnChallanDetail']['batch_no']; ?></td>
										<td align="center"><?php echo $this->App->expire_dateformat($val['DistReturnChallanDetail']['expire_date']); ?></td>
										<td><?php echo $val['MeasurementUnit']['name']; ?></td>
										<td align="center"><?php echo $val['DistReturnChallanDetail']['challan_qty']<=0?'0.00':$val['DistReturnChallanDetail']['challan_qty']; ?></td>
										<td align="right">
										
										<input type="hidden" name="id[]" value="<?php echo $val['DistReturnChallanDetail']['id']; ?>"/>
										<input type="hidden" name="product_id[]" value="<?php echo $val['DistReturnChallanDetail']['product_id']; ?>"/>
										<input type="hidden" name="measurement_unit_id[]" value="<?php echo $val['DistReturnChallanDetail']['measurement_unit_id']; ?>"/>
										<input type="hidden" name="quantity[]" value="<?php echo $val['DistReturnChallanDetail']['challan_qty']; ?>"/>
										<input type="hidden" name="batch_no[]" value="<?php echo $val['DistReturnChallanDetail']['batch_no']; ?>"/>
										<input type="hidden" name="expire_date[]" value="<?php echo $val['DistReturnChallanDetail']['expire_date']; ?>"/>
										<input type="hidden" name="inventory_status_id[]" value="<?php echo $challan['DistReturnChallan']['inventory_status_id']; ?>"/>
										<input type="hidden" name="SenderStore_id[]" value="<?php echo $challan['DistReturnChallan']['sender_store_id']; ?>"/>
										<input type="hidden" name="is_ncp[]" value="<?php echo $val['DistReturnChallanDetail']['is_ncp']; ?>"/>
										
										<?php echo $val['DistReturnChallanDetail']['challan_qty']*$val['DistReturnChallanDetail']['unit_price']; ?>
										</td>
										<td><?php echo $val['DistReturnChallanDetail']['remarks']; ?></td>
									</tr>
									<?php
									$total_quantity = $total_quantity + $val['DistReturnChallanDetail']['challan_qty'];
								
									$sl++;
								}
							}
						}
						?>	
						
						
						<tr><td colspan="8"><b>Bonus Products</b></td></tr>
						<?php
						if(!empty($challandetail))
						{
							$sl = 1;
							$total_quantity = 0;
							$total_received_quantity = 0;
							foreach($challandetail as $val)
							{
								if( $val['DistReturnChallanDetail']['virtual_product_name_show'] > 0){
									$val['Product']['name'] = $val['VirtualProduct']['name'];
								}

								if($val['DistReturnChallanDetail']['unit_price']==0 && $val['DistReturnChallanDetail']['is_ncp']==0)
								{
						?>
								<tr>		
									<td align="center"><?php echo $sl; ?></td>
									<td><?php echo $val['Product']['name']; ?></td>
									<td align="center"><?php echo $val['DistReturnChallanDetail']['batch_no']; ?></td>
									<td align="center"><?php echo $this->App->expire_dateformat($val['DistReturnChallanDetail']['expire_date']); ?></td>
									<td><?php echo $val['MeasurementUnit']['name']; ?></td>
									<td align="center">
									
									<?php echo $val['DistReturnChallanDetail']['challan_qty']<=0?'0.00':$val['DistReturnChallanDetail']['challan_qty']; ?>
									
									<input type="hidden" name="id[]" value="<?php echo $val['DistReturnChallanDetail']['id']; ?>"/>
									<input type="hidden" name="product_id[]" value="<?php echo $val['DistReturnChallanDetail']['product_id']; ?>"/>
									<input type="hidden" name="measurement_unit_id[]" value="<?php echo $val['DistReturnChallanDetail']['measurement_unit_id']; ?>"/>
									<input type="hidden" name="quantity[]" value="<?php echo $val['DistReturnChallanDetail']['challan_qty']; ?>"/>
									<input type="hidden" name="batch_no[]" value="<?php echo $val['DistReturnChallanDetail']['batch_no']; ?>"/>
									<input type="hidden" name="expire_date[]" value="<?php echo $val['DistReturnChallanDetail']['expire_date']; ?>"/>
									<input type="hidden" name="inventory_status_id[]" value="<?php echo $challan['DistReturnChallan']['inventory_status_id']; ?>"/>
									<input type="hidden" name="SenderStore_id[]" value="<?php echo $challan['DistReturnChallan']['sender_store_id']; ?>"/>
									<input type="hidden" name="is_ncp[]" value="<?php echo $val['DistReturnChallanDetail']['is_ncp']; ?>"/>
									
									</td>
									
									<td align="right"><?php echo $val['DistReturnChallanDetail']['challan_qty']*$val['DistReturnChallanDetail']['unit_price']; ?>
									
									</td>
									
									
									
									<td><?php echo $val['DistReturnChallanDetail']['remarks']; ?></td>
								</tr>
								<?php
								$total_quantity = $total_quantity + $val['DistReturnChallanDetail']['challan_qty'];
							
								$sl++;
								}
							}
						}
						?>
						
						<tr><td colspan="8"><b>NCP Products</b></td></tr>
						<?php
						if(!empty($challandetail))
						{
							$sl = 1;
							$total_quantity = 0;
							$total_received_quantity = 0;
							foreach($challandetail as $val)
							{
								if( $val['DistReturnChallanDetail']['virtual_product_name_show'] > 0){
									$val['Product']['name'] = $val['VirtualProduct']['name'];
								}
								
								if($val['DistReturnChallanDetail']['is_ncp']==1)
								{
						?>
								<tr>		
									<td align="center"><?php echo $sl; ?></td>
									<td><?php echo $val['Product']['name']; ?></td>
									<td align="center"><?php echo $val['DistReturnChallanDetail']['batch_no']; ?></td>
									<td align="center"><?php echo $this->App->expire_dateformat($val['DistReturnChallanDetail']['expire_date']); ?></td>
									<td><?php echo $val['MeasurementUnit']['name']; ?></td>
									<td align="center"><?php echo $val['DistReturnChallanDetail']['challan_qty']<=0?'0.00':$val['DistReturnChallanDetail']['challan_qty']; ?></td>
									<td align="right">
									<?php echo $val['DistReturnChallanDetail']['challan_qty']*$val['DistReturnChallanDetail']['unit_price']; ?>
									<input type="hidden" name="id[]" value="<?php echo $val['DistReturnChallanDetail']['id']; ?>"/>
									<input type="hidden" name="product_id[]" value="<?php echo $val['DistReturnChallanDetail']['product_id']; ?>"/>
									<input type="hidden" name="measurement_unit_id[]" value="<?php echo $val['DistReturnChallanDetail']['measurement_unit_id']; ?>"/>
									<input type="hidden" name="quantity[]" value="<?php echo $val['DistReturnChallanDetail']['challan_qty']; ?>"/>
									<input type="hidden" name="batch_no[]" value="<?php echo $val['DistReturnChallanDetail']['batch_no']; ?>"/>
									<input type="hidden" name="expire_date[]" value="<?php echo $val['DistReturnChallanDetail']['expire_date']; ?>"/>
									<input type="hidden" name="inventory_status_id[]" value="<?php echo $challan['DistReturnChallan']['inventory_status_id']; ?>"/>
									<input type="hidden" name="SenderStore_id[]" value="<?php echo $challan['DistReturnChallan']['sender_store_id']; ?>"/>
									<input type="hidden" name="is_ncp[]" value="<?php echo $val['DistReturnChallanDetail']['is_ncp']; ?>"/>
									</td>
									<td><?php echo $val['DistReturnChallanDetail']['remarks']; ?></td>
								</tr>
								<?php
								$total_quantity = $total_quantity + $val['DistReturnChallanDetail']['challan_qty'];
							
								$sl++;
								}
							}
						}
						?>
						
												
					</tbody>	
				</table>
			</div>	
			<?php
			if($challan['DistReturnChallan']['status'] == 1 AND $office_parent_id !=0){
			?>
			<?php echo $this->Form->submit('Received', array('class' => 'btn btn-large btn-primary')); ?>
			<?php
			}
			?>
			<?php echo $this->Form->end(); ?>
			</br>
		</div>			
	</div>
</div>


<?php
$todayDate = date('Y-m-d');
$startDate = date('d-m-Y', strtotime($challan['DistReturnChallan']['challan_date']));
$endDateOfMonth = date('Y-m-t', strtotime($startDate));

if(strtotime($todayDate) < strtotime($endDateOfMonth) ){
	$endDate = date('d-m-Y');
}else{
	$endDate = date('t-m-Y', strtotime($startDate));
}
?>

<script>
	$(document).ready(function () {
		$('.datepicker_range').datepicker({
			startDate: '<?php echo $startDate; ?>',
			endDate: '<?php echo $endDate; ?>',
			format: "dd-mm-yyyy",
			autoclose: true,
			todayHighlight: true
		});
	});
</script>