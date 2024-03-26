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
                    <?php if($this->App->menu_permission('CollectionEditablePermission','admin_index')){ echo $this->Html->link(__('Requisition List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
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
                                <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id','empty'=>'--- select ---','options'=>$office_list,'required'=>false)); ?>
                            </td>
                            <td width="50%">
                               <?php echo $this->Form->input('territory_id', array('class' => 'form-control territory_id','id'=>'territory_id','empty'=>'--- select ---','required'=>false)); ?>
                                
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
                            <th class="text-center">Collection Date</th>
                            <th class="text-center">Collection Amount</th>
                            <th class="text-center">Requisition Note</th>
                            <th class="text-center">Recommend Note</th>
	                        <th class="text-center">Recommend</th>
	                    </tr>
	                </thead>
                    <tbody id = "list_table">
                        <?php if(!empty($permited)){
                            foreach ($permited as $key => $per) {
                               $id = $per['CollectionEditablePermission']['id'];
                            ?>
                            <tr>
                                <td><?php echo $per['Office']['office_name']?></td>
                                
                                <td><?php echo $per['Collection']['memo_no']?></td>
                                <td><?php echo $per['Outlet']['name']?></td>
                                <td><?php echo $per['Type']['name']?></td>
                                <td><?php echo $per['InstrumentType']['name']?></td>
                                 <td><?php echo $per['Collection']['collectionDate']?></td>
                                <td><?php echo $per['Collection']['collectionAmount']?></td>
                                <td><?php echo $per['CollectionEditablePermission']['remarks']?></td>
                                <td>
                                    <?php echo $this->Form->input('recommender_note', array('class' => 'form-control recommender_note rcm_note','label'=>false,'id'=>'recommender_note_'.$id,'required'=>true)); ?>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-xs toggleBtn add_more_<?=$id?>" onclick="collection_recommended(<?=$id?>);"><i class="glyphicon glyphicon-plus"></i></a>
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

    

    $('body').on('click','.find',function() {
        var office_id = $("#office_id").val();
        var territory_id = $("#territory_id").val();
        var market_id = $("#market_id").val();
        var outlet_id = $("#outlet_id").val();
        var type = $("#type").val();

        $.ajax
        ({
            type: "POST",
            url: '<?=BASE_URL?>collection_editable_permissions/get_collection_recommended_list',
            data: 
            {
                office_id:office_id,
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
                console.log(obj);
                $("#list_table").empty();
                for(var i = 0; i<obj.length; i++){
                    var id = obj[i].id;
                    var office_name = obj[i].office_name;
                    var memo = obj[i].memo;
                    var outlet = obj[i].outlet;
                    var type = obj[i].type;
                    var inst_type = obj[i].inst_type==null?'':obj[i].inst_type;
                    var collectionDate = obj[i].collectionDate;
                    var collectionAmount = obj[i].collectionAmount;
                    var remarks = obj[i].remarks;


                    var recRow = 
                    '<tr id ="table_row_'+id+'">\
                         <td class ="office_name" value ="'+office_name+'">'+office_name+'</td>\
                        <td class ="memo" value ="'+memo+'">'+memo+'</td>\
                        <td class ="outlet" value ="'+outlet+'">'+outlet+'</td>\
                        <td class ="type" value ="'+type+'">'+type+'</td>\
                        <td class ="inst_type" value ="'+inst_type+'">'+inst_type+'</td>\
                        <td class ="collectionDate" value ="'+collectionDate+'">'+collectionDate+'</td>\
                        <td class ="collectionAmount" value ="'+collectionAmount+'">'+collectionAmount+'</td>\
                        <td class ="remarks" value ="'+remarks+'">'+remarks+'</td>\
                        <td class ="recommender_note" >\
                            <input type ="text" class="recommender_note" name="data[DepositEditablePermissions][recommender_note]" id="recommender_note_'+id+'">\
                        </td>\
                        <td><a class="btn btn-info btn-xs toggleBtn add_more_<?=$id?>" onclick="collection_recommended('+id+');"><i class="glyphicon glyphicon-plus"></i></a></td></tr>';
                    $('#search_memo_table').append(recRow);

                }

                
            }
        });
    });




    function collection_recommended(id){
        var recommender_note = $("#recommender_note_"+id).val();
        if(recommender_note == ''){
            alert('Please Give a Note.');
            return false;
        }else{
            $.ajax
            ({
                type: "POST",
                url: '<?=BASE_URL?>collection_editable_permissions/collection_recommended',
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