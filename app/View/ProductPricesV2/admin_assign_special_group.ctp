
<style>
@media only screen and (max-width: 768px) {
    
    #market_list .checkbox{
        width:100% !important;
        float:left;
        margin:1px 0;
    }
    .search label {
        width: auto !important;
    }
   .search td table thead {
    display: none;
  }
 
}

#market_list .checkbox label{
	padding-left:20px;
	width:auto;
}
#market_list .checkbox{
	width:25%;
	float:left;
	margin:1px 0;
}

.radio input[type="radio"], .radio-inline input[type="radio"]{
    margin-left: 0px;
    position: relative;
	margin-top:8px;
}
.search label {
    width: 25%;
}
#market_list{
	padding-top:5px;
}
.market_list2 .checkbox{
	width:15% !important;
}
.market_list3 .checkbox{
	width:20% !important;
}
.box_area{
	display:none;
}
.checkbox input[type="checkbox"]{
    margin-left: 0px;
}
table label
{
	width: 70%;
}
.price_slabs label
{
	width: 45%;
}
.price_section
{
	margin-top:10px;
}
.row
{
	margin-left: 0px;
	margin-right: 0px;
}
.price_section table,.price_section table td 
{ 
    border: 1px solid black;
    border-collapse: collapse;
    height: 25px;
} 

.price_section table 
{ 
    width: 100%; 
}  
.column_10 { 
    width: 10%; 
} 

// Fixing width of second  
// column of each row  
.column_20 { 
    width: 20%; 
}
.column_70 { 
    width: 70%; 
}
.div_for_disabled_table_input
{
	background: black;
	position: absolute;
	height: 199px;
	width: 97.8%;
	opacity: 0.3;
	z-index: 2;
	top: 14px;
	display: none;
}
.table-success
{
	background-color: #c3e6cb;
	border-color: black;
}
.table-primary
{
	background-color: #b8daff;
	border-color: black;
}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-plus"></i> <?php echo __('Add New Slot'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('product_prices','admin_price_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Price List'), array('action' => "admin_price_list/$product_id"), array('class' => 'btn btn-primary', 'escape' => false)); } ?>		
				</div>
			</div>	
			<div class="box-body">				
                <?php echo $this->Form->create('ProductPrice', array('role' => 'form')); ?>

                <div class="form-group">
					<?php echo $this->Form->input('effective_date', array('type'=>'hidden','class' => 'datepicker form-control effective_date','value'=>$effective_date)); ?>
				</div>
				<div class="section all_price_section">
					<div class="price_section"  style="border:1px solid black;">
						<div class="row">
							<div class="col-xs-10">
								<input type="hidden" name="section_id[]" value="1" class="section_id">
								<table style="margin-top: 1%;">
									<tbody>
										<tr>
											<td class="column_10">
												<?php echo $this->Form->input('is_so.1', array('label'=>'<b>SO :</b>','type'=>'checkbox','class' => 'checkbox-inline is_so','div'=>false,'id'=>false)); ?>
											</td>
											<td class="column_70">
												<div id="market_list" class="input select so_special_td" style="float:left; width:80%; padding-left:20px;display: none">

				                                    <div class="selection">
				                                         <?php echo $this->Form->input('so_special_group_id.1', array('id' => 'so_special_group_id', 'label'=>false, 'class' => 'checkbox so_special_group_id', 'multiple' => 'checkbox', 'options' => $so_special_group)); ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
										<tr>
											<td class="column_10">
												<?php echo $this->Form->input('is_sr.1', array('label'=>'<b>SR :</b>','type'=>'checkbox','class' => 'checkbox-inline is_sr','div'=>false,'id'=>false)); ?>
											</td>
											<td class="column_70">
				                                <div id="market_list" class="input select sr_special_td" style="float:left; width:80%; padding-left:20px;display: none">

				                                    <div class="selection">
				                                         <?php echo $this->Form->input('sr_special_group_id.1', array('id' => 'sr_special_group_id', 'label'=>false, 'class' => 'checkbox sr_special_group_id', 'multiple' => 'checkbox', 'options' => $sr_special_group)); ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="div_for_disabled_table_input"></div>
								<button class="btn btn-info btn_process pull-right">Process</button>
								<button class="btn btn-danger btn_unprocess pull-right" disabled>Unprocess</button>
							</div>
							<div class="col-xs-2">
								
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 price_slabs" style="display: none">
								<div class="row all_slabs" style="border:1px solid black;margin-top: 10PX;margin-bottom: 10px">
									<div class="col-xs-10">
										<div class="row slab_basic_entry" style="margin-top: 1%">
											<div class="col-xs-3">
												<div class="form-group">
													<?php echo $this->Form->input('min_qty', array('type'=>'number',"required"=>"required",'class'=>'form-control min_qty','name'=>'data[min_qty][1][]')); ?>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="form-group">
													<?php echo $this->Form->input('trade_price', array('type'=>'number','step'=>'any',"required"=>"required",'class'=>'form-control trade_price','name'=>'data[trade_price][1][]')); ?>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 other_pricing">
												
											</div>
										</div>
									</div>
									<div class="col-xs-2">
										<button class="btn btn-primary pull-right add_more_slabs" style="margin-top: 5%">Add More Slab</button>
									</div>
								</div>
							</div>
							
						</div>
					</div>
					
				</div>
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary pull-right')); ?>
				<?php echo $this->Form->end(); ?>							
			</div>			
		</div>
	</div>
	
