<style type="text/css">
	.width_15{width: 15%};
</style>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Combination'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Combination List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Combination', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control','required'=>true)); ?>
                    
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('type'=>'text','class' => 'datepicker form-control effective_date','required'=>true)); ?>
				</div>
				<!-- <div class="form-group">
					<?php //echo $this->Form->input('create_for', array('class' => 'form-control create_for','required'=>true,'options'=>$create_for,'empty'=>'--- Select ---')); ?>
				</div>
				<div class="form-group so_outlet_category">
					<?php //echo $this->Form->input('reffrence_id', array('class' => 'form-control','required'=>true,'options'=>$outlet_categories,'empty'=>'--- Select ---','disabled')); ?>
				</div>
				<div class="form-group sr_outlet_category">
					<?php //echo $this->Form->input('reffrence_id', array('class' => 'form-control','required'=>true,'options'=>$sr_outlet_categories,'empty'=>'--- Select ---','disabled')); ?>
				</div>
				<div class="form-group so_special_group">
					<?php //echo $this->Form->input('reffrence_id', array('type'=>'select','class' => 'form-control','required'=>true,'empty'=>'--- Select ---','disabled')); ?>
				</div>
				<div class="form-group sr_special_group">
					<?php //echo $this->Form->input('reffrence_id', array('type'=>'select', 	'class' => 'form-control','required'=>true,'empty'=>'--- Select ---','disabled')); ?>
				</div> -->
                <br/>
				<div class="form-group">
					<label>Product :</label>
					
                    <?php 
					
						echo $this->Form->input('product_id', array('class' => 'form-control product_common_class','id'=>'product_id1','empty'=>'---- Select Product ----','label'=>false,'name'=>'data[ProductCombination][product_id][]')); ?>
					<?php
					//echo $this->Form->input('slab_id', array('class' => 'form-control width_15','id'=>'slab_id1','empty'=>'---- Select Slab ----','label'=>false,'name'=>'data[ProductCombination][parent_slab_id][]')); ?>
					<button class="add_product_button">Add More</button>
				</div>
                <span class="input_products_wrap">
					
				</span>
                <br/>
                <div class="form-group"><span>
					<?php echo $this->Form->input('combined_qty', array('class' => 'form-control','required'=>true,'label'=>'Combined Qty : ')); ?><!--<button class="add_qty_button" >Add More</button>--></span>
				</div>	
				<span class="input_qty_wrap">
					
				</span>
				<br/>		
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
$(document).ready(function (){
	var max_fields      = 5; 
    var product_wrapper     = $(".input_products_wrap"); 
	var qty_wrapper         = $(".input_qty_wrap"); 
    var add_product_button      = $(".add_product_button"); 
	var add_qty_button  = $(".add_qty_button"); 
    
    var x = 1;
    /*
		<select class="form-control width_15" name="data[ProductCombination][parent_slab_id][]" id="slab_id'+x+'" required>\
			<option value="">---- Select Slab -----</option>\
		</select>\
     */
    $(add_product_button).click(function(e){ 
        e.preventDefault();
        if(x < max_fields){ 
            x++;             
			$(product_wrapper).append(
				'<div class="slap_set">\
					<div class="form-group">\
						<label>Product :</label>\
						<select class="form-control product_common_class" name="data[ProductCombination][product_id][]" id="product_id'+x+'" required>\
								<option value="">---- Select Product -----</option>'+'<?=$product_list; ?>'+'\
						</select>\
						<a href="#" class="remove_product_field btn btn-primary btn-xs">Remove</a></div>'); 
        }
		
    });
	
	var y = 1; 
    $(add_qty_button).click(function(e){ 
        e.preventDefault();
        if(y < max_fields){ 
            y++;             
			$(qty_wrapper).append('<div class="slap_set"><div class="form-group"><label>Combined Qty :</label><input class="form-control" name="data[ProductCombination][min_qty][]" type="number" required><a href="#" class="remove_qty_field btn btn-primary btn-xs">Remove</a></div>'); 
        }
    });
    
    $(product_wrapper).on("click",".remove_product_field", function(e){ 
        e.preventDefault(); $(this).parent('div').remove(); x--;
    });
	$(qty_wrapper).on("click",".remove_qty_field", function(e){ 
        e.preventDefault(); $(this).parent('div').remove(); y--;
    });
});
</script>
<script>
$(document).ready(function (){
	/*$("body").on("change",".product_common_class",function(){
		var selected_product_id = $(this).attr('id');
		var id = selected_product_id.toString().slice(10);
		var product_id = $(this).val();
		var create_for =$('.create_for').val();
		var so_special_group=$('.so_special_group select').val();
	    var sr_special_group=$('.sr_special_group select').val();
		var so_outlet_category=$('.so_outlet_category select').val();
	    var sr_outlet_category=$('.sr_outlet_category select').val();
	    var effective_date=$('.effective_date').val();
	    if(!effective_date)
	    {
	    	alert('please select effective date');
	    	$(this).val('');
	    	return false;
	    }
		$.ajax({
			url:  "<?php echo BASE_URL; ?>admin/CombinationsV2/get_slab_list",
			type:"POST",
			data:{
				product_id:product_id,
				create_for:create_for,
				so_special_group:so_special_group,
				sr_special_group:sr_special_group,
				so_outlet_category:so_outlet_category,
				sr_outlet_category:sr_outlet_category,
				effective_date:effective_date
			},
			success: function(result){
				console.log(result);
				$("#slab_id"+id).html(result);
			}
		});
	});
	$('.effective_date').on('changeDate',function(){
    	var effective_date=$(this).val();
    	if(effective_date)
    	{
	    	$.ajax({
	    		url:'<?=BASE_URL ?>/combinations_v2/get_so_sr_special_group',
	    		type: "POST",
	    		data:{'effective_date':effective_date},
	    		data_type:'JSON',
	    		success:function(response)
	    		{
	    			var res=$.parseJSON(response);
	    			$('.so_special_group select').html(res.so_special);
	    			$('.sr_special_group select').html(res.sr_special);
	    		}

	    	});
    	}
    });
    $('.so_special_group ').hide();
    $('.sr_special_group').hide();
    $('.so_outlet_category ').hide();
    $('.sr_outlet_category').hide()
    $('.create_for').change(function(){
    	var val=$(this).val();
    	$('.so_special_group select').attr('disabled',true);
	    $('.sr_special_group select').attr('disabled',true);

		$('.so_outlet_category select').attr('disabled',true);
	    $('.sr_outlet_category select').attr('disabled',true);

	    $('.so_special_group ').hide();
	    $('.sr_special_group').hide();
	    $('.so_outlet_category ').hide();
	    $('.sr_outlet_category').hide();

	    if(val==4)
	    {
	    	$('.so_outlet_category ').show();
	    	$('.so_outlet_category select').attr('disabled',false);
	    }
	    else if(val==5)
	    {
	    	$('.sr_outlet_category ').show();
	    	$('.sr_outlet_category select').attr('disabled',false);
	    }
	    else if(val==6)
	    {
	    	$('.so_special_group').show();
	    	$('.so_special_group select').attr('disabled',false);
	    }
	    else if(val==7)
	    {
	    	$('.sr_special_group').show();
	    	$('.sr_special_group select').attr('disabled',false);
	    }
    });*/
});
</script>
