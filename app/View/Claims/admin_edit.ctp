<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Claim Details'); ?></h3>
				<div class="box-tools pull-right">

					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Claim  List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>

				</div>
			</div>
			<div class="box-body">
				<table class="table table-bordered table-striped">
					<tbody>
					<tr>
						<td width="25%"><strong><?php echo 'Claim No.'; ?></strong></td>
						<td><?php echo h($claim['Claim']['claim_no']); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo 'Transaction Type'; ?></strong></td>
						<td><?php echo h($claim['TransactionType']['name']); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo 'Sender Store'; ?></strong></td>
						<td><?php echo h($claim['SenderStore']['name']); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo 'Claim Date'; ?></strong></td>
						<td><?php echo $this->App->dateformat($claim['Claim']['created_at']); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo 'Receiver Store'; ?></strong></td>
						<td><?php echo h($claim['ReceiverStore']['name']); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo 'Received Date'; ?></strong></td>
						<td><?php echo $this->App->dateformat(($claim['Claim']['received_date']));?></td>
					</tr>
					<tr>
						<td><strong><?php echo 'Status'; ?></strong></td>
						<td><?php echo $claim['Claim']['status'] == 1 ? '<span class="btn btn-warning btn-xs">Pending</span>' : '<span class="btn btn-success btn-xs">Received</span>'; ?></td>
					</tr>
					<tr>
						<td><strong><?php echo 'Challan No'; ?></strong></td>
						<td><?php echo h($claim['Claim']['challan_id']); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo 'Remarks'; ?></strong></td>
						<td><?php echo h($claim['Claim']['remarks']); ?></td>
					</tr>
				</table>
			</div>

			<div class="box-body">
				<table class="table table-striped table-bordered">
					<thead>
					<tr>
						<th>Product Name</th>
						<th class="text-center">Batch No.</th>
						<th class="text-center">Expire Date</th>
						<th class="text-center">Claim Type</th>
						<th class="text-center">Challan Quantity</th>
						<th class="text-center">Claim Quantity</th>
						<th class="text-center">Action</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>
							<?php echo $this->Form->input('product_id', array('id' => 'product_id','label'=>false, 'class' => 'full_width form-control product_id chosen','empty'=>'---- Select Product ----')); ?>
						</td>
						<td width="15%" align="center">
							<?php echo $this->Form->input('batch_no', array('id' => 'batch_no','label'=>false,'type'=>'select','class' => 'full_width form-control batch_no','empty'=>'---- Select Batch ----')); ?>
						</td>
						<td width="15%" align="center">
							<?php echo $this->Form->input('expire_date', array('id' => 'expire_date','label'=>false,'type'=>'select','class' => 'full_width form-control expire_date','empty'=>'---- Select Expire Date ----')); ?>
						</td>
						<td width="20%">
							<?php echo $this->Form->input('claim_type', array('id' => 'claim_type','label'=>false, 'type'=>'select','class' => 'full_width form-control ','options'=>$claimType)); ?>
						</td>
						<td width="15%" align="center">
							<span class="challan_qty"></span>
							<?php echo $this->Form->input('challan_qty', array('id' => 'challan_qty','type'=>'hidden','class' => 'challan_qty')); ?>
						</td>
						<td width="12%" align="center">
							<?php echo $this->Form->input('claim_qty', array('label'=>false, 'class' => 'full_width form-control quantity')); ?>
						</td>
						<td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>
					</tr>
					</tbody>
				</table>
				<?php echo $this->Form->create('Claim', array('role' => 'form')); ?>
				<?php echo $this->Form->input('challan_id', array('id'=>'challan_id','type'=>'hidden','class' => 'challan_id','value'=>$claim['Claim']['challan_id'])); ?>
				<?php echo $this->Form->input('claim_id', array('id'=>'claim_id','type'=>'hidden','class' => 'claim_id','value'=>$claim['Claim']['id'])); ?>
				<table class="table table-bordered invoice_table">
					<tbody>
					<tr>
						<th class="text-center">SL.</th>
						<th class="text-center">Product Name</th>
						<th class="text-center">Batch No.</th>
						<th class="text-center">Expire Date</th>
						<th class="text-center">Claim Type</th>
						<th class="text-center">Quantity</th>
						<th class="text-center">Action</th>

					</tr>
					<?php
						if(!empty($claimdetail))
						{
							$sl = 1;
							$total_quantity = 0;
							$total_received_quantity = 0;
							$rowCount = 1;
							foreach($claimdetail as $val){
						?>
					<tr class="table_row" id="rowCount<?=$rowCount?>">
						<td align="center"><?php echo $sl; ?></td>
						<td><?php echo $val['Product']['name'];?></td>
						<td align="center"><?php echo $val['ClaimDetail']['batch_no']; ?></td>
						<td align="center"><?php echo $this->App->dateformat($val['ClaimDetail']['expire_date']); ?></td>
						<?php if($claim['Claim']['status'] == 1 AND $office_paren_id !=0){ ?>
							<?php echo $this->Form->input('product_id', array('id' => 'product_id','type'=>'hidden','name'=>'product_id[]','label'=>false, 'class' => 'full_width form-control product_id chosen','default'=>$val['ClaimDetail']['product_id']));?>
							<?php echo $this->Form->input('batch_no', array('id' => 'batch_no','type'=>'hidden','name'=>'batch_no[]','label'=>false,'class' => 'full_width form-control batch_no','default'=>$val['ClaimDetail']['batch_no'])); ?>
							<?php echo $this->Form->input('expire_date', array('id' => 'expire_date','type'=>'hidden','name'=>'expire_date[]','label'=>false,'class' => 'full_width form-control expire_date','default'=>$val['ClaimDetail']['expire_date'])); ?>
								<!--<td width="15%" align="center"><span class="challan_qty"></span><?php echo $this->Form->input('challan_qty', array('id' => 'challan_qty','type'=>'hidden','class' => 'challan_qty','default'=>$val['ClaimDetail']['product_id'])); ?></td>-->
							<td align="center"><?php echo $this->Form->input('claim_type', array('id' => 'claim_type','name'=>'claim_type[]','align'=>'right','label'=>false,'class' => 'full_width form-control ','options'=>$claimType,'default'=>$val['ClaimDetail']['claim_type'])); ?></td>
							<td align="center"><?php echo $this->Form->input('claim_qty', array('id' => 'claim_qty','name'=>'claim_qty[]','label'=>false,'align'=>'right','class' => 'full_width form-control quantity','default'=>$val['ClaimDetail']['claim_qty'])); ?></td>
							<td align="center"><button class="btn btn-danger btn-xs remove" type='button' value="<?=$rowCount?>"><i class="fa fa-times"></i></button></td>

						<?php $rowCount++; } else{?>
							<td align="center"><?php echo $val['ClaimDetail']['claim_type']; ?></td>
							<td align="center"><?php echo $val['ClaimDetail']['claim_qty']; ?></td>


						<?php }?>
					</tr>
					<?php
							$total_quantity = $total_quantity + $val['ClaimDetail']['claim_qty'];
							
							$sl++;
							}							
						}
						?>

					</tbody>
					<tfoot>
					<!--<tr>
						<td align="right" colspan="5"><strong>Total Quantity :</strong></td>
						<td align="left" class="total_quantity"><?php echo $total_quantity; ?></td>
						<td></td>

					</tr>-->
					</tfoot>
				</table>

				<input id="rowCount" hidden value="<?=$rowCount?>"></input>
			</div>
			<?php
			if($claim['Claim']['status'] == 1 AND $office_paren_id !=0){
			?>
			<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary save')); ?>
			<?php
			}
			?>
			<?php echo $this->Form->end(); ?>
			</br>
		</div>
	</div>
