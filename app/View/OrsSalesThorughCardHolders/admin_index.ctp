<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('ORS Sales Through Card Holder'); ?></h3>
				
			</div>	
			<div class="box-body" style="min-height: 750px;;">
				<div class="search-box">
					<form method="post" action="<?=BASE_URL;?>admin/ors_sales_thorugh_card_holders">
					<?php // echo $this->Form->create('SalesAnalysisReports', array('role' => 'form','action'=>'index')); ?>
					<table class="search">
						<tr>
							<td><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>false)); ?></td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>false)); ?>
							</td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
				<?php

                if (!empty($outlet_value)) { ?>

                    <div id="content" style="width:96%; margin:0 2%; float:left;">

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

                            .titlerow th div {
                                text-transform: capitalize;
                                min-width: 100px;
                                float: left;
                                position: relative;
                            }

                            .titlerow th {
                                text-align: center;
                            }
                        </style>


                        <div class="table-responsive">

                            <div class="pull-right csv_btn" style="padding-top:20px;">
                                
                                <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                            </div>

                            <div id="xls_body">
                                <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both;">
                                    <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
                                    <h3 style="margin:2px 0;">ORS Sales Through Card Holder</h3>
                                    <p>
                                        Time Frame : <b><?= date('d M, Y', strtotime($date_from)) ?></b> to <b><?= date('d M, Y', strtotime($date_to)) ?></b>
                                    </p>
                                   
                                </div>

                                <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                    <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
										<tr>
											<th colspan="11">
												<h3>ORS Sales Through Card Holder</h3>
											</th>
										</tr>
										<tr>
											<th rowspan="2" width="10%"> Card Type </th>
											<th colspan="5"> ORS Card Holder Sales By (DS) </th>
											<th colspan="5"> ORS Card Holder Sales By (Db) </th>
											<!--th rowspan="2" width="10%"> % of National Revenue </th-->
										</tr>
										<tr>
											<th>No. of Card Holder</th>
											<th>Quantity</th>
											<th>Value</th>
											<th>% of DS ORS Sales (Quantity)</th>
											<th>% of DS Revenue</th>
											<th>No. of Card Holder</th>
											<th>Quantity</th>
											<th>Value</th>
											<th>% of DB ORS Sales (Quantity)</th>
											<th>% of DB Revenue</th>
										</tr>
										<tr>
											<td> Retail Card </td>
											<td align="right"> <?=$outlet_value[0]['total_card_holder']?> </td>
											<td align="right"> <?=$outlet_value[0]['total_qty']?> </td>
											<td align="right"> <?=$outlet_value[0]['total_value']?> </td>
											<td align="right"> <?=$outlet_value[0]['ors_per']?>%</td>
											<td rowspan="2" align="center"> <?=$outlet_value[0]['ors_per']+ $outlet_value[1]['ors_per'];?>% </td>
											<td align="right"> <?=$outlet_value[2]['total_card_holder']?> </td>
											<td align="right"> <?=$outlet_value[2]['total_qty']?> </td>
											<td align="right"> <?=$outlet_value[2]['total_value']?> </td>
											<td align="right"><?=$outlet_value[2]['ors_per']?>%</td>
											<td rowspan="2" align="center"> <?=$outlet_value[2]['ors_per']+ $outlet_value[3]['ors_per'];?>% </td>
											<!--td rowspan="2" align="center"> 70% </td-->
										</tr>
										<tr>
											<td> Stockist  Card </td>
											<td align="right"> <?=$outlet_value[1]['total_card_holder']?> </td>
											<td align="right"> <?=$outlet_value[1]['total_qty']?> </td>
											<td align="right"> <?=$outlet_value[1]['total_value']?> </td>
											<td align="right"> <?=$outlet_value[1]['ors_per']?>%</td>
											
											<td align="right"> <?=$outlet_value[3]['total_card_holder']?> </td>
											<td align="right"> <?=$outlet_value[3]['total_qty']?> </td>
											<td align="right"> <?=$outlet_value[3]['total_value']?> </td>
											<td align="right"> <?=$outlet_value[3]['ors_per']?> </td>
											
										</tr>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                			
			</div>			
		</div>
	</div>
</div>

<script>
    $("#download_xl").click(function(e) {
        e.preventDefault();
        var html = $("#xls_body").html();

        var blob = new Blob([html], {
            type: 'data:application/vnd.ms-excel'
        });
        var downloadUrl = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = downloadUrl;
        a.download = "ors_sales_thorugh_card_holders.xls";
        document.body.appendChild(a);
        a.click();
    });
</script>