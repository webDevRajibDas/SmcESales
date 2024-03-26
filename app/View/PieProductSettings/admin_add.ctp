<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Pie Product Setting'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Setting List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('PieProductSetting', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('brand_id', array('id'=>'brand_id', 'required'=>true, 'class' => 'form-control chosen','empty'=>'---- Select ----')); ?>
				</div>
            	<div class="form-group">
					<?php echo $this->Form->input('product_id', array('id'=>'product_id', 'required'=>false, 'class' => 'form-control chosen','empty'=>'---- Select ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('sort', array('class' => 'form-control', 'type' => 'number')); ?>
				</div>
				
				
			<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
$(document).ready(function(){

	$(".chosen").chosen();

	$('#brand_id').selectChain({
		target: $('#product_id'),
		value:'name',
		url: '<?= BASE_URL.'product_settings/get_brand_wise_product_list'?>',
		type: 'post',
		data:{'brand_id': 'brand_id'  },
		afterSuccess:function(){
			$("#product_id").val('').trigger("chosen:updated");
		}
	});

});
</script>