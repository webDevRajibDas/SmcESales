<?php
	// pr($current_deposit);exit;
	App::import('Controller', 'DepositLogsController');
	$DepositLogsController = new DepositLogsController;	
?>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Deposit'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Deposit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <?php foreach($deposit_log as $data){ ?>
                <table class="table table-bordered">
                	<tbody>
                		<tr>
                			<td><b>Log Status</b></td>
                			<td>
                				<?php if($data['DepositLog']['is_deleted']==0) {echo '<span class="btn btn-warning btn-xs">Edited</span>';}else{echo '<span class="btn btn-danger btn-xs">Deleted</span>';} ?>
                			</td>
                			<td>
                				<b><?php if($data['DepositLog']['is_deleted']==0) {echo '<span>Edited At</span>';}else{echo '<span>Deleted At</span>';} ?></b>
                			</td>
                			<td>
                				<?php echo $this->App->datetimeformat($data['DepositLog']['deleted_at']); ?>
                			</td>
                		</tr>
                		<tr>
                			<td><b>Sales Person</b></td>
                			<td>
                				<?= $data['SalesPerson']['name']?>
                			</td>
                			<td>
                				<b>Territory</b>
                			</td>
                			<td>
                				<?= $data['Territory']['name']?>
                			</td>
                		</tr>
                		<?php if($data['DepositLog']['type']==2){ ?>
                		<tr>
                			<td><b>Memo no</b></td>
                			<td>
                				<?= $data['DepositLog']['memo_no']?>
                			</td>
                			<td>
                				<b>Outlet</b>
                			</td>
                			<td>
                				<?=($data['DepositLog']['type']==2)?$DepositLogsController->getOutletName($data['Memo']['outlet_id']):'N/A';?>
                			</td>
                		</tr>
                		<?php } ?>
                		<tr>
                			<td><b>Deposit Type</b></td>
                			<td>
                				<?= $data['DepositType']['name']?>
                			</td>
                			<td>
                				<b>Instrument Type</b>
                			</td>
                			<td>
                				<?= $data['InstrumentType']['name']?>
                			</td>
                		</tr>
                		<tr>
                			<td><b>Bank</b></td>
                			<td>
                				  <?=$DepositLogsController->getBankName($data['BankBranch']['bank_id'])?>
                			</td>
                			<td>
                				<b>Bank Branch</b>
                			</td>
                			<td>
                				<?= $data['BankBranch']['name']?>
                			</td>
                		</tr>
                		<tr>
                			<td><b>Slip No</b></td>
                			<td>
                				  <?= $data['DepositLog']['slip_no']?>
                			</td>
                			<td>
                				<b>Deposit Amount</b>
                			</td>
                			<td>
                				<?= sprintf("%0.2f",$data['DepositLog']['deposit_amount'])?>
                			</td>
                		</tr>
                		<tr>
                			<td><b>Deposit Date</b></td>
                			<td>
                				  <?= $this->App->dateformat($data['DepositLog']['deposit_date'])?>
                			</td>
                			<td>
                				<b>Week</b>
                			</td>
                			<td>
                				<?= $data['Week']['week_name']?>
                			</td>
                		</tr>
                	</tbody>
                </table>
                <br>
                <?php } ?>

                <?php if($current_deposit) {?>
	                 <?php foreach($current_deposit as $data){ ?>
	                <table class="table table-bordered">
	                	<tbody>
	                		<tr>
	                			<td><b>Log Status</b></td>
	                			<td>
	                				<span class="btn btn-info btn-xs">Current Deposit</span>
	                			</td>
	                			<td>
	                				
	                			</td>
	                			<td>
	                				
	                			</td>
	                		</tr>
	                		<tr>
	                			<td><b>Sales Person</b></td>
	                			<td>
	                				<?= $data['SalesPerson']['name']?>
	                			</td>
	                			<td>
	                				<b>Territory</b>
	                			</td>
	                			<td>
	                				<?= $data['Territory']['name']?>
	                			</td>
	                		</tr>
	                		<?php if($data['Deposit']['type']==2){ ?>
	                		<tr>
	                			<td><b>Memo no</b></td>
	                			<td>
	                				<?= $data['Deposit']['memo_no']?>
	                			</td>
	                			<td>
	                				<b>Outlet</b>
	                			</td>
	                			<td>
	                				<?=($data['Deposit']['type']==2)?$DepositLogsController->getOutletName($data['Memo']['outlet_id']):'N/A';?>
	                			</td>
	                		</tr>
	                		<?php } ?>
	                		<tr>
	                			<td><b>Deposit Type</b></td>
	                			<td>
	                				<?= $data['DepositType']['name']?>
	                			</td>
	                			<td>
	                				<b>Instrument Type</b>
	                			</td>
	                			<td>
	                				<?= $data['InstrumentType']['name']?>
	                			</td>
	                		</tr>
	                		<tr>
	                			<td><b>Bank</b></td>
	                			<td>
	                				  <?=$DepositLogsController->getBankName($data['BankBranch']['bank_id'])?>
	                			</td>
	                			<td>
	                				<b>Bank Branch</b>
	                			</td>
	                			<td>
	                				<?= $data['BankBranch']['name']?>
	                			</td>
	                		</tr>
	                		<tr>
	                			<td><b>Slip No</b></td>
	                			<td>
	                				  <?= $data['Deposit']['slip_no']?>
	                			</td>
	                			<td>
	                				<b>Deposit Amount</b>
	                			</td>
	                			<td>
	                				<?= sprintf("%0.2f",$data['Deposit']['deposit_amount'])?>
	                			</td>
	                		</tr>
	                		<tr>
	                			<td><b>Deposit Date</b></td>
	                			<td>
	                				  <?= $this->App->dateformat($data['Deposit']['deposit_date'])?>
	                			</td>
	                			<td>
	                				<b>Week</b>
	                			</td>
	                			<td>
	                				<?= $data['Week']['week_name']?>
	                			</td>
	                		</tr>
	                	</tbody>
	                </table>
	                <?php } ?>
                <?php } ?>
			</div>			
		</div>
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

