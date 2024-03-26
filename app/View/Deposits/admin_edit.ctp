<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Admin Edit Deposit'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Deposit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Deposit', array('role' => 'form')); ?>
					<div class="form-group">
						<?php echo $this->Form->hidden('id', array('class' => 'form-control')); ?>
						<?php echo $this->Form->hidden('sales_person_id', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('type', array(
						'class' => 'form-control',
						'options'=>$types,
						'empty'=>'--- Select ---',
						'default'=>$this->request->data['Deposit']['type'],
						'disabled',
						)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('instrument_type', array(
						'class' => 'form-control',
						'options'=>$instrument_types,
						'empty'=>'--- Select ---',
						'default'=>$this->request->data['Deposit']['instrument_type'],
						'disabled'=>$this->request->data['Deposit']['type']==2?true:false,
						)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('slip_no', array(
						'class' => 'form-control',
						'value'=>$this->request->data['Deposit']['slip_no'],
						
						)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('instrument_ref_no', array(
						'class' => 'form-control',
						'value'=>$this->request->data['Deposit']['instrument_ref_no']
						)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('deposit_date', array(
						'class' => 'form-control deposit_date',
						'type'=>'text',
						'required'=>true, 
						'readonly' => true,
						/*'value'=>date('d-m-Y',strtotime($this->request->data['Deposit']['deposit_date'])),*/
						)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('deposit_amount', array(
						'class' => 'form-control deposit_amount',
						'readonly'=>$this->request->data['Deposit']['type']==2?true:false,
						)); ?>
						<?php if($this->request->data['Deposit']['type']==2) {?>
						<button class="btn btn-xs btn-success update_deposit_amount_btn" data-payment-id="<?=$this->request->data['Deposit']['payment_id']?>">Update with collection</button>
						<?php } ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('bank_id', array(
						'class' => 'form-control bank_id',
						'options'=>$banks,
						'empty'=>'--- Select ---',
						'default'=>$this->request->data['BankBranch']['bank_id']
						)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('bank_branch_id', array(
						'class' => 'form-control bank_branch_id',
						'options'=>$bankBranches,
						'empty'=>'--- Select ---',
						'default'=>$this->request->data['BankBranch']['id']
						)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('week_id', array(
						'class' => 'form-control week_id',
						'options'=>$weeks,
						'empty'=>'--- Select ---',
						'default'=>$this->request->data['Deposit']['week_id']
						)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('remarks', array('class' => 'form-control')); ?>
					</div>

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
$(document).ready(function(){
	$(".bank_id").change(function(){
		if($(this).val())
		{
			$.ajax({
				'url':'<?php echo BASE_URL; ?>deposits/get_bank_branch',
				'data':{
					'bank_id':$(this).val(),
					'territory_id':<?php echo  $this->request->data['Deposit']['territory_id'];?>,
					},
				'type':'POST',
				'success':function(response)
				{
					$(".bank_branch_id").html(response);
				}
			});
		}
	});
	$(".deposit_date").datepicker({
		format: 'yyyy-mm-dd'
	});
	$(".deposit_date").on('changeDate', function(ev){
		if($(this).val())
		{
			$.ajax({
				'url':'<?php echo BASE_URL; ?>deposits/get_week',
				'data':{
					'deposit_date':$(this).val(),
					},
				'type':'POST',
				'success':function(response)
				{
					$(".week_id").html(response);
				}
			});
		}
	});
	$('.update_deposit_amount_btn').click(function(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		var _this=$(this);
		$.ajax({
				'url':'<?php echo BASE_URL; ?>deposits/get_collection_amount_by_payment_id',
				'data':{
					'payment_id':$(this).data("payment-id")
					},
				'type':'POST',
				'success':function(response)
				{
					_this.parent().find('.deposit_amount').val(response);
				}
			});

	});
});	
</script>