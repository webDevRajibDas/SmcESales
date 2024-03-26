<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('New NCP Product Issue'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>NCP Product Issue List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Challan', array('role' => 'form')); ?>
			<div class="form-group">
				<?php echo $this->Form->input('receiver_store_id', array('id'=> 'receiver_store_id', 'class' => 'form-control','empty'=>'---- Select Receiver ----','options'=>$receiverStore,'required'=>true)); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('challan_id', array('label'=>'Challan No.','id'=>'challan_id','class' => 'form-control challan_id','empty'=>'---- Select Challan ----','required'=>true)); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('remarks', array('class' => 'form-control')); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('is_close', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is Challan Close :</b>')); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('inventory_status_id', array('id'=>'inventory_status_id','class' => 'form-control inventory_status_id','type'=>'hidden','value'=>1)); ?>
			</div>
			<div class="form-group">
				<?php //echo $this->Form->input('transaction_type_id', array('id'=>'transaction_type_id','class' => 'form-control transaction_type_id','type'=>'hidden','value'=>1)); ?>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Product Name</th>
						<th class="text-center">Batch No.</th>
						<th class="text-center">Expire Date</th>
						<th class="text-center">Quantity</th>
						<th class="text-center">Remaining <br/>Quantity</th>
						<th class="text-center">Quantity In Stock</th>
						<th class="text-center">Quantity</th>
						<th class="text-center">Action</th>					
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?php echo $this->Form->input('product_id', array('id' => 'product_id','label'=>false, 'class' => 'full_width form-control product_id','empty'=>'---- Select Product ----')); ?>
						</td>
						<td width="15%" align="center">							
							<?php echo $this->Form->input('batch_no', array('id' => 'batch_no','label'=>false,'type'=>'select','class' => 'full_width form-control batch_no','empty'=>'---- Select Batch ----')); ?>
						</td>
						<td width="15%" align="center">							
							<?php echo $this->Form->input('expire_date', array('id' => 'expire_date','label'=>false,'type'=>'select','class' => 'full_width form-control expire_date','empty'=>'---- Select Expire Date ----')); ?>
						</td>
						<td width="15%" align="center">							
							<span class="product_qty"></span>
							<?php echo $this->Form->input('qty', array('type'=>'hidden','class' => 'qty')); ?>
						</td>
						<td width="12%" align="center">							
							<span class="remaining_qty"></span>
							<?php echo $this->Form->input('remaining_qty', array('type'=>'hidden','class' => 'remaining_qty')); ?>
						</td>
						<td width="12%" align="center">							
							<span class="stock_quantity"></span>
							<?php echo $this->Form->input('stock_qty', array('type'=>'hidden','class' => 'stock_qty')); ?>
						</td>
						<td width="12%" align="center">							
							<?php echo $this->Form->input('challan_qty', array('label'=>false, 'type'=>'number', 'class' => 'full_width form-control quantity')); ?>
						</td>
						<td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>					
					</tr>				
				</tbody>
			</table>			
			<table class="table table-striped table-condensed table-bordered invoice_table">
				<thead>
					<tr>
						<th class="text-center" width="5%">SL.</th>
						<th class="text-center">Product Name</th>
						<th class="text-center" width="12%">Batch No.</th>
						<th class="text-center" width="12%">Expire Date</th>
						<th class="text-center" width="12%">Unit</th>
						<th class="text-center" width="12%">Quantity</th>
						<th class="text-center" width="15%">Remarks</th>
						<th class="text-center" width="10%">Action</th>					
					</tr>
				</thead>					
			</table>
			</br>
			<div class="pull-right">
			<?php echo $this->Form->submit('Save & Submit', array('class' => 'btn btn-large btn-primary save', 'div'=>false, 'name'=>'save', 'disabled')); ?>
            <?php echo $this->Form->submit('Draft', array('class' => 'btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft')); ?>
            </div>
			<?php echo $this->Form->end(); ?>			
			</div>
		</div>			
	</div>
</div>
<script>		
	$(document).ready(function(){			
		
		$('#receiver_store_id').selectChain({
			target: $('#challan_id'),
			value:'title',
			url: '<?= BASE_URL.'ncp_product_issues/get_challan_list';?>',
			type: 'post',
			data:{'receiver_store_id': 'receiver_store_id' }
		});

		$('#challan_id').selectChain({
			target: $('#product_id'),
			value:'title',
			url: '<?= BASE_URL.'ncp_product_issues/get_challan_product_list';?>',
			type: 'post',
			data:{'challan_id': 'challan_id' }
		});	

		var is_maintain_batch = 1;
		var is_maintain_expire_date = 1;
		$('#product_id').change(function(){
			var challan_id = $('.challan_id').val();
			var product_id = $(this).val();
			var batch_no = $('.batch_no').val();
			var expire_date = $('.expire_date').val();
			var inventory_status_id = $('.inventory_status_id').val();
			var transaction_type_id = $('.transaction_type_id').val();
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
							url: '<?php echo BASE_URL;?>ncp_product_issues/get_inventory_details',
							data: 'challan_id='+challan_id+'&product_id='+product_id+'&batch_no='+''+'&expire_date='+''+'&inventory_status_id='+inventory_status_id+'&transaction_type_id='+transaction_type_id,
							cache: false, 
							success: function(response1){						
								var obj1 = jQuery.parseJSON(response1);	
								//console.log(obj1);
								$('.product_qty').html(obj1.qty);					
								$('.qty').val(obj1.qty);
								$('.remaining_qty').val(obj1.remaining_qty);					
								//$('.remaining_qty').html(obj1.remaining_qty);
								$('.stock_qty').val(obj1.stock_quantity);					
								$('.stock_quantity').html(obj1.stock_quantity);	
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
		});
		
		$('#product_id').selectChain({
			target: $('#batch_no'),
			value:'title',
			url: '<?= BASE_URL.'ncp_product_issues/get_batch_list';?>',
			type: 'post',
			//data:{'product_id': 'product_id','inventory_status_id':'inventory_status_id','transaction_type_id':'transaction_type_id' }
			data:{'product_id': 'product_id','inventory_status_id':'inventory_status_id','with_stock':true}
		});	
		
		$('#batch_no').change(function(){
			var product_id = $('.product_id').val();
			var batch_no = $('.batch_no').val();
			var expire_date = $('.expire_date').val();
			var inventory_status_id = $('.inventory_status_id').val();
			var transaction_type_id = $('.transaction_type_id').val();			
			
			if(product_id == '')
			{
				alert('Please select any product.');
				return false;
			}else{
				if(is_maintain_expire_date == 0)
				{
					$.ajax({
						type: "POST",
						url: '<?php echo BASE_URL;?>ncp_product_issues/get_inventory_details',
						data: 'challan_id='+challan_id+'&product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date+'&inventory_status_id='+inventory_status_id+'&transaction_type_id='+transaction_type_id,
						cache: false, 
						success: function(response){						
							var obj = jQuery.parseJSON(response);
							$('.product_qty').html(obj.qty);					
							$('.qty').val(obj.qty);
							$('.remaining_qty').val(obj.remaining_qty);					
							$('.remaining_qty').html(obj.remaining_qty);
							$('.stock_qty').val(obj.stock_quantity);					
							$('.stock_quantity').html(obj.stock_quantity);				
						}
					});
				}
			}
		});
		
		$('#batch_no').selectChain({
			target: $('#expire_date'),
			value:'title',
			url: '<?= BASE_URL.'current_inventories/get_expire_date_list';?>',
			type: 'post',
			//data:{'product_id': 'product_id','batch_no': 'batch_no' ,'inventory_status_id':'inventory_status_id','transaction_type_id':'transaction_type_id' }
			data:{'product_id': 'product_id','batch_no': 'batch_no' ,'inventory_status_id':'inventory_status_id','with_stock':true}
		});
		
		$('#expire_date').change(function(){
			var challan_id = $('.challan_id').val();
			var product_id = $('.product_id').val();
			var batch_no = $('.batch_no').val();
			var expire_date = $('.expire_date').val();
			var transaction_type_id = $('#transaction_type_id').val();
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
					url: '<?php echo BASE_URL;?>ncp_product_issues/get_inventory_details',
					//data: 'challan_id='+challan_id+'&product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date+'&transaction_type_id='+transaction_type_id+'&inventory_status_id='+inventory_status_id,
					data: 'challan_id='+challan_id+'&product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date+'&inventory_status_id='+inventory_status_id,
					cache: false, 
					success: function(response){						
						var obj = jQuery.parseJSON(response);
						$('.product_qty').html(obj.qty);					
						$('.qty').val(obj.qty);
						$('.remaining_qty').val(obj.remaining_qty);					
						$('.remaining_qty').html(obj.remaining_qty);
						$('.stock_qty').val(obj.stock_quantity);					
						$('.stock_quantity').html(obj.stock_quantity);				
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
			var display_exp_date=expire_date_format(expire_date); 
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
			}else if(!quantity.match(/^(\d+\.?\d{1,2}|\d|\.\d{1,2})$/) || parseFloat(quantity) <= 0.00)
			{
				alert('Please enter valid quantity. Ex. : 100 or 100.00');
				$('.quantity').val(''); 
				return false;
			}else if(parseFloat(quantity) > parseFloat($('.remaining_qty').val())){
				alert('Quantity should be less then equal Remaining Quantity.');
				$('.quantity').val(''); 
				return false;
			}else if(parseFloat(quantity) > parseFloat($('.stock_qty').val())){
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
						var recRow = '<tr class="table_row" id="rowCount'+rowCount+'"><td align="center">'+rowCount+'</td><td><input type="hidden" name="selected_product_id[]" class="selected_product_id" value="'+obj.Product.id+batch_no+expire_date+'"/>'+obj.Product.name+'<input type="hidden" name="product_id[]" value="'+obj.Product.id+'"/></td><td align="center"><input type="hidden" name="batch_no[]" value="'+batch_no+'">'+batch_no+'</td><td align="center"><input type="hidden" name="expire_date[]" value="'+expire_date+'">'+display_exp_date+'</td><td align="center">'+obj.ChallanMeasurementUnit.name+'<input type="hidden" name="measurement_unit[]" value="'+obj.Product.challan_measurement_unit_id+'"></td><td align="center"><input type="hidden" name="quantity[]" class="p_quantity" value="'+quantity+'">'+quantity+'</td><td align="center"><input type="text" class="full_width form-control" name="remarks[]"></td><td align="center"><button class="btn btn-danger btn-xs remove" value="'+rowCount+'"><i class="fa fa-times"></i></button></td></tr>'; 
						$('.invoice_table').append(recRow);
						clear_field();
						
						var total_quantity = set_total_quantity();
						if(total_quantity>0)
						{
							$('.draft').prop('disabled', false);
						}						
					}
				});				
				
			}
		});
		
		
		$(document).on("click",".remove",function() {				
			var removeNum = $(this).val(); 
			$('#rowCount'+removeNum).remove(); 
			var total_quantity = set_total_quantity();				
			if(total_quantity<=0)
			{
				$('.draft').prop('disabled', true);
			}				
		});	

	
		$('.draft').prop('disabled', true);
		
		function clear_field() {
	        
			$('.product_qty').html('');	
			$('.qty').val('');	
			$('.remaining_qty').html('');	
			$('.remaining_qty').val('');	
			$('.stock_quantity').html('');	
			$('.stock_qty').val('');	
			
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
   		
   		function set_total_quantity() {
	        var sum = 0;
	        var num = 1;
			$('.table_row').each(function() {
				var table_row = $(this);
				var total_quantity = table_row.closest('tr').find('.p_quantity').val();
				sum += parseFloat(total_quantity);
				$(this).find("td:first").html(num++);
			});	
			
			$('.total_quantity').html(sum);
			return sum;
	    }
		
	});
</script>