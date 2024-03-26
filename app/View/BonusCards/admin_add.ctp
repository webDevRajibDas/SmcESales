<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Add Bonus Card'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Bonus Card List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">
				<?php echo $this->Form->create('BonusCard', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control', 'empty' => '---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('bonus_card_type_id', array('class' => 'form-control', 'empty' => '---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('product_id', array('label' => 'Main product', 'class' => 'form-control chosen', 'empty' => '---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('min_qty_per_memo', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('min_qty_per_year', array('class' => 'form-control')); ?>
				</div>

				<div class="bonus-card">
					<div class="form-group">
						<?php echo $this->Form->input('card_product_id.', array('label' => 'card product', 'class' => 'form-control chosen original-card-product card_product', 'empty' => '---- Select ----', 'options' => $products, 'required')); ?>
						<button class="btn btn-primary btn-p-add"><i class="glyphicon glyphicon-plus"></i></button>
					</div>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Is Active :</b>')); ?>
				</div>

				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$(".chosen").chosen();
		$('.btn-p-add').on('click', function(e) {
			e.preventDefault();
			var clone_product = `<?php echo $this->Form->input('card_product_id.', array('label' => 'card product', 'class' => 'form-control chosen card_product', 'empty' => '---- Select ----', 'options' => $products, 'required')); ?>`;

			$('.bonus-card').append('<div class="form-group">' + clone_product + '<button class="btn btn-primary btn-p-remove"><i class="glyphicon glyphicon-remove"></i></button></div>');
			$(".chosen").chosen();
		});
		$('body').on('click', '.btn-p-remove', function(e) {
			e.preventDefault();
			//console.log($(this).parent());
			$(this).parent().remove();
		});
		$('body').on('change', '.card_product', function(e) {
			var curret_value = $(this).val();
			$(this).removeAttr('data-selected-id-' + $(this).attr('data-selected-id'));
			if ($('.card_product[data-selected-id-' + curret_value + ']').length == 0) {

				$(this).attr('data-selected-id-' + curret_value, curret_value);
				$(this).attr('data-selected-id', curret_value);
			} else {
				$(this).val('').trigger("chosen:updated");
				alert('already select this product');
			}

		})
	})
</script>