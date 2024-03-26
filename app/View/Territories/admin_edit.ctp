<?php

// pr(isset($this->request->data['Territory']['product_group_id']) ? $this->request->data['Territory'] 	: '');
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Edit Territory'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Territory List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<?php echo $this->Form->create('Territory', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('short_name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('class' => 'form-control', 'empty' => '---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_child', array('class' => 'form-control is_child', 'type' => 'checkbox', 'label' => '<strong>Is Child(product grouping) :</strong>')); ?>
				</div>
				<div class="form-group parent_territory">
					<?php echo $this->Form->input('parent_id', array('class' => 'form-control', 'empty' => '---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('product_group_id.', array('label' => 'Product Group : ', 'class' => 'form-control chosen', 'multiple', 'required' => true, 'options' => $productGroups, 'selected' => isset($this->request->data['Territory']['product_group_id']) ? $this->request->data['Territory']['product_group_id'] : ''));
					if ($this->Form->isFieldError('product_group_id')) {
						echo $this->Form->error('product_group_id');
					} ?>
				</div>
				<div class="form-group dist_thana">
					<label></label>
					<table class="thana_table">

						<tr>
							<td style="padding:5px 0;"><?php echo $this->Form->input('district_id', array('label' => false, 'style' => 'width:155px;margin-right:10px;', 'id' => 'district_id', 'class' => 'district_id form-control', 'empty' => '---- Select District ----')); ?></td>
							<td><?php echo $this->Form->input('thana_id', array('label' => false, 'style' => 'width:155px;margin-right:10px;', 'class' => 'thana_id form-control', 'empty' => '---- Select Thana ----')); ?></td>
							<td><span class="btn btn-primary btn-xs add_more">Add</span></td>
						</tr>
						<?php echo $this->Form->end(); ?>
						<?php
						if (!empty($thanas)) {
							foreach ($thanas as $val) {
						?>
								<tr>
									<td><?= $districts[$val['Thana']['district_id']] ?></td>
									<td style="padding:5px 0;" colspan="2"><input class="selected_thana_id" type="hidden" value="<?php echo $val['Thana']['id']; ?>" /><?php echo $val['Thana']['name']; ?></td>
									<td><?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_thana', $val['ThanaTerritory']['id'], $this->request->data['Territory']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $val['ThanaTerritory']['id']));  ?></td>
								</tr>
						<?php
							}
						}
						?>

					</table>
				</div>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>

			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		jQuery(".chosen").chosen();
		$('.is_child').on('ifChanged', function(event) {
			if (event.target.checked) {
				$('.parent_territory').show();
				$('.dist_thana').hide();
			} else {
				$('.parent_territory').hide();
				$('.dist_thana').show();
			}
		});
		$('.is_child').trigger('ifChanged');
		/* $('.is_child').on('ifUnchecked', function(event) {
			$('.parent_territory').hide();
			$('.dist_thana').show();
		}); */
		$('.district_id').selectChain({
			target: $('.thana_id'),
			value: 'name',
			url: '<?= BASE_URL . 'admin/territories/get_thana'; ?>',
			type: 'post',
			data: {
				'district_id': 'district_id'
			}
		});

		var rowCount = 1;
		$(".add_more").click(function() {

			var district = $(".district_id option:selected").text();
			var thana = $(".thana_id option:selected").text();
			var thana_id = $('.thana_id').val();
			var selected_stock_array = $(".selected_thana_id").map(function() {
				return $(this).val();
			}).get();
			var stock_check = $.inArray(thana_id, selected_stock_array) != -1;

			if (thana_id == '') {
				alert('Please select thana.');
				return false;
			} else if (stock_check == true) {
				alert('This thana already added.');
				$('.district_id').val('');
				$('.thana_id').val('');
				return false;
			} else {
				rowCount++;
				var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td style="padding:5px 0;">' + district + '</td><td><input type="hidden" name="thana_id[]" class="selected_thana_id" value="' + thana_id + '"/>' + thana + '</td><td><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
				$('.thana_table').append(recRow);
				$('.district_id').val('');
				$('.thana_id').val('');
			}
		});


		$(document).on("click", ".remove", function() {
			var removeNum = $(this).val();
			$('#rowCount' + removeNum).remove();
		});

	});
</script>