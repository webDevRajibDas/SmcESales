<?php
App::import('Controller', 'LpcReportsController');
$LpcReportsController = new LpcReportsController;
echo $this->Html->css('select2/select2');
?>


<style>
	.search .radio label {
		width: auto;
		float: none;
		padding: 0px 5% 0px 5px;
		margin: 0px;
	}

	.search .radio legend {
		float: left;
		margin: 5px 20px 0 0;
		text-align: right;
		width: 12.5%;
		display: inline-block;
		font-weight: 700;
		font-size: 14px;
		border-bottom: none;
	}

	#market_list .checkbox label {
		padding-left: 0px;
		width: auto;
	}

	#market_list .checkbox {
		width: 25%;
		float: left;
		margin: 1px 0;
	}

	body .td_rank_list .checkbox {
		width: auto !important;
		padding-left: 20px !important;
	}

	.radio input[type="radio"],
	.radio-inline input[type="radio"] {
		margin-left: 0px;
		position: relative;
		margin-top: 8px;
	}

	.search label {
		width: 25%;
	}

	#market_list {
		padding-top: 5px;
	}

	.market_list2 .checkbox {
		width: 15% !important;
	}

	.market_list3 .checkbox {
		width: 20% !important;
	}

	.box_area {
		display: none;
	}



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
	.search span{
		float:left;
		width: 100%;
		text-align:right;
	}
	.select2-container--bootstrap .select2-selection--single .select2-selection__placeholder{
		text-align:left;
	}
</style>

<div id="divLoading" class=""> </div>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title ?></h3>
			</div>
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('LpcReports', array('role' => 'form', 'action' => 'index')); ?>
					<table class="search">
						<tr>
							<td class="required" width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?></td>
							<td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?></td>
						</tr>
						<tr>
						<?php if ($office_parent_id == 0) { ?>
								<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>
								<td></td>
						<?php }?>
						</tr>
						<tr>
						<?php if ($office_parent_id == 14) { ?>
								<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'options' => $region_offices,)); ?></td>
								<td></td>
						<?php } ?>
						</tr>
						<tr>
							<td width="50%">
								<div class="input select">
									<label for="office_id">Area Office :</label>
									<select name="office_id" id="office_id" class="form-control select2auto" data-parent="0" data-child="[territory_id,so_id]" data-limit="30" data-route="GetOfficeId">
										<?php
										if($offices['id']){
											echo '<option value="'.$offices['id'].'" selected="selected">'.$offices['office_name'].'</option>';
										}
										?>
									</select>
								</div>
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td>
								<div class="input select">
									<label for="region_office_id">Territory :</label>
									<select name="territory_id" id="territory_id" class="form-control select2auto" data-parent="0" data-limit="30" data-route="GetTerritoryId">
										<?php
										if($territories['id']){
											echo '<option value="'.$territories['id'].'" selected="selected">'.$territories['name'].'</option>';
										}
										?>
									</select>
								</div>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>
								<div class="input select">
									<label for="region_office_id">Sales Officers :</label>
									<select name="so_id" id="so_id" class="form-control select2auto" data-parent="0" data-limit="30" data-route="GetSoId">
										<?php
										if($so_list['id']){
											echo '<option value="'.$so_list['id'].'" selected="selected">'.$so_list['name'].'</option>';
										}
										?>
									</select>
								</div>
								<?php //echo $this->Form->input('so_id', array('label' => 'Sales Officers', 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'options' => $so_list, 'empty' => '---- All ----')); ?>
							</td>
							<td></td>
						</tr>
						<tr>
							<td colspan="2">
								<?php echo $this->Form->input('type', array('legend' => 'Type :', 'class' => 'type', 'type' => 'radio', 'default' => 'so', 'onClick' => 'typeChange(this.value)', 'options' => $types ));  ?>
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
				<?php if (!empty($results)) { ?>

					<div id="content" style="width:90%; margin:0 5%;">

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
						</style>

						<div class="table-responsive">

							<div class="pull-right csv_btn" style="padding-top:20px;">
								<?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
							</div>

							<div id="xls_body">
								<div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
									<h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
									<h3 style="margin:2px 0;">Line Per Call Report</h3>
									<p>
										<b> Time Frame : <?= @date('d M, Y', strtotime($request_data['LpcReports']['date_from'])) ?> to <?= @date('d M, Y', strtotime($request_data['LpcReports']['date_to'])) ?></b>
									</p>
									<p>
										<?php if ($header_cap['region']) { ?>
											<span>Region Office: <?= $header_cap['region'] ?></span>
										<?php } else { ?>
											<span>Head Office</span>
										<?php }
										if (isset($header_cap['office']) && $header_cap['office']) { ?>
											<span>, Area Office: <?= $header_cap['office'] ?></span>
										<?php }
										if ($header_cap['territory']) { ?>
											<span>, Territory Name: <?= $header_cap['territory'] ?></span>
										<?php } ?>
									</p>
								</div>
								<div style="float:left; width:100%; height:430px; overflow:scroll;">
									<table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
										<tbody>
											<tr class="titlerow">
												<?php foreach ($columns as $column) { ?>
													<th><?= $column ?></th>
												<?php } ?>
											</tr>


											<?php
											$i=0;
											$colSpan = count($results[0]);
											foreach($results as $result){
												$result['lpc'] = round(($result['total_sales_qty']/$result['total_memos']),2);
												echo "<tr>";
												echo "<td>".++$i."</td>";
												foreach($result as $key=>$val){
													echo "<td>".$val."</td>";
												}
												echo "</tr>";
												$ttlLpc+=$result['lpc'];
											}
											?>
											<tr>
												<td colspan="<?=$colSpan+1?>">Total Average</td>
												<td><?=round(($ttlLpc/$i),2)?></td>
											</tr>
										</tbody>
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
<?php echo $this->Html->script('select2/js/select2.min'); ?>
<?php echo $this->Html->script('select2/select2'); ?>
<script>

	$('.region_office_id').change(function() {
		var region_office_id = $(this).val();
		//$('#territory_id').html('<option value="">---- All ----');
		$('#office_id').attr('data-parent',region_office_id);
	});

    $('#office_id').change(function() {
        //alert($(this).val());
        date_from = $('.date_from').val();
        date_to = $('.date_to').val();
        if (date_from && date_to) {
            return true;
        } else {
            if($(this).attr("data-parent")){
                alert('Please select date range!');
            }
        }
    });

    $(window).on('load', function() {
        $("#region_office_id").trigger('change');
        $("#office_id").trigger('change');
    });
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

        //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('<html><head><title></title><style>.csv_btn{display:none;}</style>');
        mywindow.document.write('</head><body>');
        //mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        //mywindow.close();

        return true;
    }

    $(document).ready(function() {
        $("#download_xl").click(function(e) {
            e.preventDefault();
            var html = $("#xls_body").html();
            // console.log(html);
            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });
            var downloadUrl = URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.href = downloadUrl;
            a.download = "lpc_reports.xls";
            document.body.appendChild(a);
            a.click();
        });

    });
</script>


