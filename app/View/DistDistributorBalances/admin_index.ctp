<?php 
//pr($DistDistributorBalances);die();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Deposits'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistDistributorBalances', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add New'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    /*if ($this->App->menu_permission('DistDistributorBalances', 'admin_adjustment')) {
                        echo $this->Html->link(__(' <i class="glyphicon glyphicon-plus"></i> Adjustment Deposits'), array('action' => 'adjustment'), array('class' => 'btn btn-info', 'escape' => false));
                    }*/
                    ?>
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistDistributorBalance', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----')); ?></td>

                        </tr>
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('dist_distributor_id', array('label'=>'Distributor :','id' => 'dist_distributor_id', 'class' => 'form-control dist_distributor_id', 'required' => false, 'empty' => '---- Select Distributor ----')); ?></td>

                        </tr>
                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>

                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                
                                <?php echo $this->Form->button('<i class="fa fa-download"></i> Download Excel', array('type' => 'button', 'class' => 'btn btn-large btn-primary','id'=>'dis_exel','escape' => false)); ?>
                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>
                <table id="Territories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Office name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Distributor name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Balance'); ?></th>
                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dist_distributor_balances as $dist): ?>
                            <tr>
                                <td class="text-center"><?php echo h($dist['DistDistributorBalance']['id']); ?></td>
                               <!--  <td class="text-center"><?php echo h($distDistributors[$dist['DistDistributor']['id']]); ?></td> -->
                                 <td class="text-center"><?php echo h($dist['Office']['office_name']); ?></td>
                                 <td class="text-center"><?php echo h($dist['DistDistributor']['name']); ?></td>
                                <td class="text-center"><?php echo h($dist['DistDistributorBalance']['balance']); ?></td>
                               
                                <td class="text-center">
                                    <?php
                                    if ($this->App->menu_permission('DistDistributorBalances', 'admin_edit')) {
                                        //echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $dist['DistDistributorBalance']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistDistributorBalances', 'admin_view')) {
                                        //echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $dist['DistDistributorBalance']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
										
										 echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $dist['DistDistributorBalance']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
										
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistDistributorBalances', 'admin_delete')) {
                                        //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $dist['DistDistributorBalance']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $dist['DistDistributorBalance']['id']));
                                    }
                                    ?>
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
            </div>			
        </div>
    </div>
</div>

<script>
    /*$('#office_id').selectChain({
     target: $('#name'),
     value:'name',
     url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
     type: 'post',
     data:{'office_id': 'office_id' }
     });*/
</script>

<script>
$(document).ready(function () {

   /* $('.office_id').selectChain({
        target: $('#dist_distributor_id'),
        value:'name',
        url: '<?=BASE_URL.'admin/doctors/get_distribute';?>',
        type: 'post',
        data:{'office_id': 'office_id' }
    });*/
    $('.office_id').selectChain({
        target: $('#dist_distributor_id'),
        value:'name',
        url: '<?=BASE_URL.'admin/DistDistributorBalances/get_distribute';?>',
        type: 'post',
        data:{'office_id': 'office_id' }
    });


    $('#dis_exel').click(function () {
            var office_id = $("#office_id").val();
            var dist_name = $("#name").val();
            office_id=(office_id)?office_id:0;
            dist_name=(dist_name)?dist_name:"";
            window.open("<?= BASE_URL; ?>DistDistributorBalances/download_xl/" + office_id + "/" + dist_name,'_blank');
        });
});
</script>