<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Claim Details'); ?></h3>
				<div class="box-tools pull-right">

	                 <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Claim  List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					<?php echo $this->Form->create('Claim', array('role' => 'form')); ?>
				</div>
			</div>			
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo 'Claim No.'; ?></strong></td>
							<td><?php echo h($claim['Claim']['claim_no']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Transaction Type'; ?></strong></td>
							<td><?php echo h($claim['TransactionType']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Sender Store'; ?></strong></td>
							<td><?php echo h($claim['SenderStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Claim Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat($claim['Claim']['created_at']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Receiver Store'; ?></strong></td>
							<td><?php echo h($claim['ReceiverStore']['name']); ?></td>
						</tr>

						<tr>		
							<td><strong><?php echo 'Received Date'; ?></strong></td>
							<td>
								<?php 
								if($claim['Claim']['status'] == 1 AND $office_paren_id ==0)
								{	
									echo $this->Form->input('received_date', array('type'=>'text','label'=>false, 'class' => 'form-control datepicker_range','required'=>true)); 
								}else{								
									echo $this->App->dateformat(($claim['Claim']['received_date']));
								}
								?>
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Status'; ?></strong></td>
							<td><?php echo $claim['Claim']['status'] == 1 ? '<span class="btn btn-warning btn-xs">Pending</span>' : '<span class="btn btn-success btn-xs">Received</span>'; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Remarks'; ?></strong></td>
							<td><?php echo h($claim['Claim']['remarks']); ?></td>
						</tr>						
				</table>
			</div>

			<div class="box-body">
                <table class="table table-bordered">
					<tbody>
						<tr>		
							<th class="text-center">SL.</th>
							<th class="text-center">Product Name</th>
							<th class="text-center">Batch No.</th>
							<th class="text-center">Expire Date</th>
							<th class="text-center">Quantity</th>							
							<th class="text-center">Claim Type</th>

						</tr>
						<?php
						if(!empty($claimdetail))
						{
							$sl = 1;
							$total_quantity = 0;
							$total_received_quantity = 0;
							foreach($claimdetail as $val){
						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Product']['name']; ?></td>
							<td align="center"><?php echo $val['ClaimDetail']['batch_no']; ?></td>
							<td align="center"><?php echo $this->App->dateformat($val['ClaimDetail']['expire_date']); ?></td>
							<td align="center"><?php echo $val['ClaimDetail']['claim_qty']; ?></td>
							<!--<td align="center"><?php echo $val['ClaimDetail']['claim_type']; ?></td>-->
							<td align="center"><?php echo $val['ClaimDetail']['claim_type'] == 1 ? '<span class="btn btn-warning btn-xs">Excess</span>' : '<span class="btn btn-success btn-xs">Short</span>'; ?></td>
						</tr>
						<?php
							$total_quantity = $total_quantity + $val['ClaimDetail']['claim_qty'];
							
							$sl++;
							}							
						}
						?>	
						<tr>		
							<td align="right" colspan="4"><strong>Total Quantity :</strong></td>
							<td align="center"><?php echo $total_quantity; ?></td>							
							<td></td>							
						</tr>
					</tbody>	
				</table>
			</div>	
			<?php
			if($claim['Claim']['status'] == 1 AND $office_paren_id ==0){
			?>
			<?php echo $this->Form->submit('Received', array('class' => 'btn btn-large btn-primary')); ?>
			<?php
			}
			?>
			<?php echo $this->Form->end(); ?>
			</br>
		</div>			
	</div>
</div>
<?php
$startDate = date('d-m-Y',strtotime($claim['Claim']['created_at']));
$endDate = date('d-m-Y');
?>
<script>
$(document).ready(function (){
	$('.datepicker_range').datepicker({
		startDate: '<?php echo $startDate; ?>',
		endDate: '<?php echo $endDate; ?>',
		format: "dd-mm-yyyy",
		autoclose: true,
		todayHighlight: true
		
	});			
});
</script>