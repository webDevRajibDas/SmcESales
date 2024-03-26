<?php 
$maintain_dealer_type=1;
//pr($bns_product[$order_details[1]['OrderDetail']['bonus_product_id']]);die();
//pr($order_details);die();
$manages = new ManagesController();
/* 
Call by Masud for memos_details actual price
$memo_price = $manages->get_memo_actual_price($order['Order']['order_no']);

echo "<pre>";
		print_r($memo_price);
echo "</pre>"; */
//exit;
?>
<style>
  #content { display: none; }
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<!--- Office Info --->
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Office Info:'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Product Issue List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
				<div class="box-tools pull-right">
				   <button type="button" onclick="PrintElem('content')" class="btn btn-info">
							<i class="glyphicon glyphicon-print"></i> Print
					</button>
			   </div>
				
			</div>
			<div class="box-body">
		
				<table class="table table-bordered table-striped">
					<tbody>
						
						<?php if($maintain_dealer_type==1){ ?>
							<?php if($dealer_is_limit_check==1){ ?>
							<tr>
								<td><strong><?php echo 'Distributor :'; ?></strong></td>
								<td><?php echo $distributor['DistDistributor']['name']; ?></td>
							</tr>
						<?php }?>
					<?php }?>
						<tr>
							<td><strong><?php echo 'Market :'; ?></strong></td>
							<td><?php echo $order['Market']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Territory :'; ?></strong></td>
							<td><?php echo $order['Territory']['name']; ?></td>
						</tr>
						
						<tr>
							<td><strong><?php echo 'Outlet :'; ?></strong></td>
							<td><?php echo $order['Outlet']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Balance Amount :'; ?></strong></td>
							<td><?php echo $balance; ?></td>
						</tr>
						
					</tbody>
				</table>
			</div>
			<!----- End ------>
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Requisition Details'); ?></h3>
				
			</div>
			<div class="box-body">
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td width="25%"><strong><?php echo 'Requisition No. :'; ?></strong></td>
							<td><?php echo $order['Order']['order_no']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Requisition Date :'; ?></strong></td>
							<td><?php echo $order['Order']['order_date']; ?></td>
						</tr>
						
						<!-- <tr>
							<td><strong><?php //echo 'Distributor Balance :'; ?></strong></td>
							<td><?php //echo $orderLimits; ?></td>
						</tr> -->
						 
						<tr>
							<td><strong><?php echo 'Sales Person :'; ?></strong></td>
							<td><?php echo $order['SalesPerson']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Requisition Status :'; ?></strong></td>
							<td>
								<?php //echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
								<?php
								if($order['Order']['status']==0){
										echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
								}
								else{
									if ($order['Order']['credit_amount'] !=0 ) {
										echo '<span class="btn btn-danger btn-xs">Due</span>';
									}else {
										echo '<span class="btn btn-success btn-xs">Paid</span>';
									}
								}
								?>
							</td>
						</tr>
						<tr>
							<td><strong><?php echo 'Confirmation Status :'; ?></strong></td>
							<td>
								<?php //echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
								<?php
									if ($order['Order']['confirmed'] == 1) {
										echo '<span class="btn btn-success btn-xs">Confirm</span>';
									}else{
										echo '<span class="btn btn-info btn-xs draft">Pending</span>';
									}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="box-body">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th class="text-center" width="50">SL.</th>
							<th class="text-left">Product Name</th>
							<th class="text-center">Order Qty</th>
							<th class="text-center">Deliverd Qty</th>
							<th class="text-right">Price</th>
							<th class="text-right">Total Price</th>
							<th class="text-right">Remarks</th>
						</tr>
						<?php
						if(!empty($order_details)){
							$sl = 1;
							$total_price = 0;
							foreach($order_details as $val){
								if($val['OrderDetail']['price'] == 0) continue;
								?>
								<tr>
									<td align="center"><?php echo $sl; ?></td>
									<td><?php echo $val['Product']['name']; ?></td>
									<td align="center"><?php echo $val['OrderDetail']['sales_qty']; ?></td>
									<td align="right">
										<?php if(empty($val['OrderDetail']['deliverd_qty'])){
											echo 0;
										}else{ echo $val['OrderDetail']['deliverd_qty'];}?>
									</td>
									<td align="right">
									<?php echo sprintf('%.2f',$val['OrderDetail']['price']); ?>
									</td>
									<td align="right">
										<?php
										$total = round(($val['OrderDetail']['price'] * $val['OrderDetail']['deliverd_qty']),2);
										echo sprintf('%.2f',$total);
										?>
									</td>
									<td><?php echo $val['OrderDetail']['challan_remarks']; ?></td>
								</tr>
								<?php
								$total_price =  $total_price + $total;
								$sl++;
							}
							?>
							<tr>
								<td align="right" colspan="5"><strong>Total Deliverd Amount :</strong></td>
								<td align="right"><strong><?php echo sprintf('%.2f',$total_price); ?></strong></td>
							</tr>
							<tr>
								<td align="right" colspan="5"><strong>Total Discount Amount :</strong></td>
								<td align="right"><strong><?php echo sprintf('%.2f',$order['Order']['total_discount']); ?></strong></td>
							</tr>
							<tr>
								<td align="right" colspan="5"><strong>Total Payable Amount :</strong></td>
								<td align="right"><strong><?php echo sprintf('%.2f',round(($total_price-$order['Order']['total_discount']),2)); ?></strong></td>
							</tr>
						<?php
						}else{
						?>
						<tr>
							<td align="center" colspan="5"><strong>No product available</strong></td>	
						</tr>
						<?php
						}
						?>
				</table>
			</div>
		</div>
		<div>
			<div class="box-body">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th class="text-center" width="50">SL.</th>
							<th class="text-left">Product Name</th>
							<th class="text-left">Unit</th>
							<th class="text-center">Bonus Qty</th>
						</tr>
						<?php
						if(!empty($order_details)){
						$sl = 1;
						$total_price = 0;
						foreach($order_details as $val){
							if($val['OrderDetail']['price'] > 0) continue;
						?>
							<tr>
								<td align="center"><?php echo $sl; ?></td>
								<td><?php echo $bns_product[$val['OrderDetail']['bonus_product_id']]; ?></td>
								<td><?=$val['MeasurementUnit']['name'] ?></td>
								<td align="center"><?php echo $val['OrderDetail']['bonus_qty']; ?></td>
							</tr>
						<?php
						$sl++;  }
						?>
						<?php
							}else{
						?>
						<tr>
							<td align="center" colspan="5"><strong>No product available</strong></td>
						</tr>
						<?php
							} 
						?>	
				</table>
			</div>
		</div>
		  <div class="row">
			<div class="col-lg-8"></div>
			<div class="col-lg-2"></div>
			<div class="col-lg-2">
			</div>
		</div>
	</div>
