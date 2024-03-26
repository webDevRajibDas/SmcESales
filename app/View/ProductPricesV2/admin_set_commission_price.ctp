<style>
#percentige{
	margin-top:-18px;
}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-plus"></i> <?php echo __('Add Distributor Commission'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('product_prices','admin_price_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Price List'), array('action' => "admin_price_list/$product_id"), array('class' => 'btn btn-primary', 'escape' => false)); } ?>		
				</div>
			</div>	
			<div class="box-body">				
                <?php echo $this->Form->create('DistributorCommission', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('commission_amount', array('label'=>'Distributor Commission','type'=>'text','id'=>'commission_amount','class' => 'form-control')); ?>
					<div id="percentige">&nbsp;&nbsp;&nbsp;%</div>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('type'=>'text','class' => 'datepicker form-control')); ?>
					<?php echo $this->Form->input('outlet_category_id', array('type'=>'hidden', 'value'=>17, 'class' => 'form-control')); ?>
				</div>
				<?php echo $this->Form->submit('Add', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>							
			</div>			
		</div>
	</div>
	
</div>
<script>
$(document).ready(function (){
});
</script>