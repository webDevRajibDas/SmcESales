<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Do Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> DO List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo 'DO No.'; ?></strong></td>
							<td><?php echo h($requisition['Requisition']['do_no']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Sender Store'; ?></strong></td>
							<td><?php echo h($requisition['SenderStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'DO Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat($requisition['Requisition']['created_at']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Receiver Store'; ?></strong></td>
							<td><?php echo h($requisition['ReceiverStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Completed Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat($requisition['Requisition']['updated_at']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Status'; ?></strong></td>
							<td>
							<?php 
								if($requisition['Requisition']['status'] == 1)
								{ 
									echo '<span class="btn btn-warning btn-xs">Open</span>';
								}else{
									echo '<span class="btn btn-danger btn-xs">Close</span>';
								}	
							?>
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Remarks'; ?></strong></td>
							<td><?php echo h($requisition['Requisition']['remarks']); ?></td>
						</tr>						
				</table>
			</div>
								
			<div class="box-body">
                <table class="table table-bordered">
					<tbody>
						<tr>		
							<th width="6%" class="text-center">SL.</th>
							<th class="text-center">Product Name</th>
							<!-- <th width="12%" class="text-center">Batch No.</th>
							<th width="12%" class="text-center">Expire Date</th> -->
							<th width="12%" class="text-center">Unit</th>
							<th width="12%" class="text-center">Quantity</th>							
							<th width="12%" class="text-center">Remaining Quantity</th>				
						</tr>
						<?php
						if(!empty($requisitiondetail))
						{
							$sl = 1;
							$total_quantity = 0;							
							foreach($requisitiondetail as $val){

								if($val['RequisitionDetail']['virtual_product_name_show'] > 0){
									$val['Product']['name'] = $val['VirtualProduct']['name'];
								}

						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Product']['name']; ?></td>
							<!-- <td align="center"><?php echo $val['RequisitionDetail']['batch_no']; ?></td>
							<td align="center"><?php echo $this->App->dateformat($val['RequisitionDetail']['expire_date']); ?></td> -->
							<td align="center"><?php echo $val['MeasurementUnit']['name']; ?></td>
							<td align="center"><?php echo $val['RequisitionDetail']['qty']; ?></td>
							<td align="center"><?php echo $val['RequisitionDetail']['remaining_qty']; ?></td>
						</tr>
						<?php
							$total_quantity = $total_quantity + $val['RequisitionDetail']['qty'];
							
							$sl++;
							}							
						}
						?>	3						
					<tbody>	
				</table>
			</div>			
		</div>			
	</div>
</div>


