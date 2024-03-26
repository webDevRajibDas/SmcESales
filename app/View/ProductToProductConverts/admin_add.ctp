<style>
	#loading {
		position: absolute;
		width: auto;
		height: auto;
		text-align: center;
		top: 45%;
		left: 50%;
		display: none;
		z-index: 999;
	}

	#loading img {
		display: inline-block;
		height: 100px;
		width: auto;
	}
</style>
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
				<?php echo $this->Form->create('ProductToProductConvert', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('store_id', array('class' => 'form-control')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('from_product_type', array('class' => 'form-control', 'required' => true, 'empty' => '----- Select Type ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('from_product_id', array('class' => 'form-control chosen', 'required' => true, 'empty' => '--- Select Product ---')); ?>

				</div>
				<div class="form-group">
					<?php echo $this->Form->input('from_batch_no', array('id' => 'batch_no', 'type' => 'select', 'required' => true, 'class' => 'form-control batch_no', 'empty' => '---- Select Batch ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('from_expire_date', array('id' => 'expire_date', 'type' => 'select', 'required' => true, 'class' => 'form-control expire_date', 'empty' => '---- Select Expire Date ----')); ?>
				</div>
				<div class="form-group" id="curentinventorystock">
					<?php echo $this->Form->input('current_stock_quantity', array('class' => 'form-control', 'readonly' => true)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('to_product_type', array('class' => 'form-control', 'required' => true, 'empty' => '----- Select Type ----', 'options' => $fromProductTypes)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('to_product_id', array('class' => 'form-control chosen-other', 'required' => true, 'empty' => '--- Select Product ---')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('to_batch_no', array('id' => 'to_batch_no', 'required' => false, 'type' => 'select', 'class' => 'form-control to_batch_no chosen-other', 'empty' => '---- Select Batch ----'));
					?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('to_expire_date', array('id' => 'to_expire_date', 'required' => false, 'type' => 'select', 'class' => 'form-control to_expire_date', 'empty' => '---- Select Expire Date ----'));
					?>
				</div>
				<!-- <div class="form-group">
					<?php //echo $this->Form->input('stock_quantity', array('class' => 'form-control', 'required' => true, 'readonly' => false));
					?>
				</div> -->

				<div class="form-group" id="tocurrentinventorystock">
					<?php echo $this->Form->input('quantity', array('class' => 'form-control'));
					?>
				</div>

				<?php echo $this->Form->submit('Submit', array('id' => 'submit', 'class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="myModal"></div>
<div id="loading">
	<?php echo $this->Html->image('load.gif'); ?>
</div>

<script>
	$(document).ready(function() {

		var fromProBaseQty = '';
		var toProBaseQty = '';
		var fromProCategory = '';
		var toProCategory = '';
		$("#ProductToProductConvertStockQuantity").val('');
		$("#ProductToProductConvertCurrentStockQuantity").val('');

		$('#ProductToProductConvertFromProductType').change(function() {
			$("#ProductToProductConvertToProductType").val();
			$('#ProductToProductConvertToProductId').html('<option value=""> --- Select Product --- </option>');
			$('#batch_no').html("<option>----- Select Batch ------</option>");
			$('#expire_date').html("<option>----- Select expire date ------</option>");
			$("#ProductToProductConvertStockQuantity").val('');
			$("#ProductToProductConvertCurrentStockQuantity").val('');
			var types = $(this).val();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>product_to_product_converts/get_inventory_status_by_inv_id',
				data: {
					'types': types
				},
				cache: false,
				dataType: 'json',
				beforeSend: function() {
					$('#loading').show();
					$('#myModal').modal({
						backdrop: 'static',
						keyboard: false
					});
				},
				success: function(response) {
					//console.log(response);

					$('#ProductToProductConvertFromProductId').html('');
					$('#ProductToProductConvertFromProductId').html('<option value=""> --- Select Product --- </option>');

					$.each(response[1], function(value, key) {

						$('#ProductToProductConvertFromProductId').append($("<option></option>")
							.attr("value", value).text(key));
					});
					$(".chosen").trigger("chosen:updated");
					fromProBaseQty = response['base_measurement_unit_id'];
					fromProCategory = response['product_category_id'];
					$('#loading').hide();
					$('#myModal').modal('hide');
				}
			});
		});
		var is_maintain_batch = 1;
		var is_maintain_expire_date = 1;
		var product_category_id = 0;
		$(".chosen").chosen().change(function() {
			$("#ProductToProductConvertToProductType").val();
			$('#ProductToProductConvertToProductId').html('<option value=""> --- Select Product --- </option>');
			$('#batch_no').html("<option>----- Select Batch ------</option>");
			$('#expire_date').html("<option>----- Select expire date ------</option>");
			$("#ProductToProductConvertStockQuantity").val('');
			$("#ProductToProductConvertCurrentStockQuantity").val('');
			var product_id = $(this).val();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>admin/products/product_details',
				data: 'product_id=' + product_id,
				cache: false,
				beforeSend: function() {
					$('#loading').show();
					$('#myModal').modal({
						backdrop: 'static',
						keyboard: false
					});
					$('#batch_no').html("<option>----- Select Batch ------</option>");
				},
				success: function(response) {
					var obj = jQuery.parseJSON(response);
					var source = obj.Product.source;
					$('.source').val(source);
					product_category_id = obj.Product.product_category_id;
					if (obj.Product.maintain_batch == 0 && obj.Product.is_maintain_expire_date == 0) {
						is_maintain_batch = 0;
						is_maintain_expire_date = 0;
						$('.batch_no').val('');
						$('.batch_no').attr('disabled', true);
						$('.expire_date').val('');
						$('.expire_date').attr('disabled', true);
						$('#expire_date').trigger('change');
					} else if (obj.Product.maintain_batch == 1 && obj.Product.is_maintain_expire_date == 0) {
						maintain_batch = 1;
						is_maintain_expire_date = 0;
						$('.batch_no').val('');
						$('.batch_no').attr('disabled', false);
						$('.expire_date').val('');
						$('.expire_date').attr('disabled', true);
						$('.qty').val('');
						$('.product_qty').html('');
					} else if (obj.Product.maintain_batch == 0 && obj.Product.is_maintain_expire_date == 1) {
						is_maintain_batch = 0;
						is_maintain_expire_date = 1;
						$('.batch_no').val('');
						$('.batch_no').attr('disabled', true);
						$('.expire_date').val('');
						$('.expire_date').attr('disabled', false);
						$('.qty').val('');
						$('.product_qty').html('');
					} else {
						is_maintain_batch = 1;
						is_maintain_expire_date = 1;
						$('.batch_no').val('');
						$('.batch_no').attr('disabled', false);
						$('.expire_date').val('');
						$('.expire_date').attr('disabled', false);
						$('.qty').val('');
						$('.product_qty').html('');
					}
					$('#loading').hide();
					$('#myModal').modal('hide');
				}
			});
		});
		$('#ProductToProductConvertFromProductId').selectChain({
			target: $('#batch_no'),
			value: 'title',
			url: '<?= BASE_URL . 'product_to_product_converts/get_batch_list/1'; ?>',
			type: 'post',
			data: {
				'product_id': 'ProductToProductConvertFromProductId'
			},
			beforeSend: function() {
				$('#loading').show();
				$('#myModal').modal({
					backdrop: 'static',
					keyboard: false
				});

			},
			afterSuccess: function() {
				$('#loading').hide();
				$('#myModal').modal('hide');
			}
		});

		$('#batch_no').selectChain({
			target: $('#expire_date'),
			value: 'title',
			url: '<?= BASE_URL . 'product_to_product_converts/get_expire_date_list/1'; ?>',
			type: 'post',
			data: {
				'product_id': 'ProductToProductConvertFromProductId',
				'batch_no': 'batch_no'
			},
			beforeSend: function() {
				$('#loading').show();
				$('#myModal').modal({
					backdrop: 'static',
					keyboard: false
				});

				$('#expire_date').html("<option>----- Select expire date ------</option>");
				$("#ProductToProductConvertStockQuantity").val('');
				$("#ProductToProductConvertCurrentStockQuantity").val('');
			},
			afterSuccess: function() {
				$('#loading').hide();
				$('#myModal').modal('hide');
			}
		});

		$('#batch_no').change(function() {
			if (is_maintain_expire_date == 0) {
				$('#expire_date').trigger('change');
			}
		})

		$('#expire_date').change(function() {
			var expire_date = $(this).val();
			var ptype = $("#ProductToProductConvertFromProductType").val();
			var productId = $("#ProductToProductConvertFromProductId").val();
			var batch_no = $("#batch_no").val();
			var toproduct = 0;
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>product_to_product_converts/get_inventory_stock_and_to_product_list',
				data: {
					'ptype': ptype,
					'expire_date': expire_date,
					'productId': productId,
					'batch_no': batch_no,
					'toproduct': toproduct
				},
				cache: false,
				dataType: 'json',
				beforeSend: function() {
					$('#loading').show();
					$('#myModal').modal({
						backdrop: 'static',
						keyboard: false
					});
				},
				success: function(response) {
					//console.log(response);
					$("#ProductToProductConvertCurrentStockQuantity").val(response[0]);

					$('#loading').hide();
					$('#myModal').modal('hide');
				}
			});
		});

		$(".chosen-other").chosen();
		$('#ProductToProductConvertToProductType').change(function() {
			var fromproductId = $("#ProductToProductConvertFromProductId").val();
			if (fromproductId) {
				get_to_product_list();
			} else {
				alert("please select from product first");
				$('#ProductToProductConvertToProductType').val('');
			}
		});

		function get_to_product_list() {
			var ptype = $("#ProductToProductConvertToProductType").val();
			var productId = $("#ProductToProductConvertFromProductId").val();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>product_to_product_converts/get_to_product_list',
				data: {
					'ptype': ptype,
					'maintain_expire': is_maintain_expire_date,
					'maintain_batch': is_maintain_batch,
					'productId': productId,
					'product_category_id': product_category_id,
				},
				cache: false,
				dataType: 'json',
				beforeSend: function() {
					$('#loading').show();
					$('#myModal').modal({
						backdrop: 'static',
						keyboard: false
					});
				},
				success: function(response) {

					$('#ProductToProductConvertToProductId').html('');
					$('#ProductToProductConvertToProductId').html('<option value=""> --- Select Product --- </option>');
					$.each(response[0], function(value, key) {

						$('#ProductToProductConvertToProductId').append($("<option></option>")
							.attr("value", value).text(key));
					});
					$("#ProductToProductConvertToProductId").trigger("chosen:updated");
					$('#loading').hide();
					$('#myModal').modal('hide');

				}
			});
		}


		$('#ProductToProductConvertToProductId').selectChain({
			target: $('#to_batch_no'),
			value: 'title',
			url: '<?= BASE_URL . 'product_to_product_converts/get_batch_list'; ?>',
			type: 'post',
			data: {
				'product_id': 'ProductToProductConvertToProductId'
			},
			afterSuccess: function() {
				$("#to_batch_no").trigger("chosen:updated");
			}
		});

		$('#to_batch_no').selectChain({
			target: $('#to_expire_date'),
			value: 'title',
			url: '<?= BASE_URL . 'product_to_product_converts/get_expire_date_list'; ?>',
			type: 'post',
			data: {
				'product_id': 'ProductToProductConvertToProductId',
				'batch_no': 'to_batch_no'
			}
		});

		$('#to_expire_date').change(function() {
			var expire_date = $(this).val();
			var ptype = $("#ProductToProductConvertFromProductType").val();
			var productId = $("#ProductToProductConvertToProductId").val();
			var batch_no = $("#to_batch_no").val();
			var toproduct = 1;
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>product_to_product_converts/get_inventory_stock_and_to_product_list',
				data: {
					'ptype': ptype,
					'expire_date': expire_date,
					'productId': productId,
					'batch_no': batch_no,
					'toproduct': toproduct
				},
				cache: false,
				dataType: 'json',
				success: function(response) {
					//console.log(response);
					$("#ProductToProductConvertStockQuantity").val(response[0]);

				}
			});
		});


		$('#submit').click(function() {
			var fromQty = parseFloat($('#ProductToProductConvertCurrentStockQuantity').val());
			var conQty = parseFloat($('#ProductToProductConvertQuantity').val());
			if (conQty > fromQty) {
				alert('Quantity must be less From  Quantity.');
				$('#divLoading_default').removeClass('show');
				return false;
			}

		});

		$("#expire_date").change(function() {
			var product_id = $(".chosen").val();
			$("#curentinventorystock span").empty();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>product_to_product_converts/get_product_measurement_units_info',
				data: {
					'product_id': product_id
				},
				cache: false,
				dataType: 'json',
				success: function(response) {

					$("#ProductToProductConvertCurrentStockQuantity").after('<span ="pdmmunitname" style="margin-left: 20px;color: green;font-size: 16px;">Measurement Unit : ' + response + '</span>');

				}
			});

		});

		$("#ProductToProductConvertToProductId").change(function() {
			var product_id = $(this).val();
			$("#tocurrentinventorystock span").empty();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL; ?>product_to_product_converts/get_product_measurement_units_info',
				data: {
					'product_id': product_id
				},
				cache: false,
				dataType: 'json',
				success: function(response) {
					//console.log(response);
					$("#ProductToProductConvertQuantity").after('<span ="topdmmunitname" style="margin-left: 20px;color: green;font-size: 16px;">Measurement Unit : ' + response + '</span>');

				}
			});

		});


	});
</script>