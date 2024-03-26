<style>
	.width_5 {
		width: 5%
	}

	.width_15 {
		width: 15%
	}

	.display_none {
		display: none;
	}

	hr {
		margin: 10px 0px;
	}

	.policy_option1,
	.policy_option {
		margin-bottom: 0px;
	}

	.discount_product,
	.discount_product div {
		margin-bottom: 0px;
	}

	.div_select {
		width: 500px;
	}

	.m_select {
		width: 10%;
		margin-left: 5px;
	}

	.search .radio label {
		width: auto;
		float: none;
		padding: 0px 15px 0px 5px;
		margin: 0px;
	}
	.display_none_deduct_value{
		display: none;
	}

	.radio,
	.checkbox {
		margin-top: 0px !important;
		margin-bottom: 0px !important;
	}

	.search .radio legend {
		float: left;
		margin: 5px 20px 0 0;
		text-align: right;
		width: 20%;
		display: inline-block;
		font-weight: 700;
		font-size: 14px;
		border-bottom: none;
	}

	.radio input[type="radio"],
	.radio-inline input[type="radio"] {
		margin-left: 0px;
		position: relative;
		margin-top: 8px;
	}

	table,
	table td {
		border: 1px solid #ccc;
		border-collapse: collapse;
		height: 25px;
		padding: 10px;
	}

	table {
		width: 100%;
		margin-bottom: 10px;
		margin-top: 10px;
	}

	.checkbox-inline {
		display: inline-flex;
	}

	.checkbox-inline input[type="checkbox"] {

		margin-left: 25px;
		margin-top: 9px;
	}

	.formula_div {
		float: left;
		width: 30%;
		margin-bottom: 10px;
		margin-top: 10px;
		border: 1px solid #ccc;
		padding: 10px;
	}

	.chosen-container.chosen-container-multi {
		width: 500px !important;
		/* or any value that fits your needs */
	}
	
	.bonus_file_section{
		border: 1px solid #ccc;
		padding: 10px 0px;
		margin: 10px 0px;
	}
</style>

