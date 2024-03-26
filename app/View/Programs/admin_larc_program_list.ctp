<?php
App::import('Controller', 'ProgramsController');
$ProgramsController = new ProgramsController;
?>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Pink Star Program List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if ($this->App->menu_permission('programs', 'admin_add_larc')) {
						echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add/Edit Pink Star Program'), array('action' => 'add_larc'), array('class' => 'btn btn-primary', 'escape' => false));
					} ?>
				</div>
			</div>
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('Program', array('role' => 'form', 'action' => 'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%">
								<?php echo $this->Form->input('program_type_id', array('type' => 'hidden', 'value' => 3)); ?>
								<?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select ----', 'required' => false)); ?>
							</td>
							<td width="50%"><?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control', 'empty' => '---- Select ----', 'required' => false)); ?></td>
						</tr>
						<tr>
							<td>
								<?php echo $this->Form->input('status', array('id' => 'status', 'class' => 'form-control', 'empty' => '---- Select ----', 'options' => $status, 'required' => false)); ?>
							</td>
							<td><?php echo $this->Form->input('thana_id', array('id' => 'thana_id', 'class' => 'form-control', 'empty' => '---- Select ----', 'required' => false)); ?></td>
						</tr>
						<tr>
							<td></td>
							<td><?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control', 'empty' => '---- Select ----', 'required' => false)); ?></td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<?php if ($programs) { ?>
									<a class="btn btn-success" id="download_xl">Download XL</a>
								<?php } ?>
							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
				<table id="BonusCards" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="20" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center">Office</th>
							<th class="text-center"><?php echo $this->Paginator->sort('Territory.territory_id', 'Territory'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Market.thana_id', 'Thana'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Market.market_id', 'Market'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Outlet.name', 'Outlet/Clinic Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Doctor.name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('code'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('assigned_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('program_officer', 'Program Officer'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
							<th width="60" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($programs as $program) : ?>
							<tr office-id="<?php echo($program['Office']['id']); ?>" data-id="<?php echo $program['Program']['id']?>">
								<td class="text-center"><?php echo h($program['Program']['id']); ?></td>

								<td class="text-left"><?php echo h($program['Office']['office_name']); ?></td>
								<td class="text-left"><?php echo h($program['Territory']['name']); ?></td>
								<td class="text-left">
									<?= $ProgramsController->get_thana_info($program['Market']['thana_id'])['Thana']['name'] ?>
								</td>
								<td class="text-left"><?php echo h($program['Market']['name']); ?></td>
								<td class="text-left"><?php echo h($program['Outlet']['name']); ?></td>

								<td class="text-left"><?php echo h($program['Doctor']['name']); ?></td>

								<td class="text-center"><?php echo h($program['Program']['code']); ?></td>
								<td class="text-center"><?php echo $this->App->dateformat($program['Program']['assigned_date']); ?></td>
								<td class="text-center pr_off_field"><?php echo h($program['Program']['program_officer']); ?></td>
								<td class="text-center">
									<?php
									if ($program['Program']['status'] == 1) {
										echo '<span class="btn btn-success btn-xs assigned">Assigned</span>';
									} else if ($program['Program']['status'] == 2) {
										echo '<span class="btn btn-danger btn-xs">De-Assigned</span>';
									} else {
										echo '<span class="btn btn-warning btn-xs">Not Assigned</span>';
									}
									?>
								</td>
								<td class="text-center">
									<?php if ($this->App->menu_permission('programs', 'admin_edit_larc')) {
										echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit_larc', $program['Program']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Deassigned'));
									} ?>

									<button type="button" value="<?php echo  $program['Program']['id']?>" class="btn btn-info btn-xs programOfficerAdd"><i class="glyphicon glyphicon-edit"></i></button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
							<?php echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>
								<?php
								echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
								echo $this->Paginator->numbers(array("separator" => "", "currentTag" => "a", "currentClass" => "active", "tag" => "li", "first" => 1));
								echo $this->Paginator->next(__("next"), array("tag" => "li", "currentClass" => "disabled"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="programOfficerModalLarc">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Assign Program Officer </h4>
			</div>
			<div class="modal-body">
			<?php echo $this->Form->create('assign_program_officer', array('role' => 'form')); ?>
			
			<div class="form-group">
				<label>Office</label>
				<input type="text" class="form-control office" name="office" disabled value=""/>
			</div>
			<div class="form-group">
				<label>Territory</label>
				<input type="text" class="form-control territory" name="territory" disabled value=""/>
			</div>
			<div class="form-group">
				<label>Thana</label>
				<input type="text" class="form-control thana" name="thana_name" disabled value=""/>
			</div>

			<div class="form-group">
				<label>Market</label>
				<input type="text" class="form-control market" name="market" disabled value=""/>
			</div>
			<div class="form-group">
				<label>Outlet</label>
				<input type="text" class="form-control outlate" name="outlate" disabled value=""/>
			</div>

			<div class="form-group">
				<label>Program Officer</label>
					<select class="form-control" id="progOfficerData">
				</select>
			</div>

			<input type="hidden" class="form-control program_id"/>
			<input type="hidden" class="form-control row_id" name="row_id" value=""/>
			
			<?php echo $this->Form->end(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary program_officer_update">Update</button>
			</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
</div>

<?php /*?><script>
$(document).ready(function(){
	$('#office_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});
	
	$('#territory_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'markets/get_market_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id','location_type_id':''}
	});	
	
	$('#office_id').change(function (){
		$('#market_id').html('<option value="">---- Select -----</option>');
	});	
	
});
</script><?php */ ?>

<script>

// window.addEventListener('load', function () {
// 	var pr_off_field = document.getElementById("pr_off_field");
// 	console.log(pr_off_field);
// })

	$(document).ready(function() {
		// var programOfficerName = $('td.pr_off_field').text();
		// var programOfficerAdd = $('.programOfficerAdd');
		// console.log(programOfficerName);	
		
		// if (programOfficerName.length > 0){
		// 	programOfficerAdd.prop("disabled",true);
		// }else{
		// 	programOfficerAdd.prop("disabled",false);
		// }

		$(".programOfficerAdd").click(function () {
			var ProgramId = $(this).val();
			var currentRow = $(this).closest("tr");
			var row_id = $(this).closest('tr').data('id');
			var office_id = $(this).closest('tr').attr('office-id');
			var office=currentRow.find("td:eq(1)").text();
			var territory =currentRow.find("td:eq(2)").text();
			var getThana =currentRow.find("td:eq(3)").text();
			var trimThana = $.trim(getThana);
			console.log(getThana);
			var market =currentRow.find("td:eq(4)").html();
			var outlate =currentRow.find("td:eq(5)").html();
           
			//send to modal
			$('input[name="office"]').val(office);
			$('input[name="territory"]').val(territory);
			$('input[name="thana_name"]').val(trimThana);
			$('input[name="market"]').val(market);
			$('input[name="outlate"]').val(outlate);
			$('input[name="row_id"]').val(row_id);

			$.ajax({
				type: "POST",
				url: '<?= BASE_URL . 'programs/program_officer_list'?>',
				data: {'office_id':office_id},
				cache: false,
				dataType: "json",
				success: function (response) {
					//console.log(response);
					$.each(response,function(key, value){
                    $("#progOfficerData").append('<option value=' + key + '>' + value + '</option>');
                });
					
				}
			});
            $('#programOfficerModalLarc').modal('toggle');
        });

		$(".program_officer_update").click(function () {
			var programOfficerId = $('#progOfficerData').val();
			var programID = $('.row_id').val();
	           $.ajax({
					type: "POST",
					url: '<?= BASE_URL . 'admin/programs/update_officer_id' ?>',
					dataType : 'json',
					data: {program_officer_id: programOfficerId,program_id:programID},
              	success: function(res){
					$('#programOfficerModal').modal('hide');
					setTimeout(function () {
								document.location.reload(true);
							}, 500);
					},
                });
        });
		$('#office_id').selectChain({
			target: $('#territory_id'),
			value: 'name',
			url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
			type: 'post',
			data: {
				'office_id': 'office_id'
			}
		});

		/*$('#territory_id').selectChain({
			target: $('#market_id'),
			value:'name',
			url: '<?= BASE_URL . 'markets/get_market_list' ?>',
			type: 'post',
			data:{'territory_id': 'territory_id','location_type_id':''}
		});	*/





		$('#territory_id').selectChain({
			target: $('#thana_id'),
			value: 'name',
			url: '<?= BASE_URL . 'programs/get_thana_list' ?>',
			type: 'post',
			data: {
				'territory_id': 'territory_id',
				'location_type_id': ''
			}
		});

		$('#thana_id').selectChain({
			target: $('#market_id'),
			value: 'name',
			url: '<?= BASE_URL . 'programs/get_market_list' ?>',
			type: 'post',
			data: {
				'thana_id': 'thana_id',
				'location_type_id': ''
			}
		});

		$('#office_id').change(function() {
			$('#market_id').html('<option value="">---- Select -----</option>');
		});
		$('#office_id').change(function() {
			$('#thana_id').html('<option value="">---- Select -----</option>');
		});

		$('#territory_id').change(function() {
			$('#market_id').html('<option value="">---- Select -----</option>');
		});
		$('#territory_id').change(function() {
			$('#thana_id').html('<option value="">---- Select -----</option>');
		});

		$("#download_xl").click(function(e) {
			e.preventDefault();
			var territory_id = $('#territory_id').val();
			var status = $('#status').val();
			var office_id = $('#office_id').val();
			var thana_id = $('#thana_id').val();
			var market_id = $('#market_id').val();
			var program_type_id = 3;
			var html = '';
			$.ajax({
				url: '<?= BASE_URL . 'programs/bsp_download_xl'; ?>',
				type: 'POST',
				data: {
					'program_type_id': program_type_id,
					'territory_id': territory_id,
					'status': status,
					'office_id': office_id,
					'thana_id': thana_id,
					'market_id': market_id,
				},
				cache: false,
				success: function(response) {
					// console.log(response);
					html += response;
					var blob = new Blob([html], {
						type: 'data:application/vnd.ms-excel'
					});
					var downloadUrl = URL.createObjectURL(blob);
					var a = document.createElement("a");
					a.href = downloadUrl;
					a.download = "downloadFile.xls";
					document.body.appendChild(a);
					a.click();
				}

			});

		});



	});
</script>