
<style type="text/css">
	.territory_box
	{
		width: 652px;
		min-height: 100px;
		border: 1px dotted black;
		padding: 5px;
		margin-bottom: 5px;
		margin-left: 190px;
		display: none;
	}
	.territory_box label
	{
		width:20%;
	}
	.territory_box .form-control
	{
		width:75%;
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
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Admin Add Special Group'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Special Group List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('SpecialGroup', array('role' => 'form')); ?>
				<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('type'=>'text','class' => 'form-control','label'=>'Policy Name:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('remarks', array('type'=>'text','class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('start_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'Start Date:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('end_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'End Date:')); ?>
				</div>
				
				
				<?php /*?><div class="form-group">
					<?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id', 'multiple' => true, 'name'=>'data[GroupWiseDiscountBonusPolicyToOffice][office_id]', 'required'=>false)); ?>
				</div><?php */?>
				
				<div class="form-group">
					<label for="office_id">Office :</label>
					<div class="input select">
					<select name="data[SpecialGroup][office_id][]" id="office_id" class="form-control office_id div_select" multiple="multiple">
						<?php foreach($offices as $o_key => $o_val){ ?>
							<option <?php if(array_search($o_key,$office_ids)!==false){echo 'Selected';} ?> value="<?=$o_key;?>"><?=$o_val;?></option>
						<?php } ?>
					</select>
					</div>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_territory', array('label'=>'<b>Territory Add :</b>','type'=>'checkbox','class' => 'checkbox-inline is_territory','div'=>false,'checked'=>count($territory_ids)>0?true:false)); ?>
				</div>
				<div class="territory_box">
					<div class="form-group">
						<?php echo $this->Form->input('office_for_territory', array('id' => 'office_for_territory','label'=>'Office :','class' => 'form-control office_for_territory div_select', 'type'=>'select','empty'=>'--- Select ---', 'required'=>false)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('territory', array('id' => 'territory','label'=>'Territory :','class' => 'form-control territory div_select', 'name'=>'dd','empty'=>'--- Select ---', 'type'=>'select', 'required'=>false)); ?>
						<a href="#" id="add_outlet" class="btn btn-primary btn-sm pull-right">Add</a>
						<a href="#" id="remove_outlet" class="btn btn-primary btn-sm pull-right">Remove All</a>
					</div>
					<div class="form-group">
						<div class="item_box" style="width:80%;float:left;margin-left: 10%">
						<h5>Territory List :</h5>
						<table class="table table-bordered" >
							<thead>
								<tr>
									<th width="50%">Office</th>
									<th width="30%">Territory</th>
									<th width="10%">#</th>
								</tr>
							</thead>
							<tbody>
								<?php
							if(!empty($territory_ids)){
								foreach($territory_ids as $val){
									?>
									<tr>
										<td>
											<?=$val['Office']['office_name']?>
										</td>
										<td>
											<?=$val['Territory']['name']?>
											<select class="hidde_select" name="data[SpecialGroup][territory_id][]">
												<option value="<?=$val['Territory']['id']?>"><?=$val['Territory']['name']?></option>
											</select>
										</td>
										<td>
											<i id="<?=$val['Territory']['id']?>" class="glyphicon glyphicon-remove icon"></i>
										</td>
									</tr>
									</span>
									<?php
								}
							}
							?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
				<div class="form-group">

					<label for="office_id">Outlet Group :</label>
					<div class="input select">
					<select name="data[SpecialGroup][outlet_group_id][]" id="outlet_group_id" class="form-control chosen div_select" multiple="multiple">
						<?php foreach($outlet_groups as $o_key => $o_val){ ?>
							<option <?php if(array_search($o_key,$outlet_group_id)!==false){echo 'Selected';} ?> value="<?=$o_key;?>"><?=$o_val;?></option>
						<?php } ?>
					</select>
					</div>
				</div>
				
				<div class="form-group">
					<label for="office_id">Outlet Category :</label>
					<div class="input select">
					<select name="data[SpecialGroup][outlet_category_id][]" id="outlet_group_id" class="form-control chosen div_select" multiple="multiple">
						<?php foreach($outlet_categories as $o_key => $o_val){ ?>
							<option <?php if(array_search($o_key,$outlet_category_id)!==false){echo 'Selected';} ?> value="<?=$o_key;?>"><?=$o_val;?></option>
						<?php } ?>
					</select>
					</div>
				</div>					

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
	
$(document).ready(function(){
	var territory_info=new Array();
	$("input[type='checkbox']").iCheck('destroy');
    $("input[type='radio']").iCheck('destroy');
	$(".chosen").chosen();
	$(".office_id").chosen();
	$(".office_id").data("placeholder", "Select Offices...").chosen();
	$("body").on('click','.is_territory',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$('.territory_box').show();
	    }
	    else
	    {
	    	$('.territory_box').hide();
	    }
    });
    $('.is_territory').trigger('click');
    put_office_in_office_id_for_territory();
    function put_office_in_office_id_for_territory()
    {
		var option='<option value="">--- Select ---</option>';
    	$('.office_id option:selected').each(function(index,value){
    		option+='<option value="'+$(this).val()+'">'+$(this).text()+'</option>';
    	});
    	$('.territory_box .office_for_territory').html(option);
    }
    $('.office_id').change(function(){
    	put_office_in_office_id_for_territory();
    });
    $(".office_for_territory").change(function(){
		get_territory_list();
	});
    function get_territory_list()
	{
		var office_id = $('.office_for_territory').val();
		if(office_id)
		{
			$.ajax({
				url:  "<?php echo BASE_URL; ?>special_groups/get_territory_list",
				type:"POST",
				data:{office_id:office_id},
				success: function(result){
					var response=$.parseJSON(result);
					// console.log(response);
					territory_info=response.other_info;
					$("#territory").html(response.territory_html);

				}
			});
		}
		else
		{
			$("#territory").html("<option value=''>---- All ---- </option>");
		}
		// console.log(outlet_info);
	}

	$("#add_outlet").click(function()
	{
			
		var btn = $(".item_box>table>tbody").html();
		var all_val = $("#territory option:selected").val();
		if(all_val == 'all'){
			var territory_list = [];
			$( "#territory option" ).each(function()
			{
				var territory_id = $( this ).attr("value");
				var territory_name = $( this ).html();
				if(territory_id != 'all')
				{
					territory_list[territory_id] = territory_name;
				}
			});
			for(var key in territory_list){
				var territory_details=territory_info[key];
				var office_name=territory_details.office;
				var territory_id = key;
				var territory_name = territory_list[key];


				var new_btn = '<tr>\
				<td>'+office_name+'</td>\
					<td>'+territory_name+'\
						<select class="hidde_select" name="data\[SpecialGroup\]\[territory_id\]\[\]">\
							<option value="'+territory_id+'">'+territory_name+'</option>\
						</select>\
					</td>\
					<td>\
						<i id="'+territory_id+'" class="glyphicon glyphicon-remove icon"></i>\
					</td>\
					</tr>';


				if(btn.search(territory_id) == -1)
				{
					btn = btn+new_btn;
					// $(".item_box>table>tbody").html(btn);
					$(".item_box>table>tbody").append(new_btn);
				}
				else
				{
					alert("Please select another! "+territory_name +" Already Selected");
				};
			}
		}
		else
		{
			var territory_name = $("#territory option:selected").html();
			var territory_id = $("#territory").val();

			var territory_details=territory_info[territory_id];
			var office_name=territory_details.office;

			var new_btn = '<tr>\
			<td>'+office_name+'</td>\
					<td>'+territory_name+'\
						<select class="hidde_select" name="data\[SpecialGroup\]\[territory_id\]\[\]">\
							<option value="'+territory_id+'">'+territory_name+'</option>\
						</select>\
					</td>\
					<td>\
						<i id="'+territory_id+'" class="glyphicon glyphicon-remove icon"></i>\
					</td>\
					</tr>';
			if(territory_id != '')
			{
				if(btn.search(territory_id) == -1 /*&& btn.search(outlet_name) == -1*/)
				{
					btn = btn+new_btn;
					// $(".item_box>table>tbody").html(btn);
					$(".item_box>table>tbody").append(new_btn);
				}
				else
				{
					alert("Please select another! "+territory_name +" Already Selected");
				};
			}
			else
			{
				alert("Please select any Territory!");
			}
		}
	});
	/*--------- delete single outlet ---------*/
	$("body").on("click",".icon",function(e){
		e.preventDefault();
		$(this).parent().parent().remove();
	});
	/*------- delete all outlet -------*/
	$("body").on("click","#remove_outlet",function(){
		var btn = $(".item_box>table>tbody").html();
        var all_val = $("#territory option:selected").val();
		if(all_val == 'all')
		{
            var territory_list_del = [];
            $( "#territory option" ).each(function() {
                var territory_id = $( this ).attr("value");
                var territory_name = $( this ).html();
                if(territory_id != 'all'){
                territory_list_del[territory_id] = territory_name;
                }
            });
            var btn_del = '';
            for(var key in territory_list_del)
            {
                var territory_id = key;
                var territory_name = territory_list_del[key];
                $(".item_box>table>tbody> tr td:nth-child(3)").find("#"+territory_id).parent().parent().remove();
                
            }
           
        }
	});
});
</script>