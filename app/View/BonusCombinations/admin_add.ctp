<style type="text/css">
	.width_15{width: 15%}

	.slap_set{
		margin-top:5px;
		margin-left: 10%;
		border: 1px dotted;
		width: 50%;
		padding: 0.5%;
	}
	.slap_set label
	{
		float: left;
		width: 19%;
		text-align: right;
		margin: 5px 20px 0 0;
	}
	.slap_set .form-control {
		float: left;
		width: 60%;
		font-size: 13px;
		height: 28px;
		padding: 0px 4px;
	}
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
			<?php echo $this->Form->create('OpenCombination', array('role' => 'form')); ?>
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
                <div class="slap_set">
                	<div class="form-group">
                		<label>Product Type:</label>
                		<?php 
                			echo $this->Form->input('product_type_id', array('class' => 'form-control product_type_id','empty'=>'---- Select ----','label'=>false));

                		 ?>
                	</div>
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
						echo $this->Form->input('product_id', array('class' => 'form-control product_common_class','id'=>'product_id1','empty'=>'---- Select Product ----','label'=>false,'div'=>false,'default'=>$product_id, 'name'=>'data[OpenCombination][product_id][]',$disabled)); ?>
						
						<button class="add_product_button">Add More</button>
					</div>
				</div>
                <span class="input_products_wrap">
					
				</span>
                <br/>
                <br/>
                	
				
				<div class="form-group">
                    <label for="UserActive">Is Active :</label>
                    <?php echo $this->Form->input('active', array('class' => 'form-control','type'=>'checkbox','label'=>false)); ?>
                </div> 
		
		<div class="hidden product_type_option">
					<div class="form-group">
                		<label>Product Type:</label>
                		<?php 
                			echo $this->Form->input('product_type_id', array('class' => 'form-control product_type_id','empty'=>'---- Select ----','label'=>false));

                		 ?>
                	</div>
				</div>
							
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
            var product_type_option=$('.product_type_option').html();          
			$(product_wrapper).append('\
				<div class="slap_set">\
					'+product_type_option+'\
					<div class="form-group">\
						<label>Product :</label>\
						<select class="form-control product_common_class" name="data[OpenCombination][product_id][]" id="product_id'+x+'" required>\
							<option value="">---- Select Product -----</option>\
							'+'<?=$product_list; ?>'+'\
						</select>\
						<a href="#" class="remove_product_field btn btn-primary btn-xs">Remove</a>\
					</div>'); 
        }
		
    });
    $(product_wrapper).on("click",".remove_product_field", function(e){ 
        e.preventDefault(); $(this).parent('div').parent('div').remove(); x--;
    });
    $("body").on('change',".product_type_id",function(){
    	var _this=$(this);
    	var product_type_id=$(this).val();
    	$.ajax({
    		'url':'<?php echo BASE_URL;?>/BonusCombinations/get_product_by_type_id',
    		'type':'post',
    		'data':{'product_type_id':product_type_id},
    		'success':function(data){
    			var response=$.parseJSON(data);
    			var option="";
    			$.each(response,function(i,val){
    				option+="<option value='"+val.id+"'>"+val.name+"</option>";
    			});
    			
    			_this.parent().parent().next().find('.product_common_class').html(option);
    		}
    	});
    });
});
</script>

