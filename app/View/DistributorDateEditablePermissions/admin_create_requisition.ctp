
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i>
                	<?php echo __('Create Requisitions'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php if($this->App->menu_permission('DistributorDateEditablePermissions','admin_index')){ echo $this->Html->link(__('Collection Requisition List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                </div>
            </div> 
             
            <div class="box-body">
            	<h4 style="margin-left: -110px;margin-bottom: 30px; font-size: 16px;">
            		<?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id','empty'=>'--- select ---','options'=>$office_list,'required'=>false)); ?>
            	</h4><br>


            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Distributor Selections'); ?>
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
                        	<?php echo $this->Form->input('outlet_id', array('label' => 'Distributor:','id' => 'distribut_outlet_id','class' => 'form-control distribut_outlet_id','required'=>false,'empty'=>'---- Select Distributers ----')); ?>
                        	
                        </td>
                        <td width="50%">

                        		
                        </td>                      
                    </tr>
                   

                    <tr>
                    	<td></td>
                        <td width="50%" class="text-center">
                        	<?php echo $this->Form->button('<i class="fa fa-search"></i> Find', array('type' => 'button','class' => 'btn btn-large btn-primary find','id'=>'find','escape' => false)); ?>
                        </td>                
                    </tr>
                  
                </table>
                <?php echo $this->Form->end(); ?>


                <div></div>
                <br>
				<br>
                <table id="search_deposit_table" class="table table-bordered table-striped search_deposit_table">
	                <thead>
	                    <tr>
	                    	<th class="text-center">ID</th>
	                        <th class="text-center">Order Reference No</th>
	                        <th class="text-center">Outlet</th>
	                        <th class="text-center">Market</th>
	                        <th class="text-center">Territory</th>
	                        <th class="text-center">Order Total </th>
	                        <th class="text-center">Order Date</th>
	                        <th class="text-center">Confirm Status</th>
	                        <th width="120" class="text-center">Action</th>
	                    </tr>
	                </thead>
	                <tbody id = "search_deposit_table_tbody">
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
            		<?php echo __('Selected Distributor');?>
            	</h4>
            	<?php echo $this->Form->create('DepositEditablePermission', array('role' => 'form')); ?>
            	<table id="selected_deposit_table" class="table table-bordered table-striped selected_deposit_table">
	                <thead>
	                    <tr>
	                        <th class="text-center">ID</th>
	                        <th class="text-center">Order Reference No</th>
	                        <th class="text-center">Outlet</th>
	                        <th class="text-center">Market</th>
	                        <th class="text-center">Territory</th>
	                        <th class="text-center">Order Total </th>
	                        <th class="text-center">Order Date</th>
	                        <th class="text-center">Confirm Status</th>
	                        <th class="text-center">Remarks</th>
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
        target: $('.distribut_outlet_id'),
        value:'name',
        url: '<?= BASE_URL.'sales_people/get_outlet_list_with_distributor_name';?>',
        type: 'post',
        data:{'office_id': 'office_id'}

  });

	
	var exist_memo_array = 0;

	$('body').on('click','.find',function() {
		var date_from = $('.date_from').val();
		var date_to = $("#date_to").val();
		var office_id = $("#office_id").val();
		var distribut_outlet_id = $("#distribut_outlet_id").val();
		

		if(office_id == '')
		{
			alert('Please Select Office');
            return false;
		}
		else
		{
			$.ajax
			({
				type: "POST",
				url: '<?=BASE_URL?>distributor_date_editable_permissions/get_distributor_issues_list',
				data: {
					office_id:office_id,
					date_from:date_from,
					date_to:date_to,
					distribut_outlet_id:distribut_outlet_id
				},
				cache: false, 
				success: function(response)
				{     
					console.log(response);

					var obj = jQuery.parseJSON(response);
					$("#search_deposit_table_tbody").empty();
					for(var i = 0; i<obj.length; i++){
						var order_id = obj[i].Order.id;
						var order_no = obj[i].Order.order_no;
						var order_date = obj[i].Order.order_date;
						var order_toal = obj[i].Order.gross_value;
						var confirmed = obj[i].Order.confirmed;
						var outlet = obj[i].Outlet.name;
						var territory = obj[i].Territory.name;
						var market = obj[i].Market.name;

						if(confirmed == 1){
							var status ="Confirmed";
						}else{
							var status ="";
						}

						
						var btn = '<a class="btn btn-primary btn-xs toggleBtn add_more_'+order_id+'" onclick="selected_order('+order_id+');"><i class="glyphicon glyphicon-plus"></i></a>';

						

						var recRow = 
						'<tr id ="table_row_'+order_id+'">\
							<td class ="order_id" value ="'+order_id+'">'+order_id+'</td>\
							<td class ="order_no" value ="'+order_no+'">'+order_no+'</td>\
							<td class ="outlet" value ="'+outlet+'">'+outlet+'</td>\
							<td class ="market" value ="'+market+'">'+market+'</td>\
							<td class ="territory" value ="'+territory+'">'+territory+'</td>\
							<td class ="order_toal" value ="'+order_toal+'">'+order_toal+'</td>\
							<td class ="order_date" value ="'+order_date+'">'+order_date+'</td>\
							<td class ="status" value ="'+status+'">'+status+'</td>\
							<td>'+btn+'</td>\
						</tr>';
						$('.search_deposit_table').append(recRow);
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

	function selected_order(sl){

		var $el = $('.add_more_'+sl);
		$el.toggleClass('btn-danger btn-primary');
		$el.find('i.glyphicon').toggleClass('glyphicon-minus glyphicon-plus');
		var memo_arr =[];
		$el.closest('tr').find('td').each(
		function (i) {
		  memo_arr[i] = $(this).text();
		});
		console.log(memo_arr);
		exist_memo_array = exist_memo_array + 1;

		var order_id = sl;
		var order_no = memo_arr[1];
		var outlet = memo_arr[2];
		var market = memo_arr[3];
		var territory = memo_arr[4];
		var order_toal = memo_arr[5];
		var order_date = memo_arr[6];
		var status = memo_arr[7];

		console.log(memo_arr);
		
		var recRow = 
		'<tr class ="sl_table_row_'+order_id+'">\
			<td id = "sl_deposit_id_'+order_id+'" class= "sl_deposit_id_'+order_id+'" value = "'+order_id+'">\
				<input type = "hidden"  name="data[DistributorDateEditablePermission][order_id][]" id="sl_deposit_id_'+order_id+'" value = "'+order_id+'"/>'+order_id+'\
			</td>\
			<td>'+order_no+'</td>\
			<td>'+outlet+'</td>\
			<td>'+market+'</td>\
			<td>'+territory+'</td>\
			<td>'+order_toal+'</td>\
			<td>'+order_date+'</td>\
			<td>'+status+'</td>\
			<td><input type ="text" name="data[DistributorDateEditablePermission][remarks][]" id = "remarks_'+order_id+'" class = "remarks_'+order_id+'" required/></td>\
			<td><a class="btn btn-danger btn-xs toggleBtn sl_add_more_'+order_id+'" onclick="change_sl_login_button('+order_id+');"><i class="glyphicon glyphicon-minus"></i></a></td>\
		</tr>';
		
		$('.selected_deposit_table').append(recRow);
		$('.add_more_'+sl).attr("onclick","change_sl_login_button("+sl+")");
	}
		
	

	function change_sl_login_button(sl){
		var $el = $('.sl_add_more_'+sl);
		$el.closest('tr').remove();

		var $esl = $('.add_more_'+sl);
		$esl.toggleClass('btn-danger btn-primary');
	  	$esl.find('i.glyphicon').toggleClass('glyphicon-minus glyphicon-plus');
	  	$esl.attr("onclick","selected_order("+sl+")");
	  	exist_memo_array = exist_memo_array - 1;
	}
</script>
