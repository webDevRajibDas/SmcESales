<?php 
//pr($DistBalanceTransactionTypes);die();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Balance Transaction Type List'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistBalanceTransactionTypes', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add Balance Transaction Type'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistBalanceTransactionType', array('role' => 'form', 'action' => 'filter')); ?>
                    
                    <?php echo $this->Form->end(); ?>
                </div>
                <table id="Territories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Debit/Credit'); ?></th>
                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dist_balance_transaction_type as $type){
                            $inout_type = $type['DistBalanceTransactionType']['inout'];
                            $inout = "";
                            if($inout_type == 0){
                                $inout = 'Credit';
                            }else{
                                $inout = 'Debit';
                            }
							if($type['DistBalanceTransactionType']['status'] == 1){
                         ?>
                            <tr>
                                <td class="text-center"><?php echo h($type['DistBalanceTransactionType']['id']); ?></td>
                                <td class="text-center"><?php echo h($type['DistBalanceTransactionType']['name']); ?></td>
                                <td class="text-center"><?php echo $inout; ?></td>
                               
                                <td class="text-center">
                                    <?php
                                    if ($this->App->menu_permission('DistBalanceTransactionTypes', 'admin_edit')) {
                                        if($type['DistBalanceTransactionType']['status'] == 1){
                                            echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $type['DistBalanceTransactionType']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                        }
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistBalanceTransactionTypes', 'admin_view')) {
										 echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $type['DistBalanceTransactionType']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
										
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistBalanceTransactionTypes', 'admin_delete')) {
                                        //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $tso['DistBalanceTransactionType']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $tso['DistBalanceTransactionType']['id']));
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } }?>
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
