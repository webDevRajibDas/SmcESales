<?php 
$maintain_dealer_type=1;
//pr($dealer_is_limit_check);die();
?>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<!--- Office Info --->
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Office Info:'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Order List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
		
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td><strong><?php echo 'Distributor :'; ?></strong></td>
							<td><?php echo $distributor['DistDistributor']['name']; ?></td>
						</tr>
					
						<tr>		
							<td><strong><?php echo 'Market :'; ?></strong></td>
							<td><?php echo $order['Market']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Territory :'; ?></strong></td>
							<td><?php echo $order['Territory']['name']; ?></td>
						</tr>
						
						<tr>		
							<td><strong><?php echo 'Outlet :'; ?></strong></td>
							<td><?php echo $order['Outlet']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Limit Amount :'; ?></strong></td>
							<td><?php echo $balance; ?></td>
						</tr>
					  
					</tbody>
				</table>
			</div>
            <!----- End ------>
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Order Details'); ?></h3>
				
			</div>
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo 'Requisition No. :'; ?></strong></td>
							<td><?php echo $order['Order']['order_no']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Requisition Date :'; ?></strong></td>
							<td><?php echo $order['Order']['order_date']; ?></td>
						</tr>
						
						<tr>		
							<td><strong><?php echo 'Order Limits :'; ?></strong></td>
							<td><?php echo $orderLimits; ?></td>
						</tr>
						
						<!-- <tr>		
							<td><strong><?php //echo 'Sales Person :'; ?></strong></td>
							<td><?php //echo $order['SalesPerson']['name']; ?></td>
						</tr> -->
						<tr>		
							<td><strong><?php echo 'Order Status :'; ?></strong></td>
							<td>
								<?php //echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
                                <?php
								if($order['Order']['status']==0)
								{
										echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
								}
								else
								{
									if ($order['Order']['credit_amount'] !=0 ) {
										echo '<span class="btn btn-danger btn-xs">Due</span>';
									}else {
										echo '<span class="btn btn-success btn-xs">Paid</span>';
									}
								}
								?>
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Confirmation Status :'; ?></strong></td>
							<td>
								<?php //echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
                                <?php
									if ($order['Order']['confirmed'] == 1) {
										echo '<span class="btn btn-success btn-xs">Confirm</span>';
									}else{
										echo '<span class="btn btn-info btn-xs draft">Pending</span>';
									}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>			
			<div class="box-body">
                <table class="table table-bordered">
					<tbody>
						<tr>		
							<th class="text-center" width="50">SL.</th>
							<th class="text-left">Product Name</th>							
							<th class="text-center">Order Qty</th>							
							<th class="text-right">Price</th>					
							<th class="text-right">Total Price</th>					
						</tr>
						<?php
						if(!empty($order_details)){
						$sl = 1;
						$total_price = 0;
						foreach($order_details as $val){
						 if($val['OrderDetail']['is_bonus'] != 1){							
						?>
							<tr>		
								<td align="center"><?php echo $sl; ?></td>
								<td><?php echo $val['Product']['name']; ?></td>
								<td align="center"><?php echo $val['OrderDetail']['sales_qty']; ?></td>
								<td align="right"><?php echo sprintf('%.2f',$val['OrderDetail']['price']-$val['OrderDetail']['discount_amount']); ?></td>
								<td align="right">
									<?php
									$total = ($val['OrderDetail']['price']-$val['OrderDetail']['discount_amount']) * $val['OrderDetail']['sales_qty'];
									echo sprintf('%.2f',$total);
									?>
								</td>
							</tr>
						<?php
							$total_price =  $total_price + $total;
							$sl++;
						} 
					}
						?>
						<tr>		
							<td align="right" colspan="4"><strong>Total Amount :</strong></td>
							<td align="right"><strong><?php echo sprintf('%.2f',$total_price); ?></strong></td>							
						</tr>
						<?php
							}else{
						?>
						<tr>		
							<td align="center" colspan="5"><strong>No product available</strong></td>	
						</tr>
						<?php
							}
						?>	
				</table>
			</div>

			<div class="box-body">
				<div class="box-header">
					<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Bonus Product'); ?></h3>
				</div>
                <table class="table table-bordered">
					<tbody>
						<tr>		
							<th class="text-center" width="50">SL.</th>
							<th class="text-left">Product Name</th>							
							<th class="text-center">Sales Qty</th>							
											
						</tr>
						<?php
						if(!empty($order_details)){
						$sl = 1;
						$total_price = 0;
						foreach($order_details as $val){
						 if($val['OrderDetail']['is_bonus'] == 1){							
						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Product']['name']; ?></td>
							<td align="center"><?php echo $val['OrderDetail']['sales_qty']; ?></td>
							
						</tr>
						<?php	
								} 
							}
						}		
						?>
				</table>
			</div>
		</div>
		  <div class="row">
		  	<div class="col-lg-8"></div>
		  	<div class="col-lg-2"></div>
		  	<div class="col-lg-2">
		  		
		  	</div>
			
		</div>
	</div>
</div>