</div>




<div id="content" style="width:90%;height:100%; font-size: 11px;">
	<!--<style media="print">
		@page {
			size: auto;
			margin: 0;
		}
	</style>-->
	<style type="text/css">
		@media screen {
			div.divFooter {
				display: none;
			}
		}
		@media print {
			#non-printable {
				display: none;
			}

			#content {
				display: block;
			}

			.table_content {
				padding-top: 50px;
			}

			table {
				width: 100%;
				font-size: 11px;
				margin-top: 50px;
			}

			table, th, td {
				border: 1px solid black;
				border-collapse: collapse;
			}
			footer {
				position: fixed;
				left: 10%;
				bottom: 6mm;
				font-size: 10px;
			}

			.footer1 {
				width: 100%;
				height: 100px;
				/*position: relative;*/
				font-size: 10px;
				overflow-y: inherit;
			}

			.font_size {
				font-size: 11px;
			}

			.page-break {
				page-break-after: always;
			}

			#heading_name {
				font-size: 18px;
			}

			#heading_add {
				font-size: 16px;
			}

			.page_header {
				position: relative;
				width: 100%;
				font-weight: normal;
				font-size: 8px;
				float: right;
				text-align: right;
				margin-right: 3%;
				/* position: relative;
				 top:0px;normal
				 right:0px;
				 width:30%;
				 font-size: 8px;
				 margin-bottom: 10px;*/
			}

			@page {
				size: auto;
				margin: 0;
				/*margin: 30px;*/
			}

			body {
				margin: 12.7mm;
			}
		}

	</style>

	<?php
	// pr($so_info);exit;
	if(!empty($order_details))
	{
		$bonusProduct = array();
		foreach($order_details as $val){

			if($val['OrderDetail']['price'] > 0) continue;
				$batch = $manages->getProductBatchList($val['OrderDetail']);
				$val['OrderDetail']['batch'] = $batch['batch'];
				$val['OrderDetail']['pname'] = $val['Product']['name'];
				$val['OrderDetail']['mname'] = $val['MeasurementUnit']['name'];
				$val['OrderDetail']['sales_qty'] = $batch['convert_qty'];
				$bonusProduct[$val['OrderDetail']['product_id']] = $val['OrderDetail'];
			
		}

		//echo '<pre>';print_r($bonusProduct);exit;

		$sl = 1;
		$in=0;
		$total_quantity = 0;
		$total_received_quantity = 0;
		$data=array();
		$tr='';
		$gross_value = 0;

		$bonusdata = 1;
		
		//exit;
		foreach($order_details as $val){

			if($val['OrderDetail']['price'] == 0) continue;
			$productPrice = $manages->getProductPrice($val['OrderDetail']['product_id'], $val['Order']['order_date']);	
		
			$total_price = round(($val['OrderDetail']['price'] * $val['OrderDetail']['deliverd_qty']),2);
			$gross_value += $total_price;
			$total_price = sprintf('%.2f',$total_price);
			$price = sprintf('%.2f',$val['OrderDetail']['price']);

			$bproductinfo = $bonusProduct[$val['OrderDetail']['product_id']];
			$totalqty = 0;
			if(!empty($bproductinfo)){

				$bqty = $bproductinfo['sales_qty'];

				$batch = $manages->getProductSalebaleBonusBatchList($val['OrderDetail'], $bproductinfo);
				
				$totalqty = $batch['total_qty'];
				
				unset($bonusProduct[$val['OrderDetail']['product_id']]);

			}else{
				$bqty = 0;
				$totalqty = $val['OrderDetail']['deliverd_qty'];

				$batch = $manages->getProductBatchList($val['OrderDetail']);

			}

			$tr.='<tr>		
					<td align="center">'.$sl.'</td>
					<td>'.$val['Product']['name'].'</td>
					<td>' . $val['MeasurementUnit']['name'] . '</td>
					<td align="center">'.$val['OrderDetail']['sales_qty'].'</td>
					<td align="right">'.$val['OrderDetail']['deliverd_qty'].'</td>
					<td align="right">'.$bqty.'</td>
					<td align="right">'.$batch['batch'].'</td>
					<td align="right">' . $productPrice['vat'] . '</td>
					<td align="right">'.$price.'</td>
					<td align="right">'.$total_price.'</td>
					<td align="right">'. $manages->get_remarks_product_id_munit_id($val['OrderDetail']['Product']['id'], $val['MeasurementUnit']['id'],$totalqty) .'</td>
				</tr>';
			$sl++;
			if($in==22){
				$in=1;
				$data[]=$tr;
				$tr='';
				$bonusdata = 0;
			}
			$in += $batch['count'];
		   // $in++;


		}

		if( $bonusdata == 1){
			$sl = $sl+1;
			foreach($bonusProduct as $val){
				
				$in += $val['b_batch']['count'];

				if($in==22){
					$in=1;
					$data[]=$tr;
					$tr='';
					$bonusdata = 0;
				}
	
				$tr.='<tr>		
						<td align="center">'.$sl.'</td>
						<td>'.$bns_product[$val['bonus_product_id']].'</td>
						<td>' . $val['mname'] . '</td>
						<td align="center">0</td>
						<td align="right">0</td>
						<td align="right">'.$val['sales_qty'].'</td>
						<td align="right">'.$val['batch'].'</td>
						<td align="right">0</td>
						<td align="right">0</td>
						<td align="right">0</td>
						<td align="right">'.$manages->get_remarks_product_id_munit_id($val['product_id'], $val['measurement_unit_id'],$val['bonus_qty']).'</td>
					</tr>';
				$sl++;
				
				
	
			}


		}

		$data[]=$tr;

	}

