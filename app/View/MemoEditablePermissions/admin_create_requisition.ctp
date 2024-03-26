
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i>
                	<?php echo __('Create Requisitions'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php if($this->App->menu_permission('MemoEditablePermissions','admin_index')){ echo $this->Html->link(__('Memo Requisition List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                </div>
            </div> 
             
            <div class="box-body">
            	<h4 style="margin-left: -110px;margin-bottom: 30px; font-size: 16px;">
            		<?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id','empty'=>'--- select ---','options'=>$office_list,'required'=>false)); ?>
            	</h4><br>


            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Memo Selections'); ?>
            	</h4>
            <div style="border: 1px solid gray;padding: 10px;">
        		<table class="search">
        			
                    <tr>
                        <td width="50%">
                        	<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from','id'=>'date_from','required'=>false)); ?>
                        	
                        </td>
                        <td width="50%">
                        	<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to','id'=>'date_to','required'=>false)); ?>
                        		
                        </td>                      
                    </tr>
                    <tr>
             
                        <td width="50%">
                        	<?php echo $this->Form->input('territory_id', array('class' => 'form-control territory_id','id'=>'territory_id','empty'=>'--- select ---','required'=>false)); ?>
                        	
                        </td>
                        <td width="50%">
                        	<?php echo $this->Form->input('thana_id', array('class' => 'form-control thana_id','id'=>'thana_id','empty'=>'--- select ---','required'=>false)); ?>
                        		
                        </td>
                                              
                    </tr>
                    <tr>
                    	
                        <td width="50%">
                        	<?php echo $this->Form->input('market_id', array('class' => 'form-control market_id','id'=>'market_id','empty'=>'--- select ---','required'=>false)); ?>
                        	
                        </td>
                        <td width="50%">
                        	<?php echo $this->Form->input('outlet_id', array('class' => 'form-control outlet_id','id'=>'outlet_id','empty'=>'--- select ---','required'=>false)); ?>
                        		
                        </td>                    
                    </tr>
                    <tr>
                    	
                        <td width="50%">
                        	<?php echo $this->Form->input('memo_no', array('class' => 'form-control memo_no','id'=>'memo_no','required'=>false)); ?>
                        	
                        </td>
                        <td width="50%" class="text-center">
                        	<?php echo $this->Form->button('<i class="fa fa-search"></i> Find', array('type' => 'button','class' => 'btn btn-large btn-primary find','id'=>'find','escape' => false)); ?>
                        </td>                
                    </tr>
                  
                </table>
                <?php echo $this->Form->end(); ?>


                <div></div>
                <br>
				<br>
                <table id="search_memo_table" class="table table-bordered table-striped search_memo_table">
	                <thead>
	                    <tr>
	                        <th class="text-center">Memo No</th>
	                        <th class="text-center">Office</th>
	                        <th class="text-center">Market</th>
	                        <th class="text-center">Outlet</th>
	                        <th class="text-center">Thana</th>
	                        <th class="text-center">Territory</th>
	                        <th width="120" class="text-center">Action</th>
	                    </tr>
	                </thead>
	                <tbody id = "search_memo_table_tbody">
	                </tbody>
            	</table>
			<br>
			<br>
			<br>
			<br>
            </div>




            <div class="box-body">
            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Selected Memo');?>
            	</h4>
            	<?php echo $this->Form->create('MemoEditablePermission', array('role' => 'form')); ?>
            	<table id="selected_memo_table" class="table table-bordered table-striped selected_memo_table">
	                <thead>
	                    <tr>
	                        <th class="text-center">Memo</th>
	                        <th class="text-center">Office</th>
	                        <th class="text-center">Market</th>
	                        <th class="text-center">Outlet</th>
	                        <th class="text-center">Thana</th>
	                        <th class="text-center">Territory</th>
	                        <th class="text-center">Remark</th>
	                        <th width="120" class="text-center">Action</th>
	                    </tr>
	                </thead>

            	</table>
            	<br>
            	<br>
            	<br>
            	<br>
            	<div class="text-right">
            		<?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-large btn-primary save','id'=>'save','escape' => false)); ?>
            	</div>
            	<?php echo $this->Form->end(); ?>
            </div>          
        </div>
    </div>
</div>
<script>

	$('.office_id').selectChain({
		target: $('.territory_id'),
		value:'name',
		url: '<?= BASE_URL.'admin/memo_editable_permissions/get_territory';?>',
		type: 'post',
		data:{'office_id': 'office_id'}
	});
	
	$('.thana_id').selectChain({
		target: $('.market_id'),
		value:'name',
		url: '<?= BASE_URL.'admin/memo_editable_permissions/get_market_by_thana';?>',
		type: 'post',
		data:{'thana_id': 'thana_id' }
	});
	$('.market_id').selectChain({
		target: $('.outlet_id'),
		value:'name',
		url: '<?= BASE_URL.'admin/memo_editable_permissions/get_outlet';?>',
		type: 'post',
		data:{'market_id': 'market_id' }
	});
	

	$('body').on('change','.territory_id',function() {
		get_thana_list($(this).val());
		
	});
	function get_thana_list(territory_id)
	{
		$.ajax
		({
			type: "POST",
			url: '<?=BASE_URL?>memos/get_thana_by_territory_id',
			data: 'territory_id='+territory_id,
			cache: false, 
			success: function(response)
			{          
				$('.thana_id').html(response); 
				<?php if(isset($this->request->data['Memo']['thana_id'])){?> 
					$('.thana_id option[value="<?=$this->request->data['Memo']['thana_id']?>"]').attr("selected",true);
				<?php }?>   
			}
		});
	}
	
	var exist_memo_array = 0;

	$('body').on('click','.find',function() {
		var date_from = $('.date_from').val();
		var date_to = $("#date_to").val();
		var office_id = $("#office_id").val();
		var territory_id = $("#territory_id").val();
		var thana_id = $("#thana_id").val();
		var market_id = $("#market_id").val();
		var outlet_id = $("#outlet_id").val();
		var memo_no = $("#memo_no").val();

		console.log(date_from);
		console.log(date_to);
		console.log(office_id);
		console.log(territory_id);
		console.log(thana_id);
		console.log(market_id);
		console.log(memo_no);

		if(office_id == ''){
			alert('Please Select Office');
            return false;
		}
		else{
			$.ajax
			({
				type: "POST",
				url: '<?=BASE_URL?>memo_editable_permissions/get_memo_list',
				data: {office_id:office_id,date_from:date_from,date_to:date_to,territory_id:territory_id,thana_id:thana_id,market_id:market_id,outlet_id:outlet_id,memo_no:memo_no},
				cache: false, 
				success: function(response)
				{          
					var obj = jQuery.parseJSON(response);
					$("#search_memo_table_tbody").empty();
					console.log(obj);
					for(var i = 0; i<obj.length; i++){
						var memo_id = obj[i].Memo.id;
						var memo_no = obj[i].Memo.memo_no;
						var office_id = obj[i].Memo.office_id;
						var territory_id= obj[i].Memo.territory_id;
						var market_id = obj[i].Memo.market_id;
						var outlet_id = obj[i].Memo.outlet_id;
						var market_name = obj[i].Market.name;
						var outlet_name = obj[i].Outlet.name;
						var territory_name = obj[i].Territory.name;
						var office_name = obj[i].Office.office_name;
						var thana_name = obj[i].Thana.name;
						var btn = '<a class="btn btn-primary btn-xs toggleBtn add_more_'+memo_id+'" onclick="selected_memo('+memo_id+');"><i class="glyphicon glyphicon-plus"></i></a>';

						if(exist_memo_array > 0){
							var sl_memo = $('#sl_memo_no_'+memo_id).val();
							if(sl_memo == memo_id){
								btn = '<a class="btn btn-danger btn-xs toggleBtn add_more_'+memo_id+'" onclick="change_sl_login_button('+memo_id+');"><i class="glyphicon glyphicon-minus"></i></a>';
							}
						}

						var recRow = '<tr id ="table_row_'+memo_id+'"><td id = "memo_id_'+memo_id+'" class= "memo_id" value = "'+memo_no+'">'+memo_no+'</td><td id = "office_id_'+office_id+'" class= "office_id" value = "'+office_name+'">'+office_name+'</td><td class ="market_name" value ="'+market_name+'">'+market_name+'</td><td class ="outlet_name" value ="'+outlet_name+'">'+outlet_name+'</td><td class ="'+thana_name+'" value ="">'+thana_name+'</td><td class ="territory_name" value ="'+territory_name+'">'+territory_name+'</td><td>'+btn+'</td></tr>';
						$('.search_memo_table').append(recRow);
					}

				}
			});
		}
	});

	function change_login_button(sl) {
	  var $el = $('.add_more_'+sl);
	  $el.toggleClass('btn-danger btn-primary');
	  $el.find('i.glyphicon').toggleClass('glyphicon-minus glyphicon-plus');
	  var memo_arr =[];
	  $el.closest('tr').find('td').each(
	    function (i) {
	      memo_arr[i] = $(this).text();
	    });

	  selected_memo(sl,memo_arr);
	}

	function selected_memo(sl){

		var $el = $('.add_more_'+sl);
		$el.toggleClass('btn-danger btn-primary');
		$el.find('i.glyphicon').toggleClass('glyphicon-minus glyphicon-plus');
		var memo_arr =[];
		$el.closest('tr').find('td').each(
		function (i) {
		  memo_arr[i] = $(this).text();
		});

		exist_memo_array = exist_memo_array + 1;

		var memo_id = sl;
		var memo_no = memo_arr[0];
		var office_name = memo_arr[1];
		var market_name = memo_arr[2];
		var outlet_name = memo_arr[3];
		var thana_name = memo_arr[4];
		var territory_name = memo_arr[5];
		var recRow = '<tr class ="sl_table_row_'+memo_id+'"><td id = "sl_memo_id_'+memo_id+'" class= "sl_memo_id_'+memo_id+'" value = "'+memo_id+'"><input type = "hidden"  name="data[MemoEditablePermission][memo_id][]" id="sl_memo_no_'+memo_id+'" value = "'+memo_id+'"/>'+memo_no+'</td><td>'+office_name+'</td><td>'+market_name+'</td><td>'+outlet_name+'</td><td>'+thana_name+'</td><td>'+territory_name+'</td><td><input type ="text" name="data[MemoEditablePermission][remarks][]" id = "remarks_'+memo_id+'" class = "remarks_'+memo_id+'" required/></td><td><a class="btn btn-danger btn-xs toggleBtn sl_add_more_'+memo_id+'" onclick="change_sl_login_button('+memo_id+');"><i class="glyphicon glyphicon-minus"></i></a></td></tr>';
		
		$('.selected_memo_table').append(recRow);
		$('.add_more_'+sl).attr("onclick","change_sl_login_button("+sl+")");
	}
		
	

	function change_sl_login_button(sl){
		var $el = $('.sl_add_more_'+sl);
		$el.closest('tr').remove();

		var $esl = $('.add_more_'+sl);
		$esl.toggleClass('btn-danger btn-primary');
	  	$esl.find('i.glyphicon').toggleClass('glyphicon-minus glyphicon-plus');
	  	$esl.attr("onclick","selected_memo("+sl+")");
	  	exist_memo_array = exist_memo_array - 1;
	}
</script>
