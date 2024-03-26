<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Product Details'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<table id="ProductCategories" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td width="25%"><strong><?php echo __('Product Name'); ?></strong></td>
							<td><?php echo h($product['Product']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Product Category'); ?></strong></td>
							<td><?php echo h($product['ProductCategory']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Product Type'); ?></strong></td>
							<td><?php echo h($product['ProductType']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Brand'); ?></strong></td>
							<td><?php echo h($product['Brand']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Variant'); ?></strong></td>
							<td><?php echo h($product['Variant']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Group'); ?></strong></td>
							<td><?php echo h($product['Group']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Source'); ?></strong></td>
							<td><?php echo h($product['Product']['source']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Base measurement unit'); ?></strong></td>
							<td><?php echo h($product['BaseMeasurementUnit']['name']); ?></td>
						</tr>
						<?php
						if (!empty($product_measurement_unit)) {
						?>
							<tr>
								<td><strong><?php echo __('Others unit'); ?></strong></td>
								<td>
									<table width="50%" class="table table-bordered">
										<tr>
											<td class="text-center"><b>Unit Name</b></td>
											<td class="text-center"><b>Quantity in Base</b></td>
											<td class="text-center"><b>Action</b></td>
										</tr>
										<?php foreach ($product_measurement_unit as $val) { ?>
											<tr>
												<td class="text-center"><?= $val['MeasurementUnit']['name']; ?></td>
												<td class="text-center"><?= $val['ProductMeasurement']['qty_in_base']; ?></td>
												<td class="text-center"><?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_measurement_unit', $val['ProductMeasurement']['id'], $product['Product']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $val['ProductMeasurement']['id']));  ?></td>
											</tr>
										<?php } ?>
									</table>
								</td>
							</tr>
						<?php
						}
						?>
						<tr>
							<td><strong><?php echo __('Sales measurement unit'); ?></strong></td>
							<td><?php echo h($product['SalesMeasurementUnit']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Challan measurement unit'); ?></strong></td>
							<td><?php echo h($product['ChallanMeasurementUnit']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Return measurement unit'); ?></strong></td>
							<td><?php echo h($product['ReturnMeasurementUnit']['name']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Is Pharma'); ?></strong></td>
							<td><?php if ($product['Product']['is_pharma'] == 1) {
									echo 'Yes';
								} else {
									echo 'No';
								}; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Is Maintain Batch'); ?></strong></td>
							<td><?php if ($product['Product']['maintain_batch'] == 1) {
									echo 'Yes';
								} else {
									echo 'No';
								}; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Is Maintain Expire Date'); ?></strong></td>
							<td><?php if ($product['Product']['is_maintain_expire_date'] == 1) {
									echo 'Yes';
								} else {
									echo 'No';
								}; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Is Active'); ?></strong></td>
							<td><?php if ($product['Product']['is_active'] == 1) {
									echo 'Yes';
								} else {
									echo 'No';
								}; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('Order'); ?></strong></td>
							<td><?php echo h($product['Product']['order']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo __('CYP'); ?></strong></td>
							<td><?php echo h($product['Product']['cyp']); ?></td>
						</tr>
                        <!------parent product------->
                        <tr>
                            <td><strong><?php echo __('Parent'); ?></strong>
                                <?php if (['Product']['is_virtual'] != 1)
                                    echo $parent_products
                                ?></td>
						</tr>
						<tr>
							<td><strong>Product Img</strong></td>
							<td><img src="<?= BASE_URL . 'app/webroot/img/product_img/' . $product['Product']['product_image'] ?>" height="200" width="200"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>