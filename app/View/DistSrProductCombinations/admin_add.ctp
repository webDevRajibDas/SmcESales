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
			<?php echo $this->Form->create('DistSrCombination', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control','required'=>true)); ?>
                    
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('DistSrProductCombination.effective_date', array('type'=>'text','class' => 'datepicker form-control','required'=>true)); ?>
				</div>
				<br/>
                <br/>
				<div class="form-group">
					<label>Product :</label>
					
                    <?php 
					$disabled = '';
					if(isset($product_id) && $product_id != '')
					{	
						
						//$disabled = "disabled=true";
						
						 echo $this->Form->input('redirect_product_id', array('class' => 'form-control','type'=>'hidden','value'=>$product_id));
					}else
					{
						$product_id = '';
						$disabled = '';
					}
					echo $this->Form->input('product_id', array('class' => 'form-control product_common_class','id'=>'product_id1','empty'=>'---- Select Product ----','label'=>false,'default'=>$product_id,'name'=>'data[DistSrProductCombination][product_id][]',$disabled)); ?>
					<?php
					echo $this->Form->input('parent_slab_id', array('class' => 'form-control width_15','id'=>'slab_id1','empty'=>'---- Select Slab ----','label'=>false,'options'=>$slab_list,'name'=>'data[DistSrProductCombination][parent_slab_id][]',$disabled)); ?>
					<button class="add_product_button">Add More</button>
				</div>
                <span class="input_products_wrap">
					
				</span>
                <br/>
                <br/>
                <div class="form-group"><span>
					<?php echo $this->Form->input('DistSrProductCombination.min_qty', array('class' => 'form-control','required'=>true,'name'=>'data[DistSrProductCombination][min_qty][]','label'=>'Combined Qty : ')); ?><!--<button class="add_qty_button" >Add More</button>--></span>
				</div>	
				<span class="input_qty_wrap">
					
				</span>
				<br/>
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
    $(add_product_button).click(function(e){ 
        e.preventDefault();
        if(x < max_fields){ 
            x++;             
			$(product_wrapper).append('<div class="slap_set"><div class="form-group"><label>Product :</label><select class="form-control product_common_class" name="data[DistSrProductCombination][product_id][]" id="product_id'+x+'" required><option value="">---- Select Product -----</option>'+'<?=$product_list; ?>'+'</select><select class="form-control width_15" name="data[DistSrProductCombination][parent_slab_id][]" id="slab_id'+x+'" required><option value="">---- Select Slab -----</option></select><a href="#" class="remove_product_field btn btn-primary btn-xs">Remove</a></div>'); 
        }
		
    });
	
	var y = 1; 
    $(add_qty_button).click(function(e){ 
        e.preventDefault();
        if(y < max_fields){ 
            y++;             
			$(qty_wrapper).append('<div class="slap_set"><div class="form-group"><label>Combined Qty :</label><input class="form-control" name="data[DistSrProductCombination][min_qty][]" type="number" required><a href="#" class="remove_qty_field btn btn-primary btn-xs">Remove</a></div>'); 
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
	$("body").on("change",".product_common_class",function(){
		var selected_product_id = $(this).attr('id');
		var id = selected_product_id.toString().slice(10);
		var product_id = $(this).val();
		$.ajax({
			url:  "<?php echo BASE_URL; ?>admin/DistSrProductCombinations/get_slab_list",
			type:"POST",
			data:{product_id:product_id},
			success: function(result){
				console.log(result);
				$("#slab_id"+id).html(result);
			}
		});
	});
});
</script>
