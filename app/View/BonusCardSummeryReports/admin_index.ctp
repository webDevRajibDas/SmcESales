<?php //pr($bonus_cards);die; ?>
<style type="text/css">
	.border,.border td {
		border: 1px solid black;
		white-space: nowrap;
	}
    .table-hover > tbody > tr:hover{
        cursor: pointer;
        background-color: #0030ff4d;
    }
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Bonus Summery Report'); ?></h3>
			</div>
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('search', array('role' => 'form')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('class' => 'form-control','empty'=>'---- Select Office ----','required'=>true)); ?></td>
							<td width="50%"><?php //echo $this->Form->input('bonus_card_id', array('class' => 'form-control','empty'=>'---- Select Bonus Card ----','required'=>true)); ?></td>
						</tr>

                        <tr>
                            <td width="50%"><?php echo $this->Form->input('fiscal_year_id', array('id'=>'fiscal_year_id','class' => 'form-control','empty'=>'---- Select Fiscal Year ----','required'=>true)); ?></td>
                            <td width="50%"><?php echo $this->Form->input('bonus_card_id', array('id'=>'bonus_card_id','class' => 'form-control','empty'=>'---- Select Bonus Card ----','required'=>true)); ?></td>
                        </tr>

						<tr>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>true)); ?></td>
							<td width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>true)); ?></td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<?php if(!empty($result)){?>
								<a class="btn btn-success" id="download_xl">Download XL</a>
                            <!-- <button type="button" onclick="PrintElem('content')" class="btn btn-primary">
                                <i class="glyphicon glyphicon-print"></i> Print
                            </button> -->
                            <?php }?> 
                        </td>						
                    </tr>
                </table>
                <?php echo $this->Form->end(); ?>
                <?php if(!empty($result)){?>
                <div class="row" >
                	<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;">
                		<div style="width:100%;">
                			<div style="width:25%;text-align:left;float:left">
                				&nbsp;&nbsp;&nbsp;&nbsp;
                			</div>
                			<div style="width:50%;text-align:center;float:left">
                				<font id="heading_name"><b>SMC Enterprise Limited</b></font><br>
                				<span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
                				<font><b>Bonus Summery Report (<?php echo h($bonusCards[$this->request->data['search']['bonus_card_id']]);?>)</b></font><br>
                				<font><b>Area Office : <?php echo h($offices[$this->request->data['search']['office_id']]); ?></b></font><br>
                				<font><?php if(!empty($this->request->data)){ ?><strong>Between:</strong>&nbsp;&nbsp;<u><?php  echo date('d-F-Y',strtotime($this->request->data['search']['date_from'])); ?>&nbsp;&nbsp;to&nbsp;&nbsp;<?php echo date('d-F-Y',strtotime($this->request->data['search']['date_to'])); }?></u>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Reporting Date:</strong>&nbsp;&nbsp;<?php echo date('d-F-Y');?></font>
                			</div>
                			<div style="width:25%;text-align:right;float:left">
                				&nbsp;&nbsp;&nbsp;&nbsp;
                			</div>        
                		</div>
                		<table class="table border table-hover" style="font-size:12px;" border="1">
                			<thead>
                				<tr>
                					<td class="text-center">Sales Officer</td>
                                    <td class="text-center">Product</td>
                					<td class="text-center">Outlet Type</td>
                					<td class="text-center">No. of Cust</td>
                					<td class="text-center">No. of Inv</td>
                					<td class="text-center">Quantity</td>
                					<td class="text-center">Value</td>
                					<td class="text-center">Inv Bonus</td>
                				</tr>

                			</thead>
                			<tbody>
                				<?php 
                				$from_date=date('Y-m-d',strtotime($this->request->data['search']['date_from']));
                				$to_date = date('Y-m-d',strtotime($this->request->data['search']['date_to']));
                				$bonus_card = $this->request->data['search']['bonus_card_id'];
                				$office_id = $this->request->data['search']['office_id'];
                				?>
                				<?php foreach($result as $data){?>
                				<tr class="view_data" data-href="<?=BASE_URL;?>/bonus_card_summery_reports/detail_bonus_card_report/<?=$data['territory_id'].'/'.$bonus_card.'/'.$from_date.'/'.$to_date.'/'.$office_id.'/0/'.$data['outlet_type_id']?>">
                					<td class="text-center"><?=$data['sales_person']?></td>
                                    <td class="text-center"><?=$data['product']?></td>
                					<td class="text-center"><?=$data['outlet_type']?></td>
                					<td class="text-center"><?=$data['outlet']?></td>
                					<td class="text-center" style="mso-number-format:\@;"><?=$data['memo']?></td>
                					<td class="text-center"><?=$data['qty']?></td>
                					<td class="text-center"><?=$data['value']?></td>
                					<td class="text-center"><?=$data['stamp']?></td>
                				</tr>
                				<?php }?>

                                <tr class="view_data" data-href="<?=BASE_URL;?>/bonus_card_summery_reports/detail_bonus_card_report/<?='0/'.$bonus_card.'/'.$from_date.'/'.$to_date.'/'.$office_id.'/1/0'?>">
                                    <td class="text-center" colspan="5" ><b>Total Non-Pharma</b></td>
                                    <td class="text-center"><?=$total_qty['non_pharma']?></td>
                                    <td class="text-center"><?=$total_value['non_pharma']?></td>
                                    <td class="text-center"><?=$total_stamp['non_pharma']?></td>
                                </tr>

                                <tr class="view_data" data-href="<?=BASE_URL;?>/bonus_card_summery_reports/detail_bonus_card_report/<?='0/'.$bonus_card.'/'.$from_date.'/'.$to_date.'/'.$office_id.'/1/1'?>">
                                    <td class="text-center" colspan="5" ><b>Total Pharma</b></td>
                                    <td class="text-center"><?=$total_qty['pharma']?></td>
                                    <td class="text-center"><?=$total_value['pharma']?></td>
                                    <td class="text-center"><?=$total_stamp['pharma']?></td>
                                </tr>
                                <tr class="view_data" data-href="<?=BASE_URL;?>/bonus_card_summery_reports/detail_bonus_card_report/<?='0/'.$bonus_card.'/'.$from_date.'/'.$to_date.'/'.$office_id.'/1'?>">
                                    <td class="text-center" colspan="5" ><b>Total</b></td>
                                    <td class="text-center"><?=$total_qty['non_pharma']+$total_qty['pharma']?></td>
                                    <td class="text-center"><?=$total_value['non_pharma']+$total_value['pharma']?></td>
                                    <td class="text-center"><?=$total_stamp['non_pharma']+$total_stamp['pharma']?></td>
                                </tr>
                			</tbody>
                		</table>
                		<div style="width:100%;padding-top:100px;">
                			<footer style="width:100%;text-align:center;">
                				"This Report has been generated from SMC Automated Sales System at <?php echo h($offices[$this->request->data['search']['office_id']]); ?> Area. This information is confidential and for internal use only."
                			</footer>	  
                		</div>
                	</div>
                </div>

                <?php }?>
            </div>
        </div>
    </div>
</div>
</div>
<script>
	function PrintElem(elem)
	{

		$("#table_content").html($("#report_content").html());
		var mywindow = window.open('', 'PRINT', 'height=600,width=960');

		mywindow.document.write('<html><head><title></title><?php echo $this->Html->css('bootstrap.min.css');
			echo $this->fetch('css');?>');
		mywindow.document.write('</head><body >');
		mywindow.document.write(document.getElementById(elem).innerHTML);
		mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
    $(document).ready(function(){
        $("#fiscal_year_id").change(function(){
            $.post('<?=BASE_URL.'bonus_card_summery_reports/get_bonus_card'?>',{'fiscal_year_id':$(this).val()}, function(data,status){
                $("#bonus_card_id").html(data);
            });
        });
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
    $('.view_data').click(function(){
    	var url = $(this).data('href');
                // window.open(url, 'details', 'titlebar=no, status=no, menubar=no, resizable=yes, scrollbars=yes, toolbar=no,location=no, height=1000, width=1000, top=50, left=50');
                window.open(url, '_blank');
            });
        </script>