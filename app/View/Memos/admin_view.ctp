<?php
$memos = new MemosController();
//$memos->dd($memo_details);
?>


<style>
	#content {
		display: none;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Memo Details'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Memo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					<button type="button" onclick="PrintElem('content')" class="btn btn-primary">
						<i class="glyphicon glyphicon-print"></i> Print
					</button>
				</div>
			</div>
			<div class="box-body">
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td width="25%"><strong><?php echo 'Memo No. :'; ?></strong></td>
							<td><?php echo $memo['Memo']['memo_no']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Memo Date/Time :'; ?></strong></td>
							<td><?php echo $memo['Memo']['memo_time']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Sales Person :'; ?></strong></td>
							<td><?php echo $memo['SalesPerson']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Outlet :'; ?></strong></td>
							<td><?php echo $memo['Outlet']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Market :'; ?></strong></td>
							<td><?php echo $memo['Market']['name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Territory :'; ?></strong></td>
							<td><?php echo $memo['Territory']['name']; ?></td>
						</tr>
						<!-- 	<tr>
							<td><strong><?php echo 'First Push At :'; ?></strong></td>
							<td><?php echo date('d M y h:ia', strtotime($memo['Memo']['created_at'])); ?></td>
						</tr> -->
						<tr>
							<td><strong><?php echo 'Push  At :'; ?></strong></td>
							<td><?php echo date('d M y h:ia', strtotime($memo['Memo']['updated_at'])); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo 'Memo Status :'; ?></strong></td>
							<td>
								<?php
								//echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; 
								if ($memo['Memo']['status'] == 1) {
									echo '<span class="btn btn-danger btn-xs">Due</span>';
								} elseif ($memo['Memo']['status'] == 2) {
									echo '<span class="btn btn-success btn-xs">Paid</span>';
								} else {
									echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
								}
								?>
							</td>
						</tr>
						<?php if ($memo['Memo']['remarks'] != '') { ?>
							<tr>
								<td><strong><?php echo 'Remarks :'; ?></strong></td>
								<td><?php echo $memo['Memo']['remarks'] ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div class="box-body">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th class="text-center" width="50">SL.</th>
							<th class="text-left">Product Name</th>
							<th class="text-left">Measurement Unit</th>
							<th class="text-center">Sales Qty</th>
							<th class="text-center">VAT</th>
							<th class="text-right">Price</th>
							<th class="text-right">Total Price</th>
						</tr>
						<?php
						if (!empty($memo_details)) {
							$sl = 1;
							$total_price = 0;
							foreach ($memo_details as $val) {
								//$productPrice = $memos->getProductPrice($val['MemoDetail']['product_id'], $val['Memo']['memo_date']);
						?>
								<tr>
									<td align="center"><?php echo $sl; ?></td>
									<td>
										<?php echo $val['Product']['name']; ?>
										<?php if ($val['MemoDetail']['price'] == 0.00 && $val['MemoDetail']['is_bonus'] == 0) { ?>
											<span class="label label-info">Open</span>
										<?php } ?>
									</td>
									<td><?php echo $val['MeasurementUnit']['name']; ?></td>
									<td align="center"><?php echo $val['MemoDetail']['sales_qty']; ?></td>
									<td>
										<?php //echo $productPrice['vat']; 
										?>
										<?php echo $vat_amount = $memos->get_vat_by_product_id_memo_date_v2($val['MemoDetail']['product_id'], $val['Memo']['memo_date'], $val['Memo']['is_distributor'], $val['Memo']['outlet_id']) ?>

									</td>
									<td align="right">
									<?php
										if($memo['Memo']['is_distributor']==1){
											echo sprintf('%.2f', $val['MemoDetail']['actual_price']); 
										}else{
											echo sprintf('%.2f', $val['MemoDetail']['price']); 
										}
									?>
									</td>
									<td align="right">
										<?php
										if($memo['Memo']['is_distributor']==1){
											$total = $val['MemoDetail']['actual_price'] * $val['MemoDetail']['sales_qty'];
										}else{
											$total = $val['MemoDetail']['price'] * $val['MemoDetail']['sales_qty'];
										}
										echo sprintf('%.2f', $total);
										?>
									</td>
								</tr>
							<?php
								$total_price += round($total,2);
								if ($val['MemoDetail']['discount_amount']>0){
									$price_discount =  $price_discount + ($val['MemoDetail']['sales_qty'] * $val['MemoDetail']['discount_amount']);
								}
								$vat = $total - ((100 * $total) / (100 + $vat_amount));
								$total_vat += $vat;
								$sl++;
							}
							?>
							<tr>
								<td align="right" colspan="6"><strong>Total Amount :</strong></td>
								<td align="right"><strong><?php echo sprintf('%.2f', $total_price); ?></strong></td>
							</tr>
							<tr>
								<td align="right" colspan="6"><strong>Total Discount :</strong></td>
								<td align="right"><strong><?php echo sprintf('%.2f', $memo['Memo']['total_discount']); ?></strong></td>
							</tr>
							<tr>
								<td align="right" colspan="6"><strong>Total Payable Amount :</strong></td>
								<td align="right"><strong><?php echo sprintf('%.2f', round(($total_price-$memo['Memo']['total_discount']),2)); ?></strong></td>
							</tr>
							<tr>
								<td align="right" colspan="6"><strong>Total VAT Amount :</strong></td>
								<td align="right"><strong><?php echo sprintf('%.2f', round($total_vat,2)); ?></strong></td>
							</tr>
						<?php
						} else {
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

	</div>
</div>
<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;margin-top: 50px; font-size: 11px;">
	<style type="text/css">
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

			table,
			th,
			td {
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
			}

			@page {
				size: auto;
				margin: 0;
				/*margin: 30px;*/
			}

			body {
				margin: 12.7mm 12.7mm 0 12.7mm;
			}
		}
	</style>
	<?php
	// pr($so_info);exit;
	if (!empty($memo_details)) {
		$sl = 1;
		$total_price = 0;
		$total_vat = 0;
		$in = 1;
		$data = array();
		$tr = '';
		foreach ($memo_details as $val) {
			if($memo['Memo']['is_distributor']==1){
				$total = $val['MemoDetail']['actual_price'] * $val['MemoDetail']['sales_qty'];
			}else{
				$total = $val['MemoDetail']['price'] * $val['MemoDetail']['sales_qty'];
			}
			$vat_amount = $memos->get_vat_by_product_id_memo_date_v2($val['MemoDetail']['product_id'], $val['Memo']['memo_date'], $val['Memo']['is_distributor'], $val['Memo']['outlet_id']);
			// $productPrice = $memos->getProductPrice($val['MemoDetail']['product_id'], $val['Memo']['memo_date']);
			
			$tr .= '<tr>
				<td align="center">' . $sl . '</td>
				<td>' . $val['Product']['name'] . '</td>
				<td align="right">' . $val['MemoDetail']['sales_qty'] . '</td>
				<td align="right">' . $vat_amount . '</td>
				<td align="right">' . sprintf('%.2f', ($memo['Memo']['is_distributor']==1?$val['MemoDetail']['actual_price']:$val['MemoDetail']['price'])) . '</td>
				<td align="right">' . sprintf('%.2f', $total) . '</td>
			 </tr>
			 ';


			$total_price += $total;
			if ($val['MemoDetail']['discount_amount']>0){
				$price_discount += ($val['MemoDetail']['sales_qty'] * $val['MemoDetail']['discount_amount']);
			}
			$vat = $total - ((100 * $total) / (100 + $vat_amount));
			$total_vat += $vat;
			$sl++;
			if ($in == 15) {
				$in = 1;
				$data[] = $tr;
				$tr = '';
			}
			$in++;
		}
		$data[] = $tr;
	}
	$total_page = count($data);
	?>
	<div class="page-top">
		<p style="font-size: 15px; border: 2px solid #000000; padding: 3px 5px; display: inline-block; margin: 0; float: right">
			Mushak-6.3</p>
	</div>
	<div style="margin-right : 0;float: right;text-align:right;" class="page_header">
		<br>
		<!--Page No :<?php /*echo '1 Of '.$total_page;*/ ?><br>
		Print Date :--><?php /*echo $this->App->dateformat(date('Y-m-d H:i:s'));*/ ?>
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
				Bangladesh, NBR, Dhaka</font>
		</p>
		<p style="margin: 0; padding: 5px 0 0; font-size: 16px">
			<!-- <font>Central Registered Affiliate Organization Delivery Vat Challan</font> -->
			<font>VAT CHALLAN PATRA</font>
		</p>
		<p style="margin: 0; padding: 0 0 0 5px; font-size: 16px">
			<!-- <font>Central Registered Affiliate Organization Delivery Vat Challan</font> -->
			<font> Clause Ga & Cha of Subrules 1 of Rules 40</font>
		</p>
	</div>
	<div style="width:100%;">
		<div style="width:25%;text-align:left;float:left">
			&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
		<div style="width:50%;text-align:center;float:left">
			<font id="heading_name">SMC Enterprise Limited</font><br>
			<span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
			<span id="heading_add">Central BIN:000049992-0101</span><br>
			<font>Issueing Office : <?php echo h($so_info['Office']['office_name']); ?></font><br>
			<font>Address : <?php echo h($so_info['Office']['address']); ?></font><br>
			<font>Issueing SO : <?php echo h($so_info['SalesPerson']['name'] . ',' . $so_info['Territory']['name']); ?></font>
		</div>
		<div style="width:25%;text-align:right;float:left">
			&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
	</div>

	<div style="width:100%;">
		<div style="width:50%;text-align:left;float:left">
			<?php echo "Recipient : " . h($memo['Outlet']['name'] . ', ' . $memo['Market']['name']); ?><br>
			<span style="margin-left: 25px;"><?= $so_info['st']['address'] ?></span><br>
			<?php /*echo "Address : " . h($so_info['Territory']['address']); */ ?>
			<!--<br>-->
		</div>

		<div style="width:50%;text-align:right;float:left">
			Memo No : <?php echo h($memo['Memo']['memo_no']); ?> <br>
			Date : <?php echo $this->App->dateformat($memo['Memo']['memo_date']); ?>
		</div>
	</div>
	<?php $page_count = 1;
	foreach ($data as $data) { ?>
		<?php if ($page_count > 1) { ?><div class="page_header">
				Page No :<?php echo $page_count . ' Of ' . $total_page; ?><br>
				Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s')); ?>
			</div><?php } ?>
		<div <?php if ($page_count > 1) {
					echo ' class="table_content"';
				} ?>>
			<table style="width:100% font-size:11px; <?php if ($page_count > 1) {
					echo 'margin-top: 50px';
				} ?>" border="1px solid black" cellspacing="0" text-align="center">
				<thead>
					<tr>
						<th>SL #</th>
						<th>Product</th>
						<th align="right">Sales Qty</th>
						<th align="right">VAT</th>
						<th align="right">Price</th>
						<th align="right">Total Price</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $data; ?>
				</tbody>
				<tr>
					<td align="right" colspan="5"><strong>Total Amount :</strong></td>
					<td align="right"><strong><?php echo sprintf('%.2f', $total_price);  ?></strong></td>
				</tr>
				<tr>
					<td align="right" colspan="5"><strong>Total Discount :</strong></td>
					<td align="right"><strong><?php echo sprintf('%.2f', $memo['Memo']['total_discount']); ?></strong></td>
				</tr>
				<tr>
					<td align="right" colspan="5"><strong>Total Payable Amount :</strong></td>
					<td align="right"><strong><?php echo sprintf('%.2f', round(($total_price-$memo['Memo']['total_discount']),2));  ?></strong></td>
				</tr>
				<tr>
					<td align="right" colspan="5"><strong>Total VAT Amount :</strong></td>
					<td align="right"><strong><?php echo sprintf('%.2f', $total_vat); ?></strong></td>
				</tr>
			</table>
			<p style="padding: 5px 0; margin: 0;font-size: small">* Product(s) price are SD free & including
				VAT</p>
			<?php if ($memo['Memo']['remarks'] != '') { ?>
				<p>
					<strong>N.B:</strong>
					<?php echo $memo['Memo']['remarks'] ?>
				</p>
			<?php } ?>
		</div>

		<div class="footer1">
			<div style="width:100%;padding-top:20px;" class="font_size">
				<div style="width:33%;text-align:left;float:left">
					Prepared by:<span style="border-bottom: 1px solid black;width: 70%;display: inline-block;"></span>
				</div>
				<div style="width:30%;text-align:center;float:left;margin-left: 3%;">
					Checked by:______________
				</div>
				<div style="width:33%;float:left">
					Carried by:<span style="border-bottom: 1px solid black;width: 70%;display: inline-block;"></span><br><br>
				</div>
			</div>

			<div style="width:100%;" class="font_size">
				<div style="width:53%;text-align:left;float:left">
					<h4> Received the goods for Delivery </h4>
					Driver's Signature :______________________ <br><br>
					<!--Name of the Driver :<span style="border-bottom: 1px solid black;width: 30%;display: inline-block;text-align:center;"> <?/*=$challan['Challan']['driver_name']*/ ?></span>-->
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
			"This Report has been generated from SMC Automated Sales System at [<?php echo h($so_info['Office']['office_name']); ?>]. This information is confidential and for internal use only."
		</footer>
		<div class="page-break"></div>

	<?php $page_count++;
	} ?>
</div>

<script>
	function PrintElem(elem) {
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
			mywindow.onload = function() {
				setTimeout(function() { // wait until all resources loaded 
					mywindow.print(); // change window to winPrint
					mywindow.close(); // change window to winPrint
				}, 200);
			};
		} else {
			mywindow.print();
			mywindow.close();
		}

		return true;
	}
</script>