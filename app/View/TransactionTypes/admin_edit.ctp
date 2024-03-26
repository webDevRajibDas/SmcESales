<?php 
	// pr($this->request->data);
 ?>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Transaction Type'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Transaction Type List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('TransactionType', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('transaction_code', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
				</div>
				<!-- <div class="form-group">
					<?php //echo $this->Form->input('side', array('class' => 'form-control')); ?>
				</div> -->
				<div class="form-group">
					<?php //echo $this->Form->input('inout', array('class' => 'form-control')); ?>
					<?php echo $this->Form->input('inout', array('class' => 'form-control','label'=>'In/Out','empty'=>'--- Select --- ','options'=>array('2'=>'In','1'=>'Out'))); ?>
				</div>	
				<div class="form-group">
					<?php echo $this->Form->input('adjust', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Adjust :</b>')); ?>
				</div>		
				<div class="form-group">
					<?php echo $this->Form->input('active_status', array('class' => 'form-control', 'type'=>'checkbox','label'=>'<b>Is Active :</b>')); ?>
				</div>		
				<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>