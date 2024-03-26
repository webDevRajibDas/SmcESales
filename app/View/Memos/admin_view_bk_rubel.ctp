<style>
	#content { display: none; }
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Memo Details'); ?></h3>
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
							<td><strong><?php echo 'Memo Date :'; ?></strong></td>
							<td><?php echo $memo['Memo']['memo_date']; ?></td>
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
						<tr>
							<td><strong><?php echo 'Memo Status :'; ?></strong></td>
							<td>
								<?php //echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
                                <?php
									if ($memo['Memo']['status'] == 1) {
										echo '<span class="btn btn-danger btn-xs">Due</span>';
									}elseif ($memo['Memo']['status'] == 2) {
										echo '<span class="btn btn-success btn-xs">Paid</span>';
									}else{
										echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
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
							<th class="text-center">Sales Qty</th>
							<th class="text-right">Price</th>
							<th class="text-right">Total Price</th>
						</tr>
						<?php
						if(!empty($memo_details)){
						$sl = 1;
						$total_price = 0;
						foreach($memo_details as $val){
						?>
							<tr>
								<td align="center"><?php echo $sl; ?></td>
								<td><?php echo $val['Product']['name']; ?></td>
								<td align="center"><?php echo $val['MemoDetail']['sales_qty']; ?></td>
								<td align="right"><?php echo sprintf('%.2f',$val['MemoDetail']['price']); ?></td>
								<td align="right">
									<?php
									$total = $val['MemoDetail']['price'] * $val['MemoDetail']['sales_qty'];
									echo sprintf('%.2f',$total);
									?>
								</td>
							</tr>
						<?php
							$total_price =  $total_price + $total;
							$sl++;
						}
						?>
						<tr>
							<td align="right" colspan="4"><strong>Total Amount :</strong></td>
							<td align="right"><strong><?php echo sprintf('%.2f',$total_price); ?></strong></td>
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

	</div>
</div>
<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;margin-top: 50px; font-size: 11px;">
	<style type="text/css">
		@media print
		{
			#non-printable { display: none; }
			#content {
				display: block;
			}
			.table_content {
				padding-top: 50px;
			}
			table{
				width:100%;
				font-size: 11px;
				margin-top: 50px;
			}
			table, th, td {
				border: 1px solid black;
				border-collapse: collapse;
			}
			footer{
				position: fixed;
				bottom: 0;
				font-size: 10px;
			}
			.footer1{
				width:100%;
				height: 100px;
				/*position: relative;*/
				font-size: 10px;
				overflow-y: inherit;
			}

			.font_size{
				font-size: 11px;
			}
			.page-break{
				page-break-after: always;
			}
			#heading_name{
				font-size: 24px;
			}
			#heading_add{
				font-size: 18px;
			}
			.page_header{
				position: relative;
				width:100%;
				font-weight: normal;
				font-size: 8px;
				float: right;
				text-align: right;
				margin-right: 3%;
			}
			@page {size: auto;  margin: 30px; }
		}

	</style>
	<?php
	// pr($so_info);exit;
	if(!empty($memo_details))
	{
		$sl = 1;
		$total_price = 0;
		$in=1;
		$data=array();
		$tr='';
		foreach($memo_details as $val){
			$total = $val['MemoDetail']['price'] * $val['MemoDetail']['sales_qty'];
			$tr.='<tr>      
				<td align="center">'.$sl.'</td>            
				<td>'.$val['Product']['name'].'</td>
				<td align="center">'.$val['MemoDetail']['sales_qty'].'</td>
				<td align="center">'.sprintf('%.2f',$val['MemoDetail']['price']).'</td>
				<td>'.sprintf('%.2f',$total).'</td>
       		 </tr>
       		 ';


			$total_price =  $total_price + $total;
			$sl++;
			if($in==15){
				$in=1;
				$data[]=$tr;
				$tr='';
			}
			$in++;
		}
		$data[]=$tr;
	}
	$total_page=count($data);
	?>
	<div class="page_header">
		Page No :<?php echo '1 Of '.$total_page;?><br>
		Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s'));?>
	</div>
	<div style="width: 100%;height:30px;margin-top: 10px">
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

	<div style="width:100%;">
		<div style="width:25%;text-align:left;float:left">
			&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
		<div style="width:50%;text-align:center;float:left">
			<font id="heading_name"><b>SMC Enterprise Limited</b></font><br>
			<span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
			<font><b><u>Delivery Challan</u></b></font><br>
			<font><b>Issueing Office : <?php echo h($so_info['Office']['office_name']); ?></b></font><br>
			<font><b>Issueing SO : <?php echo h($so_info['SalesPerson']['name'].','.$so_info['Territory']['name']); ?></b></font>
		</div>
		<div style="width:25%;text-align:right;float:left">
			&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
	</div>

	<div style="width:100%;">
		<div style="width:50%;text-align:left;float:left">
			<?php echo "To : ". h($memo['Outlet']['name'].', '.$memo['Market']['name']); ?><br>
			<span style="margin-left: 25px;"><?=$so_info['st']['address']?></span>
		</div>

		<div style="width:50%;text-align:right;float:left">
			Memo No : <?php echo h($memo['Memo']['memo_no']); ?> <br>
			Date : <?php echo $this->App->dateformat($memo['Memo']['memo_date']); ?>
		</div>
	</div>
	<?php $page_count=1; foreach($data as $data){?>
		<?php if($page_count>1){?><div class="page_header">
			Page No :<?php echo $page_count.' Of '.$total_page;?><br>
			Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s'));?>
			</div><?php }?>
		<div <?php if($page_count>1){echo ' class="table_content"';}?>>
			<table style="width:100% font-size:11px; <?php if($page_count>1){echo 'margin-top: 50px';}?>" border="1px solid black"  cellspacing="0" text-align="center">
				<thead>
				<tr>
					<th>SL #</th>
					<th>Product</th>
					<th>Sales Qty</th>
					<th>Price</th>
					<th>Total Price</th>
				</tr>
				</thead>
				<tbody>
				<?php echo $data;?>
				</tbody>
				<tr>
					<td align="right" colspan="4"><strong>Total Amount :</strong></td>
					<td align="right"><strong><?php echo sprintf('%.2f',$total_price); ?></strong></td>
				</tr>
			</table>
		</div>

		<div class="footer1">
			<div style="width:100%;padding-top:100px;" class="font_size">
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
					<!--Name of the Driver :<span style="border-bottom: 1px solid black;width: 30%;display: inline-block;text-align:center;"> <?/*=$challan['Challan']['driver_name']*/?></span>-->
					<br><br><br>
					<h4> Approved by </h4>
					Signature :_____________________________ <br><br>
					Name :________________________________  <br> <br>
					Designation :___________________________ <br> <br>
				</div>

				<div style="width:33%;text-align:left;float:right;margin-right: 3px;">
					<h4> Received the goods in good condition: </h4>
					Signature of the Recipient:___________________ <br> <br>
					Name :___________________________________  <br><br>
					Designation :______________________________  <br><br>
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
		<footer style="width:100%;text-align:center;">
			"This Report has been generated from SMC Automated Sales System at [<?php echo h($so_info['Office']['office_name']); ?>]. This information is confidential and for internal use only."
		</footer>
		<div class="page-break"></div>

		<?php $page_count++; }?>
</div>

<script>
    function PrintElem(elem)
    {
        var mywindow = window.open('', 'PRINT', 'height=600,width=960');

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

