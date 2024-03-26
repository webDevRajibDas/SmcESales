
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Challan Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Challan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>	
			
			<?php echo $this->Form->create('RetureturnChallanDetailrnChallan', array('role' => 'form')); ?>
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo 'Challan No.'; ?></strong></td>
							<td><?php echo h($returnChallan['ReturnChallan']['challan_no']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Challan Referance No.'; ?></strong></td>
							<td><?php echo h($returnChallan['ReturnChallan']['challan_referance_no']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Transaction Type'; ?></strong></td>
							<td><?php echo h($returnChallan['TransactionType']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Inventory Status'; ?></strong></td>
							<td><?php echo h($returnChallan['InventoryStatus']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Sender Store'; ?></strong></td>
							<td><?php echo h($returnChallan['SenderStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Challan Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat(($returnChallan['ReturnChallan']['challan_date'])); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Receiver Store'; ?></strong></td>
							<td><?php echo h($returnChallan['ReceiverStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Received Date'; ?></strong></td>
							<td>
								<?php 
								if($returnChallan['ReturnChallan']['status'] == 1 AND $office_parent_id !=0)
								{	
									echo $this->Form->input('received_date', array('type'=>'text','label'=>false, 'class' => 'form-control datepicker_range', 'required'=>true,'readonly'=>true));
								}else{								
									echo $this->App->dateformat(($returnChallan['ReturnChallan']['received_date']));
								}
								?>							
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Status'; ?></strong></td>
							<td><?php echo $returnChallan['ReturnChallan']['status'] == 1 ? '<span class="btn btn-warning btn-xs">Pending</span>' : '<span class="btn btn-success btn-xs">Received</span>'; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Remarks'; ?></strong></td>
							<td><?php echo h($returnChallan['ReturnChallan']['remarks']); ?></td>
						</tr>						
				</table>
			</div>			
											
			<div class="box-body">
                <table class="table table-bordered">
					<tbody>
						<tr>		
							<th class="text-center">SL.</th>
							<th class="text-center">Product Name</th>
							<th class="text-center">Batch No.</th>
							<th class="text-center">Unit</th>
							<th class="text-center">Expire Date</th>							
							<th class="text-center">Quantity</th>							
							<th class="text-center">Remarks</th>							
						</tr>
						<?php
						if(!empty($returnChallanDetail))
						{
							$sl = 1;
							$total_quantity = 0;
							$total_received_quantity = 0;
							foreach($returnChallanDetail as $val){							
						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Product']['name']; ?></td>
							<td align="center"><?php echo $val['ReturnChallanDetail']['batch_no']; ?></td>
							<td><?php echo $val['MeasurementUnit']['name']; ?></td>
							<td align="center"><?php echo $this->App->expire_dateformat($val['ReturnChallanDetail']['expire_date']); ?></td>
							<td align="center">								
								<input type="hidden" name="id[]" value="<?php echo $val['ReturnChallanDetail']['id']; ?>"/>
								<input type="hidden" name="product_id[]" value="<?php echo $val['ReturnChallanDetail']['product_id']; ?>"/>
								<input type="hidden" name="virtual_product_id[]" value="<?php echo $val['ReturnChallanDetail']['virtual_product_id']; ?>"/>
								<input type="hidden" name="measurement_unit_id[]" value="<?php echo $val['ReturnChallanDetail']['measurement_unit_id']; ?>"/>
								<input type="hidden" name="quantity[]" value="<?php echo $val['ReturnChallanDetail']['challan_qty']; ?>"/>
								<input type="hidden" name="batch_no[]" value="<?php echo $val['ReturnChallanDetail']['batch_no']; ?>"/>
								<input type="hidden" name="expire_date[]" value="<?php echo $val['ReturnChallanDetail']['expire_date']; ?>"/>
								<input type="hidden" name="inventory_status_id[]" value="<?php echo $returnChallan['ReturnChallan']['inventory_status_id']; ?>"/>
								<input type="hidden" name="SenderStore_id[]" value="<?php echo $returnChallan['SenderStore']['id']; ?>"/>
								
								<?php echo $val['ReturnChallanDetail']['challan_qty']; ?>								
							</td>
							<td><?php echo $val['ReturnChallanDetail']['remarks']; ?></td>
						</tr>
						<?php							
							$sl++;
							}							
						}
						?>							
				</table>
			</div>	
			<?php
			if($returnChallan['ReturnChallan']['status'] == 1 AND $office_parent_id !=0){
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
</div>


<?php
$todayDate = date('Y-m-d');
$startDate = date('d-m-Y', strtotime($returnChallan['ReturnChallan']['challan_date']));
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
