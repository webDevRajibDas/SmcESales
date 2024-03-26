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
                    <?php if($this->App->menu_permission('DistributorDateEditablePermissions','admin_create_requisition')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Create Requisition'), array('action' => 'create_requisition'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                    <?php if($this->App->menu_permission('DistributorDateEditablePermissions','admin_recommender_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Recommender List'), array('action' => 'recommender_list'), array('class' => 'btn btn-info', 'escape' => false)); } ?>
                    <?php if($this->App->menu_permission('DistributorDateEditablePermissions','admin_approval_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Approval List'), array('action' => 'approval_list'), array('class' => 'btn btn-success', 'escape' => false)); } ?>
                </div>
            </div> 
             
            <div class="box-body">
            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Distributor product issue Selections'); ?>
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
                            <td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?></td>
                            <td>
                                <?php echo $this->Form->input('outlet_id', array('label' => 'Distributor:','id' => 'distribut_outlet_id','class' => 'form-control distribut_outlet_id','required'=>false,'empty'=>'---- Select Distributers ----','options'=>$distributors)); ?>
                                    
                            </td>
                                                  
                        </tr>
                        
                        <tr>
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
                            <th class="text-center">ID</th>
                            <th class="text-center">Order Reference No</th>
                            <th class="text-center">Outlet</th>
                            <th class="text-center">Market</th>
                            <th class="text-center">Territory</th>
                            <th class="text-center">Order Total </th>
                            <th class="text-center">Order Date</th>
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
                                
                                <td><?php echo $per['Order']['id']?></td>
                                <td><?php echo $per['Order']['order_no']?></td>
                                <td><?php echo $per['Outlet']['name']?></td>
                                <td><?php echo $per['Market']['name']?></td>
                                <td><?php echo $per['Territory']['name']?></td>
                                <td><?php echo $per['Order']['gross_value']?></td>
                                <td><?php echo $per['Order']['order_date']?></td>
                                <td><?php echo $per['DistributorDateEditablePermission']['remarks']?></td>
                                <td><?php echo $per['DistributorDateEditablePermission']['recommender_note']?></td>
                                <td><?php echo $per['DistributorDateEditablePermission']['approval_note']?></td>
                                <td><?php echo $status[$per['DistributorDateEditablePermission']['status']]?></td>
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
        target: $('.distribut_outlet_id'),
        value:'name',
        url: '<?= BASE_URL.'sales_people/get_outlet_list_with_distributor_name';?>',
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
        var distribut_outlet_id = $("#distribut_outlet_id").val();
       
        $.ajax
        ({
            type: "POST",
            url: '<?=BASE_URL?>distributor_date_editable_permissions/get_order_editable_permited_list',
            data: 
            {
                office_id:office_id,
                date_from:date_from,
                date_to:date_to,
                distribut_outlet_id:distribut_outlet_id
            },
            cache: false, 
            success: function(response)
            {          
                var obj = jQuery.parseJSON(response);
                $("#list_table").empty();
                for(var i = 0; i<obj.length; i++){
                    var order_id = obj[i].order_id;
                    var order_no = obj[i].order_no;
                    var outlet = obj[i].outlet;
                    var market = obj[i].market;
                    var territorie = obj[i].territorie;
                    var order_toal = obj[i].order_toal;
                    var order_date = obj[i].order_date;
                    var remarks = obj[i].remarks;
                    var recommender_note = obj[i].recommender_note;
                    var approval_note = obj[i].approval_note;
                    var req_status=obj[i].status
                    req_status=status1[req_status];
                    var recRow = 
                    '<tr id ="table_row_'+order_id+'">\
                        <td class ="order_id" value ="'+order_id+'">'+order_id+'</td>\
                        <td class ="order_no" value ="'+order_no+'">'+order_no+'</td>\
                        <td class ="outlet" value ="'+outlet+'">'+outlet+'</td>\
                        <td class ="market" value ="'+market+'">'+market+'</td>\
                        <td class ="territorie" value ="'+territorie+'">'+territorie+'</td>\
                        <td class ="order_toal" value ="'+order_toal+'">'+order_toal+'</td>\
                        <td class ="order_date" value ="'+order_date+'">'+order_date+'</td>\
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