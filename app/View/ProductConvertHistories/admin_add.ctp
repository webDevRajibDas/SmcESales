<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Admin Add Product Convert History'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Convert History List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<?php echo $this->Form->create('ProductConvertHistory', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('store_id', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('from_status_id', array('class' => 'form-control', 'empty' => '----- Select Status ----', 'options' => $inventory_status)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('from_product_type', array('class' => 'form-control', 'empty' => '----- Select Type ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('from_product_id', array('class' => 'form-control', 'empty' => '--- Select Product ---')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('from_batch_no', array('id' => 'batch_no', 'type' => 'select', 'class' => 'form-control batch_no', 'empty' => '---- Select Batch ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('from_expire_date', array('id' => 'expire_date', 'type' => 'select', 'class' => 'form-control expire_date', 'empty' => '---- Select Expire Date ----')); ?>
				</div>
				<!-- <div class="form-group">
						<?php //echo $this->Form->input('to_product_id', array('class' => 'form-control')); 
						?>
					</div> -->
				<div class="form-group" id="curentinventorystock">
					<?php echo $this->Form->input('current_stock_quantity', array('class' => 'form-control', 'readonly' => true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('to_status_id', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('quantity', array('class' => 'form-control')); ?>
				</div>
				<!--<div class="form-group">
						<?php //echo $this->Form->input('type', array('class' => 'form-control')); 
						?>
					</div>
					<div class="form-group">
						<?php //echo $this->Form->input('created_at', array('class' => 'form-control')); 
						?>
					</div>
					<div class="form-group">
						<?php //echo $this->Form->input('created_by', array('class' => 'form-control')); 
						?>
					</div>-->
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {

		var fromProBaseQty = '';
		var toProBaseQty = '';
		var fromProCategory = '';
		var toProCategory = '';

		$('#ProductConvertHistoryFromProductType').change(function() {
			var inv_id = $('#ProductConvertHistoryFromStatusId').val();
			var types = $(this).val();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>current_inventories/get_inventory_status_by_inv_id',
				data: {
					'inv_id': inv_id,
					'types': types
				},
				cache: false,
				dataType: 'json',
				success: function(response) {
					//alert(response[0].toString());
					//console.log(response);
					$('#ProductConvertHistoryToStatusId').html('');
					$('#ProductConvertHistoryFromProductId').html('');
					$('#ProductConvertHistoryToStatusId').html('<option value=""> --- Select Status --- </option>')
					$('#ProductConvertHistoryFromProductId').html('<option value=""> --- Select Product --- </option>');
					$.each(response[0], function(value, key) {
						$('#ProductConvertHistoryQuantity').attr("value", response['qty']);

						$('#ProductConvertHistoryToStatusId').append($("<option></option>")
							.attr("value", value).text(key));
					});
					/* $.each(response[2], function(value, key) {
						
						$('#ProductConvertHistoryFromProductId').append($("<option></option>")
									.attr("value", value).text(key));
					}); */
					$.each(response[1], function(value, key) {

						$('#ProductConvertHistoryFromProductId').append($("<option></option>")
							.attr("value", value).text(key));
					});

					fromProBaseQty = response['base_measurement_unit_id'];
					fromProCategory = response['product_category_id'];

				}
			});
		});


		$('#ProductConvertHistoryToProductId').change(function() {
			var inv_id = $(this).val();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>current_inventories/get_product_Info_by_inv_id',
				data: {
					'inv_id': inv_id
				},
				cache: false,
				dataType: 'json',
				success: function(response) {
					//var obj = $.parseJSON(response );

					toProBaseQty = response['base_measurement_unit_id'];
					toProCategory = response['product_category_id'];

					if (fromProBaseQty != toProBaseQty) {
						alert("base qntity must be same");
						$('#ProductConvertHistoryToStatusId').attr("value", '').text('');
					} else if (fromProCategory != toProCategory) {
						alert("product category  must be same");
						$('#ProductConvertHistoryToStatusId').attr("value", '').text('');
					} else {
						$.each(response[0], function(value, key) {
							$('#ProductConvertHistoryToStatusId').append($("<option></option>")
								.attr("value", value).text(key));

						});
					}

				}
			});
		});

		$('#ProductConvertHistoryFromProductId').selectChain({
			target: $('#batch_no'),
			value: 'title',
			url: '<?= BASE_URL . 'current_inventories/get_batch_list'; ?>',
			type: 'post',
			data: {
				'product_id': 'ProductConvertHistoryFromProductId',
				'inventory_status_id': 'ProductConvertHistoryFromStatusId',
				'with_stock': true
			}
		});
		$('#batch_no').selectChain({
			target: $('#expire_date'),
			value: 'title',
			url: '<?= BASE_URL . 'current_inventories/get_expire_date_list'; ?>',
			type: 'post',
			data: {
				'product_id': 'ProductConvertHistoryFromProductId',
				'batch_no': 'batch_no',
				'inventory_status_id': 'ProductConvertHistoryFromStatusId',
				'with_stock': true
			}
		});
		$('#ProductConvertHistoryFromProductId,#batch_no,#expire_date').change(function() {
			var expire_date = $("#expire_date").val();
			var productId = $("#ProductConvertHistoryFromProductId").val();
			var batch_no = $("#batch_no").val();
			var inv_id = $('#ProductConvertHistoryFromStatusId').val();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>product_convert_histories/get_inventory_stock_and_to_product_list',
				data: {
					'expire_date': expire_date,
					'productId': productId,
					'batch_no': batch_no,
					'inv_status': inv_id
				},
				cache: false,
				dataType: 'json',
				beforeSend: function() {
					$("#ProductConvertHistoryCurrentStockQuantity").val(0);
					$('#loading').show();
					$('#myModal').modal({
						backdrop: 'static',
						keyboard: false
					});
				},
				success: function(response) {
					//console.log(response);
					$("#ProductConvertHistoryCurrentStockQuantity").val(response[0]);

					$('#loading').hide();
					$('#myModal').modal('hide');
				}
			});
		});

	});
</script>