?>
<div>
		<p style="font-size: 15px; border: 2px solid #000000; padding: 3px 5px; display: inline-block; margin: 0; float: right">
			Mushak-6.3</p>
	</div>
	<div style="margin-right : 0; margin-top: 10px !important;" class="page_header">
		<!--Page No :<?php /*echo '1 Of ' . $total_page; */ ?><br>
		Print Date :--><?php /*echo $this->App->dateformat(date('Y-m-d H:i:s')); */ ?>
	</div>
	<div style="width: 100%;height:25px;margin-top: 10px">
		<div style="width: 33%;float: left;">
			Form No: ADM/FORM/003
		</div>
		<div style="width: 33%;float: left;text-align: center;">
			Effective Date : 01-08-2015<br>
		</div>
		<div style="width: 33%;float: right; text-align:right;">
			Version No: 03
		</div>
	</div>
	<div style="width:100%;text-align:center;float:left">
		<p style="margin-bottom: 0; padding-bottom: 0">
			<font id="heading_name">Government of the People's Republic of
				Bangladesh, NBR, Dhaka</font></p>
		<p style="margin: 0; padding: 5px 0; font-size: 16px">
		<font>Central Registered Affiliate Organization Delivery Product Issue</font></p>
		<p style="margin: 0; padding: 0 0 0 5px; font-size: 16px">
			
			<font> Clause Ga & Cha of Subrules 1 of Rules 40</font>
		</p>
	</div>
	<div style="width:100%;">
		<div style="width:25%;text-align:left;float:left">
			&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
		<div style="width:50%;text-align:center;float:left;">
			<font id="heading_name">SMC Enterprise Limited</font><br>
			<span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
			<span id="heading_add">Central BIN:000049992-0101</span><br>
			<font>Issueing Office : <?php echo h($order['Office']['office_name']); ?></font><br>
		</div>
		<div style="width:25%;text-align:right;float:left">
			&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
	</div>

