
<?php
	// pr($soStockCheckDetail);exit;
 ?>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('So Stock Check'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> So Stock Check List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="SoStockChecks" class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td><strong><?php echo __('Id'); ?></strong></td>
							<td>
									<?php echo h($soStockCheck['SoStockCheck']['id']); ?>
									&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('So Id'); ?></strong></td>
							<td>
								<?php echo h($soStockCheck['SalesPerson']['name']); ?>
								&nbsp;
							</td>
						</tr>
					<tr>		
						<td><strong><?php echo __('Store'); ?></strong></td>
						<td>
							<?php echo $this->Html->link($soStockCheck['Store']['name'], array('controller' => 'stores', 'action' => 'view', $soStockCheck['Store']['id']), array('class' => '')); ?>
							&nbsp;
						</td>
					</tr>
					<tr>		
						<td><strong><?php echo __('Reported Time'); ?></strong></td>
						<td>
							<?php echo h($soStockCheck['SoStockCheck']['reported_time']); ?>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td><strong><?php echo __('Created At'); ?></strong></td>
						<td>
							<?php echo h($soStockCheck['SoStockCheck']['created_at']); ?>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td><strong><?php echo __('Created By'); ?></strong></td>
						<td>
							<?php echo h($soStockCheck['SoStockCheck']['created_by']); ?>
							&nbsp;
						</td>
					</tr>					
				</tbody>
			</table>



				<table id="SoStockChecks" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center">Serial</th>
							<th class="text-center">Product</th>
							<th class="text-center">Web Stock</th>
							<th class="text-center">App Stock</th>
							<th class="text-center">Physical Stock</th>
							<th class="text-center">Diff. Btw.<br> WEB & APP Stock</th>
							<th class="text-center">Diff. Btw.<br> WEB & Phy. Stock</th>
						</tr>
					</thead>
					<tbody>
					<?php $i=1;foreach ($soStockCheckDetail as $data): ?>
						<tr>
							<td class="text-center"><?php echo h($i++); ?></td>
							<td class="text-center"><?php echo h($data['Product']['name']); ?></td>
							<td class="text-center"><?php echo h($data['SoStockCheckDetail']['web_stock']); ?></td>
							<td class="text-center"><?php echo h($data['SoStockCheckDetail']['app_stock']); ?></td>
							<td class="text-center"><?php echo h($data['SoStockCheckDetail']['physical_stock']); ?></td>

							<td class="text-center"><?php echo h(sprintf('%.2f',$data['SoStockCheckDetail']['web_stock']-$data['SoStockCheckDetail']['app_stock'])); ?></td>
							<td class="text-center"><?php echo h(sprintf('%.2f',$data['SoStockCheckDetail']['web_stock']-$data['SoStockCheckDetail']['physical_stock'])); ?></td>
							
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

