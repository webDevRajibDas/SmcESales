<style>
    .rcm_note{
        width: 100% !important;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i>
                	<?php echo __('Recommend Approval List'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php if($this->App->menu_permission('DistributorDateEditablePermissions','admin_index')){ echo $this->Html->link(__('Requisition List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                </div>
            </div> 
             
            <div class="box-body">
            	<h4 class="box-title">
            		<i class="glyphicon glyphicon-th-large"></i>
            		<?php echo __('Distributor Selections'); ?>
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
                            <th class="text-center">Recommend Note</th>
                            <th class="text-center">Approval Note</th>
                            <th class="text-center">Approval</th>
	                    </tr>
	                </thead>
                    <tbody id = "list_table">
                        <?php if(!empty($permited)){
                            foreach ($permited as $key => $per) {
                                $id = $per['DistributorDateEditablePermission']['id'];
                            ?>
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
                                <td>
                                    <?php echo $this->Form->input('approval_note', array('class' => 'form-control approval_note rcm_note','label'=>false,'id'=>'approval_note_'.$id)); ?>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-xs toggleBtn add_more_<?=$id?>" onclick="collection_approved(<?=$id?>);"><i class="glyphicon glyphicon-plus"></i></a>
                                    <a class="btn btn-danger btn-xs toggleBtn remove_more_<?=$id?>" onclick="collection_not_approved(<?=$id?>);"><i class="glyphicon glyphicon-minus"></i></a>
                                </td>
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

    /*
    $('body').on('click','.find',function() {
        var office_id = $("#office_id").val();
        var territory_id = $("#territory_id").val();
        var type = $("#type").val();

        $.ajax
        ({
            type: "POST",
            url: '<?=BASE_URL?>deposit_editable_permissions/get_deposit_approved_list',
            data: 
            {
                office_id:office_id,
                territory_id:territory_id,
                type:type
            },
            cache: false, 
            success: function(response)
            {          
                var obj = jQuery.parseJSON(response);
                console.log(obj);
                $("#list_table").empty();
                for(var i = 0; i<obj.length; i++){
                    var id = obj[i].id;
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
                        <td class ="recommender_note" >\
                            <input type ="text" class="form-control rcm_note  approval_note" name="data[DepositEditablePermissions][approval_note]" id="approval_note_'+id+'">\
                        </td>\
                        <td>\
                            <a class="btn btn-info btn-xs toggleBtn add_more_<?=$id?>" onclick="deposit_approved('+id+');"><i class="glyphicon glyphicon-plus"></i></a><a class="btn btn-danger btn-xs toggleBtn remove_more_'+id+'" onclick="deposit_not_approved('+id+');"><i class="glyphicon glyphicon-minus"></i></a>\
                        </td>\
                    </tr>';
                    $('#search_memo_table').append(recRow);
                }

            }
        });
    });

    */

    function collection_approved(id){
        var approval_note = $("#approval_note_"+id).val();
        $.ajax
        ({
            type: "POST",
            url: '<?=BASE_URL?>distributor_date_editable_permissions/order_approved',
            data: {id:id,approval_note:approval_note},
            cache: false, 
            success: function(response)
            {          
                var obj = jQuery.parseJSON(response);
                if(obj[0]== 1){
                    alert("Approval Submit Successfully");
                    var $el = $('.add_more_'+id);
                    $el.closest('tr').remove();
                }else{
                    alert("Approval is not Submited");
                }
            }
        });
    }
    function collection_not_approved(id){
        var approval_note = $("#approval_note_"+id).val();
        if(approval_note == '')
        {
            alert('Please Give a Note.');
            return false;
        }
        else{
            $.ajax
            ({
                type: "POST",
                url: '<?=BASE_URL?>distributor_date_editable_permissions/order_not_approved',
                data: {id:id,approval_note:approval_note},
                cache: false, 
                success: function(response)
                {          
                    var obj = jQuery.parseJSON(response);
                    if(obj[0]== 1){
                        alert("Approval Submit Successfully");
                        var $el = $('.add_more_'+id);
                        $el.closest('tr').remove();
                    }else{
                        alert("Approval is not Submited");
                    }
                }
            });
        }
    }

</script>