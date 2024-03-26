<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
		
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('New Return Challan'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Return Challan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			
			
			<div class="box-body">
					
				<?php echo $this->Form->create('DistReturnChallan', array('role' => 'form')); ?>
				
				<div class="form-group required">
					<?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control office_id','empty' => '---- Select Office ----','required'=>true)); ?>
				</div>
				<div class="form-group required">
					<?php echo $this->Form->input('distributor_id', array('id' => 'distributor_id', 'class' => 'form-control distributor_id','empty' => '---- Select ----','required'=>true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('return_date', array('class' => 'form-control', 'value' => date('Y-m-d'), 'readonly' => true)); ?>
				</div>
	
				
				<h3 style="font-size:16px; font-weight:600;">Sellable Products</h3>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Product Name</th>
							<th class="text-center">Batch No.</th>
							<th class="text-center">Expire Date</th>
							<th class="text-center">In Stock</th>
							<th class="text-center">Quantity</th>
							<th class="text-center">Value</th>
							<th class="text-center">Action</th>					
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?php echo $this->Form->input('product_id', array('id' => 'product_id','label'=>false, 'class' => 'full_width form-control product_id product_id_all','empty'=>'---- Select Product ----')); ?>
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
								<?php echo $this->Form->input('challan_qty', array('id' => 'challan_qty','label'=>false, 'type'=>'number', 'class' => 'full_width form-control quantity')); ?>
							</td>
							<td width="12%" align="center">		
								<?php echo $this->Form->input('unit_price', array('id' => 'unit_price', 'type'=>'hidden','class' => 'unit_price')); ?>					
								<?php echo $this->Form->input('gross_value', array('id' => 'gross_value', 'label'=> false, 'type'=>'number', 'readonly'=>true, 'class' => 'full_width form-control gross_value')); ?>
							</td>
							<td width="10%" align="center"><span class="btn btn-xs btn-primary s_add_more"> Add Product </span></td>					
						</tr>				
					</tbody>
				</table>
				<table class="table table-striped table-condensed table-bordered sellable_table">
					<thead>
						<tr>
							<th class="text-center" width="5%">SL.</th>
							<th class="text-center">Product Name</th>
							<th class="text-center" width="12%">Batch No.</th>
							<th class="text-center" width="12%">Expire Date</th>
							<th class="text-center" width="12%">Unit</th>
							<!-- <th class="text-center" width="12%">Unit Price</th> -->
							<th class="text-center" width="12%">Quantity</th>
							<th class="text-center" width="12%">Value</th>
							<th class="text-center" width="15%">Remarks</th>
							<th class="text-center" width="10%">Action</th>					
						</tr>
					</thead>					
				</table>
			
				
				<h3 style="font-size:16px; font-weight:600; margin-top:20px;">Bonus Products</h3>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Product Name</th>
							<th class="text-center">Batch No.</th>
							<th class="text-center">Expire Date</th>
							<th class="text-center">In Stock</th>
							<th class="text-center">Quantity</th>
							<th class="text-center">Action</th>					
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?php echo $this->Form->input('b_product_id', array('id' => 'b_product_id','label'=>false, 'class' => 'full_width form-control b_product_id product_id_all','empty'=>'---- Select Product ----')); ?>
							</td>
							<td width="15%" align="center">							
								<?php echo $this->Form->input('b_batch_no', array('id' => 'b_batch_no','label'=>false,'type'=>'select','class' => 'full_width form-control b_batch_no','empty'=>'---- Select Batch ----')); ?>
							</td>
							<td width="15%" align="center">							
								<?php echo $this->Form->input('b_expire_date', array('id' => 'b_expire_date','label'=>false,'type'=>'select','class' => 'full_width form-control b_expire_date','empty'=>'---- Select Expire Date ----')); ?>
							</td>
							<td width="15%" align="center">							
								<span class="b_product_qty"></span>
								<?php echo $this->Form->input('b_qty', array('type'=>'hidden','class' => 'b_qty')); ?>
							</td>
							<td width="12%" align="center">							
								<?php echo $this->Form->input('b_challan_qty', array('id' => 'b_challan_qty','label'=>false, 'type'=>'number', 'class' => 'full_width form-control b_quantity')); ?>
							</td>
							
							<td width="10%" align="center"><span class="btn btn-xs btn-primary b_add_more"> Add Product </span></td>					
						</tr>				
					</tbody>
				</table>
				<table class="table table-striped table-condensed table-bordered bonus_table">
					<thead>
						<tr>
							<th class="text-center" width="5%">SL.</th>
							<th class="text-center">Product Name</th>
							<th class="text-center" width="12%">Batch No.</th>
							<th class="text-center" width="12%">Expire Date</th>
							<th class="text-center" width="12%">Unit</th>
							<!-- <th class="text-center" width="12%">Unit Price</th> -->
							<th class="text-center" width="12%">Quantity</th>
							<th class="text-center" width="15%">Remarks</th>
							<th class="text-center" width="10%">Action</th>					
						</tr>
					</thead>					
				</table>
				
				
				<h3 style="font-size:16px; font-weight:600;">NCP Products</h3>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Product Name</th>
							<th class="text-center">Batch No.</th>
							<th class="text-center">Expire Date</th>
							<th class="text-center">In Stock</th>
							<th class="text-center">Quantity</th>
							<th class="text-center">Value</th>
							<th class="text-center">Action</th>					
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?php echo $this->Form->input('n_product_id', array('id' => 'n_product_id','label'=>false, 'class' => 'full_width form-control n_product_id product_id_all','empty'=>'---- Select Product ----')); ?>
							</td>
							<td width="15%" align="center">							
								<?php echo $this->Form->input('n_batch_no', array('id' => 'n_batch_no','label'=>false,'type'=>'select','class' => 'full_width form-control n_batch_no','empty'=>'---- Select Batch ----')); ?>
							</td>
							<td width="15%" align="center">							
								<?php echo $this->Form->input('n_expire_date', array('id' => 'n_expire_date','label'=>false,'type'=>'select','class' => 'full_width form-control n_expire_date','empty'=>'---- Select Expire Date ----')); ?>
							</td>
							<td width="15%" align="center">							
								<span class="n_product_qty"></span>
								<?php echo $this->Form->input('n_qty', array('type'=>'hidden','class' => 'n_qty')); ?>
							</td>
							<td width="12%" align="center">							
								<?php echo $this->Form->input('n_challan_qty', array('id' => 'n_challan_qty','label'=>false, 'type'=>'number', 'class' => 'full_width form-control n_quantity')); ?>
							</td>
							<td width="12%" align="center">		
								<?php echo $this->Form->input('n_unit_price', array('id' => 'n_unit_price', 'type'=>'hidden','class' => 'n_unit_price')); ?>					
								<?php echo $this->Form->input('n_gross_value', array('id' => 'n_gross_value', 'label'=> false, 'type'=>'number', 'readonly'=>true, 'class' => 'full_width form-control n_gross_value')); ?>
							</td>
							<td width="10%" align="center"><span class="btn btn-xs btn-primary n_add_more"> Add Product </span></td>					
						</tr>				
					</tbody>
				</table>
				<table class="table table-striped table-condensed table-bordered ncp_table">
					<thead>
						<tr>
							<th class="text-center" width="5%">SL.</th>
							<th class="text-center">Product Name</th>
							<th class="text-center" width="12%">Batch No.</th>
							<th class="text-center" width="12%">Expire Date</th>
							<th class="text-center" width="12%">Unit</th>
							<!-- <th class="text-center" width="12%">Unit Price</th> -->
							<th class="text-center" width="12%">Quantity</th>
							<th class="text-center" width="12%">Value</th>
							<th class="text-center" width="15%">Remarks</th>
							<th class="text-center" width="10%">Action</th>					
						</tr>
					</thead>					
				</table>
			
			
			
			
				<p style="margin-top:20px; text-align:right;">
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary save', 'div'=>false, 'name'=>'save')); ?>
				<?php /*?><?php echo $this->Form->submit('Draft', array('class' => 'btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft')); ?><?php */?>
				</p>
				<?php echo $this->Form->end(); ?>
			
			</div>
			
			
		</div>			
	</div>
</div>
<script>		
	$(document).ready(function(){			
		
		$('#office_id').selectChain({
            target: $('#distributor_id'),
            value:'name',
            url: '<?= BASE_URL.'DistDistributors/get_dist_distributor_list'?>',
            type: 'post',
            data:{'office_id': 'office_id' }
        });
		$('#distributor_id').selectChain({
            target: $('.product_id'),
            value:'name',
            url: '<?= BASE_URL.'DistReturnChallans/get_inventory_product_list'?>',
            type: 'post',
            data:{'distributor_id': 'distributor_id' }
        });
		$('#distributor_id').selectChain({
            target: $('.b_product_id'),
            value:'name',
            url: '<?= BASE_URL.'DistReturnChallans/get_bonus_inventory_product_list'?>',
            type: 'post',
            data:{'distributor_id': 'distributor_id' }
        });
		$('#distributor_id').selectChain({
            target: $('.n_product_id'),
            value:'name',
            url: '<?= BASE_URL.'DistReturnChallans/get_inventory_product_list'?>',
            type: 'post',
            data:{'distributor_id': 'distributor_id' }
        });
		
		
		
		
		
		//Start for Sellable Products
		var inventory_status_id=1;
		var is_maintain_batch = 1;
		var is_maintain_expire_date = 1;
		
		$('#product_id').change(function(){
			var product_id = $('#product_id').val();
			var distributor_id = $('#distributor_id').val();
			var office_id = $('#office_id').val();
			var with_stock=1
			var inventory_status_id = 1;
			
			$("#expire_date").html('<option value="">---- Select ----</option>');
			
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>dist_return_challans/get_batch_list',
				data: 'product_id='+product_id+'&distributor_id='+distributor_id+'&office_id='+office_id,
				cache: false, 
				success: function(response){
					console.log(response);
					//alert(response);											
					$('#batch_no').html(response);				
				}
			});
			
			$.ajax({
					type: "POST",
					url: '<?php echo BASE_URL;?>dist_return_challans/get_inventory_details_in_return_challan',
					data: 'product_id='+product_id+'&distributor_id='+distributor_id+'&office_id='+office_id,
					cache: false, 
					success: function(response){						
						$('.qty').val(response);					
						$('.product_qty').html(response);					
					}
				});
				
			//get_product_price();
		});
		
		
		$('#challan_qty').keyup(function(){
			var min_qty = $('#challan_qty').val();
			var product_id = $('#product_id').val();
			var product_id_list = '47,'
			$.ajax({
                url: '<?= BASE_URL . 'dist_return_challans/get_individual_price' ?>',
                'type': 'POST',
                data: {min_qty: min_qty, product_id: product_id},
                success: function (result) 
                {
                   var obj = jQuery.parseJSON(result);
				   $('#unit_price').val(obj.unit_rate);
				   $('#gross_value').val(obj.total_value);
                   //alert(obj.total_value);
				}
			});
		});
				
		
		$('#batch_no').change(function(){
			var product_id = $('.product_id').val();
			var distributor_id = $('#distributor_id').val();
			var office_id = $('#office_id').val();
			var batch_no = $('#batch_no').val();
			var with_stock=1
			var inventory_status_id = 1;
			
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>dist_return_challans/get_expire_date_list',
				data: 'product_id='+product_id+'&distributor_id='+distributor_id+'&office_id='+office_id+'&batch_no='+batch_no,
				cache: false, 
				success: function(response){
					console.log(response);
					//alert(response);											
					$('#expire_date').html(response);				
				}
			});
		
		});
				
		
		var s_rowCount = 1; 		
		$(".s_add_more").click(function() {	
			
		 	var product_id = $('.product_id').val();
			var quantity = $('.quantity').val(); 
			var unit_price = $('#unit_price').val();
			var gross_value = $('#gross_value').val();
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
			}else if(!quantity.match(/^\d+(\.\d{1,2})?$/) || parseFloat(quantity) <= 0.00)
			{
				alert('Please enter valid quantity. Ex. : 100 or 100.00');
				$('.quantity').val(''); 
				return false;
			}else if(parseFloat(quantity) > parseFloat($('.qty').val())){
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
				s_rowCount ++; 
				$.ajax({
					type: "POST",
					url: '<?php echo BASE_URL;?>admin/products/product_details',
					data: 'product_id=' + product_id,
					cache: false, 
					success: function(response){						
						var obj = jQuery.parseJSON(response);
						
						var recRow = '<tr class="s_table_row" id="s_rowCount'+s_rowCount+'"><td align="center">'+s_rowCount+'</td><td><input type="hidden" name="s[selected_product_id][]" class="selected_product_id" value="'+obj.Product.id+batch_no+expire_date+'"/>'+obj.Product.name+'<input type="hidden" name="s[product_id][]" value="'+obj.Product.id+'"/></td><td align="center"><input type="hidden" name="s[batch_no][]" value="'+batch_no+'">'+batch_no+'</td><td align="center"><input type="hidden" name="s[expire_date][]" value="'+expire_date+'">'+display_exp_date+'</td><td align="center">'+obj.ReturnMeasurementUnit.name+'<input type="hidden" name="s[measurement_unit][]" value="'+obj.Product.return_measurement_unit_id+'"></td><td align="center"><input type="hidden" name="s[quantity][]" class="p_quantity" value="'+quantity+'">'+quantity+'</td><td align="center"><input type="hidden" name="s[unit_price][]" value="'+unit_price+'"><input type="hidden" name="s[gross_value][]" class="p_gross_value" value="'+gross_value+'">'+gross_value+'</td><td align="center"><input type="text" class="full_width form-control" name="s[remarks][]"></td><td align="center"><button class="btn btn-danger btn-xs s_remove" value="'+s_rowCount+'"><i class="fa fa-times"></i></button></td></tr>';
						$('.sellable_table').append(recRow);
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
		
		
		$(document).on("click",".s_remove",function() {				
			var removeNum = $(this).val(); 
			$('#s_rowCount'+removeNum).remove(); 
			var total_quantity = set_total_quantity();				
			if(total_quantity<=0)
			{
				$('.draft').prop('disabled', true);
			}				
		});	

	
		$('.draft').prop('disabled', true);
		
		function clear_field() {
	        $('.product_id').val('');		
	        $('.quantity').val('');		
	        $('.batch_no').val('');	
	        $('.expire_date').val('');	
			$('.product_qty').html('');	
			$('.qty').val('');	
			$('.chosen').val('').trigger('chosen:updated');
			$('.s_add_more').val('');
			$('.batch_no').attr('disabled',false);
			$('.expire_date').attr('disabled',false);
			
			$('.gross_value').val('');
			$('.unit_price').val('');
	    }
   		
   		function set_total_quantity() {
	        var sum = 0;
	        var num = 1;
			$('.s_table_row').each(function() {
				var table_row = $(this);
				var total_quantity = table_row.closest('tr').find('.p_quantity').val();
				sum += parseFloat(total_quantity);
				$(this).find("td:first").html(num++);
			});	
			//$('.total_quantity').html(sum);
			return sum;
	    }
		//End for Sellable Products
		
		
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		//Start for Bonus Products
		var inventory_status_id=1;
		var is_maintain_batch = 1;
		var is_maintain_expire_date = 1;
		
		$('#b_product_id').change(function(){
			var product_id = $('#b_product_id').val();
			var distributor_id = $('#distributor_id').val();
			var office_id = $('#office_id').val();
			var with_stock=1
			var inventory_status_id = 1;
			
			$("#b_expire_date").html('<option value="">---- Select ----</option>');
			
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>dist_return_challans/get_batch_list',
				data: 'product_id='+product_id+'&distributor_id='+distributor_id+'&office_id='+office_id,
				cache: false, 
				success: function(response){
					console.log(response);
					//alert(response);											
					$('#b_batch_no').html(response);				
				}
			});
			
			$.ajax({
					type: "POST",
					url: '<?php echo BASE_URL;?>dist_return_challans/get_inventory_details_in_return_challan',
					data: 'product_id='+product_id+'&distributor_id='+distributor_id+'&office_id='+office_id+'&is_bonus='+1,
					cache: false, 
					success: function(response){						
						$('.b_qty').val(response);					
						$('.b_product_qty').html(response);					
					}
				});
				
			//get_product_price();
		});
		
		$('#b_batch_no').change(function(){
			var product_id = $('.b_product_id').val();
			var distributor_id = $('#distributor_id').val();
			var office_id = $('#office_id').val();
			var batch_no = $('#b_batch_no').val();
			var with_stock=1
			var inventory_status_id = 1;
			
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>dist_return_challans/get_expire_date_list',
				data: 'product_id='+product_id+'&distributor_id='+distributor_id+'&office_id='+office_id+'&batch_no='+batch_no,
				cache: false, 
				success: function(response){
					console.log(response);
					//alert(response);											
					$('#b_expire_date').html(response);				
				}
			});
		
		});
				
		
		var b_rowCount = 1; 		
		$(".b_add_more").click(function() {	
		 	var product_id = $('.b_product_id').val();
			var quantity = $('.b_quantity').val(); 
			var unit_price = $('#b_unit_price').val();
			var batch_no = $('.b_batch_no').val(); 
			var expire_date = $('.b_expire_date').val(); 
			var display_exp_date=expire_date_format(expire_date);
			//var selected_stock_id = $("input[name=selected_stock_id]").val();
			var selected_stock_array = $(".b_selected_product_id").map(function() {
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
			}else if(!quantity.match(/^\d+(\.\d{1,2})?$/) || parseFloat(quantity) <= 0.00)
			{
				alert('Please enter valid quantity. Ex. : 100 or 100.00');
				$('.quantity').val(''); 
				return false;
			}else if(parseFloat(quantity) > parseFloat($('.qty').val())){
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
				b_rowCount ++; 
				$.ajax({
					type: "POST",
					url: '<?php echo BASE_URL;?>admin/products/product_details',
					data: 'product_id=' + product_id,
					cache: false, 
					success: function(response){						
						var obj = jQuery.parseJSON(response);
						
						var recRow = '<tr class="b_table_row" id="b_rowCount'+b_rowCount+'"><td align="center">'+b_rowCount+'</td><td><input type="hidden" name="b[selected_product_id][]" class="b_selected_product_id" value="'+obj.Product.id+batch_no+expire_date+'"/>'+obj.Product.name+'<input type="hidden" name="b[product_id][]" value="'+obj.Product.id+'"/></td><td align="center"><input type="hidden" name="b[batch_no][]" value="'+batch_no+'">'+batch_no+'</td><td align="center"><input type="hidden" name="b[expire_date][]" value="'+expire_date+'">'+display_exp_date+'</td><td align="center">'+obj.ReturnMeasurementUnit.name+'<input type="hidden" name="b[measurement_unit][]" value="'+obj.Product.return_measurement_unit_id+'"></td><td align="center"><input type="hidden" name="b[quantity][]" class="p_quantity" value="'+quantity+'">'+quantity+'</td><td align="center"><input type="text" class="full_width form-control" name="b[remarks][]"></td><td align="center"><button class="btn btn-danger btn-xs b_remove" value="'+b_rowCount+'"><i class="fa fa-times"></i></button></td></tr>';
						//alert(recRow);
						$('.bonus_table').append(recRow);
						b_clear_field();
						
						var total_quantity = b_set_total_quantity();
						if(total_quantity>0)
						{
							$('.draft').prop('disabled', false);
						}						
					}
				});				
				
			}
		});
		
		
		$(document).on("click",".b_remove",function() {				
			var removeNum = $(this).val(); 
			$('#b_rowCount'+removeNum).remove(); 
			var total_quantity = set_total_quantity();				
			if(total_quantity<=0)
			{
				$('.draft').prop('disabled', true);
			}				
		});	

	
		//$('.draft').prop('disabled', true);
		
		function b_clear_field() {
	        $('.b_product_id').val('');		
	        $('.b_quantity').val('');		
	        $('.b_batch_no').val('');	
	        $('.b_expire_date').val('');	
			$('.b_product_qty').html('');	
			$('.b_qty').val('');	
			$('.b_chosen').val('').trigger('chosen:updated');
			$('.b_add_more').val('');
			$('.b_batch_no').attr('disabled',false);
			$('.b_expire_date').attr('disabled',false);
			
			$('.b_gross_value').val('');
			$('.b_unit_price').val('');
	    }
   		
   		function b_set_total_quantity() {
	        var sum = 0;
	        var num = 1;
			$('.b_table_row').each(function() {
				var table_row = $(this);
				var total_quantity = table_row.closest('tr').find('.p_quantity').val();
				sum += parseFloat(total_quantity);
				$(this).find("td:first").html(num++);
			});	
			//$('.total_quantity').html(sum);
			return sum;
	    }
		//End for Sellable Products
		
		
		
		
		
		
		
		//Start for NCP Products
		$('#n_product_id').change(function(){
			var product_id = $('#n_product_id').val();
			var distributor_id = $('#distributor_id').val();
			var office_id = $('#office_id').val();
			var with_stock=1
			var inventory_status_id = 1;
			
			$("#n_expire_date").html('<option value="">---- Select ----</option>');
			
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>dist_return_challans/get_batch_list',
				data: 'product_id='+product_id+'&distributor_id='+distributor_id+'&office_id='+office_id+'&inventory_status_id='+inventory_status_id,
				cache: false, 
				success: function(response){
					console.log(response);
					//alert(response);											
					$('#n_batch_no').html(response);				
				}
			});
			
			$.ajax({
					type: "POST",
					url: '<?php echo BASE_URL;?>dist_return_challans/get_inventory_details_in_return_challan',
					data: 'product_id='+product_id+'&distributor_id='+distributor_id+'&office_id='+office_id,
					cache: false, 
					success: function(response){						
						$('.n_qty').val(response);					
						$('.n_product_qty').html(response);					
					}
				});
				
			//get_product_price();
		});
		
		
		$('#n_challan_qty').keyup(function(){
			var min_qty = $('#n_challan_qty').val();
			var product_id = $('#n_product_id').val();
			$.ajax({
                url: '<?= BASE_URL . 'dist_return_challans/get_individual_price' ?>',
                'type': 'POST',
                data: {min_qty: min_qty, product_id: product_id},
                success: function (result) 
                {
                   var obj = jQuery.parseJSON(result);
				   $('#n_unit_price').val(obj.unit_rate);
				   $('#n_gross_value').val(obj.total_value);
                   //alert(obj.total_value);
				}
			});
		});
				
		
		$('#n_batch_no').change(function(){
			var product_id = $('.n_product_id').val();
			var distributor_id = $('#distributor_id').val();
			var office_id = $('#office_id').val();
			var batch_no = $('#n_batch_no').val();
			var with_stock=1
			var inventory_status_id = 1;
			
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>dist_return_challans/get_expire_date_list',
				data: 'product_id='+product_id+'&distributor_id='+distributor_id+'&office_id='+office_id+'&batch_no='+batch_no,
				cache: false, 
				success: function(response){
					console.log(response);
					//alert(response);											
					$('#n_expire_date').html(response);				
				}
			});
		
		});
				
		
		var n_rowCount = 1; 		
		$(".n_add_more").click(function() {	
			
		 	var product_id = $('.n_product_id').val();
			var quantity = $('.n_quantity').val(); 
			var unit_price = $('#n_unit_price').val();
			var gross_value = $('#n_gross_value').val();
			var batch_no = $('.n_batch_no').val(); 
			var expire_date = $('.n_expire_date').val(); 
			var display_exp_date=expire_date_format(expire_date);
			//var selected_stock_id = $("input[name=selected_stock_id]").val();
			var selected_stock_array = $(".n_selected_product_id").map(function() {
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
			}else if(!quantity.match(/^\d+(\.\d{1,2})?$/) || parseFloat(quantity) <= 0.00)
			{
				alert('Please enter valid quantity. Ex. : 100 or 100.00');
				$('.quantity').val(''); 
				return false;
			}else if(parseFloat(quantity) > parseFloat($('.qty').val())){
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
				n_rowCount ++; 
				$.ajax({
					type: "POST",
					url: '<?php echo BASE_URL;?>admin/products/product_details',
					data: 'product_id=' + product_id,
					cache: false, 
					success: function(response){						
						var obj = jQuery.parseJSON(response);
						
						var recRow = '<tr class="n_table_row" id="n_rowCount'+n_rowCount+'"><td align="center">'+n_rowCount+'</td><td><input type="hidden" name="n[selected_product_id][]" class="selected_product_id" value="'+obj.Product.id+batch_no+expire_date+'"/>'+obj.Product.name+'<input type="hidden" name="n[product_id][]" value="'+obj.Product.id+'"/></td><td align="center"><input type="hidden" name="n[batch_no][]" value="'+batch_no+'">'+batch_no+'</td><td align="center"><input type="hidden" name="n[expire_date][]" value="'+expire_date+'">'+display_exp_date+'</td><td align="center">'+obj.ReturnMeasurementUnit.name+'<input type="hidden" name="n[measurement_unit][]" value="'+obj.Product.return_measurement_unit_id+'"></td><td align="center"><input type="hidden" name="n[quantity][]" class="p_quantity" value="'+quantity+'">'+quantity+'</td><td align="center"><input type="hidden" name="n[unit_price][]" value="'+unit_price+'"><input type="hidden" name="n[gross_value][]" value="'+gross_value+'">'+gross_value+'</td><td align="center"><input type="text" class="full_width form-control" name="n[remarks][]"></td><td align="center"><button class="btn btn-danger btn-xs n_remove" value="'+n_rowCount+'"><i class="fa fa-times"></i></button></td></tr>';
						$('.ncp_table').append(recRow);
						n_clear_field();
						
						var total_quantity = n_set_total_quantity();
						if(total_quantity>0)
						{
							$('.draft').prop('disabled', false);
						}						
					}
				});				
				
			}
		});
		
		
		$(document).on("click",".n_remove",function() {				
			var removeNum = $(this).val(); 
			$('#n_rowCount'+removeNum).remove(); 
			var total_quantity = set_total_quantity();				
			if(total_quantity<=0)
			{
				$('.draft').prop('disabled', true);
			}				
		});	

	
		$('.draft').prop('disabled', true);
		
		function n_clear_field() {
	        $('.n_product_id').val('');		
	        $('.n_quantity').val('');		
	        $('.n_batch_no').val('');	
	        $('.n_expire_date').val('');	
			$('.n_product_qty').html('');	
			$('.n_qty').val('');	
			$('.chosen').val('').trigger('chosen:updated');
			$('.n_add_more').val('');
			$('.n_batch_no').attr('disabled',false);
			$('.n_expire_date').attr('disabled',false);
			
			$('.n_gross_value').val('');
			$('.n_unit_price').val('');
	    }
   		
   		function n_set_total_quantity() {
	        var sum = 0;
	        var num = 1;
			$('.n_table_row').each(function() {
				var table_row = $(this);
				var total_quantity = table_row.closest('tr').find('.p_quantity').val();
				sum += parseFloat(total_quantity);
				$(this).find("td:first").html(num++);
			});	
			//$('.total_quantity').html(sum);
			return sum;
	    }
		//End for NCP Products
		
		
		
		
	});
</script>