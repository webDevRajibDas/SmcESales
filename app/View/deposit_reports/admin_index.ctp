<?php //pr($aso_offices);die;?>
	<style>
		div{
			margin:0px;
			padding:0px;
			font-family: arial;
		}
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
		}
		.page-footer{
			text-align:center;
			margin-bottom:5px;
		}
		.print-table{
			font-size: 11px;
		}
		#content { display: block; }
		@media print
			{
				#non-printable { display: none; }
				#content { display: block; }
				table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
		}
			}
    </style>
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
					<?php echo $this->Form->create(array('role' => 'form')); ?>
					<table class="search">
						<tr>
							<td width="33%"><?php echo $this->Form->input('office_id', array('name'=>'office_id', 'id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----','options'=>$aso_offices)); ?></td>
							<td width="33%"><?php echo $this->Form->input('date_from', array('name'=>'date_from', 'class' => 'form-control datepicker','required'=>false)); ?></td>
							<td width="33%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>false, 'name'=>'date_to')); ?></td>
						</tr>				
						<tr align="center">
							<td colspan="3">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<input type="button" onclick="PrintElem('content')" value="print a div!" />

							</td>						
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>
				<table id="Deposits" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php //echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('memo_id'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('outlet_id'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('sales_person_id'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('bank_branch_id'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('bank_id'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('instrument_type','Deposit Type'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('slip_no'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('deposit_amount'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('deposit_date'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$total_amount = 0;
					//foreach ($deposits as $deposit): 
					?>
					<tr>
						<td class="text-center"><?php //echo h($deposit['Deposit']['id']); ?></td>
						<td class="text-center"><?php //if($deposit['Deposit']['instrument_type']==2){ echo $deposit['Deposit']['memo_id']; } ?></td>
						<td class="text-center"><?php //if($deposit['Deposit']['instrument_type']==2){ echo h($deposit['Memo']['Outlet']['name']); } ?></td>
						<td class="text-center"><?php //echo h($deposit['SalesPerson']['name']); ?></td>
						<td class="text-center"><?php //echo h($deposit['BankBranch']['name']); ?></td>
						<td class="text-center"><?php //echo h($deposit['BankBranch']['Bank']['name']); ?></td>
						<td class="text-center"><?php //if($deposit['Deposit']['instrument_type']==1){ echo 'Cash'; }else if($deposit['Deposit']['instrument_type']==2){ echo 'Cheque'; }; ?></td>
						<td class="text-center"><?php //echo h($deposit['Deposit']['slip_no']); ?></td>
						<td class="text-center"><?php //echo sprintf('%.2f',$deposit['Deposit']['deposit_amount']); ?></td>
						<td class="text-center"><?php //echo $this->App->dateformat($deposit['Deposit']['deposit_date']); ?></td>
					</tr>
					<?php 
					//$total_amount = $total_amount + $deposit['Deposit']['deposit_amount'];
					//endforeach; 
					?>
					<tr>
						<td align="right" colspan="8"><b>Total Amount :</b></td>
						<td align="center"><b><?php //echo sprintf('%.2f',$total_amount); ?></b></td>
						<td></td>
						<td></td>
					</tr>
					</tbody>
				</table>
				<div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
						<?php	//echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>	
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>									
								<?php
									//echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
									//echo $this->Paginator->numbers(array("separator" => "","currentTag" => "a", "currentClass" => "active","tag" => "li","first" => 1));
									//echo $this->Paginator->next(__("next"), array("tag" => "li","currentClass" => "disabled"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
								?>								
							</ul>
						</div>
					</div>
				</div>				
			</div>			
		</div>
	</div>
</div>

