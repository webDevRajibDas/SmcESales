<?php
$creditnotes = new CreditNotesController();
?>
<style>
    
</style>

<style>
    #divLoading {
        display: none;
    }

    #divLoading.show {
        display: block;
        position: fixed;
        z-index: 100;
        background-image: url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
        background-color: #666;
        opacity: 0.4;
        background-repeat: no-repeat;
        background-position: center;
        left: 0;
        bottom: 0;
        right: 0;
        top: 0;
    }

    #loadinggif.show {
        left: 50%;
        top: 50%;
        position: absolute;
        z-index: 101;
        width: 32px;
        height: 32px;
        margin-left: -16px;
        margin-top: -16px;
    }
</style>

<div id="divLoading" class=""> </div>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">



            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title ?></h3>
                <?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New OutletCharacteristic Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
            </div>


            <div class="box-body">

			<div class="pull-right csv_btn" style="padding-top:20px;">
				&nbsp;<button onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>&nbsp;
				&nbsp;<?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>&nbsp;
            </div>

				<div id="content" style="width:100%; margin:0 1.5%;">

					<style type="text/css">
						.table-responsive {
							color: #333;
							font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
							line-height: 1.42857;
						}

						.report_table {
							font-size: 12px;
						}

						.qty_val {
							width: 125px;
							margin: 0;
							float: left;
							text-transform: capitalize;
						}

						.val {
							border-right: none;
						}

						p {
							margin: 2px 0px;
						}

						.bottom_box {
							float: left;
							width: 33.3%;
							text-align: center;
						}

						td,
						th {
							padding: 5px;
						}

						table {
							border-collapse: collapse;
							border-spacing: 0;
						}

						.titlerow,
						.totalColumn {
							background: #f1f1f1;
						}

						.report_table {
							margin-bottom: 18px;
							max-width: 100%;
							width: 100%;
						}

						.table-responsive {
							min-height: 0.01%;
							overflow-x: auto;
						}
						#info_person p{
							clear: both;
						}
						#mushak {
							border: 1px solid black;
							padding: 7px;
							width: 95px;
							float: right;
						}
					</style>

					<div class="table-responsive" style="width: 97%;">

						<div id="xls_body">

							<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">

							<div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
								<h2 style="margin:2px 0;">Government of the People's Republic of Bangladesh</h2>


								<h3 style="margin:2px 0;">National Board of Revenue</h3>

								<h4 style="margin:2px 0; font-weight: bold;">Credit Note</h4>
								<p>[ See clauses (Cho/G) of Sub-Rule (1) of Rule 40]</p>

								<p id="mushak">
									Mushak-6.7
								</p>
								
								<div style="clear: both;margin-right: 160px;text-align: ;text-align: left;float: right;">
									<p style="font-weight: normal;">
										Credit Note No : <?=$credit_info['CreditNote']['credit_number']; ?>
									</p>
									<p style="font-weight: normal;">
										Date Of Issue : <?=date("Y-m-d", strtotime($credit_info['CreditNote']['created_at'])); ?>
									</p>
									<p style="font-weight: normal;">
										Time Of Issue : <?=date("h:i:s a", strtotime($credit_info['CreditNote']['created_at'])); ?>
									</p>
								</div>

								
							</div>
							<div id="info_person">
								<p> Name of Registered Person : Social Marketing Company</p>
								<p> BIN of Registered Person : 000049992-0101</p>
								<p> Address of Registered Person : SMC Tower, 33 Banani C/A, Dhaka- 1213</p>
								<p>Name of Reveiver/Buyer : <?=$credit_info['Outlet']['name'];?></p>
								<p>BIN of Reveiver/Buyer : </p>
								<p>Address : <?=$credit_info['Outlet']['address'];?></p>
								<p>Vehicle  Nature & No : </p>
								<br><br>
							</div>

							<div style="float:left; width:100%;">

								<table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
									<thead>
										<tr>
											<th style="text-align:left;" width="8%" rowspan="2">SL NO.</th>
											<th style="text-align:left;" width="15%" rowspan="2">Tax invoice Number</th>
											<th style="text-align:left;" width="6%" rowspan="2"> Date</th>
											<th style="text-align:left;" width="15%" rowspan="2">Reasons of issuing credit note</th>
											<th style="text-align:left;" width="15%" rowspan="2">Product</th>
											<th style="text-align:center;" width="30%" colspan="4" >Invoice of Supply</th>
											<th style="text-align:center;" width="30%" colspan="4" >Decreasing adjustment related</th>
										</tr>
										<tr>
											<th>Value</th>
											<th>Quantity</th>
											<th>VAT Amount</th>
											<th>SD Amount</th>
											<th>Value</th>
											<th>Quantity</th>
											<th>VAT Amount</th>
											<th>Adjustable SD Amount</th>
										</tr>
										<?php 
											$i=1;
											foreach($exiting_data as $val){	 
										?>
										<tr>
											<td><?=$i;?></td>
											<td><?=$val['Memo']['memo_no'];?></td>
											<td><?=$val['Memo']['memo_date'];?></td>
											<td><?=$val['CreditNoteDetail']['reason'];?></td>
											<td><?=$val['Product']['name'];?></td>
											<td><?=sprintf("%01.2f", $val['0']['value']);?></td>
											<td><?=$val['MemoDetail']['sales_qty'];?></td>
											<td>
											<?php  
												$vat = $creditnotes->get_vat_by_product_id_memo_date_v2($val['MemoDetail']['product_id'], $val['Memo']['memo_date']); 
												//echo $vat;
												$sales = $val['0']['value'];
												$total_vat_value = $sales*($vat/100);
												echo sprintf("%01.2f", $total_vat_value);
											?>

											</td>
											<td></td>
											<td><?=sprintf("%01.2f", $val['0']['r_value']);?></td>
											<td><?=$val['CreditNoteDetail']['return_qty'];?></td>
											<td>
											<?php  
												$rsales = $val['0']['r_value'];
												$total_vat_value2 = $rsales*($vat/100);
												echo sprintf("%01.2f", $total_vat_value2);
											?>
											</td>
											
											<td></td>
										</tr>

										<?php $i++; } ?>
									</thead>


									<tbody>
										
									</tbody>
								</table>
							</div>
							
							<div style="clear:both">
									<p> Name of responsible person :</p>
									<p> Designation :</p>
									<p> Designation :</p>
									<p> Signature :</p>
									<p> Seal :</p>
							</div>

							<h5 style="text-align: center;"> Value except all kinds of tax </h5>

						</div>

					</div>

				</div>

             

            </div>
        </div>
    </div>
