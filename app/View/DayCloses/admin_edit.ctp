<style>
	.width_100{
		width:100%;
	}
</style>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Create Memo'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Memo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Memo', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('thana_id', array('class' => 'form-control','id'=>'thana_id','options'=>$thana_list,'empty'=>'---- Select Thana ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('sale_type_id', array('class' => 'form-control','id'=>'sale_type_id','options'=>$sale_type_list,'empty'=>'---- Select Sale Type ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('sales_person_id', array('class' => 'form-control','id'=>'sales_person_id','options'=>$sales_person_list,'empty'=>'---- Select Sale Sales Officer ----','default'=>$existing_record['Memo']['sales_person_id'])); ?>
					<?php echo $this->Form->input('territory_id', array('class' => 'form-control','id'=>'territory_id','type'=>'hidden')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('csa_id', array('class' => 'form-control','id'=>'sales_csa_id','empty'=>'---- Select Sale CSA ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('market_id', array('class' => 'form-control','id'=>'market_id','options'=>$market_list,'empty'=>'---- Select Market ----','default'=>$existing_record['Memo']['market_id'])); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('outlet_id', array('class' => 'form-control','id'=>'outlet_id','options'=>$outlet_list,'empty'=>'---- Select Outlet Name ----','default'=>$existing_record['Memo']['outlet_id'])); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('entry_date', array('class' => 'form-control datepicker','type'=>'text')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('memo_date', array('class' => 'form-control datepicker','type'=>'text','value'=>date('d-m-Y',strtotime($existing_record['Memo']['memo_date'])))); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('memo_no', array('class' => 'form-control','type'=>'text')); ?>
				</div>
				<!--Set Product area-->
				<table class="table table-striped table-condensed table-bordered invoice_table">
				<thead>
					<tr>
						<th class="text-center" width="5%">ID</th>
						<th class="text-center">Product Name</th>
						<th class="text-center" width="12%">Unit</th>
						<th class="text-center" width="12%">Rate</th>
						<th class="text-center" width="12%">QTY</th>
						<th class="text-center" width="12%">Value</th>
						<th class="text-center" width="10%">Bonus</th>
						<th class="text-center" width="10%">Action</th>
					</tr>
				</thead>
				<tbody class="product_row_box">
				<?php
					if(!empty($existing_record)){
						$sl = 1;
						$total_price = 0;
						$gross_val = 0;
						foreach($existing_record['MemoDetail'] as $val){
							$total_price = $val['Price'] * $val['sales_qty'];
							$gross_val = $gross_val + $total_price;
				?>
					<tr>
						<th class="text-center" width="5%"><?php echo $sl?></th>
						<th class="text-center">
							<?php
								echo $this->Form->input('product_id',array('name'=>'data[MemoDetail][product_id][]','class'=>'form-control width_100 product_id','options'=>$product_list,'empty'=>'---- Select Product ----','label'=>false,'default'=>$val['product_id']));
							?>
						</th>
						<th class="text-center" width="12%">
							<input type="text" name="" class="form-control width_100 product_unit_name" value="<?=$val['MeasurementUnit']['name']?>"/>
							<input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id" value="<?=$val['measurement_unit_id']?>"/>
						</th>
						<th class="text-center" width="12%">
							<input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate <?='prate-'.$val['product_id']?>" value="<?=$val['Price']?>"/>
							<input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id" value="<?=$val['product_price_id']?>"/>
						</th>
						<th class="text-center" width="12%">
							<input type="number" name="data[MemoDetail][sales_qty][]" class="form-control width_100 min_qty" value="<?=$val['sales_qty']?>"/>
							<input type="hidden" class="combined_product" value="<?php if(isset($val['combined_product'])){ echo $val['combined_product'];}?>"/>
						</th>
						<th class="text-center" width="12%">
							<input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value <?='tvalue-'.$val['product_id']?>" value="<?=$total_price?>"/>
						</th>
						<th class="text-center" width="10%">
							<input type="text" name="" class="form-control width_100"/>
						</th>
						<th class="text-center" width="10%">
							<a href="#" class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>
							<a href="#" class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-trash"></i></a>
							<?php //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_memo'), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete')); ?>
						</th>
					</tr>
				<?php
					$sl++;
						}
					}
				?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5" align="right"><b>Total : </b></td>
						<td align="center"><input name="data[Memo][gross_value]" class="form-control width_100" type="text" id="gross_value" value="<?=$gross_val?>"/>
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="5" align="right"><b>Cash Collection : </b></td>
						<td align="center"><input name="data[Memo][cash_recieved]" class="form-control width_100" type="text" id="cash_collection"/>
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="5" align="right"><b>Credit : </b></td>
						<td align="center"><input name="data[Memo][credit_amount]" class="form-control width_100" type="text" id="credit_amount"/>
						</td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>	
			</table>
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
	
</div>
<script>
$(document).ready(function() {
	$('#thana_id').selectChain({
		target: $('#market_id'),
		value:'name',
		url: '<?= BASE_URL.'memos/get_market_list'?>',
		type: 'post',
		data:{'thana_id': 'thana_id' }
	});		
	$('#sale_type_id').selectChain({
		target: $('#sales_person_id'),
		value:'name',
		url: '<?= BASE_URL.'memos/get_sales_officer_list'?>',
		type: 'post',
		data:{'sale_type_id': 'sale_type_id' }
	});	
	$('#market_id').selectChain({
		target: $('#outlet_id'),
		value:'name',
		url: '<?= BASE_URL.'memos/get_outlet_list'?>',
		type: 'post',
		data:{'market_id': 'market_id' }
	});	
	$("body").on("change","#sales_person_id",function(){
		var sales_person_id = $(this).val();
		$.ajax({
			url: '<?=BASE_URL.'memos/get_territory_id'?>',
			type: 'POST',
			data: {sales_person_id:sales_person_id},
			success: function(response){
				var obj = jQuery.parseJSON(response);
				$("#territory_id").val(obj.territory_id);
			}
			
		});
		
	});
});
</script>
<script>
	$(document).ready(function(){
		$("body").on("change",".product_id",function(){
			/*----- make array with product list -------*/
			var product_id_list = '';
			$('.product_id').each(function(){
				if($(this).val() != ''){
					//product_id_list = $(this).val()+','+product_id_list;
					if(product_id_list.search( $(this).val() ) == -1){
						product_id_list = $(this).val()+','+product_id_list;
					}else{
						alert("This poduct already exists");
						$(this).val('').attr('selected', true)
					}
					
				}else{
					alert("Please select a product");
					return false;
				}
			});	
			var product_id = $(this).val();
			var product_box = $(this).parent().parent().parent();
			var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
			var product_unit_id = product_box.find("th:nth-child(3) .product_unit_id");
			var product_rate = product_box.find("th:nth-child(4) .product_rate");
			var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
			var product_qty = product_box.find("th:nth-child(5) .min_qty");
			var total_val = product_box.find("th:nth-child(6) .total_value");
			var combined_product = product_box.find("th:nth-child(5) .combined_product");
			
			//product_rate.addClass('p_rate_'+product_id);
			//total_val.addClass('t_value_'+product_id);
			
			var rate_class = product_rate.attr('class').split(' ').pop();
			var value_class = total_val.attr('class').split(' ').pop();

			if (rate_class.lastIndexOf('-') && value_class.lastIndexOf('-') > -1)
			{
				product_rate.removeClass(rate_class);
				total_val.removeClass(value_class);
				/*-----------*/
				product_rate.addClass('prate-'+product_id);
				total_val.addClass('tvalue-'+product_id);
			}else{
				product_rate.addClass('prate-'+product_id);
				total_val.addClass('tvalue-'+product_id);
			}
			
			$.ajax({
				url : '<?= BASE_URL.'memos/get_product_unit'?>',
				'type' : 'POST',
				data : {product_id : product_id,product_id_list:product_id_list},
				success : function(result){
					alert(result);
					var obj = jQuery.parseJSON( result );
					product_unit.val(obj.product_unit.name);
					product_unit_id.val(obj.product_unit.id);
					product_rate.val(obj.product_price.general_price);
					product_price_id.val(obj.product_price.id);
					product_qty.val(obj.product_combination.min_qty);
					combined_product.val(obj.combined_product);
				}
			});
		});
		$("body").on("click",".add_more",function(){
			var product_box = $(this).parent().parent().parent();
			var current_row = $(this).parent().parent();
			
			var row = current_row.clone(true).find("th .product_id,.product_unit_name,.product_rate,.min_qty,.total_value,.product_unit_id,.combined_product").val("").end();
			product_box.append(row);
		});
		/*------- unset session -------*/
	});
</script>
<script>
	/*--------- check combined or individual product price --------*/
		$("body").on("keyup",".min_qty",function(){
			var product_box = $(this).parent().parent();
			var product_field = product_box.find("th:nth-child(2) .product_id");
			var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
			var product_rate = product_box.find("th:nth-child(4) .product_rate");
			var product_qty = product_box.find("th:nth-child(5) .min_qty");
			var total_value = product_box.find("th:nth-child(6) .total_value");
			var combined_product = product_box.find("th:nth-child(5) .combined_product");
			var combined_product = combined_product.val();
			var min_qty = product_qty.val();
			var id = product_field.val();
			/*-----------------------------------*/
			var product_id_list = '';
			$('.product_id').each(function(){

				product_id_list = $(this).val()+','+product_id_list;
			});	
			/*-----------------------------------*/
			$.ajax({
				url : '<?= BASE_URL.'memos/get_combine_or_individual_price'?>',
				'type' : 'POST',
				data : {combined_product : combined_product,min_qty:min_qty,product_id:id,product_id_list:product_id_list},
				success : function(result){
					//alert(result);
					var obj = jQuery.parseJSON( result );
					if(obj.unit_rate != ''){
						product_rate.val(obj.unit_rate);
					}
					if(obj.total_value){
						total_value.val(obj.total_value);
					}
					$.each(obj, function( index, value ) {
						var prate = $(".prate-"+index);
						var tvalue = $(".tvalue-"+index);
						prate.val(value.unit_rate);
						tvalue.val(value.total_value);
					});
					
					var gross_total = 0;
					$('.total_value').each(function(){
						gross_total = parseInt(gross_total) + parseInt($(this).val());
					});
					$("#gross_value").val(gross_total);
				}
			});
			
			
		});
</script>
<script>
	$(document).ready(function(){
		$('body').on('click','.delete_item',function(){
			var product_box = $(this).parent().parent();
			var product_field = product_box.find("th:nth-child(2) .product_id");
			var combined_product = product_box.find("th:nth-child(5) .combined_product");
			var product_qty = product_box.find("th:nth-child(5) .min_qty");
			combined_product = combined_product.val();
			var id = product_field.val();
			var min_qty = product_qty.val();
			if(product_field.val() == ''){
				product_box.remove();
				alert('Removed this row -------');
			}else{
				$.ajax({
					url : '<?= BASE_URL.'memos/delete_memo'?>',
					'type' : 'POST',
					data : {combined_product : combined_product,product_id:id},
					success : function(result){
						if(result == 'yes'){
							product_box.remove();
						}
					}
				});
			}
		});
	/*--------------------------------*/
		$("body").on("keyup","#cash_collection",function(){
			var gross_value = parseInt($("#gross_value").val());
			var collect_cash = parseInt($(this).val());
			var credit_amount = gross_value - collect_cash;
			$("#credit_amount").val(credit_amount);
		});
	});
</script>
