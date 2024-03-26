
<style>
.radio label {
    width: auto;
	float:none;
	padding:0px 5% 0px 5px;
	
}
 .radio legend {
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
.search label {
    width: 25%;
}
#market_list .checkbox label{
padding-left:10px;
width:auto;
}
#market_list .checkbox{
width:33%;
float:left;
margin:1px 0;
}
</style>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Stock Process'); ?></h3>
			</div>
			<div class="box-body">
			<?php echo $this->Form->create('StockProcesses', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('from_date', array('type'=>'text','class' => 'form-control from_date','required'=>true)); ?>
				</div>
				<div class="form-group">
					<?php
					echo $this->Form->input('office_id', array('class' => 'form-control office_id','empty'=>'---- Select ----', 'required'=>true,'id'=>'office_id')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('type', array('legend'=>'Type :', 'class' => 'type', 'type' => 'radio', 'default' => 'office', 'onClick' => 'typeChange(this.value)', 'options' => array('office'=>'Office','territory'=>'Territory'), 'required'=>true));  ?>
				</div>

				<div class="form-group" id="territory_html">
					<?php echo $this->Form->input('territory_id', array('class' => 'form-control territory_id','empty'=>'---- Select ----', 'required'=>true)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('process', array('legend'=>'Process :', 'class' => 'type', 'type' => 'radio', 'default' => 'both', 'onClick' => 'typeChange(this.value)', 'options' => array('stock'=>'Only Stock Processs','report'=>'Only Report Process','both'=>'Stock And Report Process'), 'required'=>true));  ?>
				</div>

				<div class="form-group">
					<label style="float:left; width:20%;">Product Type :</label>
					<div id="market_list" class="input select" style="float:left; width:75%; padding-left:20px;">
						<div class="selection">
							<?php echo $this->Form->input('product_type', array('id' => 'product_type', 'label'=>false, 'class' => 'checkbox product_type', 'multiple' => 'checkbox', 'options' => $product_type_list)); ?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label style="float:left; width:20%;">Products : </label>
                            <div id="market_list" class="input select" style="float:left; width:75%; padding-left:0px;">
                                <div style="margin:auto; width:90%; float:left;">
                                    <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                    <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                </div>
                                <div class="product selection" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto">
                                <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $product_list)); ?>
                                </div>
                            </div>
				</div>
				
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary submit')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$("input[type='checkbox']").iCheck('destroy');
		$("input[type='radio']").iCheck('destroy');
		$('.from_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});
		typeChange();
		$('.office_id').selectChain({
			target: $('.territory_id'),
			value:'name',
			url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
			type: 'post',
			data:{'office_id': 'office_id' }
		});
		get_product_list($(".product_type [type='checkbox']:checked").serializeArray());
	    $(".product_type").change(function(){
	        product_type=$(".product_type [type='checkbox']:checked").serializeArray();
	        get_product_list(product_type);
	    });
	    var product_check=<?php echo @json_encode($this->request->data['StockProcesses']['product_id']);?>;
	   
	    function get_product_list(product_type)
	    {
	        $.ajax({
	            type: "POST",
	            //url: '<?=BASE_URL?>sales_analysis_reports/get_office_so_list',
	            url: '<?=BASE_URL?>stock_processes/get_product_list',
	            data: product_type,
	            cache: false, 
	            success: function(response){
	                $(".product").html(response);
	                if(product_check)
	                {
	                    $.each(product_check, function(i, val){

	                        $(".product_id>input[value='" + val + "']").prop('checked', true);

	                 });
	                }
	            }
	        });
	    }

	    $(".submit").click(function(e){
	    	e.preventDefault();
	    	if(!$('.from_date').val())
	    	{
	    		alert("Please Enter From Date");
	    		return 0;
	    	}

	    	if(!$('.office_id').val())
	    	{
	    		alert("Please Select Office");
	    		return 0;
	    	}

	    	if($('.type:checked').val()=='territory')
	    	{
	    		if(!$('.territory_id').val())
		    	{
		    		alert("Please Select Territory");
		    		return 0;
		    	}
	    	}
	    	if(!$(".product_id [type='checkbox']:checked").val())
	    	{
	    		alert("Please Select Product");
		    	return 0;
	    	}
	    	$('#StockProcessesAdminIndexForm').submit();
	    });
	});
	function typeChange()
	{
		var type = $('.type:checked').val();
		$('#territory_html').hide();

		if(type=='territory')
		{
			$('#territory_html').show();
		}
		
	}
</script>
