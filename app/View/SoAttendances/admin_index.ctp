<style>
    #divLoading {
        display: none;
    }

    #divLoading.show {
        display: block;
        position: fixed;
        z-index: 100;
        background-image: url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
        background-color: #666;
        opacity: 0.4;
        background-repeat: no-repeat;
        background-position: center;
        left: 0;
        bottom: 0;
        right: 0;
        top: 0;
    }

    #loadinggif.show {
        left: 50%;
        top: 50%;
        position: absolute;
        z-index: 101;
        width: 32px;
        height: 32px;
        margin-left: -16px;
        margin-top: -16px;
    }
</style>

<div id="divLoading" class=""> </div>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">



            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title ?></h3>

            </div>


            <div class="box-body">

                <div class="search-box">
                    <?php //echo $this->Form->create('DistSrCheckInOut', array('role' => 'form', 'action' => 'index')); ?>
                    <?php echo $this->Form->create('SoCheckInOut', array('role' => 'form', 'url'=>array('controller' => 'so_attendances', 'action'=>'index'))); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('from_date', array('class' => 'form-control datepicker from_date', 'required' => true, 'readonly' => true)); ?></td>
                            <td class="required">
                                <?php echo $this->Form->input('to_date', array('class' => 'form-control datepicker to_date', 'required' => true, 'readonly' => true)); ?></td>
                            </tr>


                        <tr>
                            <?php if ($office_parent_id == 0) { ?>
                                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>
                            <?php } ?>
                            <?php if ($office_parent_id == 14) { ?>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>
                            <?php } ?>
                            <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                                <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- All ----')); ?></td>

                        </tr>
                    <?php } else { ?>

                        <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id')); ?></td>

                    <?php } ?>
                    </tr>


                    <tr align="center">
                        <td colspan="2">

                            <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>

                            <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                        </td>
                    </tr>
                    </table>


                    <?php echo $this->Form->end(); ?>
                </div>


                <?php if (!empty($result)) { ?>
                
                    <table id="OutletCategories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center"><?php echo __('id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Office.office_name', 'Area Office'); ?></th>
                             <th class="text-center"><?php echo $this->Paginator->sort('AE.name', 'AE'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Territory.name', 'Territory'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('SalesPerson.name', 'SO'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('SoCheckInOut.date', 'Date'); ?></th>
                            <th class="text-center"><?php echo __('In Time'); ?></th>
                            <th class="text-center"><?php echo __('Out Time'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('SoCheckInOut.status', 'Status'); ?></th>
                            <th class="text-center"><?php echo __( 'Note'); ?></th>
                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            foreach ($result as $key => $val) : ?>
                            <tr>
                                <td class="text-center"><?=$key+1; ?></td>
                                <td><?php echo h($val['Office']['office_name']); ?></td>
                                 <td><?php echo h($val['AE']['name']); ?></td> 
                                <td><?php echo h($val['Territory']['name']); ?></td>
                                <td>
                                    <?php echo h($val['SalesPerson']['name']); ?>
                                    <input type="hidden" id="so_name_<?=$val['SoCheckInOut']['id'];?>" value=" <?php echo h($val['SalesPerson']['name']); ?>">
                                </td>
                                
                                <td><?php echo h($val['SoCheckInOut']['date']); ?></td>
                                <td><?php echo h(date('h:i A', strtotime($val['SoCheckInOut']['check_in_time']))); ?></td>
                                <td><?php 
                                    if(!empty($val['SoCheckInOut']['check_out_time'])){
                                        echo h(date('h:i A', strtotime($val['SoCheckInOut']['check_out_time']))); 
                                    }

                                ?></td>
                                
                                <td style="text-align: center;">
                                    <?php 
                                        if($val['SoCheckInOut']['status'] == 1){
                                            $approve = 'Approve';
                                            $color = 'style="background:green;color:white;padding:2px 10px;border-radius: 8px;"';

                                        }elseif($val['SoCheckInOut']['status'] == 2){
                                            $approve = 'Reject';
                                            $color = 'style="background:yellow;color:red;padding:2px 10px;border-radius: 8px;"';
                                        }else{
                                            $approve = 'Pending';
                                            $color = 'style="background:red;color:white;padding:2px 10px;border-radius: 8px;"';
                                        }
                                    ?>
                                    <a  <?=$color;?>  type="button"><?=$approve;?></a>
                                </td>
                                <td><?php echo h($val['SoCheckInOut']['note']); ?></td>
                                <td class="text-center">
                                    <?php 
                                        if ( $val['SoCheckInOut']['status'] < 1 ){
                                            //if ($this->App->menu_permission('DistSrCheckInOut', 'admin_attendance_status_update')) { 
										?>
                                                <a data-toggle="modal" data-target="#myModal" onclick="approve_function(<?=$val['SoCheckInOut']['id'];?>)" class='btn btn-primary btn-xs' data-toggle='tooltip' title ='Approve'><i class="glyphicon glyphicon-certificate"></i></a> 
                                    <?php  //}  
									} ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
                    

                <?php } else { ?>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<style>
    .custom_width{
        width: 60%;
    }
</style>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"> SO Attendance Approve </h4>
        </div>
        <div class="modal-body">
            <?php echo $this->Form->create('SoCheckInOut', array('role' => 'form', 'url'=>array('controller' => 'so_attendances', 'action'=>'attendance_status_update'))); ?>
                <div class="form-group">
                    <input type="hidden" name="data[SoCheckInOut][id]" id="pid" value="0">
                </div>
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('so_name', array('class' => 'form-control custom_width', 'id'=>'soname', 'readonly'=>true ));
                    ?>
                </div>

                <div class="form-group">
                    <?php 
                        echo $this->Form->input('status', array('class' => 'form-control custom_width', 'options'=>array('1'=>'Approve', '2'=>'Reject')));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('note', array('class' => 'form-control custom_width', 'type'=>'textarea'));
                    ?>

                </div>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary' )); ?>
			<?php echo $this->Form->end(); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>

<script>
    $('.region_office_id').selectChain({
        target: $('.office_id'),
        value: 'name',
        url: '<?= BASE_URL . 'dist_market_characteristic_reports/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
    $('.region_office_id').change(function() {
        $('#territory_id').html('<option value="">---- All ----');
    });

    function approve_function(pid){
        var srname = $("#so_name_"+pid).val();
        $("#soname").val(srname);
        $("#pid").val(pid);
        $("#SoCheckInOutNote").val('');
        $("#SoCheckInOutNote").text('');
    }

</script>


<script>
    $(document).ready(function() {


    });
</script>
