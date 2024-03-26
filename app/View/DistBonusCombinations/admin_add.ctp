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
			<?php echo $this->Form->create('DistOpenCombination', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control','required'=>true)); ?>
                    
				</div>
                <div class="form-group">
					<?php echo $this->Form->input('description', array('type'=>'textarea', 'class' => 'form-control','required'=>false)); ?>
                    
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('start_date', array('type'=>'text', 'class' => 'datepicker form-control','required'=>true)); ?>
				</div>
                <div class="form-group">
					<?php echo $this->Form->input('end_date', array('type'=>'text', 'class' => 'datepicker form-control','required'=>true)); ?>
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
					echo $this->Form->input('product_id', array('class' => 'form-control product_common_class','id'=>'product_id1','empty'=>'---- Select Product ----','label'=>false,'default'=>$product_id, 'name'=>'data[DistOpenCombination][product_id][]',$disabled)); ?>
					
					<button class="add_product_button">Add More</button>
				</div>
                <span class="input_products_wrap">
					
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
	var max_fields      = 15; 
    var product_wrapper     = $(".input_products_wrap"); 
    var add_product_button      = $(".add_product_button"); 
    
    var x = 1; 
    $(add_product_button).click(function(e){ 
        e.preventDefault();
        if(x < max_fields){ 
            x++;             
			$(product_wrapper).append('<div class="slap_set"><div class="form-group"><label>Product :</label><select class="form-control product_common_class" name="data[DistOpenCombination][product_id][]" id="product_id'+x+'" required><option value="">---- Select Product -----</option>'+'<?=$product_list; ?>'+'</select><a href="#" class="remove_product_field btn btn-primary btn-xs">Remove</a></div>'); 
        }
		
    });
    $(product_wrapper).on("click",".remove_product_field", function(e){ 
        e.preventDefault(); $(this).parent('div').remove(); x--;
    });
});
</script>

