<?php 
//pr($DistDistributorLimits);die();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Limit'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistDistributorLimits', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add Distributor Limit'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistDistributorLimit', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        </tr>
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
                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>
                <table id="Territories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Distributor name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('max_amount'); ?></th>
                            <!-- <th class="text-center"><?php //echo $this->Paginator->sort('effective_date'); ?></th> -->
                            <!-- <th class="text-center"><?php //echo $this->Paginator->sort('end_effective_date'); ?> --></th>
                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($DistDistributorLimits as $limit): ?>
                            <tr>
                                <td class="text-center"><?php echo h($limit['DistDistributorLimit']['id']); ?></td>
                               <!--  <td class="text-center"><?php //echo h($distDistributors[$limit['DistDistributor']['id']]); ?></td> -->
                                 <td class="text-center"><?php echo h($limit['DistDistributor']['name']); ?></td>
                                <td class="text-center"><?php echo h($limit['DistDistributorLimit']['max_amount']); ?></td>
                                <!-- <td class="text-center">
                                    <?php /*?><?php
                                    if ($limit['DistDistributorLimit']['is_active'] == 1) {
                                        echo h('Yes');
                                    } else {
                                        echo h('No');
                                    }
                                    ?><?php */?>
                                    <?php //echo h($limit['DistDistributorLimit']['effective_date']); ?>
                                </td> -->
                               <!--  <td class="text-center">
                                   <?php /*?><?php
                                   if ($limit['DistDistributorLimit']['is_active'] == 1) {
                                       echo h('Yes');
                                   } else {
                                       echo h('No');
                                   }
                                   ?><?php */?>
                                   <?php //echo h($limit['DistDistributorLimit']['end_effective_date']); ?>
                               </td> -->
                                <td class="text-center">
                                    <?php
                                    //if ($this->App->menu_permission('DistDistributorLimits', 'admin_edit')) {
                                        //echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $limit['DistDistributorLimit']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                   // }
                                    ?>
                                    <?php
                                   if ($this->App->menu_permission('DistDistributorLimits', 'admin_view')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $limit['DistDistributorLimit']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                                                        
                                                                         //echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'edit', $limit['DistDistributorLimit']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                                                        
                                    }
                                    ?>
                                    <?php
                                   // if ($this->App->menu_permission('DistDistributorLimits', 'admin_delete')) {
                                       // echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $limit['DistDistributorLimit']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $limit['DistDistributorLimit']['id']));
                                  //  }
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
    $('.company_id').selectChain({
        target: $('.office_id'),
        value:'name',
        url: '<?=BASE_URL.'admin/territories/get_office_list'?>',
        type: 'post',
        data:{'company_id': 'company_id' }
    });
    
    $('.office_id').selectChain({
        target: $('#dist_distributor_id'),
        value:'name',
        url: '<?=BASE_URL.'/dist_distributors/get_dist_distributor_list';?>',
        type: 'post',
        data:{'office_id': 'office_id' }
    });
});
</script>