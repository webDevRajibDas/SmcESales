<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Admin Add Claim'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Claim List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Claim', array('role' => 'form')); ?>
				<div class="box-body">
					<?php echo $this->Form->create('Claim', array('role' => 'form')); ?>
					<div class="form-group">
						<?php echo $this->Form->input('receiver_store_id', array('class' => 'form-control','empty'=>'---- Select Receiver ----','options'=>$receiverStore,'required'=>true)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('challan_id', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('remarks', array('class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('inventory_status_id', array('id'=>'inventory_status_id','class' => 'form-control','type'=>'hidden','value'=>1)); ?>
					</div>

				<table class="table table-striped table-bordered">
					<thead>
					<tr>
						<th>Product Name</th>
						<!--<th>Product Code</th>-->
						<th class="text-center">Batch No.</th>
						<th class="text-center">Expire Date</th>
						<th class="text-center">Claim Type</th>
						<th class="text-center">Claim Quantity</th>
						<th class="text-center">Action</th>
					</tr>
					</thead>
					<tbody>
					<tr>

						<td width="20%">
							<?php echo $this->Form->input('product_id', array('id' => 'product_id','label'=>false, 'class' => 'full_width form-control product_id chosen','empty'=>'---- Select Product ----','value'=>$products)); ?>
						</td>

						<td width="12%" align="center">
							<?php echo $this->Form->input('batch_no', array('label'=>false, 'class' => 'full_width form-control quantity')); ?>
						</td>
						<td width="20%">
							<?php echo $this->Form->input('challan_date', array('label'=>false,'class' => 'full_width form-control datepicker','required'=>true)); ?>
						</td>
						<td width="20%">
							<?php echo $this->Form->input('claim_type', array('id' => 'claim_type','label'=>false, 'type'=>'select','class' => 'full_width form-control ','options'=>$claimType)); ?>
						</td>


						<td width="12%" align="center">
							<?php echo $this->Form->input('claim_qty', array('label'=>false, 'class' => 'full_width form-control quantity')); ?>
						</td>


						<td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>
					</tr>
					</tbody>
				</table>
				<table class="table table-striped table-condensed table-bordered invoice_table">
					<thead>
					<tr>
						<th>Product Name</th>
						<th>Product Code</th>
						<th class="text-center">Batch No.</th>
						<th class="text-center">Expire Date</th>
						<th class="text-center">Claim Quantity</th>
						<th class="text-center">Action</th>
					</tr>
					</thead>
				</table>
				</br>
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>

<script>
	$(document).ready(function(){

		var is_maintain_batch = 1;
		var is_maintain_expire_date = 1;
		/*$(".chosen").chosen().change(function(){
			var product_id = $(this).val();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>admin/products/product_details',
				data: 'product_id=' + product_id,
				cache: false,
				success: function(response){
					var obj = jQuery.parseJSON(response);

					if(obj.Product.maintain_batch == 0 && obj.Product.is_maintain_expire_date == 0){
						is_maintain_batch = 0;
						is_maintain_expire_date = 0;
						$.ajax({
							type: "POST",
							url: '<?php echo BASE_URL;?>current_inventories/get_inventory_details',
							data: 'product_id='+product_id+'&batch_no='+''+'&expire_date='+'',
							cache: false,
							success: function(response1){
								$('.qty').val(response1);
								$('.product_qty').html(response1);
							}
						});
						$('.batch_no').val('');
						$('.batch_no').attr('disabled',true);
						$('.expire_date').val('');
						$('.expire_date').attr('disabled',true);
					}else if(obj.Product.maintain_batch == 1 && obj.Product.is_maintain_expire_date == 0){
						maintain_batch = 1;
						is_maintain_expire_date = 0;
						$('.batch_no').val('');
						$('.batch_no').attr('disabled',false);
						$('.expire_date').val('');
						$('.expire_date').attr('disabled',true);
						$('.qty').val('');
						$('.product_qty').html('');
					}else if(obj.Product.maintain_batch == 0 && obj.Product.is_maintain_expire_date == 1){
						is_maintain_batch = 0;
						is_maintain_expire_date = 1;
						$('.batch_no').val('');
						$('.batch_no').attr('disabled',true);
						$('.expire_date').val('');
						$('.expire_date').attr('disabled',false);
						$('.qty').val('');
						$('.product_qty').html('');
					}else{
						is_maintain_batch = 1;
						is_maintain_expire_date = 1;
						$('.batch_no').val('');
						$('.batch_no').attr('disabled',false);
						$('.expire_date').val('');
						$('.expire_date').attr('disabled',false);
						$('.qty').val('');
						$('.product_qty').html('');
					}
				}
			});
		});*/

		$('#product_id').selectChain({
			target: $('#batch_no'),
			value:'title',
			url: '<?= BASE_URL.'current_inventories/get_batch_list';?>',
				type: 'post',
				data:{'product_id': 'product_id','inventory_status_id':'inventory_status_id'}
	});

	/*$('#batch_no').change(function(){
		var product_id = $('.product_id').val();
		var batch_no = $('.batch_no').val();
		var expire_date = $('.expire_date').val();

		if(product_id == '')
		{
			alert('Please select any product.');
			return false;
		}else{
			if(is_maintain_expire_date == 0)
			{
				$.ajax({
					type: "POST",
					url: '<?php echo BASE_URL;?>current_inventories/get_inventory_details',
					data: 'product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date,
					cache: false,
					success: function(response){
						$('.qty').val(response);
						$('.product_qty').html(response);
					}
				});
			}
		}
	});*/

	$('#batch_no').selectChain({
		target: $('#expire_date'),
		value:'title',
		url: '<?= BASE_URL.'current_inventories/get_expire_date_list';?>',
			type: 'post',
			data:{'product_id': 'product_id','batch_no': 'batch_no' ,'inventory_status_id':'inventory_status_id' }
	});

	$('#expire_date').change(function(){
		var product_id = $('.product_id').val();
		var batch_no = $('.batch_no').val();
		var expire_date = $('.expire_date').val();
		var inventory_status_id = $('#inventory_status_id').val();
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
				url: '<?php echo BASE_URL;?>current_inventories/get_inventory_details',
				data: 'product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date+'&inventory_status_id='+inventory_status_id,
				cache: false,
				success: function(response){
					$('.qty').val(response);
					$('.product_qty').html(response);
				}
			});

		}
	});



	var rowCount = 1;
	$(".add_more").click(function() {

		var product_id = $('.product_id').val();
		var quantity = $('.quantity').val();
		var batch_no = $('.batch_no').val();
		var expire_date = $('.expire_date').val();
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
		}else if(!quantity.match(/^\d+$/) || parseInt(quantity) <= 0)
		{
			alert('Please enter valid quantity.');
			$('.quantity').val('');
			return false;
		}else if(parseInt(quantity) >= 10000000)
		{
			alert('Quantity must be less then equal 10000000.');
			$('.quantity').val('');
			return false;
		}else if(parseInt(quantity) > parseInt($('.qty').val())){
			alert('Quantity should be less then equal Stock quantity.');
			$('.quantity').val('');
			return false;
		}else if(stock_check == true){
			alert('This product already added.');
			clear_field();
			return false;
		}
		else
		{
			rowCount ++;
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>admin/products/product_details',
				data: 'product_id=' + product_id,
				cache: false,
				success: function(response){
					var obj = jQuery.parseJSON(response);
					var recRow = '<tr class="table_row" id="rowCount'+rowCount+'"><td align="center">'+rowCount+'</td><td><input type="hidden" name="selected_product_id['+rowCount+']" class="selected_product_id" value="'+obj.Product.id+batch_no+expire_date+'"/>'+obj.Product.name+'<input type="hidden" name="product_id['+rowCount+']" value="'+obj.Product.id+'"/></td><td align="center"><input type="hidden" name="batch_no['+rowCount+']" value="'+batch_no+'">'+batch_no+'</td><td align="center"><input type="hidden" name="expire_date['+rowCount+']" value="'+expire_date+'">'+expire_date+'</td><td align="center">'+obj.ChallanMeasurementUnit.name+'<input type="hidden" name="measurement_unit['+rowCount+']" value="'+obj.Product.challan_measurement_unit_id+'"></td><td align="center"><input name="quantity['+rowCount+']" class="'+product_id+'_product_qty p_quantity" value="'+quantity+'"></td><td align="center" class="'+product_id+'"></td><td align="center"><input type="text" class="full_width form-control" name="remarks['+rowCount+']"></td><td align="center"><button class="btn btn-danger btn-xs remove" value="'+rowCount+'"><i class="fa fa-times"></i></button></td></tr>';
					$('.invoice_table').append(recRow);
					product_wise_total_quantity(product_id);
					clear_field();

					var total_quantity = set_total_quantity();
					if(total_quantity>0)
					{
						$('.save').prop('disabled', false);
					}
				}
			});

		}
	});


	$(document).on("click",".remove",function() {
		var removeNum = $(this).val();
		var p_id=$("input[name~='product_id["+removeNum+"]']").val();
		$('#rowCount'+removeNum).remove();
		product_wise_total_quantity(p_id);
		var total_quantity = set_total_quantity();
		if(total_quantity<1)
		{
			$('.save').prop('disabled', true);
		}
	});

	$('.save').prop('disabled', true);

	function clear_field() {
		$('.product_id').val('');
		$('.quantity').val('');
		$('.batch_no').val('');
		$('.expire_date').val('');
		$('.product_qty').html('');
		$('.qty').val('');
		$('.chosen').val('').trigger('chosen:updated');
		$('.add_more').val('');
		$('.batch_no').attr('disabled',false);
		$('.expire_date').attr('disabled',false);
	}




	$("form").submit(function(){
		$('.save').prop('disabled', true);
	});

	});
</script>