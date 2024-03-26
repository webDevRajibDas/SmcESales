<style>
	.form-control2 {
		width: 10%;
	}

	.cyp .input.select label {
		width: 8%;
	}

	@media only screen and (max-width: 768px) {
		.form-control2 {
			width: 50%;
		}

		.cyp .input.select {
			display: inline-block;
			margin-top: 10px;
			width: 100%;
		}

		.cyp .input.select label {
			width: 35%;
		}
	}
</style>


<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Add Product'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">

				<?php echo $this->Form->create('Product', array('role' => 'form', 'enctype' => 'multipart/form-data')); ?>
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#info" aria-controls="info" role="tab" data-toggle="tab">Product Info</a></li>
					<li role="presentation"><a href="#unit" aria-controls="unit" role="tab" data-toggle="tab">Measurement Unit</a></li>
					<li role="presentation" class="fraction_slab_tab"><a href="#fraction_slab" aria-controls="fraction_slab" role="tab" data-toggle="tab">Fraction Slab</a></li>
				</ul>
				<div class="tab-content">
					<br />
					<div role="tabpanel" class="tab-pane active" id="info">
						<div class="form-group">
							<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('name_bangla', array('class' => 'form-control')); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('product_category_id', array('id' => 'category_id', 'class' => 'form-control get_product_order', 'empty' => '---- Select Category -----')); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('brand_id', array('class' => 'form-control', 'empty' => '---- Select Brand -----')); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('variant_id', array('class' => 'form-control', 'empty' => '---- Select Variant -----')); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('product_type_id', array('class' => 'form-control get_product_order', 'empty' => '---- Select Type -----')); ?>
							<?php //echo $this->Form->input('product_type_id', array("onchange"=>"gift_sample_product_fuction()", 'class' => 'form-control', 'empty' => '---- Select Type -----')); 
							?>
						</div>
						<!--div class="form-group gift_sample_product_div">
							<?php echo $this->Form->input('gift_sample_product_id', array('label' => 'Gift Sample Parent Product', 'empty' => '---- Select ----', 'class' => 'form-control mother_product chosen', 'options' => $giftsampleprodutct)); ?>
						</div-->
						<div class="form-group">
							<?php echo $this->Form->input('is_pharma', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Is Pharma :</b>', 'default' => 1)); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('maintain_batch', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Maintain Batch :</b>', 'default' => 1)); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('is_maintain_expire_date', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Is Maintain Expire Date :</b>', 'default' => 1)); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('is_distributor_product', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Is Distributor Product :</b>', 'default' => 1)); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Is Active :</b>', 'default' => 1)); ?>
						</div>

						<div class="form-group">
							<?php echo $this->Form->input('is_injectable', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Is Injectable :</b>', 'default' => 0)); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('is_virtual', array('class' => 'form-control is_virtual', 'type' => 'checkbox', 'label' => '<b>Is Virtual :</b>', 'default' => 0)); ?>
						</div>
						<div class="form-group mother_product_div">
							<?php echo $this->Form->input('parent_id', array('empty' => '---- Select ----', 'class' => 'form-control mother_product chosen', 'options' => $parent_products)); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('group_id', array('empty' => '---- Select ----', 'class' => 'form-control', 'required' => true)); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('source', array('empty' => '---- Select ----', 'class' => 'form-control')); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('order', array('class' => 'form-control', 'value' => NULL)); ?>
						</div>

						<div class="form-group cyp">
							<?php echo $this->Form->input('cyp', array('label' => 'CYP :', 'type' => 'text', 'class' => 'form-control form-control2 cyp_value')); ?>

							<?php echo $this->Form->input('cyp_cal', array('empty' => '---- Select ----',  'options' => $cyp_cals, 'label' => 'CYP Calc :', 'class' => 'form-control form-control2 cyp_operator')); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('product_image', array('type' => 'file', 'class' => 'form-control')); ?>
						</div>

					</div>


					<div role="tabpanel" class="tab-pane" id="unit">

						<div class="form-group" style="display:none;">
							<?php echo $this->Form->input('mrp', array('label' => 'MRP :', 'type' => 'text', 'class' => 'form-control')); ?>
						</div>

						<div class="form-group">
							<?php echo $this->Form->input('base_measurement_unit_id', array('class' => 'form-control base_measurement_unit_id', 'empty' => '---- Select Unit -----')); ?>
						</div>
						<span class="input_fields_wrap">

						</span>
						<div class="form-group">
							<label></label>
							<div class="add_field_button btn btn-success btn-xs">Add More Unit</div>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('sales_measurement_unit_id', array('class' => 'form-control measurement_unit_id', 'empty' => '---- Select Unit -----')); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('challan_measurement_unit_id', array('class' => 'form-control measurement_unit_id', 'empty' => '---- Select Unit -----')); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('return_measurement_unit_id', array('class' => 'form-control measurement_unit_id', 'empty' => '---- Select Unit -----')); ?>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="fraction_slab">
						<div class="row">
							<div class="col-xs-6 col-xs-offset-1">
								<p><b>Product : </b> <span class="product_name_for_fraction_slab"></span></p>
								<p><b>Sale Unit : </b> <span class="sale_unit_for_fraction_slab"></span></p>
								<p><b>Size : </b> <span class="size_for_fraction_slab"></span></p>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6 col-xs-offset-1">
								<style type="text/css">
									table {
										border: none;
										border-collapse: collapse;
									}

									table thead tr {
										background-color: #000880;
										color: #ddd;
									}

									table td {
										border-left: 1px solid #ddd;
										border-right: 1px solid #ddd;
									}

									table th {
										border-top: 1px solid #ddd !important;
									}

									table th:first-child {
										border-left: 1px solid #000880;
									}

									table th:last-child {
										border-right: 1px solid #000880;
									}

									table tr:last-child td {
										border-bottom: 1px solid #ddd !important;
									}

									table td input {
										width: 70%;
									}

									/*table td:last-child {
								    border-left: 1px solid #fff !important;
								}
								table td:nth-child (4){
								    border-right:1px solid #fff !important;
								}*/
								</style>
								<table class="table">
									<thead>
										<!-- <tr>
											<th colspan="2" class="text-center">Unit to Convert</th>
											<th colspan="2" class="text-center">Usage Unit For</th>
										</tr> -->
										<tr>
											<th class="text-center" width="25%">Sales Unit</th>
											<th class="text-center">Base Unit</th>
											<th class="text-center">Sales</th>
											<th class="text-center" width="15%">Bonus</th>
											<th class="text-center" width="10%"></th>
										</tr>
									</thead>
									<tbody class="fraction_slab">
										<tr>
											<td width="25%" class="text-center">
												<?php echo $this->Form->input('ProductFractionSlab.fraction_sales_unit.0', array('label' => false, 'required' => false, 'class' => "sales_qty")); ?>
											</td>
											<td width="25%" class="text-center">
												<?php echo $this->Form->input('ProductFractionSlab.fraction_base_unit.0', array('label' => false, 'required' => false, 'class' => "base_qty")); ?>
											</td>
											<td class="text-center">
												<?php echo $this->Form->input('ProductFractionSlab.fraction_is_sales.0', array('type' => 'checkbox', 'label' => false, 'default' => 0, 'required' => false)); ?>
											</td>
											<td class="text-center" width="15%" style="border-right: none;">
												<?php echo $this->Form->input('ProductFractionSlab.fraction_is_bonus.0', array('class' => "checkbox-inline", 'type' => 'checkbox', 'label' => false, 'default' => 0, 'required' => false)); ?>

											</td>
											<td class="text-center add_more_fraction_slab" width="10%" style="border-left: none;">
												<i class="glyphicon glyphicon-plus text-info" style="cursor:pointer"></i>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="submit">
					<?php echo $this->Form->button('Save', array('class' => 'btn btn-large btn-primary')); ?>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
<script>
	function gift_sample_product_fuction() {

		var product_type_id = $("#ProductProductTypeId").val();
		if (product_type_id == 1) {
			$(".gift_sample_product_div").hide();
		} else {
			$(".gift_sample_product_div").show();
		}

	}
	$(document).ready(function() {
		var sales_base_value = 0;
		/*$(".fraction_slab_tab").click(function(e){*/
		$("a[href='#fraction_slab']").on('shown.bs.tab', function(e) {
			e.preventDefault();
			var product_name_for_fraction = $("#ProductName").val();
			var sales_measurement_unit_for_fraction = $("#ProductSalesMeasurementUnitId option:selected").text();
			var sales_measurement_unit_id_for_fraction = $("#ProductSalesMeasurementUnitId").val();
			sales_base_value = $(".input_fields_wrap select option:selected[value='" + sales_measurement_unit_id_for_fraction + "']").parent().next().val();

			var base_measurement_unit = $("#ProductBaseMeasurementUnitId option[selected]").text();
			if (!product_name_for_fraction) {
				alert('No product name ');
				$('.nav-tabs a[href="#info"]').tab('show');
				$(".product_name_for_fraction_slab").text('');
				$(".sale_unit_for_fraction_slab").text('');
				$(".size_for_fraction_slab").text('');
			} else if (!sales_measurement_unit_for_fraction) {
				alert('Select Sales Measurement unit first');
				$('.nav-tabs a[href="#unit"]').tab('show');
				$(".product_name_for_fraction_slab").text('');
				$(".sale_unit_for_fraction_slab").text('');
				$(".size_for_fraction_slab").text('');
				$('.nav-tabs a[href="#unit"]').tab('show');
			} else if (!sales_base_value) {
				alert('Please fill up Measurement Value');
				$('.nav-tabs a[href="#unit"]').tab('show');
				$(".product_name_for_fraction_slab").text('');
				$(".sale_unit_for_fraction_slab").text('');
				$(".size_for_fraction_slab").text('');
			} else {
				$(".product_name_for_fraction_slab").text(product_name_for_fraction);
				$(".sale_unit_for_fraction_slab").text(sales_measurement_unit_for_fraction);
				$(".size_for_fraction_slab").text(sales_base_value + " " + base_measurement_unit + "/" + sales_measurement_unit_for_fraction);
			}
		});
		$('body').on('change', ".sales_qty", function() {
			var sales_qty = $(this).val();
			if (sales_qty >= 1) {
				alert("Cannot entry more than 1");
				$(this).val('');
				return true;
			}
			/*var selected_stock_qty = $(".sales_qty").map(function() {
               return $(this).val();
            }).get();
            */
			var base_qty = Math.round(sales_qty * sales_base_value);
			$(this).parent().parent().next().find('.base_qty').val(base_qty);
		});

		$('body').on('change', ".base_qty", function() {
			var base_qty = $(this).val();
			var dec = 2;
			var sales_qty = base_qty / sales_base_value;
			sales_qty = sales_qty * Math.pow(10, dec);
			sales_qty = parseInt(sales_qty);
			sales_qty = sales_qty / Math.pow(10, dec);
			$(this).parent().parent().prev().find('.sales_qty').val(sales_qty);
		});

		var fraction_slab = 1;
		$(".add_more_fraction_slab").click(function() {

			var sales_unit = $(".fraction_slab tr:last-child").find('td:nth-child(1) input').val();
			var base_unit = $(".fraction_slab tr:last-child").find('td:nth-child(2) input').val();
			var is_sales = $(".fraction_slab tr:last-child").find('td:nth-child(3) input[type="checkbox"]').is(":checked");
			var is_bonus = $(".fraction_slab tr:last-child").find('td:nth-child(4) input[type="checkbox"]').is(':checked');
			if (sales_unit && base_unit && (is_sales || is_bonus)) {
				$(".fraction_slab").append('<tr>\
								<td width="25%" class="text-center">\
									<div class="input text"><input name="data[ProductFractionSlab][fraction_sales_unit][' + fraction_slab + ']" type="text" id="ProductFractionSlabFractionSalesUnit" class="sales_qty" /></div> \
								</td>\
								<td width="25%" class="text-center">\
									<div class="input text"><input name="data[ProductFractionSlab][fraction_base_unit][' + fraction_slab + ']" type="text" id="ProductFractionSlabFractionBaseUnit" class="base_qty" /></div> \
								</td>\
								<td class="text-center">\
									<div class="input checkbox"><input type="hidden" name="data[ProductFractionSlab][fraction_is_sales][' + fraction_slab + ']" id="ProductFractionSlabFractionIsSales' + fraction_slab + '_" value="0"/><input type="checkbox" name="data[ProductFractionSlab][fraction_is_sales][' + fraction_slab + ']"  value="1" id="ProductFractionSlabFractionIsSales' + fraction_slab + '"/></div>\
								</td>\
								<td class="text-center" width="15%" style="border-right: none;">\
									<div class="input checkbox"><input type="hidden" name="data[ProductFractionSlab][fraction_is_bonus][' + fraction_slab + ']" id="ProductFractionSlabFractionIsBonus' + fraction_slab + '_" value="0"/><input type="checkbox" name="data[ProductFractionSlab][fraction_is_bonus][' + fraction_slab + ']"  class="checkbox-inline" value="1" id="ProductFractionSlabFractionIsBonus' + fraction_slab + '"/></div>\
								</td>\
								<td class="text-center remove_fraction_slab" width="10%" style="border-left: none;">\
									<i class="glyphicon glyphicon-remove text-danger" style="cursor:pointer"></i>\
								</td>\
							</tr>');
				fraction_slab++;

				$("input[type='checkbox']").iCheck({
					checkboxClass: 'icheckbox_minimal',
					radioClass: 'iradio_minimal'
				});
			} else {
				alert("Please fill all information");
			}

		});

		$(".fraction_slab").on("click", ".remove_fraction_slab", function(e) {
			e.preventDefault();
			$(this).parent('tr').remove();
		});
		var max_fields = 10;
		var x = 1;
		$(".add_field_button").click(function() {
			if (x < max_fields) {
				x++;
				$(".input_fields_wrap").append('<div class="slap_set"><div class="form-group"><label>Unit :</label><select style="width:15% !important;" class="form-control unit" name="data[ProductMeasurement][measurement_unit_id][]" required><option value="">---- Select Unit -----</option><?php echo $units; ?></select> <input style="margin-left:2%;width:13% !important;" class="form-control" name="data[ProductMeasurement][qty_in_base][]" type="number" required> <a href="#" class="remove_field btn btn-danger btn-xs">X</a></div></div>');
			}
		});

		$(".input_fields_wrap").on("click", ".remove_field", function(e) {
			e.preventDefault();
			$(this).parent('div').remove();
			x--;
			clear_field();
		});


		$(document).on('change', '.base_measurement_unit_id', function() {
			$('.measurement_unit_id').val('');
			$('.unit').val('');
		});

		$(document).on('change', '.unit', function() {
			clear_field();
			var unit_id = $(this).val();
			var base_measurement_unit_id = $('.base_measurement_unit_id').val();
			if (base_measurement_unit_id == unit_id) {
				alert('Unit should be different from Base Unit.');
				$(this).val('');
				return false;
			}
		});

		$(document).on('change', '.measurement_unit_id', function() {
			var measurement_unit_id = $(this).val();
			var base_measurement_unit_id = $('.base_measurement_unit_id').val();

			var unit_array = $(".unit").map(function() {
				return $(this).val() ? $(this).val() : null;
			}).get();

			var unit_check = $.inArray(measurement_unit_id, unit_array) != -1;
			if (unit_check == false && measurement_unit_id != base_measurement_unit_id && measurement_unit_id != '') {
				alert('Unit did not match');
				$(this).val('');
				return false;
			}
		});
		/* get product order number by category */
		$(".get_product_order").change(function(e) {
			e.preventDefault();
			var reqData;
			if ($(this).attr('id') == 'category_id') {
				reqData = {
					CategoryId: $(this).val()
				}
			} else if ($(this).attr('id') == 'ProductProductTypeId') {
				reqData = {
					ProductTypeId: $(this).val(),
					CategoryId: $('#category_id').val()
				}
			}
			if (reqData.CategoryId) {
				$.ajax({
					type: 'POST',
					data: reqData,
					dataType: 'text',
					url: '<?= BASE_URL . 'products/get_product_order' ?>',
					success: function(response) {
						//console.log(response);
						//return;
						response = $.parseJSON(response);
						$('#ProductOrder').val(response.order);
					}
				});
			} else {
				$(this).val('');
				alert('Please select category first!');
			}
		});
		$('.cyp_value,.cyp_operator').change(function() {
			set_cyp_validation();
		});
		set_cyp_validation();

		function set_cyp_validation() {
			var cyp_val = $('.cyp_value').val();
			var cyp_operator = $('.cyp_operator').val();
			if (cyp_val) {
				$('.cyp_operator').attr('required', true);
			}

			if (cyp_operator) {
				$('.cyp_value').attr('required', true);
			}
			if (!cyp_val && !cyp_operator) {
				$('.cyp_operator').attr('required', false);
				$('.cyp_value').attr('required', false);
			}
		}

		function clear_field() {
			$('.measurement_unit_id').val('');
		}

		/* is virtual product check */

		$(".chosen").chosen();
		$(".mother_product_div").hide();
		$(".is_virtual").on('ifChecked', function(e) {
			$(".mother_product_div").show();
		});
		$(".is_virtual").on('ifUnchecked', function(e) {
			$(".mother_product_div").hide();
		});


	});
</script>