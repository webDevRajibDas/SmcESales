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
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-plus"></i> <?php echo __('Add New Group'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<?php echo $this->Form->create('ProgramOfficerTag', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('program_officer_id', array('id' => 'program_officer_id', 'class' => 'form-control program_officer_id chosen', 'required', 'empty' => '---- Select Program Officer ----', 'options' => $ProgramOfficers)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('program_type_id', array('id' => 'program_type_id', 'class' => 'form-control program_type_id', 'required' => true, 'empty' => '---- Select program ----', 'data-val' => 0)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('assign_deassign', array('id' => 'assign_deassign', 'class' => 'form-control assign_deassign', 'required' => true, 'options' => $assign_deassign_array, 'data-val' => 1)); ?>
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

							</tbody>
						</table>
					</div>
				</div>
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>

</div>
<div class="modal" id="myModal" data-backdrop="static" data-keyboard="false"></div>
<div id="loading">
	<?php echo $this->Html->image('load.gif'); ?>
</div>
<script>
	$(document).ready(function() {
		$(".chosen").chosen();
		var outlet_info = new Array();
		$("#program_type_id").change(function() {
			var newValue = $(this).val();
			var oldValue = $(this).attr('data-val');
			$(this).attr('data-val', newValue);
			if (oldValue != 0 && oldValue != newValue) {
				if (confirm('If Program type change , all selected outlet will be empty')) {
					$(".item_box>table>tbody").html('');
					$('.market_id').html('<option value="">---- Select Market ----');
					$('.outlet_id').html('<option value="">---- Select Outlet ----');
					$('.territory_id').html('<option value="">---- Select Territory ----');
					$('.thana_id').html('<option value="">---- Select Thana ----');
					$('.office_id').val('');
				} else {
					$("#program_type_id").val(oldValue);
					$(this).attr('data-val', oldValue);

				}
			}
		});
		$("#assign_deassign").change(function() {
			var newValue = $(this).val();
			var oldValue = $(this).attr('data-val');
			$(this).attr('data-val', newValue);
			if (oldValue != newValue) {
				if (confirm('If Program type change , all selected outlet will be empty')) {
					$(".item_box>table>tbody").html('');
					$('.market_id').html('<option value="">---- Select Market ----');
					$('.outlet_id').html('<option value="">---- Select Outlet ----');
					$('.territory_id').html('<option value="">---- Select Territory ----');
					$('.thana_id').html('<option value="">---- Select Thana ----');
					$('.office_id').val('');
				} else {
					$("#assign_deassign").val(oldValue);
					$(this).attr('data-val', oldValue);

				}
			}
		});
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
					var program_id = outlet_details.program_id;
					var outlet_id = key;
					var outlet_name = outlet_list[key];


					var new_btn = '<tr>\
						<td>' + office_name + '</td>\
							<td>' + territory_name + '</td>\
							<td>' + market_name + '</td>\
							<td>' + outlet_name + '\
								<input type="hidden" name="data\[ProgramOfficerTag\]\[program_id\]\[\]" value="' + program_id + '">\
								<select class="hidde_select" name="data\[ProgramOfficerTag\]\[outlet_id\]\[\]">\
									<option value="' + outlet_id + '">' + outlet_name + '</option>\
								</select>\
								<select class="hidde_select" name="data\[ProgramOfficerTag\]\[outlet_name\]\[\]">\
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
				var program_id = outlet_details.program_id;
				var new_btn = '<tr>\
				<td>' + office_name + '</td>\
						<td>' + territory_name + '</td>\
						<td>' + market_name + '</td>\
						<td>' + outlet_name + '\
							<input type="hidden" name="data\[ProgramOfficerTag\]\[program_id\]\[\]" value="' + program_id + '">\
							<select class="hidde_select" name="data\[ProgramOfficerTag\]\[outlet_id\]\[\]">\
								<option value="' + outlet_id + '">' + outlet_name + '</option>\
							</select>\
							<select class="hidde_select" name="data\[ProgramOfficerTag\]\[outlet_name\]\[\]">\
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

		$('.office_id').change(function() {
			$('.market_id').html('<option value="">---- Select Market ----');
			$('.outlet_id').html('<option value="">---- Select Outlet ----');
		});

		$('.territory_id').change(function() {
			$('.outlet_id').html('<option value="">---- All ----');
		});

		$("body").on("change", "#market_id, #territory_id, #thana_id", function() {
			get_outlet_list();
		});

		function get_outlet_list() {
			var territory_id = $("#territory_id").val();
			var office_id = $('#office_id').val();
			var thana_id = $('#thana_id').val();
			var market_id = $('#market_id').val();
			var assign_deassign = $('#assign_deassign').val();
			var program_officer_id = $('#program_officer_id').val();
			var program_type_id = $('#program_type_id').val();
			if (program_type_id == '') {
				alert("please select program");
			}
			/* else if (program_officer_id == '') {
				alert("please select program officer");
			} */
			else if (territory_id || market_id || thana_id || market_id) {
				$.ajax({
					url: "<?php echo BASE_URL; ?>program_officer_outlet_tags/get_outlet_list",
					type: "POST",
					data: {
						territory_id: territory_id,
						office_id: office_id,
						market_id: market_id,
						thana_id: thana_id,
						program_type_id: program_type_id,
						assign_deassign: assign_deassign,
						program_officer_id: program_officer_id,
					},
					beforeSend: function() {
						$('#myModal').modal('show');
						$('#loading').show();
					},
					success: function(result) {
						var response = $.parseJSON(result);
						 console.log(response);
						outlet_info = response.other_info;
						$("#outlet_id").html(response.outlet_html);
						$('#myModal').modal('hide');
						$('#loading').hide();
					}
				});
			} else {
				$("#outlet_id").html("<option value=''>---- All ---- </option>");
			}
			console.log(outlet_info);
		}

	});
</script>