<?php
//App::import('Controller', 'SalesReportsController');
//$SalesReportsController = new SalesReportsController;

//office_list


?>


<style>
    .search .radio label {
        width: auto;
        float: none;
        padding: 0px 15px 0px 5px;
        margin: 0px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 15%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
    }

    .pro_label_title {
        width: 92%;
        margin-right: 3%;
    }

    .left_align {
        text-align: left;
       
    }

   

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 33%;
        float: left;
        margin: 1px 0;
    }

    td, th {
        padding: 2px 5px !important;
    }

    td.left {
        border-right: #c7c7c7 solid 1px;
    }

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }
	
	#sum_table {
		padding:10px;
	}
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title; ?></h3>
            </div>

            <div class="box-body">
                <div class="search-box">

                    <?php echo $this->Form->create('Memo', array('role' => 'form')); ?>

                    <table class="search">
                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                            <td class="required">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('product_id', array('label' => 'Product', 'class' => 'form-control product_id', 'options' => $product_list)); ?>
                            </td>
                            <td>
                                <div style="margin-left: 95px;">


                                    <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
                                </div>
                            </td>

                        </tr>
						
						<tr>
                            
                            <td>
                                <div style="margin-left: 95px;">
									<?php echo $this->Form->input('indicator', array('legend' => 'Indicators :', 'class' => 'indicator', 'type' => 'radio', 'default' => '1', 'options' => $indicator, 'required' => true));  ?>
                                </div>
                            </td>

                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>


            </div>

            <?php if ($dataresult == 1) { ?>
                <h2 style="text-align: center;">No Data Found.</h2>
            <?php } else { ?>

                <?php
                if (!empty($request_data)) {
                ?>
                    <div id="content" style="width:90%; margin: 0 5%;">
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
                                width: 90px;
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
                                overflow-y: hidden;
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

                                <?php echo $this->Html->link(__('Download XLS'), array('action' => '#'), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                            </div>

                            <div id="xls_body">

								<div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">

									<?php
									$source = $productInfo['source'];
									if ($source == 'SMCEL') {
										$cname = "SMC Enterprise Limited";
									} else {
										$cname = "SOCIAL MARKETING COMPANY";
									}

									?>

									<h2 style="margin:2px 0;"><?= $cname; ?></h2>
									<h3 style="margin:2px 0;"> Program Officer Sales Performance Report</h3>
									<b> Time Frame : <?= @date('d M, Y', strtotime($this->request->data['Memo']['date_from'])) ?> to <?= @date('d M, Y', strtotime($this->request->data['Memo']['date_to'])) ?></b>
									<!--h5 style="margin:2px 0;">The Report is Generated Using eSales Software of SMC</h5-->

								</div>

								<div style="float:left; width:100%; ">
									<table id="sum_table" style="width: 100%; margin: 0;" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

										<tr class="titlerow" height="35">
											<th style="text-align:left;">
												<div>Area Office</div>
											</th>
											<th>
												<div>Program Officer</div>
											</th>

											<th>
												<div>%</div>
											</th>
											<th>
												<div>Natundin Sales</div>
											</th>
											<th>
												<div>%</div>
											</th>
											<th>
												<div>Other Sales (SO Sales)</div>
											</th>
											<th>
												<div>%</div>
											</th>
											<th>
												<div>Total</div>
											</th>
										</tr>
										<?php 
										
											$right_totoal = 0;
											
											$pof_total = 0;
											$pof_total_per = 0;
											
											$nat_total = 0;
											$nat_total_per = 0;
											
											$oth_total = 0;
											$oth_total_per = 0;
											
											//echo '<pre>';print_r($all_rs);exit;
											
											foreach($office_list as $key => $name){
												
												$other_so_sales = $all_rs[$key]; 
												$natondind_so_sales = $notondin_rs[$key]; 
												$program_so_sales = $program_rs[$key];
												
												if($this->request->data['Memo']['indicator'] == 1){
													
													$pof_total += $program_so_sales['value'];
													$nat_total += $natondind_so_sales['value'];
													$oth_total += $other_so_sales['value'];
													
													$pno_total = ( $program_so_sales['value'] + $natondind_so_sales['value'] + $other_so_sales['value'] ) ;
													$right_totoal +=$pno_total;
													
													$pof_persange = round((($program_so_sales['value'] / $pno_total) * 100),2);
													$pof_total_per +=$pof_persange;
													
													$nat_persange = round((($natondind_so_sales['value'] / $pno_total) * 100),2);
													$nat_total_per += $nat_persange;
													
													$oth_persange = round((($other_so_sales['value'] / $pno_total) * 100),2);
													$oth_total_per += $oth_persange;
													
													
												}else{
												
													$pof_total += $program_so_sales['qty'];
													$nat_total += $natondind_so_sales['qty'];
													$oth_total += $other_so_sales['qty'];
													
													$pno_total = ( $program_so_sales['qty'] + $natondind_so_sales['qty'] + $other_so_sales['qty'] ) ;
													$right_totoal +=$pno_total;
													
													$pof_persange = round((($program_so_sales['qty'] / $pno_total) * 100),2);
													$pof_total_per +=$pof_persange;
													
													$nat_persange = round((($natondind_so_sales['qty'] / $pno_total) * 100),2);
													$nat_total_per += $nat_persange;
													
													$oth_persange = round((($other_so_sales['qty'] / $pno_total) * 100),2);
													$oth_total_per += $oth_persange;
												
												}
										
										?>
											<?php if($this->request->data['Memo']['indicator'] == 1){ ?>
											<tr>
												<td align="left" style="text-align: left;"><?=$name;?></td>
												<td style="text-align: right;"><?=round($program_so_sales['value'],2);?></td>
												<td style="text-align: right;"><?= $pof_persange ;?></td>
												
												<td style="text-align: right;"><?=round($natondind_so_sales['value'],2);?></td>
												<td style="text-align: right;"><?= $nat_persange ;?></td>
												
												<td style="text-align: right;"><?=round($other_so_sales['value'],2);?></td>
												<td style="text-align: right;"><?= $oth_persange ;?></td>
												
												<td style="text-align: right;"><?=round($pno_total,2);?></td>
												
											</tr>
											
											<?php }else{ ?>
											<tr>
												<td style="text-align: left;"><?=$name;?></td>
												<td style="text-align: right;"><?=$program_so_sales['qty'];?></td>
												<td style="text-align: right;"><?= $pof_persange ;?></td>
												
												<td style="text-align: right;"><?=$natondind_so_sales['qty'];?></td>
												<td style="text-align: right;"><?= $nat_persange ;?></td>
												
												<td style="text-align: right;"><?=$other_so_sales['qty'];?></td>
												<td style="text-align: right;"><?= $oth_persange ;?></td>
												
												<td style="text-align: right;"><?=$pno_total;?></td>
												
											</tr>
											<?php } ?>
											
										<?php } ?>
											
											
											<tr>
												<th>Total</th>
												<td style="text-align: right;"><?= $pof_total ;?></td>
												<td style="text-align: right;"><?= round((($pof_total/$right_totoal)*100),2) ;?></td>
												<td style="text-align: right;"><?= $nat_total ;?></td>
												<td style="text-align: right;"><?= round((($nat_total/$right_totoal)*100),2) ;?></td>
												<td style="text-align: right;"><?= $oth_total ;?></td>
												<td style="text-align: right;"><?= round((($oth_total/$right_totoal)*100),2) ;?></td>
												<td style="text-align: right;"><?= $right_totoal ;?></td>
											</tr>
											
											
									</table>
								</div>
                            </div>
                        </div>
                    </div>
					<Br><br><br><br>
            <?php }
            } ?>
        </div>
    </div>
</div>

<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

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

<script>
    $(document).ready(function() {
		
		$('input').iCheck('destroy');

        $("#download_xl").click(function(e) {

            e.preventDefault();

            $(".downloadborder").attr("border", "1");

            var html = $("#xls_body").html();

            // console.log(html);

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });


            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            console.log(a);

            a.href = downloadUrl;

            a.download = "<?= $productInfo['name'] . '_report'; ?>.xls";

            document.body.appendChild(a);

            a.click();

            $(".downloadborder").removeAttr("border");

        });

    });
</script>