<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;">
		<div style="text-align:right;width:100%;">Page No :1 of 2</div>
		<div style="text-align:right;width:100%;">Print Date :19 Jul 2017</div>
		

		<div style="width:100%;text-align:center;">
			<div style="font-size:20px;">SMC Enterprise Limited</div>
			<div style="font-size:14px;"><strong>SALES, COLLECTION AND DEPOSITION STATEMENT</strong></div>
			<div style="font-size:11px;">Between:&nbsp;&nbsp;01&nbsp;May&nbsp;2017&nbsp;to&nbsp;31&nbsp;May&nbsp;2017&nbsp;&nbsp;&nbsp;&nbsp;Reporting Date:&nbsp;&nbsp;31&nbsp;July&nbsp;2017</div>
			<div style="font-size:11px;padding-bottom:30px;">Area : Rajshahi</div>
		</div>	  
		

		
		<table class="print-table" style="width:100%" border="1px solid black" cellpadding="2px" cellspacing="0">
			  <tr>
				<th >NAME OF SO'S</th>
				<th colspan="3">OPENING BALANCE</th> 
				<th colspan="3">SALES</th>
				<th colspan="3">CREDIT COLLECTION</th>
				<th>TOTAL COLLECTION</th>
				<th colspan="3">DEPOSIT</th>
				<th colspan="3">CLOSING BALANCE</th>
				<th colspan="3">CLOSING FOLLOW-UP</th>
			  </tr>
			  <tr>
				<td></td>
				<td>IN THE HANDS OF SO</td>
				<td>MARKET OUTSTANDING</td>
				<td>RECEIVABLE FORM A.O/Z.O</td>
				<td>CASH</td>
				<td>CREDIT</td>
				<td>TOTAL</td>
				<td>AGAINST CUR PERIOD'S SALE</td>
				<td>AGAINST PRE PERIOD'S SALE</td>
				<td>TOTAL</td>
				<td></td>
				<td>AGAINST CUR PERIOD'S SALE</td>
				<td>AGAINST PRE PERIOD'S SALE</td>
				<td>TOTAL</td>
				<td>IN HANDS OF SO</td>
				<td>MARKET OUTSTANDING</td>
				<td>RECEIVABLE FORM A.O/Z.O</td>
				<td>Date of Subsequent Period</td>
				<td>Subsequent Period Deposit</td>
				<td>Balance</td>
			  </tr>
			  <tr>
				<td></td>
				<td align="center">[A]</td>
				<td align="center">[B]</td>
				<td align="center">[C=A+B]</td>
				<td align="center">[D]</td>
				<td align="center">[E]</td>
				<td align="center">[F=D+E]</td>
				<td align="center">[G]</td>
				<td align="center">[H]</td>
				<td align="center">[I=G+H]</td>
				<td align="center">[J=I+D]</td>
				<td align="center">[K1]</td>
				<td align="center">[K2]</td>
				<td align="center">[K=K1+K2]</td>
				<td align="center">[L=(J-K)+A]</td>
				<td align="center">[M=(F-J)+B]</td>
				<td align="center">[N=L+M]</td>
				<td align="center">[O]</td>
				<td align="center">[P]</td>
				<td align="center">[Q=L-P]</td>
			  </tr>
			  <?php
			  	foreach ($sales_people as $sales_person) {
			  ?>
			  <tr>
				<td align="left"><?php echo $sales_person['SalesPerson']['name']; ?></td>
				<td align="right">3</td>
				<td align="right">4</td>
				<td align="right">5</td>
				<td align="right">4</td>
				<td align="right">4</td>
				<td align="right">44</td>
				<td align="right">1000</td>
				<td align="right">2</td>
				<td align="right">004</td>
				<td align="right">567</td>
				<td align="right">150005</td>
				<td align="right">2018</td>
				<td align="right">456</td>
				<td align="right">123</td>
				<td align="right">1010</td>
				<td align="right">2</td>
				<td align="right">4</td>
				<td align="right">258</td>
				<td align="right">150005</td>
			  </tr>
			  <?php
			  	}
			  ?>			  
      </table>
	  
		<div style="width:100%;padding-top:100px; font-size:13px;">
			<div style="width:33%;text-align:left;float:left">
				Prepared by:______________ 
			</div>
			<div style="width:33%;text-align:center;float:left">
				Checked by:______________ 
			</div>
			<div style="width:33%;text-align:right;float:left">
				Signed by:______________
			</div>		  
		</div>
		<div style="width:100%;padding-top:0px; font-size:13px;">
			<div style="width:33%;text-align:left;float:left">
				<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(I.T.O)</span>
			</div>
			<div style="width:33%;text-align:center;float:left">
				<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(A.O)</span> 
			</div>
			<div style="width:33%;text-align:center;float:left">
				<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(S.M)</span>
			</div>		  
		</div>
		
		<!--<div class="page-footer">
			<h5>"This report has been generated from SMC IMIS[Rajshahi Area]. The information is confidential and for internal use only"</h5>
		</div>-->
		
		
	</div>


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

	<script>
			function PrintElem(elem)
					{
						var mywindow = window.open('', 'PRINT', 'height=400,width=600');

						//mywindow.document.write('<html><head><title>' + document.title  + '</title>');
						mywindow.document.write('<html><head><title></title>');
						mywindow.document.write('</head><body >');
						//mywindow.document.write('<h1>' + document.title  + '</h1>');
						mywindow.document.write(document.getElementById(elem).innerHTML);
						mywindow.document.write('</body></html>');

						mywindow.document.close(); // necessary for IE >= 10
						mywindow.focus(); // necessary for IE >= 10*/

						mywindow.print();
						mywindow.close();

						return true;
					}
	</script>