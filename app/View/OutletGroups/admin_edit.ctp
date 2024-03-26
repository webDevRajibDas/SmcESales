<?php
$OutletGroup = new OutletGroupsController();
/* echo "<pre>";
	print_r($this->request->data['Project']);
	exit; */
?>

<style>
	.outlet_item {
		background-color: #367FA9;
		color: #fff;
		padding: 5px 4px;
		border-radius: 4px;
		margin-right: 5px;
	}

	.icon {
		font-size: 11px;
		padding-left: 2px;
		cursor: pointer;
	}

	.hidde_select {
		background-color: #367FA9;
		width: .5px;
		border: 0px;
		display: none;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-plus"></i> <?php echo __('Update Group'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<?php echo $this->Form->create('OutletGroup', array('role' => 'form')); ?>

				<div class="form-group">
					<?php echo $this->Form->input('name', array('type' => 'text', 'class' => 'form-control', 'label' => 'Group Name:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'empty' => '---- Select Territory')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('thana_id', array('id' => 'thana_id', 'class' => 'form-control thana_id', 'empty' => '--- Select---', 'options' => '', 'label' => 'Thana')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'name' => '', 'empty' => '---- All----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'name' => '', 'empty' => '---- All----')); ?>
					<a href="#" id="add_outlet" class="btn btn-primary btn-sm">Add</a>
					<a href="#" id="remove_outlet" class="btn btn-primary btn-sm">Remove All</a>
				</div>
				<div class="form-group">
					<div class="item_box" style="width:50%;float:left;margin-left: 10%">
						<h5>Outlet List :</h5>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="25%">Office</th>
									<th width="20%">Territory</th>
									<th width="25%">Market</th>
									<th width="25%">Outlet</th>
									<th width="5%">#</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if (!empty($this->request->data['OutletGroupToOutlet'])) {
									foreach ($this->request->data['OutletGroupToOutlet'] as $val) {
										$outlet_info = $OutletGroup->get_outlet_info($val['outlet_id']);
								?>
										<tr>
											<td>
												<?= $outlet_info['Office']['office_name'] ?>
											</td>
											<td>
												<?= $outlet_info['Territory']['name'] ?>
											</td>
											<td>
												<?= $outlet_info['Market']['name'] ?>
											</td>
											<td>
												<?= $outlet_info['Outlet']['name'] ?>
												<select class="hidde_select" name="data[OutletGroupToOutlet][outlet_id][]">
													<option value="<?= $val['outlet_id'] ?>"><?= $val['outlet_name'] ?></option>
												</select>
												<select class="hidde_select" name="data[OutletGroupToOutlet][outlet_name][]">
													<option value="<?= $val['outlet_name'] ?>"><?= $val['outlet_name'] ?></option>
												</select>
											</td>
											<td>
												<i id="<?= $val['outlet_id'] ?>" class="glyphicon glyphicon-remove icon"></i>
											</td>
										</tr>
										</span>
								<?php
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>




				<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>

</div>
<script>
	$(document).ready(function() {
		var outlet_info = new Array();
		var max_fields = 5;
		var wrapper = $(".input_fields_wrap");
		var add_button = $(".add_field_button");

		var x = 1;
		$(add_button).click(function(e) {
			e.preventDefault();
			if (x < max_fields) {
				x++;
				$(wrapper).append('<div class="slap_set"><div class="form-group"><label>MinQty :</label><input class="form-control" name="data[ProductCombination][min_qty][]" type="number" required></div><div class="form-group"><label for="price">Price :</label><input class="form-control" name="data[ProductCombination][price][]" type="text" required></div><label></label><a href="#" class="remove_field btn btn-primary btn-xs">Remove</a></div>');
			}
		});

		$(wrapper).on("click", ".remove_field", function(e) {
			e.preventDefault();
			$(this).parent('div').remove();
			x--;
		});


	});
</script>
<script>
	$(document).ready(function() {
		$("#add_outlet").click(function() {

			var btn = $(".item_box>table>tbody").html();
			var all_val = $("#outlet_id option:selected").val();

			if (all_val == 'all') {
				var outlet_list = [];
				$("#outlet_id option").each(function() {
					var outlet_id = $(this).attr("value");
					var outlet_name = $(this).html();
					if (outlet_id != 'all') {
						outlet_list[outlet_id] = outlet_name;
					}
				});
				for (var key in outlet_list) {
					var outlet_details = outlet_info[key];
					var office_name = outlet_details.office;
					var territory_name = outlet_details.territory;
					var market_name = outlet_details.market;
					var outlet_id = key;
					var outlet_name = outlet_list[key];


					var new_btn = '<tr>\
					<td>' + office_name + '</td>\
						<td>' + territory_name + '</td>\
						<td>' + market_name + '</td>\
						<td>' + outlet_name + '\
							<select class="hidde_select" name="data\[OutletGroupToOutlet\]\[outlet_id\]\[\]">\
								<option value="' + outlet_id + '">' + outlet_name + '</option>\
							</select>\
							<select class="hidde_select" name="data\[OutletGroupToOutlet\]\[outlet_name\]\[\]">\
								<option value="' + outlet_name + '">' + outlet_name + '</option>\
							</select>\
						</td>\
						<td>\
							<i id="' + outlet_id + '" class="glyphicon glyphicon-remove icon"></i>\
						</td>\
						</tr>';


					if (btn.search(outlet_id) == -1) {
						btn = btn + new_btn;
						// $(".item_box>table>tbody").html(btn);
						$(".item_box>table>tbody").append(new_btn);
					} else {
						alert("Please select another! " + outlet_name + " Already Selected");
					};
				}
			} else {
				var outlet_name = $("#outlet_id option:selected").html();
				var outlet_id = $("#outlet_id").val();

				var outlet_details = outlet_info[outlet_id];
				var office_name = outlet_details.office;
				var territory_name = outlet_details.territory;
				var market_name = outlet_details.market;

				var new_btn = '<tr>\
				<td>' + office_name + '</td>\
						<td>' + territory_name + '</td>\
						<td>' + market_name + '</td>\
						<td>' + outlet_name + '\
							<select class="hidde_select" name="data\[OutletGroupToOutlet\]\[outlet_id\]\[\]">\
								<option value="' + outlet_id + '">' + outlet_name + '</option>\
							</select>\
							<select class="hidde_select" name="data\[OutletGroupToOutlet\]\[outlet_name\]\[\]">\
								<option value="' + outlet_name + '">' + outlet_name + '</option>\
							</select>\
						</td>\
						<td>\
							<i id="' + outlet_id + '" class="glyphicon glyphicon-remove icon"></i>\
						</td>\
						</tr>';
				if (outlet_id != '') {
					if (btn.search(outlet_id) == -1 /*&& btn.search(outlet_name) == -1*/ ) {
						btn = btn + new_btn;
						// $(".item_box>table>tbody").html(btn);
						$(".item_box>table>tbody").append(new_btn);
					} else {
						alert("Please select another! " + outlet_name + " Already Selected");
					};
				} else {
					alert("Please select any outlet!");
				}
			}
		});
		/*--------- delete single outlet ---------*/
		$("body").on("click", ".icon", function(e) {
			e.preventDefault();
			// var avoid = $(this).parent().clone().wrap('<span>').parent().html();
			// btn = btn.replace(avoid,'');
			$(this).parent().parent().remove();
		});
		/*------- delete all outlet -------*/
		$("body").on("click", "#remove_outlet", function() {
			var btn = $(".item_box>table>tbody").html();
			var all_val = $("#outlet_id option:selected").val();

			if (all_val == 'all') {
				var outlet_list_del = [];
				$("#outlet_id option").each(function() {
					var outlet_id = $(this).attr("value");
					var outlet_name = $(this).html();
					if (outlet_id != 'all') {
						outlet_list_del[outlet_id] = outlet_name;
					}
				});
				var btn_del = '';
				for (var key in outlet_list_del) {

					var outlet_id = key;
					var outlet_name = outlet_list_del[key];
					$(".item_box>table>tbody> tr td:nth-child(5)").find("#" + outlet_id).parent().parent().remove();

				}

			}
		});
	});


	$('.office_id').selectChain({
		target: $('.territory_id'),
		value: 'name',
		url: '<?= BASE_URL . 'sales_people/get_territory_list_new' ?>',
		type: 'post',
		data: {
			'office_id': 'office_id'
		}
	});

	$('.territory_id').selectChain({
		target: $('.market_id'),
		value: 'name',
		url: '<?= BASE_URL . 'admin/doctors/get_market'; ?>',
		type: 'post',
		data: {
			'territory_id': 'territory_id'
		}
	});

	function get_thana_list(territory_id) {
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL ?>memos/get_thana_by_territory_id',
			data: 'territory_id=' + territory_id,
			cache: false,
			success: function(response) {
				$('.thana_id').html(response);
				<?php if (isset($this->request->data['Memo']['thana_id'])) { ?>
					$('.thana_id option[value="<?= $this->request->data['Memo']['thana_id'] ?>"]').attr("selected", true);
				<?php } ?>
			}
		});
	}
	if ($('.territory_id').val() != '') {
		get_thana_list($('.territory_id').val());
	}
	$('body').on('change', '.territory_id', function() {

		get_thana_list($(this).val());
	});
	$('.thana_id').selectChain({
		target: $('.market_id'),
		value: 'name',
		url: '<?= BASE_URL . 'memos/market_list'; ?>',
		type: 'post',
		data: {
			'thana_id': 'thana_id'
		}
	});
	/*$('.market_id').selectChain({
		target: $('.outlet_id'),
		value:'name',
		url: '<?= BASE_URL . 'admin/outlet_groups/get_outlet'; ?>',
		type: 'post',
		data:{'market_id': 'market_id' }
	});*/

	$("#market_id").change(function() {
		get_outlet_list();
	});

	$('.office_id').change(function() {
		$('.market_id').html('<option value="">---- Select Market ----');
		$('.outlet_id').html('<option value="">---- Select Outlet ----');
	});

	$('.territory_id').change(function() {
		$('.outlet_id').html('<option value="">---- All ----');
	});


	function get_outlet_list() {
		var territory_id = $("#territory_id").val();
		var office_id = $('#office_id').val();
		var institute_id = $('#institute_id').val();
		var market_id = $('#market_id').val();

		if (market_id) {
			$.ajax({
				url: "<?php echo BASE_URL; ?>outlet_groups/get_outlet_list",
				type: "POST",
				data: {
					territory_id: territory_id,
					office_id: office_id,
					market_id: market_id
				},
				success: function(result) {
					var response = $.parseJSON(result);
					// console.log(response);
					outlet_info = response.other_info;
					$("#outlet_id").html(response.outlet_html);

				}
			});
		} else {
			$("#outlet_id").html("<option value=''>---- All ---- </option>");
		}
		// console.log(outlet_info);
	}
</script>