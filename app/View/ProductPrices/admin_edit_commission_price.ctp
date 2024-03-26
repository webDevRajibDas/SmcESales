<style>
#percentige{
	margin-top:-18px;
}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-plus"></i> <?php echo __('Edit Commission'); ?></h3>
			</div>	
			<div class="box-body">				
                <?php echo $this->Form->create('DistributorCommission', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('commission_amount', array('label'=>'Distributor Commission','type'=>'text','class' => 'form-control')); ?>
					<div id="percentige">&nbsp;&nbsp;&nbsp;%</div>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('type'=>'text','class' => 'datepicker form-control','value'=>date('d-m-Y',strtotime($this->request->data['DistributorCommission']['effective_date'])))); ?>
					<?php echo $this->Form->input('outlet_category_id', array('type'=>'hidden', 'value'=>17, 'class' => 'form-control')); ?>
				</div>
				<?php
				echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); 
				
				?>
				<?php echo $this->Form->end(); ?>							
			</div>			
		</div>
	</div>
	
</div>
<script>
$(document).ready(function (){
});
</script>