<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">

			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Edit Discount/Bonus Policy'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>

			<div class="box-body">
				<?php echo $this->Form->create('DiscountBonusPolicy', array('role' => 'form', 'enctype'=>'multipart/form-data')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('type' => 'text', 'class' => 'form-control', 'label' => 'Policy Name:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('remarks', array('type' => 'text', 'class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('start_date', array('type' => 'text', 'class' => 'datepicker form-control start_date', 'label' => 'Start Date:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('end_date', array('type' => 'text', 'class' => 'datepicker form-control', 'label' => 'End Date:')); ?>
				</div>

				<div class="create_for">
					<?php echo $this->Form->input('create_policy_for', array('id' => 'create_policy_for', 'label' => 'Create For :', 'class' => 'checkbox-inline create_policy_for', 'multiple' => 'checkbox', 'options' => array('1' => 'SO', '2' => 'SR', '3' => 'DB'), 'div' => false, 'hiddenField' => false, 'selected' => $selected_create_for)); ?>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<table>
							<tr>
								<td class="so text-center">SO</td>
								<td class="sr text-center">SR</td>
							</tr>
							<tr>
								<td class="so">
									<div class="form-group">
										<label for="so_special_group_id">Special Group :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicySpecialGroup][so_special_group_id][]" id="so_special_group_id" class="form-control so_special_group_id div_select" multiple="multiple">
												<?php foreach ($so_special_group as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $so_special_group_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="so_office_id">Office :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicyToOffice][so_office_id][]" id="so_office_id" class="form-control so_office_id office_id div_select so_other_settings" multiple="multiple">
												<?php foreach ($offices as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $so_office_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>


									<div class="form-group">
										<label for="office_id">Outlet Group :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicyToOutletGroup][so_outlet_group_id][]" id="so_outlet_group_id" class="form-control outlet_group_id so_other_settings chosen div_select" multiple="multiple">
												<?php foreach ($outlet_groups as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $so_outlet_group_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="office_id">Excluding Outlet Group :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicyToExcludingOutletGroup][so_excluding_outlet_group_id][]" id="so_excluding_outlet_group_id" class="form-control so_excluding_outlet_group_id so_other_settings chosen div_select" multiple="multiple">
												<?php foreach ($outlet_groups as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $so_excluding_outlet_group_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>


									<div class="form-group">
										<label for="so_outlet_category_id">Outlet Category :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicyToOutletCategory][so_outlet_category_id][]" id="so_outlet_category_id" class="so_other_settings form-control chosen div_select" multiple="multiple">
												<?php foreach ($outlet_categories as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $so_outlet_category_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>
								</td>
								<td class="sr">
									<div class="form-group">
										<label for="sr_special_group_id">Special Group :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicySpecialGroup][sr_special_group_id][]" id="sr_special_group_id" class="form-control sr_special_group_id div_select" multiple="multiple">
												<?php foreach ($sr_special_group as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $sr_special_group_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="sr_office_id">Office :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicyToOffice][sr_office_id][]" id="sr_office_id" class="form-control sr_office_id sr_other_settings office_id div_select" multiple="multiple">
												<?php foreach ($offices as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $sr_office_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>


									<div class="form-group">
										<label for="office_id">Outlet Group :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicyToOutletGroup][sr_outlet_group_id][]" id="sr_outlet_group_id" class="form-control outlet_group_id sr_other_settings chosen div_select" multiple="multiple">
												<?php foreach ($sr_outlet_groups as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $sr_outlet_group_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="office_id">Excluding Outlet Group :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicyToExcludingOutletGroup][sr_excluding_outlet_group_id][]" id="sr_excluding_outlet_group_id" class="form-control outlet_group_id sr_other_settings chosen div_select" multiple="multiple">
												<?php foreach ($sr_outlet_groups as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $sr_excluding_outlet_group_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="sr_outlet_category_id">Outlet Category :</label>
										<div class="input select">
											<select name="data[DiscountBonusPolicyToOutletCategory][sr_outlet_category_id][]" id="sr_outlet_category_id" class="form-control chosen sr_other_settings div_select" multiple="multiple">
												<?php foreach ($sr_outlet_categories as $o_key => $o_val) { ?>
													<?php if (in_array($o_key, $sr_outlet_category_ids)) { ?>
														<option value="<?= $o_key; ?>" <?= 'selected' ?>><?= $o_val; ?></option>
													<?php } else { ?>
														<option value="<?= $o_key; ?>"><?= $o_val; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				
				<div class="bonus_file_section">
					<div class="form-group">
						<?php echo $this->Form->input('numbering', array('type' => 'number', 'class' => 'form-control', 'label' => 'Numbering :')); ?>
					</div>
					<div class="form-group">
						<div class="input file">
							<label>File :</label>
							<input class="form-control" name="data[DiscountBonusPolicy][bonus_file][]" type="file" multiple>
						</div>
					</div>
					<div class="form-group">
						<div class="input file">
							<table style="border:none; width:70%;margin-left:20%;" border="0">
							<tr>
							<?php  
								if(!empty($dbpfile_list)){
									foreach($dbpfile_list as $v){
										$fileid = $v['DiscountBonusPolicyFile']['id'];
										$ext = substr(strtolower(strrchr($v['DiscountBonusPolicyFile']['file_name'], '.')), 1);
										$fileurl = BASE_URL.'app/webroot/bonus_policy/' . $v['DiscountBonusPolicyFile']['file_name'];
							?>
							<td>
							<a href="<?=$fileurl;?>" target="_blank">
								<?php 
									if($ext == 'png'){
										$img =  BASE_URL.'theme/CakeAdminLTE/img/pngicon.png';
									} 
									elseif($ext == 'jpg' || $ext == 'jpeg'){
										$img =  BASE_URL.'theme/CakeAdminLTE/img/jpgicon.png';
									} 

									elseif($ext == 'pdf'){
										$img =  BASE_URL.'theme/CakeAdminLTE/img/pdficon.png';
									}elseif($ext == 'xls' || $ext == 'xlsx' || $ext == 'csv'){
										$img =  BASE_URL.'theme/CakeAdminLTE/img/xls.ico';
									} elseif($ext == 'docx'){
										$img =  BASE_URL.'theme/CakeAdminLTE/img/docxicon.jpg';
									} else{
										$img =  BASE_URL.'theme/CakeAdminLTE/img/defualticon.png';
									}

									$reomoveicon = BASE_URL.'theme/CakeAdminLTE/img/removeicon.png';

								?>
								<img src="<?=$img;?>" style="width:32px;height:32px;">
								</a>
								&nbsp;&nbsp;&nbsp;&nbsp; 
								<a id="discountremovefile<?=$fileid?>" onclick="discountremovefile('<?=$fileid?>')"> 
									<img src="<?=$reomoveicon;?>" style="width:12px;height:12px;float:right;margin-top: -5px;"> 
								</a>
							</td>
							<?php }} ?>
							</tr>
							</table>
							<input type="hidden" id="removedicountfileid" name="data[DiscountBonusPolicy][discount_bonus_policy_file_id]" autocomplete="off">
					</div></div>
					
				</div>


				<div class="form-group">
					<?php
					$i = 0;
					foreach ($this->request->data['DiscountBonusPolicyProduct'] as $key => $val) {
						$i++;
					?>

						<div class="form-group slap_set<?= $i ?>">
							<label>Product :</label>
							<div class="input select">
								<select name="data[DiscountBonusPolicyProduct][policy_product_id][]" class="form-control policy_product_id" id="policy_product_id1" onchange="add_option_product(this,<?= $i ?>)">
									<?php foreach ($products as $p_key => $p_val) { ?>
										<option value="<?= $p_key; ?>" <?php if ($this->request->data['DiscountBonusPolicyProduct'][$key]['product_id'] == $p_key) {
																			echo 'selected';
																		} ?>><?= $p_val; ?></option>
									<?php } ?>
								</select>
							</div>

							<?php /*?><?php
						if($i==1){
							echo '<button type="button" class="add_product_button hide_all">Add More</button>';
						}else{
							echo '<a href="#" class="remove_product_field btn btn-primary hide_all btn-xs">Remove</a>';
						}
						?><?php */ ?>
						</div>
					<?php
					}
					$total_product = $i;
					?>
					<span class="input_products_wrap"></span>
				</div>

				<?php /*?><div class="submit">
					<button onClick="showOptions()" type="button">Set</button>
					<button onClick="window.location.reload()" type="button">Reset</button>
				</div><?php */ ?>
				
				
				
				


				<div class="policy_options">

					<?php
					$i = 0;
					$selected_slab = array();
					foreach ($this->request->data['DiscountBonusPolicyOption'] as $option_val) {
						$i++;
					?>
						<hr>
						</hr>
						<div class="form-group policy_option<?= $i ?>">
							<div class="form-group">
								<?php echo $this->Form->input('policy_type', array('type' => 'select', 'label' => 'Policy Option', 'class' => 'form-control policy_option', 'id' => 'policy_type' . $i, 'onChange' => 'showBonusOrDiscount(this.value, ' . $i . ')', 'options' => $policy_types, 'selected' => $option_val['policy_type'], 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][policy_type]',)); ?>
								<?php if ($i == 1) { ?>
									<button type="button" onClick="addOption()" class="add_option_button">Add More Option</button>
								<?php } else { ?>
									<button type="button" onclick="removeOption(<?= $i ?>)" class="add_option_button">Remove</button>
								<?php } ?>
							</div>
							<div class="form-group slab_create_for<?= $i ?> slab_create_for">
								<label> </label>
								<?php if (array_search(1, $selected_create_for) !== false) {
									$selected_slab[$i]['is_so'] = $option_val['is_so']; ?>
									<div class="checkbox-inline create_slab_for">
										<input type="checkbox" name="data[DiscountBonusPolicyOption][<?= $i ?>][create_slab_for][]" value="1" id="create_slab_for<?= $i ?>1" style="" <?php if ($option_val['is_so'] == 1) echo 'checked' ?>>
										<label for="create_slab_for<?= $i ?>1">SO</label>
									</div>
								<?php } ?>
								<?php if (array_search(2, $selected_create_for) !== false) {
									$selected_slab[$i]['is_sr'] = $option_val['is_sr']; ?>
									<div class="checkbox-inline create_slab_for">
										<input type="checkbox" name="data[DiscountBonusPolicyOption][<?= $i ?>][create_slab_for][]" value="2" id="create_slab_for<?= $i ?>2" style="" <?php if ($option_val['is_sr'] == 1) echo 'checked' ?>>
										<label for="create_slab_for<?= $i ?>2">SR</label>
									</div>
								<?php } ?>
								<?php if (array_search(3, $selected_create_for) !== false) {
									$selected_slab[$i]['is_db'] = $option_val['is_db']; ?>
									<div class="checkbox-inline create_slab_for">
										<input type="checkbox" name="data[DiscountBonusPolicyOption][<?= $i ?>][create_slab_for][]" value="3" id="create_slab_for<?= $i ?>3" style="" <?php if ($option_val['is_db'] == 1) echo 'checked' ?>>
										<label for="create_slab_for<?= $i ?>3">DB</label>
									</div>
								<?php } ?>
							</div>
							<div class="form-group required min_qty<?= $i ?>">
								<?php echo $this->Form->input('min_qty', array('type' => 'number', 'label' => 'Min Qty', 'class' => 'form-control', 'id' => 'min_qty' . $i, 'value' => $option_val['min_qty'], 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][min_qty]', 'required' => true)); ?>
								<select class="min_qty_measurement_unit_id form-control m_select" data-selected-id="<?= $option_val['measurement_unit_id'] ?>" name="data[DiscountBonusPolicyOption][<?= $i ?>][min_qty_measurement_unit_id]">
									<option value="">--- Select ----</option>
								</select>
							</div>

							<div class="form-group required min_value<?= $i ?>">
								<?php echo $this->Form->input('min_value', array('type' => 'number', 'label' => 'Min Value', 'class' => 'form-control', 'id' => 'min_value' . $i, 'value' => $option_val['min_value'], 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][min_value]', 'required' => true)); ?>
								
							</div>

							<div class="form-group">
								<?php echo $this->Form->input('min_memo_value', array('type' => 'number', 'label' => 'Min Memo Value', 'class' => 'form-control', 'id' => 'min_memo_value' . $i, 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][min_memo_value]', 'value' => $option_val['min_memo_value'])); ?>
							</div>
							<div class="form-group exclusion_inclusion1">

								<label> </label>
								<div class="checkbox-inline create_exclusion_inclusion">
									<input type="checkbox" <?php if (count($option_val['DiscountBonusPolicyOptionExclusionProduct']) > 0) echo 'checked' ?> value="1" id="create_exclusion_inclusion_for<?= $i ?>1" style="margin-left: 65px;">
									<label for="create_exclusion_inclusion_for<?= $i ?>1">Exclusion</label>
								</div>
								<div class="checkbox-inline create_exclusion_inclusion">
									<input type="checkbox" <?php if (count($option_val['DiscountBonusPolicyOptionInclusionProduct']) > 0) echo 'checked' ?> value="2" id="create_exclusion_inclusion_for<?= $i ?>2" style="margin-left: 65px;">
									<label for="create_exclusion_inclusion_for<?= $i ?>2">Inclusion</label>
								</div>
							</div>
							<div class="exclusion_product <?php if (count($option_val['DiscountBonusPolicyOptionExclusionProduct']) == 0) echo 'display_none' ?>">

								<div class="exclusion_<?= $i ?>_product_wraps">
									<?php
									$ex_p = 1;
									foreach ($option_val['DiscountBonusPolicyOptionExclusionProduct'] as $ex_pro_data) { //pr($ex_pro_data);exit;
									?>
										<div class="form-group <?php if ($ex_p > 1) echo 'exclusion_product' . $i ?>">
											<?php echo $this->Form->input('exclusion_product_id', array('type' => 'select', 'required' => true, 'label' => 'Exclusion Product : ', 'class' => 'form-control exclusionProduct', 'empty' => '---- Select Product ----', 'id' => 'exclusion_product_id' . $i, 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][exclusion_product_id][]', 'options' => $products, 'default' => $ex_pro_data['product_id'])); ?>

											<?php echo $this->Form->input('exclusion_min_qty', array('type' => 'number', 'label' => false, 'class' => 'form-control m_select', 'id' => 'exclusion_min_qty' . $i, 'placeholder' => 'Min Qty', 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][exclusion_min_qty][]', 'value' => $ex_pro_data['min_qty'])); ?>
											<?php if ($ex_p == 1) { ?>
												<button type="button" onClick="addExProduct(<?= $i ?>)" class="add_ex_option_button">Add More</button>
											<?php } else {  ?>
												<button type="button" class="add_option_button remove_ex_in_product">Remove</button>
											<?php } ?>
										</div>
									<?php $ex_p++;
									} ?>
									<?php if (count($option_val['DiscountBonusPolicyOptionExclusionProduct']) == 0) { ?>
										<div class="form-group">
											<?php echo $this->Form->input('exclusion_product_id', array('type' => 'select', 'label' => 'Exclusion Product : ', 'class' => 'form-control exclusionProduct', 'empty' => '---- Select Product ----', 'id' => 'exclusion_product_id' . $i, 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][exclusion_product_id][]', 'options' => $products)); ?>

											<?php echo $this->Form->input('exclusion_min_qty', array('type' => 'number', 'label' => false, 'class' => 'form-control m_select', 'id' => 'exclusion_min_qty' . $i, 'placeholder' => 'Min Qty', 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][exclusion_min_qty][]')); ?>
											<button type="button" onClick="addExProduct(<?= $i ?>)" class="add_ex_option_button">Add More</button>
										</div>
									<?php } ?>
								</div>
							</div>
							<div class="inclusion_product <?php if (count($option_val['DiscountBonusPolicyOptionInclusionProduct']) == 0) echo 'display_none' ?>">
								<div class="inclusion_<?= $i ?>_product_wraps">
									<?php
									$ex_p = 1;
									foreach ($option_val['DiscountBonusPolicyOptionInclusionProduct'] as $ex_pro_data) { //pr($ex_pro_data);exit;
									?>
										<div class="form-group <?php if ($ex_p > 1) echo 'inclusion_product' . $i ?>">
											<?php echo $this->Form->input('inclusion_product_id', array('type' => 'select', 'required' => true, 'label' => 'Inclusion Product : ', 'class' => 'form-control inclusionProduct', 'empty' => '---- Select Product ----', 'id' => 'inclusion_product_id' . $i, 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][inclusion_product_id][]', 'options' => $products, 'default' => $ex_pro_data['product_id'])); ?>

											<?php echo $this->Form->input('inclusion_min_qty', array('type' => 'number', 'label' => false, 'class' => 'form-control m_select', 'id' => 'inclusion_min_qty' . $i, 'placeholder' => 'Min Qty', 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][inclusion_min_qty][]', 'value' => $ex_pro_data['min_qty'])); ?>
											<?php if ($ex_p == 1) { ?>
												<button type="button" onClick="addInProduct(<?= $i ?>)" class="add_in_option_button">Add More</button>
											<?php } else {  ?>
												<button type="button" class="add_option_button remove_ex_in_product">Remove</button>
											<?php } ?>
										</div>
									<?php $ex_p++;
									} ?>
									<?php if (count($option_val['DiscountBonusPolicyOptionInclusionProduct']) == 0) { ?>
										<div class="form-group">
											<?php echo $this->Form->input('inclusion_product_id', array('type' => 'select', 'label' => 'Inclusion Product : ', 'class' => 'form-control inclusionProduct', 'empty' => '---- Select Product ----', 'id' => 'inclusion_product_id' . $i, 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][inclusion_product_id][]', 'options' => $products)); ?>

											<?php echo $this->Form->input('inclusion_min_qty', array('type' => 'number', 'label' => false, 'class' => 'form-control m_select', 'id' => 'inclusion_min_qty' . $i, 'placeholder' => 'Min Qty', 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][inclusion_min_qty][]')); ?>
											<button type="button" onClick="addInProduct(<?= $i ?>)" class="add_in_option_button">Add More</button>
										</div>
									<?php } ?>
								</div>
							</div>
							<div class="bonus_product <?= ($option_val['policy_type'] == 0 ? 'display_none' : '') ?>">

								<div class="option_<?= $i ?>_product_wraps">
									<?php
									if ($option_val['DiscountBonusPolicyOptionBonusProduct']) {
										$p = 1;
										$bp_total = count($option_val['DiscountBonusPolicyOptionBonusProduct']);
										$defaultProductList = array();
										foreach ($option_val['DiscountBonusPolicyOptionBonusProduct'] as $p_result) {

											$defaultProductList[$p_result['bonus_product_id']] = $products[$p_result['bonus_product_id']];

			

									?>
											<?php if ($p > 1) { ?>
												<script type="text/javascript">
													$(document).ready(function() {
														pushBProductOption(<?= $i ?>);
													});
												</script>
											<?php } ?>

											<div class="form-group <?php if ($p > 1) { ?>b_product<?= $i ?><?php } ?> pc_<?= $p ?>">
												<?php echo $this->Form->input('bonus_product_id', array('type' => 'select', 'required' => true, 'label' => 'Bonus Product', 'class' => 'form-control bonus_product_formula', 'empty' => '---- Select Product ----', 'id' => 'bonus_product_id1', 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][bonus_product_id][]', 'selected' => $p_result['bonus_product_id'], 'options' => $products, 'onChange' => 'get_product_units(this.value,' . $i . ',' . $p . ')')); ?>
												<div class="option<?= $i ?>_bonus_product<?= $p ?>_wrap">
													<select class="form-control m_select" name="data[DiscountBonusPolicyOption][<?= $i ?>][measurement_unit_id][]">
														<option value="">Select Measurement</option>
													</select>
												</div>
												<?php echo $this->Form->input('bonus_qty', array('type' => 'number', 'label' => false, 'required' => true, 'class' => 'form-control m_select width_5 onlyBonus', 'placeholder' => 'Bonus Qty', 'value' => $p_result['bonus_qty'], 'name' => ' data[DiscountBonusPolicyOption][' . $i . '][bonus_qty][]')); ?>
												<?php echo $this->Form->input('bonus_in_hand', array('type' => 'number', 'label' => false, 'class' => 'bonus_in_hand form-control width_5 ' . ($option_val['is_db'] == 1 ? '' : 'display_none'), 'value' => $p_result['in_hand_bonus_qty'], 'id' => 'bonus_in_hand' . $i, 'placeholder' => 'DB in Hand', 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][bonus_in_hand][]')); ?>
												<?php if ($p == 1) { ?>
													<button type="button" onClick="addBProduct(<?= $i ?>)" class="add_option_button">Add More</button>
												<?php } else { ?>
													<button type="button" onclick="removeBProduct(<?= $i ?>,<?= $p ?>)" class="add_option_button">Remove</button>
												<?php } ?>
											</div>

											<script type="text/javascript">
												$(document).ready(function() {
													getProductUnits('<?= $i ?>', '<?= $p ?>', '<?= $p_result['bonus_product_id'] ?>', '<?= $p_result['measurement_unit_id'] ?>');
												});
											</script>
									<?php
											$p++;
										}
									}
									?>

								</div>

								<div class="default_bonus_product_selection_<?= $i ?>">
									<?php
									if ($option_val['DiscountBonusPolicyDefaultBonusProductSelection']) {
										$p = 1;
										$bp_total = count($option_val['DiscountBonusPolicyDefaultBonusProductSelection']);
										foreach ($option_val['DiscountBonusPolicyDefaultBonusProductSelection'] as $p_result) {
									?>
									<div class="form-group default_product<?= $i ?> pc_<?= $p ?>">
										<?php echo $this->Form->input('default_bonus_product_id', array('type' => 'select', 'label' => 'Default Bonus Pproduct Selection', 'onchange'=>'default_product_set('.$i.')', 'class' => 'form-control default_bonus_product_formula', 'empty' => '---- Select Product ----', 'id' => 'default_bonus_product_id'.$i, 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][default_bonus_product_id][]', 'selected' => $p_result['product_id'], 'options' => $defaultProductList,)); ?>
										<?php if ($p == 1) { ?>
											<button type="button" onClick="adddefualtProduct(<?= $i ?>)" class="add_default_option_button">Add More</button>
										<?php } else { ?>
											<button type="button" onclick="removeDefaultProduct(<?= $i ?>,<?= $p ?>)" class="add_default_option_button">Remove</button>
										<?php } ?>
									</div>
									<?php $p++; }}else{ ?>
										
										<div class="form-group">
											<?php echo $this->Form->input('default_bonus_product_id', array('type' => 'select', 'onchange'=>'default_product_set('.$i.')', 'label' => 'Default Bonus Pproduct Selection', 'class' => 'form-control default_bonus_product_formula', 'empty' => '---- Select Product ----', 'id' => 'default_bonus_product_id'.$i, 'name' => 'data[DiscountBonusPolicyOption]['.$i.'][default_bonus_product_id][]', 'options' => $defaultProductList,)); ?>
											<button type="button" onClick="adddefualtProduct('<?=$i;?>')" class="add_default_option_button">Add More</button>
										</div>
										
									<?php } ?>
								</div>

								<div class="formula">
									<label>Formula</label>
									<div class="formula_div">
										<div class="operator">
											<button class="operator_name btn btn-xs" name="braces_left" id="braces_left" value="(">(</button>
											<button class="operator_name btn btn-xs" name="braces_right" id="braces_right" value=")">)</button>
											<button class="operator_name btn btn-xs" name="square_braces_left" id="square_braces_left" value="AND">AND</button>
											<button class="operator_name btn btn-xs" name="square_braces_right" id="square_braces_right" value="OR">OR</button>
											<?php foreach ($option_val['DiscountBonusPolicyOptionBonusProduct'] as $p_result) {
											?>
												<button class="operator_name product_operator btn btn-xs" value="<?= $p_result['bonus_product_id'] ?>"><?= $products[$p_result['bonus_product_id']] ?></button>
											<?php } ?>
										</div>
										<div class="formula_text">
											<textarea name="data[DiscountBonusPolicyOption][<?= $i ?>][bonus_formula]" class="product_showing" rows="1" cols="55" readonly=""><?= $option_val['bonus_formula_text'] ?></textarea>
											<textarea name="data[DiscountBonusPolicyOption][<?= $i ?>][bonus_formula_with_product_id]" class="product_id_showing" rows="1" cols="55" readonly="" style="display: none;"><?= $option_val['bonus_formula_text_with_product_id'] ?></textarea>
											<button class="btn btn-xs btn-danger clear_formula_text pull-right">Clear</button>
										</div>
									</div>
								</div>
							</div>
							<?php 
							
								if($option_val['deduct_from_value'] == 1){
									$displaystyle = "display_none_deduct_value";
								}else{
									$displaystyle = "  ";
								}

							?>
							<div class="discount_product <?= ($option_val['policy_type'] == 1 ? 'display_none' : '') ?>">
								<div class="form-group">

									<?php
									if ($option_val['discount_amount'] > 0) {
										$reqCon = 'true';
									} else {
										$reqCon = 'false';
									}
									?>

									<?php echo $this->Form->input('discount_amount', array('type' => 'text', 'required' => $reqCon, 'label' => 'Discount Amount', 'class' => 'form-control discountAmount', 'id' => 'discount_amount1', 'value' => $option_val['discount_amount'], 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][discount_amount]')); ?>
									<?php echo $this->Form->input('discount_in_hand', array('type' => 'text', 'label' => false, 'class' => 'discount_in_hand form-control width_5 ' . ($option_val['is_db'] == 0 ? 'display_none' : ''), 'id' => 'discount_in_hand' . $i, 'placeholder' => 'DB in Hand', 'value' => $option_val['in_hand_discount_amount'], 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][discount_in_hand]')); ?>
									<?php echo $this->Form->input('disccount_type', array('type' => 'select', 'onchange'=>"discounttype($i)", 'label' => false, 'class' => 'form-control width_5', 'id' => 'disccount_type'.$i, 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][disccount_type]', 'selected' => $option_val['disccount_type'], 'options' => $disccount_types)); ?>
								</div>

								<?php

								if ($option_val['DiscountBonusPolicyOptionPriceSlab']) {
									$k = 1;
									foreach ($option_val['DiscountBonusPolicyOptionPriceSlab'] as $s_result) {
								?>
										<div class="form-group option<?= $i ?>_products_wrap <?=' ' .$displaystyle;?>">
											<div class="option<?= $i ?>_slap_set<?= $k ?>">
												<div class="form-group">
													<label>Discount Product :</label>
													<select class="form-control product_common_class option_product" name="data[DiscountBonusPolicyOptionPriceSlab][<?= $i ?>][discount_product_id][]">
														<?php foreach ($products as $p_key => $p_val) { ?>
															<?php if ($s_result['discount_product_id'] == $p_key) { ?>
																<option value="<?= $p_key; ?>"><?= $p_val; ?></option>
															<?php } ?>
														<?php } ?>
													</select>
													<select id="so_option<?= $i ?>_slab_id<?= $k ?>" <?php if ($option_val['policy_type'] != 1 and $option_val['is_so'] != 0) {
																											echo 'required="required"';
																										} ?> class="form-control width_15 so_option_slab so_slab_id <?php if ($option_val['is_so'] == 0) {
																																										echo 'display_none';
																																									} ?>" name="data[DiscountBonusPolicyOptionPriceSlab][<?= $i ?>][so_slab_id][]">

													</select>
													<select id="sr_option<?= $i ?>_slab_id<?= $k ?>" <?php if ($option_val['policy_type'] != 1 and $option_val['is_sr'] != 0) {
																											echo 'required="required"';
																										} ?> class="form-control width_15 sr_option_slab sr_slab_id <?php if ($option_val['is_sr'] == 0) {
																																										echo 'display_none';
																																									} ?>" name="data[DiscountBonusPolicyOptionPriceSlab][<?= $i ?>][sr_slab_id][]">

													</select>
													<select id="db_option<?= $i ?>_slab_id<?= $k ?>" <?php if ($option_val['policy_type'] != 1 and $option_val['is_db'] != 0) {
																											echo 'required="required"';
																										} ?> class="form-control width_15 db_option_slab db_slab_id <?php if ($option_val['is_db'] == 0) {
																																										echo 'display_none';
																																									} ?>" name="data[DiscountBonusPolicyOptionPriceSlab][<?= $i ?>][db_slab_id][]">

													</select>
												</div>
											</div>
										</div>
										<script type="text/javascript">
											$(document).ready(function() {
												getProductSlab('<?= $i ?>', '<?= $s_result['discount_product_id'] ?>', '<?= $s_result['so_slab_id'] ?>', '<?= $s_result['sr_slab_id'] ?>', '<?= $s_result['db_slab_id'] ?>', '<?= $k ?>', );
											});
										</script>
									<?php
										$k++;
									}
									?>
								<?php } else { ?>
									<?php
									$k = 1;
									
									foreach ($this->request->data['DiscountBonusPolicyProduct'] as $key => $val) {
									?>
										<div class="form-group option<?= $i ?>_products_wrap <?=' ' .$displaystyle;?>">
											<div class="option<?= $i ?>_slap_set<?= $k ?>">
												<div class="form-group">
													<label>Discount Product :</label>
													<select class="form-control product_common_class option_product" name="data[DiscountBonusPolicyOptionPriceSlab][<?= $i ?>][discount_product_id][]">
														<?php foreach ($products as $p_key => $p_val) { ?>
															<?php if ($this->request->data['DiscountBonusPolicyProduct'][$key]['product_id'] == $p_key) { ?>
																<option value="<?= $p_key; ?>"><?= $p_val; ?></option>
															<?php } ?>
														<?php } ?>
													</select>
													<select id="so_option<?= $i ?>_slab_id<?= $k ?>" <?php if ($option_val['policy_type'] != 1 and $option_val['is_so'] != 0) {
																											echo 'required="required"';
																										} ?> class="form-control width_15 so_option_slab so_slab_id <?php if ($option_val['is_so'] == 0) {
																																										echo 'display_none';
																																									} ?>" name="data[DiscountBonusPolicyOptionPriceSlab][<?= $i ?>][so_slab_id][]">

													</select>
													<select id="sr_option<?= $i ?>_slab_id<?= $k ?>" <?php if ($option_val['policy_type'] != 1 and $option_val['is_sr'] != 0) {
																											echo 'required="required"';
																										} ?> class="form-control width_15 sr_option_slab sr_slab_id <?php if ($option_val['is_sr'] == 0) {
																																										echo 'display_none';
																																									} ?>" name="data[DiscountBonusPolicyOptionPriceSlab][<?= $i ?>][sr_slab_id][]">

													</select>
													<select id="db_option<?= $i ?>_slab_id<?= $k ?>" <?php if ($option_val['policy_type'] != 1 and $option_val['is_db'] != 0) {
																											echo 'required="required"';
																										} ?> class="form-control width_15 db_option_slab db_slab_id <?php if ($option_val['is_db'] == 0) {
																																										echo 'display_none';
																																									} ?>" name="data[DiscountBonusPolicyOptionPriceSlab][<?= $i ?>][db_slab_id][]">

													</select>
												</div>
											</div>
										</div>
										<script type="text/javascript">
											$(document).ready(function() {
												getProductSlab('<?= $i ?>', '<?= $this->request->data['DiscountBonusPolicyProduct'][$key]['product_id'] ?>', '0', 0, 0 '<?= $k ?>', );
											});
										</script>
									<?php
										$k++;
									}
									?>
								<?php } ?>
								
								<div class="deductfromvalue deduct_value_<?= $i ?>" style="margin-bottom: 20px;">
									<?php 
										
										if($option_val['deduct_from_value'] == 1){
											$checkbox = "checked";
										}else{
											$checkbox = " ";
										}
										
									?>
									<?php echo $this->Form->input('deduct_from_value', array('id' => 'deduct_from_value_' . $i , $checkbox,  'value'=>1, 'label' => 'Is Deduct from value : ', 'class' => 'checkbox-inline deduct_from_value', 'name' => 'data[DiscountBonusPolicyOption][' . $i . '][deduct_from_value]', 'onclick'=>'hidden_discount_product_row('. $i .')', 'type' => 'checkbox', 'div' => false, 'hiddenField' => false)); ?>
									
									<br>
								</div>
								

							</div>

						</div>
					<?php
					}
					$total_option = $i;
					?>
				</div>

				<input type="hidden" id="total_product" value="<?= $total_product ?>">

				<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>


<script>
	jQuery(document).ready(function() {
		$("input[type='checkbox']").iCheck('destroy');
		$("input[type='radio']").iCheck('destroy');
		jQuery(".chosen").chosen();
		jQuery(".office_id").chosen();
		jQuery(".office_id").data("placeholder", "Select Offices...").chosen();
		$('.so_special_group_id').data("placeholder", "Select Special Group...").chosen();
		$('.sr_special_group_id').data("placeholder", "Select Special Group...").chosen();

		//$('.deductfromvalue').hide();

	});

	var x = <?= $total_option ?>;

	function addOption() {
		max_fields = 50;
		if (x < max_fields) {
			x++;

			var total_product = $('#total_product').val();

			//alert(total_product);
			var selected = $('.create_for input[type="checkbox"]:checked').map(function(e, el) {
				return $(el).val();
			}).get();
			var checkbox_html = '<label> </label>';
			$.each(selected, function(e, val) {
				if (val == 1) {
					checkbox_html += '<div class="checkbox-inline create_slab_for">\
									<input type="checkbox" name="data[DiscountBonusPolicyOption][' + x + '][create_slab_for][]" value="1" id="create_slab_for' + x + '1" style="">\
									<label for="create_slab_for' + x + '1">SO</label>\
								</div>';
				} else if (val == 2) {
					checkbox_html += '<div class="checkbox-inline create_slab_for">\
									<input type="checkbox" name="data[DiscountBonusPolicyOption][' + x + '][create_slab_for][]" value="2" id="create_slab_for' + x + '2" style="">\
									<label for="create_slab_for' + x + '2">SR</label>\
								</div>';
				} else if (val == 3) {
					checkbox_html += '<div class="checkbox-inline create_slab_for">\
									<input type="checkbox" name="data[DiscountBonusPolicyOption][' + x + '][create_slab_for][]" value="3" id="create_slab_for' + x + '3" style="">\
									<label for="create_slab_for' + x + '3">DB</label>\
								</div>';
				}
			});
			var measurement_unit_for_min_qty = $(".min_qty1 .min_qty_measurement_unit_id").html();
			var html = '<div class="form-group policy_option policy_option' + x + '">\
	<hr></hr><div class="form-group">\
		<div class="input select">\
			<label>Policy Option</label>\
			<select name="data[DiscountBonusPolicyOption][' + x + '][policy_type]" class="form-control policy_option" onchange="showBonusOrDiscount(this.value, ' + x + ')">\
				<option value="0">Only Discount</option>\
				<option value="1">Only Bonus</option>\
				<option value="2">Discount and Bonus</option>\
				<option value="3">Discount or Bonus</option>\
			</select>\
		</div>\
		<button type="button" onclick="removeOption(' + x + ')" class="add_option_button">Remove</button>\
	</div>\
	<div class="form-group slab_create_for' + x + ' slab_create_for">\
	' + checkbox_html + '\
	</div>\
	<div class="form-group required">\
		<div class="input number">\
			<label>Min Qty</label>\
			<input name="data[DiscountBonusPolicyOption][' + x + '][min_qty]" class="form-control" required type="number">\
		</div>\
		<select class="min_qty_measurement_unit_id form-control m_select" name="data[DiscountBonusPolicyOption][' + x + '][min_qty_measurement_unit_id]">\
			' + measurement_unit_for_min_qty + '\
		</select>\
	</div>\
	<div class="form-group required min_value' + x + '">\
		<div class="input number">\
			<label>Min Value</label>\
			<input name="data[DiscountBonusPolicyOption][' + x + '][min_value]" class="form-control" required type="number">\
		</div>\
	</div>\
	<div class="form-group">\
		<div class="input number">\
			<label>Min Memo Value</label>\
			<input name="data[DiscountBonusPolicyOption][' + x + '][min_memo_value]" class="form-control" type="number">\
		</div>\
	</div>\
	<div class="form-group exclusion_inclusion' + x + '">\
		<label> </label>\
		<div class="checkbox-inline create_exclusion_inclusion">\
			<input type="checkbox"  value="1" id="create_exclusion_inclusion_for' + x + '1" style="margin-left: 65px;">\
			<label for="create_exclusion_inclusion_for' + x + '1">Exclusion</label>\
		</div>\
		<div class="checkbox-inline create_exclusion_inclusion">\
			<input type="checkbox"  value="2" id="create_exclusion_inclusion_for' + x + '2" style="margin-left: 65px;">\
			<label for="create_exclusion_inclusion_for' + x + '2">Inclusion</label>\
		</div>\
	</div>\
	<div class="exclusion_product display_none">\
		<div class="exclusion_' + x + '_product_wraps">\
			<div class="form-group">\
				<div class="input select">\
					<label>Exclusion Product : </label>\
					<select name="data[DiscountBonusPolicyOption][' + x + '][exclusion_product_id][]" class="form-control exclusionProduct">\
						<option value="">---- Select Product ----</option>\
						<?= $product_list ?>\
					</select>\
					<input name="data[DiscountBonusPolicyOption][' + x + '][exclusion_min_qty][]" class="form-control m_select"  type="number" placeholder="Min Qty">\
					<button type="button" onClick="addExProduct(' + x + ')" class="add_ex_option_button">Add More</button>\
				</div>\
			</div>\
		</div>\
	</div>\
	<div class="inclusion_product display_none">\
		<div class="inclusion_' + x + '_product_wraps">\
			<div class="form-group">\
				<div class="input select">\
					<label>Inclusion Product : </label>\
					<select name="data[DiscountBonusPolicyOption][' + x + '][inclusion_product_id][]" class="form-control inclusionProduct">\
						<option value="">---- Select Product ----</option>\
						<?= $product_list ?>\
					</select>\
					<input name="data[DiscountBonusPolicyOption][' + x + '][inclusion_min_qty][]" class="form-control m_select"  type="number" placeholder="Min Qty">\
					<button type="button" onClick="addInProduct(' + x + ')" class="add_in_option_button">Add More</button>\
				</div>\
			</div>\
		</div>\
	</div>\
	<div class="bonus_product display_none">\
		<div class="option_' + x + '_product_wraps">\
			<div class="form-group">\
				<div class="input select">\
					<label>Bonus Product</label>\
					<select onchange="get_product_units(this.value,' + x + ',1)" name="data[DiscountBonusPolicyOption][' + x + '][bonus_product_id][]" class="form-control bonus_product_formula">\
						<option value="">---- Select Product ----</option>\
						<?= $product_list ?>\
					</select>\
					<div class="option' + x + '_bonus_product1_wrap"><select class="form-control m_select" name="data[DiscountBonusPolicyOption][' + x + '][measurement_unit_id][]"><option value="">Select Measurement</option></select></div>\
					<input name="data[DiscountBonusPolicyOption][' + x + '][bonus_qty][]" class="form-control m_select onlyBonus"  type="number" placeholder="Bonus Qty">\
					<input name="data[DiscountBonusPolicyOption][' + x + '][bonus_in_hand][]" class="bonus_in_hand form-control width_5 display_none"  type="number" placeholder="DB In Hand">\
					<button type="button" onClick="addBProduct(' + x + ')" class="add_option_button">Add More</button>\
				</div>\
			</div>\
		</div>\
		<div class="default_bonus_product_selection_' + x +'">\
			<div class="form-group">\
				<div class="input select">\
					<label>Default Bonus Pproduct Selection</label>\
					<select onchange="default_product_set('+ x +')" required name="data[DiscountBonusPolicyOption][' + x + '][default_bonus_product_id][]" class="form-control default_bonus_product_formula">\
						<option value="">---- Select Product ----</option>\
						<?= $product_list ?>\
					</select>\
					<button type="button" onClick="adddefualtProduct(' + x + ')" class="add_default_option_button">Add More</button>\
				</div>\
			</div>\
		</div>\
		<div class="formula">\
			<label>Formula</label>\
			<div class="formula_div">\
				<div class="operator">\
					 <button class="operator_name btn btn-xs" name="braces_left" id="braces_left" value="(">(</button>\
	                <button class="operator_name btn btn-xs" name="braces_right" id="braces_right" value=")">)</button>\
	                <button class="operator_name btn btn-xs" name="square_braces_left" id="square_braces_left" value="AND">AND</button>\
	                <button class="operator_name btn btn-xs" name="square_braces_right" id="square_braces_right" value="OR">OR</button>\
				</div>\
				<div class="formula_text">\
					<textarea name="data[DiscountBonusPolicyOption][' + x + '][bonus_formula]" class="product_showing" rows="1" cols="55" readonly=""></textarea>\
					<textarea name="data[DiscountBonusPolicyOption][' + x + '][bonus_formula_with_product_id]" class="product_id_showing" rows="1" cols="55" readonly="" style="display: none;"></textarea>\
					<button class="btn btn-xs btn-danger clear_formula_text pull-right">Clear</button>\
				</div>\
			</div>\
		</div>\
	</div>\
	<div class="discount_product">\
		<div class="form-group">\
			<div class="input text">\
				<label>Discount Amount</label>\
				<input required name="data[DiscountBonusPolicyOption][' + x + '][discount_amount]" class="form-control discountAmount" type="text">\
			</div>\
			<div class="input text">\
				<input name="data[DiscountBonusPolicyOption][' + x + '][discount_in_hand]" class="discount_in_hand form-control width_5 display_none" type="text" placeholder="DB In Hand">\
			</div>\
			<div class="input select">\
				<select id="disccount_type'+x+'" onchange="discounttype('+ x +')" name="data[DiscountBonusPolicyOption][' + x + '][disccount_type]" class="form-control width_5">\
					<option value="0">%</option>\
					<option value="1">Tk</option>\
				</select>\
			</div>\
		</div>\
		<div class="form-group option'+x+'_products_wrap">';
			var i;
			for (i = 1; i <= total_product; i++) {
				option_product = $('.option1_slap_set' + i + ' select.option_product').html();
				so_option_slab = $('.option1_slap_set' + i + ' select.so_option_slab').html();
				sr_option_slab = $('.option1_slap_set' + i + ' select.sr_option_slab').html();
				db_option_slab = $('.option1_slap_set' + i + ' select.db_option_slab').html();
				html += '<div class="option' + x + '_slap_set' + i + '">\
					<div class="form-group">\
						<label>Discount Product :</label>\
						<select class="form-control product_common_class" name="data[DiscountBonusPolicyOptionPriceSlab][' + x + '][discount_product_id][]">\
							' + option_product + '\
						</select>\
						<select class="so_slab_id form-control width_15 display_none" name="data[DiscountBonusPolicyOptionPriceSlab][' + x + '][so_slab_id][]">\
							' + so_option_slab + '\
						</select>\
						<select class="sr_slab_id form-control width_15 display_none" name="data[DiscountBonusPolicyOptionPriceSlab][' + x + '][sr_slab_id][]">\
							' + sr_option_slab + '\
						</select>\
						<select class="db_slab_id form-control width_15 display_none" name="data[DiscountBonusPolicyOptionPriceSlab][' + x + '][db_slab_id][]">\
							' + db_option_slab + '\
						</select>\
					</div>\
				</div>';
			}
			html += '</div>\
		</div>\
		<div class="deductfromvalue deduct_value_' + x + '" style="margin-bottom: 20px;">\
				<label>Is Deduct from value :</label>\
				<input type="checkbox" onclick="hidden_discount_product_row('+ x +')" autocomplete="off" class="checkbox-inline deduct_from_value" value="1" name="data[DiscountBonusPolicyOption][' + x + '][deduct_from_value]" id="deduct_from_value_' + x + '">\
				<br>\
			</div>\
		</div>';

			$('.policy_options').append(html);
		}
	}

	function removeOption(id) {
		$('.policy_option' + id).remove();
	}

	/* function discounttype(v){
		
		var d_type = $("#disccount_type" + v ).val();
		
		if(d_type == 1){
			$('.deduct_value_' + v).show();
		}else{
			$('#deduct_from_value_' + v).removeAttr('checked');
			$('.deduct_value_' + v).hide();
		}

	} */

	function hidden_discount_product_row(v){
		
		$checkbox = $("#deduct_from_value_"+v).is(":checked");

		if($checkbox == true) {
			$('.policy_option' +v+ ' .option'+v+'_products_wrap').hide();
			
			$('.policy_option' +v+ ' .option'+v+'_products_wrap .so_slab_id').removeAttr('required');
			$('.policy_option' +v+ ' .option'+v+'_products_wrap .sr_slab_id').removeAttr('required');
			$('.policy_option' +v+ ' .option'+v+'_products_wrap .db_slab_id').removeAttr('required');
		} else {
			$('.policy_option' +v+ ' .option'+v+'_products_wrap').show();
			
			$('.policy_option' +v+ ' .option'+v+'_products_wrap .so_slab_id').attr('required');
			$('.policy_option' +v+ ' .option'+v+'_products_wrap .sr_slab_id').attr('required');
			$('.policy_option' +v+ ' .option'+v+'_products_wrap .db_slab_id').attr('required');
		}
		
	}

	function showOptions() {
		$('.policy_options').show();
		$('.hide_all').hide();
	}
	/*$('.policy_product_id').ready(function(){
		console.log('here');
		var product_id=$(".policy_product_id").map(function(e, el){
			return $(this).val();
		}).get();
		console.log(product_id);
	});*/


	function get_measurement_units_for_min_qty() {
		var product_id = $(".policy_product_id").map(function(e, el) {
			return $(this).val();
		}).get();
		$.ajax({
			url: "<?php echo BASE_URL; ?>discount_bonus_policies/get_product_units_for_min_qty",
			type: "POST",
			data: {
				product_id: product_id
			},
			success: function(result) {
				if ($(".min_qty1 .min_qty_measurement_unit_id").html(result)) {
					var selected_id = $(".min_qty1 .min_qty_measurement_unit_id").data('selected-id');
					$(".min_qty1 .min_qty_measurement_unit_id").val(selected_id);

					$('.min_qty_measurement_unit_id:not(:first)').each(function() {
						if ($(this).html(result)) {
							var selected_id = $(this).data('selected-id');
							$(this).val(selected_id);
						}
					});
				}
			}
		});
	}

	$(".so_special_group_id").change(function() {
		if ($(".so_special_group_id :selected").length > 0) {
			$('.so_other_settings').attr('disabled', true).trigger("chosen:updated");
		} else {
			$('.so_other_settings').attr('disabled', false).trigger("chosen:updated");
		}
	});

	$(".sr_special_group_id").change(function() {
		if ($(".sr_special_group_id :selected").length > 0) {
			$('.sr_other_settings').attr('disabled', true).trigger("chosen:updated");
		} else {
			$('.sr_other_settings').attr('disabled', false).trigger("chosen:updated");
		}
	});

	function showBonusOrDiscount(value, option_id) {
		$('.policy_option' + option_id + ' .bonus_product').hide();
		$('.policy_option' + option_id + '  .discount_product').hide();

		if (value == 0) {
			$('.policy_option' + option_id + ' .discount_product').show();

			$('.policy_option' + option_id + ' .discount_product  input.discountAmount').attr("required", "required");

			$('.policy_option' + option_id + ' .bonus_product.display_none  select.bonus_product_formula').removeAttr("required");
			$('.policy_option' + option_id + ' .bonus_product.display_none  select.m_select').removeAttr("required");
			$('.policy_option' + option_id + ' .bonus_product.display_none  input.onlyBonus').removeAttr("required");


			var soslab = $('.policy_option' + option_id + ' .slab_create_for .create_slab_for input[type="checkbox"][value="1"]:checked').val();
			var srslab = $('.policy_option' + option_id + ' .slab_create_for .create_slab_for input[type="checkbox"][value="2"]:checked').val();
			var dbslab = $('.policy_option' + option_id + ' .slab_create_for .create_slab_for input[type="checkbox"][value="3"]:checked').val();

			if (soslab == 1) {
				//$(this).parent().parent().parent().find('.discount_product .so_slab_id').attr("required","required");
				$('.policy_option' + option_id + ' .discount_product  select.so_slab_id').attr("required", "required");
			}

			if (srslab == 2) {
				$('.policy_option' + option_id + ' .discount_product  select.sr_slab_id').attr("required", "required");
			}

			if (dbslab == 3) {
				$('.policy_option' + option_id + ' .discount_product  select.db_slab_id').attr("required", "required");
			}


		} else if (value == 1) {
			$('.policy_option' + option_id + ' .bonus_product').show();

			$('.policy_option' + option_id + ' .discount_product  input.discountAmount').removeAttr("required");

			$('.policy_option' + option_id + ' .bonus_product.display_none  select.bonus_product_formula').attr("required", "required");
			$('.policy_option' + option_id + ' .bonus_product.display_none  select.m_select').attr("required", "required");
			$('.policy_option' + option_id + ' .bonus_product.display_none  input.onlyBonus').attr("required", "required");

			$('.policy_option' + option_id + ' .discount_product  input.discountAmount').removeAttr("required");
			$('.policy_option' + option_id + ' .discount_product  select.so_slab_id').removeAttr("required");
			$('.policy_option' + option_id + ' .discount_product  select.sr_slab_id').removeAttr("required");
			$('.policy_option' + option_id + ' .discount_product  select.db_slab_id').removeAttr("required");
			$('.policy_option' + option_id + ' .discount_product  select.option_product').removeAttr("required");

		} else {
			$('.policy_option' + option_id + ' .bonus_product').show();
			$('.policy_option' + option_id + '  .discount_product').show();

			//$('.policy_option'+option_id+' .discount_product  input.discountAmount').removeAttr("required");

			$('.policy_option' + option_id + ' .bonus_product.display_none  select.bonus_product_formula').attr("required", "required");
			$('.policy_option' + option_id + ' .bonus_product.display_none  select.m_select').attr("required", "required");
			$('.policy_option' + option_id + ' .bonus_product.display_none  input.onlyBonus').attr("required", "required");

			$('.policy_option' + option_id + ' .discount_product input.discountAmount').attr("required", "required");

			var soslab = $('.policy_option' + option_id + ' .slab_create_for .create_slab_for input[type="checkbox"][value="1"]:checked').val();
			var srslab = $('.policy_option' + option_id + ' .slab_create_for .create_slab_for input[type="checkbox"][value="2"]:checked').val();
			var dbslab = $('.policy_option' + option_id + ' .slab_create_for .create_slab_for input[type="checkbox"][value="3"]:checked').val();

			if (soslab == 1) {
				//$(this).parent().parent().parent().find('.discount_product .so_slab_id').attr("required","required");
				$('.policy_option' + option_id + ' .discount_product  select.so_slab_id').attr("required", "required");
			}

			if (srslab == 2) {
				$('.policy_option' + option_id + ' .discount_product  select.sr_slab_id').attr("required", "required");
			}

			if (dbslab == 3) {
				$('.policy_option' + option_id + ' .discount_product  select.db_slab_id').attr("required", "required");
			}



			//$('.policy_option'+option_id+' .discount_product  select.so_slab_id').attr("required","required");
			//$('.policy_option'+option_id+' .discount_product  select.sr_slab_id').attr("required","required");
			//$('.policy_option'+option_id+' .discount_product  select.db_slab_id').attr("required","required");
			$('.policy_option' + option_id + ' .discount_product  select.option_product').attr("required", "required");

		}
	}
	// var selected_slab={};
	var selected_slab = <?php echo json_encode($selected_slab); ?>;

	$(document).ready(function() {
		var max_fields = 15;
		var product_wrapper = $(".input_products_wrap");
		var add_product_button = $(".add_product_button");



		var x = 1;
		$(add_product_button).click(function(e) {

			e.preventDefault();
			if (x < max_fields) {
				x++;

				$(product_wrapper).append('<div class="slap_set' + x + '"><div class="form-group"><label>Product :</label><select class="form-control" name="data[DiscountBonusPolicyProduct][policy_product_id][]" onchange="add_option_product(this, ' + x + ')" id="policy_product_id' + x + '" required><option value="">---- Select Product -----</option>' + '<?= $product_list; ?>' + '</select><a href="#" class="remove_product_field btn btn-primary hide_all btn-xs">Remove</a></div>');

				$('#total_product').val(x);

			}

		});


		$(product_wrapper).on("click", ".remove_product_field", function(e) {
			e.preventDefault();
			$(this).parent('div').remove();
			$('.option1_slap_set' + x).remove();
			x--;
		});
		$('.start_date').on('changeDate', function() {
			var effective_date = $(this).val();
			if (effective_date) {
				$.ajax({
					url: '<?= BASE_URL ?>/discount_bonus_policies/get_so_sr_special_group',
					type: "POST",
					data: {
						'effective_date': effective_date
					},
					data_type: 'JSON',
					success: function(response) {
						var res = $.parseJSON(response);
						$('.so_special_group_id').html(res.so_special).data("placeholder", "Select Special Group...").chosen();
						$('.sr_special_group_id').html(res.sr_special).data("placeholder", "Select Special Group...").chosen();
						$('.sr_special_group_id').trigger("chosen:updated");
						$('.so_special_group_id').trigger("chosen:updated");
					}

				});
			}
		});
		$("body").on('click', '.create_for input[type="checkbox"]', function() {
			var selected = $('.create_for input[type="checkbox"]:checked').map(function(e, el) {
				return $(el).val();
			}).get();
			$(".slab_create_for").each(function(i, val) {
				var classes = $(this).attr('class');
				classes = classes.split(" ");
				classes = classes[1];
				classes = classes.slice(15);
				var checkbox_html = '<label> </label>';
				$.each(selected, function(e, val) {
					if (val == 1) {
						var checked = '';
						if (selected_slab[classes] != undefined && selected_slab[classes].is_so != undefined && selected_slab[classes].is_so != 0)
							checked = 'checked=""';
						checkbox_html += '<div class="checkbox-inline create_slab_for">\
										<input type="checkbox" name="data[DiscountBonusPolicyOption][' + classes + '][create_slab_for][]" value="1" id="create_slab_for' + classes + '1" style="" ' + checked + '>\
										<label for="create_slab_for' + classes + '1">SO</label>\
									</div>';
					} else if (val == 2) {
						var checked = '';
						if (selected_slab[classes] != undefined && selected_slab[classes].is_sr != undefined && selected_slab[classes].is_sr != 0)
							checked = 'checked=""';
						checkbox_html += '<div class="checkbox-inline create_slab_for">\
										<input type="checkbox" name="data[DiscountBonusPolicyOption][' + classes + '][create_slab_for][]" value="2" id="create_slab_for' + classes + '2" style="" ' + checked + '>\
										<label for="create_slab_for' + classes + '2">SR</label>\
									</div>';
					} else if (val == 3) {
						var checked = '';
						if (selected_slab[classes] != undefined && selected_slab[classes].is_db != undefined && selected_slab[classes].is_db != 0)
							checked = 'checked=""';
						checkbox_html += '<div class="checkbox-inline create_slab_for">\
										<input type="checkbox" name="data[DiscountBonusPolicyOption][' + classes + '][create_slab_for][]" value="3" id="create_slab_for' + classes + '3" style="" ' + checked + '>\
										<label for="create_slab_for' + classes + '3">DB</label>\
									</div>';
					}
				});
				$(".slab_create_for" + classes).html(checkbox_html);
				if (selected_slab[classes] != undefined && selected_slab[classes].is_db != undefined && selected_slab[classes].is_db != 0) {
					$(".slab_create_for" + classes + " .create_slab_for").each(function() {
						$(this).parent().parent().parent().find('.bonus_product .bonus_in_hand').removeClass('display_none');
						$(this).parent().parent().parent().find('.discount_product .discount_in_hand').removeClass('display_none');
						$(this).parent().parent().parent().find('.discount_product .db_slab_id').removeClass('display_none');
					});
				}
			});

		});

		$("body").on('click', '.create_for input[type="checkbox"][value="1"]', function(e) {
			if ($(this).prop('checked')) {
				$('.so').show();
				$('.chosen').trigger("chosen:updated");
			} else {
				$('.so').hide();
			}
			// put_slab_create_for();
		});
		$("body").on('click', '.create_for input[type="checkbox"][value="2"]', function(e) {
			if ($(this).prop('checked')) {
				$('.sr').show();
			} else {
				$('.sr').hide();
			}
			// put_slab_create_for();
		});

		$("body").on('click', '.create_for input[type="checkbox"][value="3"]', function(e) {
			if ($(this).prop('checked')) {

			} else {
				$('.create_slab_for').each(function() {
					$(this).parent().parent().parent().find('.bonus_product .bonus_in_hand').addClass('display_none');
					$(this).parent().parent().parent().find('.discount_product .discount_in_hand').addClass('display_none');
					$(this).parent().parent().parent().find('.discount_product .db_slab_id').addClass('display_none');
				});
			}
			// put_slab_create_for();
		});

		$("body").on('click', '.create_exclusion_inclusion input[type="checkbox"][value="1"]', function(e) {

			if ($(this).prop('checked')) {
				$(this).parent().parent().next('.exclusion_product ').removeClass('display_none');

				$(this).parent().parent().next('.exclusion_product').find('.exclusionProduct').attr("required", "required");
			} else {
				$(this).parent().parent().next('.exclusion_product ').addClass('display_none');
				$(this).parent().parent().next('.exclusion_product').find('.form-group:not(:first)').remove();
				$(this).parent().parent().next('.exclusion_product').find('.form-group input').val('');
				$(this).parent().parent().next('.exclusion_product').find('.form-group select').val('');

				$(this).parent().parent().next('.exclusion_product').find('.exclusionProduct').removeAttr("required");
			}
		});
		$("body").on('click', '.create_exclusion_inclusion input[type="checkbox"][value="2"]', function(e) {

			if ($(this).prop('checked')) {
				$(this).parent().parent().next().next('.inclusion_product ').removeClass('display_none');

				$(this).parent().parent().next().next('.inclusion_product').find('.inclusionProduct').attr("required", "required");
			} else {
				$(this).parent().parent().next().next('.inclusion_product ').addClass('display_none');
				$(this).parent().parent().next().next('.inclusion_product ').find('.form-group:not(:first)').remove();
				$(this).parent().parent().next().next('.inclusion_product ').find('.form-group input').val('');
				$(this).parent().parent().next().next('.inclusion_product ').find('.form-group select').val('');

				$(this).parent().parent().next().next('.inclusion_product').find('.inclusionProduct').removeAttr("required");
			}
		});


		$('body').on('click', '.remove_ex_in_product', function() {
			$(this).parent().remove();
		});
		/*For showing DB part. Like in hand discount . in hand bonus*/

		$("body").on('click', '.create_slab_for input[type="checkbox"][value="3"]', function(e) {
			var classes = $(this).parent().parent().attr('class');
			classes = classes.split(" ");
			classes = classes[1];
			classes = classes.slice(15);
			if (selected_slab[classes] == undefined) {
				selected_slab[classes] = {};
			}
			if (selected_slab[classes].is_db == undefined) {
				selected_slab[classes].is_db = 0;
			}

			if ($(this).prop('checked')) {
				$(this).parent().parent().parent().find('.bonus_product .bonus_in_hand').removeClass('display_none');
				$(this).parent().parent().parent().find('.discount_product .discount_in_hand').removeClass('display_none');
				$(this).parent().parent().parent().find('.discount_product .db_slab_id').removeClass('display_none');

				var optionVal = $(this).parent().parent().parent().find('.policy_option').val();
				if (optionVal != 1) {
					$(this).parent().parent().parent().find('.discount_product .db_slab_id').attr("required", "required");
				}

				selected_slab[classes].is_db = 1;
			} else {
				$(this).parent().parent().parent().find('.bonus_product .bonus_in_hand').addClass('display_none');
				$(this).parent().parent().parent().find('.discount_product .discount_in_hand').addClass('display_none');
				$(this).parent().parent().parent().find('.discount_product .db_slab_id').addClass('display_none');

				var optionVal = $(this).parent().parent().parent().find('.policy_option').val();
				if (optionVal != 1) {
					$(this).parent().parent().parent().find('.discount_product .db_slab_id').removeAttr("required");
				}

				selected_slab[classes].is_db = 0;
			}
			// put_slab_create_for();
		});
		$("body").on('click', '.create_slab_for input[type="checkbox"][value="1"]', function(e) {
			var classes = $(this).parent().parent().attr('class');
			classes = classes.split(" ");
			classes = classes[1];
			classes = classes.slice(15);
			if (selected_slab[classes] == undefined) {
				selected_slab[classes] = {};
			}
			if (selected_slab[classes].is_so == undefined) {
				selected_slab[classes].is_so = 0;
			}

			if ($(this).prop('checked')) {
				$(this).parent().parent().parent().find('.discount_product .so_slab_id').removeClass('display_none');

				var optionVal = $(this).parent().parent().parent().find('.policy_option').val();
				if (optionVal != 1) {
					$(this).parent().parent().parent().find('.discount_product .so_slab_id').attr("required", "required");
				}



				selected_slab[classes].is_so = 1;
			} else {
				$(this).parent().parent().parent().find('.discount_product .so_slab_id').addClass('display_none');

				var optionVal = $(this).parent().parent().parent().find('.policy_option').val();
				if (optionVal != 1) {
					$(this).parent().parent().parent().find('.discount_product .so_slab_id').removeAttr("required");
				}


				selected_slab[classes].is_so = 0;
			}
			// put_slab_create_for();
		});
		$("body").on('click', '.create_slab_for input[type="checkbox"][value="2"]', function(e) {
			var classes = $(this).parent().parent().attr('class');
			classes = classes.split(" ");
			classes = classes[1];
			classes = classes.slice(15);
			if (selected_slab[classes] == undefined) {
				selected_slab[classes] = {};
			}
			if (selected_slab[classes].is_sr == undefined) {
				selected_slab[classes].is_sr = 0;
			}
			if ($(this).prop('checked')) {
				$(this).parent().parent().parent().find('.discount_product .sr_slab_id').removeClass('display_none');

				var optionVal = $(this).parent().parent().parent().find('.policy_option').val();
				if (optionVal != 1) {
					$(this).parent().parent().parent().find('.discount_product .sr_slab_id').attr("required", "required");
				}

				selected_slab[classes].is_sr = 1;
			} else {
				$(this).parent().parent().parent().find('.discount_product .sr_slab_id').addClass('display_none');

				var optionVal = $(this).parent().parent().parent().find('.policy_option').val();
				if (optionVal != 1) {
					$(this).parent().parent().parent().find('.discount_product .sr_slab_id').removeAttr("required");
				}

				selected_slab[classes].is_sr = 0;
			}
			// put_slab_create_for();
		});

		$('.so_special_group_id').trigger('change');
		$('.sr_special_group_id').trigger('change');
		$('body').on('change', '.bonus_product_formula', function() {
			$(this).parent().parent().parent().next('.formula').find('.formula_div .formula_text textarea').val('');
			$(this).parent().parent().parent().next('.formula').find('.formula_div .operator .product_operator').remove();
			var product_operator = '';
			$(this).parent().parent().parent().find('.bonus_product_formula').each(function(e, el) {
				if ($(this).val())
					product_operator += '<button class="operator_name product_operator btn btn-xs"   value="' + $(this).val() + '">' + $("option:selected", this).text() + '</button>\n';
			});
			$(this).parent().parent().parent().next('.formula').find('.formula_div .operator').append(product_operator);
		});
		$('body').on('click', '.clear_formula_text', function(e) {
			e.preventDefault();
			$(this).prev('textarea').val('');
			$(this).prev().prev('textarea').val('');
		});
		$('body').on('click', '.operator_name', function(e) {
			e.preventDefault();
			var btn_txt_val = $(this).val();
			var btn_txt = $(this).text();
			var previous_text_id = $(this).parent().next('.formula_text').find('textarea.product_id_showing').val();
			var n = previous_text_id.split(" ");
			var last_index = n[n.length - 1];
			var set_check = previous_text_id.split(")");
			if (last_index == btn_txt_val) {
				alert("Same operator cannot be inserted multiple time");
				return;
			} else if (set_check.length > 2) {
				alert("Only two set are allowed");
				return;
			} else if (last_index == '(' && btn_txt_val == ')') {
				alert("Blank set not allowed");
				return;
			} else if (last_index == '(' && (btn_txt_val == 'AND' || btn_txt_val == 'OR')) {
				alert("Select Product First");
				return;
			}
			$(this).parent().next('.formula_text').find('textarea.product_id_showing').val(previous_text_id + ' ' + btn_txt_val);
			var previous_text = $(this).parent().next('.formula_text').find('textarea.product_showing').val();
			$(this).parent().next('.formula_text').find('textarea.product_showing').val(previous_text + ' ' + btn_txt);


		});
	});



	function add_option_product(sel, x) {
		var product_id = sel.value;
		var product_name = sel.options[sel.selectedIndex].text;

		var max_fields = 15;

		//var x = 1;
		if (x < max_fields) {

			var slab_result = null;
			$.ajax({
				url: "<?php echo BASE_URL; ?>admin/discount_bonus_policies/get_slab_list",
				type: "POST",
				data: {
					product_id: product_id
				},
				success: function(result) {
					$('.option1_slap_set' + x).remove();
					$(".option1_products_wrap").append(
						'<div class="option1_slap_set' + x + '">\
						<div class="form-group">\
						<label>Discount Product :</label>\
						<select class="form-control option_product" name="data[DiscountBonusPolicyOptionPriceSlab][1][discount_product_id][]" required>\
							<option value="' + product_id + '">' + product_name + '</option>' + '\
						</select>\
						<select class="form-control width_15 so_option_slab so_slab_id display_none" name="data[DiscountBonusPolicyOptionPriceSlab][1][so_slab_id][]">' + res.so + '</select>\
						<select class="form-control width_15 sr_option_slab sr_slab_id display_none" name="data[DiscountBonusPolicyOptionPriceSlab][1][sr_slab_id][]">' + res.sr + '</select>\
						<select class="form-control width_15 db_option_slab db_slab_id display_none" name="data[DiscountBonusPolicyOptionPriceSlab][1][db_slab_id][]">' + res.db + '</select>\
					</div>'
					);


					//x++;
				}
			});



			//alert(slab_result);



			//get_product_product_slab(product_id)

		}

	}

	var dpc = 1;
	var dtemp_option_ids = [];

	function adddefualtProduct(o) {
		max_fields = 50;

		if (jQuery.inArray(o, dtemp_option_ids) <= -1) {
			dtemp_option_ids.push(o);
		}

		dpc = ($(".form-group.search.default_product" + o).length + 2);

		function pc_checking(pc_c) {
			if ($('.default_product' + o + '.pc_' + pc_c).length > 0) {
				pc_c = pc_c + 1;
				pc_checking(pc_c);
			} else
				dpc = pc_c;
		}
		pc_checking(dpc);
		if (dpc < max_fields) {
			var db_in_hand_display_none = ' display_none';

			var default_bonus_html = '';

			var bonus_product_ids = {};
		
			$('.option_'+o+'_product_wraps .bonus_product_formula').each(function(e,value) {
				bonus_product_ids[$(value).find('option:selected').val()] =$(value).find('option:selected').text();
			});

			$.each(bonus_product_ids, function( index, value ) {
				//alert( index + ": " + value );
				default_bonus_html += '<option value="'+index+'">'+ value +'</option>';
			});
			
			html =
				'<div class="form-group default_product' + o + ' pc_' + dpc + '">\
					<div class="input select">\
						<label>Default Bonus Pproduct Selection</label>\
						<select onchange="default_product_set('+o+')" required  name="data[DiscountBonusPolicyOption][' + o + '][default_bonus_product_id][]" class="form-control default_bonus_product_formula">\
							<option value="">---- Select Product ----</option>\
							'+ default_bonus_html +'\
						</select>\
						<button type="button" onclick="removeDefaultProduct(' + o + ',' + dpc + ')" class="add_default_option_button">Remove</button>\
					</div>\
				</div>';

			$('.default_bonus_product_selection_'+ o).append(html);
		} else {
			alert('Maximum 50 bonus products are allowed!');
		}
		
	}

	function get_product_product_slab(product_id) {
		$.ajax({
			url: "<?php echo BASE_URL; ?>admin/discount_bonus_policies/get_slab_list",
			type: "POST",
			data: {
				product_id: product_id
			},
			success: function(result) {
				console.log(result);
				$$("#slab_id" + id).html(result);
			}
		});
	}

	function getProductSlab(id, product_id, parent_so_slab_id, parent_sr_slab_id, parent_db_slab_id, s_id) {

		var effective_date = $('.start_date').val();
		if (!effective_date) {
			effective_date = <?php echo $this->request->data['DiscountBonusPolicy']['start_date']; ?>;
		}
		var dataString = 'product_id=' + product_id + '&effective_date=' + effective_date;
		$.ajax({
			url: "<?php echo BASE_URL; ?>admin/discount_bonus_policies/get_slab_list",
			type: "POST",
			data: dataString,
			success: function(result) {
				var res = $.parseJSON(result);
				$("#so_option" + id + "_slab_id" + s_id).html(res.so);
				$("#sr_option" + id + "_slab_id" + s_id).html(res.sr);
				$("#db_option" + id + "_slab_id" + s_id).html(res.db);
				if (parent_so_slab_id) {
					$("#so_option" + id + "_slab_id" + s_id).val(parent_so_slab_id);
				}
				if (parent_sr_slab_id) {
					$("#sr_option" + id + "_slab_id" + s_id).val(parent_sr_slab_id);
				}
				if (parent_db_slab_id) {
					$("#db_option" + id + "_slab_id" + s_id).val(parent_db_slab_id);
				}
				//alert(parent_slab_id);
			}
		});
	}

	function get_product_units(product_id, x, p) {
		$.ajax({
			url: "<?php echo BASE_URL; ?>admin/group_wise_discount_bonus_policies/get_product_units",
			type: "POST",
			data: {
				product_id: product_id
			},
			success: function(result) {
				//$(".option"+x+"_bonus_product"+p+"_wrap").html('<select class="form-control m_select" name="data[DiscountBonusPolicyOption]['+x+'][measurement_unit_id][]">'+result+'</select>');
				$(".option" + x + "_bonus_product" + p + "_wrap").html('<select class="form-control m_select" name="data[DiscountBonusPolicyOption][' + x + '][measurement_unit_id][]">' + result + '</select>');
			}
		});

		default_bonus_product_selection_option(x);

	}

	function default_bonus_product_selection_option(x){

		var bonus_product_ids = {};

		$('.option_'+x+'_product_wraps .bonus_product_formula').each(function(e,value) {
			bonus_product_ids[$(value).find('option:selected').val()] =$(value).find('option:selected').text();
		});

		$('.default_bonus_product_selection_'+x+ ' .default_bonus_product_formula').empty();

		var html = '<option value="">----Select Product----</option>';
		$.each(bonus_product_ids, function( index, value ) {
			//alert( index + ": " + value );
			html += '<option value="'+index+'">'+ value +'</option>';
		});
		$('.default_bonus_product_selection_'+x+ ' .default_bonus_product_formula').append(html);

	}

	function getProductUnits(o, p, product_id, parent_unit_id) {
		//alert(parent_unit_id);
		var dataString = 'product_id=' + product_id + '&parent_unit_id=' + parent_unit_id;

		$.ajax({
			url: "<?php echo BASE_URL; ?>admin/group_wise_discount_bonus_policies/get_product_units",
			type: "POST",
			data: dataString,
			success: function(result) {
				//$(".option"+x+"_bonus_products_wrap").html('<label>Measurement Unit</label><select class="form-control" name="data[DiscountBonusPolicyOption]['+x+'][measurement_unit_id]">'+result+'</select>');
				//$(".option"+id+"_bonus_products_wrap").html('<label>Measurement Unit</label><select class="form-control" name="data[DiscountBonusPolicyOption]['+id+'][measurement_unit_id]">'+result+'</select>');
				$(".option" + o + "_bonus_product" + p + "_wrap").html('<select required class="form-control m_select" name="data[DiscountBonusPolicyOption][' + o + '][measurement_unit_id][]">' + result + '</select>');
			}
		});
	}


	var pc = 1;
	var temp_option_ids = [];

	function addBProduct(o) {
		max_fields = 50;

		if (jQuery.inArray(o, temp_option_ids) <= -1) {
			temp_option_ids.push(o);
		}
		pc = ($(".form-group.search.b_product" + o).length + 2);

		function pc_checking(pc_c) {
			if ($('.b_product' + o + '.pc_' + pc_c).length > 0) {
				pc_c = pc_c + 1;
				pc_checking(pc_c);
			} else
				pc = pc_c;
		}
		pc_checking(pc);

		if (pc < max_fields) {
			var db_in_hand_display_none = ' display_none';
			console.log(selected_slab[o]);
			if (selected_slab[o] != undefined && selected_slab[o].is_db != undefined && selected_slab[o].is_db != 0) {
				db_in_hand_display_none = '';
			}
			html =
				'<div class="form-group b_product' + o + ' pc_' + pc + '">\
			<div class="input select">\
				<label>Bonus Product</label>\
				<select required onchange="get_product_units(this.value,' + o + ',' + pc + ')" name="data[DiscountBonusPolicyOption][' + o + '][bonus_product_id][]" class="form-control bonus_product_formula">\
					<option value="">---- Select Product ----</option>\
					<?= $product_list ?>\
				</select>\
				<div class="b_product' + o + ' option' + o + '_bonus_product' + pc + '_wrap"><select required class="form-control m_select" name="data[DiscountBonusPolicyOption][' + o + '][measurement_unit_id][]"><option value="">Select Measurement</option></select></div>\
				<div class="input number"><input name="data[DiscountBonusPolicyOption][' + o + '][bonus_qty][]" required class="form-control width_5 onlyBonus" id="bonus_qty' + o + '" placeholder="Bonus Qty" type="number"></div>\
				<div class="input number"><input name="data[DiscountBonusPolicyOption][' + o + '][bonus_in_hand][]" class="bonus_in_hand form-control	width_5' + db_in_hand_display_none + '" id="bonus_qty' + o + '" placeholder="DB In Hand" type="number"></div>\
				<button type="button" onclick="removeBProduct(' + o + ',' + pc + ')" class="add_option_button">Remove</button>\
			</div>\
		</div>';

			$('.option_' + o + '_product_wraps').append(html);
		} else {
			alert('Maximum two bonus products are allowed!');
		}
	}

	function removeDefaultProduct(o, pc) {
		$('.default_product' + o + '.pc_' + pc).remove();
		$('.default_bonus_product_formula').trigger('change');
	}

	function removeBProduct(o, pc) {
		$('.b_product' + o + '.pc_' + pc).remove();
		$('.bonus_product_formula').trigger('change');
	}

	function pushBProductOption(o) {
		if (jQuery.inArray(o, temp_option_ids) <= -1) {
			temp_option_ids.push(o);
		}
	}

	function removeBProductOption(o) {
		temp_option_ids.remove(o);
		$('.bonus_product_formula').trigger('change');
	}

	function addExProduct(o) {
		html =
			'<div class="form-group exclusion_product' + o + '">\
		<div class="input select">\
			<label>Exclusion Product : </label>\
			<select name="data[DiscountBonusPolicyOption][' + o + '][exclusion_product_id][]" class="form-control exclusionProduct">\
				<option value="">---- Select Product ----</option>\
				<?= $product_list ?>\
			</select>\
		</div>\
		<div class="input number"><input name="data[DiscountBonusPolicyOption][' + o + '][exclusion_min_qty][]" class="form-control m_select" id="exclusion_min_qty' + o + '" placeholder="Min Qty" type="number"></div>\
		<button type="button" class="add_option_button remove_ex_in_product">Remove</button>\
	</div>';

		$('.exclusion_' + o + '_product_wraps').append(html);

	}

	function addInProduct(o) {
		html =
			'<div class="form-group inclusion_product' + o + '">\
		<div class="input select">\
			<label>Inclusion Product : </label>\
			<select name="data[DiscountBonusPolicyOption][' + o + '][inclusion_product_id][]" class="form-control inclusionProduct">\
				<option value="">---- Select Product ----</option>\
				<?= $product_list ?>\
			</select>\
		</div>\
		<div class="input number"><input name="data[DiscountBonusPolicyOption][' + o + '][inclusion_min_qty][]" class="form-control m_select" id="inclusion_min_qty' + o + '" placeholder="Min Qty" type="number"></div>\
		<button type="button" class="add_option_button remove_ex_in_product">Remove</button>\
	</div>';

		$('.inclusion_' + o + '_product_wraps').append(html);

	}


	function default_product_set(x){

		var product_change_flag = 1;
		var product_id_list = '';
		//alert(x);

		$('.default_bonus_product_selection_'+x+ ' .default_bonus_product_formula').each(function(e, value) {
			
			if ($(value).find('option:selected').val() != '') {

				if (product_id_list.search($(value).find('option:selected').val()) == -1) {
					product_id_list = $(value).find('option:selected').val() + ',' + product_id_list;
				} else {
					alert("This poduct already exists");
					product_change_flag = 0;
					$(value).val('').attr('selected', true);
					//$('.default_bonus_product_selection_'+x+ ' .default_bonus_product_formula').val("");
				}

			} 

		});
	}



	Array.prototype.remove = function() {
		var what, a = arguments,
			L = a.length,
			ax;
		while (L && this.length) {
			what = a[--L];
			while ((ax = this.indexOf(what)) !== -1) {
				this.splice(ax, 1);
			}
		}
		return this;
	};
</script>




<script>
	//FOR DISCOUNT PRODUCT AND SLAB
	$(document).ready(function() {
		$("body").on("change", ".product_common_class", function() {
			var selected_product_id = $(this).attr('id');
			var att_id_text = selected_product_id.split("_");
			//alert(att_id_text[0]);
			var att_id_text0 = att_id_text[0];
			var att_id_text1 = att_id_text[1];
			var option_id = att_id_text0.toString().slice(6);
			var slab_id = att_id_text1.toString().slice(7);

			var product_id = $(this).val();
			$.ajax({
				url: "<?php echo BASE_URL; ?>admin/discount_bonus_policies/get_slab_list",
				type: "POST",
				data: {
					product_id: product_id
				},
				success: function(result) {
					$("#option" + option_id + "_slab" + slab_id).html(result);
				}
			});
		});

		$("body").on("change", ".default_bonus_product_formula", function() {

			var product_change_flag = 1;
            var product_id_list = '';

            $('.default_bonus_product_formula').each(function() {
				console.log(product_id_list.search($(this).val()));
                if ($(this).val() != '') {
                    if (product_id_list.search($(this).val()) == -1) {
                        product_id_list = $(this).val() + ',' + product_id_list;
                    } else {
                        alert("This poduct already exists");
						product_change_flag = 0;
                        return false;
                    }
					console.log(product_id_list.search($(this).val()));
                } 
            });
		});

		


	});



	$(window).on('load', function() {
		get_measurement_units_for_min_qty();
		if ($('.create_for input[type="checkbox"][value="2"]').prop('checked') == false) {
			$('.sr').hide();
		}
		if ($('.create_for input[type="checkbox"][value="1"]').prop('checked') == false) {
			$('.so').hide();
		}
	});
</script>