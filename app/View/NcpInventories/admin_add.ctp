<?php //echo 'try';?><div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Admin Add Current Inventory'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Current Inventory List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('CurrentInventory', array('role' => 'form')); ?>
					<div class="form-group">
						<?php echo $this->Form->input('store_id', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('inventory_status_id', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('product_type', array('id'=>'product_type', 'class' => 'form-control ','empty'=>'---- Select Product Type ----')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('product_id', array('class' => 'form-control','id'=>'product_id','empty'=>'--- Select ---')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('batch_number', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('m_unit', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('qty', array('class' => 'form-control')); ?>
					</div>
                    
                    <?php echo $this->Form->input('transaction_date', array('class' => 'form-control', 'type' => 'hidden', 'value' => date('Y-m-d'))); ?>

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#product_type').chosen();
        $('#product_type').selectChain({
        target: $('#product_id'),
        value:'name',
        url: '<?= BASE_URL.'Challans/get_product'?>',
        type: 'post',
        data:{'product_type_id': 'product_type'  }
        });
	});
</script>