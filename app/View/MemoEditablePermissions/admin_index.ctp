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
                    <?php if($this->App->menu_permission('MemoEditablePermissions','admin_create_requisition')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Create Requisition'), array('action' => 'create_requisition'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                    <?php if($this->App->menu_permission('MemoEditablePermissions','admin_recommender_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Recommender List'), array('action' => 'recommender_list'), array('class' => 'btn btn-info', 'escape' => false)); } ?>
                    <?php if($this->App->menu_permission('MemoEditablePermissions','admin_approval_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Approval List'), array('action' => 'approval_list'), array('class' => 'btn btn-success', 'escape' => false)); } ?>
                </div>
            </div> 
             
            <div class="box-body">
            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Memo Selections'); ?>
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
                                <?php echo $this->Form->input('memo_no', array('class' => 'form-control memo_no','id'=>'memo_no','required'=>false)); ?>
                                
                            </td>
                                                  
                        </tr>
                        <tr>
                            <td width="50%"></td>
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
	                        <th class="text-center">Memo No</th>
                            <th class="text-center">Office</th>
	                        <th class="text-center">Market</th>
	                        <th class="text-center">Outlet</th>
	                        <th class="text-center">Thana</th>
                            <th class="text-center">Territory</th>
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
                                <td><a href="<?php echo BASE_URL;?>admin/MemoEditablePermissions/view/<?php echo $per['Memo']['id'];?>" target="_blank"><?php echo $per['MemoEditablePermission']['memo_no']?></a></td>
                                <td><?php echo $per['Office']['office_name']?></td>
                                <td><?php echo $per['Market']['name']?></td>
                                <td><?php echo $per['Outlet']['name']?></td>
                                <td><?php echo $per['Thana']['name']?></td>
                                <td><?php echo $per['Territory']['name']?></td>
                                <td><?php echo $per['MemoEditablePermission']['remarks']?></td>
                                <td><?php echo $per['MemoEditablePermission']['recommender_note']?></td>
                                <td><?php echo $per['MemoEditablePermission']['approval_note']?></td>
                                <td><?php echo $status[$per['MemoEditablePermission']['status']]?></td>
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
    var status1=<?php echo json_encode(array('status'=>$status),JSON_FORCE_OBJECT); ?>;
    status1=status1.status;
    $('body').on('click','.find',function() {
        var date_from = $('.date_from').val();
        var date_to = $("#date_to").val();
        var office_id = $("#office_id").val();
        var memo_no = $("#memo_no").val();
        $.ajax
        ({
            type: "POST",
            url: '<?=BASE_URL?>memo_editable_permissions/get_memo_editable_permited_list',
            data: {office_id:office_id,date_from:date_from,date_to:date_to,memo_no:memo_no},
            cache: false, 
            success: function(response)
            {          
                var obj = jQuery.parseJSON(response);
                $("#list_table").empty();
                for(var i = 0; i<obj.length; i++){
                    var memo_id = obj[i].memo_id;
                    var memo_no = obj[i].memo_no;
                    var market_name = obj[i].market_name;
                    var outlet_name = obj[i].outlet_name;
                    var territory_name = obj[i].territory_name;
                    var thana_name = obj[i].thana_name;
                    var remarks = obj[i].remarks;
                    var recommender_note = obj[i].recommender_note;
                    var approval_note = obj[i].approval_note;
                    var office_name = obj[i].office_name;
                    var req_status=obj[i].status
                    req_status=status1[req_status];
                    var recRow = '<tr id ="table_row_'+memo_id+'"><td id = "memo_id_'+memo_id+'" class= "memo_id" value = "'+memo_no+'"><a href="<?=BASE_URL?>admin/MemoEditablePermissions/view/'+memo_id+'" target="_blank">'+memo_no+'</a></td><td class ="office_name" value ="'+office_name+'">'+office_name+'</td><td class ="market_name" value ="'+market_name+'">'+market_name+'</td><td class ="outlet_name" value ="'+outlet_name+'">'+outlet_name+'</td><td class ="thana_name" value ="">'+thana_name+'</td><td class ="territory_name" value ="'+territory_name+'">'+territory_name+'</td><td class ="remarks" value ="'+remarks+'">'+remarks+'</td><td class ="recommender_note" value ="'+recommender_note+'">'+recommender_note+'</td><td class ="approval_note" value ="'+approval_note+'">'+approval_note+'</td><td class ="approval_note">'+req_status+'</td></tr>';
                    $('#search_memo_table').append(recRow);
                }

            }
        });
    });
</script>