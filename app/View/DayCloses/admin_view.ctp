
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Memo Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Memo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo 'Memo No. :'; ?></strong></td>
							<td><?php echo $memo['Memo']['memo_no']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Memo Date :'; ?></strong></td>
							<td><?php echo $memo['Memo']['memo_date']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Sales Person :'; ?></strong></td>
							<td><?php echo $memo['SalesPerson']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Outlet :'; ?></strong></td>
							<td><?php echo $memo['Outlet']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Market :'; ?></strong></td>
							<td><?php echo $memo['Market']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Territory :'; ?></strong></td>
							<td><?php echo $memo['Territory']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Memo Status :'; ?></strong></td>
							<td>
								<?php echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
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
							<th class="text-center">Product Name</th>							
							<th class="text-center">Sales Qty</th>							
							<th class="text-center">Price</th>					
							<th class="text-center">Total Price</th>					
						</tr>
						<?php
						if(!empty($memo_details)){
						$sl = 1;
						$total_price = 0;
						foreach($memo_details as $val){							
						?>
							<tr>		
								<td align="center"><?php echo $sl; ?></td>
								<td><?php echo $val['Product']['name']; ?></td>
								<td align="center"><?php echo $val['MemoDetail']['sales_qty']; ?></td>
								<td align="right"><?php echo sprintf('%.2f',$val['MemoDetail']['price']); ?></td>
								<td align="right">
									<?php
									$total = $val['MemoDetail']['price'] * $val['MemoDetail']['sales_qty'];
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
		</div>
			
	</div>
</div>