<div style="width:100%;">
	<div style="width:50%;text-align:left;float:left">
		<?php echo "Recipient : ". h($distributor['DistDistributor']['name']); ?><br>
		<?php echo "Current Balance : ".h($balance); ?>
	</div>
	<div style="width:50%;text-align:right;float:left">
		Requisition No : <?php echo h($order['Order']['order_no']); ?> <br>
		Date : <?php echo $this->App->dateformat($order['Order']['order_date']); ?>
	</div>

</div> 
<?php $page_count=1; foreach($data as $data){?>
<?php if($page_count>1){?>
	<div class="page_header">
	Page No :<?php echo $page_count.' Of '.$total_page;?><br>
	Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s'));?>
</div><br><br><br><br><br><br><br><br><?php }?>
<div <?php if($page_count>1){echo ' class="table_content"';}?>>
	<table style="width:100% font-size:11px; <?php if($page_count>1){echo 'margin-top: 50px';}?>" border="1px solid black"  cellspacing="0" text-align="center">
		<thead>
			<tr>
				<th class="text-center" width="50">SL.</th>
				<th class="text-left">Product Name</th>
				<th>Unit</th>
				<th class="text-center">Order Qty</th>
				<th class="text-center">Deliverd Qty</th>
				<th class="text-center">B.Qty</th>
				<th class="text-center">Batch</th>
				<th>VAT</th>
				<th class="text-right">Price</th>
				<th class="text-right">Total Price</th>
				<th class="text-right">Remarks</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $data;?>
			<tr>
				<td align="right" colspan="9">Total Price :</td>
				<td align="right"><?php echo sprintf('%.2f',round($gross_value,2));?></td>
				<td></td>
			</tr>
			<tr>
				<td align="right" colspan="9">Total Discount :</td>
				<td align="right"><?php echo sprintf('%.2f',round($order['Order']['total_discount'],2));?></td>
				<td></td>
			</tr>
			<tr>
				<td align="right" colspan="9">Total Payable :</td>
				<td align="right"><?php echo sprintf('%.2f',round($gross_value-$order['Order']['total_discount'],2));?></td>
				<td></td>
			</tr>
		</tbody> 
	</table>
	<br>
	<br>
</div>
<div class="footer1">
			<div style="width:100%;padding-top:0px;" class="font_size">
				<div style="width:33%;text-align:left;float:left">
					Prepared by:<span
						style="border-bottom: 1px solid black;width: 70%;display: inline-block;">&nbsp;<?= $user_name['User']['username'] ?></span>
				</div>
				<div style="width:30%;text-align:center;float:left;margin-left: 3%;">
					Checked by:______________
				</div>
				<div style="width:33%;float:left">
					Carried by:<span
						style="border-bottom: 1px solid black;width: 70%;display: inline-block;">&nbsp;</span><br><br>
					Truck No: <span
						style="border-bottom: 1px solid black;width: 70%;display: inline-block">&nbsp; <?=$order['Order']['truck_no'];?></span>
				</div>
			</div>
			<div style="width:100%;" class="font_size">
				<div style="width:53%;text-align:left;float:left">
					<h4> Received the goods for Delivery </h4>
					Driver's Signature :______________________ <br><br>
					Name of the Driver :<span
						style="border-bottom: 1px solid black;width: 30%;display: inline-block;text-align:center;">&nbsp; <?=$order['Order']['driver_name'];?> </span>
					<br><br><br>
					<h4> Approved by </h4>
					Signature :_____________________________ <br><br>
					Name :________________________________ <br> <br>
					Designation :___________________________ <br> <br>
				</div>
				<div style="width:33%;text-align:left;float:right;margin-right: 3px;">
					<h4> Received the goods in good condition: </h4>
					Signature of the Recipient:___________________ <br> <br>
					Name :___________________________________ <br><br>
					Designation :______________________________ <br><br>
					Date :____________________________________ <br><br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					(Seal)&nbsp;&nbsp;&nbsp;&nbsp;
					<br><br><br><br>
				</div>
			</div>
		</div>
		<br><br><br><br>
		<footer style="width:100%;">
				"This Report has been generated from SMC Automated Sales System at
				[<?php echo h($order['Office']['office_name']); ?>]. This information is confidential and for internal use
				only."
		</footer>
	<div class="page-break"></div>
	<?php $page_count++; }?>

