<?php
// pr($this->params);
// echo $this->params['url']['csa'];
echo $this->Html->css('select2/select2');
?>
<style>
	.width_100_this {
		width: 100%;
	}

	.display_none {
		display: none;
	}

	.width_150 {
		width: 100%;
	}

	.width_100 {
		width: 100%;
	}

	input[type=number]::-webkit-inner-spin-button,
	input[type=number]::-webkit-outer-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	.bonus {
		width: 130px !important;
	}

	.product_unit_name {
		width: 80px !important;
	}

	.product_id {
		width: 150px !important;
	}

	.open_bonus_product_id {
		width: 150px !important;
	}

	.policy_bonus_product_id {
		width: 150px !important;
	}

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

	.bonus_dis_row {
		display: none;
	}

	option:disabled {
		color: #999;
	}

	.chosen-container .chosen-results li {
    	font-weight: 600 !important;
    	text-align: left !important;
   }

</style>

<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<?php /*?><p><a onclick="setBonusDiscount(1057, 'bonus')" class="btn btn-default btn-xs">Bonus</a>&nbsp;&nbsp;&nbsp;<a onclick="setBonusDiscount(1057, 'discount')" class="btn btn-primary btn-xs">Discount</a></p><?php */ ?>
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Create Memo'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Memo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<?php echo $this->Form->create('Memo', array('role' => 'form')); ?>
				<div class="form-group">
					<?php
					if ($office_parent_id == 0) {
						echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'rowUpdate(0);', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----'));
					} else {
						echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id'));
					}
					?>
				</div>

				<div class="form-group">
				<?php if ($csa == 1) {
					echo $this->Form->input('sale_type_id', array('class' => 'form-control sale_type_id', 'onChange' => 'rowUpdate(0);', 'id' => 'sale_type_id', 'selected' => 2, 'options' => $sale_type_list));
					echo $this->Form->input('csa_id', array('class' => 'form-control ', 'id' => 'csa_id', 'empty' => '--- Select Csa ---', 'required' => 'required'));
				} else {
						echo $this->Form->input('sale_type_id', array('class' => 'form-control sale_type_id', 'onChange' => 'rowUpdate(0);', 'id' => 'sale_type_id', 'options' => $sale_type_list));
				} 
				?>
				</div>
				
				
				
				<div class="form-group">
					<div class="input select required">
						<label for="program_officer_id">Program Officer :</label>
						<?php if( $usergroupid != 1016 ){ ?>
						<select name="program_officer_id" id="program_officer_id" class="form-control select2auto" data-parent="0" data-limit="30" data-route="GetProgramOfficerId" data-allowClear="false" data-placeholder="Select a Program Officer!">
							<option value=""></option>
						</select>
						<?php }else{ ?>
							<input type="hidden"  name="program_officer_id" value="<?=$this->UserAuth->getUserId();?>">
							<input type="text" style="width:30%;" id="program_officer_id" readonly value="<?php echo $this->UserAuth->getUserName(); ?>">
						<?php } ?>
						
					</div>
				</div>
				
				
				
				<div class="form-group required" id="spo_territory_id_div" style="display:none;">
					<?php echo $this->Form->input('spo_territory_id', array('id' => 'spo_territory_id', 'class' => 'form-control spo_territory_id', 'onChange' => 'rowUpdate(1);', 'required' => false, 'empty' => '---- Select SPO Territory ----', 'options' => $spo_territories)); ?>
				</div>


				<div class="form-group" id="territory_id_div">
					<?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'required' => TRUE, 'empty' => '---- Select Territory ----', 'options' => $territories)); ?>
				</div>
				
				<?php //if ($csa == 1) { ?>
					<div class="form-group">
						<?php echo $this->Form->input('thana_filter_id', array('class' => 'form-control ', 'label'=>'Thana', 'id' => 'thana_id', 'empty' => '--- Select Thana ---', 'required' => 'required')); ?>
					</div>
				<?php //} ?>


				<div class="form-group" id="market_id_so">
					<?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'required' => TRUE, 'empty' => '---- Select Market ----')); ?>
				</div>

				<div class="form-group" id="outlet_id_so">
					<?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'onChange' => 'rowUpdate(1);', 'required' => TRUE, 'empty' => '---- Select Outlet ----', 'options' => $outlets)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('entry_date', array('class' => 'form-control datepicker', 'value' => (isset($this->request->data['Memo']['entry_date']) == '' ? $current_date : $this->request->data['Memo']['entry_date']), 'required' => TRUE)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('memo_date', array('class' => 'form-control datepicker', 'value' => $current_date, 'onChange' => 'rowUpdate(1);', 'type' => 'text', 'required' => TRUE)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('memo_no', array('class' => 'form-control memo_no', 'required' => TRUE, 'type' => 'text', 'value' => $generate_memo_no, 'readonly')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('memo_reference_no', array('class' => 'form-control memo_reference_no', 'maxlength' => '15', 'required' => false, 'type' => 'text')); ?>
				</div>

				<div class="table-responsive">
					<!--Set Product area-->
					<table class="table table-striped table-condensed table-bordered invoice_table">
						<thead>
							<tr>
								<th class="text-center" width="5%">ID</th>
								<th class="text-center">Product Name</th>
								<th class="text-center" width="12%">Unit</th>
								<th class="text-center" width="12%">Price</th>
								<th class="text-center" width="12%">QTY</th>
								<th class="text-center" width="12%">Value</th>
								<th class="text-center" width="12%">Discount Value</th>
								<th class="text-center" width="10%">Bonus</th>
								<th class="text-center" width="10%">Action</th>
							</tr>
						</thead>
						<tbody class="product_row_box">
							<tr id="1" class="new_row_number">
								<th class="text-center sl_memo" width="5%">1</th>
								<th class="text-center" id="memo_product_list">
									<?php
									echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 product_id chosen', 'empty' => '---- Select Product ----', 'label' => false, 'required' => true));
									?>
									<input type="hidden" class="product_id_clone" />
									<input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 product_category_id" />
								</th>
								<th class="text-center" width="12%">
									<input type="text" name="" class="form-control width_100 product_unit_name" disabled />
									<input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id" />
								</th>
								<th class="text-center" width="12%">
									<input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate" readonly />
									<input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id" />
								</th>



								<th class="text-center" width="12%">
									<input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 min_qty" required />
									<input type="hidden" class="combined_product" />
									<select class="fraction_sales_slab hide"></select>
								</th>
								<th class="text-center" width="12%">
									<input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly />
								</th>


								<th class="text-center" width="12%">
									<input type="text" name="data[MemoDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />
									<input type="hidden" name="data[MemoDetail][disccount_type][]" class="form-control width_100 disccount_type" />
									<input type="hidden" name="data[MemoDetail][policy_type][]" class="form-control width_100 policy_type" />
									<input type="hidden" name="data[MemoDetail][policy_id][]" class="form-control width_100 policy_id" />
									<input type="hidden" name="data[MemoDetail][is_bonus][]" class="form-control width_100 is_bonus" />
								</th>

								<th class="text-center" width="10%">
									<input type="text" class="form-control width_100 bonus" disabled />
									<input type="hidden" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id" />
									<input type="hidden" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty" />
									<input type="hidden" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id" />
								</th>
								<th class="text-center" width="10%">
									<a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>
								</th>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5" align="right"><b>Total : </b></td>
								<td align="center"><input name="data[Memo][gross_value]" class="form-control width_100" type="text" id="gross_value" value="0" readonly />
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td colspan="5" align="right"><b>Total Discount: </b></td>
								<td align="center"><input name="data[Memo][total_discount]" class="form-control width_100 total_discount" type="text" id="total_discount" value="0" readonly />
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td colspan="5" align="right"><b>Net Payable: </b></td>
								<td align="center"><input name="data[Memo][net_payable]" class="form-control width_100 net_payable" type="text" id="net_payable" value="0" readonly />
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td colspan="5" align="right"><b>Cash Collection : </b></td>
								<td align="center"><input name="data[Memo][cash_recieved]" class="form-control width_100" type="text" id="cash_collection" required />
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>

							<tr>
								<td colspan="4">
									<a class="btn btn-primary btn-xs show_bonus" data-toggle="modal" data-target="#bonus_product"><i class="glyphicon glyphicon-plus"></i>Bonus</a>
									<div id="bonus_product" class="modal fade" role="dialog">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title">Policy Bonus</h4>
												</div>
												<div class="modal-body">
													<div id="loading" class="m_loading">
														<?php echo $this->Html->image('load.gif'); ?>
													</div>
													<table class="table-bordered">
														<thead>
															<tr>
																<th class="text-left">Product Name</th>
																<th class="text-center" width="12%">Unit</th>
																<th class="text-center" width="12%">QTY</th>
																<th class="text-center" width="10%"></th>
															</tr>
														</thead>
														<tbody class="bonus_product">
															<?php /*?><tr class="bonus_row">
															<th class="text-center" id="bonus_product_list">
																<?php
																echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----', 'label' => false));
																?>
																<input type="hidden" class="product_id_clone" />
																<input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
															</th>
															<th class="text-center" width="12%">
																<input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>
																<input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>
	
																<input type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
																<input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
															</th>
															<th class="text-center" width="12%">
																<input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" />
																
															<input type="hidden" name="data[MemoDetail][discount_amount][]" />
															<input type="hidden" name="data[MemoDetail][disccount_type][]"/>
															<input type="hidden" name="data[MemoDetail][policy_type][]"/>
															<input type="hidden" name="data[MemoDetail][policy_id][]"/>
															<input type="hidden" name="data[MemoDetail][is_bonus][]"/>
																
																<select class="fraction_bonus_slab hide"></select>
																<input type="hidden" class="combined_product"/>
															</th>
															<th class="text-center" width="10%">
																<a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
															</th>
														</tr><?php */ ?>
														</tbody>
													</table>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
												</div>
											</div>
										</div>
									</div>
								</td>
								<td align="right"><b>Credit : </b></td>
								<td align="center"><input name="data[Memo][credit_amount]" class="form-control width_100" type="text" id="credit_amount" readonly />
								</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>

						</tfoot>
					</table>
				</div>

				<div class="form-group" style="padding-top:20px;">
					<div class="pull-right">
						<?php //echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div' => false, 'name' => 'save')); ?>
						<?php echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft')); ?>
					</div>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>


</div>




<div class="modal" id="myModal"></div>
<div id="loading">
	<?php echo $this->Html->image('load.gif'); ?>
</div>

<?php echo $this->Html->script('select2/js/select2.min'); ?>
<?php echo $this->Html->script('select2/select2'); ?>
<script>
	$(document).ready(function() {
		$("#office_id").prop("selectedIndex", 0);
		$("#sale_type_id").prop("selectedIndex", 0);
		$("#territory_id").prop("selectedIndex", 0);
		$("#spo_territory_id").prop("selectedIndex", 0);
		$("#market_id").prop("selectedIndex", 0);
		$("#outlet_id").prop("selectedIndex", 0);
		$(".memo_reference_no").val('');
		/* $(".show_bonus").click(function(){
			$('#bonus_product').toggle(400);
		});
		$('#bonus_product').hide();*/
		
		
		
	});
</script>




<script>
	var special_groups = [];
	var outlet_category_id;

	function productList(sl) {
		var csa_id = 0
		var territory_id = $('#territory_id').val();
		var outlet_id = $('#outlet_id').val();
		var memo_date = $('#MemoMemoDate').val();
		var sale_type_id = $("#sale_type_id option:selected").val();

		if (sale_type_id == 3) {
			territory_id = $("#spo_territory_id option:selected").val();
		}
		if (sale_type_id == 2) {
			csa_id = $("#csa_id option:selected").val();
		}
		
		if( sale_type_id == 4 ){
			var spoterritory_id = $('.spo_territory_id').val();
			if(spoterritory_id != ''){
				territory_id = spoterritory_id;
			}
		}

		if (territory_id) {
			$.ajax({
				type: "POST",
				url: '<?= BASE_URL . 'admin/Memos/get_product' ?>',
				data: {
					'territory_id': territory_id,
					'csa_id': csa_id,
					'outlet_id': outlet_id,
					'memo_date': memo_date
				},
				cache: false,
				success: function(response) {
					var json = $.parseJSON(response);
					$('.product_id option').remove();
					for (var i = 0; i < json.length; ++i) {
						$('.product_id').append('<option value="' + json[i].id + '">' + json[i].name + '</option>');
					}
					
					$('#'+sl+ ' select.product_id.chosen').chosen('destroy');
					$('#'+sl+ ' select.product_id.chosen').chosen();
					$('#'+sl+ ' .product_id.chosen').trigger("chosen:updated");
					
				}
			});
		}
	};

	function get_special_group() {
		var outlet_id = $('#outlet_id').val();
		var memo_date = $('#MemoMemoDate').val();
		var office_id = $('#office_id').val();
		var territory_id = $('#territory_id').val();
		if (outlet_id) {
			$.ajax({
				type: "POST",
				url: '<?= BASE_URL . 'memos/get_spcial_group_and_outlet_category_id' ?>',
				data: {
					'outlet_id': outlet_id,
					'memo_date': memo_date,
					'office_id': office_id,
					'territory_id': territory_id,
				},
				cache: false,
				success: function(response) {
					var res = $.parseJSON(response);
					outlet_category_id = res.outlet_category_id;
					special_groups = res.special_group_id;
				}
			});
		}
	}

	function rowUpdate(productLit) {
		sl = 1;
		product_list = '<div class="input select"><select id="MemoProductId" required="required" class="form-control width_100 product_id chosen" name="data[MemoDetail][product_id][]"><option value="">---- Select Product ----</option></select></div><input type="hidden" class="product_id_clone"><input type="hidden" class="form-control width_100 product_category_id" name="data[MemoDetail][product_category_id][]">';



		var current_row =
			'<th class="text-center sl_memo" width="5%">1</th>\
	<th id="memo_product_list" class="text-center">' + product_list + '</th>\
	<th class="text-center" width="12%">\
		<input type="text" name="" class="form-control width_100 product_unit_name" disabled/>\
		<input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>\
	</th>\
	<th class="text-center" width="12%">\
		<input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate" readonly/>\
		<input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/>\
		\
	</th>\
	<th>\
		<input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 min_qty" value="" required/>\
		<input type="hidden" name="data[MemoDetail][combination_id][]" step="any" class="combination_id" value=""/>\
		<input type="hidden" class="combined_product"/>\
		<select class="fraction_sales_slab hide"></select>\
	</th>\
	<th class="text-center" width="12%">\
		<input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly/>\
	</th>\
	<th class="text-center" width="12%">\
		<input type="text"   name="data[MemoDetail][discount_value][]" class="form-control width_100 discount_value" readonly />\
		<input type="hidden" name="data[MemoDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />\
		<input type="hidden" name="data[MemoDetail][disccount_type][]" class="form-control width_100 disccount_type"/>\
		<input type="hidden" name="data[MemoDetail][policy_type][]" class="form-control width_100 policy_type"/>\
		<input type="hidden" name="data[MemoDetail][policy_id][]" class="form-control width_100 policy_id"/>\
		<input type="hidden" name="data[MemoDetail][is_bonus][]" class="form-control width_100 is_bonus"/>\
	</th>\
	<th class="text-center" width="10%">\
		<input type="text" id="bonus" class="form-control width_100 bonus" disabled />\
		<input type="hidden" id="bonus_product_id" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>\
		<input type="hidden" id="bonus_product_qty" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>\
		<input type="hidden" id="bonus_measurement_unit_id" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>\
	</th>\
	<th class="text-center" width="10%"><a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a></th>';

		$('.product_row_box').html('<tr id="1" class=new_row_number>' + current_row + '</tr>');

		$('#gross_value').val(0);
		//$('.memo_no').val('');

		if (productLit == 1) {
			productList(sl);
			get_special_group();
		} else {
			$("#territory_id").prop("selectedIndex", 0);
		}
	}

	$(document).ready(function() {
		$('.office_id').selectChain({
			target: $('.territory_id'),
			value: 'name',
			url: '<?= BASE_URL . 'sales_people/get_territory_list_memo' ?>',
			type: 'post',
			data: {
				'office_id': 'office_id'
			}
		});

		$('.office_id').selectChain({
			target: $('.spo_territory_id'),
			value: 'name',
			url: '<?= BASE_URL . 'sales_people/get_spo_territory_list' ?>',
			type: 'post',
			data: {
				'office_id': 'office_id'
			}
		});

		$('.spo_territory_id').selectChain({
			target: $('.territory_id'),
			value: 'name',
			url: '<?= BASE_URL . 'sales_people/get_child_territory_list'; ?>',
			type: 'post',
			data: {
				'spo_territory_id': 'spo_territory_id'
			}
		});

		/*Program officer scope*/
		
		$("#office_id").trigger("change");
		$('#program_officer_id').parents(".form-group:first").css('display','none');
		$("#program_officer_id").prop("disabled", true);
		//$("#sale_type_id").trigger("change");
		$('.sale_type_id').change(function() {
			/*Program officer scope*/
			var selected_val = $(this).val();
			if(selected_val==4){
				$('#program_officer_id').parents(".form-group:first").css('display','block');
				$("#program_officer_id").prop("disabled", false);
			}else{
				$('#program_officer_id').parents(".form-group:first").css('display','none');
				$("#program_officer_id").prop("disabled", true);
			}
			/*End Program officer scope*/
			if (selected_val == 3 || selected_val == 4) {
				$('#spo_territory_id_div').show();
				if(selected_val == 4){
					$('#spo_territory_id_div').removeClass('required');
					$("#spo_territory_id").prop('required', false);
				}else{
					$('#spo_territory_id_div').addClass('required');
					$("#spo_territory_id").prop('required', true);
				}
			} else {
				$('#office_id').trigger('change');
				$('#spo_territory_id_div').hide();
				$("#spo_territory_id").prop('required', false);

			}
		});
		//end for spo

		$('.territory_id').selectChain({
			target: $('.market_id'),
			value: 'name',
			url: '<?= BASE_URL . 'admin/doctors/get_market'; ?>',
			type: 'post',
			data: {
				'territory_id': 'territory_id'
			}
		});

		$('.market_id').selectChain({
			target: $('.outlet_id'),
			value: 'name',
			url: '<?= BASE_URL . 'admin/doctors/get_outlet'; ?>',
			type: 'post',
			data: {
				'market_id': 'market_id'
			}
		});

		$('.office_id').change(function() {
			$('.market_id').html('<option value="">---- Select Market ----');
			$('.outlet_id').html('<option value="">---- Select Outlet ----');
			var office_id = $(this).val();
			$('#program_officer_id').attr('data-parent',office_id);
		});

		$('.territory_id').change(function() {
			$('.outlet_id').html('<option value="">---- Select Outlet ----');
		});

		/* temporary commented */
		/*
		$("body").on("change", "#sales_person_id", function () {
			var sales_person_id = $(this).val();
			$.ajax({
				url: '<?= BASE_URL . 'memos/get_territory_id' ?>',
				type: 'POST',
				data: {sales_person_id: sales_person_id},
				success: function (response) {
					var obj = jQuery.parseJSON(response);
					if (obj.territory_id != null) {
						$("#territory_id").val(obj.territory_id);
					} else {
						alert('Territory Id not be Null');
						return false;
					}

				}

			});

		});
		*/
	});
</script>

<script>
	function total_values() {
		var t = 0;
		$('.total_value').each(function() {
			if ($(this).val() != '') {
				t += parseFloat($(this).val());
			}
		});
		$('#gross_value').val(t);
		
		var net_payable_val =  parseFloat(t - $("#total_discount").val());
		
		$('#net_payable').val(net_payable_val);
		$('#cash_collection').val(parseFloat(t));
		//console.log('check value');
		
	}

	$(document).ready(function() {
		$("body").on("click", ".add_more", function() {
			
			//var sl = $('.invoice_table>tbody>tr').length + 1;
			
			var sl = parseInt($('.invoice_table>tbody tr:last').attr('id')) + 1;
			

			var select_option_list = $('#memo_product_list .product_id').html();

			var product_list = '<div class="input select"><select id="MemoProductId" required="required" class="form-control width_100 product_id chosen" name="data[MemoDetail][product_id][]">'+ select_option_list +'</select></div><input type="hidden" class="product_id_clone"><input type="hidden" class="form-control width_100 product_category_id" name="data[MemoDetail][product_category_id][]">';
			
			//var product_list = $('#memo_product_list').html();

			var product_box = $(this).parent().parent().parent();
			var current_row_no = $(this).parent().parent().attr('id');
			//alert(current_row_no);

			var current_row =
				'<th class="text-center sl_memo" width="5%"></th>\
		<th class="text-center">' + product_list + '</th>\
		<th class="text-center" width="12%">\
			<input type="text" name="" class="form-control width_100 product_unit_name" disabled/>\
			<input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>\
		</th>\
		<th class="text-center" width="12%">\
			<input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate readonly" readonly/>\
			<input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/>\
		</th>\
		<th>\
			<input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" value="" class="form-control width_100 min_qty" required/>\
			<input type="hidden" name="data[MemoDetail][combination_id][]" step="any" class="combination_id" value=""/>\
			<input type="hidden" class="combined_product"/><select class="fraction_sales_slab hide"></select></th><th class="text-center" width="12%">\
			<input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly/>\
		</th>\
		<th class="text-center" width="12%">\
			<input type="text"   name="data[MemoDetail][discount_value][]" class="form-control width_100 discount_value" readonly />\
			<input type="hidden" name="data[MemoDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />\
			<input type="hidden" name="data[MemoDetail][disccount_type][]" class="form-control width_100 disccount_type"/>\
			<input type="hidden" name="data[MemoDetail][policy_type][]" class="form-control width_100 policy_type"/>\
			<input type="hidden" name="data[MemoDetail][policy_id][]" class="form-control width_100 policy_id"/>\
			<input type="hidden" name="data[MemoDetail][is_bonus][]" class="form-control width_100 is_bonus"/>\
		</th>\
		<th class="text-center" width="10%">\
			<input type="text" id="bonus" class="form-control width_100 bonus" disabled />\
			<input type="hidden" id="bonus_product_id" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>\
			<input type="hidden" id="bonus_product_qty" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>\
			<input type="hidden" id="bonus_measurement_unit_id" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>\
		</th>\
		<th class="text-center" width="10%">\
			<a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>\
			<a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>\
		</th>';


			var valid_row = $('#' + current_row_no + '>th>.product_rate').val();
			if (valid_row != '') {
				product_box.append('<tr id=' + sl + ' class=new_row_number>' + current_row + '</tr>');
				$('#' + sl + '>.sl_memo').text(sl);

				$('#'+sl+ ' select.product_id.chosen').chosen('destroy');
				$('#'+sl+ ' select.product_id.chosen').chosen();
				$('#'+sl+ ' .product_id.chosen').trigger("chosen:updated");

				$(this).hide();
			} else {
				alert('Please fill up this row!');
			}
		});
		$("body").on("change", ".product_id", function() {
			$('#myModal').modal('show');
			$('#loading').show();
			$('#gross_value').val(0);
			var sl = $('.invoice_table>tbody>tr').length;
			var current_row_no = $(this).parent().parent().parent().attr('id');
			if ($('#' + current_row_no + '>th>.product_rate').val() == '') {
				$('#' + current_row_no + '>th>.bonus').val('N.A');
				$('#' + current_row_no + '>th>.bonus_product_id').val(0);
				$('#' + current_row_no + '>th>.bonus_product_qty').val(0);
				$('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
			}
			var product_change_flag = 1;
			var product_id_list = '';
			
			var product_arr1 = [];
			
			$('.product_id').each(function() {
				
				if ($(this).val() != '') {
					
					
					
					//if (product_id_list.search($(this).val()) == -1) {
						
					if($.inArray($(this).val(), product_arr1) === -1) {
						
						product_arr1.push( $(this).val() );
						
						product_id_list = $(this).val() + ',' + product_id_list;
						
					
					} else {
						alert("This product already exists");
						product_change_flag = 0;
						$('#' + current_row_no + '>th>div>select').val($('#' + current_row_no + '>th>.product_id_clone').val());
						if ($('#' + current_row_no + '>th>.product_rate').val() == '') {
							$(this).val('').attr('selected', true);
							$('#' + current_row_no + '>th>.bonus').val('');
						}

						$('#'+current_row_no+ ' .product_id.chosen').trigger("chosen:updated");

						total_values();
						new_product = 0;
						$('#myModal').modal('hide');
						$('#loading').hide();
						return false;
					}

				} else {

					pro_val = $('.product_row_box tr#' + current_row_no + ' .product_id').val();

					if (pro_val) {
						alert("Please select any product from last row or remove it!");
					} else {
						alert("Please select any product!");
					}

					product_change_flag = 0;
					$('#' + current_row_no + '>th>div>select').val($('#' + current_row_no + '>th>.product_id_clone').val());
					if ($('#' + current_row_no + '>th>.product_rate').val() == '') {
						$(this).val('').attr('selected', true);
						$('#' + current_row_no + '>th>.bonus').val('');
					}
					total_values();
					new_product = 0;
					$('#myModal').modal('hide');
					$('#loading').hide();
					
					$('#'+current_row_no+ ' .product_id.chosen').trigger("chosen:updated");
					
					return false;
				}
			});

			var product_id = $(this).val();
			var product_box = $(this).parent().parent().parent();
			var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
			var product_unit_id = product_box.find("th:nth-child(3) .product_unit_id");
			var product_rate = product_box.find("th:nth-child(4) .product_rate");
			var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
			var product_qty = product_box.find("th:nth-child(5) .min_qty");
			var total_val = product_box.find("th:nth-child(6) .total_value");
			var combined_product = product_box.find("th:nth-child(5) .combined_product");
			var combined_product_change = combined_product.val();
			var memo_date = $('#MemoMemoDate').val();
			var outlet_id = $('#outlet_id').val();
			var rate_class = product_rate.attr('class').split(' ').pop();
			var value_class = total_val.attr('class').split(' ').pop();

			if (rate_class.lastIndexOf('-') && value_class.lastIndexOf('-') > -1) {
				product_rate.removeClass(rate_class);
				total_val.removeClass(value_class);
				/*-----------*/
				product_rate.addClass('prate-' + product_id);
				total_val.addClass('tvalue-' + product_id);
			} else {
				product_rate.addClass('prate-' + product_id);
				total_val.addClass('tvalue-' + product_id);
			}
			if ($('#sale_type_id').val() == 3) {
				var territory_id = $('.spo_territory_id').val();
			} else {
				var territory_id = $('.territory_id').val();
			}
			
			if( $('#sale_type_id').val() == 4 ){
				var spoterritory_id = $('.spo_territory_id').val();
				if(spoterritory_id != ''){
					territory_id = spoterritory_id;
				}
			}
			
			$.ajax({
				url: '<?= BASE_URL . 'memos/get_product_unit' ?>',
				type: 'POST',
				data: {
					product_id: product_id,
					territory_id: territory_id,
					product_id_list: product_id_list,
					memo_date: memo_date,
					outlet_id: outlet_id
				},
				success: function(result) {
					var obj = jQuery.parseJSON(result);
					product_unit.val(obj.product_unit.name);
					product_unit_id.val(obj.product_unit.id);
					var total_qty = obj.total_qty;

					product_qty.val('');
					product_box.find("th:nth-child(6) input").val('');
					product_box.find("th:nth-child(8) input").val('');
					$('#' + current_row_no + '>th>.min_qty').attr('max', total_qty);
					$('#' + current_row_no + '>th>.product_rate').val('0.00');
					$('.add_more').removeClass('disabled');
					$('#loading').hide();
					$('#myModal').modal('hide');
				}
			});
		});
	});
</script>
<script>
	var selected_bonus = [];
	var selected_set = [];
	var selected_policy_type = [];
	var selected_option_id = [];
	var other_policy_info = [];
	
	$("body").on("keyup", ".min_qty", function() {
		var current_row_no = $(this).parent().parent().attr('id');
		var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();

		var product_wise_qty = {};
		var product_wise_qty_value = {};
		$('.product_id').each(function(index, value) {
			var producct_box_each = $(this).parent().parent().parent();
			if (producct_box_each.find("th:nth-child(5) .min_qty").val()) {
				product_wise_qty[$(this).val()] = producct_box_each.find("th:nth-child(5) .min_qty").val();
			}
			//var producct_box_eachvalue = $(this).parent().parent().parent();
		});
		
		pro_val = $('.product_row_box tr#' + current_row_no + ' .product_id').val();
		var sl = $('.invoice_table>tbody>tr').length;
		var product_box = $(this).parent().parent();
		var product_field = product_box.find("th:nth-child(2) .product_id");

		var product_rate = product_box.find("th:nth-child(4) .product_rate");
		var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
		var product_qty = product_box.find("th:nth-child(5) .min_qty");
		var total_val = product_box.find("th:nth-child(6) .total_value");
		var combined_product_obj = product_box.find("th:nth-child(5) .combined_product");
		var combined_product_id_obj = product_box.find("th:nth-child(5) .combination_id");
		var combined_product = combined_product_obj.val();
		var min_qty = product_qty.val();
		var id = product_field.val();

		var product_rate_discount = {};
		var product_price_id_discount = {};

		var memo_date = $('#MemoMemoDate').val();
		delay(function() {
			$('#myModal').modal('show');
			$('#loading').show();

			if (min_qty == '' || min_qty == 0) {
				min_qty = 1;
				$('#' + current_row_no + '>th>.min_qty').val(1);
			}
			/*-----------------------------------*/
			$.ajax({
				url: '<?= BASE_URL . 'memos/get_product_price' ?>',
				'type': 'POST',
				data: {
					combined_product: combined_product,
					min_qty: min_qty,
					product_id: id,
					memo_date: memo_date,
					cart_product: product_wise_qty,
					cart_product_value: product_wise_qty_value,
					special_group: JSON.stringify(special_groups),
					outlet_category_id: outlet_category_id
				},
				success: function(result) {
					var obj = jQuery.parseJSON(result);

					if (obj.price != '') {
						product_rate.val(obj.price);
					}
					if (obj.price_id != '') {
						product_price_id.val(obj.price_id);
					}
					if (obj.total_value) {
						total_val.val(obj.total_value);
					}
					combined_product_obj.val(obj.combine_product);
					if (obj.combination != undefined) {
						combined_product_id_obj.val(obj.combination_id);
						$.each(obj.combination, function(index, value) {
							var prate = $(".prate-" + value.product_id);
							var tvalue = $(".tvalue-" + value.product_id);
							prate.val(value.price);
							tvalue.val(value.total_value);
							prate.next('.product_price_id').val(value.price_id);
							prate.parent().parent().find("th:nth-child(5) .combined_product").val(obj.combine_product);
							prate.parent().parent().find("th:nth-child(5) .combination_id").val(obj.combination_id);
						});
					}

					if (obj.recall_product_for_price != undefined) {
						$.each(obj.recall_product_for_price, function(index, value) {
							var prate = $(".prate-" + value);
							var tvalue = $(".tvalue-" + value);
							prate.parent().parent().find("th:nth-child(5) .combined_product").val(obj.combine_product);
							prate.parent().parent().find("th:nth-child(5) .combination_id").val('');
							prate.parent().parent().find("th:nth-child(5) .min_qty").trigger('keyup');
						});
					}

					var gross_total = 0;
					$('.total_value').each(function() {
						if ($(this).val() != '') {
							var producct_box_each = $(this).parent().parent();
							var product_id = producct_box_each.find("th:nth-child(2) div .product_id").val();
							product_wise_qty_value[product_id] = $(this).val();
							gross_total = parseFloat(gross_total) + parseFloat($(this).val());

							product_rate_discount[product_id] = producct_box_each.find("th:nth-child(4)  .product_rate").val();
							product_price_id_discount[product_id] = producct_box_each.find("th:nth-child(4)  .product_price_id").val();
						}
					});
					if ($("#gross_value").val(gross_total.toFixed(2))) {
						$('.n_bonus_row').remove();
						$('.discount_value').val(0.00);
						$('.discount_amount').val(0.00);
						get_policy_data();
					}

					if (obj.mother_product_quantity != undefined) {
						var mother_product_quantity = obj.mother_product_quantity;
						var bonus_product_id = obj.bonus_product_id;
						var bonus_product_name = obj.bonus_product_name;
						var bonus_product_quantity = obj.bonus_product_quantity;
						var sales_measurement_unit_id = obj.sales_measurement_unit_id;
						var no_of_bonus_slap = mother_product_quantity.length;
						var mother_product_quantity_bonus = obj.mother_product_quantity_bonus;
						for (var i = 0; i < no_of_bonus_slap; i++) {
							if (parseFloat($('#' + current_row_no + '>th>.min_qty').val()) >= parseFloat(mother_product_quantity[i].min) && parseFloat($('#' + current_row_no + '>th>.min_qty').val()) <= parseFloat(mother_product_quantity[i].max))

							{
								if (i == 0) {
									$('#' + current_row_no + '>th>.bonus').val('N.A');
									$('#' + current_row_no + '>th>.bonus_product_id').val(0);
									$('#' + current_row_no + '>th>.bonus_product_qty').val(0);
									$('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
								} else {
									$('#' + current_row_no + '>th>.bonus').val(bonus_product_quantity[i + (-1)] + '(' + bonus_product_name[i + (-1)] + ')');
									$('#' + current_row_no + '>th>.bonus_product_id').val(bonus_product_id[i + (-1)]);
									$('#' + current_row_no + '>th>.bonus_product_qty').val(bonus_product_quantity[i + (-1)]);
									$('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i + (-1)]);
								}
								break;
							} else {
								var current_qty = parseFloat($('#' + current_row_no + '>th>.min_qty').val());

								var bonus_qty = Math.floor(current_qty / parseFloat(mother_product_quantity_bonus)) * bonus_product_quantity[i];
								$('#' + current_row_no + '>th>.bonus').val(bonus_qty + ' (' + bonus_product_name[i] + ')');
								$('#' + current_row_no + '>th>.bonus_product_id').val(bonus_product_id[i]);
								$('#' + current_row_no + '>th>.bonus_product_qty').val(bonus_qty);
								$('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]);
							}
						}
					} else {
						$('#' + current_row_no + '>th>.bonus').val('N.A');
						$('#' + current_row_no + '>th>.bonus_product_id').val(0);
						$('#' + current_row_no + '>th>.bonus_product_qty').val(0);
						$('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
					}
					$('#cash_collection').val('');
					$('#loading').hide();
					$('#myModal').modal('hide');
					$('.add_more').removeClass('disabled');
				}
			});

			function get_policy_data() {

				var territory_id = $('#territory_id').val();
				var sale_type_id = $("#sale_type_id option:selected").val();
				if (sale_type_id == 3) {
					territory_id = $("#spo_territory_id option:selected").val();
				}
				
				$.ajax({
					url: '<?= BASE_URL . 'memos/get_product_policy' ?>',
					'type': 'POST',
					data: {
						min_qty: min_qty,
						product_id: id,
						product_rate_discount: product_rate_discount,
						product_price_id_discount: product_price_id_discount,
						order_date: memo_date,
						cart_product: product_wise_qty,
						cart_product_value: product_wise_qty_value,
						memo_total: $("#gross_value").val(),
						special_group: JSON.stringify(special_groups),
						outlet_category_id: outlet_category_id,
						outlet_id: $('#outlet_id').val(),
						office_id: $('#office_id').val(),
						territory_id: territory_id,
						selected_bonus: JSON.stringify(selected_bonus),
						selected_set: JSON.stringify(selected_set),
						selected_policy_type: JSON.stringify(selected_policy_type),
						selected_option_id: JSON.stringify(selected_option_id),
						other_policy_info: JSON.stringify(other_policy_info),
					},
					success: function(result) {
						var response = $.parseJSON(result);
						if (response.discount) {
							var discount = response.discount;
							var total_discount = response.total_discount;
							$.each(discount, function(ind, val) {
								$.each(val, function(ind1, val1) {
									var prate = $(".prate-" + val1.product_id);
									var tvalue = $(".tvalue-" + val1.product_id);
									prate.val(val1.price);
									tvalue.val(val1.total_value);
									prate.next('.product_price_id').val(val1.price_id);
									prate.parent().parent().find("th:nth-child(7) .discount_value").val(val1.total_discount_value);
									prate.parent().parent().find("th:nth-child(7) .discount_amount").val(val1.discount_amount);
									prate.parent().parent().find("th:nth-child(7) .disccount_type").val(val1.discount_type);
									prate.parent().parent().find("th:nth-child(7) .policy_type").val(val1.policy_type);
									prate.parent().parent().find("th:nth-child(7) .policy_id").val(val1.policy_id);
									prate.parent().parent().find("th:nth-child(7) .is_bonus").val('0');
								});
							});
							$('.total_discount').val(total_discount.toFixed(2));
							gross_value = $('#gross_value').val();
							net_payable = (gross_value) - (total_discount);
							$('.net_payable').val(net_payable.toFixed(2));
							$('#cash_collection').val($('#gross_value').val());
						}
						if (response.bonus_html) {
							var b_html = response.bonus_html;
							selected_bonus = response.selected_bonus;
							selected_set = response.selected_set;
							selected_policy_type = response.selected_policy_type;
							other_policy_info = response.other_policy_info;
							$('.bonus_product').append(b_html);
						}
						selected_option_id = response.selected_option_id;
					}
				});
			}

		}, 1000);
	});
	$("body").on("click", ".is_bonus_checked", function() {
		if ($(this).prop('checked')) {
			$(this).parent().prev().find('.policy_min_qty').prop('readonly', false);
			$(this).parent().prev().find('.policy_min_qty').prop('required', true);
			$(this).parent().prev().find('.policy_min_qty').attr('min', 1);
		} else {
			$(this).parent().prev().find('.policy_min_qty').prop('readonly', true);
			$(this).parent().prev().find('.policy_min_qty').prop('required', false);
			$(this).parent().prev().find('.policy_min_qty').attr('min', 0);
			$(this).parent().prev().find('.policy_min_qty').val(0.00);
		}
	});


	$("body").on("keyup", ".policy_min_qty", function() {
		var class_list = $(this).attr('class');
		class_list = class_list.split(" ");
		var policy_set_class = class_list[2];
		var max_qty = parseFloat($(this).attr('max'));
		var stock_qty = parseFloat($(this).data('stock'));
		var total_provide_qty = 0.00;
		$("." + policy_set_class).not(this).each(function(ind, val) {
			total_provide_qty += parseFloat($(this).val());
		});
		var given_qty = parseFloat($(this).val());
		var max_provide_qty = max_qty - total_provide_qty;
		if (stock_qty < given_qty && stock_qty <= max_provide_qty) {
			$(this).val(stock_qty);
		} else if (given_qty > max_provide_qty) {
			$(this).val(max_provide_qty);
		}

		var set = $(this).data('set');
		var policy_id = $(this).parent().prev().find('.policy_id').val();
		// selected_bonus[policy_id]=0;
		$("." + policy_set_class).each(function(ind, val) {
			var product_id = $(this).parent().prev().prev().find('.policy_bonus_product_id').val();
			selected_bonus[policy_id][set][product_id] = $(this).val();
		});
	});
	$("body").on("click", ".btn_set", function(e) {
		e.preventDefault();
		var set = $(this).data('set');
		var policy_id = $(this).data('policy_id');
		var prev_selected = selected_set[policy_id];
		if (set != prev_selected) {
			$(".btn_set[data-set='" + set + "'][data-policy_id='" + policy_id + "']").addClass('btn-success');
			$(".btn_set[data-set='" + set + "'][data-policy_id='" + policy_id + "']").removeClass('btn-default');

			$(".btn_set[data-set='" + prev_selected + "'][data-policy_id='" + policy_id + "']").addClass('btn-default');
			$(".btn_set[data-set='" + prev_selected + "'][data-policy_id='" + policy_id + "']").removeClass('btn-success');

			$(".bonus_policy_id_" + policy_id + ".set_" + set).removeClass('display_none');
			$(".bonus_policy_id_" + policy_id + ".set_" + set + " :input:not(:checkbox)").prop('disabled', false);
			$(".bonus_policy_id_" + policy_id + ".set_" + prev_selected).addClass('display_none');
			$(".bonus_policy_id_" + policy_id + ".set_" + prev_selected + " :input:not(:checkbox)").prop('disabled', true);
			selected_set[policy_id] = set;
		}
	});
	$("body").on("click", ".btn_type", function(e) {
		e.preventDefault();
		var type = $(this).data('type');
		var policy_id = $(this).data('policy_id');
		var prev_selected = selected_policy_type[policy_id];
		if (type != prev_selected) {
			$(".btn_type[data-type='" + type + "'][data-policy_id='" + policy_id + "']").addClass('btn-primary');
			$(".btn_type[data-type='" + type + "'][data-policy_id='" + policy_id + "']").removeClass('btn-basic');

			$(".btn_type[data-type='" + prev_selected + "'][data-policy_id='" + policy_id + "']").addClass('btn-basic');
			$(".btn_type[data-type='" + prev_selected + "'][data-policy_id='" + policy_id + "']").removeClass('btn-primary');
			selected_policy_type[policy_id] = type;
			$(".min_qty:last").trigger('keyup');
		}
	});
</script>

<script>
	$(document).ready(function() {
		$('body').on('click', '.delete_item', function() {
			var product_box = $(this).parent().parent();
			var product_field = product_box.find("th:nth-child(2) .product_id");
			var product_rate = product_box.find("th:nth-child(4) .product_rate");
			var combined_product = product_box.find("th:nth-child(5) .combined_product");
			var product_qty = product_box.find("th:nth-child(6) .min_qty");
			combined_product = combined_product.val();
			var id = product_field.val();

			var total_value = $('.tvalue-' + id).val();
			var gross_total = $('#gross_value').val();
			var new_gross_value = parseFloat(gross_total - total_value);
			$('#gross_value').val(new_gross_value);
			$('#cash_collection').val('');
			alert('Removed this row -------');
			var min_qty = product_qty.val();
			if (product_field.val() == '') {
				product_box.remove();

				var last_row = $('.invoice_table>tbody tr:last').attr('id');
				$('#' + last_row + '>th>.add_more').show();

				total_values();
			} else {
				product_box.remove();
				if (combined_product) {
					$.each(combined_product.split(','), function(index, value) {
						if (value != product_field.val()) {
							var prate = $(".prate-" + value);
							prate.parent().parent().find("th:nth-child(5) .combined_product").val('');
							prate.parent().parent().find("th:nth-child(5) .min_qty").trigger('keyup');
						}
					});
				}
				var last_row = $('.invoice_table>tbody tr:last').attr('id');
				$('#' + last_row + '>th>.add_more').show();
				total_values();
			}
		});
		$("body").on("keyup", "#cash_collection", function() {
			var gross_value = parseFloat($("#gross_value").val());
			var collect_cash = parseFloat($(this).val());

			var net_payable = parseFloat($("#net_payable").val());

			var credit_amount = gross_value - collect_cash;
			
			if (credit_amount >= 0) {
				$("#credit_amount").val(credit_amount.toFixed(2));
			} else {
				$("#credit_amount").val(0);
			}
		});
		$("form input[type=submit]").click(function() {
			
			$('.min_qty').each(function(key, value) {

				var value = $(this).val();
				var max_value  = $(this).attr('max'); 
				  
				if( value > max_value ){
					$("div#divLoading_default").removeClass('show');
					return false;
				}
				
            });
			
			
			
			$("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
			$(this).attr("clicked", "true");
		});
		$("#MemoAdminCreateMemoForm").submit(function(e) {
			e.preventDefault();
			var btn = $("input[type=submit][clicked=true]").val();

			stock_check(function(check) {
				if (check) {
					$("#MemoAdminCreateMemoForm").append("<input type='hidden' name='" + btn + "' value='" + btn + "' />");
					e.currentTarget.submit();
					return true;
				} else {
					$("div#divLoading_default").removeClass('show');
					return false;
				}
			});

			return false;
		});

		function stock_check(callback) {
			var product_wise_qty = {};
			$('.product_row_box .product_id').each(function(index, value) {
				var producct_box_each = $(this).parent().parent().parent();
				if (parseFloat(producct_box_each.find("th:nth-child(5) .min_qty").val())) {
					var unit_id = producct_box_each.find("th:nth-child(3) .product_unit_id").val();
					var prev_qty = 0.0;
					if ($(this).val() in product_wise_qty && unit_id in product_wise_qty[$(this).val()]) {
						prev_qty = product_wise_qty[$(this).val()][unit_id]
					}
					product_wise_qty[$(this).val()] = {
						[unit_id]: (parseFloat(producct_box_each.find("th:nth-child(5) .min_qty").val()) + prev_qty)
					};
				}
			});
			$('.policy_bonus_product_id').each(function(index, value) {
				var producct_box_each = $(this).parent().parent().parent();
				if (parseFloat(producct_box_each.find("th:nth-child(3) .policy_min_qty").val())) {
					var unit_id = producct_box_each.find("th:nth-child(2) .open_bonus_product_unit_id").val();
					var prev_qty = 0.0;
					if ($(this).val() in product_wise_qty && unit_id in product_wise_qty[$(this).val()]) {
						prev_qty = product_wise_qty[$(this).val()][unit_id]
					}
					product_wise_qty[$(this).val()] = {
						[unit_id]: (parseFloat(producct_box_each.find("th:nth-child(3) .policy_min_qty").val()) + prev_qty)
					};
				}
			});
			$('.bonus_product_id').each(function(index, value) {
				var producct_box_each = $(this).parent().parent();
				if (parseFloat(producct_box_each.find("th:nth-child(8) .bonus_product_qty").val())) {
					var unit_id = producct_box_each.find("th:nth-child(8) .bonus_measurement_unit_id").val();
					var prev_qty = 0.0;
					if ($(this).val() in product_wise_qty && unit_id in product_wise_qty[$(this).val()]) {
						prev_qty = product_wise_qty[$(this).val()][unit_id]
					}
					product_wise_qty[$(this).val()] = {
						[unit_id]: (parseFloat(producct_box_each.find("th:nth-child(8) .bonus_product_qty").val()) + prev_qty)
					};
				}
			});
			var result;

			var territory_id = $('#territory_id').val();
			var sale_type_id = $("#sale_type_id option:selected").val();

			if (sale_type_id == 3) {
				territory_id = $("#spo_territory_id option:selected").val();
			}
			
			if( sale_type_id == 4 ){
				var spoterritory_id = $('.spo_territory_id').val();
				if(spoterritory_id != ''){
					territory_id = spoterritory_id;
				}
			}
			
			$.ajax({
				url: '<?php echo BASE_URL . 'memos/memo_stock_check' ?>',
				'type': 'POST',
				data: {
					product_qty: JSON.stringify(product_wise_qty),
					territory_id: territory_id
				},
				success: function(result) {
					obj = jQuery.parseJSON(result);
					if (obj.status == 1) {
						callback(true);
					} else {
						alert(obj.msg);
						callback(false);
					}

				}
			});
			return result;
		}
	});
</script>

<script>
	$(document).ready(function() {
		$('body').on("keyup", ".memo_no", function() {
			var memo_no = $('.memo_no').val();
			var sale_type = $('#sale_type_id').val();

			delay(function() {
				$.ajax({
					url: '<?php echo BASE_URL . 'admin/memos/memo_no_validation' ?>',
					'type': 'POST',
					data: {
						memo_no: memo_no,
						sale_type: sale_type
					},
					success: function(result) {
						obj = jQuery.parseJSON(result);
						/*if(obj == 1){
							alert('Memo Number Already Exist');
							$('.submit').prop('disabled', true);
						}*/
						if (obj == 0) {
							$('.submit_btn').prop('disabled', false);
						} else {
							alert('Memo Number Already Exist');
							$('.submit_btn').prop('disabled', true);
						}
					}
				});
			}, 1000);

		});
	});

	var delay = (function() {
		var timer = 0;
		return function(callback, ms) {
			clearTimeout(timer);
			timer = setTimeout(callback, ms);
		};
	})();

	/*For Adding Bonus Product list : START*/
	$(document).ready(function() {
		$("body").on("click", ".bonus_add_more", function() {
			var product_list = $('#bonus_product_list').html();
			var product_bonus_row =
				'\
			<tr class="bonus_row">\
				<th class="text-center">' + product_list + '</th>\
				<th class="text-center" width="12%">\
					<input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>\
					<input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>\
					<input type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />\
					<input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>\
				</th>\
				<th class="text-center" width="12%">\
					<input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" required />\
						<input type="hidden" name="data[MemoDetail][discount_amount][]"/>\
						<input type="hidden" name="data[MemoDetail][disccount_type][]"/>\
						<input type="hidden" name="data[MemoDetail][policy_type][]"/>\
						<input type="hidden" name="data[MemoDetail][policy_id][]"/>\
						<input type="hidden" name="data[MemoDetail][is_bonus][]"/>\
					<input type="hidden" class="combined_product"/>\
					<select class="fraction_bonus_slab hide"></select>\
				</th>\
				<th class="text-center" width="10%">\
					<a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>\
					<a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>\
				</th>\
			</tr>\
			';
			var product_id = $(this).parent().parent().find('.open_bonus_product_id').val();
			if (product_id) {
				$(this).hide();
				$(".bonus_product").append(product_bonus_row);
			} else {
				alert('plese select product first');
			}
		});
		$("body").on("click", ".bonus_remove", function() {
			$(this).parent().parent().remove();
			// var total_tr= $(".bonus_row").length;
			$(".bonus_row").last().find('.bonus_add_more').show();
		});
		$("body").on("change", ".open_bonus_product_id", function() {
			var product_id = $(this).val();
			var product_box = $(this).parent().parent().parent();
			var product_category_id = product_box.find("th:nth-child(1) .open_bonus_product_category_id");
			var product_unit_name = product_box.find("th:nth-child(2) .open_bonus_product_unit_name");
			var product_unit_id = product_box.find("th:nth-child(2) .open_bonus_product_unit_id");
			var product_qty = product_box.find("th:nth-child(3) .open_bonus_min_qty");
			var territory_id = $('.territory_id').val();
			$.ajax({
				url: '<?= BASE_URL . 'memos/get_bonus_product_details' ?>',
				'type': 'POST',
				data: {
					product_id: product_id,
					territory_id: territory_id
				},
				success: function(result) {
					var data = $.parseJSON(result);

					product_category_id.val(data.category_id);
					product_unit_name.val(data.measurement_unit_name);
					product_unit_id.val(data.measurement_unit_id);
					product_qty.val(1);
					product_qty.attr('min', 1);
					product_qty.attr('max', data.total_qty);
				},
				error: function(error) {
					product_category_id.val();
					product_unit_name.val();
					product_unit_id.val();
					product_qty.val(0);
				}
			});
		});
		$("body").on("change", "#territory_id,#MemoMemoDate", function() {
			var territory_id = $('#territory_id').val();
			var memo_date = $('#MemoMemoDate').val();
			var sale_type_id = $("#sale_type_id option:selected").val();

			if (sale_type_id == 3) {
				territory_id = $("#spo_territory_id option:selected").val();
			}

			//alert(territory_id);

			if (territory_id && memo_date) {
				$.ajax({
					type: "POST",
					url: '<?= BASE_URL . 'Memos/get_bonus_product' ?>',
					data: {
						'territory_id': territory_id,
						'memo_date': memo_date
					},
					cache: false,
					success: function(response) {
						var json = $.parseJSON(response);
						console.log(json);
						$('.open_bonus_product_id option').remove();
						$('.open_bonus_product_id').append('<option value="">-- Select---</option>');
						for (var i = 0; i < json.length; ++i) {
							$('.open_bonus_product_id').append('<option value="' + json[i].Product.id + '">' + json[i].Product.name + '</option>');
						}
					}
				});
			}
			/*----------------------------- SAles fraction Slab :START ----------------------------------*/
			$("body").on('change', '.product_id', function() {
				var _this = $(this);
				get_fraction_slab($(this).val(), 1, function(data) {
					var output = '';
					$.each(data, function(i, item) {
						output += '<option value = "' + item + '">"' + item + '"</option>';
					});
					$(_this).parent().parent().parent().find('th:nth-child(5) .fraction_sales_slab').html(output);
				});

			});

			function get_fraction_slab(product_id, sales_or_bonus, callback) // 1=sales 2= Bonus fraction slab
			{
				var json_return;
				$.ajax({
					type: "POST",
					url: "<?= BASE_URL . 'Memos/get_fraction_slab' ?>",
					data: {
						'product_id': product_id,
						'sales_or_bonus': sales_or_bonus
					},
					cache: false,
					success: function(response) {
						var json = $.parseJSON(response);
						callback(json)
					}
				});
				/*console.log('rrr :'+json_return);
				return json_return;*/
			}
			$("body").on('focusout', '.min_qty', function() {
				var fraction_slab = $(this).parent().find('.fraction_sales_slab option').map(function() {
					return this.value;
				}).get();
				var sales_qty = $(this).val();
				var sales_qty_split = sales_qty.split(".");
				var sales_qty_decimal = sales_qty_split[0];
				var sales_fraction_qty = (parseFloat("." + (typeof(sales_qty_split[1]) != "undefined" ? sales_qty_split[1] : '0')) * 1).toFixed(2);

				// var sales_fraction_qty_checking="."+sales_fraction_qty;
				var sales_fraction_qty_checking = sales_fraction_qty.toString().substring(1);
				if (sales_fraction_qty != 0.00 /*&& fraction_slab.length > 0*/ && $.inArray(sales_fraction_qty_checking, fraction_slab) == -1) {
					// alert("please provide valid fraction like ("+fraction_slab.join(", ")+")");
					if (fraction_slab.length > 0) {
						alert("please provide valid fraction like (" + fraction_slab.join(", ") + ")");
					} else {
						alert("No Fraction Available For This Product");
					}
					$(this).val(sales_qty_decimal);
					$(this).trigger('keyup');
				}
			});
			/*----------------------------- SAles fraction Slab :END -------------------------------------*/


			/*----------------------------- Bonus fraction Slab :START ----------------------------------*/
			$("body").on('change', '.open_bonus_product_id', function() {
				var _this = $(this);
				get_fraction_slab($(this).val(), 2, function(data) {
					var output = '';
					$.each(data, function(i, item) {
						output += '<option value = "' + item + '">"' + item + '"</option>';
					});
					$(_this).parent().parent().parent().find('th:nth-child(3) .fraction_bonus_slab').html(output);
				});

			});

			$("body").on('focusout', '.open_bonus_min_qty', function() {
				var fraction_slab = $(this).parent().find('.fraction_bonus_slab option').map(function() {
					return this.value;
				}).get();
				var sales_qty = $(this).val();
				var sales_qty_split = sales_qty.split(".");
				var sales_qty_decimal = sales_qty_split[0];
				var sales_fraction_qty = (parseFloat("." + (typeof(sales_qty_split[1]) != "undefined" ? sales_qty_split[1] : '0')) * 1).toFixed(2);
				// console.log(sales_fraction_qty);
				// var sales_fraction_qty_checking="."+sales_fraction_qty;
				var sales_fraction_qty_checking = sales_fraction_qty.toString().substring(1);
				if (sales_fraction_qty != 0.00 /*&& fraction_slab.length > 0*/ && $.inArray(sales_fraction_qty_checking, fraction_slab) == -1) {
					// alert("please provide valid fraction like ("+fraction_slab.join(", ")+")");
					if (fraction_slab.length > 0) {
						alert("please provide valid fraction like (" + fraction_slab.join(", ") + ")");
					} else {
						alert("No Fraction Available For This Product");
					}
					$(this).val(sales_qty_decimal);
					$(this).trigger('keyup');
				}
			});
			/*----------------------------- Bonus fraction Slab :END -------------------------------------*/
		})
	});
	/*For Adding Bonus Product list : START*/
	<?php if ($csa == 1) { ?>
		/*For Csa Memo Need Two Extra field (csa and thana) : Start*/
		$(document).ready(function() {
			if ($(".office_id").val()) {
				get_csa_by_office_id($(".office_id").val());
			}
			$(".office_id").change(function() {
				get_csa_by_office_id($(this).val());
			});
			$("#csa_id").change(function() {
				get_territory_id_by_csa_id($(this).val());
			});
			$(".territory_id").change(function() {
				get_thana_by_territory_id($(this).val());
			});
			$("#thana_id").change(function() {
				get_market_by_thana_id($(this).val());
			});

			function get_csa_by_office_id(office_id) {
				$.ajax({
					url: '<?= BASE_URL . 'Memos/get_csa_list_by_office_id' ?>',
					data: {
						'office_id': office_id
					},
					type: 'POST',
					success: function(data) {
						$("#csa_id").html(data);
					}
				});
			}

			function get_territory_id_by_csa_id(csa_id) {
				$.ajax({
					url: '<?= BASE_URL . 'Memos/get_territory_list_by_csa_id' ?>',
					data: {
						'csa_id': csa_id
					},
					type: 'POST',
					success: function(data) {
						// console.log(data);
						$(".territory_id").html(data);
					}
				});
			}

			function get_thana_by_territory_id(territory_id) {
				$.ajax({
					url: '<?= BASE_URL . 'Memos/get_thana_by_territory_id' ?>',
					data: {
						'territory_id': territory_id
					},
					type: 'POST',
					success: function(data) {
						// console.log(data);
						$("#thana_id").html(data);
					}
				});
			}

			function get_market_by_thana_id(thana_id) {
				$.ajax({
					url: '<?= BASE_URL . 'Memos/get_market_by_thana_id' ?>',
					data: {
						'thana_id': thana_id
					},
					type: 'POST',
					success: function(data) {
						// console.log(data);
						$("#market_id").html(data);
					}
				});
			}
		});
		/*For Csa Memo Need Two Extra field (csa and thana) : End*/
	<?php } ?>


	function setBonusDiscount(policy_id, policy_type) {
		//alert(policy_id);
		//alert(policy_type);

		if (policy_type == 1) {
			//for btn selected
			$('.policy_info_' + policy_id + ' .btn_1').addClass('btn-primary');
			$('.policy_info_' + policy_id + ' .btn_1').removeClass('btn-default');
			$('.policy_info_' + policy_id + ' .btn_0').addClass('btn-default');
			$('.policy_info_' + policy_id + ' .btn_0').removeClass('btn-primary');

			//for bonus product enable
			$('.bonus_policy_id_' + policy_id).show();
			$('.bonus_policy_id_' + policy_id + ' select').prop('disabled', false);
			$('.bonus_policy_id_' + policy_id + ' input').prop('disabled', false);


			$('.set_policy_type' + policy_id).val(1);


			var discount_value = 0;
			$('.discount_amount_p_' + policy_id).each(function() {
				if ($(this).val() > 0) {
					discount_value = parseFloat($(this).val()) + (discount_value);
				}
			});

			//alert(discount_value);

			gross_value = $('#gross_value').val();
			total_discount = $('#total_discount').val();
			n_total_discount = (total_discount) - (discount_value);
			if (n_total_discount < 0) {
				n_total_discount = 0;
			}
			$('#total_discount').val(n_total_discount.toFixed(2));
			net_payable = (gross_value) - (n_total_discount);
			$('#net_payable').val(net_payable.toFixed(2));

			$('.discount_amount_p_' + policy_id).val(0);

			$('.main_discount_amount_p_' + policy_id).val(0);

		} else {

			//for btn selected
			$('.policy_info_' + policy_id + ' .btn_0').addClass('btn-primary');
			$('.policy_info_' + policy_id + ' .btn_0').removeClass('btn-default');
			$('.policy_info_' + policy_id + ' .btn_1').addClass('btn-default');
			$('.policy_info_' + policy_id + ' .btn_1').removeClass('btn-primary');

			//for bonus product disabled
			$('.bonus_policy_id_' + policy_id).hide();
			$('.bonus_policy_id_' + policy_id + ' select').prop('disabled', true);
			$('.bonus_policy_id_' + policy_id + ' input').prop('disabled', true);

			$('.set_policy_type' + policy_id).val(0);

			$(".min_qty").keyup();
		}
	}

	function get_bonus_product_info(option_id, policy_id) {
		var product_id = $('.bonus_policy_id_' + policy_id + ' .policy_bonus_product_id').val();
		var territory_id = $('.territory_id').val();

		$.ajax({
			url: '<?= BASE_URL . 'memos/get_bonus_product_info' ?>',
			'type': 'POST',
			data: {
				product_id: product_id,
				territory_id: territory_id,
				option_id: option_id
			},
			beforeSend: function() {
				$('.m_loading').show();
			},
			success: function(result) {
				$('.m_loading').hide();
				var data = $.parseJSON(result);

				$('.bonus_policy_id_' + policy_id + ' .open_bonus_product_unit_name').val(data.measurement_unit_name);
				$('.bonus_policy_id_' + policy_id + ' .open_bonus_min_qty').val(data.bonus_qty);
				$('.bonus_policy_id_' + policy_id + ' .open_bonus_product_unit_id').val(data.measurement_unit_id);

			},
		});
	}

	function checkBonusProductQty(option_id, bonus_qty) {

		delay(function() {

			p_t_qty = 0;
			total_row = 0;
			$(".open_bonus_option_id_" + option_id).each(function() {
				p_t_qty += parseFloat($(this).val());
				total_row++;
			});

			t_qty = parseFloat($('.bonus_option_id_' + option_id + ':first').val());

			if (p_t_qty > t_qty) {
				alert('Maximum total quantity is ' + t_qty);
				$('.open_bonus_option_id_' + option_id).val(t_qty / total_row);
			}

		}, 1000);
	}
	$("body").on('click', '.remove_policy_bonus', function() {
		$(this).parent().parent().remove();
	});
	$(window).on('load', function() {
		$(".office_id").trigger('change');
	});
	
	
	//----------------added by golam rabbi---------------\\

	function get_thana_by_territory_id(territory_id) {
		$.ajax({
			url: '<?= BASE_URL . 'Memos/get_thana_by_territory_id_tow' ?>',
			data: {
				'territory_id': territory_id
			},
			type: 'POST',
			success: function(data) {
				// console.log(data);
				$("#thana_id").html(data);
			}
		});
	}

	function get_market_by_thana_id(thana_id, territory_id) {
		$.ajax({
			url: '<?= BASE_URL . 'Memos/get_market_by_thana_id_territory_id' ?>',
			data: {
				'thana_id': thana_id,
				'territory_id': territory_id
			},
			type: 'POST',
			success: function(data) {
				// console.log(data);
				$("#market_id").html(data);
			}
		});
	}

	$(document).ready(function(){

		$(".territory_id").change(function() {

			var territory_id = $('#territory_id').val();

			get_thana_by_territory_id(territory_id);


		});

		$("#thana_id").change(function() {

			var territory_id = $('#territory_id').val();

			get_market_by_thana_id($(this).val(), territory_id);

		});


	});

	//------------------end----------------\\
	
	
	
</script>