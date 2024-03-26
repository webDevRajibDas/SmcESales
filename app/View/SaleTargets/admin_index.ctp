<style>
	.sales{
		width:60%;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('National Sale Targets'); ?></h3>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-xs-12">
						<a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal">OR UPLOAD XCEL</a>

						<a class="btn btn-success btn-xs pull-right" id="dl_exel">Download EXCEL</a>
						
					</div>
					<div class="col-xs-12">
						<a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal_month" id="up_exel_month">UPLOAD XL Month wise</a>
						<a class="btn btn-success btn-xs pull-right" id="dl_exel_month">Download Month Wise XL Format</a>
					</div>
				</div>
				
				<?php echo $this->Form->create('SaleTarget', array('role' => 'form')); ?>
				<div class="form-group">
					<?php
					echo $this->Form->input('is_submit', array('name'=>'is_submit','value'=>'YES','type'=>'hidden'));
					echo $this->Form->input('fiscal_year_id', array('class' => 'form-control','name'=>'data[SaleTarget][fiscal_year_id]','empty'=>'---- Select ----','options'=>$fiscalYears,'default'=>$current_year_code)); ?>
				</div>
				<div id="tbodys">
                	<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<td style="width:90%; border-right:none; font-weight:bold;" class="text-right">
                                	Total National Amount : <span id="n_amount"></span>
                                </td>
                                <td style="width:10%; border-left:none;"></td>
							</tr>
						</thead>
                    </table>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center"><?php echo 'Product Code'?></th>
								<th class="text-center"><?php echo 'product Name' ?></th>
								<th class="text-center"><?php echo 'Unit' ?></th>
								<th class="text-center"><?php echo 'Qty'?></th>
								<th class="text-center"><?php echo 'Amount'?></th>
								<th class="text-center"><?php echo 'Action'?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($products as $product):

						//echo '<pre>';
						//print_r($product['SaleTarget']);
							?>
							<tr>
								<td class="text-left"><?php echo h($product['Product']['product_code']); ?></td>
								<td class="text-left"><?php echo h($product['Product']['name']); ?></td>
								<td class="text-left"><?php echo h($product['BaseMeasurementUnit']['name']); ?></td>
								<td class="text-left">
									<div class="form-group">
										<?php if(!empty($product['SaleTarget']))
										{
									
											echo $this->Form->input('quantity', array('class' => 'form-control sales','name'=>'data[SaleTarget][quantity]['.$product['Product']['id'].']','label'=>'','type'=>'text','value'=> number_format($product['SaleTarget']['quantity']),'step'=>'any'));
										}
										else
										{
											echo $this->Form->input('quantity', array('class' => 'form-control sales','name'=>'data[SaleTarget][quantity]['.$product['Product']['id'].']','label'=>'','value'=>0,'step'=>'any','type'=>'text'));
										}
										?>
									</div>
								</td>
								<td class="text-left">
									<div class="form-group">
										<?php
										if(!empty($product['SaleTarget']))
										{
											echo $this->Form->input('amount', array('class' => 'form-control sales t_amount','name'=>'data[SaleTarget][amount]['.$product['Product']['id'].']','label'=>'','value'=> number_format($product['SaleTarget']['amount']),'type'=>'text','step'=>'any','id'=>$product['Product']['id']));
										}else
										{
											echo $this->Form->input('amount', array('class' => 'form-control sales t_amount','name'=>'data[SaleTarget][amount]['.$product['Product']['id'].']','label'=>'','value'=>0,'step'=>'any','id'=>$product['Product']['id'],'type'=>'text'));
										}

										?>
									</div>
								</td>
								<td>
                                    <?php echo $this->Html->link('Set Monthly Target', array('action' =>''),array('class' => 'btn btn-primary btn-xs btn_month month_btn'.$product['Product']['id'], 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Monthly Target','disabled'=>'true'));  ?>
                            </td>
								<?php echo  $this->Form->input('sale_target_id', array('class' => 'form-control','type' => 'hidden','name'=>'data[SaleTarget][id]['.$product['Product']['id'].']','value'=>(isset($product['SaleTarget']['id']))?$product['SaleTarget']['id']:''));
										//echo  $this->Form->input('', array('class' => 'form-control','type' => 'text','name'=>'data[SaleTarget1][id1]['.$product['Product']['id'].']','value'=>'data[SaleTarget][id1]'));
								?>

							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary','style'=>'margin-top:10px;margin-left:250px;')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">XL uploader</h4>
			</div>
			<div class="modal-body">
				<form action="<?php echo $this->Html->url().'/upload_xl';?>" method="post" enctype="multipart/form-data">
					<input type="file" name="file" />
					<input type="hidden" name="test" value="3">
					<?php echo $this->Form->submit('UPLOAD', array('class' => 'btn btn-info btn-md','style'=>'')); ?>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>
  <div class="modal fade" id="myModal_month" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">XL uploader</h4>
        </div>
        <div class="modal-body">
			<form action="<?php echo $this->Html->url().'/upload_xl_month';?>" method="post" enctype="multipart/form-data">
			<input type="file" name="file" />
			<input type="hidden" name="test" value="3">
			<?php echo $this->Form->submit('UPLOAD', array('class' => 'btn btn-info btn-md','style'=>'')); ?>
			</form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
<script>
	$(document).ready(function() {
		if($("#SaleTargetFiscalYearId").val())
		{
			var FiscalYearId = $("#SaleTargetFiscalYearId").val();
			var url="<?php echo BASE_URL; ?>admin/SaleTargets/upload_xl_month/"+FiscalYearId;
			$("#myModal_month").find('form').prop('action',url);
			$('#dl_exel').show();
			$('#dl_exel_month').show();
			$('#up_exel_month').show();
		}
		else
		{
			$('#dl_exel').hide();
			$('#dl_exel_month').hide();
			$('#up_exel_month').hide();
		}
		$("#SaleTargetFiscalYearId").change(function() {
			var FiscalYearId = $(this).val();
			if(FiscalYearId)
			{
				var url="<?php echo BASE_URL; ?>admin/SaleTargets/upload_xl_month/"+FiscalYearId;
				$("#myModal_month").find('form').prop('action',url);
				$('#dl_exel').show();
				$('#dl_exel_month').show();
				$('#up_exel_month').show();
			}
			else
			{
				$('#dl_exel').hide();
				$('#dl_exel_month').hide();
				$('#up_exel_month').hide();
			}
			$.ajax({
				type: "POST",
				url:  "<?php echo BASE_URL; ?>admin/SaleTargets/get_national_sales_data",
				data: "FiscalYearId="+FiscalYearId,
				success: function(response){
					if(response =='[]')
					{
						$(".sales").each(function() {
							$(this).val('0');
						});

						$(".btn_month").each(function() {
							$(this).attr('href','');
							$(this).attr('disabled',true);
						});
						calculateSum();
						
					}
					else
					{
						response = jQuery.parseJSON(response);
						if(response.length != 'undefined')
							for (var i = 0; i < response.length; i++){
								$("input[name='data[SaleTarget][id]["+response[i].SaleTarget.product_id+"]']").val(response[i].SaleTarget.id);
								$("input[name='data[SaleTarget][quantity]["+response[i].SaleTarget.product_id+"]']").val((response[i].SaleTarget.quantity).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
								$("input[name='data[SaleTarget][amount]["+response[i].SaleTarget.product_id+"]']").val((response[i].SaleTarget.amount).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
								if(response[i].SaleTarget.quantity>0)
								{
									$(".month_btn"+response[i].SaleTarget.product_id).attr('href','<?php echo BASE_URL;?>admin/SaleTargets/set_monthly_target/'+response[i].SaleTarget.product_id+'/'+response[i].SaleTarget.id+'/'+$("#SaleTargetFiscalYearId").val());
									$(".month_btn"+response[i].SaleTarget.product_id).attr('disabled',false);
								}
								else
								{
									$(".month_btn"+response[i].SaleTarget.product_id).attr('href','');
									$(".month_btn"+response[i].SaleTarget.product_id).attr('disabled',true);
								}
								if((i+1)==response.length)
								{
									calculateSum();
								}
							}
						}
					}
				});
				
		});
		$('#dl_exel').click(function(){
			var fiscalYearid=$("#SaleTargetFiscalYearId").val();
			window.open("<?=BASE_URL;?>SaleTargets/download_xl/"+fiscalYearid);
		});
		$('#dl_exel_month').click(function(){
            var fiscalYearid=$("#SaleTargetFiscalYearId").val();
            window.open("<?=BASE_URL;?>SaleTargets/download_xl_month/"+fiscalYearid);
        });
	});
</script>

<script>
function numberWithCommas(number) {
    var parts = number.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}
function calculateSum() {
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".t_amount").each(function() {
	
		var value = $(this).val().replace(/,/g , '');
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	var commaNum = numberWithCommas(sum);
	$('#n_amount').text(commaNum);
};

$(document).ready(function () {
	
	//alert(calculateSum());
	calculateSum();
	
	$(".t_amount").keyup(function(){
        calculateSum();
    });
   
});
</script>

