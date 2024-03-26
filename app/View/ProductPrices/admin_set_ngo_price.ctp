<style>
	.outlet_item{
		background-color: #367FA9;
		color: #fff;
		padding: 5px 4px;
		border-radius: 4px;
		margin-right:5px;
	}
	.icon{
		font-size: 11px;
		padding-left: 2px;
		cursor: pointer;
	}
	.hidde_select{
		background-color:#367FA9;
		width:.5px;
		border:0px;
		display: none;
	}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-plus"></i> <?php echo __('Add New Slot'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('product_prices','admin_ngo_price_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Ngo Price List'), array('action' => "admin_ngo_price_list/$product_id"), array('class' => 'btn btn-primary', 'escape' => false)); } ?>		
				</div>
			</div>	
			<div class="box-body">				
                <?php echo $this->Form->create('ProductPrice', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'Start Date:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('end_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'End Date:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('type'=>'text','class' => 'form-control','label'=>'Project Name:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('general_price', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('type_id',array('id' => 'type_id','class' => 'form-control','options'=>$institute_type,'empty'=>'---- Select Institute Type')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('institute_id', array('id'=>'institute_id','class' => 'form-control','options'=>$institute_id,'empty'=>'---- Select Institute')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('id'=>'office_id','class' => 'form-control','options'=>$office_id,'empty'=>'---- Select Office')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('territory_id', array('id'=>'territory_id','class' => 'form-control','empty'=>'---- Select Territory')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('OutletNGOPrice.market_id', array('class' => 'form-control','name'=>'','empty'=>'---- All----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('OutletNGOPrice.outlet_id', array('class' => 'form-control','name'=>'','empty'=>'---- All----')); ?>
					<a href="#" id="add_outlet" class="btn btn-primary btn-sm">Add</a>
					<a href="#" id="remove_outlet" class="btn btn-primary btn-sm">Remove All</a>
				</div>
				<div class="form-group">
					<div class="item_box" style="width:50%;float:left;margin-left: 10%">
					<h5>Outlet List :</h5>
					<table class="table table-bordered" >
						<thead>
							<tr>
								<th width="25%">Office</th>
								<th width="20%">Territory</th>
								<th width="25%">Market</th>
								<th width="25%">Outlet</th>
								<th width="5%">#</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
					</div>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_vat_applicable', array('label'=>'<b>Is Vat Applicable :</b>','type'=>'checkbox','class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('vat', array('label'=>'Vat (%) :','class' => 'form-control')); ?>
				</div>
				<?php 
				if(!empty($this->request->data['ProductCombination']))
				{
					foreach($this->request->data['ProductCombination'] as $key=>$val){
				?>
					<div class="form-group"><label>MinQty :</label><input class="form-control" name="data[ProductCombination][update_min_qty][<?=$this->request->data['ProductCombination'][$key]['id'];?>]" value="<?=$this->request->data['ProductCombination'][$key]['min_qty'];?>" type="text"></div>
					<div class="form-group"><label>Price :</label><input class="form-control" name="data[ProductCombination][update_price][<?=$this->request->data['ProductCombination'][$key]['id'];?>]" value="<?=$this->request->data['ProductCombination'][$key]['price'];?>" type="text"></div>
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
				<?php echo $this->Form->submit('Add', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>							
			</div>			
		</div>
	</div>
	
</div>
<script>
$(document).ready(function (){
	var outlet_info=new Array();
	var max_fields      = 5; 
    var wrapper         = $(".input_fields_wrap"); 
    var add_button      = $(".add_field_button"); 
    
    var x = 1; 
    $(add_button).click(function(e){ 
        e.preventDefault();
        if(x < max_fields){ 
            x++; 
            $(wrapper).append('<div class="slap_set"><div class="form-group"><label>MinQty :</label><input class="form-control" name="data[ProductCombination][min_qty][]" type="number" required></div><div class="form-group"><label for="price">Price :</label><input class="form-control" name="data[ProductCombination][price][]" type="text" required></div><label></label><a href="#" class="remove_field btn btn-primary btn-xs">Remove</a></div>'); 
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ 
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});
</script>
<script>
	$(document).ready(function(){
		$("#add_outlet").click(function(){
			var btn = $(".item_box>table>tbody").html();
			var all_val = $("#OutletNGOPriceOutletId option:selected").val();
			/*var office_name=$("#office_id option:selected").text();
			var territory_name=$("#territory_id option:selected").text();
			var market_name=$("#OutletNGOPriceMarketId option:selected").text();*/
			if(all_val == 'all'){
				var outlet_list = [];
				$( "#OutletNGOPriceOutletId option" ).each(function() {
					var outlet_id = $( this ).attr("value");
					var outlet_name = $( this ).html();
					if(outlet_id != 'all'){
					outlet_list[outlet_id] = outlet_name;
					}
				});
				for(var key in outlet_list){
					var outlet_details=outlet_info[key];
					var office_name=outlet_details.office;
					var territory_name=outlet_details.territory;
					var market_name=outlet_details.market;
					var outlet_id = key;
					var outlet_name = outlet_list[key];
					/*var new_btn = '<span class="outlet_item" id="'+outlet_id+'">'+outlet_name+'<i id="'+outlet_id+'" class="glyphicon glyphicon-remove icon"></i><select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_id\]\[\]"><option value="'+outlet_id+'">'+outlet_name+'</option></select><select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_name\]\[\]"><option value="'+outlet_name+'">'+outlet_name+'</option></select></span>';*/

					var new_btn = '<tr>\
					<td>'+office_name+'</td>\
						<td>'+territory_name+'</td>\
						<td>'+market_name+'</td>\
						<td>'+outlet_name+'\
							<select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_id\]\[\]">\
								<option value="'+outlet_id+'">'+outlet_name+'</option>\
							</select>\
							<select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_name\]\[\]">\
								<option value="'+outlet_name+'">'+outlet_name+'</option>\
							</select>\
						</td>\
						<td>\
							<i id="'+outlet_id+'" class="glyphicon glyphicon-remove icon"></i>\
						</td>\
						</tr>';


					if(btn.search(outlet_id) == -1)
					{
						btn = btn+new_btn;
						// $(".item_box>table>tbody").html(btn);
						$(".item_box>table>tbody").append(new_btn);
					}
					else
					{
						alert("Please select another! "+outlet_name +" Already Selected");
					};
				}
			}
			else
			{
				var outlet_name = $("#OutletNGOPriceOutletId option:selected").html();
				var outlet_id = $("#OutletNGOPriceOutletId").val();

				var outlet_details=outlet_info[outlet_id];
				var office_name=outlet_details.office;
				var territory_name=outlet_details.territory;
				var market_name=outlet_details.market;
				// var new_btn = '<span class="outlet_item" id="'+outlet_id+'">'+outlet_name+'<i id="'+outlet_id+'" class="glyphicon glyphicon-remove icon"></i><select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_id\]\[\]"><option value="'+outlet_id+'">'+outlet_name+'</option></select><select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_name\]\[\]"><option value="'+outlet_name+'">'+outlet_name+'</option></select></span>';
				// 
				var new_btn = '<tr>\
				<td>'+office_name+'</td>\
						<td>'+territory_name+'</td>\
						<td>'+market_name+'</td>\
						<td>'+outlet_name+'\
							<select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_id\]\[\]">\
								<option value="'+outlet_id+'">'+outlet_name+'</option>\
							</select>\
							<select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_name\]\[\]">\
								<option value="'+outlet_name+'">'+outlet_name+'</option>\
							</select>\
						</td>\
						<td>\
							<i id="'+outlet_id+'" class="glyphicon glyphicon-remove icon"></i>\
						</td>\
						</tr>';
				if(outlet_id != '')
				{
					if(btn.search(outlet_id) == -1 /*&& btn.search(outlet_name) == -1*/)
					{
						btn = btn+new_btn;
						// $(".item_box>table>tbody").html(btn);
						$(".item_box>table>tbody").append(new_btn);
					}else
					{
						alert("Please select another! "+outlet_name +" Already Selected");
					};
				}
				else
				{
					alert("Please select any outlet!");
				}
			}
		});
		/*--------- delete single outlet ---------*/
		$("body").on("click",".icon",function(e){
			e.preventDefault();
			// var avoid = $(this).parent().clone().wrap('<span>').parent().html();
			// btn = btn.replace(avoid,'');
			$(this).parent().parent().remove();
		});
		/*------- delete all outlet -------*/
		$("body").on("click","#remove_outlet",function(){
			var btn = $(".item_box>table>tbody").html();
            var all_val = $("#OutletNGOPriceOutletId option:selected").val();
            /*var office_name=$("#office_id option:selected").text();
			var territory_name=$("#territory_id option:selected").text();
			var market_name=$("#OutletNGOPriceMarketId option:selected").text();*/
			if(all_val == 'all'){
                var outlet_list_del = [];
                $( "#OutletNGOPriceOutletId option" ).each(function() {
                    var outlet_id = $( this ).attr("value");
                    var outlet_name = $( this ).html();
                    if(outlet_id != 'all'){
                    outlet_list_del[outlet_id] = outlet_name;
                    }
                });
                var btn_del = '';
                for(var key in outlet_list_del){

                	/*var outlet_details=outlet_info[key];
                	var office_name=outlet_details.office;
                	var territory_name=outlet_details.territory;
                	var market_name=outlet_details.market;*/

                    var outlet_id = key;
                    var outlet_name = outlet_list_del[key];
                    $(".item_box>table>tbody> tr td:nth-child(5)").find("#"+outlet_id).parent().parent().remove();
                    // var new_btn = '<span class="outlet_item" id="'+outlet_id+'">'+outlet_name+'<i id="'+outlet_id+'" class="glyphicon glyphicon-remove icon"></i><select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_id\]\[\]"><option value="'+outlet_id+'">'+outlet_name+'</option></select><select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_name\]\[\]"><option value="'+outlet_name+'">'+outlet_name+'</option></select></span>';
                    // 
                    /*var new_btn = '<tr>\
									<td>'+office_name+'</td>\
											<td>'+territory_name+'</td>\
											<td>'+market_name+'</td>\
											<td>'+outlet_name+'\
												<select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_id\]\[\]">\
													<option value="'+outlet_id+'">'+outlet_name+'</option>\
												</select>\
												<select class="hidde_select" name="data\[OutletNgoPrice\]\[outlet_name\]\[\]">\
													<option value="'+outlet_name+'">'+outlet_name+'</option>\
												</select>\
											</td>\
											<td>\
												<i id="'+outlet_id+'" class="glyphicon glyphicon-remove icon"></i>\
											</td>\
											</tr>';*/
                   /* btn_del = btn_del+new_btn;*/
                }
                /*btn = btn.replace(btn_del,'');
                $(".item_box>table>tbody").html(btn);*/
            }
		});
	});
	$(document).ready(function(){
		$('#type_id').selectChain({
			target: $('#institute_id'),
			value:'name',
			url: '<?= BASE_URL.'product_prices/get_institute_list'?>',
			type: 'post',
			data:{'type_id': 'type_id' }
		});
		$('#office_id').selectChain({
			target: $('#territory_id'),
			value:'name',
			url: '<?= BASE_URL.'product_prices/get_territory_list'?>',
			type: 'post',
			data:{'office_id': 'office_id' }
		});
		
	});
	$(document).ready(function(){
		
		$('#territory_id').change(function(){
			get_outlet_list();
			get_market_list();
		});
		$("#office_id").change(function(){
			get_outlet_list();
			get_market_list();
		});

		$("#type_id").change(function(){
			get_outlet_list();
			get_market_list();
		});
		$("#institute_id").change(function(){
			get_outlet_list();
			get_market_list();
		});

		$("#OutletNGOPriceMarketId").change(function(){
			get_outlet_list();
		});

		function get_market_list()
		{
			var territory_id = $("#territory_id").val();
			var office_id = $('#office_id').val();
			var institute_id = $('#institute_id').val();
			if(territory_id && office_id && institute_id )
			{
				$.ajax({
					url:  "<?php echo BASE_URL; ?>product_prices/get_market_list",
					type:"POST",
					data:{territory_id:territory_id,office_id:office_id,institute_id:institute_id},
					success: function(result){
						$("#OutletNGOPriceMarketId").html(result);
					}
				});
			}
			else
			{
				$("#OutletNGOPriceMarketId").html("<option value=''>---- Select ---- </option>");
			}
		}

		function get_outlet_list()
		{
			var territory_id = $("#territory_id").val();
			var office_id = $('#office_id').val();
			var institute_id = $('#institute_id').val();
			var market_id = $('#OutletNGOPriceMarketId').val();
			if(institute_id)
			{
				$.ajax({
					url:  "<?php echo BASE_URL; ?>product_prices/get_outlet_list",
					type:"POST",
					data:{territory_id:territory_id,office_id:office_id,institute_id:institute_id,market_id:market_id},
					success: function(result){
						var response=$.parseJSON(result);
						// console.log(response);
						outlet_info=response.other_info;
						$("#OutletNGOPriceOutletId").html(response.outlet_html);

					}
				});
			}
			else
			{
				$("#OutletNGOPriceOutletId").html("<option value=''>---- Select ---- </option>");
			}
			// console.log(outlet_info);
		}
	});
</script>