<style>
	.draft_size{
		padding: 0px 15px;
	}
</style>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('NCP Product Issue Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>NCP Product Issue List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo 'Challan No.'; ?></strong></td>
							<td><?php echo h($challan['Challan']['challan_no']); ?></td>
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
							<td><strong><?php echo 'Challan Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat($challan['Challan']['challan_date']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Receiver Store'; ?></strong></td>
							<td><?php echo h($challan['ReceiverStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Received Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat($challan['Challan']['received_date']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Status'; ?></strong></td>
							<td align="left">
                            <?php
	                            if ($challan['Challan']['status'] == 1) {
	                                echo '<span class="btn btn-warning btn-xs">Pending</span>';
	                            }elseif ($challan['Challan']['status'] == 2) {
	                                echo '<span class="btn btn-success btn-xs">Received</span>';
	                            }else{
	                                echo '<span class="btn btn-primary btn-xs draft_size">Draft</span>';
	                            }
	                        ?>
                           </td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Remarks'; ?></strong></td>
							<td><?php echo h($challan['Challan']['remarks']); ?></td>
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
							<td align="center"><?php echo $val['ChallanDetail']['batch_no']; ?></td>
							<td align="center"><?php if($val['ChallanDetail']['expire_date']){echo $this->App->expire_dateformat($val['ChallanDetail']['expire_date']);}else ''; ?></td>
							<td><?php echo $val['MeasurementUnit']['name']; ?></td>
							<td align="center"><?php echo $val['ChallanDetail']['challan_qty']; ?></td>
							<td><?php echo $val['ChallanDetail']['remarks']; ?></td>
						</tr>
						<?php
							$total_quantity = $total_quantity + $val['ChallanDetail']['challan_qty'];
							
							$sl++;
							}							
						}
						?>								
				</table>
			</div>			
		</div>
			
		</div>

					
			

			
	</div>

</div>

