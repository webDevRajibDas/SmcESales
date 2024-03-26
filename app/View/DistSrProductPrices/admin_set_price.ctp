<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-plus"></i> <?php echo __('Add New Slot'); ?></h3>
			</div>	
			<div class="box-body">				
                <?php echo $this->Form->create('DistSrProductPrice', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('type'=>'text','class' => 'datepicker form-control','value'=>date('d-m-Y',strtotime($this->request->data['DistSrProductPrice']['effective_date'])))); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('general_price', array('class' => 'form-control','value'=>sprintf("%1\$.6f",$this->request->data['DistSrProductPrice']['general_price']))); ?>
				</div>
                <div class="form-group">
					<?php echo $this->Form->input('mrp', array('class' => 'form-control', 'label'=>'MRP :', 'value'=>sprintf("%1\$.6f",$this->request->data['DistSrProductPrice']['mrp']))); ?>
				</div>
				<?php 
				if(!empty($this->request->data['DistSrProductCombination']))
				{
					foreach($this->request->data['DistSrProductCombination'] as $key=>$val){
				?>
					<div class="form-group"><label>MinQty :</label><input class="form-control" name="data[DistSrProductCombination][update_min_qty][<?=$this->request->data['DistSrProductCombination'][$key]['id'];?>]" value="<?=$this->request->data['DistSrProductCombination'][$key]['min_qty'];?>" type="text"></div>
					<div class="form-group"><label>Price :</label><input class="form-control" name="data[DistSrProductCombination][update_price][<?=$this->request->data['DistSrProductCombination'][$key]['id'];?>]" value="<?php $new=$this->request->data['DistSrProductCombination'][$key]['price'];
					$new_price=sprintf("%1\$.6f",$new); echo $new_price;
					?>" type="text"></div>
				<?php 
					}
				}
				?>
				<span class="input_fields_wrap">
					
				</span>
				<br/>
				<div class="form-group">
					<label></label>
					<button class="add_field_button">Add Price Slap</button>
				</div>
				<?php
                if($update_allow)
				{
				echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); 
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
    
    var x = 1; 
    $(add_button).click(function(e){ 
        e.preventDefault();
        if(x < max_fields){ 
            x++; 
            $(wrapper).append('<div class="slap_set"><div class="form-group"><label>MinQty :</label><input class="form-control" name="data[DistSrProductCombination][min_qty][]" type="number" required></div><div class="form-group"><label for="price">Price :</label><input class="form-control" name="data[DistSrProductCombination][price][]" type="number" required step="any"></div><label></label><a href="#" class="remove_field btn btn-primary btn-xs">Remove</a></div>'); 
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ 
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});
</script>