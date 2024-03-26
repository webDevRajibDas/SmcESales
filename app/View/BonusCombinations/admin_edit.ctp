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
<?php $BonusCombinations=new BonusCombinationsController(); ?>
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
                    <div class="slap_set">
                        <?php 
                            $product_details=$BonusCombinations->get_product_details_by_product_id($this->request->data['OpenCombinationProduct'][$key]['product_id']);
                            $products_list=$BonusCombinations->get_product_list_by_type_id($product_details['Product']['product_type_id']);
                            ?>
                        <div class="form-group">
                        <label>Product Type:</label>
                        <?php 
                            echo $this->Form->input('product_type_id', array('class' => 'form-control product_type_id','empty'=>'---- Select ----','label'=>false,'default'=>$product_details['Product']['product_type_id']));

                         ?>
                    </div>
                        <div class="form-group">
                            <label>Product :</label>
                            
                            <select class="form-control product_common_class" id="product_id<?=$i?>" name="data[OpenCombination][product_id][<?=$this->request->data['OpenCombinationProduct'][$key]['id'];?>]" required>
                                <?php foreach($products_list as $okey=>$oval){ ?>
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
                    </div>                
                    <?php 				
                    }
                    ?>	
				</span>
                <br/>
                <br/>
                	
				
				<div class="form-group">
                    <label for="is_active">Is Active :</label>
                    <?php echo $this->Form->input('active', array('class' => 'form-control','type'=>'checkbox','label'=>false,'id'=>'is_active')); ?>
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
	var max_fields      		= 5; 
    var product_wrapper     	= $(".input_products_wrap"); 
    var add_product_button      = $(".add_product_button"); 
    
    var x = <?=$total?>; 
    $(add_product_button).click(function(e)
	{ 
        e.preventDefault();
        if(x < max_fields)
		{
            var product_type_option=$('.product_type_option').html();  
            x++;             
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
                    </div>\
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