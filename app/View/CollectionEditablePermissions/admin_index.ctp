<?php 
    $status=array(
        "0"=>'Waiting For Recommendation',
        "1"=>'Waiting For Approval',
        "2"=>'Approved',
        "3"=>'Rejected'
        );

 ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i>
                	<?php echo __('All Requisition List'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php if($this->App->menu_permission('DepositEditablePermissions','admin_create_requisition')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Create Requisition'), array('action' => 'create_requisition'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                    <?php if($this->App->menu_permission('CollectionEditablePermissions','admin_recommender_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Recommender List'), array('action' => 'recommender_list'), array('class' => 'btn btn-info', 'escape' => false)); } ?>
                    <?php if($this->App->menu_permission('CollectionEditablePermissions','admin_approval_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Approval List'), array('action' => 'approval_list'), array('class' => 'btn btn-success', 'escape' => false)); } ?>
                </div>
            </div> 
             
            <div class="box-body">
            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Collection Selections'); ?>
            	</h4>
        		<div class="box-body">
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
                                <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id','empty'=>'--- select ---','options'=>$office_list,'required'=>false)); ?>
                            </td>
                            <td width="50%">
                               <?php echo $this->Form->input('territory_id', array('class' => 'form-control territory_id','id'=>'territory_id','empty'=>'--- select ---','options'=>$territory_list,'required'=>false)); ?>
                                
                            </td>
                                                  
                        </tr>
                        <tr>
                            <td width="50%">
                                <?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----')); ?>
                            </td>
                            <td width="50%">
                                <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----')); ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%">
                                <?php echo $this->Form->input('type', array('class' => 'form-control type','id'=>'type','empty'=>'--- select ---','options'=>$instrument_types,'required'=>false)); ?>
                            </td>
                            <td width="50%" class="text-center">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Find', array('type' => 'button','class' => 'btn btn-large btn-primary find','id'=>'find','escape' => false)); ?>
                            </td>
                        </tr>
                    </table>      
                </div>
                <?php echo $this->Form->end(); ?>
                <div></div>
                <table id="search_memo_table" class="table table-bordered table-striped search_memo_table">
	                <thead>
	                    <tr>
                            <th class="text-center">Office</th>
                            <th class="text-center">Memo</th>
                            <th class="text-center">Outlet</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Instrument Type</th>
                            <th class="text-center">Instrument Ref.No</th>
                            <th class="text-center">Collection Date</th>
                            <th class="text-center">Collection Amount</th>
                            <th class="text-center">Requisition Note</th>
                            <th class="text-center">Recommended Note</th>
                            <th class="text-center">Approval Note</th>
	                        <th class="text-center">Status</th>
	                    </tr>
	                </thead>
                    <tbody id = "list_table">
                        <?php if(!empty($permited)){
                            foreach ($permited as $key => $per) {?>
                            <tr>
                                <td><?php echo $per['Office']['office_name']?></td>
                                <td><?php echo $per['Collection']['memo_no']?></td>
                                <td><?php echo $per['Outlet']['name']?></td>
                                <td><?php echo $per['Type']['name']?></td>
                                <td><?php echo $per['InstrumentType']['name']?></td>
                                <td><?php echo $per['Collection']['instrumentRefNo']?></td>
                                <td><?php echo $per['Collection']['collectionDate']?></td>
                                <td><?php echo $per['Collection']['collectionAmount']?></td>
                                
                                <td><?php echo $per['CollectionEditablePermission']['remarks']?></td>
                                <td><?php echo $per['CollectionEditablePermission']['recommender_note']?></td>
                                <td><?php echo $per['CollectionEditablePermission']['approval_note']?></td>
                                <td><?php echo $status[$per['CollectionEditablePermission']['status']]?></td>
                            </tr>
                        <?php }
                         }
                         ?>
                    </tbody>
            	</table>
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
    
    if( $('.office_id').val())
    {
         $('.office_id').trigger('change');
    }
    var status1=<?php echo json_encode(array('status'=>$status),JSON_FORCE_OBJECT); ?>;
    status1=status1.status;
    
	$('body').on('click','.find',function() {
        var date_from = $('.date_from').val();
        var date_to = $("#date_to").val();
        var office_id = $("#office_id").val();
        var territory_id = $("#territory_id").val();
        var type = $("#type").val();
        $.ajax
        ({
            type: "POST",
            url: '<?=BASE_URL?>collection_editable_permissions/get_collection_editable_permited_list',
            data: 
            {
                office_id:office_id,
                date_from:date_from,
                date_to:date_to,
                territory_id:territory_id,
                type:type,
            },
            cache: false, 
            success: function(response)
            {          
                var obj = jQuery.parseJSON(response);
                $("#list_table").empty();
                for(var i = 0; i<obj.length; i++){
                    var collection_id = obj[i].id;
                    var office_name = obj[i].office_name;
                    var memo_no = obj[i].memo_no;
                    var outlet_name = obj[i].outlet_name;
                    var type = obj[i].type;
                    var inst_type = obj[i].instrument==null?'':obj[i].instrument;
                    var instrumentRefNo = obj[i].instrumentRefNo;
                    var collectionDate = obj[i].collectionDate;
                    var collectionAmount = obj[i].collectionAmount;
                    var remarks = obj[i].remarks;
                    var recommender_note = obj[i].recommender_note;
                    var approval_note = obj[i].approval_note;
                    var req_status=obj[i].status
                    req_status=status1[req_status];
                    var recRow = 
                    '<tr id ="table_row_'+collection_id+'">\
                        <td class ="office_name" value ="'+office_name+'">'+office_name+'</td>\
                        <td class ="memo_no" value ="'+memo_no+'">'+memo_no+'</td>\
                        <td class ="outlet_name" value ="'+outlet_name+'">'+outlet_name+'</td>\
                        <td class ="type" value ="'+type+'">'+type+'</td>\
                        <td class ="inst_type" value ="'+inst_type+'">'+inst_type+'</td>\
                        <td class ="instrumentRefNo" value ="'+instrumentRefNo+'">'+instrumentRefNo+'</td>\
                        <td class ="collectionDate" value ="'+collectionDate+'">'+collectionDate+'</td>\
                        <td class ="collectionAmount" value ="'+collectionAmount+'">'+collectionAmount+'</td>\
                        <td class ="remarks" value ="'+remarks+'">'+remarks+'</td>\
                        <td class ="recommender_note" value ="'+recommender_note+'">'+recommender_note+'</td>\
                        <td class ="approval_note" value ="'+approval_note+'">'+approval_note+'</td>\
                        <td class ="approval_note">'+req_status+'</td>\
                    </tr>';
                    $('#search_memo_table').append(recRow);
                }

            }
        });
    });
</script>