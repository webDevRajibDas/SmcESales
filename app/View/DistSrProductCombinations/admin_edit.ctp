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
			<?php echo $this->Form->create('DistSrCombination', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('class' => 'form-control','required'=>true)); ?>
				</div>
				<div class="form-group add_more_box">
					<?php echo $this->Form->input('DistSrProductCombination.effective_date', array('default' => $this->request->data['DistSrProductCombination'][0]['effective_date'],'type'=>'text','class' => 'datepicker form-control','required'=>true,'value'=>date("d-m-Y",strtotime($this->request->data['DistSrProductCombination'][0]['effective_date'])))); ?>
				</div>
				<?php
				$i = 0;
				foreach($this->request->data['DistSrProductCombination'] as $key=>$val){
					$i++;
				?>
                	
					<div class="form-group">
						<label>Product :</label>
						<select class="form-control" id="product_id<?=$i?>" name="data[DistSrProductCombination][product_id][<?=$this->request->data['DistSrProductCombination'][$key]['id'];?>]" required>
							<?php foreach($products_list as $okey=>$oval){ ?>
								<option value="<?=$okey;?>" <?php if($this->request->data['DistSrProductCombination'][$key]['product_id']==$okey){ echo 'selected'; } ?>><?=$oval;?></option>
							<?php } ?>
						</select>
						<select class="form-control width_15" id="slab_id<?=$i?>" name="data[DistSrProductCombination][parent_slab_id][<?=$this->request->data['DistSrProductCombination'][$key]['id'];?>]" required>
							<?php foreach($total_slab[$this->request->data['DistSrProductCombination'][$key]['product_id']] as $s_key=>$s_val){ 
							?>
								<option value="<?=$s_key;?>" <?php if($this->request->data['DistSrProductCombination'][$key]['parent_slab_id']==$s_key){ echo 'selected'; } ?>><?=$s_val;?></option>
							<?php } ?>
						</select>
						<?php /* echo $this->Form->input('parent_slab_id', array('class' => 'form-control width_15','id' =>"slab_id".$i,'empty'=>'---- Select Slab ----','label'=>false,'options'=>$total_slab[$this->request->data['DistSrProductCombination'][$key]['product_id']],'name'=>'data[DistSrProductCombination][parent_slab_id][]')); */ ?>
						<?php
							if($i==1){
								echo '<button class="add_field_button out_of_loop">Add More</button>';
							}else{
								echo '<a href="#" class="remove_field btn btn-primary btn-xs">Remove</a>';
							}
						?>
					</div>		
                    
                    
                    <script type="text/javascript">
						$(document).ready(function (){
							getProductSlab('<?=$i?>', '<?=$this->request->data['DistSrProductCombination'][$key]['product_id']?>', '<?=$this->request->data['DistSrProductCombination'][$key]['parent_slab_id']?>');
						});
					</script>
                    			
				<?php 				
				}
				?>			
				<span class="input_fields_wrap">
					
				</span>
				<br/>	
				<span>
					<?php echo $this->Form->input('DistSrProductCombination.min_qty', array('class' => 'form-control','required'=>true,'default'=>$this->request->data['DistSrProductCombination'][0]['min_qty'],'name'=>'data[DistSrProductCombination][min_qty]','label'=>'Combined Qty : ')); ?><!--<button class="add_qty_button" >Add More</button>--></span>
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
    
    var x = 1; 
    $(add_button).click(function(e){ 
	var row_num = $("#DistSrCombinationAdminEditForm .form-group:last select:last").attr("id").toString().slice(7);
        e.preventDefault();
        if(x < max_fields){
            row_num++;             
			$(wrapper).append('<div class="slap_set"><div class="form-group"><label>Product :</label><select class="form-control" id="product_id'+row_num+'" name="data[DistSrProductCombination][product_id][]" required><option value="">---- Select Product -----</option><?php echo $products; ?></select><select class="form-control width_15" name="data[DistSrProductCombination][parent_slab_id][]" id="slab_id'+row_num+'" required><option value="">---- Select Slab -----</option></select><a href="#" class="remove_field btn btn-primary btn-xs">Remove</a></div>');  
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
		url:  "<?php echo BASE_URL; ?>admin/DistSrProductCombinations/get_slab_list",
		type:"POST",
		data:dataString,
		success: function(result){
			$("#slab_id"+id).html(result);
			//alert(parent_slab_id);
		}
	});
}

$(document).ready(function (){
	$("body").on("change","select",function(){
		var selected_product_id = $(this).attr('id');
		var id = selected_product_id.toString().slice(10);
		var product_id = $(this).val();
		$.ajax({
			url:  "<?php echo BASE_URL; ?>admin/DistSrProductCombinations/get_slab_list",
			type:"POST",
			data:{product_id:product_id},
			success: function(result){
				$("#slab_id"+id).html(result);
			}
		});
	});
});
</script>