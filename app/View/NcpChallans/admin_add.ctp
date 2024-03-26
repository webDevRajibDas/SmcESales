<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('New NCP Challan'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>NCP Challan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Challan', array('role' => 'form')); ?>
			<div class="form-group">
				<?php echo $this->Form->input('receiver_store_id', array('id'=>'receiver_store_id', 'class' => 'form-control','empty'=>'---- Select Receiver Store ----','options'=>$receiver_store,'required'=>true)); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('challan_id', array('label'=>'Challan No.','id'=>'challan_id','class' => 'form-control challan_id','empty'=>'---- Select Challan ----','required'=>true)); ?>
			</div>			
			<div class="form-group">
				<?php echo $this->Form->input('is_close', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is Challan Close :</b>')); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('remarks', array('class' => 'form-control')); ?>
			</div>
				
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Product Name</th>
						<th class="text-center">Challan Qty</th>
						<th class="text-center">Remaining Qty</th>
						<th class="text-center">Batch No.</th>
						<th class="text-center">Quantity</th>
						<th class="text-center">Expire Date</th>
						<th class="text-center">Action</th>					
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?php echo $this->Form->input('product_id', array('label'=>false, 'id'=>'product_id', 'class' => 'full_width form-control product_id','empty'=>'---- Select Product ----')); ?>
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
							<?php echo $this->Form->input('batch_no', array('label'=>false, 'class' => 'full_width form-control batch_no')); ?>
						</td>
						<td width="12%" align="center">							
							<?php echo $this->Form->input('challan_qty', array('label'=>false, 'class' => 'full_width form-control quantity')); ?>
						</td>
						<td width="12%" align="center">							
							<?php echo $this->Form->input('expire_date', array('label'=>false, 'class' => 'full_width form-control expire_date expireDatepicker')); ?>
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
						<th class="text-center" width="12%">Unit</th>
						<th class="text-center" width="12%">Quantity</th>
						<th class="text-center" width="12%">Expire Date</th>
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
			url: '<?= BASE_URL.'ncp_challans/get_challan_list';?>',
			type: 'post',
			data:{'receiver_store_id': 'receiver_store_id' }
		});

		$('#challan_id').selectChain({
			target: $('#product_id'),
			value:'title',
			url: '<?= BASE_URL.'ncp_challans/get_challan_product_list';?>',
			type: 'post',
			data:{'challan_id': 'challan_id' }
		});			
				
		var is_maintain_batch = 1;
		var is_maintain_expire_date = 1;
		$('#product_id').change(function(){
			var product_id = $(this).val();
			var challan_id = $('.challan_id').val();
			$.ajax({
				type: "POST",
				url: '<?php echo BASE_URL;?>admin/products/product_details',
				data: 'product_id=' + product_id,
				cache: false, 
				success: function(response){						
					
					var obj = jQuery.parseJSON(response);						
					if(obj.Product.maintain_batch == 0){
						is_maintain_batch = 0;
						$('.batch_no').val('');
						$('.batch_no').attr('readonly',true);	
					}else{
						$('.batch_no').val('');
						$('.batch_no').attr('readonly',false);
					}
					
					if(obj.Product.is_maintain_expire_date == 0){
						is_maintain_expire_date = 0;
						$('.expire_date').val('');
						$('.expire_date').attr('disabled',true);
						
					}else{
						$('.expire_date').val('');
						$('.expire_date').attr('disabled',false);
					}

					$.ajax({
						type: "POST",
						url: '<?php echo BASE_URL;?>ncp_challans/get_inventory_details',
						data: 'challan_id='+challan_id+'&product_id='+product_id,						cache: false, 
						success: function(response1){						
							var obj1 = jQuery.parseJSON(response1);
							$('.product_qty').html(obj1.qty);					
							$('.qty').val(obj1.qty);
							$('.remaining_qty').val(obj1.remaining_qty);					
							$('.remaining_qty').html(obj1.remaining_qty);		
						}
					});

					
				}
			});
		});
		
		
		var rowCount = 1; 		
		$(".add_more").click(function() {	
			
		 	var product_id = $('.product_id').val();
			var quantity = $('.quantity').val(); 
			var batch_no = $('.batch_no').val(); 
			var expire_date = $('.expire_date').val(); 
			var selected_stock_array = $(".selected_product_id").map(function() {
               return $(this).val();
            }).get();
			
			//alert(expire_date);
			//alert(is_maintain_expire_date);
            var product_check_id = product_id + batch_no + expire_date; 
            var stock_check = $.inArray(product_check_id,selected_stock_array) != -1;
			
			if(product_id=='')
			{
				alert('Please select any product.');
				return false;
			}else if(!quantity.match(/^\d+(\.\d{1,2})?$/) || parseFloat(quantity) <= 0.00)
			{
				alert('Please enter valid quantity. Ex. : 100 or 100.00');
				$('.quantity').val(''); 
				return false;
			}else if(parseFloat(quantity) > parseFloat($('.remaining_qty').val())){
				alert('Quantity should be less then equal Remaining Quantity.');
				$('.quantity').val(''); 
				return false;
			}
			else if(is_maintain_batch == 1 && batch_no.trim() == '')
			{
				alert('Please enter batch number.');
				return false;
			}
			else if(is_maintain_expire_date == 1 && expire_date.trim() == '')
			//else if(is_maintain_expire_date == 1 && expire_date == '')
			{
				alert('Please enter valid expire date.');
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
						var recRow = '<tr class="table_row" id="rowCount'+rowCount+'"><td align="center">'+rowCount+'</td><td>'+obj.Product.name+'<input type="hidden" name="product_check[]" class="selected_product_id" value="'+obj.Product.id+batch_no+expire_date+'"/><input type="hidden" name="product_id[]" value="'+obj.Product.id+'"/></td><td align="center"><input type="hidden" name="batch_no[]" value="'+batch_no+'">'+batch_no+'</td><td align="center">'+obj.ChallanMeasurementUnit.name+'<input type="hidden" name="measurement_unit[]" value="'+obj.Product.challan_measurement_unit_id+'"></td><td align="center"><input type="hidden" name="quantity[]" class="p_quantity" value="'+quantity+'">'+quantity+'</td><td align="center"><input type="hidden" name="expire_date[]" class="p_expire_date" value="'+expire_date+'">'+expire_date+'</td><td align="center"><input type="text" class="full_width form-control" name="remarks[]"></td><td align="center"><button class="btn btn-danger btn-xs remove" value="'+rowCount+'"><i class="fa fa-times"></i></button></td></tr>'; 
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
		
		function isDate(txtDate)
		{
			return txtDate.match(/^d\d?\/\d\d?\/\d\d\d\d$/);
		}
		
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
			$('.remaining_qty').val('');					
			$('.remaining_qty').html('');
	        $('.product_id').val('');		
	        $('.quantity').val('');		
	        $('.batch_no').val('');		
	        $('.expire_date').val('');		
			$('.chosen').val('').trigger('chosen:updated');
			$('.add_more').val('');
			$('.batch_no').attr('readonly',false);
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
	var dates=$('.expireDatepicker').datepicker({
             'format': "M-yy",
             'startView': "year", 
             'minViewMode': "months",
             'autoclose': true
         }); 
        $('.expireDatepicker').click(function(){
            dates.datepicker('setDate', null);
        });
</script>