<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('SR Memo Details'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> SR Memo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td width="25%"><strong><?php echo 'Distributor Memo No. :'; ?></strong></td>
							<td><?php echo $memo['DistMemo']['dist_memo_no']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Distributor Memo Date :'; ?></strong></td>
							<td><?php echo $memo['DistMemo']['memo_date']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Distributor Name :'; ?></strong></td>
							<td><?php echo $memo['Distributor']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Sales Representative :'; ?></strong></td>
							<td><?php echo $memo['DistSalesRepresentative']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Outlet :'; ?></strong></td>
							<td><?php echo $memo['Outlet']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Market :'; ?></strong></td>
							<td><?php echo $memo['Market']['name']; ?></td>
						</tr>
						<?php if ($memo['DistMemo']['remarks'] != '') { ?>
							<tr>
								<td><strong><?php echo 'Remarks :'; ?></strong></td>
								<td><?php echo $memo['DistMemo']['remarks'] ?></td>
							</tr>
						<?php } ?>

					</tbody>
				</table>
			</div>

			<div class="table-responsive">
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
							if (!empty($memo_details)) {
								$sl = 1;
								$total_price = 0;
								foreach ($memo_details as $val) {
									if ($val['DistMemoDetail']['price'] != 0) {
							?>
										<tr>
											<td align="center"><?php echo $sl; ?></td>
											<td><?php echo $val['Product']['name']; ?></td>
											<td align="center"><?php echo $val['DistMemoDetail']['sales_qty']; ?></td>
											<td align="right"><?php echo sprintf('%.2f', $val['DistMemoDetail']['price']); ?></td>
											<td align="right">
												<?php
												$total = $val['DistMemoDetail']['price'] * $val['DistMemoDetail']['sales_qty'];
												echo sprintf('%.2f', $total);
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
									<td align="right"><strong><?php echo sprintf('%.2f', $total_price); ?></strong></td>
								</tr>
							<?php
							} else {
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
					<table class="table table-bordered">
						<tbody>
							<tr>
								<th class="text-center" width="50">SL.</th>
								<th class="text-center">Product Name</th>
								<th class="text-center">Bonus Qty</th>

							</tr>
							<?php
							if (!empty($memo_details)) {
								$sl = 1;
								$total_price = 0;
								foreach ($memo_details as $val) {
									if ($val['DistMemoDetail']['price'] == 0) {
							?>
										<tr>
											<td align="center"><?php echo $sl; ?></td>
											<td><?php echo $val['Product']['name']; ?></td>
											<td align="center"><?php echo $val['DistMemoDetail']['sales_qty']; ?></td>

										</tr>
								<?php
									}
									$sl++;
								}
								?>
							<?php
							} else {
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
</div>