
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i>
                	<?php echo __('Create Requisitions'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php if($this->App->menu_permission('CollectionEditablePermissions','admin_index')){ echo $this->Html->link(__('Collection Requisition List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                </div>
            </div> 
             
            <div class="box-body">
            	<h4 style="margin-left: -110px;margin-bottom: 30px; font-size: 16px;">
            		<?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id','empty'=>'--- select ---','options'=>$office_list,'required'=>false)); ?>
            	</h4><br>


            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Collection Selections'); ?>
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
                        	<?php echo $this->Form->input('type', array('class' => 'form-control deposit_type','id'=>'deposit_type','empty'=>'--- select ---','required'=>false,'options'=>$instrument_types,'empty'=>'--- Select ---')); ?>
                        		
                        </td>
                                              
                    </tr>
                    <tr>
                    	<td><?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----')); ?></td>
                    	<td><?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----')); ?></td>
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
	                    	<th class="text-center">Momo</th>
	                        <th class="text-center">Instrument Ref.No</th>
	                        <th class="text-center">Outlet</th>
	                        <th class="text-center">Type</th>
	                        <th class="text-center">Instrument Type</th>
	                        <th class="text-center">Instrument No</th>
	                        <th class="text-center">Collection Date</th>
	                        <th class="text-center">Collection Amount</th>
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
            		<?php echo __('Selected Collection');?>
            	</h4>
            	<?php echo $this->Form->create('DepositEditablePermission', array('role' => 'form')); ?>
            	<table id="selected_deposit_table" class="table table-bordered table-striped selected_deposit_table">
	                <thead>
	                    <tr>
	                        <th class="text-center">Momo</th>
	                        <th class="text-center">Instrument Ref.No</th>
	                        <th class="text-center">Outlet</th>
	                        <th class="text-center">Type</th>
	                        <th class="text-center">Instrument Type</th>
	                        <th class="text-center">Instrument No</th>
	                        <th class="text-center">Collection Date</th>
	                        <th class="text-center">Collection Amount</th>
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

	$('.territory_id').selectChain({
		target: $('.market_id'),
		value:'name',
		url: '<?= BASE_URL.'admin/doctors/get_market';?>',
		type: 'post',
		data:{'territory_id': 'territory_id' }
	});

	$('.market_id').selectChain({
		target: $('.outlet_id'),
		value:'name',
		url: '<?= BASE_URL.'admin/doctors/get_outlet';?>',
		type: 'post',
		data:{'market_id': 'market_id' }
	});

	$('.territory_id').change(function(){
		$('.outlet_id').html('<option value="">---- Select Outlet ----');
	});
	
	var exist_memo_array = 0;

	$('body').on('click','.find',function() {
		var date_from = $('.date_from').val();
		var date_to = $("#date_to").val();
		var office_id = $("#office_id").val();
		var territory_id = $("#territory_id").val();
		var market_id = $("#market_id").val();
		var outlet_id = $("#outlet_id").val();
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
				url: '<?=BASE_URL?>collection_editable_permissions/get_collection_list',
				data: {
					office_id:office_id,
					date_from:date_from,
					date_to:date_to,
					territory_id:territory_id,
					market_id:market_id,
					outlet_id:outlet_id,
					type:type
				},
				cache: false, 
				success: function(response)
				{     
					console.log(response);     
					var obj = jQuery.parseJSON(response);
					$("#search_deposit_table_tbody").empty();
					for(var i = 0; i<obj.length; i++){
						var collection_id = obj[i].Collection.id;
						var instrumentRefNo = obj[i].Collection.instrumentRefNo;
						var memo = obj[i].Collection.memo_no;
						var outlet = obj[i].Outlet.name;
						var gettype = obj[i].Collection.type;

						if(gettype == 1){
							var type ="Cash";
						}else{
							var type ="Instrument";
						}

						var collectionDate = obj[i].Collection.collectionDate;
						var instrument_no = obj[i].Collection.instrument_no;
						var collectionAmount = obj[i].Collection.collectionAmount;
						var inst_type = obj[i].InstrumentType.name==null?'':obj[i].InstrumentType.name;
						
						var btn = '<a class="btn btn-primary btn-xs toggleBtn add_more_'+collection_id+'" onclick="selected_collection('+collection_id+');"><i class="glyphicon glyphicon-plus"></i></a>';

						

						var recRow = 
						'<tr id ="table_row_'+collection_id+'">\
							<td class ="memo" value ="'+memo+'">'+memo+'</td>\
							<td class ="instrumentRefNo" value ="'+instrumentRefNo+'">'+instrumentRefNo+'</td>\
							<td class ="outlet" value ="'+outlet+'">'+outlet+'</td>\
							<td class ="type" value ="'+type+'">'+type+'</td>\
							<td class ="inst_type" value ="'+inst_type+'">'+inst_type+'</td>\
							<td class ="instrument_no" value ="'+instrument_no+'">'+instrument_no+'</td>\
							<td class ="collectionDate" value ="'+collectionDate+'">'+collectionDate+'</td>\
							<td class ="collectionAmount" value ="'+collectionAmount+'">'+collectionAmount+'</td>\
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

	function selected_collection(sl){

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

		var collection_id = sl;
		var memo = memo_arr[0];
		var instrumentRefNo = memo_arr[1];
		var outlet = memo_arr[2];
		var type = memo_arr[3];
		var inst_type = memo_arr[4];
		var instrument_no = memo_arr[5];
		var collectionDate = memo_arr[6];
		var collectionAmount = memo_arr[7];
		
		var recRow = 
		'<tr class ="sl_table_row_'+collection_id+'">\
			<td id = "sl_deposit_id_'+collection_id+'" class= "sl_deposit_id_'+collection_id+'" value = "'+collection_id+'">\
				<input type = "hidden"  name="data[CollectionEditablePermission][collection_id][]" id="sl_deposit_id_'+collection_id+'" value = "'+collection_id+'"/>'+memo+'\
			</td>\
			<td>'+instrumentRefNo+'</td>\
			<td>'+outlet+'</td>\
			<td>'+type+'</td>\
			<td>'+inst_type+'</td>\
			<td>'+instrument_no+'</td>\
			<td>'+collectionDate+'</td>\
			<td>'+collectionAmount+'</td>\
			<td><input type ="text" name="data[CollectionEditablePermission][remarks][]" id = "remarks_'+collection_id+'" class = "remarks_'+collection_id+'" required/></td>\
			<td><a class="btn btn-danger btn-xs toggleBtn sl_add_more_'+collection_id+'" onclick="change_sl_login_button('+collection_id+');"><i class="glyphicon glyphicon-minus"></i></a></td>\
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
