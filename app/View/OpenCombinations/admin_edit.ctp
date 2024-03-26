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
                
                <span class="input_products_wrap">
					<?php
                    $i = 0;
                    $total = count($this->request->data['OpenCombinationProduct']);
                    foreach($this->request->data['OpenCombinationProduct'] as $key=> $val){
                    $i++;
                    //pr($products);
                    ?>
                    <div class="form-group">
                        <label>Product :</label>
                        
                        <select class="form-control" id="product_id<?=$i?>" name="data[OpenCombination][product_id][<?=$this->request->data['OpenCombinationProduct'][$key]['id'];?>]" required>
                            <?php foreach($products as $okey=>$oval){ ?>
                                <option value="<?=$okey;?>" <?php if($this->request->data['OpenCombinationProduct'][$key]['product_id']==$okey){ echo 'selected'; } ?>><?=$oval;?></option>
                            <?php } ?>
                        </select>
                        
                        <?php
                        if($i==1){
                            echo '<button class="add_product_button">Add More</button>';
                        }else{
                            echo '<a href="#" class="remove_product_field btn btn-primary btn-xs">Remove</a>';
                        }
                        ?>
                    </div>
                                    
                    <?php 				
                    }
                    ?>	
				</span>
                <br/>
                <br/>
                	
				<div class="form-group">
                    <label for="UserActive">Is Active :</label>
                    <?php echo $this->Form->input('active', array('class' => 'form-control','type'=>'checkbox','label'=>false)); ?>
                </div>  
							
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>

<script>
$(document).ready(function (){
	var max_fields      		= 5; 
    var product_wrapper     	= $(".input_products_wrap"); 
    var add_product_button      = $(".add_product_button"); 
    
    var x = <?=$total?>; 
    $(add_product_button).click(function(e)
	{ 
        e.preventDefault();
        if(x < max_fields)
		{ 
            x++;             
			$(product_wrapper).append('<div class="slap_set"><div class="form-group"><label>Product :</label><select class="form-control product_common_class" name="data[OpenCombination][product_id][]" id="product_id'+x+'" required><option value="">---- Select Product -----</option>'+'<?=$product_list; ?>'+'</select><a href="#" class="remove_product_field btn btn-primary btn-xs">Remove</a></div>'); 
        }
		
    });
	
    $(product_wrapper).on("click",".remove_product_field", function(e){ 
        e.preventDefault(); $(this).parent('div').remove(); x--;
    });
});
</script>