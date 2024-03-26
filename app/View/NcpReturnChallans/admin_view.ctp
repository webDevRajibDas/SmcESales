<style>
	.draft_size{
		padding: 0px 15px;
	}
</style>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('NCP Return Challan Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> NCP Return Challan  List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>	
			<?php echo $this->Form->create('ReturnChallan', array('role' => 'form')); ?>			
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo 'ReturnChallan No.'; ?></strong></td>
							<td><?php echo h($challan['ReturnChallan']['challan_no']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Transaction Type'; ?></strong></td>
							<td><?php echo h($challan['TransactionType']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Sender Store'; ?></strong></td>
							<td><?php echo h($challan['SenderStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'ReturnChallan Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat($challan['ReturnChallan']['challan_date']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Receiver Store'; ?></strong></td>
							<td><?php echo h($challan['ReceiverStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Received Date'; ?></strong></td>
							<td>
								<?php 
								if($challan['ReturnChallan']['status'] == 1 AND $office_paren_id ==0)
								{	
									echo $this->Form->input('received_date', array('type'=>'text','label'=>false, 'class' => 'form-control datepicker_range','required'=>true)); 
								}else{								
									echo $this->App->dateformat(($challan['ReturnChallan']['received_date'])); 
								}
								?>
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Status'; ?></strong></td>
							<td align="left">
                            <?php
	                            if ($challan['ReturnChallan']['status'] == 1) {
	                                echo '<span class="btn btn-warning btn-xs">Pending</span>';
	                            }elseif ($challan['ReturnChallan']['status'] == 2) {
	                                echo '<span class="btn btn-success btn-xs">Received</span>';
	                            }else{
	                                echo '<span class="btn btn-primary btn-xs draft_size">Draft</span>';
	                            }
	                        ?>
                           </td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Remarks'; ?></strong></td>
							<td><?php echo h($challan['ReturnChallan']['remarks']); ?></td>
						</tr>						
				</table>
			</div>
			<?php echo $this->Form->create('ReturnChallan', array('role' => 'form')); ?>						
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
							<th class="text-center">Remarks</th>							
						</tr>
						<?php
						if(!empty($challandetail))
						{
							$sl = 1;
							$total_quantity = 0;
							$total_received_quantity = 0;
							foreach($challandetail as $val){
						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Product']['name']; ?></td>
							<td align="center"><?php echo $val['ReturnChallanDetail']['batch_no']; ?></td>
							<td align="center"><?php echo $this->App->expire_dateformat($val['ReturnChallanDetail']['expire_date']); ?></td>
							<td><?php echo $val['MeasurementUnit']['name']; ?></td>
							<td align="center"><?php echo $val['ReturnChallanDetail']['challan_qty']; ?></td>
							<td><?php echo $val['ReturnChallanDetail']['remarks']; ?></td>
						</tr>
						<?php
							$total_quantity = $total_quantity + $val['ReturnChallanDetail']['challan_qty'];
							
							$sl++;
							}							
						}
						?>	
						<tr>		
							<td align="right" colspan="5"><strong>Total Quantity :</strong></td>
							<td align="center"><?php echo $total_quantity; ?></td>							
							<td></td>							
						</tr>
					</tbody>	
				</table>
			</div>	
			<?php
			if($challan['ReturnChallan']['status'] == 1 AND $office_paren_id ==0){
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
$startDate = date('d-m-Y', strtotime($challan['ReturnChallan']['challan_date']));
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