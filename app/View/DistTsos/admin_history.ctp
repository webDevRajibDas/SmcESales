<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('TSO History'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistTsoMappings', 'admin_index')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> TSO'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistTso', array('role' => 'form', 'action' => 'history')); ?>
                    <table class="search">
                        
                        
                        <tr><td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----', 'options' => $offices,'selected'=>$office_id)); ?></td> </tr>
                        <tr>
                            <td width="50%">
                                <?php 
                                echo $this->Form->input('dist_tso_id', array('label' => 'TSO', 'id' => 'dist_tso_id', 'class' => 'form-control dist_tso_id', 'required' => false, 'empty' => '---- Select TSO ----','selected'=>$dist_tso_id,'options'=>$distTsos));
                                ?>
                            </td>

                        </tr>
                        
                         <tr><td width="50%"><?php echo $this->Form->input('date_from', array('label' => 'Date From', 'id' => 'date_from', 'class' => 'form-control datepicker', 'required' => TRUE,'autocomplete'=>'off')); ?></td></tr>
                         <tr><td width="50%"><?php echo $this->Form->input('date_to', array('label' => 'Date To', 'id' => 'date_to', 'class' => 'form-control datepicker', 'required' => TRUE,'autocomplete'=>'off')); ?></td></tr>
                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>
                <table id="Territories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center"><?php echo "TSO Name"; ?></th>
                            <th class="text-center"><?php echo "Office Name"; ?></th>
                            <th class="text-center"><?php echo "Effective From"; ?></th>
                            <th class="text-center"><?php echo "Effective To"; ?></th>
                            <th class="text-center"><?php echo "Assigned By"; ?></th>
                            <th class="text-center"><?php echo "Assigned At"; ?></th>
                            <th class="text-center"><?php echo "Updated By"; ?></th>
                            <th class="text-center"><?php echo "Updated At"; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                                                        foreach ($data as $key => $value) {
                                                           
                                                            ?>
                        <tr>
                            <td><?php echo $value['DistTsoHistory']['name'];?></td>
                            <td><?php echo $value['Office']['office_name'];?></td>
                            <td><?php echo date("m-d-Y",strtotime($value['DistTsoHistory']['effective_date']));?></td>
                            <td><?php
                            if($value['DistTsoHistory']['effective_end_date'])
                            {
                                echo date("m-d-Y",strtotime($value['DistTsoHistory']['effective_end_date']));
                            }
                            else 
                            {
                                
                            }
                            ?></td>
                            <td><?php echo $users[$value['DistTsoHistory']['created_by']];?></td>
                            <td><?php echo date("m-d-Y",strtotime($value['DistTsoHistory']['created_at']));?></td>
                            <td><?php echo $users[$value['DistTsoHistory']['updated_by']];?></td>
                            <td><?php echo date("m-d-Y",strtotime($value['DistTsoHistory']['updated_at']));?></td>
                        </tr>
                                                            
                                                         <?php 
                                                        }
                        ?>
                        
                    </tbody>
                </table>
                <div class='row'>
                    <div class='col-xs-6'>
                        <div id='Users_info' class='dataTables_info'>
                            <?php echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>	
                        </div>
                    </div>
                    <div class='col-xs-6'>
                        <div class='dataTables_paginate paging_bootstrap'>
                            <ul class='pagination'>									
                                <?php
                                echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
                                echo $this->Paginator->numbers(array("separator" => "", "currentTag" => "a", "currentClass" => "active", "tag" => "li", "first" => 1));
                                echo $this->Paginator->next(__("next"), array("tag" => "li", "currentClass" => "disabled"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
                                ?>								
                            </ul>
                        </div>
                    </div>
                </div>				
            </div>			
        </div>
    </div>
</div>

<script>
    /*
    $('#dist_tso_id').selectChain({
        target: $('#dist_distributor_id'),
        value: 'name',
        url: '<?= BASE_URL . 'DistTsoMappings/get_dist_list' ?>',
        type: 'post',
        data: {'dist_tso_id': 'dist_tso_id'}
    });
    */
    $('#office_id').selectChain({
        target: $('#dist_tso_id'),
        value: 'name',
        url: '<?= BASE_URL . 'DistTsoMappings/get_dist_tso_list' ?>',
        type: 'post',
        data: {'office_id': 'office_id'}
    });
    
    $('.datepicker').datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true
  });  

</script>