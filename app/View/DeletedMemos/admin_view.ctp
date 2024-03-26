
<?php 
	//pr($DeletedMemo_basic);exit; 
?>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('DeletedMemo Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> DeletedMemo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td colspan="2" class="text-center">Memo Basic Info</td>
						</tr>
						<tr>		
							<td width="25%"><strong><?php echo 'Memo No. :'; ?></strong></td>
							<td><?php echo $DeletedMemo_basic['DeletedMemo']['memo_no']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Memo Date Time :'; ?></strong></td>
							<td><?php echo $this->App->datetimeformat($DeletedMemo_basic['DeletedMemo']['memo_time']); ?></td>
						</tr>
						<!-- <tr>		
							<td><strong><?php echo 'Delete/Edited time:'; ?></strong></td>
							<td><?php //echo $this->App->datetimeformat($DeletedMemo_basic['DeletedMemo']['deleted_at']); ?></td>
						</tr> -->
						<tr>		
							<td><strong><?php echo 'Office :'; ?></strong></td>
							<td><?php echo $DeletedMemo_basic['Office']['office_name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Territory :'; ?></strong></td>
							<td><?php echo $DeletedMemo_basic['Territory']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Sales Person :'; ?></strong></td>
							<td><?php echo $DeletedMemo_basic['SalesPerson']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Thana :'; ?></strong></td>
							<td><?php echo $DeletedMemo_basic['Thana']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Market :'; ?></strong></td>
							<td><?php echo $DeletedMemo_basic['Market']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Outlet :'; ?></strong></td>
							<td><?php echo $DeletedMemo_basic['Outlet']['name']; ?></td>
						</tr>
						<!-- <tr>		
							<td><strong><?php echo 'Memo Status :'; ?></strong></td>
							<td>
								<?php //echo $DeletedMemo['DeletedMemo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
                                <?php
									//if ($DeletedMemo['DeletedMemo']['status'] == 1) {
										//echo '<span class="btn btn-danger btn-xs">Credit</span>';
									//}elseif ($DeletedMemo['DeletedMemo']['status'] == 2) {
										//echo '<span class="btn btn-success btn-xs">Cash</span>';
									//}
								?>
							</td>
						</tr> -->
						<!-- <tr>		
							<td><strong><?php echo 'Log Status :'; ?></strong></td>
							<td>
								<?php //echo $DeletedMemo['DeletedMemo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
                                <?php
									/*if ($DeletedMemo['DeletedMemo']['is_delete'] == 1) {
										echo '<span class="btn btn-danger btn-xs">Deleted</span>';
									}elseif ($DeletedMemo['DeletedMemo']['is_delete'] == 0) {
										echo '<span class="btn btn-warning btn-xs">Edited</span>';
									}*/
								?>
							</td>
						</tr> -->
					</tbody>
				</table>
			</div>			
			<?php foreach($DeletedMemo as $data){ ?>
			<div class="box-body">

				<table class="table table-bordered">
					
					<tbody>
						<tr>
							<td class="text-center">Memo Status</td>
							<td class="text-center">Log Status</td>
							<td class="text-center">
								<?php if($data['DeletedMemo']['is_delete']==0) {echo '<span>Edited At</span>';}else{echo '<span>Deleted At</span>';} ?>
							</td>
						</tr>
						<tr>
							<td class="text-center">
								<?php
								if ($data['DeletedMemo']['status'] == 1) {
									echo '<span class="btn btn-danger btn-xs">Credit</span>';
								}elseif ($data['DeletedMemo']['status'] == 2) {
									echo '<span class="btn btn-success btn-xs">Cash</span>';
								}
								?>
							</td>
							<td class="text-center">
								<?php if($data['DeletedMemo']['is_delete']==0) {echo '<span class="btn btn-warning btn-xs">Edited</span>';}else{echo '<span class="btn btn-danger btn-xs">Deleted</span>';} ?>

							</td>
							<td class="text-center">
								<?php echo $this->App->datetimeformat($data['DeletedMemo']['deleted_at']); ?>
							</td>
						</tr>
					</tbody>
				</table>
	                <table class="table table-bordered">
						<tbody>
							<tr>		
								<th class="text-center" width="50">SL.</th>
								<th class="text-left">Product Name</th>							
								<th class="text-center">Sales Qty</th>							
								<th class="text-right">Price</th>					
								<th class="text-right">Total Price</th>					
							</tr>
							<?php
							if(!empty($data['DeletedMemoDetail'])){
							$sl = 1;
							$total_price = 0;
							foreach($data['DeletedMemoDetail'] as $val){							
							?>
								<tr>		
									<td align="center"><?php echo $sl; ?></td>
									<td><?php echo $product[$val['product_id']]['name']; ?></td>
									<td align="center"><?php echo $val['sales_qty']; ?></td>
									<td align="right"><?php echo sprintf('%.2f',$val['price']); ?></td>
									<td align="right">
										<?php
										$total = $val['price'] * $val['sales_qty'];
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
			<?php } ?>
<!-- current memo  -->
			<?php if(!empty($current_memo)){ ?>
			<div class="box-body">

				<table class="table table-bordered">
					
					<tbody>
						<tr>
							<td class="text-center">Memo Status</td>
							<td class="text-center">Log Status</td>
						</tr>
						<tr>
							<td class="text-center">
								<?php
								if ($current_memo['Memo']['status'] == 1) {
									echo '<span class="btn btn-danger btn-xs">Credit</span>';
								}elseif ($current_memo['Memo']['status'] == 2) {
									echo '<span class="btn btn-success btn-xs">Cash</span>';
								}
								?>
							</td>
							<td class="text-center">
								<b>Current Memo</b>

							</td>
						</tr>
					</tbody>
				</table>
	                <table class="table table-bordered">
						<tbody>
							<tr>		
								<th class="text-center" width="50">SL.</th>
								<th class="text-left">Product Name</th>							
								<th class="text-center">Sales Qty</th>							
								<th class="text-right">Price</th>					
								<th class="text-right">Total Price</th>					
							</tr>
							<?php
							if(!empty($current_memo['MemoDetail'])){
							$sl = 1;
							$total_price = 0;
							foreach($current_memo['MemoDetail'] as $val){							
							?>
								<tr>		
									<td align="center"><?php echo $sl; ?></td>
									<td><?php echo $product[$val['product_id']]['name']; ?></td>
									<td align="center"><?php echo $val['sales_qty']; ?></td>
									<td align="right"><?php echo sprintf('%.2f',$val['price']); ?></td>
									<td align="right">
										<?php
										$total = $val['price'] * $val['sales_qty'];
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
			<?php } ?>
		</div>
			
	</div>
</div>

