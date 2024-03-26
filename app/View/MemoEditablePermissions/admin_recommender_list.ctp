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
                	<?php echo __('Requisition List'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php if($this->App->menu_permission('MemoEditablePermissions','admin_index')){ echo $this->Html->link(__('Requisition List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
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
                            <th class="text-center">Recommend Note</th>
	                        <th class="text-center">Recommend</th>
	                    </tr>
	                </thead>
                    <tbody id = "list_table">
                        <?php if(!empty($permited)){
                            foreach ($permited as $key => $per) {
                               $id = $per['MemoEditablePermission']['id'];
                            ?>
                            <tr>
                                <td><a href="<?php echo BASE_URL;?>admin/MemoEditablePermissions/view/<?php echo $per['Memo']['id'];?>" target="_blank"><?php echo $per['Memo']['memo_no']?></a></td>
                                <td><?php echo $per['Office']['office_name']?></td>
                                <td><?php echo $per['Market']['name']?></td>
                                <td><?php echo $per['Outlet']['name']?></td>
                                <td><?php echo $per['Thana']['name']?></td>
                                <td><?php echo $per['Territory']['name']?></td>
                                <td><?php echo $per['MemoEditablePermission']['remarks']?></td>
                                <td>
                                    <?php echo $this->Form->input('recommender_note', array('class' => 'form-control recommender_note rcm_note','label'=>false,'id'=>'recommender_note_'.$id,'required'=>true)); ?>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-xs toggleBtn add_more_<?=$id?>" onclick="memo_recommended(<?=$id?>);"><i class="glyphicon glyphicon-plus"></i></a>
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

    $('body').on('click','.find',function() {
        var office_id = $("#office_id").val();
        var memo_no = $("#memo_no").val();

        $.ajax
        ({
            type: "POST",
            url: '<?=BASE_URL?>memo_editable_permissions/get_memo_recommended_list',
            data: {office_id:office_id,memo_no:memo_no},
            cache: false, 
            success: function(response)
            {          
                var obj = jQuery.parseJSON(response);
                console.log(obj);
                $("#list_table").empty();
                for(var i = 0; i<obj.length; i++){
                    var id = obj[i].id;
                    var memo_id = obj[i].memo_id;
                    var memo_no = obj[i].memo_no;
                    var market_name = obj[i].market_name;
                    var outlet_name = obj[i].outlet_name;
                    var territory_name = obj[i].territory_name;
                    var thana_name = obj[i].thana_name;
                    var remarks = obj[i].remarks;
                    var office_name = obj[i].office_name;


                    var recRow = '<tr id ="table_row_'+memo_id+'"><td id = "memo_id_'+memo_id+'" class= "memo_id" value = "'+memo_no+'"><a href="<?=BASE_URL?>admin/MemoEditablePermissions/view/'+memo_id+'" target="_blank">'+memo_no+'</a></td><td class ="office_name" value ="'+office_name+'">'+office_name+'</td><td class ="market_name" value ="'+market_name+'">'+market_name+'</td><td class ="outlet_name" value ="'+outlet_name+'">'+outlet_name+'</td><td class ="thana_name" value ="">'+thana_name+'</td><td class ="territory_name" value ="'+territory_name+'">'+territory_name+'</td><td class ="remarks" value ="'+remarks+'">'+remarks+'</td><td class ="recommender_note" ><input type ="text" class="recommender_note" name="data[MemoEditablePermissions][recommender_note]" id="recommender_note_'+id+'"></td><td><a class="btn btn-info btn-xs toggleBtn add_more_<?=$id?>" onclick="memo_recommended('+id+');"><i class="glyphicon glyphicon-plus"></i></a></td></tr>';
                    $('#search_memo_table').append(recRow);
                }

            }
        });
    });

    function memo_recommended(id){
        var recommender_note = $("#recommender_note_"+id).val();
        if(recommender_note == ''){
            alert('Please Give a Note.');
            return false;
        }else{
            $.ajax
            ({
                type: "POST",
                url: '<?=BASE_URL?>memo_editable_permissions/memo_recommended',
                data: {id:id,recommender_note:recommender_note},
                cache: false, 
                success: function(response)
                {          
                    var obj = jQuery.parseJSON(response);
                    
                    if(obj[0]== 1){
                        alert("Recommendation Submit Successfully");
                        var $el = $('.add_more_'+id);
                        $el.closest('tr').remove();
                    }else{
                        alert("Recommendation is not Submited");
                    }
                }
            });
        }
    }
</script>