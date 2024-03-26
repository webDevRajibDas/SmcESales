<style>
.width_5{width: 5%}
.width_15{width: 15%}
.display_none{display:none;}
hr{margin:10px 0px;}
.policy_option1,.policy_option{
	margin-bottom:0px;
}
.discount_product, .discount_product div{
	margin-bottom:0px;
}
.div_select{
	width:500px;
}
.m_select{
	width:10%;
	margin-left:5px;
}
.search .radio label {
	width: auto;
	float:none;
	padding:0px 15px 0px 5px;
	margin:0px;
}
.radio, .checkbox {
	margin-top: 0px !important;
	margin-bottom: 0px !important;
}
.search .radio legend {
	float: left;
	margin: 5px 20px 0 0;
	text-align: right;
	width: 20%;
	display: inline-block;
	font-weight: 700;
	font-size:14px;
	border-bottom:none;
}
.radio input[type="radio"], .radio-inline input[type="radio"]{
	margin-left: 0px;
	position: relative;
	margin-top:8px;
}
</style>

<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
		
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add New'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			
			<div class="box-body">		
			<?php echo $this->Form->create('GroupWiseDiscountBonusPolicy', array('role' => 'form')); ?>
            	<div class="form-group">
					<?php echo $this->Form->input('name', array('type'=>'text','class' => 'form-control','label'=>'Policy Name:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('remarks', array('type'=>'text','class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('start_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'Start Date:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('end_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'End Date:')); ?>
				</div>
				
				
				<?php /*?><div class="form-group">
					<?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id', 'multiple' => true, 'name'=>'data[GroupWiseDiscountBonusPolicyToOffice][office_id]', 'required'=>false)); ?>
				</div><?php */?>
				
				<div class="form-group">
					<label for="office_id">Office :</label>
					<div class="input select">
					<select name="data[GroupWiseDiscountBonusPolicyToOffice][office_id][]" id="office_id" class="form-control office_id div_select" multiple="multiple">
						<?php foreach($offices as $o_key => $o_val){ ?>
							<option selected value="<?=$o_key;?>"><?=$o_val;?></option>
						<?php } ?>
					</select>
					</div>
				</div>
				
				
				<div class="form-group">
					<?php echo $this->Form->input('outlet_group_id', array('id' => 'outlet_group_id','class' => 'form-control outlet_group_id chosen div_select', 'multiple' => true, 'options' => $outlet_groups, 'name'=>'data[GroupWiseDiscountBonusPolicyToOutletGroup][outlet_group_id]', 'required'=>false)); ?>
				</div>
				
				<?php /*?><div class="form-group">
					<?php echo $this->Form->input('outlet_category_id', array('id' => 'outlet_category_id','class' => 'form-control outlet_category_id chosen', 'options' => $outlet_categories, 'multiple' => true, 'name'=>'data[GroupWiseDiscountBonusPolicyToOutletCategory][outlet_category_id]', 'required'=>false)); ?>
				</div><?php */?>
				
				<div class="form-group">
					<label for="office_id">Outlet Category :</label>
					<div class="input select">
					<select name="data[GroupWiseDiscountBonusPolicyToOutletCategory][outlet_category_id][]" id="outlet_group_id" class="form-control chosen div_select" multiple="multiple">
						<?php foreach($outlet_categories as $o_key => $o_val){ ?>
							<option selected value="<?=$o_key;?>"><?=$o_val;?></option>
						<?php } ?>
					</select>
					</div>
				</div>
			
				
				<div class="form-group">
					<div class="form-group">
						<label>Product :</label>
						<?php echo $this->Form->input('product_id', array('class' => 'form-control','id'=>'policy_product_id1', 'empty'=>'---- Select Product ----', 'label'=>false, 'options' => $products,'name'=>'data[GroupWiseDiscountBonusPolicyProduct][policy_product_id][]', 'onChange'=>'add_option_product(this,1)',)); ?>
						<button type="button" class="add_product_button hide_all">Add More</button>
					</div>
					<span class="input_products_wrap"></span>
				</div>
				
				<div class="submit">
					<button onClick="showOptions()" type="button">Set</button>
					<button onClick="window.location.reload()" type="button">Reset</button>
				</div>
					
				<hr></hr>
				
				<div class="policy_options display_none"> 
					<div class="form-group policy_option1">
						
						<div class="form-group">
							<?php echo $this->Form->input('policy_type', array('type'=>'select', 'label'=>'Policy Option', 'class' => 'form-control','id'=>'policy_type1', 'onChange'=>'showBonusOrDiscount(this.value, 1)', 'options' => $policy_types, 'name'=>'data[GroupWiseDiscountBonusPolicyOption][1][policy_type]',)); ?>
							<button type="button" onClick="addOption()" class="add_option_button">Add More Option</button>
						</div>
						
						<div class="form-group required">
							<?php echo $this->Form->input('min_qty', array('type'=>'number', 'label'=>'Min Qty', 'class' => 'form-control','id'=>'min_qty1', 'name'=>'data[GroupWiseDiscountBonusPolicyOption][1][min_qty]','required' => true)); ?>
						</div>
						
						<div class="bonus_product display_none">
							<div class="option_1_product_wraps">
								<div class="form-group">
								<?php echo $this->Form->input('bonus_product_id', array('type'=>'select', 'label'=>'Bonus Product', 'class' => 'form-control', 'empty'=>'---- Select Product ----', 'id'=>'bonus_product_id1', 'name'=>'data[GroupWiseDiscountBonusPolicyOption][1][bonus_product_id][]','options' => $products, 'onChange'=>'get_product_units(this.value,1,1)')); ?>
								<div class="option1_bonus_product1_wrap">
									<select class="form-control m_select" name="data[GroupWiseDiscountBonusPolicyOption][1][measurement_unit_id][]">
										<option value="">Select Measurement</option>
									</select>
								</div>
								<?php echo $this->Form->input('bonus_qty', array('type'=>'number', 'label'=>false, 'class' => 'form-control m_select', 'id'=>'bonus_qty1', 'placeholder'=>'Bonus Qty', 'name'=>'data[GroupWiseDiscountBonusPolicyOption][1][bonus_qty][]')); ?>
								<button type="button" onClick="addBProduct(1)" class="add_option_button">Add More</button>
								</div>
							</div>
						</div>
						
						<div class="discount_product">
							<div class="form-group">
								<?php echo $this->Form->input('discount_amount', array('type'=>'text', 'label'=>'Discount Amount', 'class' => 'form-control','id'=>'discount_amount1', 'name'=>'data[GroupWiseDiscountBonusPolicyOption][1][discount_amount]')); ?>
								<?php echo $this->Form->input('disccount_type', array('type'=>'select', 'label'=> false, 'class' => 'form-control width_5','id'=>'disccount_type1', 'name'=>'data[GroupWiseDiscountBonusPolicyOption][1][disccount_type]','options' => $disccount_types)); ?>
							</div>
							<div class="form-group option1_products_wrap"></div>
						</div>
						
					</div>
				</div>
			
			<input type="hidden" id="total_product" value="1">
			
			<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>


<script>
jQuery(document).ready(function(){
	$("input[type='checkbox']").iCheck('destroy');
    $("input[type='radio']").iCheck('destroy');
	jQuery(".chosen").chosen();
	jQuery(".office_id").chosen();
	jQuery(".office_id").data("placeholder", "Select Offices...").chosen();
});

var x = 1; 
function addOption()
{
	max_fields = 50;	
	if(x < max_fields)
	{ 
		x++;             
		
		var total_product = $('#total_product').val();
		
		//alert(total_product);
		
		var html = '<div class="form-group policy_option policy_option'+x+'">\
	<hr></hr><div class="form-group">\
		<div class="input select">\
			<label>Policy Option</label>\
			<select name="data[GroupWiseDiscountBonusPolicyOption]['+x+'][policy_type]" class="form-control" onchange="showBonusOrDiscount(this.value, '+x+')">\
				<option value="0">Only Discount</option>\
				<option value="1">Only Bonus</option>\
				<option value="2">Discount and Bonus</option>\
				<option value="3">Discount or Bonus</option>\
			</select>\
		</div>\
		<button type="button" onclick="removeOption('+x+')" class="add_option_button">Remove</button>\
	</div>\
	<div class="form-group required">\
		<div class="input number">\
			<label>Min Qty</label>\
			<input name="data[GroupWiseDiscountBonusPolicyOption]['+x+'][min_qty]" class="form-control" required type="number">\
		</div>\
	</div>\
	<div class="bonus_product display_none">\
		<div class="option_'+x+'_product_wraps">\
			<div class="form-group">\
				<div class="input select">\
					<label>Bonus Product</label>\
					<select onchange="get_product_units(this.value,'+x+',1)" name="data[GroupWiseDiscountBonusPolicyOption]['+x+'][bonus_product_id][]" class="form-control">\
						<option value="">---- Select Product ----</option>\
						<?=$product_list?>\
					</select>\
					<div class="option'+x+'_bonus_product1_wrap"><select class="form-control m_select" name="data[GroupWiseDiscountBonusPolicyOption]['+x+'][measurement_unit_id][]"><option value="">Select Measurement</option></select></div>\
					<input name="data[GroupWiseDiscountBonusPolicyOption]['+x+'][bonus_qty][]" class="form-control m_select"  type="number" placeholder="Bonus Qty">\
					<button type="button" onClick="addBProduct('+x+')" class="add_option_button">Add More</button>\
				</div>\
			</div>\
		</div>\
	</div>\
	<div class="discount_product">\
		<div class="form-group">\
			<div class="input text">\
				<label>Discount Amount</label>\
				<input name="data[GroupWiseDiscountBonusPolicyOption]['+x+'][discount_amount]" class="form-control" type="text">\
			</div>\
			<div class="input select">\
				<select name="data[GroupWiseDiscountBonusPolicyOption]['+x+'][disccount_type]" class="form-control width_5">\
					<option value="0">%</option>\
					<option value="1">Tk</option>\
				</select>\
			</div>\
		</div>\
		<div class="form-group option1_products_wrap">';
	var i;
	for (i = 1; i <= total_product; i++) 
	{
		option_product = $('.option1_slap_set'+i+' select.option_product').html();
		option_slab = $('.option1_slap_set'+i+' select.option_slab').html();
	html+= '<div class="option'+x+'_slap_set'+i+'">\
				<div class="form-group">\
					<label>Discount Product :</label>\
					<select class="form-control product_common_class" name="data[GroupWiseDiscountBonusPolicyOptionPriceSlab]['+x+'][discount_product_id][]">\
						'+option_product+'\
					</select>\
					<select class="form-control width_15" name="data[GroupWiseDiscountBonusPolicyOptionPriceSlab]['+x+'][slab_id][]">\
						'+option_slab+'\
					</select>\
				</div>\
			</div>';
	}		
	html+= '</div>\
		</div>\
	</div>';
		
		$('.policy_options').append(html); 
	}
}

function removeOption(id){
	$('.policy_option'+id).remove(); 
}



function showOptions(){
	$('.policy_options').show();
	$('.hide_all').hide();
}

function showBonusOrDiscount(value, option_id){
	$('.policy_option'+option_id+' .bonus_product').hide();
	$('.policy_option'+option_id+'  .discount_product').hide();
	
	if(value==0){
		$('.policy_option'+option_id+' .discount_product').show();
	}else if(value==1){
		$('.policy_option'+option_id+' .bonus_product').show();
	}else{
		$('.policy_option'+option_id+' .bonus_product').show();
		$('.policy_option'+option_id+'  .discount_product').show();
	}
}

$(document).ready(function (){
	var max_fields      = 15; 
    var product_wrapper     = $(".input_products_wrap"); 
    var add_product_button  = $(".add_product_button"); 
	
	
    
    var x = 1; 
    $(add_product_button).click(function(e){ 

        e.preventDefault();
        if(x < max_fields){ 
            x++; 
						            
			$(product_wrapper).append('<div class="slap_set'+x+'"><div class="form-group"><label>Product :</label><select class="form-control" name="data[GroupWiseDiscountBonusPolicyProduct][policy_product_id][]" onchange="add_option_product(this, '+x+')" id="policy_product_id'+x+'" required><option value="">---- Select Product -----</option>'+'<?=$product_list; ?>'+'</select><a href="#" class="remove_product_field btn btn-primary hide_all btn-xs">Remove</a></div>'); 
			
			$('#total_product').val(x);
			
        }
		
    });

    
    $(product_wrapper).on("click",".remove_product_field", function(e){ 
        e.preventDefault(); 
		$(this).parent('div').remove();
		$('.option1_slap_set'+x).remove(); 
		x--;
    });
	
});

function add_option_product(sel, x)
{
	var product_id = sel.value;
	var product_name = sel.options[sel.selectedIndex].text;
	
	var max_fields      = 10; 
		
	//var x = 1;
	if(x < max_fields)
	{ 
           
		var slab_result = null; 
		$.ajax({
			url:  "<?php echo BASE_URL; ?>admin/group_wise_discount_bonus_policies/get_slab_list",
			type:"POST",
			data:{product_id:product_id},
			success: function(result){
				console.log(result);
				//alert(result);
				//slab_result = 123;
				//$("#option"+option_id+"_slab"+slab_id).html(result);+
				//alert(result);
				
				
				$('.option1_slap_set'+x).remove(); 
				$(".option1_products_wrap").append('<div class="option1_slap_set'+x+'"><div class="form-group"><label>Discount Product :</label><select class="form-control option_product" name="data[GroupWiseDiscountBonusPolicyOptionPriceSlab][1][discount_product_id][]" required><option value="'+product_id+'">'+product_name+'</option>'+'</select><select class="form-control width_15 option_slab" name="data[GroupWiseDiscountBonusPolicyOptionPriceSlab][1][slab_id][]">'+result+'</select></div>');
				
				
				//x++;
			}
		});
				
		//alert(slab_result);
				 
			
		//get_product_product_slab(product_id)
	
	}
	
}

function get_product_product_slab(product_id)
{
	$.ajax({
			url:  "<?php echo BASE_URL; ?>admin/group_wise_discount_bonus_policies/get_slab_list",
			type:"POST",
			data:{product_id:product_id},
			success: function(result){
				console.log(result);
				$("#option"+option_id+"_slab"+slab_id).html(result);
			}
		});
}

</script>




<script>
//FOR DISCOUNT PRODUCT AND SLAB
$(document).ready(function (){
	
			
	$("body").on("change",".product_common_class",function(){
		var selected_product_id = $(this).attr('id');
		var att_id_text = selected_product_id.split("_");
		//alert(att_id_text[0]);
		var att_id_text0=att_id_text[0];
		var att_id_text1=att_id_text[1];
		var option_id = att_id_text0.toString().slice(6);
		var slab_id = att_id_text1.toString().slice(7);

		var product_id = $(this).val();
		$.ajax({
			url:  "<?php echo BASE_URL; ?>admin/group_wise_discount_bonus_policies/get_slab_list",
			type:"POST",
			data:{product_id:product_id},
			success: function(result){
				console.log(result);
				$("#option"+option_id+"_slab"+slab_id).html(result);
			}
		});
	});
});


$(document).ready(function (){
	var max_fields      = 15; 
    var discount_products_wrap1     = $(".discount_products_wrap1"); 
    var add_discount_product_button  = $(".add_discount_product_button"); 
    
    var x = 1; 
    $(add_discount_product_button).click(function(e){ 
        e.preventDefault();
        if(x < max_fields){ 
            x++;             
			$(discount_products_wrap).append('<div class="slap_set"><div class="form-group"><label>Discount Product :</label><select class="form-control product_common_class" name="data[GroupWiseDiscountBonusPolicyOptionPriceSlab][discount_product_id]['+x+'][]" id="option'+x+'_product'+x+'" required><option value="">---- Select Product -----</option>'+'<?=$product_list; ?>'+'</select><select class="form-control width_15" name="data[GroupWiseDiscountBonusPolicyOptionPriceSlab][slab_id]['+x+'][]" id="option'+x+'_slab'+x+'"><option value="">---- Select Slab -----</option></select><a href="#" class="remove_discount_product_field1 btn btn-primary btn-xs">Remove</a></div>'); 
        }
		
    });
	
    
    $(discount_products_wrap1).on("click",".remove_product_field1", function(e){ 
        e.preventDefault(); $(this).parent('div').remove(); x--;
    });

});


//add for bonus product unit
function get_product_units(product_id,x,p)
{
	$.ajax({
		url:  "<?php echo BASE_URL; ?>admin/group_wise_discount_bonus_policies/get_product_units",
		type:"POST",
		data:{product_id:product_id},
		success: function(result){
			console.log(result);			
			$(".option"+x+"_bonus_product"+p+"_wrap").html('<select class="form-control m_select" name="data[GroupWiseDiscountBonusPolicyOption]['+x+'][measurement_unit_id][]">'+result+'</select>');
		}
	});
}

var pc = 1;
var temp_option_ids = [];
function addBProduct(o)
{
	max_fields = 50;	
	
	if(jQuery.inArray(o, temp_option_ids) <= -1)
	{
		temp_option_ids.push(o);
	}
	pc=($(".form-group.search.b_product"+o).length+2);
	function pc_checking(pc_c)
	{
		if($('.b_product'+o+'.pc_'+pc_c).length>0)
		{
			pc_c=pc_c+1;
			pc_checking(pc_c);
		}
		else
			pc= pc_c;
	}
	pc_checking(pc);
	if(pc < max_fields)
	{           
		
		html = '<div class="form-group search hidden b_product'+o+' pc_'+pc+'">\
					<div class="input radio">\
						<fieldset>\
							<legend>Relation Type</legend>\
							<input type="radio" name="data[GroupWiseDiscountBonusPolicyOption]['+o+'][relation]" value="0" checked="checked">\
							<label>OR</label>\
							<input type="radio" name="data[GroupWiseDiscountBonusPolicyOption]['+o+'][relation]" value="1">\
							<label>AND</label>\
						</fieldset>\
					</div>\
				</div>\
	<div class="form-group b_product'+o+' pc_'+pc+'">\
		<div class="input select">\
			<label>Bonus Product</label>\
			<select onchange="get_product_units(this.value,'+o+','+pc+')" name="data[GroupWiseDiscountBonusPolicyOption]['+o+'][bonus_product_id][]" class="form-control">\
				<option value="">---- Select Product ----</option>\
				<?=$product_list?>\
			</select>\
			<div class="b_product'+o+' option'+o+'_bonus_product'+pc+'_wrap"><select class="form-control m_select" name="data[GroupWiseDiscountBonusPolicyOption]['+o+'][measurement_unit_id][]"><option value="">Select Measurement</option></select></div>\
			<div class="input number"><input name="data[GroupWiseDiscountBonusPolicyOption]['+o+'][bonus_qty][]" class="form-control m_select" id="bonus_qty'+o+'" placeholder="Bonus Qty" type="number"></div>\
			<button type="button" onclick="removeBProduct('+o+','+pc+')" class="add_option_button">Remove</button>\
		</div>\
	</div>';
		
		$('.option_'+o+'_product_wraps').append(html); 
	}else{
		alert('Maximum two bonus products are allowed!');	
	}
}

function removeBProduct(o,pc){
	$('.b_product'+o+'.pc_'+pc).remove(); 
}


Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
</script>