</div>
<script>
$(document).ready(function ()
{
	$("input[type='checkbox']").iCheck('destroy');
	$("input[type='radio']").iCheck('destroy');
    var i=2;



    $("body").on('click','.is_so',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$(this).parent().next().find('.so_special_td').show();
	    }
	    else
	    {
	    	$(this).parent().next().find('.so_special_td').hide();
	    	$(this).parent().next().find('.so_special_td input').prop('checked',false);
	    }
    });

    $("body").on('click','.is_sr',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$(this).parent().next().find('.sr_special_td').show();
	    }
	    else
	    {
	    	$(this).parent().next().find('.sr_special_td').hide();
	    	$(this).parent().next().find('.sr_special_td input').prop('checked',false);
	    }
    });


    $("body").on('click','.btn_process',function(e){
    	e.preventDefault();
    	$(this).attr('disabled',false);
		$(this).next().attr('disabled',true);
		$(this).parent().parent().next().find('.price_slabs').hide();
    	if(
    		$(this).prev().prev().find('.is_so').prop('checked')==false
    		&& $(this).prev().prev().find('.is_sr').prop('checked')==false
    	)
    	{
    		alert("Please Select DB,SO Or Sr");
    	}
    	else
    	{
    		if(
    			$(this).prev().prev().find('.is_so').prop('checked')==true &&
    			$(this).prev().prev().find('.so_special_group_id input:checked').length<=0
    		)
    		{
    			alert('Please Select Special Group For SO');
    		}
    		else if(
    			$(this).prev().prev().find('.is_sr').prop('checked')==true &&
    			$(this).prev().prev().find('.so_special_group_id').length<=0
    		)
    		{
    			alert('Please Select Special Group For SR');
    		}
    		else
    		{

		    	var section_id=$(this).parent().find('.section_id').val();
    			$(this).prev().show();
    			$(this).attr('disabled',true);
    			$(this).next().attr('disabled',false);
		    	$(this).prev().css('height',$(this).prev().prev().height());
		    	/*---------------------- pricing slabs create -----------------------------------*/
		    	if($(this).prev().prev().find('.so_special_group_id input:checked').length>0)
		    	{
		    		var table_so='<table class="table table-bordered" style="margin-top:10px;margin-bottom:10px">';
			    	table_so+='<tr class="table-success"><td colspan="6" class="text-center "><b>So Price</b></td></tr>';
			    	var i=1;
			    	
			    	$(this).prev().prev().find('.so_special_group_id input:checked').each(function(index,value){
			    		var category_val=$(this).val();
			    		var category_text=$(this).parent().find('label').text();
			    		if(i==1)
			    		{
			    			table_so+='<tr>';
			    		}
			    		table_so+='<td class="text-center"><b>'+category_text+' :</b></td>';
			    		table_so+='<td class="text-center"><input type="number" required step="any" name="data[so_special_group_price]['+section_id+']['+category_val+'][]" data-so_special_id="'+category_val+'"></td>';
			    		if(i==3)
			    		{
			    			table_so+='</tr>';
			    			i=1
			    		}
			    		else
			    		{
			    			i++;
			    		}
			    		
			    	});
			    	table_so+='</table>';
			    	$(this).parent().parent().next().find('.other_pricing').append(table_so);
		    	}
		    	
		    	if($(this).prev().prev().find('.is_sr').prop('checked'))
		    	{
		    		var table_sr='<table class="table table-bordered" style="margin-top:10px;margin-bottom:10px">';
			    	table_sr+='<tr class=" table-primary"><td class="text-center"><b>SR Price</b></td>\
			    	<td class="text-left" colspan="5"><input type="number" step="any" name="data[sr_price]['+section_id+'][]" class="sr_price form-control" required></td>\
			    	</tr>';
			    	if($(this).prev().prev().find('.sr_special_group_id input:checked').length>0)
		    		{
				    	var i=1;
				    	
				    	$(this).prev().prev().find('.sr_special_group_id input:checked').each(function(index,value){
				    		var category_val=$(this).val();
				    		var category_text=$(this).parent().find('label').text();
				    		if(i==1)
				    		{
				    			table_sr+='<tr>';
				    		}
				    		table_sr+='<td class="text-center"><b>'+category_text+' :</b></td>';
				    		table_sr+='<td class="text-center"><input type="number" step="any" name="data[sr_special_group_price]['+section_id+']['+category_val+'][]" data-sr_special_id="'+category_val+'" required></td>';
				    		if(i==3)
				    		{
				    			table_sr+='</tr>';
				    			i=1
				    		}
				    		else
				    		{
				    			i++;
				    		}
				    		
				    	});
				    }
			    	table_sr+='</table>';
			    	$(this).parent().parent().next().find('.other_pricing').append(table_sr);
		    	}
		    	$(this).parent().parent().next().find('.price_slabs').show();
		    	
    		}

    	}
    });

    $("body").on('click','.btn_unprocess',function(e){
    	e.preventDefault();
    	$(this).attr('disabled',true);
		$(this).prev().attr('disabled',false);
		$(this).parent().parent().next().find('.price_slabs').hide();
		$(this).prev().prev().hide();
		$(this).parent().parent().next().find('.all_slabs:not(:eq(0))').remove();
		$(this).parent().parent().next().find('.other_pricing').children().remove();
		
    });    
    $("body").on('click','.add_more_slabs',function(e){
    	e.preventDefault();
    	var html=$(this).parent().prev().html();
    	var full_price_slabs='<div class="row all_slabs" style="border:1px solid black;margin-top: 10PX;margin-bottom: 10px">\
									<div class="col-xs-10">\
										'+html+'\
									</div>\
									<div class="col-xs-2">\
										<button class="btn btn-danger pull-right remove_slabs" style="margin-top: 5%">Remove This Slab</button>\
									</div>\
								</div>\
    	';
    	$(this).parent().parent().parent().append(full_price_slabs);
    	$(this).parent().parent().parent().find('.all_slabs:last input').val('');
		
    });

    $("body").on('click','.remove_slabs',function(e){
    	e.preventDefault();
    	$(this).parent().parent().remove();
		
    });
    $('.effective_date').on('changeDate',function(){
    	var effective_date=$(this).val();
    	if(effective_date)
    	{
	    	$.ajax({
	    		url:'<?=BASE_URL ?>/product_prices_v2/get_so_sr_special_group',
	    		type: "POST",
	    		data:{'effective_date':effective_date},
	    		data_type:'JSON',
	    		success:function(response)
	    		{
	    			var res=$.parseJSON(response);
	    			$('.so_special_td>.selection').html(res.so_special);
	    			$('.sr_special_td>.selection').html(res.sr_special);
	    		}

	    	});
    	}
    });
    $('body').on('change','.min_qty',function(){
    	var min_qty=$(this).val();
    	var current_section=$(this).parent().parent().parent().parent().parent().parent().parent().parent().prev().find('table');
		$(this).parent().find('.so_product_combination_id').remove();
		$(this).parent().find('.sr_product_combination_id').remove();
    	var _this=$(this);
    	$('.min_qty').not(this).each(function()
    	{
    		var min_qty_val=$(this).val();
    		if(min_qty_val && min_qty_val==min_qty)
    		{
    			var current_section_this=$(this).parent().parent().parent().parent().parent().parent().parent().parent().prev().find('table');
    			
    			if(current_section.find('input.is_so').prop('checked')==true && current_section_this.find('input.is_so').prop('checked')==true)
    			{
    				alert("Same slab not allowed for SO");
    				_this.val('');
    				return false;
    			}
    			if(current_section.find('input.is_sr').prop('checked')==true && current_section_this.find('input.is_sr').prop('checked')==true)
    			{
    				alert("Same slab not allowed for SR");
    				_this.val('');
    				return false;
    			}
    		}
    	});
    	$.ajax({
    		url:'<?=BASE_URL ?>/product_prices_v2/get_min_qty_details',
    		type: "POST",
    		data:{'min_qty':min_qty,'product_price_id':<?php echo $product_price_id; ?> },
    		data_type:'JSON',
    		success:function(response)
    		{
    			var res=$.parseJSON(response);
    			console.log(res.sr_product_combination_id.length);

    			if(res.so_product_combination_id!=='')
    			{
	    			$(_this).parent().append('<input name="data[so_product_combination_id][1][]" required="required" class="form-control so_product_combination_id" type="hidden" id="ProductCombinationId" value ="'+res.so_product_combination_id+'">');

    			}
    			else
    			{
    				$(_this).parent().append('<input name="data[so_product_combination_id][1][]" required="required" class="form-control so_product_combination_id" type="hidden" id="ProductCombinationId" value ="">');
    			}
    			if(res.sr_product_combination_id.length >0)
    			{
    				$(_this).parent().append('<input name="data[sr_product_combination_id][1][]" required="required" class="form-control sr_product_combination_id" type="hidden" id="ProductCombinationId" value ="'+res.sr_product_combination_id+'">');
    			}
    			else
    			{
    				$(_this).parent().append('<input name="data[sr_product_combination_id][1][]" required="required" class="form-control sr_product_combination_id" type="hidden" id="ProductCombinationId" value ="">');
    			}
    			if(res.tp_price.length>0)
    			{
	    			$(_this).parent().parent().parent().next().find('.trade_price').val(res.tp_price).attr('readonly',true);
    			}
    			else
    			{
    				if(res.sr_tp_price.length>0)
	    			{
		    			$(_this).parent().parent().parent().next().find('.trade_price').val(res.sr_tp_price).attr('readonly',true);
	    			}
	    			else
    					$(_this).parent().parent().parent().next().find('.trade_price').val('').attr('readonly',false);
    			}
    			if(res.sr_price.length>0)
    			{
	    			$(_this).parent().parent().parent().parent().next().find('.sr_price').val(res.sr_price).attr('readonly',true);
    			}
    			else
    			{
    				$(_this).parent().parent().parent().parent().next().find('.sr_price').val('').attr('readonly',false);
    			}
    		}
    	})
    });
});
</script>