</div>
<?php
$startDate = date('d-m-Y',strtotime($claim['Claim']['created_at']));
$endDate = date('d-m-Y');
?>
<script>
	$(document).ready(function (){
		var is_maintain_batch = 1;
		var is_maintain_expire_date = 1;
		$('.datepicker_range').datepicker({
			startDate: '<?php echo $startDate; ?>',
			endDate: '<?php echo $endDate; ?>',
			format: "dd-mm-yyyy",
			autoclose: true,
			todayHighlight: true

		});
		/*$(".remove").click(function() {

			var removeNum = $(this).val();

			$('#rowCount'+removeNum).remove();
			var total_quantity = set_total_quantity();
			if(total_quantity<1)
			{
				$('.save').prop('disabled', true);
			}
		});*/

		$(document).on("click",".remove",function() {

			var removeNum = $(this).val();

			$('#rowCount'+removeNum).remove();
			var total_quantity = set_total_quantity();
			if(total_quantity<1)
			{
				$('.save').prop('disabled', true);
			}
		});
		$('#product_id').selectChain({
			target: $('#batch_no'),
			value:'title',
			url: '<?= BASE_URL.'claims/get_challan_wise_batch_list';?>',
				type: 'post',
				data:{'product_id':'product_id','challan_id':'challan_id'}
		});
		$('#batch_no').selectChain({
			target: $('#expire_date'),
			value:'title',
			url: '<?= BASE_URL.'claims/get_challan_wise_exp_date_list';?>',
				type: 'post',
				data:{'product_id': 'product_id','batch_no': 'batch_no','challan_id':'challan_id' }
		});

		$('#expire_date').change(function(){
		var challan_id = $('#challan_id').val();
		var product_id = $('.product_id').val();
		var batch_no = $('.batch_no').val();
		var expire_date = $('.expire_date').val();
		if(product_id == '')
		{
			alert('Please select any product.');
			return false;
		}else if(is_maintain_batch == 1 && batch_no == '')
		{
			alert('Please select any Batch.');
			return false;
		}else{

			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>claims/get_challan_wise_pro_qty_list',
				data: 'challan_id='+challan_id+'&product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date,
				cache: false,
				success: function(response){

					$('.challan_qty').html(response);
					$('#challan_qty').val(response);
				}
			});

		}
	});

	var rowCount = $('#rowCount').val();
	$(".add_more").click(function() {

		var product_id = $('.product_id').val();
		var quantity = $('.quantity').val();
		var challanQuantity = $('#challan_qty').val();
		var batch_no = $('.batch_no').val();
		var expire_date = $('.expire_date').val();
		var claim_type = $('#claim_type').val();
		//var selected_stock_id = $("input[name=selected_stock_id]").val();
		var selected_stock_array = $(".selected_product_id").map(function() {
			return $(this).val();
		}).get();
		var product_check_id = product_id + batch_no + expire_date;
		var stock_check = $.inArray(product_check_id,selected_stock_array) != -1;

		if(product_id=='')
		{
			alert('Please select any product.');
			return false;
		}else if(is_maintain_batch == 1 && batch_no == '')
		{
			alert('Please enter valid batch number.');
			return false;
		}else if(is_maintain_expire_date == 1 && expire_date == '')
		{
			alert('Please enter expire date.');
			return false;
		}else if(! quantity.match(/^\d+$/) || parseInt( quantity) < 0)
		{
			alert('Please enter valid quantity.');
			$('.quantity').val('');
			return false;
		}else if(parseInt( quantity) > parseInt(challanQuantity)){
			alert('Quantity should be less then equal Challan quantity.');
			$('.quantity').val('');
			return false;
		}else if(stock_check == true){
			alert('This product already added.');
			clear_field();
			return false;
		}
		else
		{
			rowCount;
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>admin/products/product_details',
				data: 'product_id=' + product_id,
				cache: false,
				success: function(response){
					var obj = jQuery.parseJSON(response);
					var recRow = '<tr class="table_row" id="rowCount'+rowCount+'"><td align="center">'+rowCount+'</td>' +
							'<td><input type="hidden" name="product_id[]" value="'+obj.Product.id+'">'+obj.Product.name+'</td>' +
							'<td align="center"><input type="hidden" name="batch_no[]" value="'+batch_no+'">'+batch_no+'</td>' +
							'<td align="center"><input type="hidden" name="expire_date[]" value="'+expire_date+'">'+expire_date+'</td>' +
							'<td align="center"><input type="hidden" name="claim_type[]" value="'+claim_type+'">'+claim_type+'</td>' +
							'<td align="left"><input type="hidden" name="claim_qty[]" class="quantity" value="'+quantity+'">'+quantity+'</td>' +
							'<td align="center"><button class="btn btn-danger btn-xs remove" value="'+rowCount+'"><i class="fa fa-times"></i></button></td>' +
							'</tr>';

					$('.invoice_table').append(recRow);

					//clear_field();
					var total_quantity = set_total_quantity();

					if(total_quantity>0)
					{
						$('.save').prop('disabled', false);
					}



				}
			});

		}
	});
		$(document).on("blur",".quantity",function() {
		set_total_quantity()
		});

		function set_total_quantity() {
			var sum = 0;
			var num = 1;
			$('.table_row').each(function() {
				var table_row = $(this);

				var total_quantity = table_row.closest('tr').find('.quantity').val();
				sum += parseInt(total_quantity);
				$(this).find("td:first").html(num++);
			});

			$('.total_quantity').html(sum);
			return sum;
		}
/*
		function clear_field() {
		$('.product_id').val('');
		$('.quantity').val('');
		$('.batch_no').val('');
		$('.expire_date').val('');
		$('.claim_type').val('');
		$('.batch_no').attr('disabled',false);

		}*/

		});
</script>