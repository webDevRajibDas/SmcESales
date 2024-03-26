<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Bank Accounts'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>Bank Accounts'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('BankAccount', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('account_number', array('class' => 'form-control','label'=>'Account Number')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('bank_branch_id', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$account)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_active', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is Active :</b>')); ?>
				</div>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>