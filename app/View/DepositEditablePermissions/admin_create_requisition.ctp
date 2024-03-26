
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i>
                	<?php echo __('Create Requisitions'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php if($this->App->menu_permission('DepositEditablePermissions','admin_index')){ echo $this->Html->link(__('Deposit Requisition List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                </div>
            </div> 
             
            <div class="box-body">
            	<h4 style="margin-left: -110px;margin-bottom: 30px; font-size: 16px;">
            		<?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id','empty'=>'--- select ---','options'=>$office_list,'required'=>false)); ?>
            	</h4><br>


            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Deposit Selections'); ?>
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
                        	<?php echo $this->Form->input('deposit_type', array('class' => 'form-control deposit_type','id'=>'deposit_type','empty'=>'--- select ---','required'=>false,'options'=>$instrument_types,'empty'=>'--- Select ---')); ?>
                        		
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
	                    	<th class="text-center">Office</th>
	                        <th class="text-center">Territory</th>
	                        <th class="text-center">Type</th>
	                        <th class="text-center">Instrument Type</th>
	                        <th class="text-center">Slip No</th>
	                        <th class="text-center">Deposit Date</th>
	                        <th class="text-center">Deposit Amount</th>
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
            		<?php echo __('Selected Deposit');?>
            	</h4>
            	<?php echo $this->Form->create('DepositEditablePermission', array('role' => 'form')); ?>
            	<table id="selected_deposit_table" class="table table-bordered table-striped selected_deposit_table">
	                <thead>
	                    <tr>
	                        <th class="text-center">Office</th>
	                        <th class="text-center">Territory</th>
	                        <th class="text-center">Type</th>
	                        <th class="text-center">Instrument Type</th>
	                        <th class="text-center">Slip No</th>
	                        <th class="text-center">Deposit Date</th>
	                        <th class="text-center">Deposit Amount</th>
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
		url: '<?= BASE_URL.'admin/Deposit_editable_permissions/get_territory';?>',
		type: 'post',
		data:{'office_id': 'office_id'}
	});
	
	var exist_memo_array = 0;

	$('body').on('click','.find',function() {
		var date_from = $('.date_from').val();
		var date_to = $("#date_to").val();
		var office_id = $("#office_id").val();
		var territory_id = $("#territory_id").val();
		var type = $("#deposit_type").val();

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
				url: '<?=BASE_URL?>deposit_editable_permissions/get_deposit_list',
				data: {
					office_id:office_id,
					date_from:date_from,
					date_to:date_to,
					territory_id:territory_id,
					type:type
				},
				cache: false, 
				success: function(response)
				{          
					var obj = jQuery.parseJSON(response);
					$("#search_deposit_table_tbody").empty();
					for(var i = 0; i<obj.length; i++){
						var deposit_id = obj[i].Deposit.id;
						var slip_no = obj[i].Deposit.slip_no;
						var deposit_amount = obj[i].Deposit.deposit_amount;
						var deposit_date = obj[i].Deposit.deposit_date;
						var office_id = obj[i].Territory.office_id;
						var territory_id= obj[i].Deposit.territory_id;
						var territory_name = obj[i].Territory.name;
						var office_name = obj[i].Office.office_name;
						var type = obj[i].Type.name;
						var inst_type = obj[i].InstrumentType.name==null?'':obj[i].InstrumentType.name;
						var btn = '<a class="btn btn-primary btn-xs toggleBtn add_more_'+deposit_id+'" onclick="selected_deposit('+deposit_id+');"><i class="glyphicon glyphicon-plus"></i></a>';

						if(exist_memo_array > 0){
							var sl_memo = $('#sl_memo_no_'+deposit_id).val();
							if(sl_memo == deposit_id){
								btn = '<a class="btn btn-danger btn-xs toggleBtn add_more_'+deposit_id+'" onclick="change_sl_login_button('+deposit_id+');"><i class="glyphicon glyphicon-minus"></i></a>';
							}
						}

						var recRow = 
						'<tr id ="table_row_'+deposit_id+'">\
							<td id = "office_id_'+office_id+'" class= "office_id" value = "'+office_name+'">'+office_name+'</td>\
							<td class ="territory_name" value ="'+territory_name+'">'+territory_name+'</td>\
							<td class ="type" value ="'+type+'">'+type+'</td>\
							<td class ="inst_type" value ="'+inst_type+'">'+inst_type+'</td>\
							<td class ="slip_no" value ="'+slip_no+'">'+slip_no+'</td>\
							<td class ="deposit_date" value ="'+deposit_date+'">'+deposit_date+'</td>\
							<td class ="deposit_amount" value ="'+deposit_amount+'">'+deposit_amount+'</td>\
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

	function selected_deposit(sl){

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

		var deposit_id = sl;
		var office_name = memo_arr[0];
		var territory_name = memo_arr[1];
		var type = memo_arr[2];
		var instrument_type = memo_arr[3];
		var slip_no = memo_arr[4];
		var deposit_date = memo_arr[5];
		var deposit_amount = memo_arr[6];
		var recRow = 
		'<tr class ="sl_table_row_'+deposit_id+'">\
			<td id = "sl_deposit_id_'+deposit_id+'" class= "sl_deposit_id_'+deposit_id+'" value = "'+deposit_id+'">\
				<input type = "hidden"  name="data[DepositEditablePermission][deposit_id][]" id="sl_deposit_id_'+deposit_id+'" value = "'+deposit_id+'"/>'+office_name+'\
			</td>\
			<td>'+territory_name+'</td>\
			<td>'+type+'</td>\
			<td>'+instrument_type+'</td>\
			<td>'+slip_no+'</td>\
			<td>'+deposit_date+'</td>\
			<td>'+deposit_amount+'</td>\
			<td><input type ="text" name="data[DepositEditablePermission][remarks][]" id = "remarks_'+deposit_id+'" class = "remarks_'+deposit_id+'" required/></td>\
			<td><a class="btn btn-danger btn-xs toggleBtn sl_add_more_'+deposit_id+'" onclick="change_sl_login_button('+deposit_id+');"><i class="glyphicon glyphicon-minus"></i></a></td>\
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
	  	$esl.attr("onclick","selected_deposit("+sl+")");
	  	exist_memo_array = exist_memo_array - 1;
	}
</script>
