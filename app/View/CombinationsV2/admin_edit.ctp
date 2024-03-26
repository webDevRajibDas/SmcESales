<style type="text/css">
	.width_15{width: 15%};
	.out_of_loop{
		position:absolute !important;
		
	}
	.add_more_box{
		position:relative !important;
	}
</style>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Combination'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Combination List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">				
			<?php echo $this->Form->create('Combination', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control','required'=>true,'value'=>$this->request->data['CombinationsV2']['name'])); ?>
				</div>
				<div class="form-group add_more_box">
					<?php echo $this->Form->input('effective_date', array('default' => $this->request->data['CombinationsV2']['effective_date'],'type'=>'text','class' => 'datepicker form-control effective_date','required'=>true,'value'=>date("d-m-Y",strtotime($this->request->data['CombinationsV2']['effective_date'])))); ?>
				</div>
				<div class="form-group">
					<?php //echo $this->Form->input('create_for', array('class' => 'form-control create_for','required'=>true,'options'=>$create_for,'empty'=>'--- Select ---','default'=>$this->request->data['CombinationsV2']['create_for'])); ?>
				</div>
				<div class="form-group so_outlet_category">
					<?php //echo $this->Form->input('reffrence_id', array('class' => 'form-control','required'=>true,'options'=>$outlet_categories,'empty'=>'--- Select ---','disabled','default'=>$this->request->data['CombinationsV2']['reffrence_id'])); ?>
				</div>
				<div class="form-group sr_outlet_category">
					<?php //echo $this->Form->input('reffrence_id', array('class' => 'form-control','required'=>true,'options'=>$sr_outlet_categories,'empty'=>'--- Select ---','disabled','default'=>$this->request->data['CombinationsV2']['reffrence_id'])); ?>
				</div>
				<div class="form-group so_special_group">
					<?php //echo $this->Form->input('reffrence_id', array('type'=>'select','class' => 'form-control','required'=>true,'empty'=>'--- Select ---','disabled','options'=>$so_special_group,'default'=>$this->request->data['CombinationsV2']['reffrence_id'])); ?>
				</div>
				<div class="form-group sr_special_group">
					<?php //echo $this->Form->input('reffrence_id', array('type'=>'select', 	'class' => 'form-control','required'=>true,'empty'=>'--- Select ---','disabled','options'=>$sr_special_group,'default'=>$this->request->data['CombinationsV2']['reffrence_id'])); ?>
				</div>
				<?php
				$i = 0;
				foreach($this->request->data['CombinationDetailsV2'] as $key=>$val)
				{
					$i++;
				?>
                	
					<div class="form-group">
						<label>Product :</label>
						<select class="form-control product_common_class" id="product_id<?=$i?>" name="data[ProductCombination][product_id][<?=$i?>]" required>
							<?php foreach($products_list as $okey=>$oval){ ?>
								<option value="<?=$okey;?>" <?php if($this->request->data['CombinationDetailsV2'][$key]['product_id']==$okey){ echo 'selected'; } ?>><?=$oval;?></option>
							<?php } ?>
						</select>
						<!-- <select class="form-control width_15" id="slab_id<?=$i?>" name="data[ProductCombination][parent_slab_id][<?=$i?>]" required data-selected-id="<?php //echo $this->request->data['CombinationDetailsV2'][$key]['product_combination_id']; ?>">
							
						</select> -->
						<?php
							if($i==1){
								echo '<button class="add_field_button out_of_loop">Add More</button>';
							}else{
								echo '<a href="#" class="remove_field btn btn-primary btn-xs">Remove</a>';
							}
						?>
					</div>		
                    
				<?php 				
				}
				?>			
				<span class="input_fields_wrap">
					
				</span>
				<br/>	
				<div class="form-group"><span>
					<?php echo $this->Form->input('combined_qty', array('class' => 'form-control','required'=>true,'label'=>'Combined Qty : ','value'=>$this->request->data['CombinationsV2']['combined_qty'])); ?><!--<button class="add_qty_button" >Add More</button>--></span>
				</div>	
				<br/>
				<?php 			  
	                if(!array_key_exists($edit_id,$com_ids)) 
	                {						
						echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); 
					}
               ?>
			
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
$(document).ready(function (){
	var max_fields      = 5; 
    var wrapper         = $(".input_fields_wrap"); 
    var add_button      = $(".add_field_button"); 
    
    var x = <?=$i?>;
    /*
		<select class="form-control width_15" name="data[ProductCombination][parent_slab_id][]" id="slab_id'+x+'" required>\
			<option value="">---- Select Slab -----</option>\
		</select>\
     */
    $(add_button).click(function(e){
        e.preventDefault();
        if(x < max_fields){
           x++;            
			$(wrapper).append(
				'<div class="slap_set">\
					<div class="form-group">\
						<label>Product :</label>\
						<select class="form-control product_common_class" id="product_id'+x+'" name="data[ProductCombination][product_id][]" required>\
							<option value="">---- Select Product -----</option>\
							<?php echo $products; ?>\
						</select>\
						<a href="#" class="remove_field btn btn-primary btn-xs">Remove</a></div>');  
        }
    });
    
    $('body').on("click",".remove_field", function(e){ 
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});
</script>

<script>
function getProductSlab(id, product_id, parent_slab_id){
	
	var dataString = 'product_id='+ product_id + '&parent_slab_id=' + parent_slab_id;
	
	$.ajax({
		url:  "<?php echo BASE_URL; ?>admin/ProductCombinations/get_slab_list",
		type:"POST",
		data:dataString,
		success: function(result){
			$("#slab_id"+id).html(result);
			//alert(parent_slab_id);
		}
	});
}


$(document).ready(function (){

	$("body").on("change",".product_common_class",function(){
		var selected_product_id = $(this).attr('id');
		var id = selected_product_id.toString().slice(10);
		var product_id = $(this).val();
		var create_for =$('.create_for').val();
		var so_special_group=$('.so_special_group select').val();
	    var sr_special_group=$('.sr_special_group select').val();
		var so_outlet_category=$('.so_outlet_category select').val();
	    var sr_outlet_category=$('.sr_outlet_category select').val();
	    var effective_date=$('.effective_date').val();
	    var selected=$("#slab_id"+id).data('selected-id');
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
				if($("#slab_id"+id).html(result))
				{
					$("#slab_id"+id).val(selected);
				}
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
    	$('.product_common_class').val('');
    });
    $('.so_special_group ').hide();
    $('.sr_special_group').hide();
    $('.so_outlet_category ').hide();
    $('.sr_outlet_category').hide();
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

    });
    $('.create_for').trigger('change');
    $('.product_common_class').each(function(e){
		
		$(this).trigger('change');
	});

});
</script>