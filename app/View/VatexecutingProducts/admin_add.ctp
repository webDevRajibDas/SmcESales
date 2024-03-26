<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Vat Executing Product'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Vat Executing Product'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('VatexecutingProduct', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('product_type', array('id'=>'product_type', 'required'=>true, 'class' => 'form-control product_type','empty'=>'---- Select Product Type ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('product_id', array('class' => 'form-control',  'id'=>'product_id','empty'=>'---- Select Product ----')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('effectivedate', array( 'label'=>'Effective Date', 'class' => 'form-control datepicker', 'required'=>true,)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('price', array('class' => 'form-control', 'required'=>true,)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('vat', array('class' => 'form-control', 'required'=>true,)); ?>
				</div>

			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>

<script>
	 $(document).ready(function () {

        $('#product_type').selectChain({
        target: $('#product_id'),
        value:'name',
        url: '<?= BASE_URL.'vatexecuting_products/get_product'?>',
        type: 'post',
        data:{'product_type_id': 'product_type'  }

        });
	});
</script>