<?php //pr($bonus_cards);die; ?>
<style type="text/css">
	.border,.border td {
		border: 1px solid black;
		white-space: nowrap;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Bonus Summery Report'); ?></h3>
			</div>
			<div class="box-body">
				<a class="btn btn-success pull-right" id="download_xl">Download XL</a>
				<?php //if(!empty($result)){?>

				<!-- ================= For Print Item ========================================= -->
				<div class="row" >
					<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;">
						<div style="width:100%;">
							<div style="width:25%;text-align:left;float:left">
								&nbsp;&nbsp;&nbsp;&nbsp;
							</div>
							<div style="width:50%;text-align:center;float:left">
								<font id="heading_name"><b>SMC Enterprise Limited</b></font><br>
								<span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
								<font><b>Bonus Summery Report (<?php echo h($bonusCards[$request_data['bonus_id']]);?>)</b></font><br>
								<font><b>Area Office : <?php echo h($offices[$request_data['office_id']]); ?></b></font><br>
								<font><?php if(!empty($request_data)){ ?><strong>Between:</strong>&nbsp;&nbsp;<u><?php  echo date('d-F-Y',strtotime($request_data['date_from'])); ?>&nbsp;&nbsp;to&nbsp;&nbsp;<?php echo date('d-F-Y',strtotime($request_data['date_to'])); }?></u>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Reporting Date:</strong>&nbsp;&nbsp;<?php echo date('d-F-Y');?></font>
							</div>
							<div style="width:25%;text-align:right;float:left">
								&nbsp;&nbsp;&nbsp;&nbsp;
							</div>        
						</div> 
						<div id="table_content" style="float:left; width:100%; height:450px; overflow:scroll;">
							<table class="table  table-striped border" style="font-size:12px;" border="1">
								<thead>
									<tr>
										<td class="text-center">Product</td>
										<td class="text-center">Customer</td>
										<td class="text-center">Inv Date</td>
										<td class="text-center">Inv no</td>
										<td class="text-center">Quantity</td>
										<td class="text-center">Value</td>
										<td class="text-center">Inv Bonus</td>
									</tr>
									
								</thead>
								<tbody>
									
									<?php $last_so=''; foreach($data_array as $data){ ?>
										<?php foreach($data[$data['outlet_id']] as $row){ ?>
											<?php if($last_so!=$row['sales_people_id']) { $last_so=$row['sales_people_id'];?>
											<tr>
												<td colspan="7"><b>Sales Officer : <?=$row['sales_people_name']?></b></td>
											</tr>
											<?php }?>
											<tr>
												<td><?=$row['product']?$row['product']:'';?></td>
												<td><?=$row['outlet']?$row['outlet']:'';?></td>
												<td><?=date('d M Y',strtotime($row['inv_date']))?></td>
												<td style="mso-number-format:\@;"><?=$row['inv_no']?></td>
												<td><?=$row['qty']?></td>
												<td><?=$row['value']?></td>
												<td><?=$row['stamp']?> Stamp</td>
											</tr>
										<?php } ?>
										<tr>
											<td colspan="4" class="text-right"><b>Sub Total</b></td>
											<td><b><?=$data['total_qty']?></b></td>
											<td><b><?=$data['total_value']?></b></td>
											<td><b><?=$data['total_stamp']?> Stamp</b></td>
										</tr>
									<?php }?>
								</tbody>
							</table>
						</div>
						<div style="width:100%;padding-top:100px;">
							<footer style="width:100%;text-align:center;">
								"This Report has been generated from SMC Automated Sales System at <?php echo h($offices[$request_data['office_id']]); ?> Area. This information is confidential and for internal use only."
							</footer>	  
						</div>
					</div>
				</div>

				<!-- ========================== print item END ============ -->
				<?php //}?>
			</div>
		</div>
	</div>
</div>
<script >
	$(document).ready(function(){
                        $("#download_xl").click(function(e){
                            e.preventDefault();
                            var html = $("#content").html();
                            // console.log(html);
                            var blob = new Blob([html], { type: 'data:application/vnd.ms-excel' }); 
                            var downloadUrl = URL.createObjectURL(blob);
                            var a = document.createElement("a");
                            a.href = downloadUrl;
                            a.download = "downloadFile.xls";
                            document.body.appendChild(a);
                            a.click();
                        });
                    });
</script>