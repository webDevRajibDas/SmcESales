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
                    <?php if($this->App->menu_permission('DepositEditablePermissions','admin_recommender_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Recommender List'), array('action' => 'recommender_list'), array('class' => 'btn btn-info', 'escape' => false)); } ?>
                    <?php if($this->App->menu_permission('DepositEditablePermissions','admin_approval_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Approval List'), array('action' => 'approval_list'), array('class' => 'btn btn-success', 'escape' => false)); } ?>
                </div>
            </div> 
             
            <div class="box-body">
            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Deposit Selections'); ?>
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
                            <th class="text-center">Territory</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Instrument Type</th>
                            <th class="text-center">slip No</th>
                            <th class="text-center">Deposit Date</th>
                            <th class="text-center">Deposit Amount</th>
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
                                <td><?php echo $per['Territory']['name']?></td>
                                <td><?php echo $per['Type']['name']?></td>
                                <td><?php echo $per['InstrumentType']['name']?></td>
                                <td><?php echo $per['Deposit']['slip_no']?></td>
                                <td><?php echo $per['Deposit']['deposit_date']?></td>
                                <td><?php echo $per['Deposit']['deposit_amount']?></td>
                                <td><?php echo $per['DepositEditablePermission']['remarks']?></td>
                                <td><?php echo $per['DepositEditablePermission']['recommender_note']?></td>
                                <td><?php echo $per['DepositEditablePermission']['approval_note']?></td>
                                <td><?php echo $status[$per['DepositEditablePermission']['status']]?></td>
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
            url: '<?=BASE_URL?>deposit_editable_permissions/get_deposit_editable_permited_list',
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
                    var deposit_id = obj[i].deposit_id;
                    var office_name = obj[i].office_name;
                    var territory_name = obj[i].territory_name;
                    var type = obj[i].type;
                    var inst_type = obj[i].inst_type==null?'':obj[i].inst_type;
                    var slip_no = obj[i].slip_no;
                    var deposit_date = obj[i].deposit_date;
                    var deposit_amount = obj[i].deposit_amount;
                    var remarks = obj[i].remarks;
                    var recommender_note = obj[i].recommender_note;
                    var approval_note = obj[i].approval_note;
                    var req_status=obj[i].status
                    req_status=status1[req_status];
                    var recRow = 
                    '<tr id ="table_row_'+deposit_id+'">\
                        <td class ="office_name" value ="'+office_name+'">'+office_name+'</td>\
                        <td class ="territory_name" value ="'+territory_name+'">'+territory_name+'</td>\
                        <td class ="type" value ="'+type+'">'+type+'</td>\
                        <td class ="inst_type" value ="'+inst_type+'">'+inst_type+'</td>\
                        <td class ="slip_no" value ="'+slip_no+'">'+slip_no+'</td>\
                        <td class ="deposit_date" value ="'+deposit_date+'">'+deposit_date+'</td>\
                        <td class ="deposit_amount" value ="'+deposit_amount+'">'+deposit_amount+'</td>\
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