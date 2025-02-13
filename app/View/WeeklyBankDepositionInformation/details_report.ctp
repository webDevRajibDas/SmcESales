<?php
App::import('Controller', 'WeeklyBankDepositionInformationController');
$WeeklyController = new WeeklyBankDepositionInformationController;					 
?>
<div style="float:left; width:100%; height:450px; overflow:scroll;">
<?php if($data){?>
<a class="btn btn-success pull-right" id="download_xl">Download XL</a>
<div id="content">
	<div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both;">
            <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
            <h3 style="margin:2px 0;">Weekly Bank Deposition Detail</h3>
            <p>
                Time Frame : <b><?=date('d M, Y', strtotime($date_from))?></b> to <b><?=date('d M, Y', strtotime($date_to))?></b>
            </p>
            <p"><?php if(!empty($region_id)) echo 'Region : '.$region_offices[$region_id]; ?> <?php if(!empty($office_id)) echo 'Area : '.$offices[$office_id]; ?></p>
        </div>	
	<table class="print-table table table-bordered table-responsive table-striped" style="width:100%" border="1px solid black" cellpadding="2px" cellspacing="0">
		<thead>
			<tr style="background:#c7c7c7; font-weight:bold;">
				<td>Terrytory</td>
				<td>Slip Date</td>
				<td>Slip No</td>
				<td>Instrument Type</td>
				<td style="text-align:right;">Amount</td>
				<td style="text-align:center;">Remarks</td>
			</tr>
		</thead>
		<tbody>
		
        
        
        <?php 
		//pr($data);
		$g_total = 0;
		foreach($data as $territory=> $rpt_data)
		{ 
			//ksort($rpt_data);
			$i=0;
			?>

			<?php 
			$total_deposit_amount = 0;
			foreach($rpt_data as $deposit_data)
			{ 
			$i++;
			$total_deposit_amount+= $deposit_data['deposit_amount'];
			?>
			<tr>
				<?php if($i==1){?><td rowspan="<?=count($rpt_data);?>"><?php  echo $territory;?></td><?php }?> 
				<td><?=$deposit_data['deposit_date']?></td>
				<td><?=$deposit_data['slip_no']?></td>
				<td><?=$deposit_data['type']==2?$instrument_type[$deposit_data['c_instrument_type']]:$instrument_type[$deposit_data['type']].(@$instrument_type[$deposit_data['instrument_type']]?' ('.$instrument_type[$deposit_data['instrument_type']].')':'')?></td>
				<td style="text-align:right;"><?=sprintf("%01.2f", $deposit_data['deposit_amount'])?></td>
				<td style="text-align:center;">
                <?php //if($deposit_data['type']!=1){ ?>
                	<?php
                    $branch_info = $WeeklyController->get_bank_branch_info($deposit_data['bank_branch_id']);
					echo $branch_info['Bank']['name'].', '.$branch_info['BankBranch']['name'];
					?>
                <?php //} ?>
                </td>
			</tr>
			<?php } ?>
            
			<tr style="font-weight:bold;">
				<td colspan="4" style="text-align:right;">Total : </td>
				<td style="text-align:right;"><?=sprintf("%01.2f", $total_deposit_amount)?></td>
				<td></td>
			</tr>

		<?php 
			$g_total+=$total_deposit_amount;
		}
		?>
		<tr style="background:#ccc; font-weight:bold;">
            <td colspan="4" style="text-align:right;">Grand Total : </td>
            <td style="text-align:right;"><?=sprintf("%01.2f", $g_total)?></td>
            <td></td>
		</tr>
        
        
		</tbody>
	</table>
	</div>
	<?php }?>
</div>
<script type="text/javascript">
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