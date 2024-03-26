<style>
	.checkbox-inline input[type="checkbox"] {
		margin-left: 5px;
		margin-right: 10px;
		margin-top: 9px;
	}
</style>



<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Bonus Campaign'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Bonus Campaign List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<?php echo $this->Form->create('BonusCampaign', array('role' => 'form', 'enctype'=>'multipart/form-data')); ?>
					<div class="form-group">
						<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('date_from', array('class' => 'form-control date_picker', 'label'=>'Start Date', 'required' => true)); ?>
					</div>

					<div class="form-group">
						<?php echo $this->Form->input('date_to', array('class' => 'form-control date_picker', 'label'=>'End Date', 'required' => true)); ?>
					</div>

					<div class="form-group">
						
						<div class="form-group required">
							<label>Product :</label>
							<?php echo $this->Form->input('product_id', array('class' => 'form-control','required' => true, 'empty'=>'---- Select Product ----', 'label'=>false, 'options' => $products, 'default'=>$exitingproduct_id[0],'name'=>'data[BonusCampaign][product_id][]')); ?>
							<button type="button" class="add_product_button hide_all">Add More</button>
						</div>
						<span class="input_products_wrap">
							<?php foreach($exitingproduct_id as $key => $productid){ 
								if($key != 0){
							?>
							<div class="form-group required">
								<label>Product :</label>
								<?php echo $this->Form->input('product_id', array('class' => 'form-control','required' => true, 'empty'=>'---- Select Product ----', 'label'=>false, 'options' => $products, 'default'=>$productid,'name'=>'data[BonusCampaign][product_id][]')); ?>
								<a href="#" class="remove_product_field btn btn-primary hide_all btn-xs">Remove</a>
							</div>
							<?php  }} ?>
						</span>
					</div>

					<div class="form-group">
						<?php echo $this->Form->input('bonus_details', array('class' => 'form-control', 'required' => true, 'type'=>'textarea')); ?>
					</div>

					<div class="form-group">
						<div class="create_for">
							<?php echo $this->Form->input('create_for', array('label'=>'Create For :', 'class' => 'checkbox-inline', 'multiple' => 'checkbox', 'options' => array('1'=>'SO','2'=>'SR'),'div'=>false,'hiddenField'=>false,'selected'=>$selected_create_for)); ?>
						</div>
					</div>

					<div class="form-group">
					<label><?php echo __('Attachment'); ?><b style="color:red;"> Format ('jpg', 'jpeg','png', 'pdf')</b></label>
						<?php echo $this->Form->input('attachment', array('type' => 'file', 'label' => false,));
						?>
					</div>

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
		</div>			
	</div>
</div>


<script>
    $(document).ready(function() {

		$("input[type='checkbox']").iCheck('destroy');
    	$("input[type='radio']").iCheck('destroy');


        var yesterday = new Date(new Date().setDate(new Date().getDate() - 1));
        $('.date_picker').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true,
        });


		$('.add_product_button').click(function(e){ 
			e.preventDefault();
								
			$('.input_products_wrap').append('<div class="form-group required"><label>Product :</label><select class="form-control" name="data[BonusCampaign][product_id][]" required><option value="">---- Select Product -----</option>'+'<?=$product_list; ?>'+'</select><a href="#" class="remove_product_field btn btn-primary hide_all btn-xs">Remove</a></div>');
			
		});

		$('.input_products_wrap').on("click",".remove_product_field", function(e){ 
			e.preventDefault(); 
			$(this).parent('div').remove();
		});

    });
</script>