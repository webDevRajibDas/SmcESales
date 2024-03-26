<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('New DO Challan'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> DO Challan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Challan', array('role' => 'form')); ?>
			<div class="form-group">
				<?php echo $this->Form->input('receiver_store_id', array('id'=>'receiver_store_id','class' => 'form-control','empty'=>'---- Select Receiver ----','options'=>$receivers,'required'=>true)); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('requisition_id', array('label'=>'DO No.','id'=>'requisition_id','class' => 'form-control requisition_id','empty'=>'---- Select DO ----','required'=>true)); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('remarks', array('class' => 'form-control')); ?>
			</div>			
			<div class="form-group">
				<?php echo $this->Form->input('Requisition.status', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is DO Close :</b>','value'=>2)); ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('inventory_status_id', array('id'=>'inventory_status_id','class' => 'form-control','type'=>'hidden','value'=>1)); ?>
			</div>
				
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Product Name</th>
						<th class="text-center">Batch No.</th>
						<th class="text-center">Expire Date</th>
						<th class="text-center">Remaining <br/>Quantity In DO</th>
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
						<td width="12%" align="center">
							<?php echo $this->Form->input('batch_no', array('id' => 'batch_no','label'=>false,'type'=>'select','class' => 'full_width form-control batch_no','empty'=>'---- Select Batch ----')); ?>
						</td>
						<td width="12%" align="center">
							<?php echo $this->Form->input('expire_date', array('id' => 'expire_date','label'=>false,'type'=>'select','class' => 'full_width form-control expire_date','empty'=>'---- Select Expire Date ----')); ?>
						</td>
						<td width="12%" align="center">
							<span class="remaining_quantity"></span>
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
						<th class="text-center" width="5%">ID.</th>
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
                <tbody class="tbodyData"></tbody>
				<tfoot>
					<tr>
						<td colspan="6" align="right"><b>Total Quantity : </b></td>
						<td align="center"><b><span class="total_quantity"></span></b></td>
						<td></td>
						<td></td>
					</tr>																
				</tfoot>	
			</table>
			</br>
			<?php echo $this->Form->submit('Save & Submit', array('class' => 'btn btn-large btn-primary save', 'div'=>false, 'name'=>'save', 'disabled')); ?>
            <?php echo $this->Form->submit('Draft', array('class' => 'btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft')); ?>
			<?php echo $this->Form->end(); ?>			
			</div>
		</div>			
	</div>
</div>
<script>		
	$(document).ready(function(){
        $.ajaxSetup ({
            cache: false
        });
		
		$('#receiver_store_id').selectChain({
			target: $('#requisition_id'),
			value:'title',
			url: '<?= BASE_URL.'do_challans/get_do_list';?>',
			type: 'post',
			data:{'receiver_store_id': 'receiver_store_id' }
		});
		
		$('#requisition_id').selectChain({
			target: $('#product_id'),
			value:'title',
			url: '<?= BASE_URL.'do_challans/get_do_product_list';?>',
			type: 'post',
			data:{'requisition_id': 'requisition_id' }

		});

        
        var is_maintain_batch = 1;
		var is_maintain_expire_date = 1;
		$('#product_id').change(function(){
			var requisition_id = $('.requisition_id').val();
			var product_id = $(this).val();
			var batch_no = $('.batch_no').val();
			var expire_date = $('.expire_date').val();
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
							url: '<?php echo BASE_URL;?>do_challans/get_inventory_details',
							data: 'requisition_id='+requisition_id+'&product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date,
							cache: false,
							success: function(response){
								//alert(response);
								var obj = jQuery.parseJSON(response);
								$('.remaining_qty').val(obj.do_quantity);
								$('.remaining_quantity').html(obj.do_quantity);
								$('.stock_qty').val(obj.stock_quantity);
								$('.stock_quantity').html(obj.stock_quantity);
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
			url: '<?= BASE_URL.'current_inventories/get_batch_list';?>',
			type: 'post',
			data:{'product_id': 'product_id','inventory_status_id':'inventory_status_id','with_stock':true }
		});
		
		$('#batch_no').change(function(){
			var requisition_id = $('.requisition_id').val();
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
						url: '<?php echo BASE_URL;?>do_challans/get_inventory_details',
						data: 'requisition_id='+requisition_id+'&product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date,
						cache: false,
						success: function(response){
							var obj = jQuery.parseJSON(response);
							$('.remaining_qty').val(obj.do_quantity);
							$('.remaining_quantity').html(obj.do_quantity);
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
			data:{'product_id': 'product_id','batch_no': 'batch_no','inventory_status_id':'inventory_status_id' }
		});
		
		$('#expire_date').change(function(){
			var requisition_id = $('.requisition_id').val();
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
					url: '<?php echo BASE_URL;?>do_challans/get_inventory_details',
					data: 'requisition_id='+requisition_id+'&product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date,
					cache: false,
					success: function(response){
						var obj = jQuery.parseJSON(response);
						$('.remaining_qty').val(obj.do_quantity);
						$('.remaining_quantity').html(obj.do_quantity);
						$('.stock_qty').val(obj.stock_quantity);
						$('.stock_quantity').html(obj.stock_quantity);
					}
				});
			}
		});
		
		
		var rowCount = 1;
		$(".add_more").click(function() {
			
		 	var requisition_id = $('.requisition_id').val();
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
			}else if(!quantity.match(/^\d+(\.\d{1,2})?$/) || parseFloat(quantity) <= 0.00)
			{
				alert('Please enter valid quantity. Ex. : 100 or 100.00');
				$('.quantity').val('');
				return false;
			}else if(parseFloat(quantity) > parseFloat($('.remaining_qty').val())){
				alert('Quantity should be less then equal Remaining Quantity In DO.');
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
						var recRow = '<tr class="table_row" id="rowCount'+rowCount+'"><td align="center">'+rowCount+'</td><td><input type="hidden" name="selected_product_id[]" class="selected_product_id" value="'+obj.Product.id+batch_no+expire_date+'"/>'+obj.Product.name+'<input type="hidden" name="product_id[]" value="'+obj.Product.id+'"/><td align="center"><input type="hidden" name="batch_no[]" value="'+batch_no+'">'+batch_no+'</td></td><td align="center"><input type="hidden" name="expire_date[]" value="'+expire_date+'">'+expire_date+'</td><td align="center">'+obj.ChallanMeasurementUnit.name+'<input type="hidden" name="measurement_unit[]" value="'+obj.Product.challan_measurement_unit_id+'"></td><td align="center"><input type="hidden" name="quantity[]" class="p_quantity" value="'+quantity+'">'+quantity+'</td><td align="center"><input type="text" class="full_width form-control" name="remarks[]"></td><td align="center"><button class="btn btn-danger btn-xs remove" value="'+rowCount+'"><i class="fa fa-times"></i></button></td></tr>';
						$('.invoice_table').append(recRow);
						clear_field();
						
						var total_quantity = set_total_quantity();
						if(total_quantity>0)
						{
							$('.submit').prop('disabled', false);
						}
					}
				});
				
			}
		});
		
		
		$(document).on("click",".remove",function() {
			var removeNum = $(this).val();
			$('#rowCount'+removeNum).remove();
			var total_quantity = set_total_quantity();
			if(total_quantity<1)
			{
				$('.submit').prop('disabled', true);
			}
		});

	
		$('.submit').prop('disabled', true);
		
		function clear_field() {
	        $('.product_id').val('');
	        $('.quantity').val('');
	        $('.batch_no').val('');
			$('.product_qty').html('');
			$('.qty').val('');
			$('.remaining_quantity').html('');
			$('.remaining_qty').val('');
			$('.stock_quantity').html('');
			$('.stock_qty').val('');
			$('.chosen').val('').trigger('chosen:updated');
			$('.add_more').val('');
	    }
   		
   		function set_total_quantity() {
	        var sum = 0;
	        var num = 1;
			$('.table_row').each(function() {
				var table_row = $(this);
				var total_quantity = table_row.closest('tr').find('.p_quantity').val();
				sum += parseInt(total_quantity);
				$(this).find("td:first").html(num++);
			});
			
			$('.total_quantity').html(sum);
			return sum;
	    }


        $('body').on('change','#requisition_id',function(){
			var requisition_id=$(this).val();
			$.ajax({
				url:"<?=BASE_URL?>/do_challans/get_all_product_with_qty",
				type:"post",
				data:{'requisition_id':requisition_id},
				success: function(res) {
                    var data = $.parseJSON(res);
					//console.log(data);
                    var tblHtml = '';
                    var totalQty = 0;
                    $.each(data, function(index, itemData) {

                        if(itemData.Product.product_type_id==3 ||itemData.Product.product_type_id==2 ){
                            $('.draft').prop('disabled', false);
                            const emptyVal = '';
                            const remark = (itemData.RequisitionDetail.remarks !== null && itemData.RequisitionDetail.remarks !== undefined) ? itemData.RequisitionDetail.remarks : emptyVal;

                            var amountTotal = itemData.RequisitionDetail.qty;
                            totalQty += parseInt(amountTotal);

                            tblHtml += '<tr class="text-center">';
                            tblHtml += '<input type="hidden" name="product_id[]" value="'+ itemData.RequisitionDetail.product_id +'">'
                            tblHtml += '<input type="hidden" name="measurement_unit[]" value="'+ itemData.RequisitionDetail.measurement_unit_id +'">'

                            tblHtml += '<input type="hidden" name="batch_no[]" value="'+ itemData.RequisitionDetail.batch_no +'">'
                            tblHtml += '<input type="hidden" name="expire_date[]" value="'+ itemData.RequisitionDetail.expire_date +'">'

                            tblHtml += '<td>' + itemData.RequisitionDetail.product_id + '</td>'
                            tblHtml += '<td>' + itemData.Product.name + '</td>'
                            tblHtml += '<td><select  name="batch_no[]" id="batch_no_do_data"></select><div></div></td>'
                            tblHtml += '<td><select name="expire_date[]" id="'+ index+101 +'" class="expire_date_data"></select><div></div></td>'
                            tblHtml += '<td><input type="hidden" name="measurement_unit_id[]" value="'+ itemData.MeasurementUnit.measurement_unit_id +'">'+ itemData.MeasurementUnit.name +'</td>'
                            tblHtml += '<td>' + itemData.RequisitionDetail.qty + '<input type="hidden" name="quantity[]" value="'+ itemData.RequisitionDetail.qty +'"></td>'
                            tblHtml += '<td><input type="text" class="" value="'+remark +'" name="remarks[]" placeholder="Remarks Here"></td>'
                            tblHtml += '<td><button class="btn btn-danger btn-xs remove"><i class="glyphicon glyphicon-remove"></i></button></td>'
                            tblHtml += '</tr>';
                        }else {
                            const var2 = '';
                            const remark = (itemData.RequisitionDetail.remarks !== null && itemData.RequisitionDetail.remarks !== undefined) ? itemData.RequisitionDetail.remarks : var2;
                            var qty = itemData.RequisitionDetail.qty;
                            totalQty += parseInt(qty);
                            tblHtml += '<tr class="text-center">';
                            tblHtml += '<td> <input type="hidden" name="product_id[]" value="'+ itemData.RequisitionDetail.product_id +'">' + itemData.RequisitionDetail.product_id + '</td>'
                            tblHtml += '<td>' + itemData.Product.name + '</td>'
                            tblHtml += '<td><select  name="batch_no[]" id="batch_no_do_data"></select><div></div></td>'
                            tblHtml += '<td><select name="expire_date[]" id="'+ index+101 +'" class="expire_date_data"></select><div></div></td>'
                            tblHtml += '<td><input type="hidden" name="measurement_unit_id[]" value="'+ itemData.MeasurementUnit.measurement_unit_id +'">'+ itemData.MeasurementUnit.name +'</td>'
                            tblHtml += '<td><input type="text" class="p_quantity" name="quantity[]"  value="' + itemData.RequisitionDetail.qty +'"> </td>'
                            tblHtml += '<td><input type="text" class="" value="'+remark +'" name="remarks[]" placeholder="Remarks Here"></td>'
                            tblHtml += '<td><button class="btn btn-danger btn-xs remove"><i class="glyphicon glyphicon-remove"></i></button></td>'
                            tblHtml += '</tr>';

                        }
                    });
                    //$('tbody.tbodyData').empty();
                    $('tbody.tbodyData').html(tblHtml);
                    $('span.total_quantity').html(totalQty);
                    get_batch_list()
				}
    
			});
		});
  
		function get_batch_list() {
            $('tbody.tbodyData tr').each(function () {
                var currentRow = $(this);
                var pID = currentRow.find('td:eq(0)').text();
                $.ajax({
                    url: '<?= BASE_URL . 'current_inventories/get_batch_list';?>',
                    type: 'post',
                    data: {'product_id': pID, 'inventory_status_id': 1, 'with_stock': true},
                    success: function (data) {
                        var option = '';
                        var response = $.parseJSON(data);
                        //console.log(response);
                        if (response === undefined || response.length > 0) {
                            for (x in response) {
                                option += "<option value='" + response[x].id + "''>" + response[x].title + '</option>';
                            }
                        }else{
                            for (x in response) {
                                option += "<option value='" + response[x].id + "''>" + response[x].title + '</option>';
                            }
                        }
                        $('select#batch_no_do_data').html(option);
                        
                    }

                });
            });

		}


		$('body').on('change','select#batch_no_do_data',function(){
            var batch_no = $(this).val();
            $('tbody.tbodyData tr').each(function () {
                var currentRow = $(this);
                var product_id = currentRow.find('td:eq(0)').text();
                $.ajax({
                    url: '<?= BASE_URL . 'current_inventories/get_expire_date_list';?>',
                    type: 'post',
                    data: {'product_id': product_id, 'batch_no': batch_no, 'inventory_status_id': 1},
                    success: function (data) {
                        var option = '';
                        var response = $.parseJSON(data);
                        if (response === undefined || response.length > 0) {
                            for (x in response) {
                                option += "<option value='" + response[x].id + "''>" + response[x].title + '</option>';
                            }
                        }else{
                            for (x in response) {
                                option += "<option value='" + response[x].id + "''>" + response[x].title + '</option>';
                            }
                        }
                        $('select.expire_date_data').html(option);
                    }
                });
            });
        });


		$('body').on('change','select.expire_date_data',function(){
            var id =$(this).attr('id');
            var requisition_id = $('.requisition_id').val();
            var currentRow = $(this).closest('tr');
            var product_id = currentRow.find('td:eq(0)').text();
			var batch_no = $("select#batch_no_do_data").val();
			var expire_date = $(this).val();
            //console.log(product_id,requisition_id,batch_no,expire_date)

			if(product_id == '') {
				alert('Please select any product.');
				return false;
			}else if(is_maintain_batch == 1 && batch_no == '') {
				alert('Please select any Batch.');
				return false;
			}else{
				
				$.ajax({
					type: "POST",
					url: '<?php echo BASE_URL;?>do_challans/get_inventory_details',
					data: 'requisition_id='+requisition_id+'&product_id='+product_id+'&batch_no='+batch_no+'&expire_date='+expire_date,
					cache: false,
					success: function(response){
						var obj = jQuery.parseJSON(response);
						var label="<p style='font-size:11px; text-align:left;margin: 5px 0px 0px 0px;'>Do Quantity: <b class='do_find_qty'>"+obj.do_quantity+"</b></p>";
						$("#"+id).parent().prev().find('div').html(label);
						label ="<p style='font-size:11px;text-align:left;margin: 5px 0px 0px 0px;'>Stock Quantity: <b class='stock_find_qty'>"+obj.stock_quantity+"</b></p>";
						if(obj.do_quantity>obj.stock_quantity)
						{
							alert('Stock Quantity should be greater than DO quantity');
							$(this).val(obj.do_quantity);
							$(".draft").prop('disabled',false);
						}
						else
						{
							$(".draft").prop('disabled',false);
						}
						$("#"+id).next().html(label);
						$("#"+id).parent().prev().prev().find('.selected_product_id').val(product_id+batch_no+expire_date);
					}
				});
			}
		});

		$("body").on('blur','.p_quantity',function(){
			var quantity =parseFloat( $(this).val());
			var stock_qty = parseFloat($(this).parent().prev().prev().children().find('.stock_find_qty').text());
			var do_quantity = parseFloat($(this).parent().prev().prev().prev().children().find('.do_find_qty').text());
			
			if(quantity > do_quantity)
			{
				alert('Quantity shuld be match with DO quantity');
				$(this).val(do_quantity);
				$(".draft").prop('disabled',true);
			}
			else if(quantity>stock_qty ) {
				alert('Quantity shuld be match with stock quantity');
				$(this).val(0);
				$(".draft").prop('disabled',true);
			}
			else{
				$(".draft").prop('disabled',false);

			}
			
		});
		$(".draft").click(function(e){

           let batch_no_do = $('.batch_no_do').val();
           let expire_date_do = $('.expire_date_do').val();

            if ( batch_no_do!= ''){
                $('.batch_no_do').prop('required',false);
            }

			$(".do_find_qty").each(function(){
				var do_quantity=parseFloat($(this).text());
				var stock_qty=parseFloat($(this).parent().parent().parent().next().children().children().find('.stock_find_qty').text());
				var quantity = parseFloat($(this).parent().parent().parent().next().next().next().find('.p_quantity').val());
				var sl=$(this).parent().parent().parent().prev().prev().text();

				if(quantity > do_quantity) {
					alert('Quantity should be match with DO quantity in line no '+sl);
					$(this).val(do_quantity);
					e.preventDefault();
					$(".draft").prop('disabled',true);
				}
				else if(quantity>stock_qty )
				{
					alert('Quantity shuld be match with stock quantity line no '+sl);
					$(this).val(0);
					e.preventDefault();
					$(".draft").prop('disabled',true);
				}
				else{
					return;

				}
				
			});
		});

		$(".draft").prop('disabled',true);
	});

</script>