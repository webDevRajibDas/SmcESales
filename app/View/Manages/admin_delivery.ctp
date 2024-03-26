<?php 
$maintain_dealer_type=1;
//pr($order_details);die();
?>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<!--- Office Info --->
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Office Info:'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('manages','admin_edit')){ //echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Memo'), array('action' => 'edit'), array('class' => 'btn btn-info', 'escape' => false)); } ?>
					<a class='btn btn-info' href="<?php echo $this->Html->url('/admin/manages/edit/'.$order['Order']['id']) ?>"><i class='glyphicon glyphicon-plus'></i>New Memo</a>
				<?php }?>
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Order manage List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
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
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Order Manage Details'); ?></h3>
				
			</div>
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo 'Order No. :'; ?></strong></td>
							<td><?php echo $order['Order']['order_no']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Order Date :'; ?></strong></td>
							<td><?php echo $order['Order']['order_date']; ?></td>
						</tr>
						
								<tr>		
									<td><strong><?php echo 'Order Limits :'; ?></strong></td>
									<td><?php echo $orderLimits; ?></td>
								</tr>
						 
						<tr>		
							<td><strong><?php echo 'Sales Person :'; ?></strong></td>
							<td><?php echo $order['SalesPerson']['name']; ?></td>
						</tr>
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
							<th class="text-center">Sales Qty</th>							
							<th class="text-center">Remaining Qty</th>							
							<th class="text-center">Deliverd Qty</th>							
							<th class="text-right">Price</th>					
							<th class="text-right">Total Price</th>					
						</tr>
						<?php
						if(!empty($order_details)){
						$sl = 1;
						$total_price = 0;
						foreach($order_details as $val){							
						?>
							<tr>		
								<td align="center"><?php echo $sl; ?></td>
								<td><?php echo $val['Product']['name']; ?></td>
								<td align="center"><?php echo $val['OrderDetail']['sales_qty']; ?></td>
								<td align="center"><?php echo $val['OrderDetail']['remaining_qty']; ?></td>
								<td align="center"><?php echo $val['OrderDetail']['deliverd_qty']; ?></td>
								<td align="right"><?php echo sprintf('%.2f',$val['OrderDetail']['price']); ?></td>
								<td align="right">
									<?php
									$total = $val['OrderDetail']['price'] * $val['OrderDetail']['sales_qty'];
									echo sprintf('%.2f',$total);
									?>
								</td>
							</tr>
						<?php
							$total_price =  $total_price + $total;
							$sl++;
						}
						?>
						<tr>
							<td></td>						
							<td></td>	
							<td align="right" colspan="4"><strong>Total Amount :</strong></td>
							<td align="right"><strong><?php echo sprintf('%.2f',$total_price); ?></strong></td>	
													
						</tr>
						<tr>	
							<td></td>						
							<td></td>	
							<td align="right" colspan="4"><strong>Credit Recieved :</strong></td>
							<td align="right"><strong><?php echo sprintf('%.2f',$order['Order']['cash_recieved']); ?></strong></td>						

						</tr>
						<tr>	

							<td></td>						
							<td></td>	
							<td align="right" colspan="4"><strong>Credit Amount :</strong></td>
							<td align="right"><strong><?php echo sprintf('%.2f',$order['Order']['credit_amount']); ?></strong></td>						

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
			
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Memo Details'); ?></h3>
			</div>
			<div class="box-body">
                <table class="table table-bordered">
					<tbody>
						<tr>		
							<th class="text-center" width="50">SL.</th>
							<th class="text-left">Memo No</th>							
							<th class="text-center">Memo Date</th>							
							<th class="text-right">Price</th>					
							<th class="text-right">Credit Ammount</th>					
							<th class="text-right">Status</th>					
							<th class="text-right">Action</th>					
						</tr>
						<?php
						if(!empty($order_details)){
						$sl = 1;
						$total_price = 0;
						foreach($memolist as $val){							
						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Memo']['memo_no']; ?></td>
							<td align="center"><?php echo $val['Memo']['memo_date']; ?></td>
							<td align="right"><?php echo sprintf('%.2f',$val['Memo']['gross_value']); ?></td>
							<td align="right">
								<?php
								$total = $val['Memo']['credit_amount'];
								echo sprintf('%.2f',$total);
								?>
							</td>
							<td>
							  <?php //echo $val['Memo']['status']; 
									if(@$order['Order']['manage_draft_status']==1)
									{
									echo '<span class="btn btn-info btn-xs draft">Draft</span>';
									}
									elseif(@$order['Order']['manage_draft_status']==2)
									{
									echo '<span class="btn btn-success btn-xs draft">Confirmed</span>';
									}
									else
									{
									echo '<span class="btn btn-danger btn-xs draft">Pending</span>';	
									}
								?>
							</td>
							<td class="text-center" width="15%">

							<?php if($this->App->menu_permission('manages','admin_view')){ 
								echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller'=>'memos','action' => 'view', $val['Memo']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>

							<!-- <a class='btn btn-primary btn-xs' href="<?php echo $this->Html->url('/admin/memos/view/'.$val['Memo']['id']) ?>"><i class='glyphicon glyphicon-eye-open'></i></a> -->
							
							<?php 
							if($this->App->menu_permission('manages','admin_edit'))
							{ 
								echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller'=>'manages','action' => 'editmemo', $val['Memo']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Edit')); }
							?>
							<!-- <a class='btn btn-warning btn-xs' href="<?php echo $this->Html->url('/admin/memos/edit/'.$val['Memo']['id']) ?>"><i class='glyphicon glyphicon-pencil'></i></a> -->
							

							<?php if($this->App->menu_permission('manages','admin_delete')){
								echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller'=>'memos','action' => 'delete', $val['Memo']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $val['Memo']['id'])); }?>

							<!-- <a class='btn btn-danger btn-xs' href="<?php echo $this->Html->url('/admin/memos/delete/'.$val['Memo']['id']) ?>" data-toggle='tooltip' ><i class='glyphicon glyphicon-trash'></i></a>	 -->
							
							</td>
						</tr>
						<?php
							$total_price =  $total_price + $total;
							$sl++;
						}
						?>
						<!-- <tr>		
							<td align="right" colspan="4"><strong>Total Amount :</strong></td>
							<td align="right"><strong><?php echo sprintf('%.2f',$total_price); ?></strong></td>				
							<td></td>			
						</tr>
						<tr>		
							<td align="right" colspan="4"><strong>Credit Recieved :</strong></td>
							<td align="right"><strong><?php echo sprintf('%.2f',$order['Order']['cash_recieved']); ?></strong></td>	
							<td></td>			
						
						</tr>
						<tr>		
							<td align="right" colspan="4"><strong>Credit Amount :</strong></td>
							<td align="right"><strong><?php echo sprintf('%.2f',$order['Order']['credit_amount']); ?></strong></td>	
							<td></td>			
													
						</tr> -->
						
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
		</div>
		  <div class="row">
		  	<div class="col-lg-8"></div>
		  	<div class="col-lg-2"></div>
		  	<div class="col-lg-2">
		  		
		  	</div>
			
		</div>

		
	</div>
</div>

