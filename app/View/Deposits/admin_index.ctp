<?php
App::import('Controller', 'DepositsController');
$DepositsController = new DepositsController;	

?>

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Deposit List'); ?></h3>
				<div class="box-tools pull-right">
					<?php //if($this->App->menu_permission('deposits','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Deposit'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('Deposit', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----','options'=>$offices)); ?></td>
							<td><?php //echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----','options'=>$outlets));   ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----' ,'options'=>$territories ));?></td>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['Deposit']['date_from'])=='' ? $current_date : $this->request->data['Deposit']['date_from']),'required'=>false)); ?></td>

						</tr>
						<tr>
							<td>
							<?php //echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets));  ?>
                            <?php echo $this->Form->input('instrument_type', array('label' => 'Deposit Type','id' => 'instrument_type','class' => 'form-control instrument_type','required'=>false,'empty'=>'---- Select Type ----','options'=>$instrument_types));  ?>
                            </td>
                            

							<td><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['Deposit']['date_to'])=='' ? $current_date : $this->request->data['Deposit']['date_to']),'required'=>false)); ?></td>

						</tr>					
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<?php echo $this->Form->button('<i class="fa fa-info"></i> Excel', array('type'=>'button','name'=>'downloadexcel','id'=>'downloadexcel','class' => 'btn btn-large btn-info','escape' => false)); ?>
							</td>
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>
                
                <div class="table-responsive">	
				<table id="Deposits" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('memo_no'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('outlet_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('bank_branch_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('bank_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('instrument_type','Deposit Type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('slip_no'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('deposit_amount'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('deposit_date'); ?></th>
                            <th class="text-center">Week</th>
                            <th class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$total_amount = 0;
					//pr($deposits);die();
					foreach ($deposits as $deposit): 
					?>
					<tr>
						<td class="text-center"><?php echo h($deposit['Deposit']['id']); ?></td>
						<td class="text-center"><?php if($deposit['Deposit']['type']==2){ echo $deposit['Deposit']['memo_no']; } ?></td>
						<td class="text-center">
						
						<?php //if($deposit['Deposit']['instrument_type']==2){ echo h($deposit['Memo']['outlet_id']); } ?>
                        
                        <?=($deposit['Deposit']['type']==2)?$DepositsController->getOutletName($deposit['Memo']['outlet_id']):'';?>
                        
                        </td>
						<td class="text-center"><?php echo h($deposit['Territory']['name']); ?></td>
						<td class="text-center"><?php echo h($deposit['BankBranch']['name']); ?></td>
						<td class="text-center">
						<?php //echo h($deposit['BankBranch']['bank_id']); ?>
                        <?=$DepositsController->getBankName($deposit['BankBranch']['bank_id'])?>
                        </td>
						<!-- <td class="text-center"><?php if($deposit['Deposit']['instrument_type']==1){ echo 'Cash'; }else if($deposit['Deposit']['instrument_type']==3){ echo 'Cheque'; }; ?></td>
						<td class="text-center"><?php if($deposit['Deposit']['instrument_type']==1){ echo 'Cash'; }else if($deposit['Deposit']['instrument_type']==3){ echo 'Cheque'; }; ?></td> -->
						<td class="text-center"><?php echo  h($instrument_type[$deposit['Deposit']['type']]) ?></td>
						<td class="text-center"><?=@$deposit['Deposit']['instrument_type']!=1?@$instrument_type[$deposit['Deposit']['instrument_type']]:''; ?></td>
						<td class="text-center"><?php echo h($deposit['Deposit']['slip_no']); ?></td>
						<td class="text-center"><?php echo sprintf('%.2f',$deposit['Deposit']['deposit_amount']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($deposit['Deposit']['deposit_date']); ?></td>
                        <td class="text-center"><?=$deposit['Week']['week_name']?></td>
                        <td class="text-center">
                    		<?php if ($deposit['Deposit']['editable'] == 1) {
								if ($this->App->menu_permission('deposits', 'admin_edit')) {
									echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $deposit['Deposit']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
								}
							}?>

							<?php if ($deposit['Deposit']['editable'] == 1) {
								if ($this->App->menu_permission('deposits', 'admin_delete')) {
									echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $deposit['Deposit']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'admin_delete'), __('Are you sure you want to delete # %s?', $deposit['Deposit']['id']));;
								}
							}?>
                        </td>
					</tr>
					<?php 
					$total_amount = $total_amount + $deposit['Deposit']['deposit_amount'];
					endforeach; 
					?>
					<tr>
						<td align="right" colspan="10"><b>Total Amount :</b></td>
						<td align="center"><b><?php echo sprintf('%.2f',$total_amount); ?></b></td>
						<td></td>
						<td></td>
					</tr>
					</tbody>
				</table>
                </div>
                
				<div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
						<?php	echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>	
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>									
								<?php
									echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
									echo $this->Paginator->numbers(array("separator" => "","currentTag" => "a", "currentClass" => "active","tag" => "li","first" => 1));
									echo $this->Paginator->next(__("next"), array("tag" => "li","currentClass" => "disabled"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
								?>								
							</ul>
						</div>
					</div>
				</div>				
			</div>			
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('#downloadexcel').click(function(){
			
			var office_id=$("#office_id").val();
			var territory_id=$("#territory_id").val();
			var instrument_type=$("#instrument_type").val();
			var DepositDateFrom=$("#DepositDateFrom").val();
			var DepositDateTo=$("#DepositDateTo").val();
            var data=[office_id,territory_id,instrument_type,DepositDateFrom,DepositDateTo];
            //alert(data);
            var arrStr = encodeURIComponent(JSON.stringify(data));
			window.open("<?=BASE_URL;?>deposits/download_xl/"+arrStr);
			//window.open("Deposits/download_xl/"+office_id+territory_id+instrument_type+DepositDateFrom+DepositDateTo);
			
		});
	});
</script> 


<script>
	$('#office_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
			type: 'post',
			data:{'office_id': 'office_id' }
	});
$('.territory_id').selectChain({
	target: $('.market_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_market';?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});

$('.market_id').selectChain({
	target: $('.outlet_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_outlet';?>',
	type: 'post',
	data:{'market_id': 'market_id' }
});

$('.territory_id').change(function(){
	$('.outlet_id').html('<option value="">---- Select Outlet ----');
});

</script>