</div>

<style type="text/css" media="print">
@media print and (width: 21cm) and (height: 29.7cm) {
     @page {
        margin: 3cm;
     }
}

/* style sheet for "letter" printing */
@media print and (width: 8.5in) and (height: 11in) {
    @page {
        margin: 1in;
    }
}

/* A4 Landscape*/
@page {
    size: A4 landscape;
    margin: 10%;
}
</style>

<script>
    $(document).ready(function() {

		

        $("#download_xl").click(function(e) {

            e.preventDefault();

            var html = $("#xls_body").html();

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "credit_notes.xls";

            document.body.appendChild(a);

            a.click();

        });

    });

	function PrintElem(elem) {
		
			var mywindow = window.open('', 'PRINT', 'height=400,width=900');

			//mywindow.document.write('<html><head><title>' + document.title  + '</title>');

			mywindow.document.write(
				'\
			<html><head><title></title>\
			<style type="text/css" media="print"> @page { size: landscape; }</style>\
			');
			mywindow.document.write('</head><body >');

			//mywindow.document.write('<h1>' + document.title  + '</h1>');

			mywindow.document.write(document.getElementById(elem).innerHTML);
			mywindow.document.write('</body></html>');

			mywindow.document.close(); 
			// necessary for IE >= 10
			mywindow.focus(); 
			// necessary for IE >= 10

			mywindow.print();
			mywindw.close();  
			
			return true;

		}
</script>