<!-- <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> -->
<!--- Bonus---->
<!-- 
<?php if($b_tr != ''){?>
<?php $bonus_page_count=1; foreach($bonus_data as $bonus){?>
<?php if($bonus_page_count>1){?>
	<div class="page_header">
	Page No :<?php echo $bonus_page_count.' Of '.$total_bonus_page;?><br>
	Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s'));?>
</div><br><br><br><br><br><br><br><br><?php }?>
<div> 
	
	<table style="width:100% font-size:11px;<?php if($total_bonus_page>1){echo 'margin-top: 50px';}?>" border="1px solid black"  cellspacing="0" text-align="center">
		<thead>
			<tr>
				<th class="text-center" width="50">SL.</th>
				<th class="text-left">Product Name</th>
				<th class="text-left">Unit</th>
				<th class="text-center">Bonus Qty</th>
				<th class="text-center">Batch</th>
				<th class="text-center">Remarks</th>
			</tr>
		</thead>  
		<tbody>
			<?php echo $b_tr;?>
		</tbody> 
	</table>
</div>

<div class="footer1">
			<div style="width:100%;padding-top:20px;" class="font_size">
				<div style="width:33%;text-align:left;float:left">
					Prepared by:<span
						style="border-bottom: 1px solid black;width: 70%;display: inline-block;">&nbsp;<?= $user_name['User']['username'] ?></span>
				</div>
				<div style="width:30%;text-align:center;float:left;margin-left: 3%;">
					Checked by:______________
				</div>
				<div style="width:33%;float:left">
					Carried by:<span
						style="border-bottom: 1px solid black;width: 70%;display: inline-block;">&nbsp;</span><br><br>
					Truck No:&nbsp;<span
						style="border-bottom: 1px solid black;width: 70%;display: inline-block"></span>
				</div>
			</div>

			<div style="width:100%;" class="font_size">
				<div style="width:53%;text-align:left;float:left">
					<h4> Received the goods for Delivery </h4>
					Driver's Signature :______________________ <br><br>
					Name of the Driver :<span
						style="border-bottom: 1px solid black;width: 30%;display: inline-block;text-align:center;"> </span>
					<br><br><br>
					<h4> Approved by </h4>
					Signature :_____________________________ <br><br>
					Name :________________________________ <br> <br>
					Designation :___________________________ <br> <br>
				</div>

				<div style="width:33%;text-align:left;float:right;margin-right: 3px;">
					<h4> Received the goods in good condition: </h4>
					Signature of the Recipient:___________________ <br> <br>
					Name :___________________________________ <br><br>
					Designation :______________________________ <br><br>
					Date :____________________________________ <br><br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					(Seal)&nbsp;&nbsp;&nbsp;&nbsp;
					<br><br><br><br><br><br><br><br>
				</div>
			</div>
		</div>
		<br><br><br><br>
		<footer style="width:100%;">
				"This Report has been generated from SMC Automated Sales System at
				[<?php echo h($order['Office']['office_name']); ?>]. This information is confidential and for internal use
				only."
		</footer>
<div class="page-break"></div>
<?php $bonus_page_count++; } }?>
 -->
<!-- end Bonus--->
</div>




<script>
	function PrintElem(elem){
		var mywindow = window.open('', 'PRINT', 'height=600,width=960');

		mywindow.document.write('<html><head><title></title>');
		mywindow.document.write('</head><body >');
		//mywindow.document.write('<h1>' + document.title  + '</h1>');
		mywindow.document.write(document.getElementById(elem).innerHTML);
		mywindow.document.write('</body></html>');

		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10*/

		var is_chrome = Boolean(window.chrome);
		if (is_chrome) {
			mywindow.onload = function () {
				setTimeout(function () { // wait until all resources loaded 
					mywindow.print();  // change window to winPrint
					mywindow.close();// change window to winPrint
				}, 200);
			};
		}
		else {
			mywindow.print();
			mywindow.close();
		}

		return true;
	}
</script>